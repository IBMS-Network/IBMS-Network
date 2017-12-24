<?php

namespace pages;

use classes\core\clsPage;

class Page404 extends clsPage
{
    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered 404 error page
     * 
     * @return string
     */
    protected function getContent()
    {
        return $this->parser->render('@main/pages/page404.html');
    }
}