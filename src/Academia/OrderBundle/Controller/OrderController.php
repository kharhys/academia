<?php

namespace Academia\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Academia\OrderBundle\Entity\Order;
use Academia\OrderBundle\Form\OrderType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Order controller.
 *
 * @Route("/user")
 */
class OrderController extends Controller
{

    /**
     * Lists all Order entities for logged in user.
     *
     * @Route("/", name="u_")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AcademiaOrderBundle:Order')->findBy( array('user' => $this->getUser()->getId() ) );

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Order entity.
     *
     * @Route("/", name="u__create")
     * @Method("POST")
     * @Template("AcademiaOrderBundle:Order:new.html.twig")
     */
    public function createAction(Request $request)
    {
        /** @var \Academia\OrderBundle\Entity\Order $entity */
        $entity = new Order();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($entity);
            $acl = $aclProvider->createAcl($objectIdentity);

            // retrieving the security identity of the currently logged-in user
            $securityContext = $this->get('security.context');
            $user = $securityContext->getToken()->getUser();
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            $aclProvider->updateAcl($acl);

            return $this->redirect($this->generateUrl('u__show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Order entity.
    *
    * @param Order $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Order $entity)
    {
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('u__create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Order entity.
     *
     * @Route("/new", name="u__new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Order();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}", name="u__show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcademiaOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $securityContext = $this->get('security.context');

        // check for edit access
        if (false === $securityContext->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($id);
        $payForm = $this->createPayForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'pay_form'    => $payForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Route("/{id}/edit", name="u__edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcademiaOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $securityContext = $this->get('security.context');

        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException();
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Order entity.
    *
    * @param Order $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Order $entity)
    {
        $form = $this->createForm(new OrderType(), $entity, array(
            'action' => $this->generateUrl('u__update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Order entity.
     *
     * @Route("/{id}", name="u__update")
     * @Method("PUT")
     * @Template("AcademiaOrderBundle:Order:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AcademiaOrderBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('u__edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Order entity.
     *
     * @Route("/{id}", name="u__delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AcademiaOrderBundle:Order')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Order entity.');
            }

            $securityContext = $this->get('security.context');

            // check for edit access
            if (false === $securityContext->isGranted('DELETE', $entity)) {
                throw new AccessDeniedException();
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('u_'));
    }

    /**
     * Creates a form to delete a Order entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('u__delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
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


}
