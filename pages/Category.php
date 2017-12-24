<?php
namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use engine\modules\catalog\clsCategories;
use engine\modules\catalog\clsProducts;
use classes\clsColors;
use classes\clsTextures;
use classes\clsBrands;
use entities\ProductAvailabilityTypes;
use entities\Category as EntityCategory;

class Category extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered category page
     * 
     * @return string
     */
    protected function getContent()
    {
        //get and prepare incoming params
        $category = null;
        $categories = array();
        $criteria = array();
        $slug = $this->get('slug');
        $aviableDirections = array('asc', 'desc');
        $aviableSort = array('price', 'name');
        $page = $this->post('page') ? (int)$this->post('page') : 1;
        $orderBy = ($this->get('sort') && in_array($this->get('sort'), $aviableSort)) ? $this->get('sort') : CATEGORY_SORT_DEFAULT;
        $direction = ($this->get('direction') && in_array($this->get('direction'), $aviableDirections))
                        ? strtoupper($this->get('direction')) : CATEGORY_SORT_DIRECTION_DEFAULT;
        $limit = $this->get('limit') ? (int)$this->get('limit') : CATEGORY_PRODUCTS_DEFAULT_LIMIT;
        $offset = ($page - 1) * $limit;
        $aviableCriteria = array('pricefrom', 'priceto', 'color', 'texture', 'brand');
        foreach($aviableCriteria as $v) {
            if($this->get[$v]) {
                $criteria[$v] = $this->get[$v];
            }
        }
        $this->parser->urlParams = array_merge(array('sort' => $orderBy, 'direction' => strtolower($direction)), $criteria);

        //get category info
        if ($slug) {
            $category = clsCategories::getInstance()->getCategory((int)$this->get('slug'));
        } elseif ($this->get('id')) {
            $category = clsCategories::getInstance()->getCategory((int)$this->get('id'));
        }
        if ($category) {
            
            //categories menu block
            $this->parser->categoriesMenu = self::getMenu($category);
            
            //get name and description for main category (for 1st level category)
            if(!$category->getParentId()) {
                $this->parser->mainCategoryName = $category->getName();
                $this->parser->mainCategoryDescription = $category->getDescription();
                foreach($category->getChildren() as $v) {
                    $categories[] = $v->getId();
                }
            //get name and description for main category (for 2nd level category)
            } else {
                $this->parser->mainCategoryName = $category->getParent()->getName();
                $this->parser->mainCategoryDescription = $category->getParent()->getDescription();
                $categories[] = $category->getId();
            }
            
            //get products for 2nd level category or 1st level children categories
            $this->parser->products = clsProducts::getInstance()->getProductsByCategoryIds(
                !empty($categories) ? $categories : array((int)$this->get('slug')),
                $criteria,
                array($orderBy => $direction),
                $limit,
                $offset
            );
            
            //get colors for category
            $this->parser->colors = clsColors::getInstance()->getColorsForCategories(
                                    !empty($categories) ? $categories : array((int)$this->get('slug'))
            );
            
            //get textures for category
            $this->parser->textures = clsTextures::getInstance()->getTexturesForCategories(
                                    !empty($categories) ? $categories : array((int)$this->get('slug'))
            );
            
            //get brands for category
            $this->parser->brands = clsBrands::getInstance()->getBrandsForCategories(
                                    !empty($categories) ? $categories : array((int)$this->get('slug'))
            );
            
            //get min and max price for category
            $this->parser->minMax = clsProducts::getInstance()->getMinMaxProductsCategory($categories);
            
            //get products count 
            $this->parser->productsCount = clsProducts::getInstance()->getProductsCount(
                !empty($categories) ? $categories : array((int)$this->get('slug')),
                $criteria
            );

            $this->parser->currentMin = $this->get('pricefrom') ? (int)$this->get('pricefrom') : $this->parser->minMax['minPrice'];
            $this->parser->currentMax = $this->get('priceto') ? (int)$this->get('priceto') : $this->parser->minMax['maxPrice'];
            $this->parser->page = $page;
            $this->parser->limit = $limit;
            $this->parser->pageUrl = clsCommon::compileCategoryItemHref(
                (int)$this->get('slug'),
                false,
                array_merge(array('sort' => $orderBy, 'direction' => $direction), $criteria)
            );
            $this->parser->title = $category->getName();
            $this->parser->path = SERVER_URL_NAME;
            $this->parser->category = $category;
            $this->parser->productAvailabilities = ProductAvailabilityTypes::getValues();
            
            //get data for ajax request (using in pagination)
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
                return $this->parser->render('@main/pages/catalog/categories/view.html');
            }
        } else {
            clsCommon::redirect404();
        }
    }
    
    /**
     * Prepare category menu for category page
     * 
     * @param \Entities\Category $category
     * @return array
     */
    public static function getMenu($category) {
        $categoriesMenu = array();
        
        if(!$category->getParentId()) {
            $mainCat = &$category->getChildren();
        } else {
            $mainCat = &$category->getParent()->getChildren();
        }
        
        //prepare menu
        foreach($mainCat as $v) {
            if($v instanceof EntityCategory) {
                $tmp = array('id' => $v->getId(), 'name' => $v->getName(), 'cnt' => count($v->getProducts()));
                if($v->getId() == $category->getId()) {
                    $tmp['active'] = true;
                }

                //add 3rd level categories
                $children = $v->getChildren();
                if(!empty($children)) {
                    foreach($children as $v2) {
                        if($v2 instanceof EntityCategory) {
                            $cnt = count($v2->getProducts());
                            $tmp2 = array(
                                        'id' => $v2->getId(),
                                        'name' => $v2->getName(),
                                        'cnt' => $cnt
                                    );

                            if($v2->getId() == $category->getId()) {
                                $tmp2['active'] = true;
                                $tmp['unhide'] = true;
                            }

                            $tmp['cnt'] += $cnt;

                            $tmp['children'][] = $tmp2;
                        }
                    }
                }

                $categoriesMenu[] = $tmp;
            }
        }
        
        return $categoriesMenu;
    }
}
