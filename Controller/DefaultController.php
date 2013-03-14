<?php

namespace RC\PHPCRReorderNodesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PHPCR\Util\NodeHelper;

class DefaultController extends Controller
{
    public function indexAction($nodename){
    	
    	$data = $this->get('rc.phpcr.reorder.nodes.authorizenode');
    	
    	if(!$data->isAuthorized($nodename)){
    		throw new NotFoundHttpException();
    	}
    	$order = $this->get('rc.phpcr.reorder.nodes.ordernodes');
    	
    	$template = $data->getTemplate($nodename);
    	
        return $this->render($template, array('nodename' => $nodename, 'nodenames' => $order->getNodeNames($nodename), 'childrens' => $order->getChildrens($nodename) ));
    }
    
    public function doorderAction($nodename){
    	$data = $this->get('rc.phpcr.reorder.nodes.authorizenode');
    	$request = $this->getRequest(); 
    	if(!$data->isAuthorized($nodename)){
    		throw new NotFoundHttpException();
    	}
    	
    	$order = $this->get('rc.phpcr.reorder.nodes.ordernodes');
    	$request = $request->request->all();
    	$ids = $request['names'];
    	$order->newOrder($nodename, $ids);
    	return new Response();
    	
    }
    
    
}
