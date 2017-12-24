<?php

namespace pages;

use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use engine\modules\seo\clsSeoFields;

class adminSeofields extends clsAdminController
{

    /**
     * @var \engine\modules\seo\clsSeoFields
     */
    private $seoFieldsModel;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('SEO field', ADMIN_ENTITIES_BLOCK);
        $this->seoFieldsModel = clsSeoFields::getInstance();
        $this->parser->is_seo_fields_tab = true;
    }

    public function actionIndex()
    {
        $this->parser->fields = $this->seoFieldsModel->fetchAll([], ['id' => 'DESC']);
        return $this->parser->render('@main/pages/seo/admin/seofields/index.html');
    }

    public function actionAdd()
    {
        if ($this->post()) {
            if (!clsSeoFields::validateFieldName($this->post('name'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_seo_field_name',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Name')
                );
                $this->error->setError($error, 1, false, true);
            } else {
                $field = new \entities\SeoField();
                $field->setName($this->post('name'));
                $addRes = $this->seoFieldsModel->addField($field);
                switch ($addRes) {
                    case clsSeoFields::STATUS_FIELD_ALREADY_EXISTS :
                        $this->error->setError(
                            'Field with name "' . $field->getName() . '" already exists.',
                            1,
                            false,
                            true
                        );
                        break;
                    case clsSeoFields::STATUS_FAIL :
                        $this->error->setError('Field has not been added.', 1, false, true);
                        break;
                    default:
                        $actionStatus = clsAdminCommon::getAdminMessage(
                            'succ_add_entity',
                            ADMIN_ERROR_BLOCK,
                            array('{%entity}' => $this->entity, '{%entityname}' => $field->getName())
                        );
                        $this->error->setError($actionStatus, 1, true, true);
                        clsCommon::redirect301('Location: ' . ADMIN_PATH . '/seofields/index');
                }
            }
        }
        return $this->parser->render('@main/pages/seo/admin/seofields/add.html', ['action_text' => 'Adding']);
    }

    public function actionEdit()
    {
        # form submitted or just view
        $fieldId = (int)$this->post('id') | $this->get('id');
        $field = $this->seoFieldsModel->getField($fieldId);

        if (!$field) {
            clsCommon::redirect301('Location: ' . ADMIN_PATH . '/seofields');
        }

        if ($this->post()) {
            if (!clsSeoFields::validateFieldName($this->post('name'))) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_az',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => 'Name')
                );
                $this->error->setError($error, 1, false, true);
            } else {
                $field->setName($this->post('name'));
                $updateRes = $this->seoFieldsModel->updateField($field);
                switch ($updateRes) {
                    case clsSeoFields::STATUS_FIELD_ALREADY_EXISTS :
                        $this->error->setError(
                            'Field with name ' . $field->getName() . ' already exists.',
                            1,
                            false,
                            true
                        );
                        break;
                    case clsSeoFields::STATUS_FAIL :
                    case clsSeoFields::STATUS_FIELD_NOT_EXISTS :
                        $this->error->setError('Field has not been updated.', 1, false, true);
                        break;
                    default:
                        $actionStatus = clsAdminCommon::getAdminMessage(
                            'succ_edit_entity',
                            ADMIN_ERROR_BLOCK,
                            array('{%entity}' => $this->entity, '{%entityname}' => $field->getName())
                        );
                        $this->error->setError($actionStatus, 1, true, true);
                        clsCommon::redirect301('Location: ' . ADMIN_PATH . '/seofields/index');
                }
            }
        }
        $this->parser->field = $field;
        return $this->parser->render('@main/pages/seo/admin/seofields/edit.html');
    }

    public function actionDelete()
    {
        $delRes = $this->seoFieldsModel->deleteField($this->get('id'));
        switch ($delRes) {
            case clsSeoFields::STATUS_FAIL :
            case clsSeoFields::STATUS_FIELD_NOT_EXISTS :
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $this->get('id'))
                );
                $this->error->setError($actionStatus, 1, false, true);
                break;
            case clsSeoFields::STATUS_SUCCESS :
            default :
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $this->get('id'))
                );
                $this->error->setError($actionStatus, 1, true, true);

        }
        clsCommon::redirect301('Location: ' . ADMIN_PATH . '/seofields/');
    }
}