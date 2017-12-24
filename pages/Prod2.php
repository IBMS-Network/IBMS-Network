<?php

namespace pages;

use classes\core\clsDB;
use classes\core\clsPage;
use entities;
use PHPExcel_IOFactory;
use PHPExcel_Reader_IReadFilter;
use PHPExcel_Cell;
use classes\clsExcelProduct2;
use classes\clsAdminProducts;

class Prod2 extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    private function getBrands()
    {

        /** @var PHPExcel_IOFactory $objReader */
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');

//        echo '<pre>';
        $objReader->setReadDataOnly(true);
        $objReader->setReadFilter(new MyReadFilter());
        $objPHPExcel = $objReader->load(CONFIG_DOMAIN_PATH . 'catalog.xlsx');
        $num = $objPHPExcel->getSheetCount();
        $cats = array();
        for ($sl = 0; $sl < $num; $sl++) {
            $worksheet = $objPHPExcel->getSheet($sl);
            foreach ($worksheet->getRowIterator() as $row) {
                if ($sl != 2) {
                    continue;
                }
                if ($row->getRowIndex() > 1) {
                    $cellIterator = $row->getCellIterator();
                    $product = new clsExcelProduct2();
                    foreach ($cellIterator as $cell) {
                        $column = $cell->getColumn();
                        $val = $cell->getCalculatedValue();
                        $func = 'set' . $column;
                        call_user_func(array($product, $func), $val, $column);


                    }
                    $p = $product->getData();
                    $cats[$p['cat']][] = $p;
                }
            }
        }
//        print_r(array_keys($cats));
//        echo '</pre>';

//        die();
        return $cats;

    }

    protected function getContent()
    {
        ini_set("memory_limit", "512M");
        ini_set('max_execution_time', 0);
        $domain = 'http://karamel-shop.ru/';
        define('CAT_ID', 5);

        header('Content-type: text/html; charset=UTF-8');

        include SERVER_ROOT . '/vendor/simpledomhtml/simple_html_dom.php';
//        $cats = $this->getBrands();


        $brand_id = 15;
        $category_id = 15;
        $status = 1;

        $cat_link = $domain . 'sexy-hair-short-stayling-dlya-volos/';
        
        $html = file_get_html($cat_link); //5
        $i = 0;
        $prod_html = array();
        foreach ($html->find('td.center table tbody tr td[align=left] table tbody tr td.hdbot a') as $elem) {

            $i++;
//            echo 'AAAA ' . $elem->href ."<br>";
            $prod_link = $elem->href;
//            if (stripos($elem->first_child()->children(1)->plaintext, 'новинка') === false && stripos($elem->first_child()->children(1)->plaintext,'хит продаж') === false) {
//                $prod_html[$i]['img'] = ($elem->children(0) && $elem->children(0)->last_child()->first_child()) ? $elem->children(0)->last_child()->first_child()->src : ''; //image
//                $prod_html[$i]['link'] = ($elem->children(0) && $elem->children(0)->children(2)) ? $elem->children(0)->children(2)->href : ''; // product link
//                $prod_html[$i]['name'] = ($elem->children(2) && $elem->children(2)->first_child()) ? $elem->children(2)->first_child()->plaintext : ''; // product name
//                $prod_html[$i]['brand'] = ($elem->children(3) && $elem->children(3)->first_child()) ? $elem->children(3)->first_child()->plaintext : ''; //brand
//                $prod_html[$i]['model'] = ($elem->children(3) && $elem->children(3)->last_child()) ? $elem->children(3)->last_child()->plaintext : ''; //model
//                $prod_html[$i]['price'] = ($elem->children(4) && $elem->children(4)->first_child()) ? $elem->children(4)->first_child()->plaintext: 0; //price
//            } else {
//                $prod_html[$i]['img'] = ($elem->children(0) && $elem->last_child() && $elem->children(0)->last_child()->first_child()) ? $elem->children(0)->last_child()->first_child()->src : ''; //image
//                $prod_html[$i]['link'] = ($elem->children(0) && $elem->children(0)->children(3)) ? $elem->children(0)->children(3)->href : ''; // product link
//                $prod_html[$i]['name'] = ($elem->children(2) && $elem->children(2)->first_child()) ? $elem->children(2)->first_child()->plaintext : ''; // product name
//                $prod_html[$i]['brand'] = ($elem->children(3) && $elem->children(3)->first_child()) ? $elem->children(3)->first_child()->plaintext : ''; //brand
//                $prod_html[$i]['model'] = ($elem->children(3) && $elem->children(3)->last_child()) ? $elem->children(3)->last_child()->plaintext : ''; //model
//                $prod_html[$i]['price'] = ($elem->children(4) && $elem->children(4)->first_child()) ? $elem->children(4)->first_child()->plaintext: 0; //price
//            }

//            if(empty($prod_html[$i]['img'])){
//                echo '<pre>';print_r($prod_html[$i]);echo '</pre>';
//                die();
//            }

//            echo '<pre>';print_r($prod_html[$i]);echo '</pre>';

            // product
            $pr_link = $domain . $prod_link;
            $pr_html = file_get_html($pr_link);
            if (!empty($pr_html)) {
                $k = 0;


                foreach ($pr_html->find('div[itemprop="name"] h1 span b a ') as $pr_elem) {
                    $prod_html[$i]['name'] = $pr_elem->plaintext;
                }
                foreach ($pr_html->find('td#optionPrice') as $pr_elem) {
                    $price = $pr_elem->plaintext;
                    $prod_html[$i]['price'] = str_replace(' руб.', '', $price);
                }

                foreach ($pr_html->find('form#VotingForm') as $pr_elem) {
                    $prod_html[$i]['code'] = $pr_elem->next_sibling()->next_sibling()->plaintext;
                }

                foreach ($pr_html->find('div[itemprop=description]') as $pr_elem) {
                    $prod_html[$i]['desc'] = $pr_elem->plaintext;
                }

                foreach ($pr_html->find('td.imboxr a img') as $pr_elem) {
                    $prod_html[$i]['img'] = str_replace('data/medium/', 'data/big/', $pr_elem->src);
                }
            } else {
                echo 5555;
                echo '<pre>';
                print_r($prod_html[$i]);
                echo '</pre>';
                echo '<br>Cannot get link<br>';
            }
            $pr_html->clear(); // подчищаем за собой
            unset($pr_html);
//            echo '<pre>';print_r($prod_html);echo '</pre>';
//            if($i == 6) {
//                break;
//            }
        }
        $html->clear(); // подчищаем за собой
        unset($html);

        $html = null;

//        echo '<pre>';
//        print_r($prod_html);
//        echo '</pre>';
//        die();

        $counter = array('add' => 0, 'edit' => 0, 'not_changed' => 0 );
        foreach ($prod_html as $k => $c) {

            // save image
            $name = basename($c['img']);
            $data = $this->get_data($c['img']);
            $im_path_clean = 'images/catalog/products/' . $name;
            $im_path = SERVER_ROOT . 'domains/ip.loc/confdesign/' . $im_path_clean;
            file_put_contents($im_path, $data);


            $objProd = clsAdminProducts::getInstance();
            /** @var \entities\Product $product */
            $product = $objProd->getProductByNameAndArticul($c['name'], $c['code']);
            if(!$product) {
                // adding product
                $res = $objProd->addProduct($c['name'], array($category_id),$c['desc'],$c['desc'],$c['code'],$c['code'],$c['price'],0,0,$brand_id,0,1,0,array(),$im_path_clean,1,array());
                if($res){
                    echo '<br> Successfuly add product ' . $c['name'] . ' with id ' . $res . '<br><br><br>';
                    $counter['add']++;
                } else {
                    echo 'Not added '.$c['name'] . '<br><br><br>';
                }
            } else {
                $categories = $product->getCategories();
                $_cats = array();

                foreach($categories as $cat){
                    if($cat instanceof entities\Category){
                        $_cats[] = $cat->getId();
                    }
                }
                if(array_search($category_id, $_cats)=== false){
                    $_cats[] = $category_id;
                    $res = $objProd->updateProduct($product->getId(),$c['name'],$_cats, $c['desc'],$c['desc'],$c['code'],$c['code'],$c['price'],0,0,$brand_id,0,1,0,array(),$im_path_clean,1,array());
                    if($res){
                        $counter['edit']++;
                    } else {
                        echo 'Not edit '.$c['name'] . '<br><br><br>';
                    }
                } else {
                    $counter['not_changed']++;
                    echo 'Not changed '.$c['name'] . '<br><br><br>';
                }

                // update product

            }

            // get em
//            $this->em = clsDB::getInstance();

//            $brand = $this->em->getRepository('\entities\Brand')->findOneBy(array('name' => trim($c['brand'])));
//            $category = $this->em->getRepository('\entities\Category')->find(CAT_ID);
//            if ($brand) {
//
//                // find model
//                $model = $this->em->getRepository('\entities\Model')->findOneBy(array('name' => trim($c['model'])));
//                if (!$model) {
//                    $model = new entities\Model();
//                    $model->setName(trim($c['model']));
//
//                    $this->em->persist($model);
//                    $this->em->flush();
//                    echo '<br>Adding model ' . $c['model'] . '<br>';
//                }
//
//                // set country if exists
//                if ($c['is_country']) {
//                    $country = $this->em->getRepository('\entities\Country')->findOneBy(
//                        array('name' => trim($c['country']))
//                    );
//                }
//
//                $product = $this->em->getRepository('\entities\Product')->findOneBy(array('name' => trim($c['name'])));
//                if (!$product) {
//                    // set product
//                    $product = new entities\Product();
//                    $product->setName($c['name'])
//                        ->setCategory($category)
//                        ->setArticul($c['articul'])
//                        ->setCode($c['code'])
//                        ->setPrice($c['price'])
//                        ->setImg($im_path_clean)
//                        ->setModel($model)
//                        ->setBrand($brand)
//                        ->setDescription('');
//
//                    if ($c['is_country'] && $country) {
//                        $product->setCountry($country);
//                    }
//
//                    $this->em->persist($product);
//                    $this->em->flush();
//
//                    // set attributes
//                    foreach ($c['settings'] as $at) {
//                        $attr = new entities\Attribute();
//                        $attr->setName(trim($at['name']))
//                            ->setValue(substr($at['value'], 0, 8000));
//
//                        $this->em->persist($attr);
//                        $this->em->flush();
//
//                        $product->setAttribute($attr);
//                    }
//                    $this->em->persist($product);
//                    $this->em->flush();
//
//                    echo '<br> Successfuly add product ' . $product->getName() . ' with id ' . $product->getId(
//                        ) . '<br>';
//                }
//                $model = $brand = $country = $category = null;
//            } else {
//                echo '<pre>';
//                print_r($c);
//                echo '</pre>';
//                echo '<br>brand not find<br>';
//            }
        }
        echo 'Успешно добавлено ' .$counter['add']. ' из ' .count($prod_html). ' товаров!!!<br>';
        echo 'Успешно изменено ' .$counter['edit']. ' из ' .count($prod_html). ' товаров!!!<br>';
        echo 'Успешно неизменено ' .$counter['not_changed']. ' из ' .count($prod_html). ' товаров!!!<br>';
        $prod_html = null;
        //        echo $output;

        //

        return '';
    }

    public function get_data($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13'
        );
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

function ucfirst_utf8($str)
{
    return mb_substr(mb_strtoupper($str, 'utf-8'), 0, 1, 'utf-8') . mb_substr($str, 1, mb_strlen($str) - 1, 'utf-8');
}
