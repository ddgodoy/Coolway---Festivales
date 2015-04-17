<?php

namespace CoolwayFestivales\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sabberworm\CSS\Parser;

/**
 * Blog controller.
 *
 * @Route("/cms")
 */
class BackendController extends Controller {

    /**
     * @Route("/", name="cms_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        return array();
    }

}
