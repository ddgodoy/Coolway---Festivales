<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
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

    private $convertPoint = 100;
    private $km = 1.5;
    private $kf = 0.5;
    private $kr = 0.5;


    /**
     * Share
     *
     * @Route("/share", name="api_share")
     * @Template()
     */
    public function shareAction() {
        $data = $this->getData();
        $user = $this->checkToken($data);
        if($user){

            $em = $this->getDoctrine()->getManager();

            $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
            $lastValue = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findLastDataNotNull($feast->getId(),$user->getId());
            $lastShare = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findLastShare($feast->getId(),$user->getId());

            $checkTime = date('Y-m-d H:i:00',strtotime("-5 minutes"));
            if(!$lastShare || $lastShare['date']->format('Y-m-d H:i:00') < $checkTime) {

                $totalShare = 5 * $lastValue['total'] / 100;

                $newData = new UserFeastData();
                $newData->setUser($user);
                $newData->setFeast($feast);
                $newData->setTotal($lastValue['total']+$totalShare);
                $newData->setDance(0);
                $newData->setMusic(0);
                $newData->setTotalShare(5);
                $newData->setLatitude($data['latitude']);
                $newData->setLongitude($data['longitude']);
                $newData->setInConcert($this->checkInConcert($data['latitude'],$data['longitude'],$feast->getLatitude(),$feast->getLongitude(),$feast->getDateFrom(),$feast->getDateTo()));
                $newData->setDate(new \Datetime());
                $em->persist($newData);
                $em->flush();


                $title = "Felicitaciones!!";
                $message= "Has aumentado tu puntuación en un 5%. Sigue de fiesta y consigue nuestro premio.";

                $recipients = array(
                    'Android'=>array(),
                    'IOS'=>array()
                );

                $recipients[$user->getOs()][]= $user->getNotificationId();

                $this->send($title,$message,$recipients);
            }

            $data = array(
                'status' => 'success',
                'data' => 'Point adds'
            );

        }
        else {
            $data = array(
                'status' => 'error',
                'message' => 'ERROR SHARE ADD POINT'
            );
        }

        return $this->setResponse($data);

    }

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
            $fd->setInConcert($this->checkInConcert($data['latitude'],$data['longitude'],$feast->getLatitude(),$feast->getLongitude(),$feast->getDateFrom(),$feast->getDateTo()));
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
                $information['total'] = ceil($total['total']*$this->convertPoint);

            $ranking_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findRanking($feast->getId());
            $i = 1;

            foreach($ranking_list as $r)
            {
                if( $r['user_id'] == $user->getId() )
                {
                    $information['position'] = $i;
                    $information['points'] = ceil($r['total']*$this->convertPoint);
                    break;
                }
                $i++;
            }
            
            
            $lastData = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findLastData($user->getId());
            if($lastData)
            {
                $information['dance'] = ceil($lastData['dance']*$this->convertPoint);
                $information['music'] = ceil($lastData['music']*$this->convertPoint);
                $information['feast'] = ceil($lastData['total']*$this->convertPoint);
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

        //$total_day = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findTotalDay($feast->getId(),$tmpDate);
        //$user_day = count( $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findUsersForDay($feast->getId(),$tmpDate) );
            
        $totalForFeast = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findTotal($feast->getId());
        $usersForFeast = count ( $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findUsersForFeast($feast->getId()) );
        if($usersForFeast)
            $media = $totalForFeast['total'] / $usersForFeast;
        else
            $media = 0;
        
        $information['media'] = ceil($media*$this->convertPoint);

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
                    'point'=>ceil($r['total']*$this->convertPoint),
                    'name' => $r['user'],
                    'favorite' => isset($favorites[$r['user_id']]) || $r['user_id'] == $user->getId() ? 1 : 0,
                );
                if($i == 100)
                    break;
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
        $first = true;
        $date_array= array();
        foreach( $feastStageArtist as $f )
        {
            $date_object = !is_object($f['date'])?new \DateTime($f['date']):$f['date'];
            $date = $date_object->format('Y-m-d');
            $stage = $f['stage_id'];

            if($date != $last_date ) {
                $date_array[] = $date; 
                if($first || $f['time']->format('G') > '06')
                {
                    $first= false;
                    $i++;
                    $j = 0-1;
                    $last_stage = '';
                    $lineup[$i] = array(
                        'date' => $this->days[$date_object->format('N')].', '.$date_object->format('j').' '.$this->months[$date_object->format('n')].' '.$date_object->format('Y') ,
                        'stages' => array()
                    );
                }
                else
                   $date = $last_date;
            }
            elseif ( $f['time']->format('G') <= '06' )
            {
                $date = $date_array[count($date_array) -1 ];
                $i--;
            }

            $stageExist = false;
            if($stage != $last_stage)
            {
                foreach($lineup[$i]['stages'] as $k => $s) {
                    if ($s['name'] == $f['stage'] ) {
                        $stageExist = $k;
                        break;
                    }
                        
                }

                if($stageExist === false ) {
                    $j++;
                    $lineup[$i]['stages'][$j] = array(
                        'name' => $f['stage'],
                        'artist' => array()
                    );
                }
            }

            if($stageExist === false)
                $t = $j;
            else
                $t = $stageExist;

            $lineup[$i]['stages'][$t]['artist'][] = array(
                'id' => $f['artist_id'],
                'name' => $f['artist'],
                'hour' => $f['time']->format('H:i'),
                'date' => $date_object->format('Y-m-d'),
                'cache' => '2',
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
            if( $data['is_favorite'] ) {
                $favorite = $this->getDoctrine()->getRepository('BackendBundle:ArtistFavorites')->findOneBy(array('user'=> $user->getId(),'artist'=>$data['id']));
                $em = $this->getDoctrine()->getManager();
                $em->remove($favorite);
                $em->flush();

                $data = array(
                    'status' => 'success',
                    'data' => 'lineup favorite'
                );
            }
            else
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
            //$feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
            $timeline_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findTimeline($user->getId());

            $timeline = array();
            foreach($timeline_list as $l) {
                if( $l['date']->format('d/m/Y') == date('d/m/Y') )
                    $date = 'Hoy';
                elseif ($l['date']->format('d/m/Y') == date('d/m/Y',strtotime('- 1 days')))
                    $date = 'Ayer';
                else
                    $date = $this->days[$l['date']->format('N')].', '.$l['date']->format('j').' '.$this->months[$l['date']->format('n')];
                
                $music =  round( $l['music'] * $this->kf * $this->kr  / $l['total'] * 100 );
                $dance = 100 - $music;

                //$tmpDate = $l['date']->format('Y-m-d');
                //$total_day = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findTotalDay($feast->getId(),$tmpDate);
                //$user_day = count( $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findUsersForDay($feast->getId(),$tmpDate) );

                //$media = ceil ( ($total_day['total']/$user_day )*$this->convertPoint );

                $timeline[]=array(
                    'date' => $date,
                    'total' => ceil($l['total']*$this->convertPoint),
                    //'media' => $media,
                    'music' => $music,
                    'dance' => $dance,
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

        $slider = array();
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        if($feast){
            $slider_list = $this->getDoctrine()->getRepository('BackendBundle:Step')->findSlider($feast->getId());


            foreach($slider_list as $s) {
                $slider[]=array(
                    'text' => $s->getText(),
                );
            }
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
        
        $background = $this->getDoctrine()->getRepository('BackendBundle:Images')->findOneBy(array(
            'feast'=>$feast->getId(),
            'code_name' => 'background'
        ));

        $a = $this->getDoctrine()->getRepository('BackendBundle:Award')->findOneBy(array(
            'feast'=>$feast->getId()
        ));

        if($a)
        {
            $award = array (
                'image' => $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/awards/'.$a->getPath(),
                'title' => $a->getName(),
                'text' => $a->getTermsConditions(),

            );
            if($background)
            $award['background'] = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost().'/uploads/images/'.$background->getPath();

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
        $users =  $this->getDoctrine()->getRepository('SafetyBundle:User')->findNotification();

        $ids = array(
            'Android'=>array(),
            'IOS'=>array()
        );

        foreach($users as $u)
        {
            $ids[$u->getOs()][]= $u->getNotificationId();
        }

        if(!count($ids['Android']) || !count($ids['IOS']) )
            die("not user to send");

        $em = $this->getDoctrine()->getManager();
        $notification_list = $this->getDoctrine()->getRepository('BackendBundle:Notification')->findBy(array('send'=>0));

        foreach($notification_list as $n)
        {

            $this->send($n->getName(),$n->getText(),$ids);

            $n->setSend(1);
            $em->persist($n);
        }
        $em->flush();

        die();
    }

    /**
     * Notification Artist
     *
     * @Route("/notification/artist", name="api_notification_artist")
     * @Template()
     */
    public function notificationArtistAction()
    {
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        $artist = $this->getDoctrine()->getRepository('BackendBundle:FeastStageArtist')->findNextArtist($feast->getId());

        if(isset($artist['id']))
        {
            $users = $this->getDoctrine()->getRepository('BackendBundle:ArtistFavorites')->findUserByArtist($artist['id']);
            if(count($users)) {
                $recipients = array(
                    'Android'=>array(),
                    'IOS'=>array()
                );

                foreach($users as $u)
                {
                    $recipients[$u['os']][] = $u['notificationId'];
                }

                $title = "Quedan 15 minutos";
                $message = "para que comience el concierto de ".$artist['artist']." en el  ".$artist['stage']."!";

                $this->send($title,$message,$recipients); 
            }
        }
        die();
    }

    /**
     * Notification Ranking
     *
     * @Route("/notification/ranking", name="api_notification_ranking")
     * @Template()
     */
    public function notificationRankingAction()
    {
        $top = array(
            "A este nivel yo ya te invitaba a mis fiestas.",
            "Tus zapas echan humo!",
            "Estas que te sales!",
            "Si sigues así…esta noche triunfas!",
            "Sigue así y acabas en el escenario.",
            "Elvis/Michael Jackson estaría orgulloso de ti.",
            "El año que viene te veo en el cartel del Viña!",
            "Eres el rey de la pista!",
            "Tu eres de otro nivel!",
        );
        $middle = array(
            "Ya veo que te estas viniendo arriba!",
            "Puedes superarte…y lo sabes!",
            "Te estas poniendo a tono!",
            "Venga que lo petas.",
            "Currátelo o te van a ganar!",
            "Los de tu lado bailan mejor que tú.",
            "Ahora es el momento de dar el gran paso…anímate!",
            "Venga…ahora o nunca!",
        );
        $bottom = array(
            "Desgastate la suela de los zapatos!",
            "Yo soy tu y me vuelvo pa´casa.",
            "Puff…esto es lo que más das de ti?",
            "Mi abuela tiene más marcha que tú!",
            "Eres un Loser!",
            "Pareces un maniquí",
            "Pégate una ducha y vuelves!",
            "Tienes miedo de tener agujetas?",
            "Te mueves menos que un Playmobil.",
            "Para estar así vete a un concierto de Julio Iglesias!",
        );
        $feast = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findCurrent();
        $ranking_list = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findRanking($feast->getId());

        $ids = array();
        $total = count($ranking_list );
        $partial25  = round(25 / $total * 100);

        $i = 1; 

        $ids = array(
            'top' => array(
                'Android'=>array(),
                'IOS'=>array()
            ),
            'middle' => array(
                'Android'=>array(),
                'IOS'=>array()
            ),
            'bottom' => array(
                'Android'=>array(),
                'IOS'=>array()
            )
        );

        foreach($ranking_list as $r)
        {
            if($i <= $partial25)
                $ids['top'][$r['os']][]= $r['notificationId'];
            else if( $i <= $partial25*2 )
                $ids['middle'][$r['os']][]= $r['notificationId'];
            else
                $ids['bottom'][$r['os']][]= $r['notificationId'];
            $i++;
        }

        foreach( $ids as $key => $recipients )
        {

            if(count($recipients))
            {
                if($key == 'top') {
                    $title = "Estás en la parte ALTA de la tabla";
                    $message = $top[rand(0,8)];
                } else if ($key == 'middle') {
                    $title = "Estás en la parte MEDIA de la tabla";
                    $message = $middle[rand(0,7)];
                } else {
                    $title = "Estás en la parte BAJA de la tabla";
                    $message = $bottom[rand(0,9)];
                }
                
                $message.= " Compartelo con tus amigos y aumenta tu puntuación";

                $this->send($title,$message,$recipients);
            }
        }

        die();

    }


    /**
     * download
     *
     * @Route("/download", name="api_download")
     * @Template()
     */
    public function downlodAction() {
        //Detect special conditions devices
        $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
        $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

        //do something with this information
        if( $iPod || $iPhone || $iPad )
            $link = "link to ios";
        else
            $link = "link to android";

        die($link);
    }

    private function send($title,$message,$recipients) { 
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            "data"=>array(
                'title'=>$title,
                'message'=>$message,
            ),
            "registration_ids"=>$recipients['Android']
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
        //sender id = 1090006415155;
        //api key = AIzaSyCFpBmNym9kaRPoUA-ZKogSk-QZzvLhlfc

        $fields = array(
            'aps' => array(
                'alert' => $message,
                'title' => $title,
                'sound' => 'bingbong.aiff'
            )
        );

        $payload = json_encode($fields);

        $passphrase = 'iY88bR62';

        foreach( $recipients['IOS'] as $deviceToken ) {
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $this->container->getParameter('kernel.root_dir').'/../mobile/certs/aps_production.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
            $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx
            );
     
            if (!$fp) {
                echo "Error de conexión with apple";
            }

            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            $result = fwrite($fp, $msg, strlen($msg));
            fclose($fp);
        }
        
        return true;
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
                    $user->setOs($data['os']);
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
            {
                $user->setNotificationId($data['notificationId']);
                $user->setOs($data['os']);
            }

        	$em->persist($user);
        	$em->flush();

        	return true;
    	}
    	return false;
    }

    private function checkInConcert($userLatitude,$userLongitude,$feastLatitude,$feastLongitude,$feastDateFrom,$feastDateTo ) {
        return true;
        $now = date('Y-m-d');
        if($now >= $feastDateFrom->format('Y-m-d') && $now <= $feastDateTo->format('Y-m-d'))
        {
            $distance = pow($userLatitude - $feastLatitude, 2) + pow($userLongitude - $feastLongitude,2);
            $theta = $userLongitude - $feastLongitude;
            $dist = sin(deg2rad($userLatitude)) * sin(deg2rad($feastLatitude)) +  cos(deg2rad($userLatitude)) * cos(deg2rad($feastLatitude)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $km = $miles * 1.609344;
            if($km <= 5)
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