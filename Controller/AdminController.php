<?php namespace WebDev\ContentBundle\Controller;

// Dependencies
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WebDev\ContentBundle\Content\ContentContext;
use WebDev\ContentBundle\Entity\Page;
use WebDev\ContentBundle\Entity\Block;

// Annotations
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController
    extends Controller
{
    /**
     * @Route("/admin/content/{route}")
     * @Template
     */
    public function pageAction(Page $page=null)
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        if(is_null($page))
        {
            $page = new Page();
            $page->setRoute($request->attributes->get('route'));
        }

        $fb = $this->createFormBuilder($page)
            ->add('title');
        $form = $fb->getForm();

        // Process the form
        if($request->getMethod() == "POST")
        {
            $form->bindRequest($request);
            if($form->isValid())
            {
                $em->persist($page);
                $em->flush();
            }
        }

        return array('form' => $form->createView(), 'page' => $page);
    }

    /**
     * @Route("/admin/content/{route}/{placeholder}")
     * @Template
     */
    public function blockAction( $route, $placeholder )
    {
        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');

        $pageRepository = $this->get('content.page_repository');
        $blockRepository = $this->get('content.block_repository');

        $page = $pageRepository->findOneByRoute($route);
        if(is_null($page))
        {
            $page = new Page();
            $page->setRoute($route);
        }

        if($page->getBlocks()->containsKey($placeholder))
        {
            $block = $page->getBlocks()->get($placeholder);
        }
        else
        {
            $block = new Block();
            $block->setPage($page);
            $block->setPlaceholder($placeholder);
        }

        // Process the form
        if($request->getMethod() == "POST")
        {
            if($request->request->get('content')) {
                $block->setContent($request->request->get('content'));
                $em->persist($block);
                $em->flush();
            }
        }

        return array('page'=>$page,'block'=>$block);
    }
}