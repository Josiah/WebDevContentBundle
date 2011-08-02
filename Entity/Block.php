<?php namespace WebDev\ContentBundle\Entity;

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
 * @Entity(repositoryClass="WebDev\ContentBundle\Entity\Repository\BlockRepository")
 * @Table(name="content_block")
 */
class Block
{
    public function __toString() { return $this->content; }

    /**
     * @Id @GeneratedValue
     * @Column(type="integer")
     */
    protected $id;

    /**
     * Ties this block to a particular page
     *
     * @ManyToOne(targetEntity="Page",inversedBy="blocks", cascade={"all"})
     */
    protected $page;

    /**
     * @Column
     */
    protected $placeholder;

    /**
     * @Column(type="text")
     */
    protected $content;

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
     * Set placeholder
     *
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Get placeholder
     *
     * @return string 
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set page
     *
     * @param WebDev\ContentBundle\Entity\Page $page
     */
    public function setPage(\WebDev\ContentBundle\Entity\Page $page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return WebDev\ContentBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}