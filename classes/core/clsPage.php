<?php

namespace classes\core;

use classes\clsSliders;
use engine\modules\general\clsSysPage;
use classes\clsWebAuthorisation;
use classes\clsNews;
use engine\modules\catalog\clsProducts;
use engine\modules\catalog\clsCategories;
use classes\clsProductsNew;
use classes\clsParams;

abstract class clsPage extends clsSysPage
{

    public function clsPage()
    {
        parent::__construct();
    }

    protected function getHeader($method = "")
    {

        $this->parser->clear();
        $this->parser->setTemplate("header.html");
        $change = array("{JS}" => $this->getJSHTML(), "{CSS}" => $this->getCSSHTML(), "{BREAD_CRUMB}" => $this->getBreadCrumb(), "{TITLE}" => $this->getMetaData("Title", $method), "{TITLE_DESC}" => $this->getMetaData("Description", $method), "{TITLE_KEYWORDS}" => $this->getMetaData("Keywords", $method), "{SEARCH_PAGE}" => $this->config ["URL"] ["SearchResults"], "{FIRST_COMPLETE_ORDER}" => $_SESSION ['firstCompleteOrder'] ? true : false, "{SERVER_URL_NAME}" => SERVER_URL_NAME, "{PAGE}" => strpos($_GET ['q'], '/') != false ? substr($_GET ['q'], 0, strpos($_GET ['q'], '/')) : $_GET ['q']);
        $change ["{META_ROBOTS}"] = $this->isDisallowedPage() ? '<meta name="robots" content="noindex, nofollow, noarchive" />' : '<meta name="robots" content="index, follow" />';
        $this->parser->setVars($change);

        $headerMenuBlock = '';
        if (file_exists(CLS_PATH . "clsTheme.php")) {
            require_once (CLS_PATH . "clsTheme.php");
            if (class_exists('clsTheme')) {
                $objTheme = new clsTheme ();
                if (method_exists('clsTheme', 'getHeaderMenuBlock')) {
                    $headerMenuBlock = str_replace('{SERVER_URL_NAME}', SERVER_URL_NAME, $objTheme->getHeaderMenuBlock());
                }
            }
        }
        $this->parser->setVar("{HEADER_MENU_BLOCK}", $headerMenuBlock);

        return $this->parser->getResult();
    }

    protected function getCustomMetaData($type, $page = "", $id = 0)
    {

        if (is_string($type) && !empty($type) && is_string($page) && !empty($page) && is_int($id) && !empty($id)) {

            $result = clsMeta::getInstance()->getValue($type, $page, $id);
        }

        return $result;
    }

    protected function preparePage()
    {
        self::setLayoutVars();
        parent::preparePage();
    }

    protected function setLayoutVars()
    {
        $auth = clsWebAuthorisation::getInstance();
        if ($auth->isAuthorized()) {
            $this->parser->authUser = $auth->getUserSession();
        }
        
        if($key = clsParams::getInstance()->getParamValueByName('ga_key')) {
            $this->parser->ga = array('key' => $key, 'url' => SERVER_URL_NAME);
        }
        $this->parser->footer_email = clsParams::getInstance()->getParamValueByName('footer_email');
        $this->parser->footer_phones = clsParams::getInstance()->getParamValueByName('footer_phones');
        $this->parser->newsBlock = clsNews::getInstance()->getLastNews();
                
        $this->parser->discountBlock = clsProducts::getInstance()->getProductsForDiscountBlock(FOOTER_BLOCKS_PRODUCTS_DEFAULT_LIMIT);
        $this->parser->newBlock = clsProductsNew::getInstance()->getProductsForMain(FOOTER_BLOCKS_PRODUCTS_DEFAULT_LIMIT);
        $this->parser->topMenuBlock = clsCategories::getInstance()->getCategoriesForMenu();
        $this->parser->sliders = clsSliders::getInstance()->getAll();
    }

}
