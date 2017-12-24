<?php
define("PASS", "1prqpIWB");
// ----- DataBase -----


if (isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'console') {
    define("HOST", 'console');
} else {
    if(isset($_SERVER['HTTP_HOST'])){
        define("HOST", $_SERVER['HTTP_HOST']);
    }else{
        define("HOST", 'cosmetics.loc');
    }
}

// +++++ SERVER +++++
define("SERVER_ROOT", realpath(dirname(__FILE__) . "/..") . "/");
//$domain = str_replace("www.","", $_SERVER["SERVER_NAME"]);
$domain = "ip.loc";
define("SERVER_NAME", $domain);
define("PROJECT_NAME", 'Версия');
define("PROJECT_VERSION", 0.1);
define("SITE_NAME_URI", 'http://bodytime.artvit-develop.ru/');

// read self config
define("HTTP_REL_PATH", '');

if (HOST == 'console') {
    define("SERVER_URL_NAME", HOST);
} else {
    if(isset($_SERVER["SERVER_NAME"])) {
        define("SERVER_URL_NAME", "http://" . $_SERVER["SERVER_NAME"] . HTTP_REL_PATH);
    } else {
        define("SERVER_URL_NAME", "http://cosmetics.loc" . HTTP_REL_PATH);
    }
}

// ----- SERVER -----


// +++++ SYSTEM +++++
define("SITE_ENABLED", 1);
define("PROJECT_ON", 1);

// read self config
define("SITE_URI_ENABLED", 0);

define("USE_CACHE", 0);
define("USE_CURRENT_CACHE", 0);
define("USE_ERROR_LOG", 0);
define("USE_DEBUG", 1);
define("USE_DEBAG_W", 0);
if (!empty($_GET['echo_sql']) && $_GET['echo_sql'] == 1)
    define("USE_DEBUG_SQL", 1);
else
    define("USE_DEBUG_SQL", 1);

//define ( "USE_DEBUG_SQL", 1 );
define("DEF_LIST_LIMIT", 3);
define("DEF_PAGING_NUM", 10);
define("DEF_ERROR_NUM", 9);
define("DEF_CANNT_EMPTY_SESS", 16);
define("DEF_CANNT_WR_LOG", 17);

// const price column_number
define("COUNT_PRICE_COLUMN_NUMBER", 5);
define("DEF_PRICE_COLUMN_NUMBER", 1);

define("PROMOTION_BLOCK_LIMIT", 4);

define('COMPARE_NULL', '-');

define("DEF_SQL_QUERY_ERR", 13);
define("DEF_BALANCE_NUM_ERR", 14);
define("DEF_CANNT_ADD_TO_ICART", 15);
define("DEF_CANNT_ADD_TO_ICART_ERR", 15);
define("DEF_SQL_CONNECT_ERR", 18);
define("DEF_UNKNOWN_REQUEST_ERR", 19);
define("DEF_UNKNOWN_DATA_RECIEVE_ERR", 20);
define("DEF_NOT_SIGNIN_ERR", 21);
define("DEF_NO_ORDER_ERR", 22);
define("DEF_FILE_NOT_FOUND_ERR", 23);
define("DEF_LICENSE_UNSUBSCRIBED", 24);


// +++++ ADMIN PARAMS +++++
define("ADMIN_SELECT_LIMIT", 100);
define("ADMIN_PAGING_LIMIT", 14);
// ----- ADMIN PARAMS -----
// ----- SYSTEM -----


// +++++ PATH +++++
define("SYS_LOG_PATH", SERVER_ROOT . "logs/");
define("COMMON_CLS_PATH", SERVER_ROOT . "classes/core/");
define("CLS_PATH", SERVER_ROOT . "classes/");
define("CONFIG_PATH", SERVER_ROOT . "config/");
define("SYS_DOMAIN_PATH", SERVER_ROOT . "domains/" . SERVER_NAME . "/");
define("SYS_DOMAIN_URL_PATH", SERVER_URL_NAME . "/domains/" . SERVER_NAME . "/");
define("CONFIG_DOMAIN_PATH", SYS_DOMAIN_PATH . "confdesign/");
define("CONFIGSYS_DOMAIN_PATH", SYS_DOMAIN_PATH . "confsystem/");
define("SYS_DOMAIN_PAYMENT_LOG_PATH", SYS_DOMAIN_PATH . "/logs/payment/log.log");
define("PAGE_PATH", SERVER_ROOT . "pages/");
define("BLOCK_PATH", SERVER_ROOT . "blocks/");
define("DICTIONARY_PATH", CONFIG_DOMAIN_PATH . "dictionary/");
define("CORE_3RDPARTY_PATH", SERVER_ROOT . "3rdparty/");
define("SYS_CACHE_PATH", SERVER_ROOT . "content/");
define("SYS_CACHE_DOMAIN_PATH", SERVER_ROOT . "content/" . $domain . "/");
define("ADMIN_PATH", SERVER_URL_NAME . "/admin");
define('DATA_PATH', SYS_DOMAIN_PATH . 'data' . DIRECTORY_SEPARATOR);

// ADD ENGINE
define('ENGINE_PATH', __DIR__ . '/../modules/');
define('MODELS_PATH', ENGINE_PATH . 'models');
define('DB_DOCTRINE', true);
define('PARSER_ADAPTER', 'Twig');
define('PARSER_TEMPLATES_PATH', CONFIG_DOMAIN_PATH . 'templates');
//define('SESSION_ADAPTER', 'Redis');

// DOCTRINE OPTIONS
if (strtolower(getenv('APPLICATION_ENV')) !== 'development') {
    define('DOCTRINE_PROXIES_DIR', DATA_PATH . 'doctrine_proxies');
} else {
    define('DOCTRINE_PROXIES_DIR', null);
}

// -- TPL --
define("TPL_DOMAIN_PATH", CONFIG_DOMAIN_PATH . "templates/pages/");
define("TPL_BLOCK_DOMAIN_PATH", CONFIG_DOMAIN_PATH . "templates/blocks/");
define("TPL_EMAIL_DOMAIN_PATH", CONFIG_DOMAIN_PATH . "templates/email/");
define("TPL_AJAX_DOMAIN_PATH", CONFIG_DOMAIN_PATH . "templates/ajax/");
define("TPL_COMMON_PATH", SERVER_ROOT . "templates/");
define("TPL_BLOCK_COMMON_PATH", SERVER_ROOT . "templates/blocks/");
define("TPL_AJAX_COMMON_PATH", SERVER_ROOT . "templates/ajax/");
define("TPL_EMAIL_COMMON_PATH", SERVER_ROOT . "templates/email/");
// -- END TPL --


// -- IMAGE's DEFAULT --
define("SYS_IMAGE_PATH", CONFIG_DOMAIN_PATH . "images/");
define("SYS_IMAGE_TH_PATH", SYS_IMAGE_PATH . "th/");
define("SYS_IMAGE_URL_PATH", SYS_DOMAIN_URL_PATH . "confdesign/images/");
define("SYS_IMAGE_MENU_PATH", SYS_IMAGE_URL_PATH . "categories/");
// -- END IMAGE's --


// -- opinions --
define('OPINIONS_PER_PAGE', 5);
define('OPINIONS_MAXLENTH', 500);
define('OPINIONS_DELAY_BEFORE_ADD_NEXT', 30);
define('INT_MAX', 4294967295);
// -- opinions --
// ----- PATH -----


// +++++ REG USERS +++++
define("DigitalCodeLength", 4);
define("MIN_PASS_LENGTH", 4);
define("MAX_PASS_LENGTH", 30);
define("NoCode", 2);
define("NoEmail", 3);
define("IncorrectEmail", 4);
define("NoPassword", 5);
define("IncorrectPassword", 6);
define("NoRePassword", 7);
define("PasswordMissmatch", 8);
define("EmailExists", 9);
define("Proceed", 1);
define("SystemError", 10);
define("PageNotExists", 11);
define("SiteClosed", 29);
// ----- REG USERS -----


// +++++ STANDARD TPL +++++
define("JS_TPL", '<script type="text/javascript" src="' . SERVER_URL_NAME . '{PATH}"></script>');
define("CSS_TPL", '<link href="' . SERVER_URL_NAME . '{PATH}" rel="stylesheet" type="text/css" />');
// ----- STANDARD TPL -----

// const params for search SPHINX
define('SEARCH_SPHINX_DIR', CORE_3RDPARTY_PATH . 'sphinx/');
define('SEARCH_SPHINX_API_PATH', SEARCH_SPHINX_DIR . 'api/sphinxapi.php');
define('SEARCH_SPHINX_DATA_DIR', SEARCH_SPHINX_DIR . 'data/');
define('SEARCH_SPHINX_SERVERNAME', 'localhost');
define('SEARCH_SPHINX_PORT', 3311);

// search highlight
define('SEARCH_SPHINX_HIGHLIGHT_BEFORE_MATCH', '<span class="search_highlight">'); // start html-tag outputted before query in text
define('SEARCH_SPHINX_HIGHLIGHT_AFTER_MATCH', '</span>'); // end html-tag outputted after query in text
define('SEARCH_SPHINX_HIGHLIGHT_EXACT_PHRASE', 1); // framing phrase in text
define("LOGIN_REQUIRED", 1);

//search config
define('SEARCH_LIMIT', 12);

// const for HTTP status code
define("HTTP_STATUS_OK", 200);
define("HTTP_STATUS_CREATED", 201);
define("HTTP_STATUS_NO_CONTENT", 204);
define("HTTP_STATUS_BAD_REQUEST", 400);
define("HTTP_STATUS_UNAUTHORIZED", 401);
define("HTTP_STATUS_FORBIDDEN", 403);
define("HTTP_STATUS_NOT_FOUND", 404);
define("HTTP_STATUS_METHOD_NOT_ALLOWED", 405);
define("HTTP_STATUS_CONFLICT", 409);
define("HTTP_STATUS_SERVER_ERROR", 500);

// category defaults
define ( 'CATEGORY_PRODUCTS_DEFAULT_LIMIT', 12 );
define ( 'MAIN_BLOCKS_PRODUCTS_DEFAULT_LIMIT', 8 );
define ( 'FOOTER_BLOCKS_PRODUCTS_DEFAULT_LIMIT', 4 );
define ( 'CATEGORY_SORT_DEFAULT', 'price' );
define ( 'CATEGORY_SORT_DIRECTION_DEFAULT', 'DESC' );

// order delivery price
define ( 'DELIVERY_LOW_PRICE', 130 );
define ( 'DELIVERY_BIG_PRICE', 150 );
define ( 'DELIVERY_FULL_PRICE', 150 );
define ( 'DELIVERY_COST', 500 );
define ( 'DELIVERY_FIRST_LIMIT', 12 );
define ( 'DELIVERY_SECOND_LIMIT', 16 );

// news config
define ( 'NEWS_LIMIT', 6 );
define ( 'NEWS_LAST_BLOCK_LIMIT', 4 );

//emails config
define ( 'FEEDBACK_MAIL_ID', 2);
define ( 'CALL_BACK_MAIL_ID', 1);
define ( 'REGISTER_MAIL_ID', 4);
define ( 'PAYMENT_MAIL_ID', 5);
define ( 'CUSTOMER_ORDER_MAIL_ID', 6);
define ( 'MANAGER_ORDER_MAIL_ID', 7);
define ( 'DEFAULT_EMAIL', 'teluvremja@mail.ru');

//payment config
define ( 'ROBOCASSA_URL', 'http://test.robokassa.ru/Index.aspx');
define ( 'ROBOCASSA_MERCHANT_LOGIN', 'cosmetics_ru');
define ( 'ROBOCASSA_PASSWORD_1', '1q2w3e4r5t6y');
define ( 'ROBOCASSA_PASSWORD_2', '1122334455q');
//define ( 'ROBOCASSA_MERCHANT_LOGIN', 'bodytime_loc');
//define ( 'ROBOCASSA_PASSWORD_1', '1qazxsw2');
//define ( 'ROBOCASSA_PASSWORD_2', '2wsxzaq1');
