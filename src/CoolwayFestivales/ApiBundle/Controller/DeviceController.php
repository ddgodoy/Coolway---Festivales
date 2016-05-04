<?php

namespace CoolwayFestivales\ApiBundle\Controller;

use CoolwayFestivales\BackendBundle\Entity\Feast;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use CoolwayFestivales\SafetyBundle\Entity\Device;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;



class DeviceController extends FOSRestController implements ClassResourceInterface
{

    /**
     *
     * @param Request $request
     * @param Feast $feast
     * @ApiDoc(
     *   section="Device",
     *   resource = true,
     *   description = "Add Device",
     *   requirements={
     *      {"name"="access_token", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="access token"},
     *      {"name"="os", "dataType"="integer", "description"="operative system"},
     *      {"name"="device_token", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="device token"},
     *   },
     *   statusCodes = {
     *      200="Returned when successful"
     *   }
     * )
     *
     * @return array
     */
    public function postAction(Request $request, Feast $feast)
    {
        $response = new Response();
        $accessToken = $request->get('access_token');
        $deviceToken = $request->get('device_token');
        $os = $request->get('os');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SafetyBundle:User')->findOneBy(array('accessToken' => $accessToken));

        if ($user) {

            $user->setNotificationActive(true);
            $em->persist($user);

            $device = new Device();
            $device->setOs(intval($os));
            $device->setToken($deviceToken);
            $device->setUser($user);
            $device->setFeast($feast);
            $em->persist($device);
            $em->flush();

            $response->setContent(json_encode(array(
                'status' => true,
                'device_id' => $device->getId()
            )));
        }else
            throw new HttpException(400, "No existe el usuario");


        return $response;

    }

    /**
     *
     * @param Request $request
     * @param Feast $feast
     * @ApiDoc(
     *   section="Device",
     *   resource = true,
     *   description = "Delete device",
     *   requirements={
     *      {"name"="access_token", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="access token"},
     *      {"name"="device_token", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="device token"},
     *   },
     *   statusCodes = {
     *      200 = "En caso de éxito"
     *   }
     * )
     *
     * @return array
     */
    public function deleteAction(Request $request, Feast $feast)
    {
        $response = new Response();
        $accessToken = $request->get('access_token');
        $deviceToken = $request->get('device_token');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('SafetyBundle:User')->findOneBy(array('accessToken' => $accessToken));

        if ($user) {

            $user->setNotificationActive(false);
            $em->persist($user);

            $devices = $em->getRepository('SafetyBundle:Device')->findBy(array('user' => $user->getId(), 'token' => $deviceToken));

            if($devices)
            {
                foreach ($devices as $device)
                    $em->remove($device);
            }

            $em->flush();
            $response->setContent(json_encode(array(
                'status' => true
            )));
        } else
            throw new HttpException(400, "No existen dispositivos asociados");


        return $response;

    }
}