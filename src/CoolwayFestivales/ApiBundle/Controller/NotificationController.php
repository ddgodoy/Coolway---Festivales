<?php

namespace CoolwayFestivales\ApiBundle\Controller;

use CoolwayFestivales\BackendBundle\Entity\Feast;
use CoolwayFestivales\SafetyBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NotificationController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param Feast $feast
     * @return array
     *
     * @ApiDoc(
     *  section="Notification",
     *  description="List Notification",
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  tags={
     *   "stable" = "#4A7023",
     *   "v1" = "#ff0000"
     *  }
     * )
     */
    public function getAction(Request $request, Feast $feast)
    {
        $response = new Response();

        if(is_object($feast))
        {
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository("BackendBundle:Notification")->findBy(array('feast' => $feast->getId()), array('id' => 'DESC'));

            $data = array();
            if($notifications)
            {
                $cont=0;
                foreach($notifications as $notification)
                {
                    $data[$cont]['name'] = $notification->getName();
                    $data[$cont]['text'] = $notification->getText();
                    $data[$cont]['date'] = $notification->getDate();
                    $cont++;
                }
            }

            $response->setContent(json_encode(array(
                'success' => true,
                'data' => $data,
            )));
        }else{
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'invalid feast'
            )));
        }




        return $response;
    }



}