<?php

namespace engine\modules\admin;

use engine\modules\general\clsSysPage;
use engine\clsSysCommon;
use classes\clsAdminAuthorisation;
use classes\core\clsCommon;

class clsAdminPage extends clsSysPage
{
    /**
     * @var clsAdminAuthorisation
     */
    private $adminAuth;

    protected function __construct()
    {
        parent::__construct();
        $this->adminAuth = clsAdminAuthorisation::getInstance();
    }

    /**
     * getHeader function default html HEADER builder for all page.
     *
     * @return default html of the HEADER
     */
    protected function setHeaderVariables()
    {

        if ($this->adminAuth->isAuthorized()) {
            $this->parser->user_name = $this->adminAuth->getStorage()->getParam('admin_name','admin_user');
            $this->parser->is_session = true;
        } else {
            $this->parser->is_session = false;
        }

        $this->parser->bread_crumb = $this->getBreadCrumb();
        $this->parser->title = $this->getMetaData("Title");
        $this->parser->title_desc = $this->getMetaData("Description");
        $this->parser->title_keywords = $this->getMetaData("Keywords");
        $this->parser->server_url_name = SERVER_URL_NAME;
        $this->parser->project_name = PROJECT_NAME . ' ' .PROJECT_VERSION;

        // add dictionary to admins
        $this->setDictionaryNames();

        $this->parser->meta_robots = $this->isDisallowedPage() ? '<meta name="robots" content="noindex, nofollow, noarchive" />' : '<meta name="robots" content="index, follow" />';
    }

    /**
     * Replace button names to dictionary button names
     */
    protected function setDictionaryNames()
    {
        $this->parser->save_name = clsAdminCommon::getAdminMessage("save", ADMIN_TEXTS_BLOCK);
        $this->parser->continue_name = clsAdminCommon::getAdminMessage("continue", ADMIN_TEXTS_BLOCK);
        $this->parser->back_name = clsAdminCommon::getAdminMessage("back", ADMIN_TEXTS_BLOCK);
        $this->parser->add_name = clsAdminCommon::getAdminMessage("add", ADMIN_TEXTS_BLOCK);
        $this->parser->delete_name = clsAdminCommon::getAdminMessage("delete", ADMIN_TEXTS_BLOCK);
        $this->parser->edit_name = clsAdminCommon::getAdminMessage("edit", ADMIN_TEXTS_BLOCK);
        $this->parser->adding_name = clsAdminCommon::getAdminMessage("adding", ADMIN_TEXTS_BLOCK);
        $this->parser->back_to_list_name = clsAdminCommon::getAdminMessage("back_to_list", ADMIN_TEXTS_BLOCK);
        $this->parser->admin_path = ADMIN_PATH;
        $this->parser->admin_panel_name = clsAdminCommon::getAdminMessage("admin_panel", ADMIN_TEXTS_BLOCK);
    }

    /**
     * getCommonClientHeader function get defult settings for header template(include default js,css,meta data)
     *
     */
    protected function getCommonClientHeader()
    {

        parent::getCommonClientHeader();

        $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'admin_header.ini');
        $project_ini_info = $sys_ini_info;
        if (defined('CONFIG_DOMAIN_PATH')) {
            $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
        }
        $header = clsSysCommon::getCommonIniFiles($sys_ini_info, $project_ini_info, false);

        if (!defined('CONFIG_DOMAIN_PATH')) {
            $search = array('{__class_path__}', '{__class_name__}');
            $repl = array($project_ini_info['path'], $project_ini_info['name']);
            $err_mes = clsSysCommon::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
            clsSysCommon::debugMessage($err_mes);
        }

        $this->scriptsManager->registerFile($header["Header_JS"]);
        $this->stylesManager->registerFile($header["Header_CSS"]);

        $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'meta.ini');
        $project_ini_info = $sys_ini_info;
        if (defined('CONFIG_DOMAIN_PATH')) {
            $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
        }
        $this->meta_config = clsSysCommon::getCommonIniFiles($sys_ini_info, $project_ini_info, false);
        if (!defined('CONFIG_DOMAIN_PATH')) {
            $search = array('{__class_path__}', '{__class_name__}');
            $repl = array($project_ini_info['path'], $project_ini_info['name']);
            $err_mes = clsSysCommon::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
            clsSysCommon::debugMessage($err_mes);
        }
    }

    /**
     * Function to get html of this page
     * This function declared in parent class clsContent. Required for site engine.
     *
     * @return html of current page
     */
    public function showContent($action)
    {
        $content = '';
        $this->preparePage();

        if (clsSysCommon::isAjax()) {
            $content = $this->$action();
        } else {
            $this->setHeaderVariables();
            $content .= $this->$action();
        }

        return $content;
    }

}
