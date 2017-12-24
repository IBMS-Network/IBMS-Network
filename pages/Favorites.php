<?php

namespace pages;

use classes\core\clsPage;

class Favorites extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    /**
     * Get rendered favorites page
     * 
     * @return string
     */
    protected function getContent()
    {
        return $this->parser->render('@main/pages/favorites.html');
    }

}
