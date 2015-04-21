<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\FeastStageType;

/**
 * FeastStage controller.
 *
 * @Route("/admin/feaststage")
 */
class FeastStageController extends Controller {

    /**
     * Lists all FeastStage entities.
     *
     * @Route("/", name="admin_feaststage")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->findAll();
        return $this->render('BackendBundle:FeastStage:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all FeastStage entities.
     *
     * @Route("/list", name="admin_feaststage_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:FeastStage:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:FeastStage", "entity")
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
     * @Route("/admin_feaststage_grid", name="admin_feaststage_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_feaststage")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:FeastStage:index.html.twig');
    }

    /**
     * Crea una nueva feaststage
     *
     * @Route("/create", name="admin_feaststage_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\FeastStage();
        $form = $this->createForm(new FeastStageType(), $entity);
        $form->bind($request);
        $result = array();


        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($entity);
            $em->flush();

            /*
              //Integración con las ACLs
              $user = $this->get('security.context')->getToken()->getUser();
              $provider = $this->get('Apptibase.acl_manager');
              $provider->addPermission($entity, $user, MaskBuilder::MASK_OWNER, "object");
              //-----------------------------
             */

            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
        } catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }

        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new FeastStage entity.
     *
     * @Route("/new", name="admin_feaststage_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\FeastStage();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\FeastStageType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a feaststage entity.
     *
     * @Route("/show", name="admin_feaststage_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeastStage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing feaststage entity.
     *
     * @Route("/edit", name="admin_feaststage_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find feaststage entity.');
        }

        $editForm = $this->createForm(new FeastStageType(), $entity);
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
     * @Route("/{id}", name="admin_feaststage_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeastStage entity.');
        }
        $editForm = $this->createForm(new FeastStageType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();
                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
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
     * Deletes a FeastStage entity.
     *
     * @Route("/{id}", name="admin_feaststage_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FeastStage entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_feaststage'));
    }

    /**
     * Creates a form to delete a FeastStage entity by id.
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
     * Elimina a petición feaststage entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_feaststage_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_feaststage = $this->getDoctrine()->getRepository('BackendBundle:FeastStage');

        foreach ($ids as $id) {
            $entity = $repo_feaststage->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este feaststageo");
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

    /*
     * ==================================== Funciones específicas ==================
     */



    /*
     * =============================================================================
     */
}
