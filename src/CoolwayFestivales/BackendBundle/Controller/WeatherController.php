<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use CoolwayFestivales\BackendBundle\Entity\NotificationStats;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Notification controller.
 *
 * @Route("/admin/weather")
 */
class WeatherController extends Controller
{


    /**
     * @Route("/update", name="weather_update")
     * @Method("GET")
     */
    public function notificationSendAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $festivals = $em->getRepository('BackendBundle:Feast')->findAll();

        foreach ($festivals as $value)
        {
            if (!empty($value->getLatitude()) && !empty($value->getLongitude()))
            {
                $em->getRepository('BackendBundle:Weather')->setForecastInfo
                (
                    $value,
                    $value->getLatitude(),
                    $value->getLongitude()
                );
            }
        }


        return new Response('true');
    }



} // end class