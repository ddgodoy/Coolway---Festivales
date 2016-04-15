<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CoolwayFestivales\BackendBundle\Entity\Weather;

/**
 * @Route("/admin")
 */
class BackendController extends Controller
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Template()
     */
    public function dashboardAction(Request $request)
    {
        $fs_image = '';
        $feast_id = null;
        $user = $this->get('security.context')->getToken()->getUser();

        if ($user->getFeast())
        {
            $feast_id = $user->getFeast()->getId();
            $fs_image = $user->getFeast()->getImage();
        } else {
            $oFeast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();

            if ($oFeast) {
                $feast_id = $oFeast->getId();
            } else {
                return array();
            }
        }
        $session = $request->getSession();
        $session->set('user_feast_id', $feast_id);
        $session->set('user_feast_logo', $fs_image);
        //
        $auth_checker = $this->get('security.authorization_checker');

        if ($auth_checker->isGranted('ROLE_COOLWAY'))
        {
            return $this->redirect($this->generateUrl('admin_contest'));
            exit();
        }
        return array();
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {}

} // end class