<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Form\ImagesType;
use CoolwayFestivales\BackendBundle\Util\ResizeImage;

/**
 * Images controller.
 *
 * @Route("/admin/images")
 */
class ImagesController extends Controller
{
    /**
     * Lists all Images entities.
     *
     * @Route("/", name="admin_images")
     * @Template()
     */
    public function indexAction()
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Images')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:Images')->findInFestival($user->getFeast()->getId());
        }
        return $this->render('BackendBundle:Images:index.html.twig', array("entities" => $entities));
    }
    /**
     * Lists all Images entities.
     *
     * @Route("/list", name="admin_images_list")
     * @Template()
     */
    public function listAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Images:list.html.twig');
    }
    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Images", "entity")
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
     * @Route("/admin_images_grid", name="admin_images_grid")
     * @Template()
     */
    public function gridAction()
    {
        return $this->_datatable()->execute();
    }
    /**
     * @Route("/datatable", name="datatable_images")
     * @Template()
     */
    public function datatableAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Images:index.html.twig');
    }
    /**
     * Crea una nueva images
     *
     * @Route("/create", name="admin_images_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Images();
        $form = $this->createForm(new ImagesType($filtro), $entity);
        $form->bind($request);

        $result = array();
        $em = $this->getDoctrine()->getManager();

        try {
            $em->persist($entity);
            $em->flush();

            // upload images if any
            $this->handleImage($form->get('cartel')->getData(), $entity->getId(), $entity->getFeast()->getId());
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
        echo json_encode($result); die;
    }
    /**
     * Displays a form to create a new Images entity.
     *
     * @Route("/new", name="admin_images_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Images();
        $form = $this->createForm(new ImagesType($filtro), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }
    /**
     * Finds and displays a images entity.
     *
     * @Route("/show", name="admin_images_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Images')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Images entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Displays a form to edit an existing images entity.
     *
     * @Route("/edit", name="admin_images_edit")
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

        $entity = $em->getRepository('BackendBundle:Images')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find images entity.');
        }
        $editForm = $this->createForm(new ImagesType($filtro), $entity);
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
     * @Route("/{id}", name="admin_images_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Images')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Images entity.');
        }
        $editForm = $this->createForm(new ImagesType($filtro), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();

                // upload images if any
                $this->handleImage($editForm->get('cartel')->getData(), $entity->getId(), $entity->getFeast()->getId());

                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
            } catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $result['success'] = false;
        }
        echo json_encode($result); die;
    }
    /**
     * Deletes a Images entity.
     *
     * @Route("/{id}", name="admin_images_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Images')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Images entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_images'));
    }
    /**
     * Creates a form to delete a Images entity by id.
     *
     * @param mixed $id The entity id
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    /**
     * Elimina a petición images entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_images_batchdelete")
     * @Template()
     */
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);
        $em  = $this->getDoctrine()->getManager();

        $repo_images = $this->getDoctrine()->getRepository('BackendBundle:Images');

        foreach ($ids as $id) {
            $entity = $repo_images->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este imageso");
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
    public function handleImage($cartel, $id, $feast_id)
    {
        if ($cartel)
        {
            $oR = new ResizeImage();
            $em = $this->getDoctrine()->getManager();
            $oCartel = $this->getDoctrine()->getRepository('BackendBundle:Images')->find($id);

            if ($oCartel)
            {
                $dFestival = $this->get('kernel')->getRootDir().'/../web/uploads/festivals/';
                if (!is_dir($dFestival)) { mkdir($dFestival, 0777); chmod($dFestival, 0777); }

                $dId = $dFestival.$feast_id.'/';
                if (!is_dir($dId)) { mkdir($dId, 0777); chmod($dId, 0777); }
                if (!is_dir($dId.'cartel/')) { mkdir($dId.'cartel/', 0777); chmod($dId.'cartel/', 0777);}
                //
                $nm = $cartel->getClientOriginalName(); $oCartel->setPath($nm);

                $cartel->move($dId.'cartel', $nm);
                $oR->setSimple($nm, $nm, $dId.'cartel/', 1422, 2211, 0, 0, '', array('metodo' => 'full'));
                $oR->setSimple($nm, 'thumb1_'.$nm, $dId.'cartel/', 300, 300, 0, 0, '', array('metodo' => 'full'));
                $oR->setSimple($nm, 'thumb2_'.$nm, $dId.'cartel/', 100, 100, 0, 0, '', array('metodo' => 'full'));

                $em->persist($oCartel); $em->flush();
            }
        }
    }

} //end class