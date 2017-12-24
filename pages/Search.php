<?php
namespace pages;

use classes\clsSearch;
use classes\core\clsCommon;
use classes\core\clsPage;

class Search extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered search page
     * 
     * @return string
     */
    protected function getContent()
    {
        $query = $this->get('query');
        $page = 1;
        $limit = SEARCH_LIMIT;
        $offset = 0;
        if($this->get('limit') && $this->get('offset')) {
            $limit = clsCommon::isInt($this->get('limit'));
            $offset = clsCommon::isInt($this->get('offset'));
        } else {
            $page = clsCommon::isInt($this->post('page'));
            $page = !empty($page) ? $page : 1;
            $offset = ($page-1) * $limit;
        }

        if (!empty($query)) {
            $this->parser->products = clsSearch::getInstance()->searchProducts($query, $limit, $offset);
            $this->parser->productsCount = clsSearch::getInstance()->searchProductsCount($query);
            
            $this->parser->limit = SEARCH_LIMIT;
            $this->parser->query = $query;
            $this->parser->pageUrl = clsCommon::compileDefaultItemHref('Search', array('query' => $query));
            
            if(clsCommon::isAjax()) {
                $productsArr = array();
                foreach($this->parser->products as $v) {
                    $productsArr[] = $v->getArrayCopy();
                }
                return json_encode(
                        array(
                            'products' => $productsArr,
                            'need_more' => ($this->parser->productsCount > $offset)
                        )
                );
            //render page
            } else {
                return $this->parser->render('@main/pages/search.html');
            }
        } else {
            clsCommon::redirect404();
        }
    }
}
