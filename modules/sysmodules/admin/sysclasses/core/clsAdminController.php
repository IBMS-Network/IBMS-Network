<?php

namespace engine\modules\admin;

use classes\clsSession;
use engine\clsSysCommon;

class clsAdminController extends clsAdminPage {
    
    /**
     * variable for error mesage
     * @var $session object of clsSysSession
     */
    protected $session = NULL;
    
    
    /**
     * Constructor of clsMobCore class
     */
    public function __construct(){
        parent::__construct();
        $this->getClsSession();
    }
    
    /**
     * Enter description here ...
     * @return string
     */
    public function actionIndex(){
        return __METHOD__;
    }
    
    /**
     * Enter description here ...
     * @return string
     */
    public function actionAdd(){
        return __METHOD__;
    }
    
    /**
     * Enter description here ...
     * @return array
     */
    public function actionView(){
        $return = array();
        $itemId = $this->_isRequestId();    
        if (!empty($itemId)){
            $user = clsUser::getInstance();
            $return = $user->getUserById($itemId);
        }
        
        return $return;
    }
    
    /**
     * Enter description here ...
     * @return string
     */
    public function actionEdit(){
        return __METHOD__;
    }
    
    public function actionDelete(){
        return __METHOD__;
    }
    
    /**
     * Enter description here ...
     */
    protected function getClsSession(){
        if( clsSysCommon::isProjectOn() ){
            $this->session = clsSession::getInstance();
        }else{
            $this->session = clsSysSession::getInstance();
        }
    }
    
    /**
     * Enter description here ...
     */
    public function getError(){
        $this->error->getError();
    }
    
    /**
     * Check and prepare need value
     * 
     * @return number
     */
    protected function _isRequestId(){
        $id = 0;
        if (!empty($this->_request) && isset($this->_request['id']) && clsSysCommon::isInt($this->_request['id'])){
            $id = (int)$this->_request['id'];
        }else{
            $this->error->setError('item_id_empty');
        }
        return  $id;
    }
    
    /**
     * Check and prepare to Int parameter value
     * 
     * @param string $param
     * 	param name
     * @param bool $required
     * 	is required field
     * 
     * @return number
     */
    protected function _isRequestInt($param = '', $required = true){
        $value = 0;
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param]) && clsSysCommon::isInt($this->_request[$param])){
            $value = (int)$this->_request[$param];
        }elseif ($required){
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return  $value;
    }
    
    /**
     * Check and prepare string parameter value
     * 
     * @param string $param
     * 	param name
     * @param bool $required
     * 	is required field
     * 
     * @return string
     */
    protected function _isRequestString($param = '', $required = true){
        $value = '';
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param])){
            $value = addslashes(trim($this->_request[$param]));
        }elseif ($required){
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return  $value;
    }
    
    /**
     * Check and prepare e-mail parameter value
     * 
     * @param string $param
     * 	param name
     * @param bool $required
     * 	is required field
     * 
     * @return string
     */
    protected function _isRequestStringEmail($param = '', $required = true){
        $value = '';
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param])){
            $value = clsValidation::emailValidation($this->_request[$param]);
            if (!$value){
                $this->error->setError(sprintf('value_%s_no_valid', $param));
            }
        }elseif ($required){
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return  $value;
    }
    
    /**
     * Check and prepare phone number parameter value
     * 
     * @param string $param
     * 	param name
     * @param bool $required
     * 	is required field
     * 
     * @return string
     */
    protected function _isRequestStringPhone($param = '', $required = true){
        $value = '';
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param])){
            $value = clsValidation::phoneValidation($this->_request[$param]);
            if (!$value){
                $this->error->setError(sprintf('value_%s_no_valid', $param));
            }
        }elseif ($required){
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return  $value;
    }
    
    /**
     * Check password parameter value
     * 
     * @param string $param
     * 	param name
     * @param bool $required
     * 	is required field
     * 
     * @return string
     */
    protected function _isRequestStringPassword($param = '', $required = true){
        $value = '';
        if (!empty($this->_request) && !empty($param) && isset($this->_request[$param])){
            $value = clsValidation::requiredValidation($this->_request[$param]);
            if (!$value){
                $this->error->setError(sprintf('value_%s_no_valid', $param));
            }
        }elseif ($required){
            $this->error->setError(sprintf('value_%s_empty', $param));
        }
        return  $value;
    }
}