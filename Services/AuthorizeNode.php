<?php 
namespace RC\PHPCRReorderNodesBundle\Services;

use PHPCR\Util\PathHelper;

class AuthorizeNode {
	
	protected $security;
	protected $nodes;
	
	public function __construct($security, $nodes){
		$this->security = $security;
		$this->nodes = $nodes;
	}
	
	public function isAuthorized($nodepath){
		return $this->nodeExists($nodepath) && $this->security->isGranted($this->getRoles($nodepath));
	}
	
	protected function nodeExists($nodepath){
		foreach($this->nodes as $node){
			foreach($node as $nodename => $data){
				if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return true;				
			}
		}
		return false;
	}
	
	protected function getRoles($nodepath){
		foreach($this->nodes as $node){
			foreach($node as $nodename => $data){
				if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return $data['role'];
			}
		}
		return false;		
	}
	
	public function getTemplate($nodepath){
		foreach($this->nodes as $node){
			foreach($node as $nodename => $data){
				if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return $data['template'];
			}
		}
		return false;
	}
	
	
}