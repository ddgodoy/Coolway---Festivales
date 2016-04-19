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


class WeatherController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param Feast $feast
     * @return array
     *
     * @ApiDoc(
     *  section="Weather",
     *  description="Weather",
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
            $weathers = $em->getRepository("BackendBundle:Weather")->findBy(
                array('feast'=> $feast->getId()), array('id' => 'DESC'));


            $data = array();
            if(count($weathers) > 0)
            {
                $cont = 0;
                foreach($weathers as $weather)
                {
                    $data[$cont]['forecast_date'] = $weather->getForecastDate();
                    $data[$cont]['min_temp'] = $weather->getMinTemp();
                    $data[$cont]['max_temp'] = $weather->getMaxTemp();
                    $data[$cont]['condition_day'] = $weather->getConditionDay();
                    $data[$cont]['condition_icon'] = $weather->getConditionIcon();
                    $cont++;
                }

                $response->setContent(json_encode(array(
                    'success' => true,
                    'data' => $data,
                )));
            }


        }else{
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'invalid feast'
            )));
        }

        return $response;
    }



}