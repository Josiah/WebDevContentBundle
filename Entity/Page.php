<?php namespace WebDev\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;

// Annotations
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="content_page")
 */
class Page
{
    public function __construct()
    {
        $this->blocks = new ArrayCollection;
    }

    public function __toString(){ return (string) $this->getTitle(); }

    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @Column
     */
    protected $route;

    /**
     * @Column(nullable=true)
     */
    protected $title;

    /**
     * @Column(nullable=true)
     */
    protected $label;

    /**
     * @OneToMany(targetEntity="Block", mappedBy="page", indexBy="placeholder")
     */
    protected $blocks;

    /**
     * @ManyToOne(targetEntity="Page", inversedBy="children")
     * @var WebDev\ContentBundle\Entity\Page
     */
    protected $parent;

    /**
     * @OneToMany(targetEntity="Page", mappedBy="parent")
     * @var WebDev\ContentBundle\Entity\Page[]
     */
    protected $chidren;

    /**
     * Add block
     *
     * @param WebDev\ContentBundle\Entity\Block $block
     */
    public function addBlock(Block $block)
    {
        if(!$block->getPlaceholder())
        {
            throw new Exception("Cannot add content block without a placeholder.");
        }

        $this->blocks[$block->getPlaceholder()] = $block;
    }

    public function addBlocks(array $blocks)
    {
        foreach($blocks as $block)
        {
            $this->addBlock($block);
        }
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set route
     *
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get blocks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}