<?php

namespace CoolwayFestivales\SafetyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CoolwayFestivales\SafetyBundle\Entity\Role;
use CoolwayFestivales\SafetyBundle\Form\RoleType;

/**
 * Role controller.
 *
 * @Route("/role")
 */
class RoleController extends Controller {

    /**
     * Lists all Role entities.
     *
     * @Route("/", name="admin_role")
     * @Template()
     */
    public function indexAction() {
        $this->_datatable();
        return $this->render('SafetyBundle:Role:index.html.twig');
    }

    /**
     * Lists all Role entities.
     *
     * @Route("/list", name="admin_role_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('SafetyBundle:Role:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("SafetyBundle:Role", "e")
                ->orderBy("e.id", "desc");

        $datatable = $this->get('datatable')
                ->setFields(
                        array(
                            'name' => 'e.name', 'description' => 'e.description',
                            "_identifier_" => 'e.id')
                )->setHasAction(FALSE)
                //  ->setAcl(array("EDIT")) //OWNER,OPERATOR,VIEW
                ->setSearch(TRUE);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);
        return $datatable;
    }

    /**
     * @Route("/admin_role_grid", name="admin_role_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_role")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('SafetyBundle:Role:index.html.twig');
    }

  
     /**
     * Crea una nueva Role
     *
     * @Route("/create", name="admin_role_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new Role();
        $form = $this->createForm(new RoleType(), $entity);
        $form->bind($request);
        $result = array();
        
        $errores = $this->get('admin.util')->getErrorList($entity);
        if (count($errores) == 0) {
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
        } else {
            $result['success'] = false;
            $result['errores'] = $errores;
        }
        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Role entity.
     *
     * @Route("/new", name="admin_role_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Role();
        $form   = $this->createForm(new RoleType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/show", name="admin_role_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SafetyBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/edit", name="admin_role_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");
        
        $entity = $em->getRepository('SafetyBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        $editForm = $this->createForm(new RoleType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Edits an existing Role entity.
     *
     * @Route("/{id}", name="admin_role_update")
     * @Method("POST")
     * @Template("SafetyBundle:Role:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SafetyBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }

        
        $editForm = $this->createForm(new RoleType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $result['success'] = true;
            $result['mensaje'] = 'Editado correctamente';
        }
        
        echo json_encode($result); die;

        
    }
    
    
     /**
     * Elimina a petición activity entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_role_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);

        $em = $this->getDoctrine()->getManager();

        $repo_role = $this->getDoctrine()->getRepository('SafetyBundle:Role');

        foreach ($ids as $id) {
            $entity = $repo_role->find($id);
            try{
                $em->remove($entity);
            }catch (\Exception $e){
                $response = array("success"=>false,"message"=>"no se puede eliminar este elemento");
                $result = json_encode($response);
                return new \Symfony\Component\HttpFoundation\Response($result);
            }
        }
        
        try {
             $em->flush();
             $response = array("success"=>true,"message"=>"Eliminados correctamente");
        }  catch (\Exception $e){
            $response = array("success"=>false,"message"=>"No puede completar esta petición Error code: ".$e->getCode()." Detalles:".$e->getMessage());
        }
        
        $result = json_encode($response);
        return new \Symfony\Component\HttpFoundation\Response($result);
    }

    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}/delete", name="admin_role_delete")
     * @Method("post")
     */
    public function deleteAction($id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SafetyBundle:Role')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Role entity.');
        }
        $em->remove($entity);
        $em->flush();


        return new \Symfony\Component\HttpFoundation\Response("Eliminado correctamente");
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * cmbo
     *
     * @Route("/combo", name="admin_role_select")
     * @Template()
     */
    public function selectAction() {
        $repo = $this->getDoctrine()->getRepository("SafetyBundle:Role");
        $roles = $repo->findAll();
        return array("roles" => $roles);
    }

}
