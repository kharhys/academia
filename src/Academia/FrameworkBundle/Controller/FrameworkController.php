<?php

namespace Academia\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FrameworkController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function homeAction()
    {
        return array();
    }
}
