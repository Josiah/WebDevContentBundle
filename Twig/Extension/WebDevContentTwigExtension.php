<?php namespace WebDev\ContentBundle\Twig\Extension;

use Twig_Extension as Extension;
use WebDev\ContentBundle\Content\ContentManager;
use WebDev\ContentBundle\Entity\Block;
use WebDev\ContentBundle\Twig\ContentSectionTokenParser;

class WebDevContentTwigExtension
    extends Extension
{
    public function __construct(ContentManager $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    protected $contentManager;

    /**
     * Determines wether the current content context has the specified block
     *
     * @param string $block
     */
    public function has($block)
    {
        $context = $this->contentManager->getContext();

        return isset($context[$block]);
    }

    public function render($placeholder)
    {
        $context = $this->contentManager->getContext();
        $block = $context[$placeholder];
        $context->markRendered($block);
        
        if ($this->contentManager->isEditAccessGranted()) {
            $uri = $this->contentManager->getBlockAdminUri($block,'json');
            return "<div id=\"webdev_content_{$placeholder}\" data-content-block=\"{$placeholder}\" data-content-uri=\"{$uri}\">{$block}</div>";
        } else {
            return $block;
        }
    }

    public function add($placeholder, $content)
    {
        $context = $this->contentManager->getContext();
        $page = $context->getPage();

        $block = new Block();
        $block->setPage($page);
        $block->setPlaceholder($placeholder);
        $block->setContent($content);
        $page->addBlock($block);
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new ContentSectionTokenParser(),
        );
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getGlobals()
    {
        return array('content' => $this->contentManager->getContext());
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return 'content';
    }
}