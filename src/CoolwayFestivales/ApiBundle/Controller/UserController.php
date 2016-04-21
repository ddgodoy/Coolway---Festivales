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
                $role->setDescription('App Customer');
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
                'id' => $user->getId(),
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
     *   description = "Update password",
     *   requirements={
     *      {"name"="email", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Email Address"},
     *      {"name"="current_password", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Current Password"},
     *      {"name"="new_password", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="New Password"},
     *   },
     *   statusCodes = {
     *      200= "Returned when successful"
     *   }
     * )
     *
     * @return array
     */
    public function putAction(Request $request)
    {
        $response = new Response();
        $email = $request->get('email');
        $currentPassword = $request->get('current_password');
        $newPassword = $request->get('new_password');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $currentPasswordEncoded = $encoder->encodePassword($currentPassword, $user->getSalt());

            if($user->getPassword() != $currentPasswordEncoded) {
                throw new HttpException(400, "Datos invalidos");
            } else {
                $user->setPassword($newPassword);
                $em->persist($user);
                $em->flush();

                $response->setContent(json_encode(array(
                    'id' => $user->getId(),
                    'access_token' => $user->getAccessToken(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                )));
                return $response;
            }
        }
        else
            throw new HttpException(400, "Datos invalidos");
    }




}