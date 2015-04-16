<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin")
 */
class BackendController extends Controller {

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        return array();
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {

    }

}
