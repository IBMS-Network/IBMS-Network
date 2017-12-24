<?php

namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use classes\clsNews;

class Article extends clsPage
{
    public function __construct()
    {
        parent::clsPage();
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered static page
     * 
     * @return string
     */
    public function getContent()
    {
        //get static page info
        $slug = $this->get('slug');
        if ($slug) {
            $newsItem = clsNews::getInstance()->getNewsItemBySlug($slug);
        } elseif ($this->get('id')) {
            $newsItem = clsNews::getInstance()->getNewsItemById($this->get('id'));
        }
        
        //render page
        if ($newsItem) {
            
            //make data for monthes menu
            $newsDates = clsNews::getInstance()->getNewsMothtes();
            if(!empty($newsDates)) {
                foreach($newsDates as $k => $v) {
                    if($v['created'] == ($newsItem->getCreated()->format('m-Y'))) {
                       $newsDates[$k]['active'] = true;
                    }
                    $newsDates[$k]['dateRendered'] = clsCommon::prepareMonthes($v['created']);
                }
            }
            
            //get data for last news block
            $lastNews = clsNews::getInstance()->getLastNews();

            $this->parser->lastNews = $lastNews;
            $this->parser->newsDates = $newsDates;
            $this->parser->title = $newsItem->getName();
            $this->parser->news = $newsItem;
            return $this->parser->render('@main/pages/news/news-item-page.html');
        } else {
            clsCommon::redirect404();
        }
    }
}