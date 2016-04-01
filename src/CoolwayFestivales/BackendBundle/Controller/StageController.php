<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Proxies\__CG__\CoolwayFestivales\BackendBundle\Entity\FeastStage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\StageType;

/**
 * Stage controller.
 *
 * @Route("/admin/stage")
 */
class StageController extends Controller {

    /**
     * Lists all Stage entities.
     *
     * @Route("/", name="admin_stage")
     * @Template()
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Stage')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:Stage')->findInFestival($user->getFeast()->getId());
        }
        return $this->render('BackendBundle:Stage:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Stage entities.
     *
     * @Route("/list", name="admin_stage_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Stage:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Stage", "entity")
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
     * @Route("/admin_stage_grid", name="admin_stage_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_stage")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Stage:index.html.twig');
    }

    /**
     * Crea una nueva stage
     *
     * @Route("/create", name="admin_stage_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Stage();
        $form = $this->createForm(new StageType(), $entity);
        $form->bind($request);
        $result = array();

        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($entity);
            $em->flush();

            if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            {
                $this->getDoctrine()->getRepository('BackendBundle:Stage')->addStageToFestival(
                    $entity->getId(), $this->get('security.context')->getToken()->getUser()->getFeast()->getId()
                );
            }
            /*
              Integración con las ACLs
              $user = $this->get('security.context')->getToken()->getUser();
              $provider = $this->get('Apptibase.acl_manager');
              $provider->addPermission($entity, $user, MaskBuilder::MASK_OWNER, "object");
             */
            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
            //
            $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateForAllFestivals();
        }
        catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }

        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Stage entity.
     *
     * @Route("/new", name="admin_stage_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Stage();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\StageType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a stage entity.
     *
     * @Route("/show", name="admin_stage_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Stage')->find($id);

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
     * @Route("/edit", name="admin_stage_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Stage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find stage entity.');
        }

        $editForm = $this->createForm(new StageType(), $entity);
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
     * @Route("/{id}", name="admin_stage_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Stage')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Stage entity.');
        }
        $editForm = $this->createForm(new StageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();
                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
                //
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateForAllFestivals();
            }
            catch (\Exception $exc) {
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
     * Deletes a Stage entity.
     *
     * @Route("/{id}", name="admin_stage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid())
        {
            if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
            {
                $this->getDoctrine()->getRepository('BackendBundle:Stage')->delStageFromFestival(
                    $id, $this->get('security.context')->getToken()->getUser()->getFeast()->getId()
                );
            }
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Stage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Stage entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_stage'));
    }

    /**
     * Creates a form to delete a Stage entity by id.
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
     * Elimina a petición stage entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_stage_batchdelete")
     * @Template()
     */
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_stage = $this->getDoctrine()->getRepository('BackendBundle:Stage');

        foreach ($ids as $id)
        {
            $entity = $repo_stage->find($id);
            try {
                $em->remove($entity);
                //
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateForAllFestivals();
            }
            catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este stageo");
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

} // end class