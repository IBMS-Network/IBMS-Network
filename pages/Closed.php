<?php

namespace pages;

class Closed extends clsPage
{

    private $myblocks = array(
        "{HEADER}" => "Header",
        "{CATALOG}" => "Catalog",
        "{SEARCH}" => "Search",
    );

    public function __construct()
    {
        parent::clsPage();

        $this->setBlocks($this->myblocks);
    }

    protected function getHeader()
    {

        $result = parent::getHeader();

        return $result;
    }

    protected function getContent()
    {

        $content = $this->parser->useCache($this->config["URL"][get_class($this)], USE_CURRENT_CACHE, 'closed');
        if ($content) {
            return $content;
        }
        $blocks = $this->doBlocks();
        $vars = array();
        $vars = array_merge($vars, $blocks);

        $this->parser->clear();
        $this->parser->setVars($vars);
        $this->parser->setTemplate("page404.html");
        $content .= $this->parser->getResult(1);
        return $content;
    }

}