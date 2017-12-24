<?php

namespace pages;

use classes\clsAdminImports;
use classes\core\clsCommon;
use engine\modules\admin\clsAdminCommon;
use engine\modules\admin\clsAdminController;
use PHPExcel_IOFactory;
use PHPExcel_Reader_IReadFilter;
use PHPExcel_Cell;
use classes\clsExcelProduct;

class adminImports extends clsAdminController
{

    /**
     * @var adminImports $instance
     */
    private static $instance = null;

    /**
     * @var clsAdminImports $objImport
     */
    private $objImport = "";

    /**
     * @var string $entity
     */
    protected $entity;

    /**
     * @var string $upload_path ;
     */
    protected $upload_path = 'images/catalog/imports/';

    /**
     * constructor of the class adminImports
     */
    public function __construct()
    {
        parent::__construct();
        $this->objImport = clsAdminImports::getInstance();
        $this->entity = clsAdminCommon::getAdminMessage('import', ADMIN_ENTITIES_BLOCK);
        $this->parser->is_imports_tab = true; //set active import tab in sub menu
        $this->parser->is_cats_menu = true; //set active category in left menu
        $this->parser->current_page = ADMIN_PATH . '/imports/'; // url path to current page
    }

    /**
     * getInstance function create or return already exists object of this class
     *
     * @return adminImports $instance - object of this class
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new adminImports();
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
        $this->parser->imports = $this->objImport->getImportsList($page, DEF_PAGING_NUM, $sort, $sorter, $filter);
        $count = $this->objImport->getImportsListCount($filter);
        $this->parser->admin_paginator = clsCommon::setPaginatorObject(
            $this->parser->current_page,
            $count,
            $page,
            DEF_PAGING_NUM,
            $sort,
            $sorter,
            $filter
        );
        return $this->parser->render('@main/pages/catalog/imports/admin/index.html');
    }

    public function actionAdd()
    {

        if (!empty($this->post['act']) && $this->post['act'] == "add") {
            $error = '';
            $fieldname = clsAdminCommon::getAdminMessage('import_file', ADMIN_ENTITIES_BLOCK);
            if (empty($_FILES)) {
                $error = clsAdminCommon::getAdminMessage(
                    'error_file_empty',
                    ADMIN_ERROR_BLOCK,
                    array('{%entityname}' => $fieldname)
                );
                $this->error->setError($error, 1, false, true);
            }

            if (empty($error)) {
                $res_img = false;
                $filename_for_file = date('d.m.Y_H.i');
                $filename = $fieldname . $filename_for_file;
                if (!empty($_FILES)) {
                    $img = $this->upload_path;
                    $res_img = clsCommon::uploadXLS($filename_for_file, $_FILES['file'], CONFIG_DOMAIN_PATH . $img, false, 10898432);
                    if ($res_img === true) {
                        $img .= $filename_for_file;
                    } elseif (is_array($res_img)) {
                        $this->error->setError($res_img[0], 1, false, true);
                        $img = '';
                    } else {
                        $img = '';
                    }
                }

                $statistics = array('add_prod' => 0, 'edit_prod'=> 0, 'add_cat' =>0, 'add_subcat' => 0, 'add_brand' => 0, 'error_prod' => 0, 'errors' => array());

                /** @var PHPExcel_IOFactory $objReader */
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
//                $objReader = PHPExcel_IOFactory::createReader('Excel2007');

//                echo '<pre>';
                $objReader->setReadDataOnly(true);
                $objReader->setReadFilter( new MyReadFilter() );
                $objPHPExcel = $objReader->load(CONFIG_DOMAIN_PATH .$img);
                $num=$objPHPExcel->getSheetCount() ;
                for($sl=0;$sl<$num;$sl++) {
                    $worksheet=$objPHPExcel->getSheet($sl);
                    foreach ($worksheet->getRowIterator() as $row) {
                        if($row->getRowIndex()>1){
                            $cellIterator = $row->getCellIterator();
                            $product = new clsExcelProduct();
                            foreach ($cellIterator as $cell) {
                                $column = $cell->getColumn();
                                $val = $cell->getCalculatedValue();
//                                $product->$column = $val;
                                $func = 'set' . $column;
                                call_user_func(array($product, $func), $val, $column);
                            }

//                            print_r($product);
//                            echo '<br><br>START save <br><br>';
                            $res = $product->save();
                            if($res){
                                $actionProduct = $product->getAction();
                                switch($actionProduct){
                                    case 'add':
                                        $statistics['add_prod']++;
                                        break;
                                    case 'edit':
                                        $statistics['edit_prod']++;
                                        break;
                                }
                                if($product->isAddBrand){
                                    $statistics['add_brand']++;
                                }
                                if($product->isAddCategory){
                                    $statistics['add_cat']++;
                                }
                                if($product->isAddSubCategory){
                                    $statistics['add_subcat']++;
                                }
                            } else {
                                $statistics['error_prod']++;
                                $statistics['errors'][] = $product->getErrors();
                            }

//                            echo '<br><br>ERRORS <br><br>';
//                            print_r($product->getErrors());
//
//                            echo '<br><br>RESULT <br><br>';
//                            print_r($res->getArrayCopy());
//
//                            echo '<br><br>END save <br><br>';
                        }
                    }
                }

//                print_r($statistics);

//                echo '</pre>';

//                die();
                $statistics = serialize($statistics);
                $importIsUpdated = $this->objImport->addImport($filename, $img, $statistics);
                if ($importIsUpdated) {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'succ_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $filename)
                    );
                    $this->error->setError($actionStatus, 1, true, true);
                    clsCommon::redirect301('Location: ' . $this->parser->current_page);
                } else {
                    $actionStatus = clsAdminCommon::getAdminMessage(
                        'error_add_entity',
                        ADMIN_ERROR_BLOCK,
                        array('{%entity}' => $this->entity, '{%entityname}' => $filename)
                    );
                    $this->error->setError($actionStatus, 1, false, true);
                }
            }
            clsCommon::redirect301('Location: ' . $this->parser->current_page . 'add');
        }

        $vars = array('action' => 'add', 'action_text' => clsCommon::getMessage("adding", "AdminTexts"));
        return $this->parser->render('@main/pages/catalog/imports/admin/add.html', $vars);
    }

    public function actionEdit()
    {

        $error = '';
        $id = clsCommon::isInt($this->get['id']);
        $import = $this->objImport->getImportById($id);
        $vars = array('action' => 'edit', 'action_text' => clsCommon::getMessage("editing", "AdminTexts"));

        if (!empty($import)) { // if we have some element
            $this->parser->import_data = unserialize($import->getDesc());
            $this->parser->import = $import;
        } else {
            $error = clsAdminCommon::getAdminMessage(
                'error_load_entity',
                ADMIN_ERROR_BLOCK,
                array('{%entity}' => $this->entity, '{%entityid}' => $id)
            );
            $this->error->setError($error, 1);
            $this->setSystemErrors();
        }

        return $this->parser->render("@main/pages/catalog/imports/admin/edit.html", $vars);
    }

}

class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        $Retour=false;
        $column=PHPExcel_Cell::columnIndexFromString($column);// Warning ! A=1, not zero as usual
        if($row<2 || $column>16)
            $Retour=false;
        else
            $Retour=true;
        return $Retour;
    }
}