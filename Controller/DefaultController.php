<?php

namespace RC\PHPCRReorderNodesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RCPHPCRReorderNodesBundle:Default:index.html.twig', array('name' => $name));
    }
}
