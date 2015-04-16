<?php

namespace CoolwayFestivales\SafetyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecuredController extends Controller {

    /**
     * @Route("/login", name="_admin_login")
     * @Template()
     */
    public function loginAction() {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        );
    }

    /**
     * @Route("/login_check", name="_admin_security_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_admin_logout")
     */
    public function logoutAction() {
        // ...
    }

}
