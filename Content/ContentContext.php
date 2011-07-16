<?php namespace WebDev\ContentBundle\Content;

use BadMethodCallException;
use Doctrine\Common\Collections\ArrayCollection;
use WebDev\ContentBundle\Entity\Block;
use WebDev\ContentBundle\Entity\Page;
use ArrayAccess;

/**
 * @author Josiah <josiah@web-dev.com.au>
 */
class ContentContext
    implements ArrayAccess
{
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * @var WebDev\ContentBundle\Entity\Block[]
     */
    protected $blocks;

    /**
     * Adds a block to this context
     *
     * @param WebDev\ContentBundle\Entity\Block $block
     */
    public function addBlock(Block $block)
    {
        $this->blocks[$block->getPlaceholder()] = $block;
    }

    /**
     * Adds a set of blocks to this context
     * 
     * @param WebDev\ContentBundle\Entity\Block[] $block
     */
    public function addBlocks($blocks)
    {
        foreach($blocks as $block) $this->addBlock($block);
    }

    /**
     * @var WebDev\ContentBundle\Entity\Page
     */
    protected $page;
    /** @param WebDev\ContentBundle\Entity\Page $page */
    public function setPage(Page $page) { $this->page = $page; }
    /** @return WebDev\ContentBundle\Entity\Page */
    public function getPage() { return $this->page; }

    /**
     * Determines whether the specified placeholder exists within this content context
     *
     * @param string $placeholder
     * @return bool TRUE if the placeholder exists; FALSE otherwise
     */
    public function offsetExists($placeholder)
    {
        if(!is_null($this->getPage()) && $this->getPage()->getBlocks()->containsKey($placeholder))
        {
            return true;
        }
        else
        {
            return isset($this->blocks[$placeholder]);
        }
    }

    /**
     * Retrieves the specified placeholder from this content context
     *
     * @param string $placeholder
     * @return Block
     */
    public function offsetGet($placeholder)
    {
        if(!is_null($this->getPage()) && $this->getPage()->getBlocks()->containsKey($placeholder))
        {
            return $this->getPage()->getBlocks()->get($placeholder);
        }
        elseif(isset($this->blocks[$placeholder]))
        {
            return $this->blocks[$placeholder];
        }
        else
        {
            return null;
        }
    }

    public function offsetSet($placeholder, $value) { throw new BadMethodCallException(); }
    public function offsetUnset($placeholder) { throw new BadMethodCallException(); }
}