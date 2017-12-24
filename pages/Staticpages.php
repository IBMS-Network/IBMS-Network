<?php

namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use engine\modules\staticpages\clsSysStaticPage;

class Staticpages extends clsPage
{
    public function __construct()
    {
        parent::clsPage();
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered static page
     * 
     * @return string
     */
    public function getContent()
    {
        //get incoming params
        $page = null;
        $slug = $this->get('slug');
        
        //get static page data
        if ($slug) {
            $pages = clsSysStaticPage::getInstance()->fetchAll(array('slug' => $slug), null, 1);
            if (!empty($pages[0])) {
                $page = $pages[0];
            }
        } elseif ($this->get('id')) {
            $page = clsSysStaticPage::getInstance()->getPage($this->get('id'));
        }
        
        if ($page) {
            //menu pages render
            $this->parser->menuPages = clsSysStaticPage::getInstance()->getForMenu();
            
            $this->parser->title = $page->getTitle();
            $this->parser->page = $page;
            return $this->parser->render('@main/pages/staticpages/view.html');
        } else {
            clsCommon::redirect404();
        }
    }
}