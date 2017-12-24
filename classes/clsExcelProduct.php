<?php
/**
 * Created by PhpStorm.
 * User: BV
 * Date: 10.10.14
 * Time: 2:22
 */

namespace classes;

use entities\Category as ProjCategory;

class clsExcelProduct
{

    private $num = 0;
    protected $product = null;
    private $action = '';
    public $isAddBrand = false;
    public $isAddCategory = false;
    public $isAddSubCategory = false;
    protected $columns = array(
        'A' => 'brand',
        'B' => 'articul',
        'C' => 'cat',
        'D' => 'subcat',
        'E' => 'name',
        'F' => 'price',
        'G' => 'price2',
        'H' => 'desc_prod',
        'I' => 'color',
        'J' => 'texture',
        'K' => 'status',
        'L' => 'img',
        'M' => 'same'
    );
    private $errors = array();

    public function __constructor($num)
    {
        $this->num = $num;
    }

    public function __call($name, $arguments)
    {
        $property = array_pop($arguments);
        $value = array_pop($arguments);
        if (array_key_exists($property, $this->columns)) {
            $this->product[$this->num][$this->columns[$property]] = $value;
        }
    }

    public function getAction(){
        return $this->action;
    }

    /**
     * Save product to DB
     * @return FALSE |\entities\Product
     */
    public function save()
    {
        $result = false;
        $this->product = array_pop($this->product);
        if (!empty($this->product)) {
            if ($this->checkArticul()) {
                $brand = $this->getBrand();
                $category = $this->getCategory();
                $sub_category = $this->getCategory($category);

                if (!empty($sub_category) && $sub_category instanceof ProjCategory) {
                    $colors = $this->getColors();
                    $texture = $this->getTexture();
                    $similars = $this->getSimilars();
                    $status = $this->getStatus();

                    // get Product if exists
                    $objProd = clsAdminProducts::getInstance();
                    $product = $objProd->getProductByArticul($this->product['articul']);
                    if (!empty($product)) {

                        // update product
                        $this->action = 'edit';
                        $categories = $product->getCategories();
                        $_cats = array();

                        foreach ($categories as $cat) {
                            if ($cat instanceof ProjCategory) {
                                $_cats[] = $cat->getId();
                            }
                        }
                        $category_id = $sub_category->getId();
                        if(array_search($category_id, $_cats)=== false){
                            $_cats[] = $category_id;
                        }
                        $res = $objProd->updateProduct(
                            $product->getId(),
                            $this->product['name'],
                            $_cats,
                            $this->product['desc_prod'],
                            $this->product['desc_prod'],
                            $this->product['articul'],
                            $this->product['articul'],
                            $this->product['price'],
                            $this->product['price2'],
                            0,
                            $brand->getId(),
                            0,
                            1,
                            $texture,
                            $colors,
                            '',
                            $status,
                            $similars
                        );
                        if ($res) {
                            $result = $res;
                        } else {
                            $this->errors['product_cannot_update'] = 'Cannot update product [' . join(' | ', $this->product) . ']';
                        }
                    } else {

                        // adding product
                        $this->action = 'add';
                        $res = $objProd->addProduct(
                            $this->product['name'],
                            array($sub_category->getId()),
                            $this->product['desc_prod'],
                            $this->product['desc_prod'],
                            $this->product['articul'],
                            $this->product['articul'],
                            $this->product['price'],
                            $this->product['price2'],
                            0,
                            $brand->getId(),
                            0,
                            1,
                            $texture,
                            $colors,
                            '',
                            $status,
                            $similars
                        );
                        if($res){
                            $result = $res;
                        } else {
                            $this->errors['product_cannot_add'] = 'Cannot add product [' . join(' | ', $this->product) . ']';
                        }
                    }
                } else {
                    $this->errors['subcategory_empty'] = 'Subcategory empty for Product with name [' . $this->product['name'] . ']';
                }
            } else {
                $this->errors['articul_empty'] = 'Articul empty for Product with name [' . $this->product['name'] . ']';
            }
        }
        return $result;
    }

    /**
     * Get product data
     * @return null
     */
    public function getProduct()
    {
        return $this->product;
    }

    protected function checkArticul()
    {
        return !empty($this->product['articul']) ? true : false;
    }

    /**
     * Get Brand by name. If not exist - create it
     * @return \entities\Brand|FALSE|null
     */
    protected function getBrand()
    {
        $result = null;
        $brand_name = $this->product['brand'];
        if (!empty($brand_name)) {
            $objBrand = clsAdminBrands::getInstance();
            $_brand = $objBrand->getBrandByName($brand_name);
            if (!empty($_brand)) {
                $result = $_brand;
            } else {
                $res = $objBrand->addBrand($brand_name);
                if ($res) {
                    $result = $objBrand->getBrandById($res);
                    $this->isAddBrand = true;
                } else {
                    $this->errors['brand_cannot_add'] = 'Can not add Brand with name [' . $brand_name . ']';
                }
            }
        } else {
            $this->errors['brand_empty'] = 'Brand name empty';
        }
        return $result;
    }

    /**
     * Get category or Sub category
     * @param null|\entities\Category $parent
     * @return bool|\entities\Category|FALSE|null
     */
    protected function getCategory($parent = null)
    {
        $result = null;
        $is_p = empty($parent);
        $i = $is_p ? 0 : 1;
        $cat_name = $is_p ? $this->product['cat'] : $this->product['subcat'];
        if (!empty($cat_name)) {
            $objCat = clsAdminCategories::getInstance();
            $_cat = $objCat->getCategoryByName($cat_name, $parent);
            if (!empty($_cat)) {
                $result = $_cat;
            } else {
                $parent_id = !$is_p ? $parent->getId() : 0;
                $res = $objCat->addCategory($cat_name, $parent_id);
                if ($res) {
                    $result = $res;
                    if($is_p){
                        $this->isAddCategory = true;
                    } else {
                        $this->isAddSubCategory = true;
                    }
                } else {
                    $this->errors['cat_' . $i . '_cannot_add'] = 'Can not add Category with name [' . $cat_name . '] and parent Id [' . $parent_id . ']';
                }
            }
        } else {
            $this->errors['cat_' . $i . '_empty'] = $is_p ? 'Category name empty' : 'Subcategory name empty';
        }
        return $result;
    }

    protected function getStatus(){
        return $this->product['status'] == 'включен' ? 1 : 0;
    }

    /**
     * Get color ids
     * @return array|null
     */
    protected function getColors()
    {
        $result = null;
        $colors = $this->product['color'];

        $colors = explode(',', $colors);

        if (!empty($colors) && is_array($colors)) {
            $colors = array_map('trim', $colors);
            $objColor = clsAdminColors::getInstance();
            $newColors = array();
            $i = 0;
            foreach ($colors as $color) {
                $_color = $objColor->getColorByName($color);
                if (!empty($_color)) {
                    $newColors[] = $_color->getId();
                } else {
                    $res = $objColor->addColor($color);
                    if ($res) {
                        $newColors[] = $res->getId();
                    } else {
                        $this->errors['color_' . $i . '_cannot_add'] = 'Can not add Color with name [' . $color . ']';
                    }
                }
                $i++;
            }
            $result = $newColors;
        } else {
            $this->errors['colors_incorrect_or_empty'] = 'Colors empty or incorrect[' . $this->product['color'] . ']';
        }
        return $result;
    }

    /**
     * Get Texture
     * @return bool|integer|FALSE|int
     */
    protected function getTexture()
    {
        $result = false;
        $texture_name = $this->product['texture'];
        if (!empty($texture_name)) {
            $objTex = clsAdminTextures::getInstance();
            $_tex = $objTex->getTextureByName($texture_name);
            if (!empty($_tex)) {
                $result = $_tex->getId();
            } else {
                $res = $objTex->addTexture($texture_name);
                if ($res) {
                    $result = $res;
                } else {
                    $this->errors['texture_cannot_add'] = 'Can not add Texture with name [' . $texture_name . ']';
                }
            }
        } else {
            $this->errors['texture_empty'] = 'Texture name empty';
        }
        return $result;
    }

    /**
     * Get similars
     * @return array|null
     */
    protected function getSimilars()
    {
        $result = null;
        $sims = $this->product['same'];

        $sims = explode(',', $sims);
        if (!empty($sims) && is_array($sims)) {
            $sims = array_map('trim', $sims);
            $objProd = clsAdminProducts::getInstance();
            $newSims = array();
            $i = 0;
            foreach ($sims as $sim) {
                $_sim = $objProd->getProductByArticul($sim);
                if (!empty($_sim)) {
                    $newSims[] = array('id'=>$_sim->getId());
                } else {
                    $this->errors['sims_' . $i . '_cannot_find'] = 'Can not find Product with articul [' . $sim . ']';
                }
                $i++;
            }
            $result = $newSims;
        } else {
            $this->errors['sims_incorrect_or_empty'] = 'Similars empty or incorrect[' . $this->product['same'] . ']';
        }
        return $result;
    }

    /**
     * Return Errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

} 