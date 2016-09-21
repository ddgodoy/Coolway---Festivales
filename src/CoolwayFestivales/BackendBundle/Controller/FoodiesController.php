<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Role\Role;
use CoolwayFestivales\BackendBundle\Util\ResizeImage;
use CoolwayFestivales\BackendBundle\Form\FoodiesType;

/**
 * Foodies controller.
 *
 * @Route("/admin/services")
 */
class FoodiesController extends Controller
{
    /**
     * Lists all Foodies entities.
     *
     * @Route("/", name="admin_foodies")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $auth_checker = $this->get('security.authorization_checker');
        $em = $this->getDoctrine()->getManager();

        if ($auth_checker->isGranted('ROLE_SUPER_ADMIN'))
        {
            $entities = $this->getDoctrine()->getRepository('BackendBundle:Foodies')->findAll();
        } else {
            $token = $this->get('security.token_storage')->getToken();
            $user = $token->getUser();

            $entities = $this->getDoctrine()->getRepository('BackendBundle:Foodies')->findInFestival($user->getFeast()->getId());
        }
        return $this->render('BackendBundle:Foodies:index.html.twig', array("entities" => $entities));
    }
    /**
     * Lists all Foodies entities.
     *
     * @Route("/list", name="admin_foodies_list")
     * @Template()
     */
    public function listAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Foodies:list.html.twig');
    }
    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Foodies", "entity")
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
     * @Route("/admin_foodies_grid", name="admin_foodies_grid")
     * @Template()
     */
    public function gridAction() { return $this->_datatable()->execute(); }

    /**
     * @Route("/datatable", name="datatable_foodies")
     * @Template()
     */
    public function datatableAction()
    {
        $this->_datatable();
        return $this->render('BackendBundle:Foodies:index.html.twig');
    }
    /**
     * Crea una nuevo foodies
     *
     * @Route("/create", name="admin_foodies_create")
     * @Method("post")
     */
    public function createAction(Request $request)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $aFlags = array('foto' => true, 'portada' => true);
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Foodies();
        $form = $this->createForm(new FoodiesType($aFlags, $filtro), $entity);
        $form->bind($request);
        $result = array();

        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($entity); $em->flush();

            // upload images
            $this->handleImage($form->get('foto')->getData(), $form->get('portada')->getData(), $entity->getId());

            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
            //
            $this->getDoctrine()->getRepository('BackendBundle:Foodies')->cleanSocialNetworksValues($entity);
            //
            $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($entity->getFeast()->getId());
        }
        catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }
        echo json_encode($result); die;
    }
    /**
     * Displays a form to create a new foodies entity.
     *
     * @Route("/new", name="admin_foodies_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $aFlags = array('foto' => true, 'portada' => true);
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Foodies();
        $form = $this->createForm(new FoodiesType($aFlags, $filtro), $entity);

        return array('entity' => $entity, 'form' => $form->createView());
    }
    /**
     * Finds and displays a foodies entity.
     *
     * @Route("/show", name="admin_foodies_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Foodies')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        $deleteForm = $this->createDeleteForm($id);

        return array('entity' => $entity, 'delete_form' => $deleteForm->createView());
    }
    /**
     * Displays a form to edit an existing entity.
     *
     * @Route("/edit", name="admin_foodies_edit")
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

        $entity = $em->getRepository('BackendBundle:Foodies')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        $aFlags     = $em->getRepository('BackendBundle:Foodies')->setRequiredImages($entity);
        $editForm   = $this->createForm(new FoodiesType($aFlags, $filtro), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Edits an existing entity.
     *
     * @Route("/{id}", name="admin_foodies_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Foodies')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }
        $aFlags   = $em->getRepository('BackendBundle:Foodies')->setRequiredImages($entity);
        $editForm = $this->createForm(new FoodiesType($aFlags, $filtro), $entity);
        $editForm->bind($request);

        if ($editForm->isValid())
        {
            try {
                $em->persist($entity); $em->flush();

                // upload images
                $this->handleImage($editForm->get('foto')->getData(), $editForm->get('portada')->getData(), $entity->getId());

                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
                //
                $this->getDoctrine()->getRepository('BackendBundle:Foodies')->cleanSocialNetworksValues($entity);
                //
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($entity->getFeast()->getId());
            }
            catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {
            $result['success'] = false;
        }
        echo json_encode($result); die;
    }
    /**
     * Deletes an entity.
     *
     * @Route("/{id}", name="admin_foodies_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Foodies')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find entity.');
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_foodies'));
    }
    /**
     * Creates a form to delete an entity by id.
     *
     * @param mixed $id The entity id
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))->add('id', 'hidden')->getForm();
    }
    /**
     * Elimina a petición entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_foodies_batchdelete")
     * @Template()
     */
    public function batchdeleteAction()
    {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_artist = $this->getDoctrine()->getRepository('BackendBundle:Foodies');

        foreach ($ids as $id) {
            $entity = $repo_artist->find($id);
            try {
                $em->remove($entity);
                $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($entity->getFeast()->getId());
            }
            catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar");
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
    public function handleImage($foto, $portada, $id)
    {
        if ($foto || $portada)
        {
            $oR = new ResizeImage();
            $em = $this->getDoctrine()->getManager();
            $oArtist = $this->getDoctrine()->getRepository('BackendBundle:Foodies')->find($id);

            if ($oArtist)
            {
                $dArtist = $this->get('kernel')->getRootDir().'/../web/uploads/foodies/';
                if (!is_dir($dArtist)) { mkdir($dArtist, 0777); chmod($dArtist, 0777); }

                $dId = $dArtist.$id.'/';
                if (!is_dir($dId)) { mkdir($dId, 0777); chmod($dId, 0777); }

                if (!is_dir($dId.'80/'))  { mkdir($dId.'80/' , 0777); chmod($dId.'80/' , 0777);}
                if (!is_dir($dId.'100/')) { mkdir($dId.'100/', 0777); chmod($dId.'100/', 0777);}
                if (!is_dir($dId.'200/')) { mkdir($dId.'200/', 0777); chmod($dId.'200/', 0777);}
                if (!is_dir($dId.'400/')) { mkdir($dId.'400/', 0777); chmod($dId.'400/', 0777);}
                if (!is_dir($dId.'cover/')) { mkdir($dId.'cover/', 0777); chmod($dId.'cover/', 0777);}
                //
                if ($foto)
                {
                    $nm = $foto->getClientOriginalName(); $oArtist->setPath($nm);

                    $foto->move($dId, $nm);
                    $oR->setSimple($nm, $nm, $dId, 400, 400, 0, 0, '', array('destino' => $dId.'400/', 'metodo' => 'full'));
                    $oR->setSimple($nm, $nm, $dId.'400/', 200, 200, 0, 0, '', array('destino' => $dId.'200/', 'metodo' => 'full'));
                    $oR->setSimple($nm, $nm, $dId.'200/', 100, 100, 0, 0, '', array('destino' => $dId.'100/', 'metodo' => 'full'));
                    $oR->setSimple($nm, $nm, $dId.'100/',  80,  80, 0, 0, '', array('destino' => $dId.'80/' , 'metodo' => 'full'));
                }
                if ($portada)
                {
                    $nc = $portada->getClientOriginalName(); $oArtist->setCover($nc);

                    $portada->move($dId.'cover', $nc);
                    $oR->setSimple($nc, $nc, $dId.'cover/', 600, 450, 0, 0, '', array('metodo' => 'full'));
                }
                $em->persist($oArtist); $em->flush();
            }
        }
    }

} //end class