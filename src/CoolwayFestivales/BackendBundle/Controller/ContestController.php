<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use CoolwayFestivales\BackendBundle\Util\UploadHandler;

/**
 * Contest controller
 *
 * @Route("/admin/contest")
 */
class ContestController extends Controller
{
    /**
     * Lists all Contest entities.
     *
     * @Route("/", name="admin_contest")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Contest')->findAll();

        return $this->render(
            'BackendBundle:Contest:index.html.twig', array("entities" => $entities)
        );
    }

    /**
     * @Route("/prepare_upload", name="admin_prepare_upload")
     * @Template()
     */
    public function prepareUploadAction()
    {
        return $this->render('BackendBundle:Contest:prepare_upload.html.twig');
    }

    /**
     * @Route("/run_upload", name="admin_run_upload")
     * @Template()
     */
    public function runUploadAction()
    {
        $user    = $this->get('security.context')->getToken()->getUser();
        $feast   = $this->getDoctrine()->getRepository('BackendBundle:Feast')->find($user->getFeast()->getId());
        $contest = $this->getDoctrine()->getRepository('BackendBundle:Contest');

        $upload_handler = new UploadHandler(array('contest' => $contest, 'feast' => $feast));
        exit();
    }

    /**
     * @Route("/del_upload/{id}", name="admin_del_upload")
     * @Template()
     */
    public function delUploadAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Contest')->find($id);

        if ($entity)
        {
            $sDir = $this->get('kernel')->getRootDir().'/../web/uploads/contest/';

            if (file_exists($sDir.$entity->getName()))
            {
                unlink($sDir.$entity->getName());
                unlink($sDir.'thumbnail/'.$entity->getName());
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_contest'));
    }

} // end class