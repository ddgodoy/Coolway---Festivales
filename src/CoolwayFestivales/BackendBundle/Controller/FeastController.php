<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\FeastType;
use CoolwayFestivales\BackendBundle\Util\ResizeImage;

/**
 * Feast controller.
 *
 * @Route("/admin/feast")
 */
class FeastController extends Controller {

    /**
     * Lists all Feast entities.
     *
     * @Route("/", name="admin_feast")
     * @Template()
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Feast')->findAll();
        return $this->render('BackendBundle:Feast:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Feast entities.
     *
     * @Route("/list", name="admin_feast_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Feast:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Feast", "entity")
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
     * @Route("/admin_feast_grid", name="admin_feast_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_feast")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Feast:index.html.twig');
    }

    /**
     * Crea una nueva feast
     *
     * @Route("/create", name="admin_feast_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Feast();
        $result = array();

        $form = $this->createForm(new FeastType(), $entity);
        $form->bind($request);

        $errors = $this->checkDateRange($form);

        if ($form->isValid() && empty($errors))
        {
            try {
                $em->persist($entity);
                $em->flush();

                // upload images if any
                $this->handleImage($form->get('image')->getData(), $entity->getId());
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
        } else {
            $result['success'] = false;
            $result['error'] = array('cause' => 'Invalid', 'errors' => $errors);
        }
        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Feast entity.
     *
     * @Route("/new", name="admin_feast_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Feast();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\FeastType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a feast entity.
     *
     * @Route("/show", name="admin_feast_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Feast')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feast entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing feast entity.
     *
     * @Route("/edit", name="admin_feast_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Feast')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find feast entity.');
        }
        $editForm = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\FeastType(), $entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_feast_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Feast')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feast entity.');
        }
        $editForm = $this->createForm(new FeastType(), $entity);
        $editForm->bind($request);

        $errors = $this->checkDateRange($editForm);

        if ($editForm->isValid() && empty($errors))
        {
            try {
                $em->persist($entity);
                $em->flush();

                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';

                // upload images if any
                $this->handleImage($editForm->get('image')->getData(), $entity->getId());
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
     * Deletes a Feast entity.
     *
     * @Route("/{id}", name="admin_feast_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Feast')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feast entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_feast'));
    }

    /**
     * Creates a form to delete a Feast entity by id.
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
     * Elimina a petición feast entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_feast_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_feast = $this->getDoctrine()->getRepository('BackendBundle:Feast');

        foreach ($ids as $id) {
            $entity = $repo_feast->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este feasto");
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
        $errors    = array();
        $date_now  = date('Y-m-d');
        $date_from = $form['date_from']->getData()->format('Y-m-d');
        $date_to   = $form['date_to']->getData()->format('Y-m-d');

        if ($date_from < $date_now) {
            $errors[] = array('field' => $form->getName().'_date_from', 'message' => 'La fecha no es correcta');
        }
        if ($date_to <= $date_from) {
            $errors[] = array('field' => $form->getName().'_date_to', 'message' => 'La fecha no es correcta');
        }
        return $errors;
    }
    //
    public function handleImage($image, $id)
    {
        if ($image)
        {
            $oR = new ResizeImage();
            $em = $this->getDoctrine()->getManager();
            $oB = $this->getDoctrine()->getRepository('BackendBundle:Feast')->find($id);

            if ($oB)
            {
                $dFestival = $this->get('kernel')->getRootDir().'/../web/uploads/festivals/';
                if (!is_dir($dFestival)) { mkdir($dFestival, 0777); chmod($dFestival, 0777); }

                $dId = $dFestival.$id.'/';
                if (!is_dir($dId)) { mkdir($dId, 0777); chmod($dId, 0777); }
                if (!is_dir($dId.'header/')) { mkdir($dId.'header/', 0777); chmod($dId.'header/', 0777);}
                //
                $nm = $image->getClientOriginalName(); $oB->setImage($nm);

                $image->move($dId.'header', $nm);
                $oR->setSimple($nm, $nm, $dId.'header/', 200, 45, 0, 0, '', array('metodo' => 'full'));

                $em->persist($oB); $em->flush();
            }
        }
    }

} // end class