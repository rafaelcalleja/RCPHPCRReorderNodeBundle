<?php 
namespace RC\PHPCRReorderNodesBundle\Services;

use PHPCR\Util\PathHelper;

class AuthorizeNode {
	
	protected $security;
	protected $nodes;
	protected $dm;
	
	public function __construct($security, $nodes, $dm){
		$this->security = $security;
		$this->nodes = $nodes;
		$this->dm = $dm;
	}
	
	public function isAuthorized($nodepath){
		return $this->nodeExists($nodepath) && $this->security->isGranted($this->getRoles($nodepath));
	}
	
	protected function nodeExists($nodepath){
		foreach($this->nodes as $nodename => $data){
			if($data['childrens'] && strpos( PathHelper::absolutizePath($nodepath, '', false, false), PathHelper::absolutizePath($data['node'], '', false, false), 0 ) !== FALSE ) {
				$this->nodes[$nodename]['node'] = PathHelper::absolutizePath($nodepath, '', false, false);
				return $this->isRealNode($this->nodes[$nodename]['node']);
			}
			if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return $this->isRealNode($this->nodes[$nodename]['node']);							
		}
		return false;
	}
	
	protected function isRealNode($nodepath){
		return ( $this->dm->getPhpcrSession()->nodeExists($nodepath) && count($this->dm->getPhpcrSession()->getNode($nodepath)->getNodeNames()) > 1 );
	}
	
	protected function getRoles($nodepath){
		foreach($this->nodes as $nodename => $data){
			if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return $data['role'];
		}
		return false;		
	}
	
	public function getTemplate($nodepath){
		foreach($this->nodes as $nodename => $data){
			if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) ) return $data['template'];
		}
		return false;
	}
	
	
}