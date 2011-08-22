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

    /** @return string */
    public function __toString() { return spl_object_hash($this); }

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
     * Gets the content sections that are available globally
     *
     * @return WebDev\ContentBundle\Entity\Block[]
     */
    public function getGlobalSections()
    {
        return $this->blocks;
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
     * Indicates that block has been rendered in this context
     *
     * @param WebDev\ContentBundle\Entity\Block
     */
    public function markRendered(Block $block)
    {
        if(!in_array($block,$this->rendered,true))
        {
            $this->rendered[] = $block;
        }
    }

    /**
     * Contains a list of the blocks that have been rendered in this context
     *
     * @var WebDev\ContentBundle\Entity\Block[]
     */
    protected $rendered = array();
    public function getRendered(){ return $this->rendered; }

    /**
     * Indicates whether this context contains the specified section
     *
     * @return bool TRUE if this context contains the section; FALSE otherwise
     */
    public function has($section)
    {
        if(!is_null($this->getPage()) && $this->getPage()->getBlocks()->containsKey($section))
        {
            return true;
        }
        else
        {
            return isset($this->blocks[$section]);
        }
    }

    /**
     * Determines whether the specified section exists within this content context
     *
     * @param string $section
     * @return bool TRUE if the section exists; FALSE otherwise
     */
    public function offsetExists($section)
    {
        return $this->has($section);
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