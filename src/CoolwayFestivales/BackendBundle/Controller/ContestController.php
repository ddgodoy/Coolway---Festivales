<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use CoolwayFestivales\BackendBundle\Util\ResizeImage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $sFeastI = $session->get('user_feast_id', '');
        $this->handleTargetFoldersAction($request);

        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $festivales = $this->getDoctrine()->getRepository('BackendBundle:Feast')->listOfFeast($filtro);

        if (empty($sFeastI)) {
            foreach ($festivales as $fvalue) { $session->set('user_feast_id', $fvalue['id']); break; }
        }
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Contest')->findInFestival($session->get('user_feast_id'));

        return $this->render(
            'BackendBundle:Contest:index.html.twig',
            array(
                "entities"  => $entities,
                "festivales"=> $festivales,
                "cfestival" => $session->get('user_feast_id')
            )
        );
    }

    /**
     * @Route("/prepare_upload", name="admin_prepare_upload")
     * @Template()
     */
    public function prepareUploadAction(Request $request)
    {
        return $this->render('BackendBundle:Contest:prepare_upload.html.twig');
    }

    /**
     * @Route("/run_upload", name="admin_run_upload")
     * @Template()
     */
    public function runUploadAction(Request $request)
    {
        $session  = $request->getSession();
        $feast_id = $session->get('user_feast_id');

        $feast   = $this->getDoctrine()->getRepository('BackendBundle:Feast')->find($feast_id);
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

        $session  = $request->getSession();
        $feast_id = $session->get('user_feast_id');

        if ($entity)
        {
            $sDir = $this->get('kernel')->getRootDir().'/../web/uploads/festivals/'.$feast_id.'/concurso/';

            if (file_exists($sDir.$entity->getName()))
            {
                unlink($sDir.$entity->getName());
                unlink($sDir.'200/'.$entity->getName());
                unlink($sDir.'400/'.$entity->getName());
            }
            $em->remove($entity);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('admin_contest'));
    }
    //
    public function handleTargetFoldersAction(Request $request)
    {
        $session  = $request->getSession();
        $feast_id = $session->get('user_feast_id');

        $dFestival = $this->get('kernel')->getRootDir().'/../web/uploads/festivals/';
        if (!is_dir($dFestival)) { mkdir($dFestival, 0777); chmod($dFestival, 0777); }

        $dFeastId = $dFestival.$feast_id.'/';
        if (!is_dir($dFeastId)) { mkdir($dFeastId, 0777); chmod($dFeastId, 0777); }

        $dContest = $dFeastId.'concurso/';
        if (!is_dir($dContest)) { mkdir($dContest, 0777); chmod($dContest, 0777);}

        $dThum200 = $dContest.'200/';
        if (!is_dir($dThum200)) { mkdir($dThum200, 0777); chmod($dThum200, 0777);}

        $dThum400 = $dContest.'400/';
        if (!is_dir($dThum400)) { mkdir($dThum400, 0777); chmod($dThum400, 0777);}

        return;
    }
    /**
     * @Route("/upd_in_session", name="admin_upd_feast_session")
     */
    public function updateFeastInSessionAction(Request $request)
    {
        $id = $request->request->get('id', '');
        $session = $request->getSession();

        if (!empty($id))
        {
            $session->set('user_feast_id', $id);
        }
        return new Response('ok');
    }
    /**
     * @Route("/upd_ganadora/{id}", name="admin_set_winner")
     */
    public function updateGanadoraAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BackendBundle:Contest')->find($id);

        $em->getRepository('BackendBundle:Contest')->clearAllAndSetNew($entity);

        return $this->redirect($this->generateUrl('admin_contest'));
    }

} // end class