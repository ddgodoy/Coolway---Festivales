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


class MapController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param Feast $feast
     * @return array
     *
     * @ApiDoc(
     *  section="Map",
     *  description="Precinct Map",
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
            $precinct = $em->getRepository("BackendBundle:Feast")->findOneBy(
                array('id'=> $feast->getId()), array('id' => 'DESC'));
            $maps = $em->getRepository("BackendBundle:Googlemap")->findBy(
                array('feast'=> $feast->getId()));

            $data = array();

            if(count($precinct) > 0)
            {
                $data['name'] = $precinct->getName();
                $data['image'] = $precinct->getPath();
                $data['latitude'] = $precinct->getLatitude();
                $data['longitude'] = $precinct->getLongitude();
            }


            if(count($maps) > 0)
            {
                $cont = 0;
                foreach($maps as $map)
                {
                    $data['locations'][$cont]['name'] = $map->getName();
                    $data['locations'][$cont]['detail'] = $map->getDetail();
                    $data['locations'][$cont]['image'] = $map->getPath();
                    $data['locations'][$cont]['latitude'] = $map->getLatitude();
                    $data['locations'][$cont]['longitude'] = $map->getLongitude();
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