<?php

namespace engine;

abstract class clsSysContent
{

    /**
     * @var \engine\view\adapter\clsTwigAdapter
     */
    protected $parser = "";
    protected $request = array();
    protected $get = array();
    protected $post = array();
    protected $put = array();
    protected $delete = array();
    protected $_request = array();
    protected $config = array();

    /**
     * @var \engine\clsSysError
     */
    public $error = NULL;
    protected $inner = array();

    protected function __construct()
    {
        $this->initClsParser();
        $this->getClsError();
        $this->config = clsSysCommon::getDomainConfig();
    }

    public function setParams($get = array(), $post = array(), $request = array(), $inner = array())
    {
        $this->get = $get;
        $this->post = $post;

        $this->request = $request;
        $this->inner = $inner;

        $this->_request = array_merge($get, $post, $inner);
        if (!empty($this->get["err_num"])) {
            $this->error->setError(clsSysCommon::isInt($this->get["err_num"]), 2);
        }
    }

    protected function show($var)
    {
        $show = "";
        if (is_array($var) || is_object($var)) {
            $this->printArray((array)$var);
        } elseif (!empty($var)) {
            $show = "<div style='border:2px solid #633c04;background-color:#f1dda5;width:300px;height:100;padding: 5px 5px;FONT-SIZE: 7pt; FONT-FAMILY: Verdana;'  >" . $var . "</div>";
            echo $show;
        } else {
            $show = "Empty variable ";
            echo $show;
        }
    }

    public function printArray($a)
    {

        static $count = 0;
        $count = (isset($count)) ? ++$count : 0;
        $colors = array('#FFCB72', '#FFB072', '#FFE972', '#F1FF72', '#92FF69', '#6EF6DA', '#72D9FE', '#77FFFF', '#FF77FF');
        if ($count > count($colors)) {
            $count--;

            return;
        }

        if (!is_array($a)) {
            echo "Passed argument is not an array!<p>";

            return;
        }

        echo "<table border=1 cellpadding=0 cellspacing=0 bgcolor=$colors[$count]>";

        while (list($k, $v) = each($a)) {
            echo "<tr><td style='padding:1em'>$k</td><td style='padding:1em'>$v</td></tr>\n";
            if (is_array($v)) {
                echo "<tr><td> </td><td>";
                self::printArray($v);
                echo "</td></tr>\n";
            }
        }
        echo "</table>";
        $count--;
    }

    /**
     * Return value from the Post params by key
     *
     * @param string $key
     * @return mixed Value by key or null if not exists
     */
    public function post($key = '')
    {
        if (!$key) {
            return $this->post;
        }
        if (!empty($this->post[$key])) {
            return $this->post[$key];
        }

        return null;
    }

    /**
     * Return value from the GET params by key
     *
     * @param string $key
     * @return mixed Value by key or null if not exists
     */
    public function get($key = '')
    {
        if (!$key) {
            return $this->get;
        }
        if (!empty($this->get[$key])) {
            return $this->get[$key];
        }

        return null;
    }

    protected function initClsParser()
    {
        $adapterClassName = '';
        if (constant('PARSER_ADAPTER')) {
            $adapterClassName = 'engine\\view\\adapter\\cls' . ucfirst(PARSER_ADAPTER) . 'Adapter';
        }
        if (class_exists($adapterClassName)) {
            $this->parser = new $adapterClassName();
        } else {
            if (clsSysCommon::isProjectOn()) {
                $this->parser = new classes\core\clsParser;
            } else {
                $this->parser = new engine\clsSysParser();
            }
        }
    }

    protected function getClsError()
    {
        if (clsSysCommon::isProjectOn()) {
            $this->error = \classes\core\clsError::getInstance();
        } else {
            $this->error = engine\clsSysError::getInstance();
        }
    }

    public function __destruct()
    {
        $this->parser = NULL;
        $this->error = NULL;
        $this->config = NULL;
    }

}