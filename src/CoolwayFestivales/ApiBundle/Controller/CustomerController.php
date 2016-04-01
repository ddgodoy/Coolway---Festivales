<?php

namespace CoolwayFestivales\ApiBundle\Controller;

use CoolwayFestivales\SafetyBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;


class CustomerController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @return array
     *
     * @ApiDoc(
     *  section="Customer",
     *  description="Login customer",
     *  requirements={
     *      {"name"="email", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Email Address"},
     *      {"name"="password", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Password"},
     *   },
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  tags={
     *   "stable" = "#4A7023",
     *   "v1" = "#ff0000"
     *  }
     * )
     */
    public function getAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $social = $request->get('social', false);
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $passwordEncoded = $encoder->encodePassword($password, $user->getSalt());

            if ($passwordEncoded === $user->getPassword() || $social) {
                $user->setAccessToken(md5($user->getId() . '-' . date('Y-m-d-H-i-s')));
                $em->persist($user);
                $em->flush();

                $response = array(
                    'success' => true,
                    'access_token' => $user->getAccessToken(),
                    'email' => $user->getEmail());
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'invalid data'
                );
            }

        } else {
            if ($social) {
                $userManager = $this->get('fos_user.user_manager');
                $user = $userManager->createUser();

                $user->setPlainPassword($password);
                $user->addRole("ROLE_USER_APP");
                $user->setUsername($email);
                $user->setEmail($email);
                $user->setEnabled(true);
                $user->setFirstLogin(true);

                $userManager->updateUser($user);

                $user->setAccessToken(md5($user->getId() . '-' . date('Y-m-d-H-i-s')));

                $userManager->updateUser($user);

                $pf = new ProfessionalProfile();
                $pf->setUser($user);
                $pf->setDisponibilidad("a negociar");
                $pf->setDescription("agrega una descripción");
                $pf->setInteres(0);

                $em->persist($pf);
                $em->flush();

                $response['success'] = true;
                $response['access_token'] = $user->getAccessToken();
                $response['email'] = $user->getEmail();
                $response['cv'] = '0';
                $response['image'] = false;
                $response['complete'] = 0;

            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Datos Incorrectos'
                );
            }
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     *
     * @ApiDoc(
     *  section="Customer",
     *  description="Create a customer",
     *   requirements={
     *      {"name"="email", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Email Address"},
     *      {"name"="password", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Password"},
     *   },
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  tags={
     *   "stable" = "#4A7023",
     *   "v1" = "#ff0000"
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            $response['success'] = false;
            $response['message'] = 'El email ya existe';
        } else {

            $user = new User();
            $user->setPassword($password);
            $role = $em->getRepository("SafetyBundle:Role")->findOneByName("ROLE_CUSTOMER");
            $user->addRole($role);
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setEnabled(true);
            $user->setAccessToken(md5($user->getId() . '-' . date('Y-m-d-H-i-s')));

            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Nueva Cuenta')
                ->setFrom('info@coolway.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->get('templating')->render('CoolwayFestivalesApiBundle:Email:welcome.html.twig'),
                    'text/html'
                );

            $this->get('mailer')->send($message);

            $response['success'] = true;
            $response['access_token'] = $user->getAccessToken();
            $response['email'] = $user->getEmail();
        }

        return $response;
    }

    /**
     *
     * @param Request $request
     * @ApiDoc(
     *   resource = true,
     *   description = "Reenvia Contraseña",
     *   requirements={
     *      {"name"="email", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Email Address"},
     *   },
     *   statusCodes = {
     *      200 = "En caso de éxito"
     *   }
     * )
     *
     * @return array
     */
    public function putAction(Request $request)
    {

        $data = $this->getData();
        if($this->checkToken($data,true))
        {
            $em = $this->getDoctrine()->getManager();
            $data = array(
                'status' => 'success',
                'message' => 'Login Correcto'
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'Se produjo un error por favor intentelo nuevamente '
            );
        }

        return $this->setResponse($data);

        $email = $request->get('email');

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('QuikcuUserUserBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserByEmail($email);;
            $password = md5(date('dmYHis'));
            $password = substr($password, 1, 10);
            //$user->setPlainPassword($password);
            $user->setPlainPassword('123456');
            $userManager->updateUser($user);

            $message = \Swift_Message::newInstance()
                ->setSubject('Nueva Contraseña Quikcu')
                ->setFrom('info@quikcu.com')
                ->setTo($user->getEmail())
                ->setBody("Su nueva contraseña es $password");

            $this->get('mailer')->send($message);

            $response['success'] = true;
            $response['message'] = 'Hemos enviado su nueva contraseña a su e-mail';
        } else {
            $response['success'] = false;
            $response['message'] = 'El E-mail no existe';
        }
        return $response;

    }




}