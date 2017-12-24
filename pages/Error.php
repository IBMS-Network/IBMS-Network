<?php

namespace pages;

/**
 * Error class perform Error(Warning) page
 *
 */
class Error extends clsPage
{

    /**
     * Constructor for Error class
     *
     */
    public function Error()
    {
        parent::clsPage();
    }

    /**
     * Function for preparing all nessesary js,css,meta-headers,breadcrumb information in HEADER-template for this page
     * This function declared in parent class clsPage. Required for site engine.
     *
     * @return text - header html for this page
     */
    protected function getHeader()
    {
        $result = parent::getHeader();
        return $result;
    }

    /**
     * Function for preparing content information in BODY-template for this page
     * This function declared in parent class clsPage. Required for site engine.
     *
     * @return html body
     */
    protected function getContent()
    {
        $content = "";
        $vars = array();
        $error_msg = $this->error->getErrorMessage($this->getErrorNum());
        $vars = array_merge($vars, $blocks);
        $this->parser->clear();
        $this->parser->setVar("{ERROR_MSG}", $error_msg);
        $this->parser->setVars($vars);
        $this->parser->setTemplate("error.html");
        $content .= $this->parser->getResult();
        return $content;
    }

    /**
     * getErrorNum function present checking for ErrorNum from url
     *
     * @return integer Errornum
     */
    private function getErrorNum()
    {
        if (!empty($this->get["err_num"]) && intval($this->get["err_num"]) > 0 && is_numeric($this->get["err_num"])) {
            $err = $this->get["err_num"];
        } else {
            $err = DEF_ERROR_NUM;
        }
        return $err;
    }

}