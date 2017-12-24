<?php

namespace engine\view\adapter;

#require_once __DIR__ . DIRECTORY_SEPARATOR . '../../clsSysParser.php';

/**
 * This class is Engine renderer adpter
 */
class clsEngineAdapter extends clsAbstractAdapter
{

    /**
     * Instance of parser
     * @var clsSysParser
     */
    public $parser = null;

    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->parser = new clsSysParser;
    }

    public function render($template, $context = array())
    {
        if (!empty($context)) {
            $this->setVars($context);
        }
        $this->parser->setMixedTemplate($template);
        return $this->parser->getResult();
    }

}
