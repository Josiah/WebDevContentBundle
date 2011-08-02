<?php namespace WebDev\ContentBundle\Content;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class ContentToolbar
{
    public function __construct(
        ContentManager $contentManager,
        TwigEngine $twig)
    {
        $this->contentManager = $contentManager;
        $this->twig = $twig;
    }

    protected $contentManager;

    protected $twig;
    
    /**
     * Triggers the injection of the content toolbar
     *
     * @param FilterResponseEvent $event
     */
    public function onResponseInjectToolbar(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }
        
        $this->injectToolbar($response);
    }
    
    /**
     * Injects the content toolbar into the Response.
     *
     * @param Response $response A Response instance
     */
    protected function injectToolbar(Response $response)
    {
        if (function_exists('mb_stripos'))
        {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        }
        else
        {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $context = $this->contentManager->getContext();

        if (false !== ($pos = $posrFunction($content, '</body>'))
            && $context)
        {
            $toolbar = PHP_EOL.
                $this->twig->render(
                    'ContentBundle:Admin:toolbar.html.twig',
                    array('context'=>$this->contentManager->getContext())
                )
            .PHP_EOL;
            $content = $substrFunction($content, 0, $pos).$toolbar.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}