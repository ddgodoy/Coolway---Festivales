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


class CoolwayController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param Feast $feast
     * @return array
     *
     * @ApiDoc(
     *  section="Coolway",
     *  description="Coolway",
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
            $coolway = $em->getRepository("BackendBundle:Contest")->findBy(
                array('feast'=> $feast->getId()), array('id' => 'ASC'));


            $data = array();
            if(count($coolway) > 0)
            {
                $cont = 0;
                foreach($coolway as $item)
                {
                    $data[$cont]['image'] = $item->getName();
                    $data[$cont]['winner'] = $item->getWinner();
                    $data[$cont]['date'] = $item->getLoadDate();
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