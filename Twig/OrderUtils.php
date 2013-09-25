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
            'reorder_parent_path'=>  new \Twig_Function_Method($this, 'getParentPath'),
        );
    }

    public function canReorder($document){

        if(!$document instanceof BaseBlock && !$document instanceof MenuNode ) return false;

        $nodename = $document->getId();

        $data = $this->container->get('rc.phpcr.reorder.nodes.authorizenode');

        if(!$data->isAuthorized($nodename)){
            return false;
        }

        return true;
    }

    public function getPath($document, $locale = false, $ref = true){
        if(!$document instanceof BaseBlock  && !$document instanceof MenuNode) return false;

        $generator = $this->container->get('router');
        $ref = ( $ref )  ? urldecode($this->container->get('request')->getPathInfo()) : false;
        return $generator->generate('rcphpcr_reorder_nodes_homepage', array('nodename' => $document->getId(), 'locale' => $locale, 'ref' => $ref ));
    }

    public function getParentPath($nodename, $locale = false){
        $data = $this->container->get('rc.phpcr.reorder.nodes.authorizenode');
        $parentpath = $data->getParentPath($nodename);

        if($data->isAuthorized($parentpath)){
            return $this->container->get('router')->generate('rcphpcr_reorder_nodes_homepage', array('nodename' => $parentpath, 'locale' => $locale));
        }

        return false;
    }
   
    public function hasChildren($children){
    	try{
            if(!$children instanceof \Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode ) return false;
	    	return count($children->getChildren()) > 1;
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