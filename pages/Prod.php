<?php

namespace pages;

use classes\core\clsDB;
use classes\core\clsPage;
use entities;

class Prod extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        ini_set("memory_limit", "512M");
        $domain = 'http://rezat.ru/';
        //        $res = $this->get_data($domain.'fonari/podvodnye/?onpage=all&');
        define('CAT_ID', 4);

        header('Content-type: text/html; charset=UTF-8');
        //        $content = iconv("cp1251","UTF-8",$res);

        include SERVER_ROOT . '/vendor/simpledomhtml/simple_html_dom.php';
        //        $html = file_get_html($domain.'fonari/podvodnye/?onpage=all&'); //80
        //        $html = file_get_html($domain.'fonari/nalobnye/?onpage=all&'); //81
        //        $html = file_get_html($domain.'fonari/podvodnye/?onpage=all&'); //82
        //        $html = file_get_html($domain.'fonari/velosipednye/'); //83
        //        $html = file_get_html($domain.'fonari/karmannye_BRL/'); //84
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/skladnye_nozhi_s_fiksatorom_lezvija/?onpage=20&page=74'); //12
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/avtomaticheskie_skladnye_nozhi/?onpage=50&page=5'); //13
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/poluavtomaticheskie_nozhi/?onpage=20&page=10'); //14
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/frontal_nye_i_vykidnye_nozhi/'); //15
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/skadnye_nozhi_bez_fiksatora_lezvija/?onpage=20&page=5'); //16
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/skladnye_shvejtsarskie_nozhi/?onpage=20&page=12'); //17
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/nozhi_dlja_jahtsmenov/?onpage=20&page=4'); //18
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/rybatskie_nozhi/?onpage=20&page=2'); //19
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/nozhibreloki/?onpage=20&page=5'); //20
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/chehly_dlja_nozhej_1/?onpage=20&page=5'); //21
//        $html = file_get_html($domain.'perochinnye_skladnie_nozhi/nozhi-babochki/'); //22
//        $html = file_get_html($domain.'ohotnichi_nozhi/ohotnich_i_nozhi/?onpage=20&page=31'); //23
//        $html = file_get_html($domain.'ohotnichi_nozhi/Scandinavian_Kniives/?onpage=20&page=9'); //24
//        $html = file_get_html($domain.'ohotnichi_nozhi/Skinners/?onpage=20&page=9'); //25
//        $html = file_get_html($domain.'ohotnichi_nozhi/nozhi_s_fiksirovannymi_klinkami/?onpage=20&page=9'); //26
//        $html = file_get_html($domain.'ohotnichi_nozhi/nozhi_kinzhaly/?onpage=20&page=2'); //27
//        $html = file_get_html($domain.'ohotnichi_nozhi/Japan_Tanto/?onpage=20&page=2'); //28
//        $html = file_get_html($domain.'ohotnichi_nozhi/nozhi_skeletnogo_tipa/?onpage=20&page=6'); //29
//        $html = file_get_html($domain.'ohotnichi_nozhi/topory_machete_pily_lopaty/?onpage=20&page=5'); //30
//        $html = file_get_html($domain.'ohotnichi_nozhi/spetsial_nye_i_hoz_bytovye_nozhi/?onpage=20&page=2'); //31
//        $html = file_get_html($domain.'ohotnichi_nozhi/nozhi_dlja_dajverov/'); //32
//        $html = file_get_html($domain.'ohotnichi_nozhi/Fishermans_Knives/?onpage=20&page=4'); //33
//        $html = file_get_html($domain.'ohotnichi_nozhi/machete/?onpage=20&page=4'); //34
//        $html = file_get_html($domain.'ohotnichi_nozhi/nepalskie_noji_kukri/?onpage=20&page=4'); //35
//        $html = file_get_html($domain.'ohotnichi_nozhi/metatel_nye_nozhi/'); //36
//        $html = file_get_html($domain.'ohotnichi_nozhi/Trainers_Knives/?onpage=20&page=2'); //37
//        $html = file_get_html($domain.'ohotnichi_nozhi/klinki/'); //38
//        $html = file_get_html($domain.'ohotnichi_nozhi/nozhi_i_stroporezy_dlja_spasatelej/'); //39
//        $html = file_get_html($domain.'knifes/povarskie/?onpage=20&page=27'); //40
//        $html = file_get_html($domain.'knifes/universalnye/?onpage=20&page=11'); //41
//        $html = file_get_html($domain.'knifes/razdelochnye_i_obvalochnye/?onpage=20&page=9'); //42
//        $html = file_get_html($domain.'knifes/filejnye_dlja_tonkoj_narezki/?onpage=20&page=12'); //43
//        $html = file_get_html($domain.'knifes/dlja_chistki_i_rezki_ovoshchej/?onpage=20&page=4'); //44
//        $html = file_get_html($domain.'knifes/dlja_chistki_ovoshchej_i_fruktov/?onpage=20&page=11'); //45
//        $html = file_get_html($domain.'knifes/dlja_hleba/?onpage=20&page=5'); //46
//        $html = file_get_html($domain.'knifes/dlja_rubki_mjasa/?onpage=20&page=2'); //47
//        $html = file_get_html($domain.'knifes/dlja_stejka/?onpage=20&page=4'); //48
//        $html = file_get_html($domain.'knifes/dlja_syra/'); //49
//        $html = file_get_html($domain.'knifes/spetsial_nye/'); //50
//        $html = file_get_html($domain.'kuhonnye_aksessuary/podstavki_dlja_nozhej/?onpage=20&page=5'); //51
//        $html = file_get_html($domain.'kuhonnye_aksessuary/magnitnye_derzhateli/?onpage=20&page=2'); //52
//        $html = file_get_html($domain.'kuhonnye_aksessuary/razdelochnye_doski/?onpage=20&page=4'); //53
//        $html = file_get_html($domain.'kuhonnye_aksessuary/kuhonnye_nozhnitsy/?onpage=20&page=2'); //54
//        $html = file_get_html($domain.'kuhonnye_aksessuary/sablja_dlja_shampanskogo/'); //55
//        $html = file_get_html($domain.'kuhonnye_aksessuary/otkryvalki_i_shtopory/?onpage=20&page=3'); //56
//        $html = file_get_html($domain.'kuhonnye_aksessuary/dlja_ochistki_ryby/'); //57
//        $html = file_get_html($domain.'kuhonnye_aksessuary/kuhonnye_terki/?onpage=20&page=5'); //58
//        $html = file_get_html($domain.'kuhonnye_aksessuary/ovoshchechistki/?onpage=20&page=3'); //59
//        $html = file_get_html($domain.'kuhonnye_aksessuary/stolovie_pribori/?onpage=20&page=6'); //60
//        $html = file_get_html($domain.'kuhonnye_aksessuary/mel_nitsy/?onpage=20&page=3'); //61
//        $html = file_get_html($domain.'kuhonnye_aksessuary/mehanicheskie_mel_nitsy/?onpage=20&page=5'); //62
//        $html = file_get_html($domain.'kuhonnye_aksessuary/poleznye_melochi/?onpage=20&page=5'); //63
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/elektricheskie_tochilki/?onpage=20&page=2'); //64
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/mehanicheskie_tochilki/?onpage=20&page=3'); //65
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/tochil_nye_nabory/?onpage=20&page=2'); //66
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/musaty/?onpage=20&page=3'); //67
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/almaznye_bruski/?onpage=20&page=5'); //68
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/vodnye_tochml_nye_kamni/?onpage=20&page=5'); //69
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/natural_nye_tochil_nye_kamni/'); //70
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/karmannye_tochilki/?onpage=20&page=6'); //71
//        $html = file_get_html($domain.'instrumenty_dlja_zatochki/komplektujushchie_k_tochilkam/?onpage=20&page=3'); //72
//        $html = file_get_html($domain.'multitul/polnorazmernye/?onpage=20&page=4'); //73
//        $html = file_get_html($domain.'multitul/karmannye/?onpage=20&page=3'); //74
//        $html = file_get_html($domain.'multitul/breloki_dlja_kljuchej/'); //75
//        $html = file_get_html($domain.'multitul/tooi_logic/'); //76
//        $html = file_get_html($domain.'manikjurnye_instrumenty/manicurnie_nabory/?onpage=20&page=11'); //77
//        $html = file_get_html($domain.'manikjurnye_instrumenty/nozhnitsy/?onpage=20&page=2'); //78
//        $html = file_get_html($domain.'manikjurnye_instrumenty/kusachki/'); //79
//        $html = file_get_html($domain.'muzhskie_aksessuary/aksessuary_dlja_brit_ja/?onpage=20&page=3'); //85
//        $html = file_get_html($domain.'muzhskie_aksessuary/opasnye_britvy/'); //86
//        $html = file_get_html($domain.'muzhskie_aksessuary/Termo_cups/'); //87
//        $html = file_get_html($domain.'muzhskie_aksessuary/baseball_bats/'); //88
//        $html = file_get_html($domain.'muzhskie_aksessuary/tactical_difens_pens/?onpage=20&page=3'); //89
//        $html = file_get_html($domain.'muzhskie_aksessuary/inox_case/'); //90
//        $html = file_get_html($domain.'muzhskie_aksessuary/Blowgun/'); //91
//        $html = file_get_html($domain.'muzhskie_aksessuary/walking_sticks/'); //92
//        $html = file_get_html($domain.'muzhskie_aksessuary/Lighters/?onpage=20&page=10'); //93
//        $html = file_get_html($domain.'muzhskie_aksessuary/sharikovye_ruchki/?onpage=20&page=8'); //94
//        $html = file_get_html($domain.'muzhskie_aksessuary/per_evye_ruchki/?onpage=20&page=6'); //95
//        $html = file_get_html($domain.'muzhskie_aksessuary/rollernye_ruchki/?onpage=20&page=4'); //96
//        $html = file_get_html($domain.'muzhskie_aksessuary/podarochnye_nabory_ruchek/'); //97
//        $html = file_get_html($domain . 'muzhskie_aksessuary/mehanicheskie_karandashi/'); //98
        $html = file_get_html($domain . 'knifesset/'); //4
        $i = 0;
        foreach ($html->find('div.n4-1') as $elem) {
            $i++;
            if (stripos($elem->children(1)->plaintext, 'новинка') === false && stripos(
                    $elem->children(1)->plaintext,
                    'хит продаж'
                ) === false
            ) {
                $prod_html[$i]['img'] = ($elem->children(2) && $elem->children(2)->first_child()) ? $elem->children(
                    2
                )->first_child()->src : ''; //image
                $prod_html[$i]['link'] = ($elem->children(4) && $elem->children(4)->first_child()) ? $elem->children(
                    4
                )->first_child()->first_child()->href : ''; // product link
                $prod_html[$i]['name'] = ($elem->children(4) && $elem->children(4)->first_child()) ? $elem->children(
                    4
                )->first_child()->first_child()->plaintext : ''; // product name
                $prod_html[$i]['brand'] = ($elem->children(5) && $elem->children(5)->first_child()) ? $elem->children(
                    5
                )->first_child()->plaintext : ''; //brand
                $prod_html[$i]['model'] = ($elem->children(5) && $elem->children(5)->first_child()) ? $elem->children(
                    5
                )->last_child()->plaintext : ''; //brand
                $prod_html[$i]['price'] = ($elem->children(6) && $elem->children(6)->first_child()) ? $elem->children(
                    6
                )->first_child()->plaintext : 0; //price
            } else {
                $prod_html[$i]['img'] = ($elem->children(3) && $elem->children(3)->first_child()) ? $elem->children(
                    3
                )->first_child()->src : ''; //image
                $prod_html[$i]['link'] = ($elem->children(5) && $elem->children(5)->first_child()) ? $elem->children(
                    5
                )->first_child()->first_child()->href : ''; // product link
                $prod_html[$i]['name'] = ($elem->children(5) && $elem->children(5)->first_child()) ? $elem->children(
                    5
                )->first_child()->first_child()->plaintext : ''; // product name
                $prod_html[$i]['brand'] = ($elem->children(6) && $elem->children(6)->first_child()) ? $elem->children(
                    6
                )->first_child()->plaintext : ''; //brand
                $prod_html[$i]['model'] = ($elem->children(6) && $elem->children(6)->first_child()) ? $elem->children(
                    6
                )->last_child()->plaintext : ''; //brand
                $prod_html[$i]['price'] = ($elem->children(7) && $elem->children(7)->first_child()) ? $elem->children(
                    7
                )->first_child()->plaintext : 0; //price
            }

//            if(empty($prod_html[$i]['img'])){
//                echo '<pre>';print_r($prod_html[$i]);echo '</pre>';
//                die();
//            }

            $prod_html[$i]['settings'] = array();
            // product
            $pr_link = substr($domain, 0, -1) . $prod_html[$i]['link'];
            $pr_html = file_get_html($pr_link);
            if (!empty($pr_html)) {
                foreach ($pr_html->find('div#content table tbody tr td div.params') as $pr_elem) {
                    $prod_html[$i]['articul'] = trim(
                        str_replace('Артикул: ', '', $pr_elem->children(2)->plaintext)
                    ); //articul
                    $c_string = $pr_elem->plaintext;
                    $c_string = substr($c_string, stripos($c_string, 'Код товара:'));
                    $c_string = str_replace('Код товара:', '', $c_string);
                    $c_string = trim(substr($c_string, 0, stripos($c_string, 'Цена')));
                    $prod_html[$i]['code'] = $c_string;
                }

                $k = 0;
                foreach ($pr_html->find('div#content table tbody tr td table.paramstable tbody tr') as $pr_elem) {
                    $k++;
                    $prod_html[$i]['settings'][$k]['name'] = substr($pr_elem->first_child()->plaintext, 0, -2);
                    $prod_html[$i]['settings'][$k]['value'] = $pr_elem->last_child()->plaintext;
                    if (stripos($prod_html[$i]['settings'][$k]['name'], 'Страна изготовитель') !== false) {
                        $prod_html[$i]['is_country'] = true;
                        $prod_html[$i]['country'] = $prod_html[$i]['settings'][$k]['value'];
                    }

                }
            } else {
                echo '<pre>';
                print_r($prod_html[i]);
                echo '</pre>';
                echo '<br>Cannot get link<br>';
            }
            $pr_html = $pr_elem = null;
            echo '<pre>';print_r($prod_html);echo '</pre>';
            break;
        }

        $html = null;
        echo '<pre>';print_r($prod_html);echo '</pre>';die();

        foreach ($prod_html as $k => $c) {

            // save image
            $im = substr($domain, 0, -1) . $c['img'];
            $name = basename($c['img']);
            $data = $this->get_data($im);
            $im_path_clean = 'images/catalog/products/' . $name;
            $im_path = SERVER_ROOT . 'domains/ip.loc/confdesign/' . $im_path_clean;
            file_put_contents($im_path, $data);

            // get em
            $this->em = clsDB::getInstance();

            $brand = $this->em->getRepository('\entities\Brand')->findOneBy(array('name' => trim($c['brand'])));
            $category = $this->em->getRepository('\entities\Category')->find(CAT_ID);
            if ($brand) {

                // find model
                $model = $this->em->getRepository('\entities\Model')->findOneBy(array('name' => trim($c['model'])));
                if (!$model) {
                    $model = new entities\Model();
                    $model->setName(trim($c['model']));

                    $this->em->persist($model);
                    $this->em->flush();
                    echo '<br>Adding model ' . $c['model'] . '<br>';
                }

                // set country if exists
                if ($c['is_country']) {
                    $country = $this->em->getRepository('\entities\Country')->findOneBy(
                        array('name' => trim($c['country']))
                    );
                }

                $product = $this->em->getRepository('\entities\Product')->findOneBy(array('name' => trim($c['name'])));
                if (!$product) {
                    // set product
                    $product = new entities\Product();
                    $product->setName($c['name'])
                        ->setCategory($category)
                        ->setArticul($c['articul'])
                        ->setCode($c['code'])
                        ->setPrice($c['price'])
                        ->setImg($im_path_clean)
                        ->setModel($model)
                        ->setBrand($brand)
                        ->setDescription('');

                    if ($c['is_country']) {
                        $product->setCountry($country);
                    }

                    $this->em->persist($product);
                    $this->em->flush();

                    // set attributes
                    foreach ($c['settings'] as $at) {
                        $attr = new entities\Attribute();
                        $attr->setName(trim($at['name']))
                            ->setValue(substr($at['value'], 0, 8000));

                        $this->em->persist($attr);
                        $this->em->flush();

                        $product->setAttribute($attr);
                    }
                    $this->em->persist($product);
                    $this->em->flush();

                    echo '<br> Successfuly add product ' . $product->getName() . ' with id ' . $product->getId(
                        ) . '<br>';
                }
                $model = $brand = $country = $category = null;
            } else {
                echo '<pre>';
                print_r($c);
                echo '</pre>';
                echo '<br>brand not find<br>';
            }
        }
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
