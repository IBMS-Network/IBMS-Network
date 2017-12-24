<?php

session_start();
require_once(realpath(dirname(__FILE__) . "/..") . "/config/config.php" );
require_once (ENGINE_PATH . "sys_index.php");
require_once (COMMON_CLS_PATH . "clsCommon.php");
require_once (SERVER_ROOT . "bootstrap.php");


$auth = new clsAuthorisation();

if(!empty($_POST['method'])) {
    switch ($_POST['method']) {
        case "login":
            echo json_encode($auth->login($_POST['email'], $_POST['password']));
            break;
        case "register":
            $dataUser = array('fullName' => $_POST['fullName'], 'phone' => $_POST['phone'],
                        'email' => $_POST['email'], 'password' => $_POST['password']);
            $dataCompany = array('name' => $_POST['name'], 'address' => $_POST['address'], 'city' => $_POST['city'],
                        'OGRN' => $_POST['OGRN'], 'INN' => $_POST['INN'], 'address_fact' => $_POST['address_fact'],
                        'payment_method' => $_POST['payment_method'], 'bank_name' => $_POST['bank_name'],
                        'current_account' => $_POST['current_account'], 'BIK' => $_POST['BIK'], 'status' => 1,
                        'correspondent_account' => $_POST['correspondent_account']);
            $dataAddress = array('address' => $_POST['address2']);
//            $res = clsAuthorisation::getInstance();
//            
//            if (!$res) {
//                $this->parser->setVar("{MESSAGE}", 'Ошибка при создании');
//                //$this->error->setError(clsCommon::getMessage("Cannot change password", "Errors"));
//            } else {
//                $this->parser->setVar("{MESSAGE}", 'Добавление пользователя успешно завершено');
//                clsCommon::redirect302("Location: " . SERVER_URL_NAME . "/profile/");
//                //$this->error->setError(clsCommon::getMessage("Password has been succesfully changed", "Errors"), 1, true);
//            }
//            $data = array('name' => $_POST['name'],
//                'surname' => $_POST['surname'],
//                'phone' => $_POST['phone'],
//                'password' => $_POST['password'],
//                'email' => $_POST['email'],
//                'companyAddress' => $_POST['companyAddress'],
//                'companyName' => $_POST['companyName'],
//                'address' => $_POST['address'] ? $_POST['address'] : "",
//                'requisite' => $_POST['requisite']);
//
//            $params = array();
//            $params['fields'] = array('name', 'surname', 'phone', 'password', 'email',
//                'companyAddress', 'companyName', 'address', 'requisite');
//            $params['names'] = array('Имя', 'Фамилия', 'Телефон', 'Пароль', 'Email',
//                'Адрес фирмы', 'Название фирмы', 'Адрес для доставки', 'Реквизиты фирмы');
//            $params['filters']['required'] = array(0, 2, 3, 4);
//            $params['filters']['uniqueEmail'] = array(4);
//            $params['filters']['email'] = array(4);
//            $params['filters']['nameString'] = array(0, 1);
//            $params['filters']['phone'] = array(2);
            echo json_encode($auth->registerFull($dataUser, $dataCompany, $dataAddress));
            break;
        case "logout":
            echo $auth->logout();
            break;        
        case "loginAdditional":
            $data = array('name' => $_POST['name'],
                'surname' => $_POST['surname'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'networkId' => $_POST['user_id'],
                'network' => $_POST['from']);
            echo $auth->loginAdditional($data);
            break;
        case "checkSocial":
            echo $auth->checkSocial($_POST['id'], $_POST['from']);
            break;
        case 'logoutAdditional':
            echo $auth->logoutAdditional();
            break;
        default:
            break;
    }
} else {
    return false;
}