<?php

namespace entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * SeoFieldsValue
 *
 * @ORM\Table(name="seo_fields_values", indexes={@ORM\Index(name="fk_seo_fields_values_mid_idx", columns={"module_id"}), @ORM\Index(name="fk_seo_fields_values_pid_idx", columns={"page_id"}), @ORM\Index(name="fk_seo_fields_values_fid_idx", columns={"field_id"})})
 * @ORM\Entity
 */
class SeoFieldValue
{

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="module_id", type="integer", nullable=true)
     */
    private $moduleId;

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="page_id", type="integer", nullable=true)
     */
    private $pageId;

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="field_id", type="integer", nullable=true)
     */
    private $fieldId;

    /**
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param $value
     * @return self
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageId(){
        return $this->pageId;
    }

    /**
     * @param $id
     * @return self
     */
    public function setPageId($id){
        $this->pageId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFieldId(){
        return $this->fieldId;
    }

    /**
     * @param $id
     * @return self
     */
    public function setFieldId($id){
        $this->fieldId = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getModuleId(){
        return $this->fieldId;
    }

    /**
     * @param $id
     * @return self
     */
    public function setModuleId($id){
        $this->moduleId = $id;
        return $this;
    }

}
