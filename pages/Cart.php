<?php
namespace pages;

use classes\clsSession;
use classes\core\clsPage;
use engine\modules\catalog\clsProducts;

class Cart extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        $cart = clsSession::getInstance()->getCart();
        $sum = 0;
        if (!empty($cart)) {
            $ids = array_keys($cart);
            $products = clsProducts::getInstance()->getProductsByIds($ids, true);
            $similars = array();
            
            foreach ($products as $key => $product) {
                $product->count = $cart[$product->getId()]['count'];
                foreach($product->getSimilars() as $v) {
                    $similars[$v->getId()] = $v;
                }

                $sum += ($product->getPrice2() > 0 ? $product->getPrice2() : $product->getPrice()) * $cart[$product->getId()]['count'];
            }
        }
       
        $this->parser->similars = $similars;
        $this->parser->products = $products;
        $this->parser->sum = $sum;
        $this->parser->path = SERVER_URL_NAME;

        return $this->parser->render('@main/pages/cart/view.html');
    }

}
