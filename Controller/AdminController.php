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

        $fb = $this->createFormBuilder($block)
            ->add('content','html');
        $form = $fb->getForm($fb);

        // Process the form
        if($request->getMethod() == "POST")
        {
            $form->bindRequest($request);
            if($form->isValid())
            {
                $em->persist($block);
                $em->flush();
            }
        }

        return array('form'=>$form->createView(),'page'=>$page,'block'=>$block);
    }
}