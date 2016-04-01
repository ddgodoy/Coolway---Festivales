<?php

namespace CoolwayFestivales\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Googlemap controller.
 *
 * @Route("/admin/googlemap")
 */
class GooglemapController extends Controller
{
    /**
     * @Route("/", name="admin_googlemap")
     * @Route("/{getfestival}", name="admin_googlemap_get")
     * @Template()
     */
    public function indexAction(Request $request, $getfestival = 0)
    {
        $filtro = $this->getDoctrine()->getRepository('BackendBundle:Step')->setFiltroByUser(
            $this->get('security.authorization_checker'), $this->get('security.token_storage')
        );
        $cImage     = $request->request->get('h_image'  , '');
        $cFestival  = $request->request->get('cfestival', $getfestival);
        $cLatitud   = $request->request->get('clatitud' , '');
        $cLongitud  = $request->request->get('clongitud', '');
        $objImage   = $request->files->get('cimage');
        $festivales = $this->getDoctrine()->getRepository('BackendBundle:Feast')->listOfFeast($filtro);

        if ($request->isMethod('post'))
        {
            $lgr_nombres    = $request->request->get('lgr_nombres');
            $lgr_details    = $request->request->get('lgr_details');
            $lgr_latitudes  = $request->request->get('lgr_latitudes');
            $lgr_longitudes = $request->request->get('lgr_longitudes');
            $hid_iconos     = $request->request->get('hid_iconos');
            $lgr_iconos     = $request->files->get('lgr_iconos');
            $count_lugrs    = count($lgr_nombres);

            $this->getDoctrine()->getRepository('BackendBundle:Googlemap')->wipeOutCoordenadas($cFestival);

            if ($count_lugrs > 0)
            {
                for ($i = 0; $i < $count_lugrs; $i++)
                {
                    $upIcono = $lgr_iconos[$i];
                    $l_icono = $hid_iconos[$i];

                    if ($upIcono)
                    {
                        $l_icono = sha1(uniqid(mt_rand(), true)).'.'.$upIcono->guessExtension();
                        $upIcono->move('uploads/googlemap', $l_icono);
                    }
                    $this->getDoctrine()->getRepository('BackendBundle:Googlemap')->addCoordinatesValues(
                        $cFestival, $lgr_nombres[$i], $lgr_details[$i], $lgr_latitudes[$i], $lgr_longitudes[$i], $l_icono
                    );
                }
            }
            if ($objImage)
            {
                $cImage = sha1(uniqid(mt_rand(), true)).'.'.$objImage->guessExtension();
                $objImage->move('uploads/feast', $cImage);
            }
            $this->getDoctrine()->getRepository('BackendBundle:Feast')->updateMapFeastValues(
                $cFestival, $cLatitud, $cLongitud, $cImage
            );
            //
            $this->getDoctrine()->getRepository('BackendBundle:VersionControl')->updateVersionNumber($cFestival);
        } else {
            if ($festivales)
            {
                if (!empty($cFestival))
                {
                    foreach ($festivales as $festival)
                    {
                        if ($festival['f_id'] == $cFestival)
                        {
                            $cLatitud  = $festival['f_latitud'];
                            $cLongitud = $festival['f_longitud'];
                            $cImage    = $festival['f_path'];
                            break;
                        }
                    }
                } else {
                    $cFestival = $festivales[0]['f_id'];
                    $cLatitud  = $festivales[0]['f_latitud'];
                    $cLongitud = $festivales[0]['f_longitud'];
                    $cImage    = $festivales[0]['f_path'];
                }
            }
        }
        return $this->render(
            'BackendBundle:Googlemap:index.html.twig', array(
                "coordenada" => $this->getDoctrine()->getRepository('BackendBundle:Googlemap')->getCoordinatesValues($cFestival),
                "nfestival"  => $this->getDoctrine()->getRepository('BackendBundle:Feast')->find($cFestival),
                "festivales" => $festivales,
                "cfestival"  => $cFestival,
                "clatitud"   => $cLatitud,
                "clongitud"  => $cLongitud,
                "cimage"     => $cImage
            )
        );
    }
   
} // end class