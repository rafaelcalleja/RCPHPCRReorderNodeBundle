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

    public function getParentPath($nodepath){
        return PathHelper::getParentPath(PathHelper::absolutizePath($nodepath, '', false, false));
    }
	
	public function isAuthorized($nodepath){
		return $this->nodeExists($nodepath) && $this->security->isGranted($this->getRoles($nodepath));
	}

    protected function nodeExists($nodepath){

		foreach($this->nodes as $nodename => $data){

            $node = $data['node'];
			$data['node'] = PathHelper::absolutizePath($data['node'], '', false, false);
			$nodepath = PathHelper::absolutizePath($nodepath, '', false, false);


			if($data["denied_root"] && $nodepath == $data['node'] ) return false;


			if($data['childrens'] &&  strpos( $nodepath, $data['node'], 0 ) !== FALSE ) {
                //Cuando el nodo es un hijo de un nodo autorizado, copiamos la configuración como nodo nuevo

                $newnodename = 'custom_node_' . strval(count($this->nodes)+1);
                $this->nodes[$newnodename] = $this->nodes[$nodename];
                $this->nodes[$newnodename]['node'] = $nodepath;
				return $this->isRealNode($this->nodes[$newnodename]['node']);
			}


            if($data['childrens'] && @preg_match("/$node/", $nodepath) ){
                return $this->isRealNode($nodepath);
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
            $node = $data['node'];
			if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) || @preg_match("/$node/", $nodepath)  ) return $data['role'];
		}
		return false;		
	}
	
	public function getTemplate($nodepath){
		foreach($this->nodes as $nodename => $data){
            $node = $data['node'];
			if($data['node'] == PathHelper::absolutizePath($nodepath, '', false, false) || @preg_match("/$node/", $nodepath) ) return $data['template'];
		}
		return false;
	}
	
	
}