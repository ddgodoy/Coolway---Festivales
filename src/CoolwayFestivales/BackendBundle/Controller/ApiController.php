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
use CoolwayFestivales\BackendBundle\Entity\UserFeastData;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller {

    protected $days = array(1 =>'Lunes',2 =>'Martes',3 =>'Miercoles',4 =>'Jueves',5 =>'Viernes',6 =>'Sabado',7 =>'Domingo');

    protected $months = array (1 => 'Enero',2 => 'Febrero',3 => 'Marzo',4 => 'Abril',5 => 'Mayo',6 => 'Junio',7 => 'Julio',8 => 'Agosto',9 => 'Septiembre',10 => 'Octubre',11 => 'Noviembre',12 => 'Diciembre');

    /**
     * Add Data
     *
     * @Route("/data", name="api_data")
     * @Template()
     */
    public function dataAction() {
        $data = $this->getData();
        $user = $this->checkToken($data);
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        
        if($data['first'] == "0" && $data['logged'] == "1" && $user)
        {
            $d = new \DateTime();
            $fd = new UserFeastData();
            $fd->setUser($user);
            $fd->setFeast($feast);            
            $fd->setTotal($data['total']);
            $fd->setDance($data['dance']);
            $fd->setMusic($data['music']);
            $fd->setLatitude($data['latitude']);
            $fd->setLongitude($data['longitude']);
            $fd->setTotalShare('0');
            $fd->setDate($d);

            $em = $this->getDoctrine()->getManager();
            $em->persist($fd);
            $em->flush();
        }

        $information = array();
        $information['position'] = "0";
        $information['points'] = "0";
        $information['total'] = "0";

        if($data['logged'] == "1" && $user )
        {
            $total = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findMyTotal($feast->getId(),$user->getId());

            if($total)            
                $information['total'] = ceil($total['total']*100);

            $ranking_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findRanking($feast->getId());
            $i = 1;

            foreach($ranking_list as $r)
            {
                if( $r['user_id'] == $user->getId() )
                {
                    $information['position'] = $i;
                    $information['points'] = ceil($r['total']*100);
                    break;
                }
            }
            
            
            $lastData = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findLastData($user->getId());
            if($lastData)
            {
                $information['dance'] = $lastData['dance']*100;
                $information['music'] = $lastData['music']*100;
                $information['feast'] = $lastData['total']*100;
            }
            else
            {
                $information['dance'] = "0";
                $information['music'] = "0";
                $information['feast'] = "0";
            }
        }
        else
        {
            $information['total'] = "0";
            $information['points'] = "0";
            $information['position'] = "0";
            $information['dance'] = "0";
            $information['music'] = "0";
            $information['feast'] = "0";
        }
            
        $media = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findMedia($feast->getId());
        
        if($media)
            $information['media'] = ceil(($media['total']*100)/$media['quantity']);

        $data = array(
            'status' => 'success',
            'data' => $information
        );

        return $this->setResponse($data);
    }

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
                    'point'=>ceil($r['total']*100),
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
        $data = array('token'=>'1e93ee47231575bd');
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
                    'percent' => ceil($l['total']),
                    'music' => ceil($l['music']),
                    'dance' => ceil($l['dance']),
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
        $a = $this->getDoctrine()->getRepository('BackendBundle:Terms')->findOneBy(array(
            'id'=>1
        ));

        if($a)
        {
            $terms = array (
                'title' => $a->getName(),
                'text' => $a->getText()
            );

            $data = array(
                'status' => 'success',
                'data' => $terms
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

    /**
     * SendNotification
     *
     * @Route("/notification/send", name="api_notification_send")
     * @Template()
     */
    public function notificationSendAction()
    {
        $url = 'https://android.googleapis.com/gcm/send';

        $users =  $this->getDoctrine()->getRepository('SafetyBundle:User')->findNotification();

        $ids = array();

        foreach($users as $u)
        {
            $ids[]= $u->getNotificationId();            
        }

        if(!count($ids))
            die("not user to send");

        $em = $this->getDoctrine()->getManager();
        $notification_list = $this->getDoctrine()->getRepository('BackendBundle:Notification')->findBy(array('send'=>0));

        foreach($notification_list as $n)
        {

            $fields = array(
                "data"=>array(
                    'title'=>$n->getName(),
                    'message'=>$n->getText(),
                ),
                "registration_ids"=>$ids 
            );

            $headers = array( 
                'Authorization: key=AIzaSyCFpBmNym9kaRPoUA-ZKogSk-QZzvLhlfc',
                'Content-Type: application/json'
            );
            
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            file_put_contents('notification_data.text', "$result\r\n",FILE_APPEND);

            $n->setSend(1);
            $em->persist($n);
        }
        $em->flush();

        die();

        //sender id = 1090006415155;
        //api key = AIzaSyCFpBmNym9kaRPoUA-ZKogSk-QZzvLhlfc
    }

    private function checkToken($data, $create = false) {
    	
    	if (isset($data['token']) && $token = $data['token'])
    	{
    		$em = $this->getDoctrine()->getManager();
        	if($user = $this->getDoctrine()->getRepository('SafetyBundle:User')->findOneBy(array('token_phone'=>$token)))
            {
                if(isset($data['notificationId']) && $data['notificationId'] )
                {
                    $user->setNotificationId($data['notificationId']);
                    $em->persist($user);
                    $em->flush();
                }
        		return $user;
            }
            if(!$create)
                return false;
        	
            $user = new User();
        	$user->setTokenPhone($token);
        	$user->setUsername($token);
        	$user->setPassword(md5(time().rand()));
        	$user->setSalt(md5(time().rand()));
        	$user->setName($data['name']);
        	$user->setEmail(strtolower(trim($data['email'])));
            
            if(isset($data['notificationId']) && $data['notificationId'] )
                $user->setNotificationId($data['notificationId']);

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