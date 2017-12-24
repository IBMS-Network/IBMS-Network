<?php

namespace engine;

use classes\clsSession;

/**
 * Class of system errors
 *
 * @author Anatoly.Bogdanov
 *
 */
class clsSysError
{

    /**
     * Self Object
     * @var object
     */
    protected static $instance = NULL;

    /**
     * Array of system Errors
     * @var array
     */
    protected $errors = array();

    /**
     * Array of system Warnings|Messages
     * @var array
     */
    protected $messages = array();

    /**
     * name of the system session errors
     * @var string
     */
    private $errorSessText = 'SystemErrors';

    /**
     * name of the system session messages
     * @var string
     */
    private $messageSessText = 'SystemMessages';

    public function __construct()
    {
        $this->_getDataFromSession();
    }

    /**
     * Singleton
     * @return NULL|Object of clsSysError
     */
    public static function getInstance()
    {

        if (self::$instance == NULL) {
            self::$instance = new clsSysError();
        }
        return self::$instance;
    }

    /**
     * setError function set error on page
     *
     * @param mixed $error  - text of the error or num of the error
     * @param integer $type - type of the error
     * 1 - show entered text of an error(default)
     * 2 - show text of an error from entered num of an error
     * @param boolean $is_message
     * true - this is message, false - it's error
     * @param boolean $is_server
     * true - save in SESSION, false - not save
     * @return true on success
     */
    public function setError($error, $type = 1, $is_message = false, $is_server = false)
    {
        if ($type == 1) {
            $err = $error;
        } elseif ($type == 2) {
            $err = $this->getErrorMessage($error) !== false ? $this->getErrorMessage($error) : "";
        }
        if (!empty($err)) {
            if ($is_message) {
                if (!in_array($err, $this->messages)) {
                    $this->messages[] = $err;
                }
            } else {
                if (!in_array($err, $this->errors)) {
                    $this->errors[] = $err;
                }
            }
        }

        // save to session if need
        if ($is_server) {
            $obj_sess = clsSession::getInstance();
            $sess_text = $is_message ? $this->messageSessText : $this->errorSessText;
            $data = array();

            if ($obj_sess->isParamSet($sess_text)) {
                $data = $obj_sess->getParam($sess_text);
            }
            $data[] = $err;
            $obj_sess->setParam($sess_text, $data);
        }
        return true;
    }

    /**
     * Has errors or not
     * @return boolean
     */
    public function isErrors()
    {
        return count($this->errors) ? true : false;
    }

    /**
     * Has messages or not
     * @return boolean
     */
    public function isMessages()
    {
        return count($this->messages) ? true : false;
    }

    /**
     * Return all errors|messages
     * @param boolean $is_message
     * true - messages, false - errors
     * @return array
     */
    public function getError($is_message = false)
    {
        return ($is_message) ? $this->messages : $this->errors;
    }

    /**
     * Clear errors|messages
     * @param boolean $is_server
     * true - delete from session, false - only from this object
     */
    public function clearError($is_server = false)
    {
        $this->errors = NULL;
        $this->messages = NULL;
        if ($is_server) {
            $obj_sess = clsSession::getInstance();
            // errors
            if ($obj_sess->isParamSet($this->errorSessText)) {
                $obj_sess->clearParam($this->errorSessText);
            }

            // messages
            if ($obj_sess->isParamSet($this->messageSessText)) {
                $obj_sess->clearParam($this->messageSessText);
            }
        }
    }

    /**
     * Get text of an error by number from DOMAIN_PATH . 'dictionary/errtext.php'
     * @param int $err_num
     * @return mixed|boolean
     */
    public function getErrorMessage($err_num)
    {
        $var = "ERROR_MESSAGE_" . $err_num;
        if (defined($var)) {
            return constant($var);
        } else {
            return false;
        }
    }

    /**
     * Pull data(errors|messages) from session.
     */
    private function _getDataFromSession()
    {
        $obj_sess = clsSession::getInstance();

        // errors
        if ($obj_sess->isParamSet($this->errorSessText)) {
            $this->errors = $obj_sess->getParam($this->errorSessText);
            $obj_sess->clearParam($this->errorSessText);
        }

        // messages
        if ($obj_sess->isParamSet($this->messageSessText)) {
            $this->messages = $obj_sess->getParam($this->messageSessText);
            $obj_sess->clearParam($this->messageSessText);
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->clearError();
    }

}
