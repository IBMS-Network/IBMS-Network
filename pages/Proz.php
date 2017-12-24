<?php

namespace pages;

use classes\core\clsPage;
use entities;

class Proz extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        $domain = 'http://rezat.ru/';
        $res = $this->get_data($domain . 'producers/');

        header('Content-type: text/html; charset=UTF-8');
        $content = iconv("cp1251", "UTF-8", $res);

        include SERVER_ROOT . '/vendor/simpledomhtml/simple_html_dom.php';
        $html = file_get_html('http://rezat.ru/producers/');
        $i = 0;
        foreach ($html->find('div.brand1 table tr td img') as $elem) {
            $i++;
            if ($i > 14) {
                $img[] = $elem->src;
            }
        }
        $i = 0;
        foreach ($html->find('div.brand1 table tr td a') as $elem) {
            $i++;
            if ($i > 14) {
                $brands[] = $elem->plaintext;
            }
        }
        $html = null;
        $brands = array_slice($brands, 0, 270);
        $brands = array_unique($brands);

        foreach ($brands as $k => $c) {
            $im = substr($domain, 0, -1) . $img[$k];
            $name = basename($img[$k]);
            $data = $this->get_data($im);
            $im_path_clean = 'images/catalog/brands/' . $name;
            $im_path = SERVER_ROOT . 'domains/ip.loc/confdesign/' . $im_path_clean;
//            file_put_contents($im_path, $data);
//
//            $this->em = clsDB::getInstance();
//            $brand = new entities\Brand();
//            $brand->setName($c)
//            ->setImg($im_path_clean)
//            ->setDescription('');
//
//            $this->em->persist( $brand );
//            $this->em->flush();
        }
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
