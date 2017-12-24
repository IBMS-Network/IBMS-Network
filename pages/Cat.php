<?php

namespace pages;

use classes\core\clsPage;
use entities;

class Cat extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
    }

    protected function getContent()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://rezat.ru/catalog/');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');

        $res = curl_exec($curl);
        curl_close($curl);
        header('Content-type: text/html; charset=UTF-8');
        $content = iconv("cp1251", "UTF-8", $res);
        $pattern = "|<li[^>]*>[^<]*<a href='[^']*'>(.*)</a></li>|";
        preg_match_all($pattern, $content, $categories);
        $categories = array_slice($categories[1], 0, 11);

//        $this->em = clsDB::getInstance();
//        foreach ($categories as $cat) {
//            $category = new entities\Category();
//            $category->setName($cat)
//            ->setCreated()
//            ->setUpdated()
//            ->setParentId(0)
//            ->setStatus(1)
//            ->setDescription('');
//
//            $this->em->persist( $category );
//            $this->em->flush();
//        }

        return '';
    }

}
