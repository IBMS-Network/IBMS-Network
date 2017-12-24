<?php

namespace classes;

use classes\clsUser;

class clsValidation
{

    private $objUser = "";

    public function clsValidation()
    {
        
    }

    /**
     * E-mail validation and clear, 
     * 	return: is valid - clear email; else - false
     * 
     * @param string $email
     * 
     * @return string|boolean
     */
    public static function emailValidation($email)
    {
        $return = false;

        if (preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) {

            // @TODO: what?
            $email = stripslashes($email);
            $email = htmlspecialchars($email);
            $email = strip_tags($email);

            $return = $email;
        }

        return $return;
    }

    /**
     * Access token validation, 
     * 	return: is valid - string; else - false
     * 
     * @param string $token
     * 
     * @return string|boolean
     */
    public static function accessTokenValidation($token)
    {
        $return = false;

        if (preg_match("|^[-0-9a-z]{32,40}$|i", $token)) {
            $return = $token;
        }

        return $return;
    }

    public static function uniqueEmailValidation($email)
    {
        if (clsUser::getInstance()->isUniqueEmail($email)) {
            return $email;
        }

        return false;
    }

    public static function nameStringValidation($string)
    {
        if (preg_match("/^[- '`a-zA-Zа-яА-Я]*$/ui", $string)) {
            $string = stripslashes($string);
            $string = htmlspecialchars($string);
            $string = strip_tags($string);

            return $string;
        }

        return false;
    }

    public static function phoneValidation($string)
    {
        if (preg_match("/^\+?\d{7,12}$/", $string)) {
            $string = stripslashes($string);
            $string = htmlspecialchars($string);
            $string = strip_tags($string);

            return $string;
        }

        return false;
    }
    
    public static function phoneExtValidation($string)
    {
        if (preg_match("/^\+[0-9]{1,2}\s?\([0-9]{3}\)\s?[0-9]+\-[0-9]+\-[0-9]+$/", $string)) {
            return $string;
        }

        return false;
    }

    public static function stringValidation($string)
    {
        //        if (is_string($string) && $string != "") {
        //            $string = stripslashes($string);
        //            $string = html_entity_decode($string);
        //            $string = strip_tags($string);
        //            
        //            return $string;
        //        }


        return false;
    }

    public static function requiredValidation($value)
    {
        if (!empty($value)) {
            return $value;
        }

        return false;
    }

}