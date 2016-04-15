<?php

namespace CoolwayFestivales\ApiBundle\Controller;

use CoolwayFestivales\SafetyBundle\Entity\Role;
use CoolwayFestivales\SafetyBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;


class UserController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @return array
     *
     * @ApiDoc(
     *  section="User",
     *  description="Create a user",
     *   requirements={
     *      {"name"="name", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Full Name"},
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
        $response = new Response();
        $name = $request->get('name');
        $email = $request->get('email');
        $password = $request->get('password');

        if(!isset($name))
            throw new HttpException(400, "El campo nombre es obligatorio");
        if(!isset($email))
            throw new HttpException(400, "El campo email es obligatorio");
        if(!isset($password))
            throw new HttpException(400, "El campo contraseña es obligatorio");


        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            throw new HttpException(400, "El email ya se encuentra registrado");
        } else {

            $user = new User();
            $user->setPassword($password);
            $role = $em->getRepository("SafetyBundle:Role")->findOneByName("ROLE_CUSTOMER");
            if(count($role) < 1)
            {
                $role =  new Role();
                $role->setName('ROLE_CUSTOMER');
                $role->setDescription('ROLE_CUSTOMER');
                $em->persist($role);
            }
            $user->setName($name);
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
            $response->setContent(json_encode(array(
                'access_token' => $user->getAccessToken(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            )));
            return $response;
        }
    }

    /**
     *
     * @param Request $request
     * @ApiDoc(
     *   section="User",
     *   resource = true,
     *   description = "Reenviar Contraseña",
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