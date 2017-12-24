<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * News
 *
 *
 * @ORM\Table(name="news",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="slug", columns={"slug"})},
 *      indexes={
 *          @ORM\Index(name="index_author_id", columns={"author_id"})
 *      })
 * @ORM\Entity
 */
class News extends AbstractEntity
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
     * @Gedmo\Slug(fields={"name"})
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

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
     * @ORM\Column(name="text", type="text", length=5000, nullable=true)
     */
    protected $text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updated;
    
    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255, nullable=false)
     */ 
    protected $img = '';

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return News
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return News
     */
    public function setAuthor(\entities\Admin $admin){
        $this->author = $admin;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return News
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return News
     */
    public function setCreated($created)
    {
        if (is_string($created)) {
            $created = new \DateTime($created);
        } elseif (is_array($created)) {
            $created = \DateTime::__set_state($created);
        }

        $this->created = $created;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return News
     */
    public function setUpdated($modified)
    {
        if (is_string($modified)) {
            $modified = new \DateTime($modified);
        } elseif (is_array($modified)) {
            $modified = \DateTime::__set_state($modified);
        }

        $this->updated = $modified;

        return $this;
    }
    
    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return News
     */
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }


}