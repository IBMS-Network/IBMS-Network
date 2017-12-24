<?php

namespace classes;

use classes\core\clsCommon;

class clsEmail {

    static private $instance = NULL;

    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new clsEmail();
        }
        return self::$instance;
    }

    public function sendEmail($subj, $to, $type, $params = array()) {

        $i = 0;

        $headers = "From:" . ucfirst(SERVER_NAME) . "  <support@" . SERVER_NAME . ">\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\n";
        $headers .= "Content-Transfer-Encoding: Quot-Printed\n\n";

        $parser = new clsParser();
        $parser->clear();
        $tpl = array(1 => 'client_to_manager.html', 2 => 'client_to_manager_unregistered.html',
            3 => 'recovery_password.html', 4 => 'user_registration.html', 5 => 'manager_to_client.html');
        foreach ($params as $k => $v) {
            $parser->setVar($k, $v);
            $i++;
        }
        $parser->setEmailTemplate($tpl[$type]);
        $body = $parser->getResult();
        $res = clsCommon::SendEmail($to, $subj, $body, $headers);
        if (!$res) {
            if (USE_DEBUG) {
                echo "Cannot send email";
            } else {
                // +++++ USE DEBUG +++++
                //clsCommon::Log();
            }
        }
        return true;
    }
    
    /**
     * Send email
     * 
     * @param string $subj
     * @param string $to
     * @param string $body
     * @param array $params
     * @return boolean
     */
    public function send($subj, $to, $body, $params = array()) {

        $headers = "From:" . DEFAULT_EMAIL . "\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\n";
        $headers .= "Content-Transfer-Encoding: Quot-Printed\n\n";

        foreach ($params as $k => $v) {
            $subj = str_replace($k, $v, $subj);
            $body = str_replace($k, $v, $body);
        }
        $res = clsCommon::SendEmail($to, $subj, $body, $headers);
        if (!$res) {
            if (USE_DEBUG) {
                echo "Cannot send email";
            } else {
                // +++++ USE DEBUG +++++
                //clsCommon::Log();
            }
        }
        return true;
    }
}