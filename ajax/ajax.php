<?php
ini_set("display_errors",1);
error_reporting(E_ALL);

session_start();

$loader = require_once __DIR__ . '/../vendor/autoload.php';

require_once (__DIR__ . "/../config/config.php");
$modules_names = array('general');
require_once (ENGINE_PATH . "sys_index.php");

use classes\clsValidation;
use classes\clsWebAuthorisation;
use classes\clsCart;
use classes\clsUser;
use classes\core\clsCommon;
use engine\modules\catalog\clsProducts;
use classes\clsProductsHits;
use classes\clsProductsNew;
use classes\clsEmailTemps;
use classes\clsEmail;

define( 'STATUS_USER_ALREADY_EXISTS', -1 );
define( 'STATUS_USER_DATA_INVALID', -2 );

$cart = clsCart::getInstance();
$user = clsUser::getInstance();
$return = array('result' => false);


if(!empty($_POST['method'])) {
    switch ($_POST['method']) {
        case "addToCart":
            echo json_encode($cart->addToCart($_POST['params']));
            break;
        case "register":
            
            $errors = array();
            if(!clsValidation::requiredValidation($_POST['params']['userName'])) {
                $errors[] = clsCommon::getMessage('error_first_name_short', 'ErrorsValidation');
            }
            if(!clsValidation::requiredValidation($_POST['params']['secondName'])) {
                $errors[] = clsCommon::getMessage('error_last_name_short', 'ErrorsValidation');
            }
            if(!clsValidation::requiredValidation(clsValidation::emailValidation($_POST['params']['email']))) {
                $errors[] = clsCommon::getMessage('error_email_not_used', 'ErrorsValidation');
            }
            if(!($password = clsValidation::requiredValidation($_POST['params']['password']))) {
                $errors[] = clsCommon::getMessage('error_password_short', 'ErrorsValidation');
            } elseif($_POST['params']['password'] != $_POST['params']['passwordRepeat']) {
                $errors[] = clsCommon::getMessage('error_password_not_match', 'ErrorsValidation');
            }
            if(empty($errors)) {
                $userId = $user->createUser($_POST['params']['userName'], $_POST['params']['secondName'], $_POST['params']['email'], $_POST['params']['password'], new \DateTime(), 1);
                
                switch ($userId) {
                    case $user::STATUS_USER_ALREADY_EXISTS:
                        $errors[] = clsCommon::getMessage('error_user_already_exists', 'ErrorsCreateUser');
                        break;
                    case $user::STATUS_USER_DATA_INVALID:
                        $errors[] = clsCommon::getMessage('error_unknown', 'ErrorsCreateUser');
                        break;
                    default:
                        $template = clsEmailTemps::getInstance()->getEmailtempById(REGISTER_MAIL_ID);
                        if(!empty($template)) {
                            $params = array(
                                        '{{SITE_URL}}' => SERVER_URL_NAME,
                                        '{{USERNAME}}' => $_POST['params']['userName'] . ' ' . $_POST['params']['secondName'],
                            );
                            clsEmail::getInstance()->send($template->getSubject(), $_POST['params']['email'], $template->getValue(), $params);
                        } else {
                            $result['errors'] = clsCommon::getMessage('system_error', 'Errors');
                        }
                        break;
                }
            }
            
            if(empty($errors)) {
                clsWebAuthorisation::getInstance()->login($_POST['params']['email'], $_POST['params']['password']);
                
                $result['result'] = true;
            } else {
                $result['errors'] = $errors;
            }
            
            echo json_encode($result);
            
            break;
            
        case "login":
            
            $errors = array();
            if(!clsValidation::requiredValidation($_POST['params']['userName'])) {
                $errors[] = clsCommon::getMessage('error_email_short', 'ErrorsValidation');
            }
            if(!($password = clsValidation::requiredValidation($_POST['params']['password']))) {
                $errors[] = clsCommon::getMessage('error_password_short', 'ErrorsValidation');
            }
            
            if(empty($errors)) {
                $login = clsWebAuthorisation::getInstance()->login($_POST['params']['userName'], $_POST['params']['password'], $_POST['params']['rememberMe']);
                if(!empty($login['result'])){
                    $result = $login;
                } else {
                    $errors[] = clsCommon::getMessage('error_not_match', 'ErrorsLogin');
                }
            }
            
            $result['errors'] = $errors;
            
            echo json_encode($result);
            
            break;
        
       case "logout":
            
            clsWebAuthorisation::getInstance()->logout();
            $result['result'] = true;
            
            echo json_encode($result);
            
            break;
        
       case "getProducts":
            $page = 1;
            $limit = MAIN_BLOCKS_PRODUCTS_DEFAULT_LIMIT;
            $offset = 0;
            if($_POST['limit'] && $_POST['offset']) {
                $limit = clsCommon::isInt($_POST['limit']);
                $offset = clsCommon::isInt($_POST['offset']);
            } else {
                $page = clsCommon::isInt($_POST['page']);
                $page = !empty($page) && $page > 0 ? $page : 1;
                $offset = ($page-1) * $limit;
            }
           
            $aviableTypes = array('hits', 'new', 'share');
            if($_POST['type'] && in_array($_POST['type'], $aviableTypes)) {
                switch($_POST['type']) {
                    case 'hits':
                        $products = clsProductsHits::getInstance()->getProductsForMain($limit, $offset);
                        $productsCount = clsProductsHits::getInstance()->getProductsCount();
                        break;
                    case 'new':
                        $products = clsProductsNew::getInstance()->getProductsForMain($limit, $offset);
                        $productsCount = clsProductsNew::getInstance()->getProductsCount();
                        break;
                    case 'share':
                        $products = clsProducts::getInstance()->getProductsForDiscountBlock($limit, $offset);
                        $productsCount = clsProducts::getInstance()->getProductsCountForDiscountBlock();
                        break;
                    default:
                        $products = array();
                        break;
                }
            }
           
            $productsArr = array();
            if(!empty($products)) {
                foreach($products as $v) {
                    $productsArr[] = $v->getArrayCopy();
                }
            }
            
            $result['products'] = $productsArr;
            $result['need_more'] = ($productsCount > $offset);
           
            $result['result'] = true;
            
            echo json_encode($result);
            
            break;

        case "recall":
            $errors = array();
            
            if(!clsValidation::requiredValidation($_POST['params']['userName'])) {
                $errors[] = clsCommon::getMessage('error_first_name_short', 'ErrorsValidation');
            } elseif(!clsValidation::nameStringValidation($_POST['params']['userName'])) {
                $errors[] = clsCommon::getMessage('error_incorrect_name',
                        'ErrorsValidation',
                        array('?'),
                        array(clsCommon::getMessage('field_name', 'CallbackFieldsNames')));
            }
            if(!clsValidation::requiredValidation($_POST['params']['userPhone'])) {
                $errors[] = clsCommon::getMessage('error_incorrect_name',
                        'ErrorsValidation',
                        array('?'),
                        array(clsCommon::getMessage('field_phone', 'CallbackFieldsNames')));
            } elseif(!clsValidation::phoneExtValidation($_POST['params']['userPhone'])) {
                $errors[] = clsCommon::getMessage('error_phone',
                        'ErrorsValidation',
                        array('?'),
                        array(clsCommon::getMessage('field_phone', 'CallbackFieldsNames')));
            }
            
            if(empty($errors)) {
                $template = clsEmailTemps::getInstance()->getEmailtempById(CALL_BACK_MAIL_ID);
                if(!empty($template)) {
                    $params = array(
                                '{{USER}}' => $_POST['params']['userName'],
                                '{{PHONE}}' => $_POST['params']['userPhone'],
                    );
                    clsEmail::getInstance()->send($template->getSubject(), $template->getEmail(), $template->getValue(), $params);

                    $result['message'] = clsCommon::getMessage('callback_sent', 'Feedback');
                    $result['result'] = true;
                } else {
                    $result['errors'] = clsCommon::getMessage('system_error', 'Errors');
                }
            } else {
                $result['errors'] = $errors;
            }
            
            echo json_encode($result);
            
            break;
        default:
            break;
    }
} else {
    return false;
}