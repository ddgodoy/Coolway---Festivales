<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\UserFeastDataType;

/**
 * UserFeastData controller.
 *
 * @Route("/admin/userfeastdata")
 */
class UserFeastDataController extends Controller {

    /**
     * Lists all UserFeastData entities.
     *
     * @Route("/", name="admin_userfeastdata")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData')->findAll();
        return $this->render('BackendBundle:UserFeastData:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all UserFeastData entities.
     *
     * @Route("/list", name="admin_userfeastdata_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:UserFeastData:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:UserFeastData", "entity")
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
     * @Route("/admin_userfeastdata_grid", name="admin_userfeastdata_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_userfeastdata")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:UserFeastData:index.html.twig');
    }

    /**
     * Crea una nueva userfeastdata
     *
     * @Route("/create", name="admin_userfeastdata_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\UserFeastData();
        $form = $this->createForm(new UserFeastDataType(), $entity);
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
     * Displays a form to create a new UserFeastData entity.
     *
     * @Route("/new", name="admin_userfeastdata_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\UserFeastData();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\UserFeastDataType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a userfeastdata entity.
     *
     * @Route("/show", name="admin_userfeastdata_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:UserFeastData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserFeastData entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing userfeastdata entity.
     *
     * @Route("/edit", name="admin_userfeastdata_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:UserFeastData')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find userfeastdata entity.');
        }

        $editForm = $this->createForm(new UserFeastDataType(), $entity);
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
     * @Route("/{id}", name="admin_userfeastdata_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:UserFeastData')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserFeastData entity.');
        }
        $editForm = $this->createForm(new UserFeastDataType(), $entity);
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
     * Deletes a UserFeastData entity.
     *
     * @Route("/{id}", name="admin_userfeastdata_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:UserFeastData')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserFeastData entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_userfeastdata'));
    }

    /**
     * Creates a form to delete a UserFeastData entity by id.
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
     * Elimina a petición userfeastdata entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_userfeastdata_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_userfeastdata = $this->getDoctrine()->getRepository('BackendBundle:UserFeastData');

        foreach ($ids as $id) {
            $entity = $repo_userfeastdata->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este userfeastdatao");
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
