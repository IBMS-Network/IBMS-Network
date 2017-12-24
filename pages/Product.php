<?php
namespace pages;

//use classes\clsCategory;
use classes\core\clsCommon;
use classes\core\clsPage;
use engine\modules\catalog\clsCategories;
use engine\modules\catalog\clsProducts;
use pages\Category;
use entities\ProductAvailabilityTypes;

class Product extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->menuAlias = 'catalog';
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered product page
     * 
     * @return string
     */
    protected function getContent()
    {
        $product = null;
        $slug = $this->get('slug');

        //get product
        if ($slug) {
//            $pages = clsSysStaticPage::getInstance()->fetchAll(['slug' => $slug], null, 1);
//            if (!empty($pages[0])) {
//                $page = $pages[0];
//            }
            $product = clsProducts::getInstance()->getProduct((int)$this->get('slug'));
        } elseif ($this->get('id')) {
            $product = clsProducts::getInstance()->getProduct((int)$this->get('id'));
        }
        if ($product) {
            //categories menu block
            $this->parser->categoriesMenu = Category::getMenu($product->getCategories()->first());
            $this->parser->productAvailabilities = ProductAvailabilityTypes::getValues();

            $this->parser->title = $product->getName();
            $this->parser->path = SERVER_URL_NAME;
            $this->parser->product = $product;
            return $this->parser->render('@main/pages/catalog/products/view.html');
        } else {
            clsCommon::redirect404();
        }
    }
}
