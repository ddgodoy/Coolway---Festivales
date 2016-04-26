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


class AuthenticationController extends FOSRestController implements ClassResourceInterface
{


    /**
     * @param Request $request
     * @return array
     *
     * @ApiDoc(
     *  section="Authentication",
     *  description="Authentication",
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
    public function postAction(Request $request)
    {
        $response = new Response();
        $email = $request->get('email');
        $password = $request->get('password');
        $social = $request->get('social', false);
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($social && $user) {
            $user->setAccessToken(md5($user->getId() . '-' . date('Y-m-d-H-i-s')));
            $em->persist($user);
            $em->flush();

            $response->setContent(json_encode(array(
                'id' => $user->getId(),
                'access_token' => $user->getAccessToken(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            )));

        }else if($social) {
            $user = new User();
            $name = $request->get('name');
            $password = '12345678';
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

            $response->setContent(json_encode(array(
                'id' => $user->getId(),
                'access_token' => $user->getAccessToken(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            )));
        }else if($user) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $passwordEncoded = $encoder->encodePassword($password, $user->getSalt());

            if ($passwordEncoded === $user->getPassword()) {
                $user->setAccessToken(md5($user->getId() . '-' . date('Y-m-d-H-i-s')));
                $em->persist($user);
                $em->flush();

                $response->setContent(json_encode(array(
                    'id' => $user->getId(),
                    'access_token' => $user->getAccessToken(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                )));
            }else
                throw new HttpException(400, "Datos invalidos");
        }else
            throw new HttpException(400, "Datos invalidos");

        return $response;
    }



    /**
     *
     * @param Request $request
     * @ApiDoc(
     *   section="Authentication",
     *   resource = true,
     *   description = "Resend password",
     *   requirements={
     *      {"name"="email", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Email address"},
     *   },
     *   statusCodes = {
     *      200="Returned when successful"
     *   }
     * )
     *
     * @return array
     */
    public function putAction(Request $request)
    {
        $response = new Response();
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('SafetyBundle:User')
            ->findOneBy(array('email' => $email));

        if ($user) {
            $password = $this->generateRandomString(8);
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Nueva contraseña Festival de les arts')
                ->setFrom('info@festivaldelesarts.com')
                ->setTo($user->getEmail())
                ->setBody("Su nueva contraseña es $password ");

            $this->get('mailer')->send($message);

            $response->setContent(json_encode(array(
                'success' => true
            )));
        }else
            throw new HttpException(400, "Datos invalidos");

        return $response;
    }


    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }




}