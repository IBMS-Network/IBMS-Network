<?php
namespace engine;

class clsSysValidation {
    
    private $rules = array('email', 'name');
    
    public function clsSysValidation() {
    
    }
    
    public function validate($field = '', $validate = '') {
        //        if (true == $options['required']){
        //            $field = self::requiredValidation($field);
        //            unset($options['required']);
        //        }
        
        if ($field && !empty($validate)) {
            switch ($validate) {
                case 'email' :
                    $field = self::emailValidation($field);
                    break;
                case 'name' :
                    $field = self::nameValidation($field);
                    break;
                case 'phone' :
                    $field = self::phoneValidation($field);
                    break;
                case 'password' :
                    $field = self::passwordValidation($field);
                    break;
            }
        }
        
        return $field;
    
    }
    
    /**
     * Check and prepare email value
     * 
     * @param string $param
     * param name
     * 
     * @return string
     */
    public static function emailValidation($string) {
        $return = false;
        if (preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $string)){
            $return = self::_clearField($string);
        }
        
        return $return;
    }
    
    /**
     * Check and prepare name value
     * 
     * @param string $param
     * param name
     * 
     * @return string
     */
    public static function nameValidation($string) {
        $return = false;
        if (preg_match("/^[- '`a-zA-Zа-яА-Я]*$/ui", $string)) {
            $return = self::_clearField($string);
        }
        return $return;
    }
    
    /**
     * Check and prepare phone number parameter value
     * 
     * @param string $param
     * param name
     * 
     * @return string
     */
    public static function phoneValidation($string) {
        $return = false;
        if (preg_match("/^\+?\d{7,12}$/", $string)) {
            $return = self::_clearField($string);
        }
        
        return $return;
    }
    
    /**
     * Check password string value
     * 
     * @param string $param
     * param name
     * 
     * @return string
     */
    public static function passwordValidation($string) {
        $return = false;
        $lenString = mb_strlen($string);
        if ($lenString >= MIN_PASS_LENGTH && $lenString <= MAX_PASS_LENGTH) {
            $return = $string;
        }
        
        return $return;
    }
    
    public static function requiredValidation($value) {
        $return = false;
        if (!empty($value)) {
            $return = $value;
        }
        
        return $return;
    }
    
    public static function _clearField($string = '') {
        if (!empty($string)) {
            $string = stripslashes($string);
            $string = htmlspecialchars($string);
            $string = strip_tags($string);
        }
        
        return $string;
    }
}