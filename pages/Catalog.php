<?php


namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use engine\modules\catalog\clsCategories;
use engine\modules\catalog\clsProducts;

/*
 * deprecated class
 */
class Catalog extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->menuAlias = 'catalog';
    }

    protected function getContent()
    {
        $criteria = array();
        $aviableDirections = array('asc', 'desc');
        $aviableFilters = array('hits' => 'Хиты продаж', 'new' => 'Новинки', 'share' => 'Акции');
        $page = $this->post('page') ? (int)$this->post('page') : 1;
        $orderBy = $this->get('sort') ? $this->get('sort') : CATEGORY_SORT_DEFAULT;
        $direction = ($this->get('direction') && in_array($this->get('direction'), $aviableDirections)) ? strtoupper(
            $this->get('direction')
        ) : CATEGORY_SORT_DIRECTION_DEFAULT;
        $limit = $this->get('limit') ? (int)$this->get('limit') : CATEGORY_PRODUCTS_DEFAULT_LIMIT;
        $offset = ($page - 1) * $limit;
        $aviableCriteria = array('pricefrom', 'priceto', 'type');
        foreach($aviableCriteria as $v) {
            if($this->get[$v]) {
                $criteria[$v] = $this->get[$v];
            }
        }

        $this->parser->products = clsProducts::getInstance()->getProductsByCategoryIds(array(), $criteria, array($orderBy => $direction), $limit, $offset);

        $productsCount = clsProducts::getInstance()->getCountAll($criteria);

        $this->parser->mainCategoryName = ($this->get('type') && in_array($this->get('type'), array_keys($aviableFilters)))
                                            ? $aviableFilters[$this->get('type')] : 'Вся продукция';
        $this->parser->urlPart = clsCommon::compileDefaultItemHref('Catalog');
        $this->parser->productsCount = $productsCount;
        $this->parser->pagesCount = ceil($productsCount / $limit);
        $this->parser->page = $page;
        $this->parser->limit = $limit;
        $this->parser->sort = $orderBy;
        $this->parser->direction = $direction;
        $this->parser->directionNext = $aviableDirections[array_search(strtolower($direction), $aviableDirections) - 1];
        $this->parser->pageUrl = clsCommon::compileDefaultItemHref(
            'Catalog',
            array('limit' => $limit, 'sort' => $orderBy, 'direction' => $direction)
        );

        $this->parser->title = 'Все продукты';
        $this->parser->path = SERVER_URL_NAME;
        return $this->parser->render('@main/pages/catalog/categories/view.html');
    }

}
