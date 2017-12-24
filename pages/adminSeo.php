<?php

namespace pages;

use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use engine\modules\seo\clsSeo;
use engine\modules\seo\clsSeoFields;

class adminSeo extends clsAdminController
{
    private $seoModel;

    public function __construct()
    {
        parent::__construct();
        $this->entity = clsAdminCommon::getAdminMessage('SEO page', ADMIN_ENTITIES_BLOCK);
        $this->seoModel = clsSeo::getInstance();
        $this->parser->is_seo_tab = true;
    }

    public function actionIndex()
    {
        $pages = $this->seoModel->getPages();
        return $this->parser->render('@main/pages/seo/admin/pages/index.html', ['pages' => $pages]);
    }

    public function actionEdit()
    {
        $pageId = (int)$this->get('id');
        $page = $this->seoModel->getPageByField('id', $pageId);
        if (!$page) {
            clsCommon::redirect404();
        }
        if ($this->post()) {
            $fields = $this->post('fields');
            $this->seoModel->savePageFields($page, $fields);
            $actionStatus = clsAdminCommon::getAdminMessage(
                'succ_edit_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityname}' => $page->getName())
            );
            $this->error->setError($actionStatus, 1, true, true);
            clsCommon::redirect301('Location: ' . ADMIN_PATH . '/seo/edit?id=' . $pageId);

        } else {
            $fields = clsSeoFields::getInstance()->fetchAll();
            $pageFields = $this->seoModel->getPageFields($pageId);
            return $this->parser->render(
                '@main/pages/seo/admin/pages/edit.html',
                ['fields' => $fields, 'page' => $page, 'pageFields' => $pageFields]
            );
        }
    }
}