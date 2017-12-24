<?php

namespace classes;

use classes\core\clsDB;

class clsClient
{

    static private $instance = NULL;
    private $db = NULL;

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new clsClient();
        }
        return self::$instance;
    }

    /**
     * Constructor for clsClient class
     *
     */
    public function clsClient()
    {
        $this->db = clsDB::getInstance();
    }

    public function createClient($uid, $name, $surname, $phone, $subPhone, $fullname = '', $email = '')
    {

        $errors = array();
//        $validation = new clsValidation();
//        $validName = clsValidation::stringValidation($name);
//        die(var_dump($validation));
//        if($validName === false) {
//            $errors[] = "Неверно введено имя";
//        }
//        $validSurname = clsValidation::stringValidation($surname);
//        if($validSurname === false) {
//            $errors[] = "Неверно введена фамилия";
//        }
//        $validPhone = clsValidation::stringValidation($phone);
//        if($validPhone === false) {
//            $errors[] = "Неверно введен телефон";
//        }
//        $validEmail = clsValidation::stringValidation($email);
//        if($validEmail === false) {
//            $errors[] = "Неверно введен email";
//        }
//        if(count($errors) == 0) {
//           
//            $sql = "INSERT INTO clients(user_id, first_name, last_name, phone, sub_phone) VALUES (?, ?, ?, ?, ?)";
//            $sqlArr = array($validName, $validSurname, $validPhone, $validEmail, md5($pass));
//            $res = $this->db->Execute($sql, $sqlArr);
//            
//            if(!$res)
//                $errors[] = "Ошибка сохранения пользователя"; // TODO запихнуть сюда SystemError
//        }
        if (empty($errors)) {

            $sql = "INSERT
                INTO clients(user_id, first_name, last_name, phone, sub_phone, full_name, email)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sqlArr = array($uid, $name, $surname, $phone, $subPhone, $fullname, $email);
            $res = $this->db->Execute($sql, $sqlArr);

            if (!$res)
                $errors[] = "Ошибка сохранения клиента"; // TODO запихнуть сюда SystemError
        }

        if (count($errors) > 0) {
            return array('errors' => $errors);
        } else {
            return array('result' => true, 'id' => $this->db->Insert_ID());
        }
    }

    public function updateClientByOrderId($params = array())
    {
        $result = false;

        $id = (int) $params['order_id'];
        unset($params['order_id']);

        $sql = "UPDATE clients c JOIN orders o ON c.id = o.client_id SET ";
        foreach ($params as $k => $v) {
            $sql .= ('c.' . $k . " = " . $v . " ");
        }

        $sql .= " WHERE o.id = ?";

        $sqlArr = array($id);
        $res = $this->db->Execute($sql, $sqlArr);

        if (isset($res)) {
            $result = true;
        }

        return $result;
    }

}