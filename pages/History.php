<?php

namespace pages;

use classes\core\clsPage;

class History extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        return $this->parser->render('@main/pages/history.html');
    }

}
