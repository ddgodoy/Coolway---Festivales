<?php

namespace CoolwayFestivales\ApiBundle\Controller;

use CoolwayFestivales\BackendBundle\Entity\Artist;
use CoolwayFestivales\BackendBundle\Entity\ArtistFavorites;
use CoolwayFestivales\SafetyBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FavoriteController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @param Request $request
     * @param User $user
     * @return array
     *
     * @ApiDoc(
     *  section="Favorite",
     *  description="List Favorite",
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  tags={
     *   "stable" = "#4A7023",
     *   "v1" = "#ff0000"
     *  }
     * )
     */
    public function getAction(Request $request, User $user)
    {
        $response = new Response();
        $em = $this->getDoctrine()->getManager();
        $favorites = $em->getRepository("BackendBundle:ArtistFavorites")->findBy( array( 'user' => $user->getId()) );

        $data = array();
        if(count($favorites) > 0 )
        {
            $cont=0;
            foreach($favorites as $favorite)
            {
                $data[$cont]['id'] = $favorite->getArtist()->getId();
                $data[$cont]['name'] = $favorite->getArtist()->getName();
                $cont++;
            }
        }

        $response->setContent(json_encode(array(
            'success' => true,
            'data' => $data,
        )));

        return $response;
    }




    /**
     * @param Request $request
     * @param User $user
     *
     * @ApiDoc(
     *  section="Favorite",
     *  description="Add to Favorite",
     *  requirements={
     *      {"name"="artist_id", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Artist id"},
     *   },
     *  statusCodes={
     *         200="Returned when successful"
     *  },
     *  tags={
     *   "stable" = "#4A7023",
     *   "v1" = "#ff0000"
     *  }
     * )
     * @return array
     */
    public function postAction(Request $request, User $user)
    {
        $artistId = $request->get('artist_id');
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $artistFavorites = $em->getRepository('BackendBundle:ArtistFavorites')
            ->findOneBy( array( 'user' => $user->getId(), 'artist' => $artistId) );

        if ($artistFavorites){
            $response->setContent(json_encode(array(
                'success' => false
            )));
        }else{
            $artist = $em->getRepository('BackendBundle:Artist')
                ->findOneBy( array( 'id' => $artistId ));

            $artistFavorites = new ArtistFavorites();
            $artistFavorites->setArtist($artist);
            $artistFavorites->setUser($user);
            $em->persist($artistFavorites);
            $em->flush();

            $response->setContent(json_encode(array(
                'success' => true
            )));
        }


        return $response;
    }


    /**
     * @param Request $request
     * @param User $user
     * @return array
     *
     * @ApiDoc(
     *  section="Favorite",
     *  description="Remove to Favorite",
     *  requirements={
     *      {"name"="artist_id", "dataType"="string", "requirement"="/^[A-Za-z0-9 _.-]+$/", "description"="Artist id"},
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
    public function deleteAction(Request $request,User $user)
    {
        $artistId = $request->get('artist_id');
        $response = new Response();
        $em = $this->getDoctrine()->getManager();

        $artistFavorites = $em->getRepository('BackendBundle:ArtistFavorites')
            ->findOneBy( array( 'user' => $user->getId(), 'artist' => $artistId) );

        if ($artistFavorites){
            $em->remove($artistFavorites);
            $em->flush();
            $response->setContent(json_encode(array(
                'success' => true
            )));
        }else{
            $response->setContent(json_encode(array(
                'success' => false
            )));
        }


        return $response;
    }



}