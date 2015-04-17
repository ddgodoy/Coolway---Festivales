<?php

namespace CoolwayFestivales\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class DefaultController extends Controller {

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction() {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR, $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        return $this->render('AppBundle:Frontend:login.html.twig', array(
                    'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
                    'error' => $error
        ));
    }

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction() {
        $request = $this->getRequest();
        $plan = $request->get('plan', '25139');
        $entity = new CoolwayFestivales\SafetyBundle\Entity\User();
        $form = $this->createForm(new CoolwayFestivales\SafetyBundle\Form\UserType(), $entity);

        return $this->render('AppBundle:Backend:register.html.twig', array(
                    'form' => $form->createView(),
                    'error' => false,
                    'plan' => $plan
        ));
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

    /**
     * @Route("/validate/email", name="validate_email")
     */
    public function validateeAction(\Symfony\Component\HttpFoundation\Request $request) {
        $result = array("valid" => true);

        $params = $request->get('prodi_safetybundle_usertype');
        $email = $params['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $em = $this->getDoctrine()->getManager();
            $repo_user = $em->getRepository("SafetyBundle:User");
            $user = $repo_user->findOneBy(array("email" => $email));
            if ($user) {
                $result['valid'] = false;
            }
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($result));
    }

    /**
     * @Route("/validate/username", name="validate_username")
     */
    public function validateuAction(\Symfony\Component\HttpFoundation\Request $request) {
        $result = array("valid" => true);

        $params = $request->get('prodi_safetybundle_usertype');
        $username = $params['username'];

        $em = $this->getDoctrine()->getManager();
        $repo_user = $em->getRepository("SafetyBundle:User");
        $customer = $repo_user->findOneBy(array("username" => $username));

        if ($customer) {
            $result['valid'] = false;
        }


        return new \Symfony\Component\HttpFoundation\Response(json_encode($result));
    }

    /**
     * Crea una nueva customer
     *
     * @Route("/user_add", name="user_create")
     */
    public function createAction(\Symfony\Component\HttpFoundation\Request $request) {
        $entity = new CoolwayFestivales\SafetyBundle\Entity\User();

        $plan = $request->get('plan');

        $form = $this->createForm(new CoolwayFestivales\SafetyBundle\Form\UserType(), $entity);
        $form->bind($request);
        $em = $this->getDoctrine()->getManager();

        try {
            $entity->setEnabled(true);
            $role = $em->getRepository("SafetyBundle:Role")->findOneByName("ROLE_CMS");
            if (!$role) {
                $role = new CoolwayFestivales\SafetyBundle\Entity\Role();
                $role->setDescription("User CMS");
                $role->setName("ROLE_CMS");
                $em->persist($role);
                $em->flush();
            }
            $entity->addRole($role);
            $em->persist($entity);
            $em->flush();

//            //logear al user
//            try {
//                $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($entity, $entity->getPassword(), 'usuarios', $entity->getRoles());
//                $this->container->get('security.context')->setToken($token);
//
//            } catch (Exception $exc) {
//                echo $exc->getTraceAsString();
//            }

            $user_id = $entity->getId();
            return $this->redirect("https://subs.pinpayments.com/apptibase-test/subscribers/$user_id/subscribe/$plan/apptibase-user-$user_id");
        } catch (\Exception $exc) {
            return $this->render('AppBundle:Backend:register.html.twig', array(
                        'error' => $exc->getMessage(),
                        'plan' => ""
            ));
        }
    }

    /**
     * Mi cuenta
     *
     * @Route("/customer_account", name="_customer_account")
     * @Template()
     */
    public function account() {
        $user_id = $this->get("security.context")->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SafetyBundle:User')->find($user_id);
        return array();
    }

    // codificar el passwd
    private function setSecurePassword(&$entity) {
        $confg = Yaml::parse(__DIR__ . '/../../../../app/config/security.yml');
        $params = $confg['security']['encoders'][get_class($entity)];
        $encoder = new MessageDigestPasswordEncoder($params['algorithm'], $params['encode_as_base64'], $params['iterations']);
        $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
        $entity->setPassword($password);
    }

    // codificar el passwd
    private function setSecurePasswordForgot(&$entity) {
        $confg = Yaml::parse(__DIR__ . '/../../../../app/config/security.yml');
        $params = $confg['security']['encoders'][get_class($entity)];
        $encoder = new MessageDigestPasswordEncoder($params['algorithm'], $params['encode_as_base64'], $params['iterations']);
        $password = $encoder->encodePassword($entity->getPasswordForgot(), $entity->getSalt());
        $entity->setPassword($password);
    }

}
