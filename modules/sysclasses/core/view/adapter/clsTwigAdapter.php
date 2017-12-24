<?php

namespace engine\view\adapter;

/**
 * This class is Twig adpter
 * @author Sergey.Khaletsky
 */
class clsTwigAdapter extends clsAbstractAdapter
{

    /**
     * The Twig Environment object.
     *
     * @var object
     */
    protected $environment = null;

    /**
     * Constructor for this adapter - sets relevant default configurations for Twig to be used
     * when instantiating a new Twig_Environment and Twig_Loader_Filesystem.
     *
     * @param array options Optional configuration directives.
     *        Please see http://twig.sensiolabs.org/doc/api.html#environment-options for all
     *        available configuration keys and their description.
     * @return void
     */
    public function __construct(array $options = array())
    {
        \Twig_Autoloader::register();
        return $this->createEnvironment('filesystem', $options);
    }

    /**
     * Renders a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function render($template, $context = array())
    {
        $this->setVars($context);
        return $this->environment->render($template, $this->getVars());
    }

    /**
     * Method to create Twig environment of certain type
     * @param string $type Available types: array, filesystem, string
     * @return \Twig_Environment
     */
    public function createEnvironment($type, $options)
    {
        $this->environment = new \Twig_Environment(null, $options);
        $this->environment->addExtension(new \Twig_Extension_Debug());
        $this->setLoader($type);
        return $this->environment;
    }

    public function setLoader($type)
    {
        if (!$this->environment) {
            $search = array('{__field_name__}');
            $repl = array(__CLASS__ . '::' . $name);
            $error_message = clsSysCommon::getMessage('call_undefined_func', 'Errors', $search, $repl);
            throw new \Exception($error_message);
            /**
             * @todo for the clients error
             */
        }

        $this->environment->setLoader($this->getLoaderInstance($type));
    }

    /**
     * Method to get instance of Twig loader by type
     * @param string $type Loader Type
     * @return \Twig_LoaderInterface
     * @throws Exception
     */
    public function getLoaderInstance($type)
    {
        $type = strtolower($type);
        $loader = null;
        switch ($type) {
            case 'array' :
                $loader = new \Twig_Loader_Array();
                break;
            case 'string' :
                $loader = new \Twig_Loader_String();
                break;
            case 'filesystem' :
                $loader = new \Twig_Loader_Filesystem();
                /**
                 * @todo change it on config value
                 */
                $loader->addPath(PARSER_TEMPLATES_PATH, 'main', true);
                break;
            default :
                if (clsSysCommon::getCommonDebug()) {
                    $search = array('{__field_name__}');
                    $repl = array(__CLASS__ . '::' . $name);
                    $error_message = clsSysCommon::getMessage('call_undefined_func', 'Errors', $search, $repl);
                    throw new \Exception($error_message);
                }
            /**
             * @todo for the clients error
             */
        }
        return $loader;
    }

    /**
     * Method to set path to directory with templates
     * @param string $path
     * @param string $alias
     * @param bool $prepend Do prepend path in the main stack
     * @return void
     * @throws Exception
     */
    public function addPath($path, $alias = '', $prepend = false)
    {
        if (!file_exists($path)) {
            if (clsSysCommon::getCommonDebug()) {
                $search = array('{__field_name__}');
                $repl = array(__CLASS__ . '::' . $name);
                $error_message = clsSysCommon::getMessage('call_undefined_func', 'Errors', $search, $repl);
                throw new \Exception($error_message);
            }
            /**
             * @todo for the clients error
             */
        }
        $loader = $this->environment->getLoader();
        if ($prepend) {
            $loader->prependPath($path, $alias);
        } else {
            $loader->addPath($path, $alias);
        }
        $this->environment->setLoader($loader);
    }

}
