<?php

namespace engine;

use \classes\core\clsCommon;

class clsSysCommon
{

    private static $config = array();
    private static $notes = array();

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $error = "PHP ERROR : " . $errstr . "<br /> FILE : " . $errfile . " LINE : " . $errline;

        switch ($errno) {
            case E_USER_WARNING:
                $type = 2;
                break;

            case E_USER_NOTICE:
                $type = 1;
                break;

            case E_USER_ERROR:
            default:
                $type = 3;
                break;
        }
        ;
        if (USE_ERROR_LOG) {
            $logPath = is_dir(CONFIGSYS_DOMAIN_PATH . 'logs/') ? CONFIGSYS_DOMAIN_PATH . 'logs/' : SYS_LOG_PATH;
            self::Log($error, $type, "php_log.log");
        }
        ;

        return true;
    }

    /**
     * show message on screen in debug mode
     *
     * @param string $message
     * message text to show on
     * @param string $title
     * Label for message block
     * @param boolean $use_debug_mode
     * use or not debug mode feature
     * @param boolean $is_page
     * prepare pages HTML tags for message or not
     * @param array $replace
     * assoc array for message replacement if needs. Must have keys 'search' and 'repl' and 'block'
     * use default project constant to check can we output message or not
     */
    public static function debugMessage($message, $title = "", $use_debug_mode = 0, $is_page = false, $replace = array())
    {

        if (empty($title)) {
            $title = self::getMessage("Error", "ErrorDebug");
        }

        if (empty($message)) {
            $message = self::getMessage("Error", "DefaultText");
        } else {

            // check if message must be get from notes.ini
            $is_repl_good = !empty($replace) && is_array($replace) && !empty($replace['block']);

            if ($is_repl_good) {
                $replace['search'] = empty($replace['search']) ? array() : $replace['search'];
                $replace['repl'] = empty($replace['repl']) ? array() : $replace['repl'];
                $message = self::getMessage($message, $replace['block'], $replace['search'], $replace['repl']);
            }
        }

        if ($use_debug_mode) {
            $use_debug_mode = self::getCommonDebug();
        }

        if ($is_page) {
            echo '<html><head><title>Error page</title>
            <link href="/css/sys.css" rel="stylesheet" type="text/css" />
            </head><body>';
        }

        echo '<div id="errorboxoutline">
            <div id="errorboxheader">' . $title . '</div>
            <div id="errorboxbody"><p><strong>' . $message . '</strong></p></div>
            </div>';

        if ($is_page) {
            echo '</body></html>';
        }
    }

    /**
     * get all config files(project) in object property
     */
    public static function getDomainConfig()
    {

        if (self::$config == array() ) {

            $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'config.ini');
            $project_ini_info = $sys_ini_info;
            $project_ini_info_secure = array('path' => CONF_SYS_PATH, 'name' => 'config_secure.ini');

            if (defined('CONFIG_DOMAIN_PATH')) {
                $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
            }
            self::$config = self::getCommonIniFiles($sys_ini_info, $project_ini_info, true);
            if (!defined('CONFIG_DOMAIN_PATH')) {
                $search = array('{__class_path__}', '{__class_name__}');
                $repl = array($project_ini_info['path'], $project_ini_info['name']);
                $err_mes = self::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
                self::debugMessage($err_mes);
            }

            if (defined('CONFIGSYS_DOMAIN_PATH')) {
                $project_ini_info['path'] = CONFIGSYS_DOMAIN_PATH;
                $project_ini_info_secure['path'] = CONFIGSYS_DOMAIN_PATH;
            }
            $sys_config = self::getCommonIniFiles($sys_ini_info, $project_ini_info, true);
            self::$config = array_merge(self::$config, $sys_config);

            $config_secure = self::getCommonIniFiles($project_ini_info_secure, $project_ini_info_secure, true);
            if(!empty($config_secure) && is_array($config_secure)){
                foreach($config_secure as $section => $sectionData){
                    if(!empty($sectionData) && is_array($sectionData)){
                        foreach($sectionData as $key => $value){
                            self::$config[$section][$key] = $value;
                        }
                    }
                }
            }

            if (!defined('CONFIGSYS_DOMAIN_PATH')) {
                $search = array('{__class_path__}', '{__class_name__}');
                $repl = array($project_ini_info['path'], $project_ini_info['name']);
                $err_mes = self::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
                self::debugMessage($err_mes);
            }
        }

        return self::$config;
    }

    /**
     * Init file with messages for system needs
     * @param string $message
     * key of message in *.ini file or incoming text message
     * @param string $block
     * block-key of message in *.ini file
     * @param array $search
     * array of placeholders
     * @param array $replace
     * array of replacement
     * @return string
     */
    public static function getMessage($message, $block = 'Main', $search = array(), $replace = array())
    {

        if (empty($block)) {
            $block = 'Main';
        }

        if (empty($message)) {
            $message = 'default';
        }

        self::getDictionary();

        // try to find message in message block
        if (isset(self::$notes[$block][$message])) { // find
            $res = self::$notes[$block][$message];
        } else { // not find
            $res = $message;
        }

        // try to init replacement
        $is_replace = !empty($search) && !empty($replace) && is_array($search) && is_array($replace);
        $is_replace = $is_replace && (count($search) == count($replace));
        if ($is_replace) {
            $res = str_replace($search, $replace, $res);

        }
        return (string) $res;
    }

    public static function getDictionary(){
        // init engine note.ini file
        if (self::$notes == array()) {
            $sys_ini_info = array('path' => DICTIONARY_SYS_PATH, 'name' => 'notes.ini');
            $project_ini_info = $sys_ini_info;
            if (defined('DICTIONARY_PATH')) {
                $project_ini_info['path'] = DICTIONARY_PATH;
            }
            self::$notes = self::getCommonIniFiles($sys_ini_info, $project_ini_info, true);
            if (!defined('DICTIONARY_PATH')) {
                $search = array('{__class_path__}', '{__class_name__}');
                $repl = array($project_ini_info['path'], $project_ini_info['name']);
                $err_mes = self::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
                self::debugMessage($err_mes);
            }
        }
    }

    public static function escapeName($src)
    {
        //echo $src."<br />\n";
        $src = trim($src);
        $src = str_replace(array('&#xE4;', '&#xF6;', '&#xFC;', '&#xDF;', '&#225;', // 5
            'å', 'á', 'é', 'í', '&#246;', // 10
            '&#228;', '&#252;', 'Ö', 'ö', '&#196;', // 15
            'é', '…'), array('ae', 'oe', 'ue', 'ss', 'a', // 5
            'a', 'a', 'e', 'i', 'oe', // 10
            'ae', 'ue', 'Oe', 'oe', 'Ae', // 15
            'e', '-'), $src);
        $src = strip_tags($src);
        $src = preg_replace('|(&#x\w+?;)|', '', $src);

        $src = strtr($src, ":/ '\"()[]@#\$%^&*!+?._,\\", "--------------n--------");
        $src = trim($src, "-");
        $src = preg_replace('|-{2,}|', '-', $src);
        if (!$src)
            $src = '_';
        return $src;
    }

    public static function isInt($number)
    {
        return empty($number) || intval($number) < 1 || !is_numeric($number) ? 0 : (int) $number;
    }

    public static function isBigInt($number)
    {
        return empty($number) || !is_numeric($number) ? 0 : $number;
    }

    public function getURLPaging($compileHrefMethod, $pageLimit = DEF_LIST_LIMIT, $count = 0, $filters = array())
    {
        $chain = "";
        if (empty($filters['page']) || self::isInt($filters['page']) < 1)
            $page = 1;
        else
            $page = (integer) $filters['page'];

        if ($count > $pageLimit) {
            if ($page > 1) {
                $prevHref = self::$compileHrefMethod(self::getChangedFilters($filters, array("page" => $page - 1)));
                $chain .= '<a href="' . $prevHref . '" title="Previous"><img src="/images/black_arrow_l.gif" width="8" height="12" alt="arrow" /></a> ';
            }
            $pcnt = $pageCount = intval(ceil($count / $pageLimit));
            $startPage = 1;
            if ($pageCount > DEF_PAGING_NUM) {
                $m = $page % DEF_PAGING_NUM;
                if ($m > 0)
                    $startPage = $page - $m + 1;
                else
                    $startPage = (intval($page / DEF_PAGING_NUM) - 1) * DEF_PAGING_NUM + 1;

                $pcnt = DEF_PAGING_NUM;
                if ($startPage + DEF_PAGING_NUM > $pageCount) {
                    $pcnt = $pageCount % DEF_PAGING_NUM;
                    if ($pcnt == 0)
                        $pcnt = DEF_PAGING_NUM;
                }
            }

            $thislink = ($page % DEF_PAGING_NUM != 0) ? $page % DEF_PAGING_NUM : DEF_PAGING_NUM;
            for ($j = 0; $j < $thislink - 1; $j++) {
                $href = self::$compileHrefMethod(self::getChangedFilters($filters, array("page" => $j + $startPage)));
                $chain .= "<a href=\"" . $href . "\">" . ($j + $startPage) . "</a>";
            }

            $chain .= "<a href='#' class='active'>" . ($j + $startPage) . "</a>";
            for ($j = $thislink; $j < $pcnt; $j++) {
                $href = self::$compileHrefMethod(self::getChangedFilters($filters, array("page" => $j + $startPage)));
                $chain .= "<a href=\"" . $href . "\">" . ($j + $startPage) . "</a>";
            }

            if ($page < $pageCount) {
                $nextHref = self::$compileHrefMethod(self::getChangedFilters($filters, array("page" => $page + 1)));
                $chain .= '<a href="' . $nextHref . '" title="next"><img src="/images/black_arrow_r.gif" width="8" height="12" alt="arrow" /></a> ';
            }
        }

        return $chain;
    }

    /**
     * getPagingNext function build html paging navigation for ajax
     *
     * @param string $name
     * name class
     * @param integer $page
     * current page of listing (default 1)
     * @param integer $pageLimit
     * number of items per page (default depends on domain config)
     * @param integer $count
     * number of all items in listing
     * @param array $filters
     * array of various filters( need to build url for paging html )
     *
     * @return html
     * of paging navigation
     */
    public static function getPagingNext($name = '', $page = 1, $pageLimit = DEF_LIST_LIMIT, $count, $filters = array())
    {
        // default
        $chain = '';

        if (empty($page) || self::isInt($page) < 1) {
            $page = 1;
        }

        $maximumCount = ceil($page * (int) $pageLimit);
        if ((int) $count > (int) $maximumCount) {
            $filters['page'] = ++$page;
            $url = self::compileDefaultListHref($name, $filters);
            $chain .= "<a href='" . $url . "' class='more read-more ajax-show-more'>" . 'Далее >' . "</a>";
        }
        return $chain;
    }

    /**
     * getPagingAjax function generate html block for paging navigation
     *
     * @param integer $page - num of the current page
     * @param integer $pageLimit - number items per page
     * @param integer $count - number of items
     * @param string $anchor - name of the anchor in HTML to redirect
     * @param string $js_function - name of the JS function depends on AJAX paging
     * @param array $filters - filters thats must be in link
     * @return text $chain - html block for paging navigation
     */
    public function getPagingAjax($page = 1, $pageLimit = DEF_LIST_LIMIT, $count, $anchor = "", $js_function = "", $filters = array())
    {
        $chain = "";
        if (empty($page) || self::isInt($page) < 1) {
            $page = 1;
        }

        $anchor = empty($anchor) ? "#" : $anchor;
        $js_function = empty($js_function) ? "javascript:getPagingHTML" : $js_function;
        $filter_link = empty($filters) ? "" : (",'" . join("','", $filters) . "'");

        if ($count > $pageLimit) {
            if ($page > 1) {
                $chain .= '<a href="' . $anchor . '" onclick="' . $js_function . '(' . ($page - 1) . $filter_link . ');" title="Previous"><img src="/images/black_arrow_l.gif" width="8" height="12" alt="arrow" /></a> ';
            }
            $pcnt = $pageCount = intval(ceil($count / $pageLimit));
            $startPage = 1;
            if ($pageCount > DEF_PAGING_NUM) {
                $m = $page % DEF_PAGING_NUM;
                if ($m > 0)
                    $startPage = $page - $m + 1;
                else
                    $startPage = (intval($page / DEF_PAGING_NUM) - 1) * DEF_PAGING_NUM + 1;

                $pcnt = DEF_PAGING_NUM;
                if ($startPage + DEF_PAGING_NUM > $pageCount) {
                    $pcnt = $pageCount % DEF_PAGING_NUM;
                    if ($pcnt == 0)
                        $pcnt = DEF_PAGING_NUM;
                }
            }

            $thislink = ($page % DEF_PAGING_NUM != 0) ? $page % DEF_PAGING_NUM : DEF_PAGING_NUM;
            for ($j = 0; $j < $thislink - 1; $j++) {
                $chain .= "<a href=\"" . $anchor . "\" onclick=\"" . $js_function . '(' . ($j + $startPage) . $filter_link . ");\">" . ($j + $startPage) . "</a>";
            }

            $chain .= "<a href='" . $anchor . "' class='active'>" . ($j + $startPage) . "</a>";
            for ($j = $thislink; $j < $pcnt; $j++) {
                $chain .= "<a href=\"" . $anchor . "\" onclick=\"" . $js_function . '(' . ($j + $startPage) . $filter_link . ");\">" . ($j + $startPage) . "</a>";
            }

            if ($page < $pageCount) {
                $chain .= '<a href="' . $anchor . '" title="next" onclick="' . $js_function . '(' . ($page + 1) . $filter_link . ');"><img src="/images/black_arrow_r.gif" width="8" height="12" alt="arrow" /></a> ';
            }
        }

        return $chain;
    }

    public static function IsValidEmail($email)
    {
        $res = preg_match("|^[\w-._]+@[\w-._]+\.[\w]{2,4}$|i", $email);
        if ($res < 1)
            return false;

        return true;
    }

    public static function IsValidPassword($password)
    {
        $res = preg_match("/(?:\'+|\"+|\\\\+|\/+)/", $password);
        if (is_bool($res) && $res == false)
            return false;

        if ($res > 0)
            return false;

        return true;
    }

    public static function redirect301($location)
    {
        $sname = session_name();
        $sid = session_id();
        if (strlen($sid) < 1) {
            Header("HTTP/1.1 301 Moved Permanently");
            Header($location);
            exit();
        }

        if (isset($_COOKIE[$sname]) || strpos($location, $sname . "=" . $sid) !== false) {
            Header("HTTP/1.1 301 Moved Permanently");
            Header($location);
            exit();
        } else {
            if (strpos($location, "?") > 0)
                $separator = "&";
            else
                $separator = "?";

            $fixed = $location . $separator . $sname . "=" . $sid;
            Header("HTTP/1.1 301 Moved Permanently");
            Header($fixed);
            exit();
        }
    }

    public static function redirect404($location = '')
    {
        if (empty($location)) {
            $location = "Location: " . SERVER_URL_NAME . "/404/";
        }
        $sname = session_name();
        $sid = session_id();
        if (strlen($sid) < 1) {
            Header("HTTP/1.1 404 Not Found");
            Header($location);
            exit();
        }

        if (isset($_COOKIE[$sname]) || strpos($location, $sname . "=" . $sid) !== false) {
            Header("HTTP/1.1 404 Not Found");
            Header($location);
            exit();
        } else {
            if (strpos($location, "?") > 0)
                $separator = "&";
            else
                $separator = "?";

            $fixed = $location . $separator . $sname . "=" . $sid;
            Header("HTTP/1.1 404 Not Found");
            Header($fixed);
            exit();
        }
    }

    public static function redirect302($location)
    {
        $sname = session_name();
        $sid = session_id();
        if (strlen($sid) < 1) {
            Header($location);
            exit();
        }

        if (isset($_COOKIE[$sname]) || strpos($location, $sname . "=" . $sid) !== false) {
            Header($location);
            exit();
        } else {
            if (strpos($location, "?") > 0)
                $separator = "&";
            else
                $separator = "?";

            $fixed = $location . $separator . $sname . "=" . $sid;
            Header($fixed);
            exit();
        }
    }

    public static function checkAuthData($data)
    {
        $len = strlen($data['auth_email']);
        if ($len < 1)
            return NoEmail;

        if (!self::IsValidEmail($data['auth_email']))
            return IncorrectEmail;

        $len = strlen($data['auth_pass']);

        if ($len < 1 || $len < MinPassLength || $len > MaxPassLength)
            return NoPassword;

        if (!self::IsValidPassword($data['auth_pass']))
            return IncorrectPassword;

        return Proceed;
    }

    /**
     * Log function check variable for is array and return empty array if variable empty
     *
     * @param text $text  - text to log it
     * @param integer $type - type of the log message
     * 1 - Notice
     * 2 - Warning
     * 3 - Error
     * @param string $file - file to write log(default to log file from config)
     * @return $result array
     */
    public static function Log($text, $type = 1, $file_name = "")
    {

        if (!$file_name)
            $file_name = "log.log";

        $logPath = $domain_log_path = CONFIGSYS_DOMAIN_PATH . 'logs/';
        clsCommon::CreateDirRec($domain_log_path);
        if (!is_dir($domain_log_path)) {
            $logPath = SYS_LOG_PATH;
            clsCommon::CreateDirRec($logPath);
        }

        $file = $logPath . $file_name;

        $type_text = "Notice";
        switch ($type) {
            case 2:
                $type_text = "Warning";
                break;
            case 3:
                $type_text = "Error";
                break;
            case 1:
            default:
                $type_text = "Notice";
                break;
        }

        $errText = date("d-m-Y H:i:s") . ' ' . $type_text . ': ' . $text . "\r\n";

        $fd = @fopen($file, "ab");
        $result = @fwrite($fd, $errText);
        @fclose($fd);

        if (!empty($result)) {
            if (USE_DEBUG) {
                $objError = clsError::getInstance();

                //$objError->setError(DEF_CANNT_WR_LOG, 2);
            }
        }
    }

    public static function CreateDirRec($dst)
    {
        if (!@file_exists($dst)) {
            $res = @mkdir($dst);
            if (!$res) {
                $stack = array($dst);
                $dir = $dst;
                $cnt = 64;
                while (!$res && $cnt-- > 0) {
                    $root = dirname($dir);
                    if (!@file_exists($root)) {
                        $stack[] = $root;
                        $dir = $root;
                    } else {
                        break;
                    }
                }
                $stackSize = sizeof($stack);
                for ($i = $stackSize - 1; $i >= 0; $i--) {
                    $res = @mkdir($stack[$i]);
                    if (!$res) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * This function check variable for is array and return empty array if variable empty
     *
     * @param $array mixed - checking array
     * @return $result array
     */
    public static function isArray($array)
    {
        return !empty($array) && is_array($array) && count($array) > 0 ? $array : array();
    }

    /**
     * This function send email
     *
     * @param $to string - email to
     * @param $subj string - email subject
     * @param $body text - email body
     * @param $headers string - email headers
     * @return true|false
     */
    public static function SendEmail($to, $subj, $body, $headers)
    {
        $res = mail($to, $subj, $body, $headers);
        if (!$res)
            return false;
        return true;
    }

    /**
     * hashLink function create hash from entered string
     *
     * @param string $link  - compose link
     * @return string $result
     */
    public static function hashLink($link)
    {
        $hash = base64_encode(self::md5_encrypt($link));
        $base64 = strtr($hash, '+/', '-_');
        return $base64;
    }

    /**
     * dehashLink function unhash from entered hash string
     *
     * @param string $link  - compose clear link
     * @return string $result
     */
    public static function dehashLink($link)
    {

        $base64 = strtr($link, '-_', '+/');
        $hash = base64_decode($base64);
        return self::md5_decrypt($hash);
    }

    public static function get_rnd_iv($iv_len)
    {
        $iv = '';
        while ($iv_len-- > 0) {
            $iv .= chr(mt_rand() & 0xff);
        }
        return $iv;
    }

    public static function md5_encrypt($plain_text, $iv_len = 1)
    {
        $password = PASS;
        $plain_text .= "\x13";
        $n = strlen($plain_text);
        if ($n % 16)
            $plain_text .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;
        $enc_text = self::get_rnd_iv($iv_len);
        $iv = substr($password ^ $enc_text, 0, 512);
        while ($i < $n) {
            $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
            $enc_text .= $block;
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return base64_encode($enc_text);
    }

    public static function md5_decrypt($enc_text, $iv_len = 1)
    {
        $password = PASS;
        $enc_text = base64_decode($enc_text);
        $n = strlen($enc_text);
        $i = $iv_len;
        $plain_text = '';
        $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $block = substr($enc_text, $i, 16);
            $plain_text .= $block ^ pack('H*', md5($iv));
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return preg_replace('/\\x13\\x00*$/', '', $plain_text);
    }

    public static function remoteIP()
    {
        $ip = !empty($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : (!empty($_SERVER["HTTP_X_REAL_IP"]) ? $_SERVER["HTTP_X_REAL_IP"] : (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER["REMOTE_ADDR"] : "127.0.0.1"));
        return $ip;
    }

    public static function geoIpResolve()
    {
        $ip = self::remoteIP();
        include_once (CORE_3RDPARTY_PATH . '/geoip/geoip.inc');
        $gi = geoip_open(CORE_3RDPARTY_PATH . '/geoip/GeoIP.dat', GEOIP_STANDARD);

        $code = geoip_country_code_by_addr($gi, $ip);
        geoip_close($gi);
        $code = strtolower(strval($code));
        return $code;
    }

    public static function prepareServerName($server)
    {
        return $server;
    }

    public static function getRegisterBlock()
    {

        if ($_SESSION['user_id'] > 0) {
            $block = '<div class="sign-in"><a href="' . SERVER_URL_NAME . '/orders/" class="orders"><span>Заказы</span></a><a href="' . SERVER_URL_NAME . '/profile/" class="profile"><span>Профиль</span></a><a href="#" class="logout"><span>Выход</span></a></div>';
        } else {
            $block = '<div class="sign-in"><a class="login" href="#"><span>Вход</span></a><a class="reg" href="' . SERVER_URL_NAME . '/registration"><span>Регистрация</span></a></div>';
        }

        return $block;
    }

    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    /**
     * interlayer for php require_once method
     * @param string $class_path
     * path to class folder
     * @param string $class_name
     * name of class file
     * @throws Exception
     */
    public static function autoLoaderClass($class_path = CLS_SYS_PATH, $class_name = '')
    {
        $search = array('{__class_path__}', '{__class_name__}');
        $repl = array($class_path, $class_name);
        if (empty($class_path) || empty($class_name)) {
            $throw_mess = self::getMessage('empty_file_path_in_load', 'Errors', $search, $repl);
            throw new \Exception($throw_mess);
        }

        if (!file_exists($class_path . $class_name)) {
            $throw_mess = self::getMessage('req_file_not_found', 'Errors', $search, $repl);
            throw new \Exception($throw_mess);
        } else {
            require_once ($class_path . $class_name);
        }
        return true;
    }

    /**
     * Is project on
     * @return boolean
     */
    public static function isProjectOn()
    {
        return (defined("PROJECT_ON") && PROJECT_ON);
    }

    /**
     * Return DEBUG mode for project
     * @return boolean
     */
    public static function getCommonDebug()
    {
        return self::isProjectOn() && defined("USE_DEBUG") ? USE_DEBUG : USE_SYS_DEBUG;
    }

    /**
     * Try to parse common(engine and project) ini files. Ini file from engine is required
     *
     * @param array $sys_ini_info
     * assoc array of 'path' and 'name' to engine ini file.
     * @param array $project_ini_info
     * assoc array of 'path' and 'name' to project ini file
     * @param boolean $required
     * is required engine ini file or not. Default - not.
     * @throws Exception
     * @return array
     * mixed assoc array of data from ini files
     */
    public static function getCommonIniFiles($sys_ini_info, $project_ini_info, $required = false)
    {
        $result = array();

        $search = array('{__class_path__}', '{__class_name__}');
        $repl = array($sys_ini_info['path'], $sys_ini_info['name']);
        $arr_replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);

        $is_file = true;
        // check for incoming standart of  array : array('path'=>'some_path', 'name'=>some_name)
        if (!is_array($sys_ini_info) || empty($sys_ini_info['path']) || empty($sys_ini_info['name'])) {
            if ($required) {
                $throw_mess = '<br>Engine : [' . $sys_ini_info['path'] . $sys_ini_info['name'] . '] incorrect data. <br>';
                throw new \Exception($throw_mess);
            } else {
                self::debugMessage('empty_file_path_in_load', '', true, true, $arr_replace);
                $is_file = false;
            }
        }

        if (!file_exists($sys_ini_info['path'] . $sys_ini_info['name'])) {
            if ($required) {
                $throw_mess = '<br>Engine : [' . $sys_ini_info['path'] . $sys_ini_info['name'] . '] not find. <br>';
                throw new \Exception($throw_mess);
            } else {
                self::debugMessage('req_file_not_found', '', true, true, $arr_replace);
                $is_file = false;
            }
        }
        // load file if file exists
        if ($is_file) {
            $result = parse_ini_file($sys_ini_info['path'] . $sys_ini_info['name'], true);
        }

        // init project note.ini file if project exists and "on"
        if (self::isProjectOn()) {

            $repl = array($project_ini_info['path'], $project_ini_info['name']);
            $arr_replace = array('block' => 'Errors', 'search' => $search, 'repl' => $repl);

            $is_file = true;
            // check for incoming standart of array : array('path'=>'some_path', 'name'=>some_name)
            if (!is_array($project_ini_info) || empty($project_ini_info['path']) || empty($project_ini_info['name'])) {
                if ($required) {
                    $throw_mess = '<br>Engine : [' . $project_ini_info['path'] . $project_ini_info['name'] . '] incorrect data. <br>';
                    throw new \Exception($throw_mess);
                } else {
                    self::debugMessage('empty_file_path_in_load', '', true, true, $arr_replace);
                    $is_file = false;
                }
            }

            if (!file_exists($project_ini_info['path'] . $project_ini_info['name'])) {
                if ($required) {
                    $throw_mess = '<br>Engine : [' . $project_ini_info['path'] . $project_ini_info['name'] . '] not find. <br>';
                    throw new \Exception($throw_mess);
                } else {
                    self::debugMessage('req_file_not_found', '', true, true, $arr_replace);
                    $is_file = false;
                }
                $is_file = false;
            }
            // load file if file exists and merge data with engine default messages
            if ($is_file) {
                $sub_result = parse_ini_file($project_ini_info['path'] . $project_ini_info['name'], true);
                foreach ($result as $key => $value) {
                    if(!empty($sub_result[$key]) && is_array($sub_result[$key])) {
                        $sub_result[$key] = array_merge($result[$key], $sub_result[$key]);
                    }else{
                        $sub_result[$key] = $result[$key];
                    }
                }
                $result = array_merge($result, $sub_result);
            }
        }
        return $result;
    }

    /**
     * Parse engine modules ini files for get required classes name.
     * @param array $modulesNames
     * 	modules name
     * @return array
     * 	key - module name; value - array with classes
     */
    public static function initUseModule($modulesNames)
    {
        // TODO: add check name in modules array
        $return = array();
        if (!empty($modulesNames) && is_array($modulesNames)) {

            $sys_ini_info = array('path' => CONF_SYS_PATH, 'name' => 'modules.ini');
            $project_ini_info = $sys_ini_info;
//            if( defined( 'CONFIG_DOMAIN_PATH' ) ){
//                $project_ini_info['path'] = CONFIG_DOMAIN_PATH;
//            }
            $classes = clsSysCommon::getCommonIniFiles($sys_ini_info, $project_ini_info, false);
            ;

            foreach ($modulesNames As $module) {
                if (!empty($classes[$module]['class'])) {
//                    self::$module_classes = array_merge(self::$module_classes, $classes[$module]['class']);
                    $return[$module] = $classes[$module]['class'];
                }
            }
        }

        return $return;
    }

    /**
     * Clear array with integer ids list
     *
     * @param array $attrIds
     *
     * @return array
     */
    public static function clearIds($attrIds = array())
    {
        $return = array();
        if (!empty($attrIds) && is_array($attrIds)) {
            array_walk($attrIds, function($v, $k) use(&$attrIds) {
                        if (empty($v)) {
                            unset($attrIds[$k]);
                        } else {
                            $attrIds[$k] = (int) $v;
                        }
                    });
            $return = $attrIds;
        }
        return $return;
    }

    public static function setMessages($messages) {
        if(!empty(self::$notes) && is_array(self::$notes) && !empty($messages) && is_array($messages)){
            self::$notes = array_merge($messages, self::$notes);
        }
    }
}