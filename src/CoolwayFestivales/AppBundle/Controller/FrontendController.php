<?php

namespace CoolwayFestivales\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\AsseticBundle\AsseticBundle;

class FrontendController extends Controller {

    /**
     * @Route("/", name="frontend")
     * @Template()
     */
    public function indexAction() {

        return $this->render("AppBundle:Frontend:index.html.twig");
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * @Template()
     */
    public function dashboardAction() {

        return $this->render("AppBundle:Frontend:dashboard.html.twig");
    }

    /**
     * @Route("/notices", name="notices")
     * @Template()
     */
    public function noticesAction() {

        return $this->render("AppBundle:Frontend:notices.html.twig");
    }

    /**
     * @Route("/notice_detail", name="notice_detail")
     * @Template()
     */
    public function notice_detailAction() {

        return $this->render("AppBundle:Frontend:notice_detail.html.twig");
    }

}
