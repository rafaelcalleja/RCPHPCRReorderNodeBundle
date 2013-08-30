<?php
namespace RC\PHPCRReorderNodesBundle\Twig;

use Symfony\Cmf\Bundle\BlockBundle\Document\BaseBlock;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderUtils extends \Twig_Extension
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * @var \Doctrine\ODM\PHPCR\DocumentManager
	 */
	protected $dm;

    public function __construct(ContainerInterface $container, $objectManagerName){
    	$this->container = $container;
    	$this->dm = $this->container->get('doctrine_phpcr')->getManager($objectManagerName);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
        	'order_has_children'=>  new \Twig_Function_Method($this, 'hasChildren'),
            'order_can_reoder'=>  new \Twig_Function_Method($this, 'canReorder'),
            'reorder_path'=>  new \Twig_Function_Method($this, 'getPath'),
        );
    }

    public function canReorder($document){
        //var_dump(get_class($document), $document->getId());
        if(!$document instanceof BaseBlock && !$document instanceof MenuNode ) return false;

        $nodename = $document->getId();

        $data = $this->container->get('rc.phpcr.reorder.nodes.authorizenode');

        if(!$data->isAuthorized($nodename)){
            return false;
        }

        return true;
    }

    public function getPath($document){
        if(!$document instanceof BaseBlock  && !$document instanceof MenuNode) return false;

        $generator = $this->container->get('router');
        return $generator->generate('rcphpcr_reorder_nodes_homepage', array('nodename' => $document->getId()));
    }
   
    public function hasChildren($children){
    	try{
            if(!$children instanceof \Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode ) return false;
	    	return count($children->getChildren()) > 0;
    	}catch(\Exception $e){
    		return false;
    	}
    }


    
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rc_phpcr_reorder_utils_twig_extension';
    }
}