<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use CoolwayFestivales\BackendBundle\Entity\NotificationStats;
use CoolwayFestivales\BackendBundle\Util\Date;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\NotificationType;
use CoolwayFestivales\SafetyBundle\Entity\User;
use CoolwayFestivales\BackendBundle\Entity\NotificationSchedule;

/**
 * Notification controller.
 *
 * @Route("/admin/notification")
 */
class NotificationController extends Controller
{
    /**
     * Lists all Terms entities.
     *
     * @Route("/", name="admin_notification")
     * @Template()
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN')) {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Notification')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:Notification')->findInFestival($user->getFeast()->getId());
        }
        return $this->render('BackendBundle:Notification:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Terms entities.
     *
     * @Route("/list", name="admin_notification_list")
     * @Template()
     */
    public function listAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Notification:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Notification", "entity")
            ->orderBy("entity.id", "desc");
        $datatable = $this->get('datatable')
            ->setFields(
                array(
                    'Nombre' => 'entity.name',
                    "_identifier_" => 'entity.id')
            )
            ->setHasAction(false)
//                ->setAcl(array("OWNER")) //OWNER,OPERATOR,VIEW
            ->setSearch(TRUE);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);
        return $datatable;
    }

    /**
     * @Route("/admin_notification_grid", name="admin_notification_grid")
     * @Template()
     */
    public function gridAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_notification")
     * @Template()
     */
    public function datatableAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Notification:index.html.twig');
    }

    /**
     * Crea una nueva stage
     *
     * @Route("/create", name="admin_notification_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $dfHora = new \DateTime('00:00');
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Notification();
        $result = array();

        $form = $this->createForm(new NotificationType($filtro, 'crear', $dfHora), $entity);
        $form->bind($request);

        $fechaHora = $form->get('date')->getData()->format('Y-m-d') . ' ' . $form->get('time')->getData()->format('H:i') . ':00';

        try {
            $em->persist($entity);
            $em->flush();
            /*
              Integración con las ACLs
              $user = $this->get('security.context')->getToken()->getUser();
              $provider = $this->get('Apptibase.acl_manager');
              $provider->addPermission($entity, $user, MaskBuilder::MASK_OWNER, "object");
             */
            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
            //
            $entity->setDate(new \DateTime($fechaHora));
            $em->persist($entity);
            $em->flush();

            // $em->getRepository('BackendBundle:Notification')->sendToMobile($entity);
        } catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }
        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Stage entity.
     *
     * @Route("/new", name="admin_notification_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $dfHora = new \DateTime('00:00');
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Notification();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\NotificationType($filtro, 'crear', $dfHora), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a stage entity.
     *
     * @Route("/show", name="admin_notification_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Notification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Stage entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing stage entity.
     *
     * @Route("/edit", name="admin_notification_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Notification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find stage entity.');
        }

        $editForm = $this->createForm(new NotificationType($filtro, 'editar', $entity->getDate()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_notification_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Notification')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Stage entity.');
        }
        $editForm = $this->createForm(new NotificationType($filtro, 'editar', $entity->getDate()), $entity);
        $editForm->bind($request);

        $fechaHora = $editForm->get('date')->getData()->format('Y-m-d') . ' ' . $editForm->get('time')->getData()->format('H:i') . ':00';

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();

                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
                //
                $entity->setDate(new \DateTime($fechaHora));
                $em->persist($entity);
                $em->flush();
            } catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $result['success'] = false;
        }
        echo json_encode($result);
        die;
    }

    /**
     * Deletes a Terms entity.
     *
     * @Route("/{id}", name="admin_notification_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Notification')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Stage entity.');
            }

            $noticationStats = $em->getRepository('BackendBundle:NotificationStats')->findBy(array('notification' => $id));
            if (count($noticationStats) > 0) {
                foreach ($noticationStats as $stat) {
                    $em->remove($stat);
                }
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_notification'));
    }

    /**
     * Creates a form to delete a Stage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * Elimina Notification entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_notification_batchdelete")
     * @Template()
     */
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_notification = $this->getDoctrine()->getRepository('BackendBundle:Notification');

        foreach ($ids as $id) {
            $entity = $repo_notification->find($id);
            $noticationStats = $em->getRepository('BackendBundle:NotificationStats')->findBy(array('notification' => $id));
            if (count($noticationStats) > 0) {
                foreach ($noticationStats as $stat) {
                    $em->remove($stat);
                }
            }
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar esta notificación");
                $result = json_encode($response);
                return new \Symfony\Component\HttpFoundation\Response($result);
            }
        }

        try {
            $em->flush();
            $response = array("success" => true, "message" => "Transacci&oacute;n realizada satisfactoriamente.");
        } catch (\Exception $e) {
            $response = array("success" => false, "message" => "No puede completar esta petición Error code: " . $e->getCode() . " Detalles:" . $e->getMessage());
        }

        $result = json_encode($response);
        return new \Symfony\Component\HttpFoundation\Response($result);
    }

    /**
     * @Route("/toSend", name="admin_notification_tosend")
     * @Method("GET")
     * @Template()
     */
    public function notificationToSendAction(Request $request)
    {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Notification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        return array('entity' => $entity);
    }

    /**
     * @Route("/runSend", name="admin_notification_runsend")
     * @Method("POST")
     */
    public function notificationRunSendAction(Request $request)
    {
        set_time_limit (0);
        ini_set('memory_limit','2G');
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository('BackendBundle:Notification')->findOneById($id);

        if ($notification) {

            $devices = $em->getRepository('SafetyBundle:Device')->findBy(array('feast' => $notification->getFeast()->getId()));
            $androidTokens = array();
            $iosTokens = array();

            foreach ($devices as $device) {
                if ($device->getOs() == 1)
                    $iosTokens[] = $device->getToken();
                else
                    $androidTokens[] = $device->getToken();
            }

            $gcmStats = array();
            $apnStats = array();
            $gcmStats["total"] = 0;
            $gcmStats["successful"] = 0;
            $gcmStats["failed"] = 0;
            $apnStats["total"] = 0;
            $apnStats["successful"] = 0;
            $apnStats["failed"] = 0;

            $gcmAppId = $notification->getFeast()->getGcmAppId();
            $apnAppId = $notification->getFeast()->getApnAppId();


            if (sizeof($androidTokens) > 0 && isset($gcmAppId)) {
                $gcm = $this->get('coolway_app.gcm');
                $gcmStats = $gcm->sendNotification($androidTokens,
                    $notification->getName(),
                    $notification->getText(),
                    'admin-notification',
                    $notification->getFeast()->getGcmAppId(),
                    false,
                    600,
                    false,
                    $notification->getFeast());
            }

            if (sizeof($iosTokens) > 0 && isset($apnAppId)) {
//                $apn = $this->get('coolway_app.apn');
//                $apnStats = $apn->sendNotification($iosTokens,
//                    $notification->getText(),
//                    5,
//                    $notification->getFeast()->getApnAppId(),
//                    'bingbong.aiff',
//                    $notification->getFeast());
                //$apnStats = ["total" => count($tokens), "successful" => 0, "failed" => 0];;
                $apnStats = $this->scheduledNotifications($iosTokens, $notification->getId());
            }

            if (count($apnStats) > 0 ||
                count($gcmStats) > 0
            ) {
                $stats = new NotificationStats();
                $stats->setNotification($notification);
                $stats->setTotalDevices($gcmStats["total"] + $apnStats["total"]);
                $stats->setTotalAndroid($gcmStats["total"]);
                $stats->setSuccessfulAndroid($gcmStats["successful"]);
                $stats->setFailedAndroid($gcmStats["failed"]);
                $stats->setTotalIOS($apnStats["total"]);
                $stats->setSuccessfulIOS($apnStats["successful"]);
                $stats->setFailedIOS($apnStats["failed"]);
                $stats->setSent(new \DateTime("now"));
                $em->persist($stats);
                $notification->setDelivery(true);
            } else
                $notification->setDelivery(false);

            $notification->setSend(true);
            $em->persist($notification);
            $em->flush();
        }
        return new Response('ok');
    }

    /**
     * @Route("/send/all/", name="notification_send_all")
     * @Method("GET")
     */
    public function notificationSendAllAction()
    {
        set_time_limit (0);
        ini_set('memory_limit','2G');
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('BackendBundle:Notification')->findForBatch();

        foreach ($notifications as $notification) {
            if ($notification) {

                $devices = $em->getRepository('SafetyBundle:Device')->findBy(array('feast' => $notification->getFeast()->getId()));
                $androidTokens = array();
                $iosTokens = array();

                foreach ($devices as $device) {
                    if ($device->getOs() == 1)
                        $iosTokens[] = $device->getToken();
                    else
                        $androidTokens[] = $device->getToken();
                }

                $gcmStats = array();
                $apnStats = array();
                $gcmStats["total"] = 0;
                $gcmStats["successful"] = 0;
                $gcmStats["failed"] = 0;
                $apnStats["total"] = 0;
                $apnStats["successful"] = 0;
                $apnStats["failed"] = 0;

                $gcmAppId = $notification->getFeast()->getGcmAppId();
                $apnAppId = $notification->getFeast()->getApnAppId();
                if (sizeof($androidTokens) > 0 && isset($gcmAppId)) {
                    $gcm = $this->get('coolway_app.gcm');
                    $gcmStats = $gcm->sendNotification($androidTokens,
                        $notification->getName(),
                        $notification->getText(),
                        'admin-notification',
                        $notification->getFeast()->getGcmAppId(),
                        false,
                        600,
                        false,
                        $notification->getFeast());
                }

                if (sizeof($iosTokens) > 0 && isset($apnAppId)) {
//                    $apn = $this->get('coolway_app.apn');
//                    $apnStats = $apn->sendNotification($iosTokens,
//                        $notification->getText(),
//                        5,
//                        $notification->getFeast()->getApnAppId(),
//                        'bingbong.aiff',
//                        $notification->getFeast());
                    $apnStats = $this->scheduledNotifications($iosTokens, $notification->getId());
                }

                if (count($apnStats) > 0 || count($gcmStats) > 0) {
                    $stats = new NotificationStats();
                    $stats->setNotification($notification);
                    $stats->setTotalDevices($gcmStats["total"] + $apnStats["total"]);
                    $stats->setTotalAndroid($gcmStats["total"]);
                    $stats->setSuccessfulAndroid($gcmStats["successful"]);
                    $stats->setFailedAndroid($gcmStats["failed"]);
                    $stats->setTotalIOS($apnStats["total"]);
                    $stats->setSuccessfulIOS($apnStats["successful"]);
                    $stats->setFailedIOS($apnStats["failed"]);
                    $stats->setSent(new \DateTime("now"));
                    $em->persist($stats);
                    $notification->setDelivery(true);
                } else
                    $notification->setDelivery(false);

                $notification->setSend(true);
                $em->persist($notification);
                $em->flush();
            }
        }


        return new Response('true');
    }


    /**
     * @Route("/send/artist-favorite/", name="notification_send_artist_favorite")
     * @Method("GET")
     */
    public function notificationSendArtistFavoriteAction()
    {
        set_time_limit (0);
        ini_set('memory_limit','2G');
        $em = $this->getDoctrine()->getManager();
        $upcomingArtists = $em->getRepository('BackendBundle:FeastStageArtist')->getUpcomingArtists();

        $cont = 0;
        foreach ($upcomingArtists as $upcoming) {
            if ($upcoming) {

                $devices = $em->getRepository('BackendBundle:ArtistFavorites')->getDeviceByArtistFavorite($upcoming->getArtist()->getId(), $upcoming->getFeastStage()->getFeast()->getId());
                $androidTokens = array();
                $iosTokens = array();

                foreach ($devices as $device) {
                    if ($device->getOs() == 1)
                        $iosTokens[] = $device->getToken();
                    else
                        $androidTokens[] = $device->getToken();
                }

                $gcmStats = array();
                $apnStats = array();
                $gcmStats["total"] = 0;
                $gcmStats["successful"] = 0;
                $gcmStats["failed"] = 0;
                $apnStats["total"] = 0;
                $apnStats["successful"] = 0;
                $apnStats["failed"] = 0;

                $artistName = $upcoming->getArtist()->getName();

                $title = 'Va a empezar el concierto!!!';
                $description = 'El concierto de ' . $artistName . ' está a punto de comenzar!!!';

                $gcmAppId = $upcoming->getFeastStage()->getFeast()->getGcmAppId();
                $apnAppId = $upcoming->getFeastStage()->getFeast()->getApnAppId();
                if (sizeof($androidTokens) > 0 && isset($gcmAppId)) {
                    $gcm = $this->get('coolway_app.gcm');
                    $gcm->sendNotification($androidTokens,
                        $title,
                        $description,
                        'artist-notification-' . $cont,
                        $upcoming->getFeastStage()->getFeast()->getGcmAppId(),
                        false,
                        600,
                        false,
                        $upcoming->getFeastStage()->getFeast());
                }

                if (sizeof($iosTokens) > 0 && isset($apnAppId)) {
//                    $apn = $this->get('coolway_app.apn');
//                    $apn->sendNotification($iosTokens,
//                        $description,
//                        5,
//                        $upcoming->getFeastStage()->getFeast()->getApnAppId(),
//                        'bingbong.aiff',
//                        $upcoming->getFeastStage()->getFeast());
                    $this->scheduledNotifications($iosTokens, -1, $description);

                }

            }

            $cont++;
        }


        return new Response('true');
    }

    /**
     * @Route("/ios/test", name="notification_ios_test")
     * @Method("GET")
     */
    public function iOSTestAction()
    {
        set_time_limit (0);
        ini_set('memory_limit','2G');

        echo "<pre>";
        print_r($_SERVER);
        echo "</pre>";
        

        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('BackendBundle:Notification')->findBy(
                array(),
                array('id' => 'DESC'),
                1
            );
        foreach ($notifications as $notification) {
            if ($notification) {

                $devices = $em->getRepository('SafetyBundle:Device')->findBy(
                    array('feast' => $notification->getFeast()->getId(), 'os' => 1),
                    array(),
                    25
                );
                $iosTokens = array();

                foreach ($devices as $device) {
                        $iosTokens[] = $device->getToken();
                }
                $apnAppId = $notification->getFeast()->getApnAppId();
                if (sizeof($iosTokens) > 0 /* && isset($apnAppId)*/) {
//                    $apn = $this->get('coolway_app.apn');
//                    $apn->sendNotification($iosTokens,
//                        $notification->getText(),
//                        5,
//                        $notification->getFeast()->getApnAppId(),
//                        'bingbong.aiff',
//                        $notification->getFeast());

                    $this->scheduledNotifications($iosTokens, $notification->getId());
                }
            }
        }


        return new Response('true');
    }

    /**
     * @param $iosTokens
     * @param $notificationId
     * @param null $name
     * @param null $text
     * Guarda las notificaciones para IOS en la nueva tabla, si $notificationId = -1 es una notificación de artista y no trae $name y $text
     */
    private function scheduledNotifications($iosTokens, $notificationId, $text = null)
    {
        $em = $this->getDoctrine()->getManager();
        $stats = ["total" => count($iosTokens), "successful" => 0, "failed" => 0];
        
        foreach ($iosTokens as $token) {
            $notificationSchedule = new NotificationSchedule();
            $notificationSchedule->setToken($token);
            $notificationSchedule->setNotificationId($notificationId);
            $notificationSchedule->setSendDate(new \DateTime());
            $notificationSchedule->setStatus(0);
            if ($notificationId == -1) {
                $notificationSchedule->setText($text);
            }
            $em->persist($notificationSchedule);
            
            $stats["successful"] += 1;   
        }
        $em->flush();
        
        return $stats;
    }

} // end class