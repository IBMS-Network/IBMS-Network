<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * StaticPage
 *
 *
 * @ORM\Table(name="static_pages",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})},
 *      indexes={
 *          @ORM\Index(name="index_author_id", columns={"author_id"})
 *      })
 * @ORM\Entity
 */
class StaticPage extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=45, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var \entities\Admin
     *
     * @ORM\ManyToOne(targetEntity="Admin")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * })
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated = 'CURRENT_TIMESTAMP';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Page", inversedBy="staticPage", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="pages_static_pages",
     *   joinColumns={
     *     @ORM\JoinColumn(name="static_page_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     *   }
     * )
     */
    protected $pages;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="show_in_menu", type="boolean", nullable=false)
     */
    protected $showMenu = false;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return StaticPage
     */
    public function setSlug($slug)
    {
        $slug = strtolower(preg_replace(array('/\s/', '/[^a-zA-Z0-9-_]/'), array('-', ''), $slug));
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return StaticPage
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \entities\Admin
     */
    public function getAuthor(){
        return $this->author;
    }

    /**
     * @param \entities\Admin $admin
     * @return StaticPge
     */
    public function setAuthor(\entities\Admin $admin){
        $this->author = $admin;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return StaticPage
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return StaticPage
     */
    public function setCreated(\DateTime $created = null)
    {
        if (!$created) {
            $created = new \DateTime('now');
        }
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return StaticPage
     */
    public function setUpdated(\DateTime $updated = null)
    {
        if (!$updated) {
            $updated = new \DateTime('now');
        }
        $this->updated = $updated;
        return $this;
    }

    /**
     * @param array $pages
     * @return self
     */
    public function setPages(array $pages)
    {
        $this->pages->clear();
        if ($pages) {
            foreach ($pages as $page) {
                if ($page instanceof \entities\Page) {
                    $this->setPermission($page);
                }
            }
        }
        return $this;
    }

    /**
     * @param \entities\Page $page
     * @return self
     */
    public function setPage(\entities\Page $page)
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
        }
        return $this;
    }
    
    /**
     * @return boolean
     */
    function getShowMenu()
    {
        return $this->showMenu;
    }

    /**
     * @param boolean $showMenu
     * @return StaticPage
     */
    function setShowMenu($showMenu)
    {
        $this->showMenu = $showMenu;
        return $this;
    }


}