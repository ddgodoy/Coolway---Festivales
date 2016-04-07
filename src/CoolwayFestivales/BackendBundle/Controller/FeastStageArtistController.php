<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\FeastStageArtistType;

/**
 * FeastStageArtist controller.
 *
 * @Route("/admin/feaststageartist")
 */
class FeastStageArtistController extends Controller {

    /**
     * Lists all FeastStageArtist entities.
     *
     * @Route("/", name="admin_feaststageartist")
     * @Template()
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:FeastStageArtist')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:FeastStageArtist')->findInFestival($user->getFeast()->getId());
        }
        $em = $this->getDoctrine()->getManager();
        return $this->render('BackendBundle:FeastStageArtist:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all FeastStageArtist entities.
     *
     * @Route("/list", name="admin_feaststageartist_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:FeastStageArtist:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:FeastStageArtist", "entity")
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
     * @Route("/admin_feaststageartist_grid", name="admin_feaststageartist_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_feaststageartist")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:FeastStageArtist:index.html.twig');
    }

    /**
     * Crea una nueva feaststageartist
     *
     * @Route("/create", name="admin_feaststageartist_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $artistas = $this->getDoctrine()->getRepository('BackendBundle:Artist')->getArtistasIds(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $dfHora = new \DateTime('00:00');
        $entity = new \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist();
        $result = array();

        $form = $this->createForm(new FeastStageArtistType($filtro, 'crear', $artistas, $dfHora), $entity);
        $form->bind($request);

        $errors = $this->checkDateRange($form);
        $fechaHora = $form->get('date')->getData()->format('Y-m-d').' '.$form->get('time')->getData()->format('H:i').':00';

        if ($form->isValid() && empty($errors))
        {
            try {
                $em->persist($entity); $em->flush();
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
                $em->persist($entity); $em->flush();
                //
                $feast_id = $entity->getFeastStage()->getFeast()->getId();
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($feast_id);
            }
            catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $result['success'] = false;
            $result['error'] = array('cause' => 'Invalid', 'errors' => $errors);
        }
        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new FeastStageArtist entity.
     *
     * @Route("/new", name="admin_feaststageartist_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $artistas = $this->getDoctrine()->getRepository('BackendBundle:Artist')->getArtistasIds(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $dfHora = new \DateTime('00:00');
        $entity = new \CoolwayFestivales\BackendBundle\Entity\FeastStageArtist();
        $form = $this->createForm(new FeastStageArtistType($filtro, 'crear', $artistas, $dfHora), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a feaststageartist entity.
     *
     * @Route("/show", name="admin_feaststageartist_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:FeastStageArtist')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeastStageArtist entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing feaststageartist entity.
     *
     * @Route("/edit", name="admin_feaststageartist_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $artistas = $this->getDoctrine()->getRepository('BackendBundle:Artist')->getArtistasIds(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");
        $entity = $em->getRepository('BackendBundle:FeastStageArtist')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find feaststageartist entity.');
        }
        $editForm = $this->createForm(new FeastStageArtistType($filtro, 'editar', $artistas, $entity->getDate()), $entity);
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
     * @Route("/{id}", name="admin_feaststageartist_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $artistas = $this->getDoctrine()->getRepository('BackendBundle:Artist')->getArtistasIds(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:FeastStageArtist')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeastStageArtist entity.');
        }
        $editForm = $this->createForm(new FeastStageArtistType($filtro, 'editar', $artistas, $entity->getDate()), $entity);
        $editForm->bind($request);

        $errors = $this->checkDateRange($editForm);
        $fechaHora = $editForm->get('date')->getData()->format('Y-m-d').' '.$editForm->get('time')->getData()->format('H:i').':00';

        if ($editForm->isValid() && empty($errors))
        {
            try {
                $em->persist($entity); $em->flush();

                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
                //
                $entity->setDate(new \DateTime($fechaHora));
                $em->persist($entity); $em->flush();
                //
                $feast_id = $entity->getFeastStage()->getFeast()->getId();
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($feast_id);
            }
            catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $result['success'] = false;
            $result['error'] = array('cause' => 'Invalid', 'errors' => $errors);
        }
        echo json_encode($result);
        die;
    }

    /**
     * Deletes a FeastStageArtist entity.
     *
     * @Route("/{id}", name="admin_feaststageartist_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:FeastStageArtist')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FeastStageArtist entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_feaststageartist'));
    }

    /**
     * Creates a form to delete a FeastStageArtist entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * Elimina a petición feaststageartist entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_feaststageartist_batchdelete")
     * @Template()
     */
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_feaststageartist = $this->getDoctrine()->getRepository('BackendBundle:FeastStageArtist');

        foreach ($ids as $id) {
            $entity = $repo_feaststageartist->find($id);
            try {
                $em->remove($entity);
                //
                $feast_id = $entity->getFeastStage()->getFeast()->getId();
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($feast_id);
            }
            catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este feaststageartisto");
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
    //
    public function checkDateRange($form)
    {
        $errors = array();
        $obj    = $form['feast_stage']->getData();
        $oFrom  = $obj->getFeast()->getDateFrom();
        $oTo    = $obj->getFeast()->getDateTo();
        $sDate  = $form['date']->getData()->format('Y-m-d');

        if (!($sDate >= $oFrom->format('Y-m-d') && $sDate <= $oTo->format('Y-m-d'))) {
            $errors[] = array('field' => $form->getName().'_date', 'message' => 'La fecha no es correcta');
        }
        return $errors;
    }

} // end class