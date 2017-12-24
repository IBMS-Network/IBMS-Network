<?php

# Инициализируем профайлер - будем считать и процессорное время и потребление памяти
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

//define('PRODUCTS_LIMIT', 30000);
define('PROMOTIONS_LIMIT', 50);
define('PRODUCTS_PROMOTIONS_LIMIT', 4);
define('BLOCKS_LIMIT', 10);
define('PAGES_LIMIT', 20);
define('NEWS_LIMIT', 100);
define('METACATEGORIES_LIMIT', 100);
define('CATEGORIES_LIMIT', 3);
define('SUBCATEGORIES_LIMIT', 2);
define('SUBCATEGORIES_ATTRIBUTES_LIMIT', 4);
define('SUBCATEGORIES_ATTRIBUTES_VALUES_LIMIT', 4);
define('PRODUCT_GROUPS_IN_SUBCATEGORY_LIMIT', 100);
define('PRODUCTS_IN_GROUP_LIMIT', 5);
//define('SYNONYMS_LIMIT', 5000);
define('SYNONYMS_CATEGORY_LIMIT', 3);
define('ORDERS_LIMIT', 100);
define('ORDERS_PRODUCTS_LIMIT', 6);
define('USERS_LIMIT', 200);
define('USERS_COMPANIES_LIMIT', 3);

$productsAll = 100;
$xmlDoc = new DOMDocument();
$dom = new domDocument("1.0", "utf-8");

//if($_GET['xml'] == 'promotions') {
    $request = $dom->createElement("request");
    $dom->appendChild($request);
    $actions = $dom->createElement("actions");
    $actionProducts = $dom->createElement("action", "products");
    $actionPromotions = $dom->createElement("action", "promotions");
    $actionUsers = $dom->createElement("action", "clients");
    $actionOrders = $dom->createElement("action", "orders");
    $actionBlocks = $dom->createElement("action", "blocks");
    $actionPages = $dom->createElement("action", "pages");
    $actionNews = $dom->createElement("action", "news");
    $actions->appendChild($actionProducts);
    $actions->appendChild($actionPromotions);
    $actions->appendChild($actionUsers);
    $actions->appendChild($actionOrders);
    $actions->appendChild($actionBlocks);
    $actions->appendChild($actionPages);
    $actions->appendChild($actionNews);
    $request->appendChild($actions);
    
    $data = $dom->createElement("data");
    
    //add metacategories
    $counter = 1000;
    $metacategories = $dom->createElement("metacategories");
    for($i = 0; $i < METACATEGORIES_LIMIT; $i++) {
        $metacategory = $dom->createElement("metacategory");
        $id = $dom->createElement("id", $counter);
        $name = $dom->createElement("name", "Метакатегория $counter");
        $description = $dom->createElement("description", "Описание категории Метакатегория $counter");

        $synonyms = $dom->createElement("synonyms");
        for ($i1 = 0; $i1 < SYNONYMS_CATEGORY_LIMIT; $i1++) {
            $synonym = $dom->createElement("synonym", "синоним $i1");
            $synonyms->appendChild($synonym);
        }
        
        $status = $dom->createElement("status", 1);
        $secondary = $dom->createElement("secondary", 0);
        $icon = $dom->createElement("icon", 'image_programm_category.png');
        $weight = $dom->createElement("weight", 1);

        //add categories
        $categories = $dom->createElement("categories");
        for ($i1 = 0; $i1 < CATEGORIES_LIMIT; $i1++) {
            $counter++;
            $category = $dom->createElement("category");
            $id1 = $dom->createElement("id", $counter);
            $name1 = $dom->createElement("name", "Категория $counter");

            $synonyms1 = $dom->createElement("synonyms");
            for ($j = 0; $j < SYNONYMS_CATEGORY_LIMIT; $j++) {
                $synonym = $dom->createElement("synonym", "синоним $j");
                $synonyms1->appendChild($synonym);
            }

            $metas = $dom->createElement("metas");
            $title = $dom->createElement("title", "Категория мета титл $counter");
            $description1 = $dom->createElement("description", "Категория мета дескришин $counter");
            $keywords = $dom->createElement("keywords", "Категория мета кейвордс $counter");
            $metas->appendChild($title);
            $metas->appendChild($description1);
            $metas->appendChild($keywords);

            $description1 = $dom->createElement("description", "Описание категории $counter");
            $status1 = $dom->createElement("status", 1);
            $secondary1 = $dom->createElement("secondary", 0);
            $icon1 = $dom->createElement("icon", 'image1.png');
            $weight1 = $dom->createElement("weight", 1);

            //add subcategories
            $subcategories = $dom->createElement("subcategories");
            for($j = 0; $j < SUBCATEGORIES_LIMIT; $j++) {
                $counter++;
                $subcategory = $dom->createElement("subcategory");
                $id2 = $dom->createElement("id", $counter);
                $name2 = $dom->createElement("name", "Категория $counter");

                $synonyms2 = $dom->createElement("synonyms");
                for ($k = 0; $k < SYNONYMS_CATEGORY_LIMIT; $k++) {
                    $synonym = $dom->createElement("synonym", "синоним $k");
                    $synonyms2->appendChild($synonym);
                }

                $metas1 = $dom->createElement("metas");
                $title1 = $dom->createElement("title", "Категория мета титл $counter");
                $description2 = $dom->createElement("description", "Категория мета дескришин $counter");
                $keywords1 = $dom->createElement("keywords", "Категория мета кейвордс $counter");
                $metas1->appendChild($title1);
                $metas1->appendChild($description2);
                $metas1->appendChild($keywords1);

                $description2 = $dom->createElement("description", "Описание категории $counter");
                $status2 = $dom->createElement("status", 1);
                $secondary2 = $dom->createElement("secondary", 0);
                $icon2 = $dom->createElement("icon", 'image1.png');
                $weight2 = $dom->createElement("weight", 1);

                $ordinaryAttributes = $dom->createElement("attributes");
                for ($l = 0; $l < SUBCATEGORIES_ATTRIBUTES_LIMIT; $l++) {
                    $attribute = $dom->createElement("attribute");
                    $id3 = $dom->createElement("id", $l);
                    $hint = $dom->createElement("hint", "Подсказка для $l");
                    $name3 = $dom->createElement("name", "Аттрибут $l");
                    $values = $dom->createElement("values");
                    for ($l1 = 0; $l1 < SUBCATEGORIES_ATTRIBUTES_VALUES_LIMIT; $l1++) {
                        $value = $dom->createElement("value", $l1+1);
                        $values->appendChild($value);
                    }

                    $attribute->appendChild($id3);
                    $attribute->appendChild($hint);
                    $attribute->appendChild($name3);
                    $attribute->appendChild($values);

                    $ordinaryAttributes->appendChild($attribute);
                }

//                $specialAttributes = $dom->createElement("special_attributes");
//                for ($l = SUBCATEGORIES_ATTRIBUTES_LIMIT; $l < SUBCATEGORIES_ATTRIBUTES_LIMIT*2; $l++) {
//                    $attribute = $dom->createElement("attribute");
//                    $id3 = $dom->createElement("id", $l);
//                    $hint = $dom->createElement("hint", "Подсказка для $l");
//                    $name3 = $dom->createElement("name", "Аттрибут $l");
//                    $values = $dom->createElement("values");
//                    for ($l1 = 0; $l1 < SUBCATEGORIES_ATTRIBUTES_VALUES_LIMIT; $l1++) {
//                        $value = $dom->createElement("value", $l1+1);
//                        $values->appendChild($value);
//                    }
//
//                    $attribute->appendChild($id3);
//                    $attribute->appendChild($hint);
//                    $attribute->appendChild($name3);
//                    $attribute->appendChild($values);
//
//                    $specialAttributes->appendChild($attribute);
//                }
                
                //add products
                $counter2 = 0;

                $product_groups = $dom->createElement("product_groups");
                for ($l = 0; $l < PRODUCT_GROUPS_IN_SUBCATEGORY_LIMIT; $l++) {
                    $counter2++;
                    $group = $dom->createElement("product_group");

                    $idx = $dom->createElement("id", $l+1);
                    $namex = $dom->createElement("name", "Название группы $counter2");
                    $descriptionx = $dom->createElement("description", "Описание группы $counter2");
                    $metasx = $dom->createElement("metas");
                    $titlex = $dom->createElement("title", "Группа мета титл $counter2");
                    $descriptionx2 = $dom->createElement("description", "Группа мета дескришин $counter2");
                    $keywordsx = $dom->createElement("keywords", "Группа мета кейвордс $counter2");
                    $metasx->appendChild($titlex);
                    $metasx->appendChild($descriptionx2);
                    $metasx->appendChild($keywordsx);
                    $statusx = $dom->createElement("status", 1);
                    
                    $ordinaryAttributes1 = $dom->createElement("ordinary_attributes");
                    for ($l1 = 0; $l1 < SUBCATEGORIES_ATTRIBUTES_LIMIT; $l1++) {
                        $attribute = $dom->createElement("attribute");
                        $name3 = $dom->createElement("name", "Аттрибут $l1");
                        $values = $dom->createElement("values");
                        $value = $dom->createElement("value", 2);
                        $values->appendChild($value);
                        $attribute->appendChild($name3);
                        $attribute->appendChild($values);

                        $ordinaryAttributes1->appendChild($attribute);
                    }

                    $products = $dom->createElement("products");
                    for($l1 = 0; $l1 < PRODUCTS_IN_GROUP_LIMIT; $l1++) {
                        $product = $dom->createElement("product");
                        $id3 = $dom->createElement("id", $productsAll);
                        $name3 = $dom->createElement("name", "Товар $productsAll");
                        $delivery = $dom->createElement("delivery", "1-" . rand(3, 9) . " дня");
                        $images = $dom->createElement("images");
                        $image = $dom->createElement("image", 'image_shilovert_d_1_1.jpg');
                        $images->appendChild($image);
                        $videos = $dom->createElement("videos");
                        $video = $dom->createElement("video", 'image_shilovert_d_1_1.mp4');
                        $videos->appendChild($video);
                        $quantities = $dom->createElement("quantities");
                        $quantity = $dom->createElement("quantity");
                        $value = $dom->createElement("value", 2);
                        $min = $dom->createElement("min", 1);
                        $quantity->appendChild($value);
                        $quantity->appendChild($min);
                        $quantities->appendChild($quantity);
                        $quantity = $dom->createElement("quantity");
                        $value = $dom->createElement("value", 3);
                        $quantity->appendChild($value);
                        $quantities->appendChild($quantity);
                        $description3 = $dom->createElement("description", "Описание товара $productsAll");
                        $articul = $dom->createElement("articul", $productsAll . $i . $j . $l . $l1 . rand(10, 99));
                        $synonyms3 = $dom->createElement("synonyms");
                        $synonym = $dom->createElement("synonym", 'синоним 0');
                        $synonyms3->appendChild($synonym);
                        $code_outs = $dom->createElement("code_outs");
                        $code_out = $dom->createElement("code_out", 'code_outs 0');
                        $code_outs->appendChild($code_out);
                        $similars = $dom->createElement("similars");
                        $product_id = $dom->createElement("product_id", 101);
                        $similars->appendChild($product_id);
                        $prices = $dom->createElement("prices");
                        for ($k7 = 0; $k7 < 5; $k7++) {
                            $price = $dom->createElement("price", $k7+4);
                            $prices->appendChild($price);
                        }
                        $specialAttributes2 = $dom->createElement("special_attributes");
                        for ($k7 = 0; $k7 < 2; $k7++) {
                            $attribute = $dom->createElement("attribute");
                            $name4 = $dom->createElement("name", "Аттрибут $k7");
                            $values = $dom->createElement("values");
                            $value = $dom->createElement("value", 1);
                            $values->appendChild($value);
                            $attribute->appendChild($name4);
                            $attribute->appendChild($values);

                            $specialAttributes2->appendChild($attribute);
                        }
                        $status3 = $dom->createElement("status", 1);
                        $productsAll++;
                
                        $product->appendChild($id3);
                        $product->appendChild($name3);
                        $product->appendChild($delivery);
                        $product->appendChild($images);
                        $product->appendChild($videos);
                        $product->appendChild($quantities);
                        $product->appendChild($description3);
                        $product->appendChild($articul);
                        $product->appendChild($synonyms3);
                        $product->appendChild($code_outs);
                        $product->appendChild($similars);
                        $product->appendChild($prices);
                        $product->appendChild($specialAttributes2);
                        $product->appendChild($status3);
                        $products->appendChild($product);
                    }

                    $group->appendChild($idx);
                    $group->appendChild($namex);
                    $group->appendChild($descriptionx);
                    $group->appendChild($metasx);
                    $group->appendChild($statusx);
                    $group->appendChild($ordinaryAttributes1);
//                    $group->appendChild($specialAttributes1);
                    $group->appendChild($products);

                    $product_groups->appendChild($group);
                }

                $subcategory->appendChild($id2);
                $subcategory->appendChild($name2);
                $subcategory->appendChild($synonyms2);
                $subcategory->appendChild($metas1);
                $subcategory->appendChild($description2);
                $subcategory->appendChild($status2);
                $subcategory->appendChild($secondary2);
                $subcategory->appendChild($icon2);
                $subcategory->appendChild($weight2);
                $subcategory->appendChild($ordinaryAttributes);
                $subcategory->appendChild($specialAttributes);
                $subcategory->appendChild($product_groups);
                $subcategories->appendChild($subcategory);
            }

            $category->appendChild($id1);
            $category->appendChild($name1);
            $category->appendChild($synonyms1);
            $category->appendChild($metas);
            $category->appendChild($description1);
            $category->appendChild($status1);
            $category->appendChild($secondary1);
            $category->appendChild($icon1);
            $category->appendChild($weight1);
            $category->appendChild($subcategories);
            $categories->appendChild($category);
        }

        $metacategory->appendChild($id);
        $metacategory->appendChild($name);
        $metacategory->appendChild($synonyms);
        $metacategory->appendChild($description);
        $metacategory->appendChild($status);
        $metacategory->appendChild($secondary);
        $metacategory->appendChild($icon);
        $metacategory->appendChild($weight);
        $metacategory->appendChild($categories);
        $metacategories->appendChild($metacategory);
    }
    $data->appendChild($metacategories);
    
    //add promotions
    $promotions = $dom->createElement("promotions");
    $counter = 1;
    for ($i = 0; $i < PROMOTIONS_LIMIT; $i++) {
        if(!($i%10)) {
            $counter++;
        }
        $promotion = $dom->createElement("promotion");
        $id = $dom->createElement("id", $i+1);
        $description = $dom->createElement("description", "Акция $i");
        $persent = $dom->createElement("persent", rand(1, 99));
        $products = $dom->createElement("products");
        for($j = 0; $j < PRODUCTS_PROMOTIONS_LIMIT; $j++) {
            $productId = $dom->createElement("product_id", $j+$counter);
            $products->appendChild($productId);
        }
        $status = $dom->createElement("status", 1);
        $promotion->appendChild($id);
        $promotion->appendChild($description);
        $promotion->appendChild($persent);
        $promotion->appendChild($products);
        $promotion->appendChild($status);
        $promotions->appendChild($promotion);
    }
    $data->appendChild($promotions);
    

    
    //add clients
    $users = $dom->createElement("clients");
    $counter = 100;
    for ($i = 0; $i < USERS_LIMIT; $i++) {
        $user = $dom->createElement("client");
        $id = $dom->createElement("id", $i+1);
        $firstName = $dom->createElement("first_name", "Имя");
        $lastName = $dom->createElement("last_name", "Фамилия");
        $user_type_id = $dom->createElement("client_type_id", 1);
        $email = $dom->createElement("email", "email$i@email.com");
        $password = $dom->createElement("password", "123");
        $phone = $dom->createElement("phone", "1234567");
        $column_number = $dom->createElement("column_number", 0);
        $show_delivery = $dom->createElement("show_delivery", 1);
        $reg_date = $dom->createElement("reg_date", '2012-12-12');
        $status = $dom->createElement("status", 1);
        $address = $dom->createElement("address", "asdassd dasd assd a $i");
        $companies = $dom->createElement("companies");
        //add companies for user
        for($j = 0; $j < USERS_COMPANIES_LIMIT; $j++) {
            $company = $dom->createElement("company");
            $id1 = $dom->createElement("id", $j+$counter);
            $name = $dom->createElement("name", "dad asd");
            $requisite = $dom->createElement("requisite", "asda ad ad $j");
            $reg_date1 = $dom->createElement("reg_date", "2012-12-12");
            $status1 = $dom->createElement("status", 1);
            $address1 = $dom->createElement("address", "address sada $j");
            $company->appendChild($id1);
            $company->appendChild($name);
            $company->appendChild($requisite);
            $company->appendChild($reg_date1);
            $company->appendChild($status1);            
            $company->appendChild($address1);            
            $companies->appendChild($company);
        }
        $user->appendChild($id);
        $user->appendChild($firstName);
        $user->appendChild($lastName);
        $user->appendChild($user_type_id);
        $user->appendChild($email);
        $user->appendChild($password);
        $user->appendChild($phone);
        $user->appendChild($column_number);
        $user->appendChild($show_delivery);
        $user->appendChild($reg_date);
        $user->appendChild($status);
        $user->appendChild($address);
        $user->appendChild($companies);
        $users->appendChild($user);
    }
    $data->appendChild($users);
    
    //add orders
    $orders = $dom->createElement("orders");
//    $counter = 100;
    for ($i = 0; $i < ORDERS_LIMIT; $i++) {
        $order = $dom->createElement("order");
        $id = $dom->createElement("id", $i+1);
        $user_id = $dom->createElement("user_id", $i+1);
        $order_status = $dom->createElement("order_status", 2);
        $company_id = $dom->createElement("company_id", 1);
        $client = $dom->createElement("client", 1);
        $summary = $dom->createElement("summary", "fds fsdf  fsdf");
        $create_date = $dom->createElement("create_date", "2012-12-12");
        $delivery_date = $dom->createElement("delivery_date", "2012-12-12");
        $status = $dom->createElement("status", 1);
        $products = $dom->createElement("products");
        //add products for order
        for($j = 0; $j < ORDERS_PRODUCTS_LIMIT; $j++) {
            $product = $dom->createElement("product");
            $id1 = $dom->createElement("id", $j);
            $price = $dom->createElement("price", 5);
            $number = $dom->createElement("number", 5);
            $product->appendChild($id1);
            $product->appendChild($price);
            $product->appendChild($number);
            $products->appendChild($product);
        }
        $order->appendChild($id);
        $order->appendChild($user_id);
        $order->appendChild($order_status);
        $order->appendChild($company_id);
        $order->appendChild($client);
        $order->appendChild($summary);
        $order->appendChild($create_date);
        $order->appendChild($delivery_date);
        $order->appendChild($status);
        $order->appendChild($products);
        $orders->appendChild($order);
    }
    $data->appendChild($orders);
    
    //add blocks
    $blocks = $dom->createElement("blocks");
    $counter = 20;
    for ($i = 0; $i < BLOCKS_LIMIT; $i++) {
        $block = $dom->createElement("block");
        $id = $dom->createElement("id", $i+$counter);
        $title = $dom->createElement("title", "Блок $i");
        $description = $dom->createElement("description", "Описание блока $i");
        $content = $dom->createElement("content", "Содержимое блока $i");
        $createDate = $dom->createElement("create_date", '2012-12-12');
        $status = $dom->createElement("status", 1);
        $block->appendChild($id);
        $block->appendChild($title);
        $block->appendChild($description);
        $block->appendChild($content);
        $block->appendChild($createDate);
        $block->appendChild($status);
        $blocks->appendChild($block);
    }
    $data->appendChild($blocks);

    //add pages
    $pages = $dom->createElement("pages");
    $counter = 20;
    for ($i = 0; $i < PAGES_LIMIT; $i++) {
        $page = $dom->createElement("page");
        $id = $dom->createElement("id", $i+$counter);
        
        $metas = $dom->createElement("metas");
        $title1 = $dom->createElement("title", "Страница мета титл");
        $description1 = $dom->createElement("description", "Страница мета дескришин");
        $keywords = $dom->createElement("keywords", "Страница мета кейвордс");
        $metas->appendChild($title1);
        $metas->appendChild($description1);
        $metas->appendChild($keywords);
        
        $title = $dom->createElement("title", "Страница $i");
        $description = $dom->createElement("description", "Описание страницы $i");
        $content = $dom->createElement("content", "Содержимое страницы $i");
        $createDate = $dom->createElement("create_date", '2012-12-12');
        $status = $dom->createElement("status", 1);
        
        $page->appendChild($id);
        $page->appendChild($title);
        $page->appendChild($metas);
        $page->appendChild($description);
        $page->appendChild($content);
        $page->appendChild($createDate);
        $page->appendChild($status);
        $pages->appendChild($page);
    }
    $data->appendChild($pages);
    
    //add news
    $news = $dom->createElement("news");
    $counter = 20;
    for ($i = 0; $i < NEWS_LIMIT; $i++) {
        $newsItem = $dom->createElement("newsitem");
        $id = $dom->createElement("id", $i+$counter);
        $title = $dom->createElement("title", "Новость $i");
        
        $metas = $dom->createElement("metas");
        $title1 = $dom->createElement("title", "Новости мета титл");
        $description1 = $dom->createElement("description", "Новости мета дескришин");
        $keywords = $dom->createElement("keywords", "Новости мета кейвордс");
        $metas->appendChild($title1);
        $metas->appendChild($description1);
        $metas->appendChild($keywords);
        
        $description = $dom->createElement("description", "Описание новости $i");
        $content = $dom->createElement("content", "Содержимое новости $i");
        $createDate = $dom->createElement("create_date", '2012-12-12');
        $status = $dom->createElement("status", 1);
        
        $newsItem->appendChild($id);
        $newsItem->appendChild($title);
        $newsItem->appendChild($metas);
        $newsItem->appendChild($description);
        $newsItem->appendChild($content);
        $newsItem->appendChild($createDate);
        $newsItem->appendChild($status);
        $news->appendChild($newsItem);
    }
    $data->appendChild($news);
    
    $request->appendChild($data);
    $dom->save("test_max.xml");
//}

# Останавливаем профайлер
//$xhprof_data = xhprof_disable();    

# Сохраняем отчет и генерируем ссылку для его просмотра
/*include_once dirname(__FILE__) . "/3rdparty/xhprof/xhprof_lib/utils/xhprof_lib.php";
include_once dirname(__FILE__) . "/3rdparty/xhprof/xhprof_lib/utils/xhprof_runs.php";
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_test");
echo "Report: http://xhprof/index.php?run=$run_id&source=xhprof_test"; # Хост, который Вы настроили ранее на GUI профайлера
echo "\n";*/

echo "Done";