<?php

namespace CoolwayFestivales\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sabberworm\CSS\Parser;

/**
 * Blog controller.
 *
 * @Route("/cms")
 */
class BackendController extends Controller {

    /**
     * @Route("/", name="cms_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        return array();
    }

    /**
     * @Route("/mediamanager", name="media_manager")
     * @Template()
     */
    public function mediamanagerAction() {
        return array();
    }

    /**
     * @Route("/slidermanager", name="media_manager")
     * @Template()
     */
    public function slidermanagerAction() {
        return array();
    }

    /**
     * @Route("/grid", name="cms_grid")
     * @Template()
     */
    public function gridAction() {
        return array();
    }

    /**
     * @Route("/marketplace", name="marketplace")
     * @Template()
     */
    public function marketplaceAction() {
        return array();
    }

    /**
     * Crea una nueva customer
     *
     * @Route("/pricing", name="_cms_pricing")
     */
    public function pricingAction() {
        $request = $this->getRequest();
        $user_id = $request->get('user_id');
        return $this->render("AppBundle:Backend:pricing.html.twig", array("user_id" => $user_id));
    }

    /**
     *
     * @Route("/success/{type}", name="_cms_success")
     * @Template()
     */
    public function successAction($type) {
        $user = $this->get('security.context')->getToken()->getUser();
        $type = strtoupper($type);
        $em = $this->getDoctrine()->getManager();
        $repo_role = $em->getRepository("SafetyBundle:Role");
        $role = $repo_role->findOneByName("ROLE_" . $type);

        if (!$role) {
            $role = new \CoolwayFestivales\SafetyBundle\Entity\Role();
            $role->setDescription("Customer");
            $role->setName("ROLE_" . $type);
            $em->persist($role);
            $em->flush();
        }

        if (in_array($role, $user->getRoles())) {
            return new \Symfony\Component\HttpFoundation\Response("Ya ud es miembro " . $type);
        } else {
            $user->addRole($role);
            $em->persist($user);
            $em->flush();
        }
        return array("type" => $type);
    }

    /**
     * @Route("/builder", name="admin_cms")
     * @Template()
     */
    public function builderAction() {
        $em = $this->getDoctrine()->getManager();
        $code = $this->getRequest()->get('token');
        $page_id = $this->getRequest()->get('page', null);

        $repo_site = $em->getRepository("AppBundle:Site");

        if ($page_id == null) {
            $sitio = $repo_site->findOneBy(array("code" => $code));
            $pages = $sitio->getPages();
            $page = $sitio->getFirst();

            foreach ($pages as $candidate) {
                if ($candidate->getHomepage()) {
                    $page = $candidate;
                }
            }
        } else {
            $repo_page = $em->getRepository("AppBundle:Page");
            $page = $repo_page->find($page_id);
            $sitio = $page->getSite();
        }


        $plugins = $em->getRepository("AppBundle:Plugin")->findAll();
        $this->get('session')->set('site_id', $sitio->getId());
        return array("site" => $sitio, "page" => $page, "plugins" => $plugins);
    }

    /**
     * @Route("/make_menu", name="make_menu")
     * @Template()
     */
    public function makeMenuAction() {
        $json_menu = $this->getRequest()->get('json_menu');
        $menu = json_decode($json_menu, true);

        $id = $this->getRequest()->get("site");
        $em = $this->getDoctrine()->getManager();
        $repo_site = $em->getRepository("AppBundle:Site");
        $site = $repo_site->find($id);

        $site->setJsonMenu($json_menu);
        $em->persist($site);
        $em->flush();

        return $this->render("AppBundle:Backend:menu.html.twig", array("links" => $menu));
    }

    /**
     * @Route("/select_wireframe", name="select_wireframe")
     * @Template()
     */
    public function selectWireframeAction() {
        $name = $this->getRequest()->get("name");
        return $this->render("AppBundle:Templates:$name.html.twig");
    }

    /**
     * @Route("/select_page", name="select_page")
     * @Template()
     */
    public function selectPageAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();
        $repo_page = $em->getRepository("AppBundle:Page");
        $page = $repo_page->find($id);
        return $this->render("AppBundle:Backend:page_load.html.twig", array("page" => $page));
    }

    /**
     * @Route("/page/{id}", name="page")
     * @Template()
     */
    public function pageAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();
        $repo_page = $em->getRepository("AppBundle:Page");
        $page = $repo_page->find($id);
        return $this->render("AppBundle:Backend:index.html.twig", array("page" => $page));
    }

    /**
     * @Route("/new_page", name="new_page")
     * @Template()
     */
    public function newPageAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $html = $request->get('html');
        $name = $request->get('name', 'page');
        $slug = $request->get('slug');
        $seoTitle = $request->get('title_seo');
        $description_seo = $request->get('description_seo');
        $seoKeywords = $request->get('keywords_seo');
        $page = new CoolwayFestivales\AppBundle\Entity\Page();
        $page->setHtml($this->renderView("AppBundle:Templates:$html.html.twig"));
        $page->setName($name);
        $page->setSeoDescription($description_seo);
        $page->setSeoKeywords($seoKeywords);
        $page->setSeoTitle($seoTitle);
        $page->setSlug($slug);
        $site = $em->getRepository("AppBundle:Site")->find($this->get('session')->get('site_id'));
        $page->setSite($site);

//     $homepage = $site->getPages()->first();
//     if ($homepage)
//         $page->setEditHtml($homepage->getEditHtml());
//     else
        $page->setEditHtml($this->renderView("AppBundle:Templates:blank.html.twig"));

        $em->persist($page);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_cms', array("page" => $page->getId())));
    }

    /**
     * @Route("/update_page", name="update_page")
     * @Template()
     */
    public function updatePageAction() {
        $request = $this->getRequest();
        $edit_content = $request->get('edit_content');
        $edit_header = $request->get('edit_header');
        $css_header = $request->get('css_header');
        $class_header = $request->get('class_header');
        $edit_footer = $request->get('edit_footer');
        $css_footer = $request->get('css_footer');
        $class_footer = $request->get('class_footer');
        $css_content = $request->get('css_content');
        $class_content = $request->get('class_content');
        $css = $request->get('css');
        $fontContent = $request->get('fontContent');
        $fontTitles = $request->get('fontTitles');
        $cssContainer = $request->get('cssContainer');
        $assets = $request->get('assets');
        $menutheme = $request->get('menutheme');
        $id = $request->get('id', 0);

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("AppBundle:Page")->find($id);
        if ($page == null) {
            $name = $request->get('name', 'page');
            $page = new CoolwayFestivales\AppBundle\Entity\Page();
            $page->setName($name);
            $page->setSlug("page");
            $page->setEditHtml($edit_content);
            $page->setCssEditContent($css_content);
            $page->setClassEditContent($class_content);
            $page->setEditHtml($edit_content);
            $page->setAssets($assets);

            $site = $page->getSite();

            $site->setEditFooter($edit_footer);
            $site->setClassEditfooter($class_footer);
            $site->setCssEditFooter($css_footer);


            $site->setEditHeader($edit_header);
            $site->setClassEditHeader($class_header);
            $site->setCssEditHeader($css_header);
        } else {
            $page->setEditHtml($edit_content);
            $page->setCssEditContent($css_content);
            $page->setClassEditContent($class_content);

            $site = $page->getSite();

            $site->setEditFooter($edit_footer);
            $site->setClassEditFooter($class_footer);
            $site->setCssEditFooter($css_footer);
            $site->setEditHeader($edit_header);
            $site->setClassEditHeader($class_header);
            $site->setCssEditHeader($css_header);

            $page->setAssets($assets);
        }

        $site->setCssBody($css);
        $site->setFontContent($fontContent);
        $site->setFontTitles($fontTitles);
        $site->setCssContainer($cssContainer);
        $site->setMenutheme($menutheme);




        $em->persist($page);
        $em->flush();

        $em->persist($site);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response($menutheme);
    }

    /**
     * @Route("/edit_pagename", name="edit_pagename")
     * @Template()
     */
    public function editPagenameAction() {
        $request = $this->getRequest();
        $id = $request->get('id');
        $name = $request->get('name');


        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("AppBundle:Page")->find($id);
        $page->setSlug($name);
        $page->setName($name);

        $em->persist($page);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response($page->getId());
    }

    /**
     * @Route("/page_tree", name="page_tree")
     * @Template()
     */
    public function pageTreeAction() {

        $em = $this->getDoctrine()->getManager();
        $site_id = $this->get('session')->get('site_id');
        $pages = $em->getRepository("AppBundle:Page")->findBySite($site_id);
        return new \Symfony\Component\HttpFoundation\Response(json_encode($pages));
    }

    /**
     * @Route("/page_list", name="page_list")
     * @Template()
     */
    public function pageListAction() {
        $em = $this->getDoctrine()->getManager();
        $site_id = $this->get('session')->get('site_id');
        $site = $em->getRepository("AppBundle:Site")->find($site_id);
        return $this->render("AppBundle:Backend:page_list.html.twig", array("site" => $site));
    }

    /**
     * @Route("/publish_page", name="publish_page")
     * @Template()
     */
    public function publishPageAction() {
        $request = $this->getRequest();
        $html = $request->get('html_container');
        $header = $request->get('html_header');
        $css_header = $request->get('css_header');
        $class_header = $request->get('class_header');
        $footer = $request->get('html_footer');
        $css_footer = $request->get('css_footer');
        $class_footer = $request->get('class_footer');
        $css_content = $request->get('css_content');
        $class_content = $request->get('class_content');
        $image_64 = $request->get('image64');

        $assets = $request->get('assets');
        $id = $request->get('id');

        $assets_json = json_encode($assets);

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository("AppBundle:Page")->find($id);
        $page->setHtml($html);
        $page->setPreview($image_64);
        $page->setAssets($assets_json);

        $site = $page->getSite();
        $site->setHeader($header);
        $site->setFooter($footer);

        $site->setClassFooter($class_footer);
        $site->setCssFooter($css_footer);

        $site->setClassHeader($class_header);
        $site->setCssHeader($css_header);

        $page->setCssContent($css_content);
        $page->setClassContent($class_content);


        $em->persist($site);
        $em->flush();

        $em->persist($page);
        $em->flush();

        $url = $this->generateUrl("render_page", array("slug" => $page->getSlug(), "site_token" => $page->getSite()->getCode()));
        return new \Symfony\Component\HttpFoundation\Response($url);
    }

    /**
     * @Route("/remove_page", name="remove_page")
     * @Template()
     */
    public function removePageAction() {
        $request = $this->getRequest();
        $id = $request->get('id');

        return array();
    }

    /**
     * @Route("/delete_page", name="delete_page")
     * @Method("DELETE")
     */
    public function deletePageAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        $entity = $em->getRepository('AppBundle:Page')->find($id);
        $result = array();

        if (!$entity) {
            $result['success'] = false;
            $result['error'] = array('cause' => 'Not Found', 'message' => 'Unable to find Excursion entity.');
        } else {
            try {
                $em->remove($entity);
                $em->flush();

                $result['success'] = true;
                $result['message'] = "Page deleted successfull";
            } catch (\Exception $ex) {
                $result['success'] = false;
                $result['error'] = array('cause' => 'Intern', 'message' => $ex->getMessage());
            }
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($result));
    }

    /**
     * @Route("/set_theme", name="set_theme")
     * @Template()
     */
    public function setThemeAction() {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $site_id = $request->get('site_id');
        $theme = $request->get('theme');

        $site = $em->getRepository("AppBundle:Site")->find($site_id);
        $site->setTheme($theme);

        $em->persist($site);
        $em->flush();
        return new \Symfony\Component\HttpFoundation\Response("ok");
    }

    /**
     * @Route("/parsercss", name="parsercss")
     * @Template()
     */
    public function parserCssAction() {
        try {
            $this->get('apptibase.cms')->parseAllThemes();
            echo "Se ha parseado satisfactoriamente todos los temas css";
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /**
     * @Route("/themesparser", name="themesparser")
     * @Template()
     */
    public function themesParserAction() {
        $path = "vendor/themes/themes.txt";
        $themes = $this->get('apptibase.cms')->read_file($path);
        $colores = array();
        $finalcolor = array();
        foreach ($themes as $theme) {
            $themeinside = explode("-", $theme);
            $colores["theme"] = $themeinside[0];
            $colores["color1"] = $themeinside[1];
            $colores["color2"] = $themeinside[2];
            $colores["color3"] = $themeinside[3];
            $colores["color4"] = $themeinside[4];
            $colores["color5"] = $themeinside[5];
            $finalcolor[] = $colores;
        }

        return array("colores" => $finalcolor);
    }

    /**
     * @Route("/parsexml", name="parsexml")
     * @Template()
     */
    public function parseXmlAction() {
        try {
            $this->get('apptibase.cms')->parseXMl();
            die;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
