<?php
namespace pages;

use classes\core\clsCommon;
use classes\core\clsPage;
use classes\clsNews;

class News extends clsPage
{

    public function __construct()
    {
        parent::clsPage();
        $this->parser->scripts = $this->scriptsManager->getHTML();
        $this->parser->styles = $this->stylesManager->getHTML();
    }

    /**
     * Get rendered news page
     * 
     * @return string
     */
    protected function getContent()
    {
        //get incoming params
        $page = clsCommon::isInt($this->get('page'));
        $page = empty($page) ? 1 : $page;
        $month = $this->get('date'); 
        $month = empty($month) ? '' : $month; 

        //get news using params
        $news = clsNews::getInstance()->fetchAll($page, $month);
        if (!empty($news)) {
            $newsCount = clsNews::getInstance()->getNewsCount($month);
            $newsDates = clsNews::getInstance()->getNewsMothtes();
            if(!empty($newsDates)) {
                foreach($newsDates as $k => $v) {
                    if($v['created'] == $month) {
                       $newsDates[$k]['active'] = true;
                    }
                    $newsDates[$k]['dateRendered'] = clsCommon::prepareMonthes($v['created']);
                }
            }
            
            $this->parser->news = $news;
            $this->parser->newsCount = $newsCount;
            $this->parser->newsDates = $newsDates;
            $this->parser->page = $page;
            $this->parser->limit = NEWS_LIMIT;
            $this->parser->pagesCount = ceil($newsCount / NEWS_LIMIT);
            $this->parser->pageUrl = clsCommon::compileDefaultItemHref('News');
            return $this->parser->render('@main/pages/news/news.html');
        } else {
            clsCommon::redirect404();
        }
    }
}
