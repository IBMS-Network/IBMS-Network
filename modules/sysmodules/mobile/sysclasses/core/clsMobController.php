<?php

namespace engine\modules\mobile;

use classes\core\clsCommon;

use engine\clsSysContent;
use engine\clsSysStorage;
use engine\clsSysCommon;
use engine\clsSysValidation;
use clsSysHttpStatus;
use classes\clsUser;
use engine\modules\mobile\clsMobCommon;

class clsMobController extends clsSysContent
{

    /**
     * variable for error mesage
     * @var $session object of clsSysSession
     */
    protected $session = NULL;

    /**
     * variable for default output format
     * @var array
     */
//    protected $output = array('format' => 'xml', 'result' => NULL, 'error' => NULL);
    protected $output = array('result' => NULL);
    protected $format = 'xml';

    /**
     * variable for init service
     * @var object
     */
    protected $service = NULL;
    protected $requestMethod = NULL;
    protected $imgGarbageId = NULL;

    public $httpStatusCode = NULL;

    /**
     * Constructor of clsMobCore class
     */
    public function __construct()
    {
        parent::__construct();

        $this->session = clsSysStorage::getInstance()->initStorage();
    }

    /**
     * Enter description here ...
     * @return string
     */
    public function actionIndex()
    {
        return __METHOD__;
    }

    /**
     * Enter description here ...
     * @return string
     */
    public function actionAdd()
    {
        return __METHOD__;
    }

    /**
     * Enter description here ...
     * @return array
     */
    public function actionView()
    {
        $return = array();
        $itemId = $this->_isRequestId();
        if (!empty($itemId)) {
            $user = clsUser::getInstance();
            $return = $user->getUserById($itemId);
        }

        return $return;
    }

    /**
     * Enter description here ...
     * @return string
     */
    public function actionEdit()
    {
        return __METHOD__;
    }

    public function actionDelete()
    {
        return __METHOD__;
    }

    /**
     * Function to get response of this page
     *
     * @return json|xml of current page
     */
    public function showContent()
    {

        $this->setRequestFormat();

        // get errors
        $errors = $this->error->getError();
        if (!empty($errors)){
            $this->output['error'] = $errors;
        }

        // prepare data for output
        $content = $this->prepareFormat();

        return $content;
    }

    /**
     * Prepare data for response in need format
     *
     * @return json|xml of current page
     */
    protected function prepareFormat()
    {
        switch ($this->getRequestFormat()) {
            case 'json' :
                $content = $this->jsonResponse();
                break;
            case 'xml' :
            default :
                $content = $this->xmlResponse();
                break;
        }

        return $content;
    }

    /**
     * Set format name for output data
     * 	by request or headers params
     */
    public function setRequestFormat()
    {
        if (!empty($this->_request['format'])) {
            $this->format = $this->_request['format'];
        }elseif (!empty($_SERVER["CONTENT_TYPE"])){
            switch ($_SERVER["CONTENT_TYPE"]) {
                case 'application/json':
                    $this->format = 'json';
                    break;
                case 'application/xml':
                default:
                    $this->format = 'xml';
                    break;
            }
        }
    }

    /**
     * Get format name for output data
     *
     * @return string
     */
    public function getRequestFormat()
    {
        return $this->format;
    }

    /**
     * Set request method
     */
    public function setRequestMethod()
    {
        $this->requestMethod = strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * Set request params by requst method
     *
     * @param string $requestMethod
     */
    public function setRequestParams($requestMethod)
    {
        $request = array();
        switch ($requestMethod) {
            case 'get' :
                $request = $_GET;
                $this->setRequestFiles();
                break;
            case 'post' :
                $request = array_merge($_GET, $_POST);
                $this->setRequestFiles();
                break;
            case 'put' :
                $this->setRequestFiles();
            case 'delete' :
                $putdata = file_get_contents('php://input');
                parse_str($putdata, $request);
                break;
            default :
        }
        $this->_request = array_merge($this->_request, $request);
    }

    /**
     * Get list request params
     *
     * @return array
     */
    public function getRequestParams()
    {
        return $this->_request;
    }

    /**
     * Get request param by name
     *
     * @param string $requestName
     *
     * @return string
     */
    public function getRequestParam($requestName = '')
    {
        return isset($this->_request[$requestName]) ? $this->_request[$requestName] : '';
    }

    /**
     * Put image to garbage for next use
     *
     * @return boolean
     */
    public function setRequestFiles()
    {
        if (!empty($_FILES['files'])) {
            $i = new clsI();
            $res = $i->putToPlace($_FILES['files']);
            if ($res) {
                $this->imgGarbageId = $res[0];
                $i->setImageToGarbageById($res[0]);
            } else {
                $this->error->setError('image_save_error');
            }
        }
        return true;
    }

    /**
     * Response HTTP Status
     *
     * @return string
     */
    public function httpStatusResponse()
    {
        if ($this->httpStatusCode){
            header("HTTP/1.1 " . $this->httpStatusCode . " " . clsSysHttpStatus::getStatusTextByCode($this->httpStatusCode));
        }
    }

    /**
     * Prepare data for response in JSON format
     *
     * @return json
     */
    protected function jsonResponse()
    {
        header('Content-Type: application/json; charset=utf-8');

        // set return output
        return json_encode($this->output);
    }

    /**
     * Prepare data for response XML format
     *
     * @return xml
     */
    protected function xmlResponse()
    {
        header('Content-Type: application/xml; charset=utf-8');

        // set return output
        $result = '';
        if (!empty($this->output['result']) && is_array($this->output['result'])) {

            foreach ($this->output['result'] as $node => $item) {
                if (is_array($item)) {
                    $subResult = '';
                    foreach ($item as $subNode => $subItem) {
                        $subResult .= $this->_getXmlNode($subNode, $subItem);
                    }
                    $node = 'item';
                    $item = $subResult;
                    $result .= $this->_getXmlNode($node, $item, false);
                } else {
                    $result .= $this->_getXmlNode($node, $item);
                }
            }
        }else{
            $result = clsMobCommon::getMobMessage('mob_empty_results');
        }

        // set error output
        $outputError = '';
        if (!empty($this->output['error']) && is_array($this->output['error'])) {
            foreach ($this->output['error'] as $node => $item) {
                $outputError .= $this->_getXmlNode('error_' . $node, $item);
            }
        }
        if (!empty($outputError)){
            $outputError = '<errors>' . $outputError . '</errors>';
        }

        $outputResult = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<output>
<result>{$result}</result>
{$outputError}
</output>
EOF;
        return $outputResult;
    }

    /**
     * Generate node for use in XML tree
     *
     * @param string $name
     * @param string $value
     * @param bool $clear
     *
     * @return string
     * xml node
     */
    private function _getXmlNode($name, $value = "", $clear = true)
    {
        $node = '';
        if (!empty($name)) {
            $name = htmlspecialchars($name);
            $value = ($clear) ? htmlspecialchars($value) : $value;
            $node = <<< EOF
<{$name}>{$value}</{$name}>
EOF;
        }
        return $node;
    }

    /**
     * Check and prepare need value
     *
     * @return number
     */
    protected function _isRequestId()
    {
        $id = 0;
        if (!empty($this->_request) && isset($this->_request['id']) && clsSysCommon::isInt($this->_request['id'])) {
            $id = (int) $this->_request['id'];
        } else {
            $this->httpStatusCode =  HTTP_STATUS_BAD_REQUEST;
            $this->error->setError('item_id_empty');
        }
        return $id;
    }

    /**
     * Check and prepare to Int parameter value
     *
     * @param string $param
     * param name
     * @param bool $required
     * is required field
     *
     * @return number
     */
    protected function _isRequestInt($param = '', $required = true)
    {
        $value = 0;
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param]) && clsSysCommon::isInt($this->_request[$param])) {
            $value = (int) $this->_request[$param];
        } elseif ($required) {
            $this->httpStatusCode =  HTTP_STATUS_BAD_REQUEST;
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return $value;
    }

    /**
     * Check and prepare string parameter value
     *
     * @param string $param
     * param name
     * @param bool $required
     * is required field
     * @param bool $validate
     * name validation rule
     *
     * @return string
     */
    protected function _isRequestString($param = '', $required = true, $validate = '')
    {
        $value = '';

        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param])) {
            $value = trim($this->_request[$param]);

            $value = clsSysValidation::validate($value, $validate);
            if (!$value) {
                $this->httpStatusCode =  HTTP_STATUS_BAD_REQUEST;
                $this->error->setError(sprintf('value_%s_no_valid', $param));
            }
        } elseif ($required) {
            $this->httpStatusCode =  HTTP_STATUS_BAD_REQUEST;
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return $value;
    }

}