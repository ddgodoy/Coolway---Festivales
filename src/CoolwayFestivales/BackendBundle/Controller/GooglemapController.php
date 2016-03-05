<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
//use CoolwayFestivales\BackendBundle\Form\FeastType;

/**
 * Googlemap controller.
 *
 * @Route("/admin/googlemap")
 */
class GooglemapController extends Controller
{
    /**
     * @Route("/", name="admin_googlemap")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render(
          'BackendBundle:Googlemap:index.html.twig', array("test" => 'test')
        );
    }
   
} // end class