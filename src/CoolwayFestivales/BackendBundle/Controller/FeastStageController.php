<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Proxies\__CG__\CoolwayFestivales\BackendBundle\Entity\Stage;
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
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->findInFestival($user->getFeast()->getId());
        }
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
    public function createAction(Request $request)
    {
        $postData  = $request->request->get('coolwayfestivales_backendbundle_feaststage');
        $feast_id  = $postData["feast"];
        $escenario = $postData["stage"];

        if (!empty($feast_id) && !empty($escenario))
        {
            $this->getDoctrine()->getRepository('BackendBundle:FeastStage')->addStageOnTheFly($feast_id, $escenario);

            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
            //
            $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($feast_id);
        } else {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => 'los valores no pueden ser nulos');
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
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $entity = new \CoolwayFestivales\BackendBundle\Entity\FeastStage();
        $form = $this->createForm(new FeastStageType($filtro, 'crear'), $entity);

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
    public function editAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find feaststage entity.');
        }
        $editForm = $this->createForm(new FeastStageType($filtro, 'editar'), $entity);
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
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:FeastStage')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FeastStage entity.');
        }
        $editForm = $this->createForm(new FeastStageType($filtro, 'editar'), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();
                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';

                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($entity->getFeast()->getId());
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
    public function deleteAction(Request $request, $id)
    {
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
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);
        $em  = $this->getDoctrine()->getManager();

        $repo_feaststage = $this->getDoctrine()->getRepository('BackendBundle:FeastStage');

        foreach ($ids as $id)
        {
            $entity = $repo_feaststage->find($id);
            $feastI = $entity->getFeast()->getId();
            try {
                $em->remove($entity);
                //
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($feastI);
            }
            catch (\Exception $e) {
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

} // end class