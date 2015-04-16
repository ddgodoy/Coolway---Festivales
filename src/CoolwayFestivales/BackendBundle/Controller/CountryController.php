<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\CountryType;

/**
 * Country controller.
 *
 * @Route("/admin/country")
 */
class CountryController extends Controller {

    /**
     * Lists all Country entities.
     *
     * @Route("/", name="admin_country")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Country')->findAll();
        return $this->render('BackendBundle:Country:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Country entities.
     *
     * @Route("/list", name="admin_country_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Country:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Country", "entity")
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
     * @Route("/admin_country_grid", name="admin_country_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_country")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Country:index.html.twig');
    }

    /**
     * Crea una nueva country
     *
     * @Route("/create", name="admin_country_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Country();
        $form = $this->createForm(new CountryType(), $entity);
        $form->bind($request);
        $result = array();

        $errores = $this->get('admin.util')->getErrorList($entity);
        if (count($errores) == 0) {
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
        } else {
            $result['success'] = false;
            $result['errores'] = $errores;
        }
        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Country entity.
     *
     * @Route("/new", name="admin_country_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Country();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\CountryType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a country entity.
     *
     * @Route("/show", name="admin_country_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing country entity.
     *
     * @Route("/edit", name="admin_country_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find country entity.');
        }

        $editForm = $this->createForm(new CountryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Country entity.
     *
     * @Route("/update/{id}", name="admin_country_update")
     * @Method("post")
     * @Template("BackendBundle:Country:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Country')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }
        $editForm = $this->createForm(new CountryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();
                $result['success'] = true;
                $result['mensaje'] = 'Editado correctamente';
            } catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $errores = $this->get('admin.util')->getErrorList($entity);
            $result['success'] = false;
            $result['errores'] = $errores;
        }
        echo json_encode($result);
        die;
    }

    /**
     * Deletes a Country entity.
     *
     * @Route("/{id}", name="admin_country_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Country')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Country entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_country'));
    }

    /**
     * Creates a form to delete a Country entity by id.
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
     * Elimina a petición country entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_country_bachdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);

        $em = $this->getDoctrine()->getManager();

        $repo_country = $this->getDoctrine()->getRepository('BackendBundle:Country');

        foreach ($ids as $id) {
            $entity = $repo_country->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este countryo");
                $result = json_encode($response);
                return new \Symfony\Component\HttpFoundation\Response($result);
            }
        }

        try {
            $em->flush();
            $response = array("success" => true, "message" => "Eliminados correctamente");
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
