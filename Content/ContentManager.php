<?php namespace WebDev\ContentBundle\Content;
use WebDev\ContentBundle\Entity\Block;

use Symfony\Component\Security\Core\SecurityContextInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Twig_Environment;
use WebDev\ContentBundle\Entity\Page;

class ContentManager
{
    public function __construct(EntityRepository $pageRepository,
                                EntityRepository $blockRepository,
                                Router $router,
                                SecurityContextInterface $securityContext,
                                LoggerInterface $logger)
    {
        $this->pageRepository = $pageRepository;
        $this->blockRepository = $blockRepository;
        $this->router = $router;
        $this->securityContext = $securityContext;
        $this->log = $logger;
    }

    /**
     * Security Context
     * 
     * @var SecurityContextInterface
     */
    protected $securityContext;

    protected $pageRepository;
    protected $blockRepository;
    protected $router;
    protected $log;

    /** 
     * @var WebDev\ContentBundle\Content\ContentContext
     */
    protected $context;
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Indicates whether the current user has access to edit content
     * 
     * @return boolean
     */
    public function isEditAccessGranted()
    {
        return $this->securityContext
                    ->isGranted("ROLE_CONTENT_ADMINISTRATOR");
    }

    public function getBlockAdminUri(Block $block, $format = 'html')
    {
        $router = $this->router;
        $page = $block->getPage();
        return $router->generate('webdev_content_admin_block', array('route' => $page->getRoute(),
                      'placeholder' => $block->getPlaceholder(),
                      '_format' => $format));
    }

    /**
     * Injects the relevant content for the context into each request
     * 
     * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onRequestLoadContext(GetResponseEvent $event)
    {
        if (!$this->context) {
            // Create the content context
            $this->context = new ContentContext();

            // Retrieve all the 'unbound' blocks as a basis for the context
            $this->context
                 ->addBlocks($this->blockRepository
                                  ->findUnbound());

            // Layer the page ontop of the context (if available)
            $route = $event->getRequest()
                           ->attributes
                           ->get('_route');
            if (!$route) {
                $this->log
                     ->err("Route is not yet assigned, cannot load page context.");
                return;
            }
            if ($page = $this->pageRepository
                             ->findOneByRoute($route)) {
                $this->context
                     ->setPage($page);
            } else {
                $this->log
                     ->info("No content page found matching route `$route`");
                $page = new Page();
                $page->setRoute($route);
                $this->context
                     ->setPage($page);
            }
        }

        // Store the context in the request and template engine
        $event->getRequest()
              ->attributes
              ->set('content', $this->context);
    }
}
