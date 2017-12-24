<?php

namespace classes\core;

use engine\clsSysCommon;

class clsCommon extends clsSysCommon
{

    private static $config = array();
    private static $notes = array();

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $error = "PHP ERROR : " . $errstr . "<br /> FILE : " . $errfile . " LINE : " . $errline;

        switch ($errno) {
            case E_USER_WARNING :
                $type = 2;
                break;

            case E_USER_NOTICE :
                $type = 1;
                break;

            case E_USER_ERROR :
            default :
                $type = 3;
                break;
        };
        if (USE_ERROR_LOG) {
            $logPath = is_dir(CONFIGSYS_DOMAIN_PATH . 'logs/') ? CONFIGSYS_DOMAIN_PATH . 'logs/' : SYS_LOG_PATH;
            self::Log($error, $type, "php_log.log");
        };

        return true;
    }

    function prepareHref($filter, $href)
    {
        $hrefQuery = array();
        if (isset($filter["sort"])) {
            $hrefQuery[] = 'sort=' . (string)$filter["sort"];
            if (isset($filter["direction"])) {
                $hrefQuery[] = 'direction=' . (string)$filter["direction"];
            }
        }
        if (!empty($filter["ids"])) {
            $hrefQuery[] = 'ids=' . (string)$filter["ids"];
        }
        if (!empty($filter["limit"])) {
            $hrefQuery[] = 'limit=' . (int)$filter["limit"];
        }
        if (!empty($filter["priceto"])) {
            $hrefQuery[] = 'priceto=' . (int)$filter["priceto"];
        }
        if (!empty($filter["pricefrom"])) {
            $hrefQuery[] = 'pricefrom=' . (int)$filter["pricefrom"];
        }
        if (!empty($filter["brand"])) {
            $hrefQuery[] = 'brand=' . (int)$filter["brand"];
        }
        if (!empty($filter["texture"])) {
            $hrefQuery[] = 'texture=' . (int)$filter["texture"];
        }
        if (!empty($filter["color"])) {
            $hrefQuery[] = 'color=' . (int)$filter["color"];
        }
        
        foreach ($filter as $key => $value) {
            $href = str_replace('{' . strtoupper($key) . '}', urlencode($value), $href);
        }

        if (!empty($hrefQuery)) {
            $href .= '?' . implode('&', $hrefQuery);
        }

        return $href;
    }

    public static function compileArticleHref($news_id, $news_name, $relative = false)
    {
        $config = self::getDomainConfig();
        return strtolower(
            (!$relative ? SERVER_URL_NAME : "") . str_replace(
                array("{NEWS_ID}", "{NEWS_NAME}"),
                array(self::isInt($news_id), self::escapeName($news_name)),
                $config["Article"]["href"]
            )
        );
    }

    /**
     * @deprecated
     */
    public static function compileNewsListHref($filter = array(), $index = false, $relative = false)
    {
        $config = self::getDomainConfig();

        $href = $config["News"]["href_paging"];
        $href = self::prepareHref($filter, $href);

        return strtolower((!$relative ? SERVER_URL_NAME : "") . $href);
    }

    /**
     * @deprecated
     */
    public static function compileSearchResultsListHref($filter = array(), $relative = false)
    {
        $config = self::getDomainConfig();

        $href = $config["SearchResults"]["href"];
        $href = self::prepareHref($filter, $href);

        return strtolower((!$relative ? SERVER_URL_NAME : "") . $href);
    }

    /**
     * Compile href url for lists
     *
     * @param string $name
     * @param array $filter
     * @param bool $relative
     *
     * @return string
     */
    public static function compileDefaultListHref($name = '', $filter = array(), $relative = false)
    {
        $config = self::getDomainConfig();

        if (isset($name) && !empty($name) && isset($config[$name]["href_paging"])) {
            $href = $config[$name]["href_paging"];
            $href = self::prepareHref($filter, $href);
        } else {
            // TODO: create link on error page
            $href = '#error';
        }

        return strtolower((!$relative ? SERVER_URL_NAME : "") . $href);
    }

    /**
     * Compile href url for lists
     *
     * @param string $name
     * @param array $filter
     * @param bool $relative
     *
     * @return string
     */
    public static function compileDefaultItemHref($name = '', $filter = array(), $relative = false)
    {
        $config = self::getDomainConfig();

        if (isset($name) && !empty($name) && isset($config[$name]["href"])) {
            $href = $config[$name]["href"];
            $href = self::prepareHref($filter, $href);
        } else {
            // TODO: create link on error page
            $href = '#error';
        }

        return strtolower((!$relative ? SERVER_URL_NAME : "") . $href);
    }

    /**
     * Compile href string for Product
     * @param integer $product_id
     * @param bool $relative
     *
     * @return string
     */
    public static function compileProductItemHref($product_id = 0, $relative = false)
    {
        $config = self::getDomainConfig();
        return strtolower(
            (!$relative ? SERVER_URL_NAME : "") . str_replace(
                array("{PRODUCT_ID}"),
                array(self::isInt($product_id)),
                $config["Products"]["href"]
            )
        );
    }

    /**
     * Compile href string for Category
     * @param integer $cat_id
     * @param bool $relative
     *
     * @return string
     */
    public static function compileCategoryItemHref($cat_id = 0, $relative = false, $filter = array())
    {
        $config = self::getDomainConfig();
        $href = str_replace(array("{SLUG}"), array($cat_id), $config["Category"]["href"]);
        $href = self::prepareHref($filter, $href);

        return strtolower((!$relative ? SERVER_URL_NAME : "") . $href);
    }

    public static function getPageName($filter, $index, $class = "VideoList")
    {
        $config = self::getDomainConfig();

        $page_name = $config["URL"][$class];
        if ($index && !empty($config["URL"][$class . "#" . $index])) {
            $page_name = $config["URL"][$class . "#" . $index];
        } elseif (!empty($filter["page_name"]) && in_array($filter["page_name"], array_values($config["URL"]))) {
            $page_name = $filter["page_name"];
        }
        return $page_name;
    }

    public function getURLPaging($compileHrefMethod, $pageLimit = DEF_LIST_LIMIT, $count = 0, $filters = array())
    {
        $chain = "";
        if (empty($filters['page']) || self::isInt($filters['page']) < 1) {
            $page = 1;
        } else {
            $page = (integer)$filters['page'];
        }

        if ($count > $pageLimit) {
            if ($page > 1) {
                $prevHref = self::$compileHrefMethod(self::getChangedFilters($filters, array("page" => $page - 1)));
                $chain .= '<a href="' . $prevHref . '" title="Previous"><img src="/images/black_arrow_l.gif" width="8" height="12" alt="arrow" /></a> ';
            }
            $pcnt = $pageCount = intval(ceil($count / $pageLimit));
            $startPage = 1;
            if ($pageCount > DEF_PAGING_NUM) {
                $m = $page % DEF_PAGING_NUM;
                if ($m > 0) {
                    $startPage = $page - $m + 1;
                } else {
                    $startPage = (intval($page / DEF_PAGING_NUM) - 1) * DEF_PAGING_NUM + 1;
                }

                $pcnt = DEF_PAGING_NUM;
                if ($startPage + DEF_PAGING_NUM > $pageCount) {
                    $pcnt = $pageCount % DEF_PAGING_NUM;
                    if ($pcnt == 0) {
                        $pcnt = DEF_PAGING_NUM;
                    }
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
     *    name class
     * @param integer $page
     *    current page of listing (default 1)
     * @param integer $pageLimit
     *    number of items per page (default depends on domain config)
     * @param integer $count
     *    number of all items in listing
     * @param array $filters
     *    array of various filters( need to build url for paging html)
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
        $maximumCount = ceil($page * (int)$pageLimit);
        if ((int)$count > (int)$maximumCount) {
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
    public function getPagingAjax(
        $page = 1,
        $pageLimit = DEF_LIST_LIMIT,
        $count,
        $anchor = "",
        $js_function = "",
        $filters = array()
    ) {
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
                if ($m > 0) {
                    $startPage = $page - $m + 1;
                } else {
                    $startPage = (intval($page / DEF_PAGING_NUM) - 1) * DEF_PAGING_NUM + 1;
                }

                $pcnt = DEF_PAGING_NUM;
                if ($startPage + DEF_PAGING_NUM > $pageCount) {
                    $pcnt = $pageCount % DEF_PAGING_NUM;
                    if ($pcnt == 0) {
                        $pcnt = DEF_PAGING_NUM;
                    }
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
        if ($res < 1) {
            return false;
        }

        return true;
    }

    public static function IsValidPassword($password)
    {
        $res = preg_match("/(?:\'+|\"+|\\\\+|\/+)/", $password);
        if (is_bool($res) && $res == false) {
            return false;
        }

        if ($res > 0) {
            return false;
        }

        return true;
    }

    public static function checkRegData($data)
    {
        if (strlen($data['reg_code']) != DigitalCodeLength) {
            unset($_SESSION['rcode']['code']);
            return NoCode;
        }

        if ($data['reg_code'] != $_SESSION['rcode']['code']) {
            unset($_SESSION['rcode']['code']);
            return NoCode;
        }
        unset($_SESSION['rcode']['code']);

        $len = strlen($data['reg_email']);
        if ($len < 1) {
            return NoEmail;
        }

        if (!self::IsValidEmail($data['reg_email'])) {
            return IncorrectEmail;
        }

        $len = strlen($data['reg_pass']);

        if ($len < 1 || $len < MinPassLength || $len > MaxPassLength) {
            return NoPassword;
        }

        if (!self::IsValidPassword($data['reg_pass'])) {
            return IncorrectPassword;
        }

        if (strlen($data['reg_repass']) < 1) {
            return NoRePassword;
        }

        if (strcmp($data['reg_pass'], $data['reg_repass']) != 0) {
            return PasswordMissmatch;
        }

        return Proceed;
    }

    public static function checkRecoverPasswordData($data)
    {
        if (strlen($data['captcha_code']) != DigitalCodeLength) {
            unset($_SESSION['rcode']['code']);
            return NoCode;
        }

        if ($data['captcha_code'] != $_SESSION['rcode']['code']) {
            unset($_SESSION['rcode']['code']);
            return NoCode;
        }
        unset($_SESSION['rcode']['code']);

        $len = strlen($data['user_email']);
        if ($len < 1) {
            return NoEmail;
        }

        if (!self::IsValidEmail($data['user_email'])) {
            return IncorrectEmail;
        }

        return Proceed;
    }

    public static function checkChangePassData($data)
    {
        $len = strlen($data['change_pass']);

        if ($len < 1 || $len < MinPassLength || $len > MaxPassLength) {
            return NoPassword;
        }

        if (!self::IsValidPassword($data['change_pass'])) {
            return IncorrectPassword;
        }

        if (strlen($data['change_repass']) < 1) {
            return NoRePassword;
        }

        if (strcmp($data['change_pass'], $data['change_repass']) != 0) {
            return PasswordMissmatch;
        }

        return Proceed;
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
            if (strpos($location, "?") > 0) {
                $separator = "&";
            } else {
                $separator = "?";
            }

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
            if (strpos($location, "?") > 0) {
                $separator = "&";
            } else {
                $separator = "?";
            }

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
            if (strpos($location, "?") > 0) {
                $separator = "&";
            } else {
                $separator = "?";
            }

            $fixed = $location . $separator . $sname . "=" . $sid;
            Header($fixed);
            exit();
        }
    }

    public static function checkAuthData($data)
    {
        $len = strlen($data['auth_email']);
        if ($len < 1) {
            return NoEmail;
        }

        if (!self::IsValidEmail($data['auth_email'])) {
            return IncorrectEmail;
        }

        $len = strlen($data['auth_pass']);

        if ($len < 1 || $len < MinPassLength || $len > MaxPassLength) {
            return NoPassword;
        }

        if (!self::IsValidPassword($data['auth_pass'])) {
            return IncorrectPassword;
        }

        return Proceed;
    }

    /**
     * Log function check variable for is array and return empty array if variable empty
     *
     * @param text $text - text to log it
     * @param integer $type - type of the log message
     * 1 - Notice
     * 2 - Warning
     * 3 - Error
     * @param string $file - file to write log(default to log file from config)
     * @return $result array
     */
    public static function Log($text, $type = 1, $file_name = "")
    {

        if (!$file_name) {
            $file_name = "log.log";
        }

        $logPath = $domain_log_path = CONFIGSYS_DOMAIN_PATH . 'logs/';
        clsCommon::CreateDirRec($domain_log_path);
        if (!is_dir($domain_log_path)) {
            $logPath = SYS_LOG_PATH;
            clsCommon::CreateDirRec($logPath);
        }

        $file = $logPath . $file_name;

        $type_text = "Notice";
        switch ($type) {
            case 2 :
                $type_text = "Warning";
                break;
            case 3 :
                $type_text = "Error";
                break;
            case 1 :
            default :
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
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * hashLink function create hash from entered string
     *
     * @param string $link - compose link
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
     * @param string $link - compose clear link
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
        if ($n % 16) {
            $plain_text .= str_repeat("\0", 16 - ($n % 16));
        }
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

    public static function prepareServerName($server)
    {
        return $server;
    }

    /**
     * Return paginator object for paginator content
     *
     * @param string $url
     * page url
     * @param integer $count
     * number of elements
     * @param integer $page
     * current page
     * @param integer $limit
     * number of an elements per page
     * @param string $sort
     * sort field name
     * @param string $sorter
     * 'asc' or 'desc'
     * @param array $filter
     * array of filters, where key of element is name of filtering field
     * @return array
     * @return array:
     */
    public static function setPaginatorObject($url, $count, $page, $limit, $sort='', $sorter = 'desc', $filter = array())
    {
        $result = array();

        $result['filterStr'] = '';
        if(is_array($filter) && !empty($filter)){
            foreach($filter as $kFilter=>$vFilter){
                $result['filterStr'] .= '&filter['.$kFilter.']='.$vFilter;
            }
        }
        $getQuery = '&sort='.$sort.'&sorter='.$sorter.$result['filterStr'];

        $result['filter'] = $filter;
        $result['count'] = (int)$count;
        $result['page'] = (int)$page;
        $result['limit'] = (int)$limit;
        $result['url'] = $url;
        $result['sort'] = $sort;
        $result['sorter'] = !empty($sorter) ? ($sorter == 'desc' ? 'desc' : 'asc' ) : '';
        $result['exists'] = $result['count'] > $result['limit'] ? true : false;
        $result['first'] = $result['exists'] ? '1'.$getQuery : '';

        $result['previous'] = ($result['page'] - 1) > 0 ? ($result['page'] - 1).$getQuery : '';
        $pages_row = (int)$page % ADMIN_PAGING_LIMIT > 0 ? floor((int)$page / ADMIN_PAGING_LIMIT) : floor((int)$page / ADMIN_PAGING_LIMIT) - 1;
        $max_page = ceil($result['count'] / $result['limit']);
        $result['next'] = ($result['page'] + 1) < $max_page ? ($result['page'] + 1).$getQuery : '';
        $result['last'] = $result['exists'] ? $max_page.$getQuery : '';
        $first_in_row = $pages_row * ADMIN_PAGING_LIMIT;
        $last_in_row = ($pages_row + 1) * ADMIN_PAGING_LIMIT > $max_page ? $max_page : ($pages_row + 1) * ADMIN_PAGING_LIMIT;
        if($pages_row > 0) {
            $result['pagesInRange'][$first_in_row - 1]['num'] = '...';
            $result['pagesInRange'][$first_in_row - 1]['url'] = $result['url'] . '?page=' .($first_in_row).$getQuery;
        }
        for ($i = $first_in_row; $i < $last_in_row; $i++) {
            $result['pagesInRange'][$i + 1]['num'] = $i + 1;
            $result['pagesInRange'][$i + 1]['url'] = $result['url'] . '?page=' .($i + 1).$getQuery;//'&sort='.$sort.'&sorter='.$sorter;
        }
        if($last_in_row < $max_page) {
            $result['pagesInRange'][$last_in_row + 1]['num'] = '...';
            $result['pagesInRange'][$last_in_row + 1]['url'] = $result['url'] . '?page=' .($last_in_row+1).$getQuery;
        }
        return $result;
    }

    /**
     * Upload image to folder
     * @param string $name
     * file name
     * @param array $files
     * $_FILES
     * @param string $upload_path
     * path to upload dir
     * @param bool $force
     * remove if exists
     * @param int $maxsize
     * maxsize
     * @param array $allowed
     * allowed extensions
     * @return array|bool
     */
    public static function uploadImage(&$name, $files, $upload_path, $force = false, $maxsize = 200000, $allowed = array("gif", "jpeg", "jpg", "png"))
    {
        $errors = array();
        $temp = explode(".", $files["name"]);
        $extension = end($temp);
        if(empty($name)){
            $name = $files["name"];
        }else{
            $name .= '.'.$extension;
        }
        $name = str_replace(' ','_',$name);
        if ((($files["type"] == "image/gif")
                || ($files["type"] == "image/jpeg")
                || ($files["type"] == "image/jpg")
                || ($files["type"] == "image/pjpeg")
                || ($files["type"] == "image/x-png")
                || ($files["type"] == "image/png"))
            && ($files["size"] < $maxsize)
            && in_array($extension, $allowed)
        ) {
            if ($files["error"] > 0) {
                $errors[] = "Return Code: " . $files["error"] . "<br>";
            } else {


                $name = self::rus2translit($name);
                if($force && file_exists($upload_path . $name)){
                    unlink($upload_path . $name);
                }
                if (file_exists($upload_path . $name)) {
                    $errors[] = $upload_path . $name . " already exists. ";
                } else {
                    move_uploaded_file(
                        $files["tmp_name"],
                        $upload_path . $name
                    );
                    $errors = true;
                }
            }
        } else {
            $error[] = !(($files["type"] == "image/gif")
                || ($files["type"] == "image/jpeg")
                || ($files["type"] == "image/jpg")
                || ($files["type"] == "image/pjpeg")
                || ($files["type"] == "image/x-png")
                || ($files["type"] == "image/png")) ? 'Incorrect image type' : '';
            $error[] = !($files["size"] < $maxsize) ? ('Incorrect image size[' . $files["size"] .']') : '';
            $error[] = !in_array($extension, $allowed) ? ('Incorrect image extension[' .$extension. ']') : '';
            $errors[] =  "Invalid uploaded file [" .join("|", $error).']';
        }
        return $errors;
    }

    /**
     * Upload xls to folder
     * @param string $name
     * file name
     * @param array $files
     * $_FILES
     * @param string $upload_path
     * path to upload dir
     * @param bool $force
     * remove if exists
     * @param int $maxsize
     * maxsize
     * @param array $allowed
     * allowed extensions
     * @return array|bool
     */
    public static function uploadXLS(&$name, $files, $upload_path, $force = false, $maxsize = 500000, $allowed = array("xls", "xlsx"))
    {
        $errors = array();
        $temp = explode(".", $files["name"]);
        $extension = end($temp);
        if(empty($name)){
            $name = str_replace(' ','_',$files["name"]);
        }else{
            $name = str_replace(' ','_',$name);
            $name .= '.'.$extension;
        }

        if ((($files["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                || ($files["type"] == "application/vnd.ms-excel")
                || ($files["type"] == "application/vnd.ms-office"))
            && ($files["size"] < $maxsize)
            && in_array($extension, $allowed)
        ) {
            if ($files["error"] > 0) {
                $errors[] = "Return Code: " . $files["error"] . "<br>";
            } else {
//                $name = self::transliterate($name);
                if($force && file_exists($upload_path . $name)){
                    unlink($upload_path . $name);
                }
                if (file_exists($upload_path . $name)) {
                    $errors[] = $upload_path . $name . " already exists. ";
                } else {
                    move_uploaded_file(
                        $files["tmp_name"],
                        $upload_path . $name
                    );
                    $errors = true;
                }
            }
        } else {
            echo 'type ' . $files["type"];
            $errors[] =  "Invalid file";
        }
        return $errors;
    }

    /**
     * Translate russian to latin
     * @param $string
     * @return string
     */
    public static function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }
    
    public function prepareMonthes($st) {
        $monthes = array(
            1   => 'январь',
            2   => 'февраль',
            3   => 'март',
            4   => 'апрель',
            5   => 'май',
            6   => 'июнь',
            7   => 'июль',
            8   => 'август',
            9   => 'сентябрь',
            10  => 'октябрь',
            11  => 'ноябрь',
            12  => 'декабрь'
        );
        
        $arr = explode('-', $st);
        if(!empty($arr) && count($arr) == 2) {
            $st = $monthes[(int)$arr[0]] . ' ' . $arr[1];
        }
        
        return $st;
    }

}