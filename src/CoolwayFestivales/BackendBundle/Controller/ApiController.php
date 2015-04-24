<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoolwayFestivales\SafetyBundle\Entity\User;
use CoolwayFestivales\BackendBundle\Entity\UserFavorites;
use CoolwayFestivales\BackendBundle\Entity\ArtistFavorites;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller {

    protected $days = array(1 =>'Lunes',2 =>'Martes',3 =>'Miercoles',4 =>'Jueves',5 =>'Viernes',6 =>'Sabado',7 =>'Domingo');

    protected $months = array (1 => 'Enero',2 => 'Febrero',3 => 'Marzo',4 => 'Abril',5 => 'Mayo',6 => 'Junio',7 => 'Julio',8 => 'Agosto',9 => 'Septiembre',10 => 'Octubre',11 => 'Noviembre',12 => 'Diciembre');

	/**
     * Login User
     *
     * @Route("/login", name="api_login")
     * @Template()
     */
    public function loginAction() {
        $data = $this->getData();
		if($this->checkToken($data,true))
		{
			$em = $this->getDoctrine()->getManager();
			$data = array(
				'status' => 'success',
				'message' => 'Login Correcto'
			);
		}
		else {
			$data = array(
				'status' => 'error',
				'message' => 'Se produjo un error por favor intentelo nuevamente '
			);
		}

		return $this->setResponse($data);
    }

    /**
     * Ranking
     *
     * @Route("/ranking", name="api_ranking")
     * @Template()
     */
    public function rankingAction() {
    	$data = $this->getData();
		if($user = $this->checkToken($data))
		{

            $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
            $ranking_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findRanking($feast->getId());
            $favorite_list = $this->getDoctrine()->getRepository('BackendBundle:UserFavorites')->findByUser($user);

            $favorites = array();
            foreach($favorite_list as $f)
                $favorites[$f->getUserFavorite()->getId()] = $f->getUserFavorite()->getId();

            $ranking = array();
            $i = 1;
            foreach( $ranking_list as $r ) {
                $ranking[] = array(
                    'id' => $r['user_id'],
                    'position' => $i,
                    'point'=>$r['total']*100,
                    'name' => $r['user'],
                    'favorite' => isset($favorites[$r['user_id']]) || $r['user_id'] == $user->getId() ? 1 : 0,
                );
                $i++;
            }
			$data = array(
				'status' => 'success',
				'data' => $ranking
			);
		}
		else {
			$data = array(
				'status' => 'error',
				'message' => 'ranking'
			);
		}

		return $this->setResponse($data);
    }

    /**
     * Ranking Favorite
     *
     * @Route("/ranking/favorite", name="api_ranking_favorite")
     * @Template()
     */
    public function rankingFavoriteAction() {
        $data = $this->getData();
        if($user = $this->checkToken($data))
        {
            if( $data['is_favorite'] && $user->getId() != $data['id'] ) {
                $favorite = $this->getDoctrine()->getRepository('BackendBundle:UserFavorites')->findOneBy(array('user'=> $user->getId(),'user_favorite'=>$data['id']));
                $em = $this->getDoctrine()->getManager();
                $em->remove($favorite);
                $em->flush();

                $data = array(
                    'status' => 'success',
                    'data' => 'ranking favorite'
                );
            }
            else if( $user->getId() != $data['id'] )
            {
                $userFavorite = $this->getDoctrine()->getRepository('SafetyBundle:User')->findOneBy(array('id'=> $data['id']));
                if($userFavorite)
                {
                    $favorite = new UserFavorites();
                    $favorite->setUser($user);

                    $favorite->setUserFavorite($userFavorite);
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($favorite);
                    $em->flush();
                }

                $data = array(
                    'status' => 'success',
                    'data' => 'ranking favorite'
                );
            }
            else
            {
                $data = array(
                    'status' => 'error',
                    'message' => 'ranking favorite'
                );    
            }
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'ranking favorite'
            );
        }
        return $this->setResponse($data);
    }

    /**
     * Lineup
     *
     * @Route("/lineup", name="api_lineup")
     * @Template()
     */
    public function lineupAction() {

        $data = $this->getData();
        $user = $this->checkToken($data);

        $feastStageArtist = $this->getDoctrine()->getRepository('BackendBundle:FeastStageArtist')->getLineup();
        if($user)
            $favorite_list = $this->getDoctrine()->getRepository('BackendBundle:ArtistFavorites')->findByUser($user);
        else {
            $favorite_list = array();
        }

        $favorites = array();
        foreach($favorite_list as $f)
            $favorites[$f->getArtist()->getId()] = $f->getArtist()->getId();

        $lineup = array();
        $last_date = '';
        $last_stage = '';
        $i = 0-1;
        foreach( $feastStageArtist as $f )
        {
            $date = $f['date']->format('Y-m-d');
            $stage = $f['stage_id'];

            if($date != $last_date) {
                $i++;
                $j = 0-1;
                $lineup[$i] = array(
                    'date' => $this->days[$f['date']->format('N')].', '.$f['date']->format('j').' '.$this->months[$f['date']->format('n')].' '.$f['date']->format('Y') ,
                    'stages' => array()
                );
            }

            if($stage != $last_stage)
            {
                $j++;
                $lineup[$i]['stages'][$j] = array(
                    'name' => $f['stage'],
                    'artist' => array()
                );
            }

            $lineup[$i]['stages'][$j]['artist'][] = array(
                'id' => $f['artist_id'],
                'name' => $f['artist'],
                'hour' => $f['time']->format('H:i'),
                'favorite' => isset($favorites[$f['artist_id']]) ? '1' : '0'
            );

            $last_date = $date;
            $last_stage = $stage;

        }

        $data = array(
            'status' => 'success',
            'data' => $lineup
        );

        return $this->setResponse($data);
    }

    /**
     * Lineup Favorite
     *
     * @Route("/lineup/favorite", name="api_lineup_favorite")
     * @Template()
     */
    public function lineupFavoriteAction() {
        $data = $this->getData();
        if($user = $this->checkToken($data))
        {
            if( $data['is_favorite'] && $user->getId() != $data['id'] ) {
                $favorite = $this->getDoctrine()->getRepository('BackendBundle:ArtistFavorites')->findOneBy(array('user'=> $user->getId(),'artist'=>$data['id']));
                $em = $this->getDoctrine()->getManager();
                $em->remove($favorite);
                $em->flush();

                $data = array(
                    'status' => 'success',
                    'data' => 'lineup favorite'
                );
            }
            else if( $user->getId() != $data['id'] )
            {
                $artist = $this->getDoctrine()->getRepository('BackendBundle:Artist')->findOneBy(array('id'=> $data['id']));
                if($artist)
                {
                    $favorite = new ArtistFavorites();
                    $favorite->setUser($user);

                    $favorite->setArtist($artist);
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($favorite);
                    $em->flush();
                }

                $data = array(
                    'status' => 'success',
                    'data' => 'lineup favorite'
                );
            }
            else
            {
                $data = array(
                    'status' => 'error',
                    'message' => 'lineup favorite'
                );    
            }
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'lineup favorite'
            );
        }
        return $this->setResponse($data);
    }

    /**
     * Timeline
     *
     * @Route("/timeline", name="api_timeline")
     * @Template()
     */
    public function timelineAction() {
        $data = $this->getData();
        $user = $this->checkToken($data);
        if($user)
        {
            $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
            $timeline_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findTimeline($feast->getId(),$user->getId());

            $timeline = array();
            foreach($timeline_list as $l) {
                if( $l['date']->format('d/m/Y') == date('d/m/Y') )
                    $date = 'Hoy';
                elseif ($l['date']->format('d/m/Y') == date('d/m/Y',strtotime('- 1 days')))
                    $date = 'Ayer';
                else
                    $date = $this->days[$l['date']->format('N')].', '.$l['date']->format('j').' '.$this->months[$l['date']->format('n')];
                $timeline[]=array(
                    'date' => $date,
                    'percent' => $l['total']*100,
                    'music' => $l['music']*100,
                    'dance' => $l['dance']*100,
                );
            }

            $data = array(
                'status' => 'success',
                'data' => $timeline
            );

        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'timeline'
            );
        }

        return $this->setResponse($data);

    }

    /**
     * Slider
     *
     * @Route("/slider", name="api_slider")
     * @Template()
     */
    public function sliderAction() {

        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        $slider_list = $this->getDoctrine()->getRepository('BackendBundle:Step')->findSlider($feast->getId());

        $slider = array();
        foreach($slider_list as $s) {
            $slider[]=array(
                'text' => $s->getText(),
            );
        }

        $data = array(
            'status' => 'success',
            'data' => $slider
        );
        return $this->setResponse($data);
    }

    /**
     * Map
     *
     * @Route("/map", name="api_map")
     * @Template()
     */
    public function mapAction() {
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();

        $img = $this->getDoctrine()->getRepository('BackendBundle:Images')->findOneBy(array(
            'feast'=>$feast->getId(),
            'code_name' => 'plano'
        ));

        if($img) {
            $images = array(
                'image' => $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/images/'.$img->getPath(),
                'title' => $feast->getName()
            );

            $data = array(
                'status' => 'success',
                'data' => $images
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'map'
            );
        }

        return $this->setResponse($data);
    }

    /**
     * Awards
     *
     * @Route("/awards", name="api_awards")
     * @Template()
     */
    public function awardsAction() {       
            
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        $a = $this->getDoctrine()->getRepository('BackendBundle:Award')->findOneBy(array(
            'feast'=>$feast->getId()
        ));

        if($a)
        {
            $award = array (
                'image' => $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/awards/'.$a->getPath(),
                'title' => $a->getName(),
                'text' => $a->getTermsConditions()
            );

            $data = array(
                'status' => 'success',
                'data' => $award
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'awards'
            );
        }

        return $this->setResponse($data);
    }

    /**
     * Terms
     *
     * @Route("/terms", name="api_terms")
     * @Template()
     */
    public function termsAction() {       
            
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        $a = $this->getDoctrine()->getRepository('BackendBundle:Award')->findOneBy(array(
            'feast'=>$feast->getId()
        ));

        if($a)
        {
            $award = array (
                'image' => $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/awards/'.$a->getPath(),
                'title' => $a->getName(),
                'text' => $a->getTermsConditions()
            );

            $data = array(
                'status' => 'success',
                'data' => $award
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'terms'
            );
        }

        return $this->setResponse($data);
    }

    /**
     * Profile
     *
     * @Route("/profile", name="api_profile")
     * @Template()
     */
    public function profileAction() {
        $data = $this->getData();
        if($user = $this->checkToken($data))
        {
            $profile = array(
                'name' => $user->getName(),
                'email' => $user->getEmail()
            );

            $data = array(
                'status' => 'success',
                'data' => $profile
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'profile'
            );
        }

        return $this->setResponse($data);
    }

    /**
     * UpdateProfile
     *
     * @Route("/profile/update", name="api_profile_update")
     * @Template()
     */
    public function profileUpdateAction() {
        $data = $this->getData();
        if($user = $this->checkToken($data))
        {
            $em = $this->getDoctrine()->getManager();
            
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            
            $em->persist($user);
            $em->flush();

            $data = array(
                'status' => 'success',
                'data' => 'data saved'
            );
        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'profile'
            );
        }

        return $this->setResponse($data);
    }

    private function checkToken($data, $create = false) {
    	
    	if (isset($data['token']) && $token = $data['token'])
    	{
    		$em = $this->getDoctrine()->getManager();
        	if($user = $this->getDoctrine()->getRepository('SafetyBundle:User')->findOneBy(array('token_phone'=>$token)))
        		return $user;
            if(!$create)
                return false;
        	
            $user = new User();
        	$user->setTokenPhone($token);
        	$user->setUsername($token);
        	$user->setPassword(md5(time().rand()));
        	$user->setSalt(md5(time().rand()));
        	$user->setName($data['name']);
        	$user->setEmail(strtolower(trim($data['email'])));
        	$em->persist($user);
        	$em->flush();

        	return true;
    	}
    	return false;
    }

    private function getData() {
        $request = $this->getRequest();
        $data = json_decode($request->getContent(),true);
        return $data;
    }

    private function setResponse($data)
    {
    	$response = new JsonResponse();
		$response->headers->set('Access-Control-Allow-Headers', 'Content-Type, x-xsrf-token');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		$response->setData($data);
		return $response;
    }
}