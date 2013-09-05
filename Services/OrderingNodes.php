<?php 
namespace RC\PHPCRReorderNodesBundle\Services;

use PHPCR\Util\NodeHelper;
use PHPCR\Util\PathHelper;

class OrderingNodes {
	
	protected $dm;
	protected $nodes;
	
	public function __construct($dm){
		$this->dm = $dm;
	}
	
	public function newOrder($nodepath, $new){
		
		$old = (array)$this->getNodeNames(PathHelper::absolutizePath($nodepath, '', false, false));
		$reorder = NodeHelper::calculateOrderBefore($old, $new);
		$node = $this->dm->getPhpcrSession()->getNode(PathHelper::absolutizePath($nodepath, '', false, false));
	   	foreach ($reorder as $srcChildRelPath => $destChildRelPath) {
	   		$node->orderBefore($srcChildRelPath, $destChildRelPath);
	   	}
	   	
		$this->dm->flush();
	}
	
	public function getNodeNames($nodepath){
		try {
				return $this->dm->getPhpcrSession()->getNode(PathHelper::absolutizePath($nodepath, '', false, false))->getNodeNames();
		}catch(\Exception $e){
			
		}
	}
	
	public function getChildrens($nodepath, $locale = false){
		return $this->dm->getChildren($this->dm->find(null, PathHelper::absolutizePath($nodepath, '', false, false)), null, 1, $locale);
	}
	
	
	
	
}