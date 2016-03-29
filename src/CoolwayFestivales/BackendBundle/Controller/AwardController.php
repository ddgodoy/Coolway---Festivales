<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\AwardType;

/**
 * Award controller.
 *
 * @Route("/admin/award")
 */
class AwardController extends Controller
{
    /**
     * Lists all Award entities.
     *
     * @Route("/", name="admin_award")
     * @Template()
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Award')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:Award')->findInFestival($user->getFeast()->getId());
        }
        return $this->render('BackendBundle:Award:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Award entities.
     *
     * @Route("/list", name="admin_award_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Award:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Award", "entity")
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
     * @Route("/admin_award_grid", name="admin_award_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_award")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Award:index.html.twig');
    }

    /**
     * Crea una nueva award
     *
     * @Route("/create", name="admin_award_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Award();
        $form = $this->createForm(new AwardType($filtro), $entity);
        $form->bind($request);
        $result = array();


        $em = $this->getDoctrine()->getManager();
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
        } catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }

        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Award entity.
     *
     * @Route("/new", name="admin_award_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Award();
        $form = $this->createForm(new AwardType($filtro), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a award entity.
     *
     * @Route("/show", name="admin_award_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Award')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Award entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing award entity.
     *
     * @Route("/edit", name="admin_award_edit")
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

        $entity = $em->getRepository('BackendBundle:Award')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find award entity.');
        }

        $editForm = $this->createForm(new AwardType($filtro), $entity);
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
     * @Route("/{id}", name="admin_award_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Award')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Award entity.');
        }
        $editForm = $this->createForm(new AwardType($filtro), $entity);
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
     * Deletes a Award entity.
     *
     * @Route("/{id}", name="admin_award_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Award')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Award entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_award'));
    }

    /**
     * Creates a form to delete a Award entity by id.
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
     * Elimina a petición award entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_award_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_award = $this->getDoctrine()->getRepository('BackendBundle:Award');

        foreach ($ids as $id) {
            $entity = $repo_award->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este awardo");
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