<?php

namespace engine\view\adapter;

abstract class clsAbstractAdapter
{

    /**
     * @var string tpl file name, if template is from file
     */
    protected $tplName = "";

    /**
     * @var array  Array of placeholders from template
     */
    protected $tplVars = array();

    public function __construct(array $options = array())
    {
        
    }

    /**
     * Magical method to set directly tpl's var value
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        if (!is_string($name)) {
            if (clsSysCommon::getCommonDebug()) {
                $search = array('{__field_name__}');
                $repl = array(__CLASS__ . '::' . $name);
                $error_message = clsSysCommon::getMessage('data_bad_type', 'Errors', $search, $repl);
                throw new \Exception($error_message);
            }
        } else {
            $this->tplVars[$name] = $value;
        }
    }

    /**
     * 
     * @param string $name
     * @return mixed NULL if var doesn't set
     */
    public function __get($name)
    {
        if (isset($this->tplVars[$name])) {
            return $this->tplVars[$name];
        }
        return null;
    }

    /**
     * Method to get all tpl vars
     * @return array
     */
    public function getVars()
    {
        return $this->tplVars;
    }

    /**
     * Method to complex set tpl vars
     * @param array $vars
     */
    public function setVars(array $vars)
    {
        if (!empty($vars)) {
            $this->tplVars = array_merge($this->tplVars, $vars);
        }
    }

    /**
     * Renders a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    abstract public function render($template, $context);
}
