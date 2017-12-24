<?php
session_start();
require_once(realpath(dirname(__FILE__) . "/..") . "/config/config.php" );
require_once (ENGINE_PATH . "sys_index.php");
require_once (COMMON_CLS_PATH . "clsCommon.php");
require_once (SERVER_ROOT . "bootstrap.php");

$cart = clsCart::getInstance();
$poll = clsPolls::getInstance();
$categories = clsCategory::getInstance();

if(!empty($_POST['method'])) {
    switch ($_POST['method']) {
        case "addToCart":
            echo json_encode($cart->addToCart($_POST['from'], $_POST['params'], $_POST['order']));
            break;
        case "deleteFromCart":
            echo json_encode($cart->deleteFromCart((int) $_POST['id'], $_POST['from'], $_POST['order']));
            break;
        case "updateAmount":
            echo json_encode($cart->updateAmount((int)$_POST['id'], (int)$_POST['amount'], $_POST['from'], (int)$_POST['order']));
            break;
        case "compare":
            echo compareProducts();
            break;
        case "addToCartAll":
            echo json_encode($cart->addToCartAll($from, $_POST['data']));
            break;
        case "interviewComplete":
            $poll->addPoll($_POST['poll'], $_POST['data']);
            echo 123;
            break;
        case "getCartBlock":
            echo json_encode($cart->getCartBlock());
            break;
        case "getAuthBlock":
            echo clsCommon::getRegisterBlock();
            break;
        case "getMenuBlock":
            $result = '';

            $aCategories = $categories->getAllForMenu((int)$_POST['catId']);
            $parentCategoryId = NULL;
            $categoryItems = '';
            if(!empty($aCategories)) {
                $parser = new clsParser();
                foreach($aCategories as $k => $v) {
                    if($parentCategoryId != $v['parent_id']) {
                        if($parentCategoryId != NULL) {
                            $parser->clear();
                            $parser->setVar('{ITEMS}', $categoryItems);
                            $parser->setBlockTemplate('category_third_block_div.html');
                            $result .= $parser->getResult();
                            $categoryItems = '';
                        }
                        $parentCategoryId = $v['parent_id'];
                    }

                    $parser->clear();
                    $parser->setVar('{SERVER_URL_NAME}', SERVER_URL_NAME);
                    $parser->setVar('{SYS_IMAGE_MENU_PATH}', SYS_IMAGE_MENU_PATH);
                    $parser->setVar('{TITLE}', $v['name']);
                    $parser->setVar('{OFFSET_X}', $v['image_offset_x'], true);
                    $parser->setVar('{OFFSET_Y}', $v['image_offset_y'], true);
                    $parser->setVars($v, true, true);
                    $parser->setBlockTemplate('category_third_block_item.html');
                    $categoryItems .= $parser->getResult();

                }
            }

            $parentCategoryId = $v['parent_id'];
            $parser->clear();
            $parser->setVar('{ITEMS}', $categoryItems);
            $parser->setBlockTemplate('category_third_block_div.html');
            $result .= $parser->getResult();
            
            echo $result;
            break;
        case "addAddress":
            echo clsAddresses::getInstance()->addAddress($_POST['address'], clsSession::getInstance()->getUserIdSession());
            break;
        case "addCompany":
            $data = $_POST;
            $data['status'] = 1;
            echo clsCompany::getInstance()->addCompanyFull($data, clsSession::getInstance()->getUserIdSession());
            break;
        case "deleteAddress":
            echo clsAddresses::getInstance()->deleteAddress((int)$_POST['id']);
            break;
        case "deleteCompany":
            echo clsCompany::getInstance()->deleteCompany((int)$_POST['id']);
            break;
        case "addOrder":
            $cart = clsSession::getInstance()->getCartSessionForOrder();
            $error = '';
            $sum = 0;
            $result = false;

            if(!empty($_POST['companyId']) && clsSession::getInstance()->getUserIdSession()) {
                foreach ($cart as $k => $v) {
                    $sum += $v['cnt'] * $v['price'];
                }
                $cost = clsUser::getInstance()->getUserCostById((int)clsSession::getInstance()->getUserIdSession());
                if($cost && $cost != 0) {
                    $sum = round( $sum * (100 - $cost) / 100, 2);
                }

                /** @var classes\clsOrder $orders */
                $orders = clsOrder::getInstance();
                $params = array('userId' => (int)clsSession::getInstance()->getUserIdSession(),
                                'orderStatusId' => 1, 'companyId' => (int)$_POST['companyId'],
                                'clientId' => 0, 'summary' => $sum, 'comments' => '',
                                'products' => $cart, 'addressId' => (int)$_POST['addressId'],);
                $result = $orders->addOrder($params);

                echo $result;
            }
            break;

        case "deleteOrder":
            echo clsOrder::getInstance()->deleteOrder((int)$_POST['id']);
            break;
        case "manager":
            $data = array('manager_name' => !empty($_POST['manager_name']) ? $_POST['manager_name'] : '',
                'manager_surname' => !empty($_POST['manager_surname']) ? $_POST['manager_surname'] : '',
                'manager_full_name' => !empty($_POST['full_name']) ? $_POST['full_name'] : '',
                'manager_phone' => !empty($_POST['phone']) ? $_POST['phone'] : '',
                'manager_phone2' => !empty($_POST['phone2']) ? $_POST['phone2'] : '',
                'manager_email' => !empty($_POST['email']) ? $_POST['email'] : '',
                'client_name' => !empty($_POST['name']) ? $_POST['name'] : '',
                'client_surname' => !empty($_POST['surname']) ? $_POST['surname'] : '',
                'client_full_name' => !empty($_POST['client_full_name']) ? $_POST['client_full_name'] : '',
                'client_email' => !empty($_POST['email']) ? $_POST['email'] : '',
                'client_email' => !empty($_POST['client_email']) ? $_POST['client_email'] : '',
//                'client_phone' => !empty($_POST['phone']) ? $_POST['phone'] : '',
//                'companyAddress' => !empty($_POST['companyAddress']) ? $_POST['companyAddress'] : '',
//                'companyName' => !empty($_POST['companyName']) ? $_POST['companyName'] : '',
//                'address' => !empty($_POST['address']) ? $_POST['address'] : '',
//                'requisite' => !empty($_POST['requisite']) ? $_POST['requisite'] : '',
                'comments' => !empty($_POST['comments']) ? $_POST['comments'] : ''
                );

            $params = array();
            $params['fields'] = array('manager_name', 'manager_surname', 'manager_phone',
                'manager_phone2', 'manager_email', 'client_name', 'client_surname',
                'client_email', 'client_phone', 'companyAddress', 'companyName',
                'address', 'requisite', 'comments', 'manager_full_name', 'client_full_name');
//            $params['fields'] = array('manager_name', 'manager_surname', 'manager_phone',
//                'manager_phone2', 'manager_email', 'client_name', 'client_surname',
//                'client_email', 'client_phone', 'companyAddress', 'companyName',
//                'address', 'requisite', 'comments', 'manager_full_name', 'client_full_name');
            $params['names'] = array('Имя менеджера', 'Фамилия менеджера', 'Я хз',
                'Внутренний телефон', 'Email менеджера', 'Ваше имя', 'Ваша фамилия',
                'Ваш Email', 'Ваш телефон', 'Адрес фирмы', 'Название фирмы',
                'Адрес для доставки', 'Реквизиты фирмы', 'Комментарии', 'ФИО менеджера', 'Ваше ФИО');
            $params['filters']['required'] = array();
            $params['filters']['email'] = array();
            $params['filters']['nameString'] = array();
            $params['filters']['phone'] = array();
            echo json_encode(clsAuthorisation::getInstance()->sendToManager($data, $params));
            break;

        case 'getCart':
            if(!empty($_POST['page']) && $_POST['page'] == 'cart') {
                $sum = clsSession::getInstance()->getUserCartSum();
            } else {
                $sum = clsSession::getInstance()->getCartSum();
            }
            $cnt = clsSession::getInstance()->getCartCount();
            echo json_encode(array('count' => $cnt, 'sum' => number_format($sum, 2)));
            break;

        case 'getProductsCount':
            $res = clsSession::getInstance()->getProductsCount();
            echo json_encode($res);
            break;
        
        case 'getProducts':
            $res = clsSession::getInstance()->getProductsCount();
            echo json_encode($res);
            break;
        
        case 'getChangeProducts':
            $res = '';
            $products = clsGoods::getInstance()->getSimilarsByProductId((int)$_POST['id']);
            if(!empty($products)) {
                $res = getSimilars($products);
            }
            echo $res;
            break;

        default:
            break;
    }
} else {
    return false;
}

function compareProducts() {
    $products = clsCart::getInstance()->compare($_POST['ids']);
    if($products) {
        $parser = new clsParser();
        $main = '';
        foreach($products[0]['attributes'] as $k => $v) {
            foreach($products[1]['attributes'] as $k2 => $v2) {
                if($v['id'] == $v2['id']) {
                    $parser->clear();
                    $parser->setVar('{NAME}', $v['name']);
                    $parser->setVar('{FIRST_VAL}', $v['value']);
                    $parser->setVar('{SECOND_VAL}', $v2['value']);
                    $parser->setTemplate('compare_products_main.html');
                    $main .= $parser->getResult();
                    unset($products[1]['attributes'][$k2]);
                    unset($products[0]['attributes'][$k]);
                }
                break;
            }
        }
        $additional = "";
        foreach($products[0]['attributes'] as $k => $v) {
            $parser->clear();
            $parser->setVar('{NAME}', $v['name']);
            $parser->setVar('{FIRST_VAL}', $v['value']);
            $parser->setVar('{SECOND_VAL}', '-');
            $parser->setTemplate('compare_products_additional.html');
            $additional .= $parser->getResult();
        }
        foreach($products[1]['attributes'] as $k => $v) {
            $parser->clear();
            $parser->setVar('{NAME}', $v['name']);
            $parser->setVar('{FIRST_VAL}', '-');
            $parser->setVar('{SECOND_VAL}', $v['value']);
            $parser->setTemplate('compare_products_additional.html');
            $additional .= $parser->getResult();
        }
        $parser->clear();
        $parser->setVar('{MAIN}', $main);
        $parser->setVar('{ADDITIONAL}', $additional);
        $parser->setVar('{FIRST_PRODUCT_NAME}', $products[0]['name']);
        $parser->setVar('{SECOND_PRODUCT_NAME}', $products[1]['name']);
        $parser->setTemplate('compare_products.html');
        $result = $parser->getResult();
        return json_encode(array('result' => $result));
    } else {
        return json_encode(array('result' => $result));
    }
}

function getSimilars($products) {
    $parser = new clsParser();
    $productsRendered = '';
    $ids = array();
    foreach($products as $v) {
        $ids[] = $v['id'];
    }
    if(!empty($ids)) {
        $attributes = clsAttributes::getInstance()->getAttributesByProductIds($ids);
        if (!empty($attributes)) {
            foreach ($attributes as $value) {
                if(!empty($value['group_attribute'])) {
                    $attributesValues[$value['product_id']]['group'][] = $value;
                } else {
                    $attributesValues[$value['product_id']]['single'][] = $value;
                }
            }
        }
    }

    foreach($products as $p) {
        $productSingleAttributes = array();

        if(!empty($attributesValues[$p['id']])) {
            $productAttributesRendered = '';

            if(!empty($attributesValues[$p['id']]['group'])) {
                $productAttributes = '';
                foreach($attributesValues[$p['id']]['group'] as $v) {
                    $parser->clear();
                    $parser->setTemplate("order_page_div_attributes_div_item.html");
                    $parser->setVars($v, true);
                    $productAttributes .= $parser->getResult();
                }

                $parser->clear();
                $parser->setVar('{CLASS}', ' left');
                $parser->setVar('{ITEMS}', $productAttributes);
                $parser->setTemplate("order_page_div_attributes_div.html");
                $productAttributesRendered .= $parser->getResult();
            }

            if(!empty($attributesValues[$p['id']]['single'])) {
                $productAttributes = '';
                foreach($attributesValues[$p['id']]['single'] as $v) {
                    $productSingleAttributes[] = $v['value'];

                    $parser->clear();
                    $parser->setTemplate("order_page_div_attributes_div_item.html");
                    $parser->setVars($v, true);
                    $productAttributes .= $parser->getResult();
                }

                $parser->clear();
                $parser->setVar('{CLASS}', ' right');
                $parser->setVar('{ITEMS}', $productAttributes);
                $parser->setTemplate("order_page_div_attributes_div.html");
                $productAttributesRendered .= $parser->getResult();
            }

            $parser->clear();
            $parser->setVar('{ITEMS}', $productAttributesRendered);
            $parser->setTemplate("order_page_div_attributes.html");
            $p['attributes'] = $parser->getResult();
        }

        $parser->clear();
        $parser->setVars($p, true);
        $parser->setVar('{SINGLE_ATTRIBUTES}', join(', ', $productSingleAttributes));
        $parser->setVar('{IMAGE}', SERVER_URL_NAME . '/dimages/products/' . (!empty($p['image']) ? $p['image'] : 'default.png'));
        $parser->setVar('{SUM}', number_format($p['price'] * $p['cnt'], 2));
        $parser->setTemplate("change_block_item.html");
        $productsRendered .= $parser->getResult();
    }
    
//    $parser->clear();
//    $parser->setVar('{ITEMS}', $productsRendered);
//    $parser->setTemplate("change_block.html");
//    $result .= $parser->getResult();
        
    return $productsRendered;
}

