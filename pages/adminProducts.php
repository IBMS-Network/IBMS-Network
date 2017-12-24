<?php

namespace pages;

use classes\clsAdminBrands;
use classes\clsAdminCategories;
use classes\clsAdminCountries;
use classes\clsAdminModels;
use classes\clsAdminProducts;
use classes\clsAdminTextures;
use classes\clsAdminColors;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use entities\Color;
use entities\Product;
use entities\ProductStatusTypes;
use entities\ProductAvailabilityTypes;
use entities\Category as ProjCategory;

use entities\Texture;
use Entity\Category;
use PHPExcel_IOFactory;
use PHPExcel;

/**
 * Class for admin entity adminProduct. Set CRUD actions under the Products.
 * @author Anatoly.Bogdanov
 *
 */
class adminProducts extends clsAdminController
{

    /**
     * self object
     * @var adminProducts
     */
    private static $instance = null;

    /**
     * @var string $upload_path ;
     */
    protected $upload_path = 'images/catalog/products/';

    /**
     * @var int $products_per_page ;
     */
    const PRODUCT_PER_PAGE = 20;

    /**
     * Object of the clsAdminProducts class
     * @var clsAdminProducts $objProduct
     */
    private $objProduct = "";

    /**
     * Constructor of the class of controller.
     * Set entity name, get object of the Product, set menu item and tab item active
     */
    public function __construct()
    {
        parent::__construct();
        $this->parser->entity_name = $this->entity = clsAdminCommon::getAdminMessage('product', ADMIN_ENTITIES_BLOCK);
        $this->objProduct = clsAdminProducts::getInstance();
        $this->parser->is_cats_menu = true; //set active catalog tab in sub menu
        $this->parser->is_prod_tab = true; //set active product in left menu
        $this->parser->current_page = ADMIN_PATH . '/products/';
    }

    /**
     * getInstance function create or return alreadty exists object of this class
     *
     * @return object $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminProducts();
        }
        return self::$instance;
    }

    /**
     * Get start page
     *
     * @return array
     */
    public function actionIndex()
    {
        $page = (int)$this->get['page'] > 0 && (int)$this->get['page'] < INT_MAX ? (int)$this->get['page'] : 1;
        $sort = !empty($this->get['sort']) ? $this->get['sort'] : '';
        $sorter = !empty($this->get['sorter']) ? $this->get['sorter'] : 'desc';
        $filter = !empty($this->get['filter']) ? $this->get['filter'] : array();
        $this->parser->products = $this->objProduct->getProductsList(
            $page,
            static::PRODUCT_PER_PAGE,
            $sort,
            $sorter,
            $filter
        );
        $count = $this->objProduct->getProductsListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->curent_page,
            $count,
            $page,
            static::PRODUCT_PER_PAGE,
            $sort,
            $sorter,
            $filter
        );
        $this->parser->categories = clsAdminCategories::getInstance()->getCategoriesList(1, ADMIN_SELECT_LIMIT);
        $this->parser->brands = clsAdminBrands::getInstance()->getBrandsList(1, ADMIN_SELECT_LIMIT);
        $this->parser->statuses = ProductStatusTypes::getValuesByAssoc();
        return $this->parser->render('@main/pages/catalog/products/admin/index.html');
    }

    /**
     * add Product controller
     * @see engine\modules\admin.clsAdminController::actionAdd()
     */
    public function actionAdd()
    {
        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $error = '';
            if (empty($this->post['name'])) {
                $fieldname = clsAdminCommon::getAdminMessage('name', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($this->post['articul'])) {
                $fieldname = clsAdminCommon::getAdminMessage('articul', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($this->post['price'])) {
                $fieldname = clsAdminCommon::getAdminMessage('price', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            $price2 = (!empty($this->post['price2']) && !empty($this->post['sale'])) ? $this->post['price2'] : 0;
            $status = (!empty($this->post['status'])) ? $this->post['status'] : 0;
            $similars = array();
            if (!empty($this->post['product_select_id'])) {
                $similars = array();
                foreach ($this->post['product_select_id'] as $key => $value) {
                    $hit_id = (int)$this->post['product_select_id'][$key] > 0 ? (int)$this->post['product_select_id'][$key] : (int)$this->post['product_id'][$key];
                    $hit_articul = (int)$this->post['product_articul'][$key] > 0 ? (int)$this->post['product_articul'][$key] : 0;
                    if (!empty($hit_id) || !empty($hit_articul)) {
                        $similars[] = array('id' => $hit_id, 'articul' => $hit_articul);
                    }
                }
            }

            if (empty($error)) {
                $img = '';
                if (!empty($_FILES)) {
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH . $img);
                    if ($res_img === true) {
                        $img .= $filename;
                    } elseif (is_array($res_img)) {
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    } else {
                        $img = '';
                    }
                }
                $productIsUpdated = $this->objProduct->addProduct(
                    $this->post['name'],
                    $this->post['category'],
                    $this->post['description'],
                    !empty($this->post['content']) ? $this->post['content'] : $this->post['description'], //TO-DO think about content
                    $this->post['articul'],
                    !empty($this->post['code']) ? $this->post['code'] : '',
                    $this->post['price'],
                    $price2,
                    !empty($this->post['model_id']) ? $this->post['model_id'] : 0,
                    $this->post['brand_id'],
                    $this->post['country_id'],
                    $this->post['availability'],
                    !empty($this->post['texture']) ? (int)$this->post['texture'] : 0,
                    !empty($this->post['colors']) ? $this->post['colors'] : array(),
                    $img,
                    $status,
                    $similars
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($productIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
        }
        $this->parser->categories = clsAdminCategories::getInstance()->getCategoriesList(
            1,
            ADMIN_SELECT_LIMIT,
            '',
            'desc',
            array('parent' => null)
        );
        $this->parser->brands = clsAdminBrands::getInstance()->getBrandsList(1, 1000);
        $this->parser->textures = clsAdminTextures::getInstance()->getTexturesList(1, 1000);
        $this->parser->colors = clsAdminColors::getInstance()->getColorsList(1, 1000);
        $this->parser->models = clsAdminModels::getInstance()->getModelsList(1, 1000);
        $this->parser->countries = clsAdminCountries::getInstance()->getCountriesList(1, 1000);
        $this->parser->availabilityTypes = ProductAvailabilityTypes::getValues();
        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/catalog/products/admin/add.html', $vars);
    }

    /**
     * Edit the Product controller
     * @see engine\modules\admin.clsAdminController::actionEdit()
     */
    public function actionEdit()
    {
        $error = '';
        if (!empty($this->post['act']) && $this->post['act'] == "update") {
            if (empty($this->post['name'])) {
                $fieldname = clsAdminCommon::getAdminMessage('name', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($this->post['articul'])) {
                $fieldname = clsAdminCommon::getAdminMessage('articul', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($this->post['price'])) {
                $fieldname = clsAdminCommon::getAdminMessage('price', ADMIN_FIELDS_BLOCK);
                $error = clsAdminCommon::getAdminMessage(
                    'error_field_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%fieldname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }
            if (empty($error)) {
                $res_img = false;
                if (!empty($_FILES)) {
                    $img = $this->upload_path;
                    $filename = $this->post['name'];
                    $res_img = clsCommon::uploadImage($filename, $_FILES['img'], CONFIG_DOMAIN_PATH . $img);
                    if ($res_img === true) {
                        $img .= $filename;
                    } elseif (is_array($res_img)) {
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    } else {
                        $img = '';
                    }
                }
                $price2 = (!empty($this->post['price2']) && !empty($this->post['sale'])) ? $this->post['price2'] : 0;
                $status = (!empty($this->post['status'])) ? $this->post['status'] : 0;
                if (!empty($this->post['product_select_id'])) {
                    $similars = array();
                    foreach ($this->post['product_select_id'] as $key => $value) {
                        $hit_id = (int)$this->post['product_select_id'][$key] > 0 ? (int)$this->post['product_select_id'][$key] : (int)$this->post['product_id'][$key];
                        $hit_articul = (int)$this->post['product_articul'][$key] > 0 ? (int)$this->post['product_articul'][$key] : 0;
                        if (!empty($hit_id) || !empty($hit_articul)) {
                            $similars[] = array('id' => $hit_id, 'articul' => $hit_articul);
                        }
                    }
                }
                $productIsUpdated = $this->objProduct->updateProduct(
                    $this->post['id'],
                    $this->post['name'],
                    $this->post['category'],
                    $this->post['description'],
                    !empty($this->post['content']) ? $this->post['content'] : $this->post['description'],
                    $this->post['articul'],
                    $this->post['code'],
                    $this->post['price'],
                    $price2,
                    $this->post['model_id'],
                    $this->post['brand_id'],
                    $this->post['country_id'],
                    $this->post['availability'],
                    !empty($this->post['texture']) ? (int)$this->post['texture'] : 0,
                    !empty($this->post['colors']) ? $this->post['colors'] : array(),
                    $img,
                    $status,
                    $similars
                );
                $name = addslashes(strip_tags($this->post['name']));
                if ($productIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_edit_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $name)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
                clsCommon::redirect301(
                    'Location: ' . $this->parser->current_page . 'edit/?id=' . clsCommon::isInt($this->get['id'])
                );
            }
        }

        $vars = array('action' => 'update', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        $product = $this->objProduct->getProductById(clsCommon::isInt($this->get['id']));
        $this->parser->categories = clsAdminCategories::getInstance()->getCategoriesList(
            1,
            ADMIN_SELECT_LIMIT,
            '',
            'desc',
            array('parent' => null)
        );
        if ($product->getCategories()) {
            $tmp = array();
            foreach ($product->getCategories() as $v) {
                if (empty($tmp[$v->getParent()->getId()])) {
                    $tmp[$v->getParent()->getId()] = array(
                        'id' => $v->getParent()->getId(),
                        'name' => $v->getParent()->getName(),
                        'cats' => array()
                    );
                }
                $tmp[$v->getParent()->getId()]['cats'][] = $v;
            }
        }
        $this->parser->parentCategories = $tmp;
        if ($product->getColors()) {
            $tmp = array();
            foreach ($product->getColors() as $v) {
                $tmp[] = $v->getId();
            }
        }
        $this->parser->productColorsIds = $tmp;
        $this->parser->brands = clsAdminBrands::getInstance()->getBrandsList(1, 1000);
        $this->parser->textures = clsAdminTextures::getInstance()->getTexturesList(1, 1000);
        $this->parser->colors = clsAdminColors::getInstance()->getColorsList(1, 1000);
        $this->parser->models = clsAdminModels::getInstance()->getModelsList(1, 1000);
        $this->parser->countries = clsAdminCountries::getInstance()->getCountriesList(1, 1000);
        $this->parser->availabilityTypes = ProductAvailabilityTypes::getValues();
        if ($product) {
            $this->parser->product = $product;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => clsCommon::isInt($this->get['id']))
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/catalog/products/admin/edit.html", $vars);
    }

    /**
     * Delete the Product controller
     * @see engine\modules\admin.clsAdminController::actionDelete()
     */
    public function actionDelete()
    {
        $id = clsCommon::isInt($this->get['id']);
        if (empty($id)) {
            $error = clsAdminCommon::getAdminMessage(
                'error_field_empty',
                ADMIN_ERROR_BLOCK,
                array('{%fieldname}' => 'ID')
            );
            $this->error->setError($error, 1, false, true);
        }

        if (empty($error)) {
            $product = $this->objProduct->getProductById($id);
            if (file_exists(CONFIG_DOMAIN_PATH . $product->getImg())) {
                unlink(CONFIG_DOMAIN_PATH . $product->getImg());
            }
            $productIsDeleted = $this->objProduct->deleteProduct($id);
            if ($productIsDeleted) {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'succ_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $id)
                );
                $this->error->setError($actionStatus, 1, true, true);
            } else {
                $actionStatus = clsAdminCommon::getAdminMessage(
                    'error_del_entity',
                    ADMIN_ERROR_BLOCK,
                    array('{%entity}' => $this->entity, '{%entityid}' => $id)
                );
                $this->error->setError($actionStatus, 1, false, true);
            }
        }
        clsCommon::redirect301('Location: ' . $this->parser->current_page);
    }

    public function actionGetsub()
    {
        $result = array('success' => false, 'error' => 'unknown');
        $id = clsCommon::isInt($this->post['category']);
        if (!empty($id)) {
            $category = clsAdminCategories::getInstance()->getCategoryById($id);
            if ($category) {
                $subs = array();
                $_subs = clsAdminCategories::getInstance()->getCategoriesList(
                    1,
                    ADMIN_SELECT_LIMIT,
                    '',
                    '',
                    array('parent' => $id)
                );
                if (!empty($_subs)) {
                    foreach ($_subs as $c) {
                        if (!empty($c) && $c instanceof Category) {
                            $subs[$c->getId()] = $c->getName();
                        }
                    }
                    $result['subs'] = $subs;
                    $result['success'] = true;
                } else {
                    $result['error'] = clsAdminCommon::getAdminMessage(
                        'error_not_get_categories',
                        ADMIN_ERROR_BLOCK
                    );
                }

            } else {
                $result['error'] = clsAdminCommon::getAdminMessage(
                    'error_not_get_categories',
                    ADMIN_ERROR_BLOCK
                );
            }
        } else {
            $result['subs'] = array();
            $result['success'] = true;
        }
        echo json_encode($result);
    }

    public function actionGetproduct()
    {
        $result = array('success' => false, 'error' => 'unknown');
        $id = clsCommon::isInt($this->post['category']);
        if (!empty($id)) {
            $category = clsAdminCategories::getInstance()->getCategoryById($id);
            if ($category) {
                $prods = array();
                $_prods = clsAdminProducts::getInstance()->getProductsList(
                    1,
                    ADMIN_SELECT_LIMIT,
                    '',
                    '',
                    array('category' => $id)
                );
                if (!empty($_prods)) {
                    foreach ($_prods as $p) {
                        if (!empty($p) && $p instanceof Product) {
                            $prods[$p->getId()] = $p->getName();
                        }
                    }
                    $result['prods'] = $prods;
                    $result['success'] = true;
                } else {
                    $result['error'] = clsAdminCommon::getAdminMessage(
                        'error_not_get_products',
                        ADMIN_ERROR_BLOCK
                    );
                }

            } else {
                $result['error'] = clsAdminCommon::getAdminMessage(
                    'error_not_get_categories',
                    ADMIN_ERROR_BLOCK
                );
            }
        } else {
            $result['prods'] = array();
            $result['success'] = true;
        }
        echo json_encode($result);
    }

    public function actionExport()
    {
        $result = array('success' => true, 'error' => 'unknown');

        $prod_count = $this->objProduct->getProductsListCount();
        $products = $this->objProduct->getProductsList(1, $prod_count);
        // Create a new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('Список продуктов');

        // headers
        $rowNumber = 1;
        $headings = array(
            'Брэнд',
            'Артикул',
            'Категория',
            'Подкатегория',
            'Название',
            'Стоимость, руб',
            'Скидка',
            'Описание товара',
            'Цвет',
            'Текстура',
            'Статус',
            'Фото',
            'С этим товаром покупают/Артикул'
        );
        $objPHPExcel->getActiveSheet()->fromArray(array($headings), null, 'A' . $rowNumber);


        // Loop through the result set
        foreach ($products as $product) {
            $rowNumber++;
            if ($product instanceof Product) {

                $categories = $product->getCategories();

                foreach($categories as $category){

                    if($category instanceof ProjCategory) {
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNumber, $product->getBrand()->getName());

                        $articul = $product->getArticul();
                        if(stripos($articul,'Временно') !== false ) {
                            $articul =  'art' . $product->getId();
                            $this->objProduct->setArticul($product, $articul);

                        }


                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNumber, $articul);

                        // category parent
                        $parent = $category->getParent();
                        $parent = !empty($parent) && $parent instanceof ProjCategory ? $parent->getName() : '';

                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNumber, $parent);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNumber, $category->getName());
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNumber, $product->getName());
                        $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowNumber, $product->getPrice());
                        $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowNumber, $product->getPrice2());
                        $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowNumber, $product->getContent());

                        // colors
                        $colors = array();
                        $_colors = $product->getColors();
                        if(!empty($_colors)){
                            foreach($_colors as $color){
                                if($color instanceof Color){
                                    $colors[] = $color->getName();
                                }
                            }
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowNumber, join(',', $colors));

                        // texture
                        $texture = $product->getTexture();
                        $texture = !empty($texture) && $texture instanceof Texture ? $texture->getName() : '';

                        $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowNumber, $texture);

                        // status
                        $status = $product->getStatus() ? 'включен' : 'выключен';
                        $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowNumber, $status);
                        $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowNumber, str_replace('images/', SITE_NAME_URI . 'dimages/',$product->getImg()));

                        // similars
                        $similars = array();
                        $_similars = $product->getSimilars();
                        if(!empty($_similars)){
                            foreach($_similars as $similar){
                                if($similar instanceof Product){
                                    $similars[] = $similar->getArticul();
                                }
                            }
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowNumber, join(',', $similars));
                    }
                }
            }
        }
        // Save as an Excel BIFF (xls) file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="productList.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        die();
    }
}