<?php

namespace Academia\OrderBundle\Controller;

use Academia\OrderBundle\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Academia\OrderBundle\Lib\PaypalPurchase;
use Academia\OrderBundle\Lib\PaypalConfiguration;

/**
 * Class PaymentController
 * @package Academia\OrderBundle\Controller
 */
class PaymentController extends Controller
{
    /**
     * @Route("/payment/success", name="academia_order_payment_success")
     * @Template()
     */
    public function successAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        /** @var \Academia\OrderBundle\Entity\Order $entity */
        $entity = $em->getRepository('AcademiaOrderBundle:Order')->findOneBy( array( 'paypalToken' => $request->query->get('token') ) );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        if ($entity->getPaid()) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'You Have already made payment for that order'
            );

            return $this->redirect($this->generateUrl('u_'));
        }

        $paypal = $this->createPurchase($entity);
        $response = $paypal->process_payment();

        $entity->setPaid(true);
        $em->persist($entity);
        $em->flush();

        return array(
            'paypal'   => $paypal,
            'response' => $response,
        );
    }

    /**
     * @Route("/payment/fail", name="academia_order_payment_fail")
     * @Template()
     */
    public function failAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcademiaOrderBundle:Order')->findOneBy( array( 'paypalToken' => $request->get('TOKEN') ) );

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        return array(
            'entity' => $entity,
        );

    }


    /**
     * @Route("/payment/{id}", name="academia_order_payment_setExpressCheckout")
     * @Method("POST")
     * @Template()
     */
    public function processAction(Request $request, $id)
    {
        $form = $this->createPayForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcademiaOrderBundle:Order')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Order entity.');
            }

            $paypal = $this->createPurchase($entity);

            $entity->setPaypalToken($paypal->token());
            $em->persist($entity);
            $em->flush();

            return array(
                'paypal' => $paypal
            );
        }

        return $this->redirect($this->generateUrl('u_'));
    }

    private function createPayForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('academia_order_payment_setExpressCheckout', array('id' => $id)))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Pay For This Order'))
            ->getForm()
            ;
    }

    private function setCredentials() {

        PaypalConfiguration::username( 'kelgitonga-facilitator_api1.gmail.com' );
        PaypalConfiguration::password( '1379182978' );
        PaypalConfiguration::signature( 'AiPC9BjkCyDFQXbSkoZcgqH3hpacAEnT65viJDKe336U4grwYUZJuqLy' );

        PaypalConfiguration::return_url( $this->generateUrl( 'academia_order_payment_success', array(), true ) );
        PaypalConfiguration::cancel_url( $this->generateUrl( 'academia_order_payment_fail', array(), true ) );
        PaypalConfiguration::business_name( 'Academia Studios' );

        PaypalConfiguration::notify_url( $this->generateUrl( 'academia_order_payment_fail' ) );

        // Uncomment the line below to switch to the live PayPal site
        //PaypalConfiguration::environment( 'live' );

        if( PaypalConfiguration::username() == 'your_api_username' || PaypalConfiguration::password() == 'your_api_password' || PaypalConfiguration::signature() == 'your_api_signature' )
            exit( 'You must set your API credentials in ' . __FILE__ . ' for this example to work.' );
    }

    /**
     * Creates and returns a PayPal DG Purchase Object
     */
    private function  createPurchase(Order $order) {

        $this->setCredentials();

        $purchase_details = array(
            'name'        => 'Payment for Professional Essay',
            'description' => 'Payment for Professional Essay',
            'amount'      => ( $order->getPages() * 10 ),
            'tax_amount'  => '0.00',
            'items'       => array(
                array(
                    'item_name'        => 'Payment for Professional Essay',
                    'item_description' => 'This is a payment to AcademiaStudios Inc. for your Essay.',
                    'item_amount'      => ( $order->getPages() * 10 ),
                    'item_tax'         => '0.00',
                    'item_quantity'    => 1,
                    'item_number'      => ( $order->getId() )
                ),
            )
        );


        return new PaypalPurchase( $purchase_details );

    }

}