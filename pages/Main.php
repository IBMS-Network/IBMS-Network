<?php

namespace pages;

use classes\core\clsPage;
use engine\modules\catalog\clsProducts;
use classes\clsProductsHits;
use classes\clsProductsNew;
use entities\ProductAvailabilityTypes;

class Main extends clsPage
{
    public function __construct()
    {
        parent::clsPage();
        $this->parser->menuAlias = 'main';
    }

    /**
     * Get rendered main page
     * 
     * @return string
     */
    protected function getContent()
    {
        $this->parser->hits = clsProductsHits::getInstance()->getProductsForMain();
        $this->parser->hitsCount = clsProductsHits::getInstance()->getProductsCount();
        $this->parser->share = clsProducts::getInstance()->getProductsForDiscountBlock();
        $this->parser->shareCount = clsProducts::getInstance()->getProductsCountForDiscountBlock();
//        $this->parser->markdown = clsProductsMarkdown::getInstance()->getProductsForMain();
        $this->parser->new = clsProductsNew::getInstance()->getProductsForMain();
        $this->parser->newCount = clsProductsNew::getInstance()->getProductsCount();
        $this->parser->productAvailabilities = ProductAvailabilityTypes::getValues();
        
        return $this->parser->render('@main/pages/main.html');
    }

}
