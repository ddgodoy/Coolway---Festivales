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


class FoodiesController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param Feast $feast
     * @return array
     *
     * @ApiDoc(
     *  section="Foodies",
     *  description="List Foodies",
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
            $foodies = $em->getRepository("BackendBundle:Foodies")->findBy(array('feast' => $feast->getId()));

            $data = array();
            $cont = 0;
            foreach($foodies as $entity)
            {
                $data[$cont]['id'] = $entity->getId();
                $data[$cont]['name'] = $entity->getName();
                $data[$cont]['description'] = $entity->getDescription();
                $data[$cont]['image_profile'] = $entity->getPath();
                $data[$cont]['image_cover'] = $entity->getCover();
                $data[$cont]['website'] = $entity->getWebsite();
                $data[$cont]['facebook'] = $entity->getFacebook();
                $data[$cont]['twitter'] = $entity->getTwitter();
                $data[$cont]['instagram'] = $entity->getInstagram();
                $cont++;
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