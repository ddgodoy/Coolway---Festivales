<?php

namespace CoolwayFestivales\SafetyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CoolwayFestivales\SafetyBundle\Entity\User;
use CoolwayFestivales\SafetyBundle\Form\UserType;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller {

    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_user")
     * @Template()
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('SafetyBundle:User')->findAll();
        return $this->render('SafetyBundle:User:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all User entities.
     *
     * @Route("/list", name="admin_user_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('SafetyBundle:User:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("SafetyBundle:User", "e")
                ->orderBy("e.id", "desc");

        $datatable = $this->get('datatable')
                ->setFields(
                        array('Username' => 'e.username', 'Email' => 'e.email',
                            "_identifier_" => 'e.id',
                        )
                )
                ->setHasAction(false)
                // ->setAcl(array("VIEW")) //OWNER,OPERATOR,VIEW
                ->setSearch(TRUE);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);
        return $datatable;
    }

    /**
     * @Route("/admin_user_grid", name="admin_user_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_user")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('SafetyBundle:User:index.html.twig');
    }

    private function setEncodePassword($entity) {

    }

    /**
     * Crea una nueva User
     *
     * @Route("/create", name="admin_user_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($request);

        $result = array();

        $em = $this->getDoctrine()->getManager();

        try {
            $em->persist($entity);
            $em->flush();

            //Integración con las ACLs
            $user = $this->get('security.context')->getToken()->getUser();
            $provider = $this->get('ACI.acl_manager');
            $provider->addPermission($entity, $user, \Symfony\Component\Security\Acl\Permission\MaskBuilder::MASK_OWNER, "object");
            //-----------------------------

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
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/show", name="admin_user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SafetyBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/edit", name="admin_user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('SafetyBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
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
     * @Route("/{id}", name="admin_user_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SafetyBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }


        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $result['success'] = true;
            $result['message'] = 'Transacci&oacute;n realizada satisfactoriamente.';
        }

        echo json_encode($result);
        die;
    }

    /**
     * Elimina a petición activity entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_user_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);

        $ids = explode(",", $ids);


        $em = $this->getDoctrine()->getManager();

        $repo_user = $this->getDoctrine()->getRepository('SafetyBundle:User');

        foreach ($ids as $id) {
            $entity = $repo_user->find($id);
            try {

                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este elemento");
                $result = json_encode($response);
                return new \Symfony\Component\HttpFoundation\Response($result);
            }
        }

        try {
            $em->flush();
            $response = array("success" => true, "message" => "Transacción realizada satisfactoriamente.");
        } catch (\Exception $e) {
            $response = array("success" => false, "message" => "No puede completar esta petición Error code: " . $e->getCode() . " Detalles:" . $e->getMessage());
        }

        $result = json_encode($response);
        return new \Symfony\Component\HttpFoundation\Response($result);
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="admin_user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SafetyBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_user'));
    }

    /**
     * Creates a form to delete a User entity by id.
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
     * cmbo
     *
     * @Route("/user/combo", name="admin_user_select")
     * @Template()
     */
    public function selectAction() {
        $repo = $this->getDoctrine()->getRepository("SafetyBundle:User");
        $repo->setContainer($this->container);
        $users = $repo->getList();
        return array("users" => $users);
    }

}
