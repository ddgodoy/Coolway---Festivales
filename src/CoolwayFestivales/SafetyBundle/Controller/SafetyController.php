<?php

namespace CoolwayFestivales\SafetyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * safety controller.
 *
 * @Route("/apptibase/safety")
 */
class SafetyController extends Controller
{
    /**
     * @Route("/{_locale}/panel", name="panel")
     * @Template()
     */
    public function panelAction(){
          /* em */
        $em = $this->getDoctrine()->getManager();
        $provider = $this->get('ACI.acl_manager');
        $tree = $provider->getBundleStruct();
        
        $repo_users = $em->getRepository('SafetyBundle:User');
        $users = $repo_users->findAll();
        
        $repo_roles = $em->getRepository('SafetyBundle:Role');
        $roles = $repo_roles->findAll();
        $permisos = array("apptibase.security.mask.view","apptibase.security.mask.create", "apptibase.security.mask.delete", "apptibase.security.mask.operator");
        
        return array("tree"=>$tree, "users"=>$users, "roles"=>$roles, "permisos"=>$permisos);
    }
    
    /**
     * Dado un si{user/rol} y un oi{clase/objeto} devuelve una lista de permisos 
     * en formato de ui{lista/row}
     *
     * @Route("/permissions", name="permissions")
     * @Template()
     */
    public function permissionsAction()
    {
        $request = $this->getRequest();
        
        $si = $request->get("si");
        $oi = $request->get("oi");
        $ui = $request->get("ui");
        
        $SI = explode("|", $si);
        $OI = explode("|", $oi);
        
        $provider = $this->get('ACI.acl_manager');
        $info_permissions = $provider->getPermissionsBy($SI, $OI);
        
        $dom_obj = $info_permissions["dom_obj"];
        $allowed_permissions = $info_permissions["allowed_permissions"];
        $sec_id = $info_permissions["sec_id"];
        $all_permissions = $info_permissions["all_permissions"];
        
        $template = $ui."_permissions";
        return $this->render("SafetyBundle:Safety:$template.html.twig",
            array(
                "all_permissions" => $all_permissions, 
                "allowed_permissions" =>$allowed_permissions,   
                "sec_id"=>$sec_id, 
                "dom_obj"=>$dom_obj, 
              )
        );
    }
    
    /**
     * Dado un objeto devuelve una lista de permisos 
     * en formato de ui{lista/row}
     * @Route("/permissionspanel", name="permissionspanel")
     * @Template()
     */
    public function permissionspanelAction()
    {
    $oi = $this->getRequest()->get("oi");
    $ui = $this->getRequest()->get("ui");
    
    return array(
        "oi" => $oi, 
        "ui" =>$ui,
      );
    }
    
    
    /**
     * cambia un permission a un  si{user/rol} sobre un oi{clase/objeto}
     *
     * @Route("/set_permissions", name="set_permissions")
     * @Template()
     */
    public function set_permissionsAction()
    {
        $request = $this->getRequest();
        
        $si = $request->get("si");
        $oi = $request->get("oi");
        $mask = $request->get("mask");
        $action = $request->get("action");
        $ui = $request->get("ui");
        
        $SI = explode("|", $si);
        $OI = explode("|", $oi);
        
        $provider = $this->get('ACI.acl_manager');
        try {
            $provider->setPermission($SI, $OI, $mask, $action);
        } catch (\Exception $exc) {
            echo "error: ".$exc->getMessage(); die;
        }
        
        $info_permissions = $provider->getPermissionsBy($SI, $OI);
        
        $dom_obj = $info_permissions["dom_obj"];
        $allowed_permissions = $info_permissions["allowed_permissions"];
        $sec_id = $info_permissions["sec_id"];
        $all_permissions = $info_permissions["all_permissions"];
        
        $template = $ui."_permissions";
        return $this->render("SafetyBundle:Safety:$template.html.twig",
            array(
                "all_permissions" => $all_permissions, 
                "allowed_permissions" =>$allowed_permissions,   
                "sec_id"=>$sec_id, 
                "dom_obj"=>$dom_obj, 
              )
        );
    }

    /**
     * @Route("/safety/dashboard", name="safety_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        /* em */
        return array();
    }
  
} // end class