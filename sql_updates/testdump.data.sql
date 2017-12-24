-- MySQL dump 10.13  Distrib 5.6.16, for Win32 (x86)
--
-- Host: localhost    Database: cosmetics
-- ------------------------------------------------------
-- Server version	5.6.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acl_mobile_permissions`
--

DROP TABLE IF EXISTS `acl_mobile_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_mobile_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=910;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_mobile_permissions`
--

LOCK TABLES `acl_mobile_permissions` WRITE;
/*!40000 ALTER TABLE `acl_mobile_permissions` DISABLE KEYS */;
INSERT INTO `acl_mobile_permissions` VALUES (11,'categories add'),(13,'categories delete'),(12,'categories edit'),(9,'categories index'),(10,'categories view'),(1,'main index'),(16,'products add'),(18,'products delete'),(17,'products edit'),(14,'products index'),(15,'products view'),(6,'users add'),(8,'users delete'),(7,'users edit'),(4,'users index'),(2,'users login'),(3,'users logout'),(5,'users view');
/*!40000 ALTER TABLE `acl_mobile_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_mobile_permissionsroles`
--

DROP TABLE IF EXISTS `acl_mobile_permissionsroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_mobile_permissionsroles` (
  `perm_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`perm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=862;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_mobile_permissionsroles`
--

LOCK TABLES `acl_mobile_permissionsroles` WRITE;
/*!40000 ALTER TABLE `acl_mobile_permissionsroles` DISABLE KEYS */;
INSERT INTO `acl_mobile_permissionsroles` VALUES (1,1),(2,1),(1,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2),(13,2),(14,2),(15,2),(16,2),(17,2),(18,2);
/*!40000 ALTER TABLE `acl_mobile_permissionsroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_mobile_roles`
--

DROP TABLE IF EXISTS `acl_mobile_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_mobile_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_mobile_roles`
--

LOCK TABLES `acl_mobile_roles` WRITE;
/*!40000 ALTER TABLE `acl_mobile_roles` DISABLE KEYS */;
INSERT INTO `acl_mobile_roles` VALUES (1,'guest'),(2,'user');
/*!40000 ALTER TABLE `acl_mobile_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_mobile_users`
--

DROP TABLE IF EXISTS `acl_mobile_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_mobile_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mob_role_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_mob_users_rid_idx` (`mob_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_mobile_users`
--

LOCK TABLES `acl_mobile_users` WRITE;
/*!40000 ALTER TABLE `acl_mobile_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `acl_mobile_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_permissions`
--

DROP TABLE IF EXISTS `acl_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=481;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_permissions`
--

LOCK TABLES `acl_permissions` WRITE;
/*!40000 ALTER TABLE `acl_permissions` DISABLE KEYS */;
INSERT INTO `acl_permissions` VALUES (18,'admins add'),(17,'admins delete'),(16,'admins edit'),(52,'admins index'),(27,'admins staticpages'),(75,'brands add'),(77,'brands delete'),(76,'brands edit'),(74,'brands index'),(54,'categories add'),(55,'categories delete'),(56,'categories edit'),(53,'categories index'),(67,'countries add'),(69,'countries delete'),(68,'countries edit'),(66,'countries index'),(51,'main index'),(48,'mobpermissions add'),(50,'mobpermissions delete'),(49,'mobpermissions edit'),(47,'mobpermissions index'),(44,'mobroles add'),(46,'mobroles delete'),(45,'mobroles edit'),(43,'mobroles index'),(71,'models add'),(73,'models delete'),(72,'models edit'),(70,'models index'),(83,'news add'),(85,'news delete'),(84,'news edit'),(82,'news index'),(79,'orders add'),(81,'orders delete'),(80,'orders edit'),(78,'orders index'),(24,'permissions add'),(26,'permissions delete'),(25,'permissions edit'),(23,'permissions index'),(58,'products add'),(59,'products delete'),(60,'products edit'),(57,'products index'),(22,'roles add'),(20,'roles edit'),(19,'roles index'),(37,'seo add'),(38,'seo delete'),(36,'seo edit'),(35,'seo index'),(32,'seofields add'),(34,'seofields delete'),(33,'seofields edit'),(31,'seofields index'),(28,'staticpages add'),(30,'staticpages delete'),(29,'staticpages edit'),(65,'staticpages index'),(40,'users add'),(42,'users delete'),(41,'users edit'),(39,'users index');
/*!40000 ALTER TABLE `acl_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_permissionsroles`
--

DROP TABLE IF EXISTS `acl_permissionsroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_permissionsroles` (
  `perm_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`perm_id`),
  KEY `fk_acl_permissionsroles_pid_idx` (`perm_id`),
  KEY `fk_acl_permissionsroles_rid_idx` (`role_id`),
  CONSTRAINT `fk_acl_permissionsroles_pid` FOREIGN KEY (`perm_id`) REFERENCES `acl_permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_acl_permissionsroles_rid` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1489;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_permissionsroles`
--

LOCK TABLES `acl_permissionsroles` WRITE;
/*!40000 ALTER TABLE `acl_permissionsroles` DISABLE KEYS */;
INSERT INTO `acl_permissionsroles` VALUES (9,1),(16,1),(16,2),(16,3),(17,1),(17,2),(17,3),(18,1),(18,2),(18,3),(19,1),(19,3),(20,1),(20,3),(22,1),(22,3),(23,1),(23,3),(24,1),(24,3),(25,1),(25,3),(26,1),(26,3),(27,1),(27,2),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(51,2),(52,1),(52,2),(53,1),(53,2),(54,1),(54,2),(55,1),(55,2),(56,1),(56,2),(57,1),(58,1),(59,1),(60,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1);
/*!40000 ALTER TABLE `acl_permissionsroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_roles`
--

DROP TABLE IF EXISTS `acl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_roles`
--

LOCK TABLES `acl_roles` WRITE;
/*!40000 ALTER TABLE `acl_roles` DISABLE KEYS */;
INSERT INTO `acl_roles` VALUES (1,'admin'),(2,'тестовая роль И ш !');
/*!40000 ALTER TABLE `acl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_admins_rid_idx` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$10$lNECFYqMv4SidVflpjUBUOBHLSN6Y53iML6oHDygIIgSJfAJJ3HJG',1),(4,'test2','$2y$10$XNa./rceoe.EaRhvJVMs2ePOM9LQGQI0yeMs4hiZ.amPx5Dc5XbUS',2);
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107774 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attributes`
--

LOCK TABLES `attributes` WRITE;
/*!40000 ALTER TABLE `attributes` DISABLE KEYS */;
INSERT INTO `attributes` VALUES (1,'Светотехнические характеристики',' Мощность: 6 люмен   Материал корпуса: Пластик   Лампы: 1 красный светодиод, 1 криптоновая лампа  Время работы: 500 часов   Батареи: 2 алкалиновые батареи ААА   Водонепроницаемость: до 100 м   Удобный шнурок   Цвет корпуса – оранжевый   Вес: 42 грамма '),(2,'Назначение','Фонарь для дайвинга '),(3,'Компания производитель','Princeton Tec, 5198 Rt. 130, Bordentown, NJ 08505 USA СПРАВКА\r\n\r\nКомпания \"Princeton Tec\" образована в 1975 году, в городе Трентоне, штате Нью-Джерси (США). Изначальная специализация - подводное осветительное оборудование. Этой теме и сейчас в компании уделяется большое внимание. В последние годы, когда на рынок фонарей начали поступать надежные источники света - светодиоды большой яркости, компания ввела в портфель своих предложений светодиодные традиционные, налобные и подводные фонари и фонари для велосипедистов с оригинальными креплениями их на раму и руль велосипеда.   В производстве фонарей \"Princeton Tec\" задействованы самые передовые технологии и материалы. Противоударная конструкция, ударопрочный пластик корпусов, рефлекторов, линз, - позволяют фонарям сохранять работоспособность даже после существенных ударов о камни. '),(4,'Гарантия (кол-во лет)','10  СПРАВКА\r\n\r\nВнимание! Гарантия не распространяется на: покрытия деталей, темляки, клипсы, карабины, защитное стекло, ремни налобных фонарей, повреждения, связанные с чрезвычайными нагрузками, повлекшие разрушение деталей корпуса, на элементы питания и повреждения, связанные с их протечкой, а также со следами самостоятельного ремонта.<?XML:NAMESPACE PREFIX = O /></SPAN></P>   '),(5,'Страна изготовитель','США '),(6,'Светотехнические характеристики','Фонарь сигнальный стробоскопический для дайвинга.    Рабочая глубина погружения - до 100 м.     Применена ксеноновая газоразрядная лампа.     Частота - 70 вспышек в мин.    Время свечения – до 8 часов.    Питание – 1 х АА.    Вес - 96 гр.     Цвет - красный.   '),(7,'Светотехнические характеристики','Фонарь сигнальный стробоскопический для дайвинга.  Рабочая глубина погружения - до 100 м.  Применена ксеноновая газоразрядная лампа.  Частота - 70 вспышек в мин.  Время свечения – до 8 часов.  Питание – 1 х АА.  Вес - 96 гр.   Цвет - красный.       '),(8,'Светотехнические характеристики','Рабочая глубина погружения - до 100 м.     Световой поток - 45 люменов.    Количество светодиодов – 1.    Время свечения - 50 часов.    Питание – 4 x AA.    Вес - 197 гр.     Цвет - черный.   '),(9,'Назначение','Фонарь светодиодный для дайвинга.      '),(10,'Дополнительно','Коллиматорная система LED OPTIC. Система состоит из линз и рефлектора. Она позволяет максимально собрать весь свет, излучаемый светодиодом и направить его параллельным пучком в нужном направлении. Такая система гораздо эффективнее обычных рефлекторных схем, разработанных другими производителями. С ее применением фонарь светит ярче и дальше.       '),(11,'Светотехнические характеристики','Длина - 160 мм   Вес - 220 гр   Световой поток - 150 лм.   Питание - 4 х AA (в комплекте)   Мощность - 16,8   Время свечения - 20 ч.     Эффективная дальность свечения - 180 м   Глубина погружения – 60 м.   Количество светодиодов - 1.   Нейлоновый темляк. Картонная упаковка.     '),(12,'Назначение','Фонарь светодиодный для дайвинга.  '),(13,'Компания производитель','Zweibr&#252;der Optoelectronics GmbH & Co.KG | Kronenstra&#223;e 5-7 | 42699 Solingen  '),(14,'Гарантия (кол-во лет)','5 '),(15,'Страна изготовитель','Германия  '),(16,'Светотехнические характеристики','Световой поток: 40 люмен.  Время свечения: 50 часов.  Длина: 125 мм.  Питание: 4 x AAA (в комплекте).  Вес - 120 г.  Количество светодиодов - 1.  Глубина погружения – 60 м  Картонная упаковка '),(17,'Назначение','Фонарь для дайвинга '),(18,'Дополнительно','Обладатель Международной Премии – iF Product Design Award – одной из наиболее престижных премий в области промышленного дизайна. С 1953 года премия служит своеобразным знаком качества продукта.      '),(19,'Светотехнические характеристики','Материал корпуса: Пластик  Источник света: 1 лампочка Xenon  Световой поток: 112 Лм  Дальность свечения: 105 м  Режимов работы: 2  Батареи: 4 алкалиновые батареи С  Время роботы: 10 часов  Вес: 683 гр.  Водостойкость: IPX8 (погружение на глубину до 100 м)  Ремешок для переноски  Эргономичная \"пистолетная\" рукоять  Цвет корпуса – черный '),(20,'Назначение','Фонарь для дайвинга '),(21,'Компания производитель','Princeton Tec, 5198 Rt. 130, Bordentown, NJ 08505 USA СПРАВКА\r\n\r\nКомпания \"Princeton Tec\" образована в 1975 году, в городе Трентоне, штате Нью-Джерси (США). Изначальная специализация - подводное осветительное оборудование. Этой теме и сейчас в компании уделяется большое внимание. В последние годы, когда на рынок фонарей начали поступать надежные источники света - светодиоды большой яркости, компания ввела в портфель своих предложений светодиодные традиционные, налобные и подводные фонари и фонари для велосипедистов с оригинальными креплениями их на раму и руль велосипеда.   В производстве фонарей \"Princeton Tec\" задействованы самые передовые технологии и материалы. Противоударная конструкция, ударопрочный пластик корпусов, рефлекторов, линз, - позволяют фонарям сохранять работоспособность даже после существенных ударов о камни. '),(22,'Гарантия (кол-во лет)','10  СПРАВКА\r\n\r\nВнимание! Гарантия не распространяется на: покрытия деталей, темляки, клипсы, карабины, защитное стекло, ремни налобных фонарей, повреждения, связанные с чрезвычайными нагрузками, повлекшие разрушение деталей корпуса, на элементы питания и повреждения, связанные с их протечкой, а также со следами самостоятельного ремонта.<?XML:NAMESPACE PREFIX = O /></SPAN></P>   '),(23,'Страна изготовитель','США '),(24,'Светотехнические характеристики','Материал корпуса: Пластик  Мощность: 126 люмен  Лампы: 1 светодиод Maxbright  Время работы: 30 часов  Батареи: 8 алкалиновых батареи АА (в комплекте)  Удобный шнурок  Вес: 365 грамм  Размер фонаря: 17 см х 5 см х 5 см  Водонепроницаемость: до 100 м  Дальность света: 70 м  Официальный сертификат международной лаборатории Underwriters Laboratories (UL), подтверждающий гарантию качества  Цвет корпуса – желтый '),(25,'Назначение','Фонарь для дайвинга '),(26,'Компания производитель','Princeton Tec, 5198 Rt. 130, Bordentown, NJ 08505 USA СПРАВКА\r\n\r\nКомпания \"Princeton Tec\" образована в 1975 году, в городе Трентоне, штате Нью-Джерси (США). Изначальная специализация - подводное осветительное оборудование. Этой теме и сейчас в компании уделяется большое внимание. В последние годы, когда на рынок фонарей начали поступать надежные источники света - светодиоды большой яркости, компания ввела в портфель своих предложений светодиодные традиционные, налобные и подводные фонари и фонари для велосипедистов с оригинальными креплениями их на раму и руль велосипеда.   В производстве фонарей \"Princeton Tec\" задействованы самые передовые технологии и материалы. Противоударная конструкция, ударопрочный пластик корпусов, рефлекторов, линз, - позволяют фонарям сохранять работоспособность даже после существенных ударов о камни. '),(27,'Гарантия (кол-во лет)','10  СПРАВКА\r\n\r\nВнимание! Гарантия не распространяется на: покрытия деталей, темляки, клипсы, карабины, защитное стекло, ремни налобных фонарей, повреждения, связанные с чрезвычайными нагрузками, повлекшие разрушение деталей корпуса, на элементы питания и повреждения, связанные с их протечкой, а также со следами самостоятельного ремонта.<?XML:NAMESPACE PREFIX = O /></SPAN></P>   '),(28,'Страна изготовитель','США '),(29,'Светотехнические характеристики','Рабочая глубина погружения - до 100 м.     Световой поток – до 95 люменов.    Количество светодиодов – 1.    Время свечения – до 30 часов.     Питание – 8 x AA.    Вес - 365 гр.     Цвет - черный.   '),(30,'Назначение','Компактный и яркий фонарь с увеличенным временем свечения.      '),(31,'Дополнительно','Коллиматорная система LED OPTIC. Система состоит из линз и рефлектора. Она позволяет максимально собрать весь свет, излучаемый светодиодом и направить его параллельным пучком в нужном направлении. Такая система гораздо эффективнее обычных рефлекторных схем, разработанных другими производителями. С ее применением фонарь светит ярче и дальше.       '),(32,'Светотехнические характеристики','Световой поток: 85 люмен.  Время свечения: 50 часов.  Длина: 160 мм.  Питание: 4 x AA (в комплекте).  Вес - 220 г.  Количество светодиодов - 1.  Глубина погружения – 60 м  Картонная упаковка '),(33,'Назначение','Фонарь для дайвинга '),(34,'Дополнительно','Обладатель Международной Премии – iF Product Design Award – одной из наиболее престижных премий в области промышленного дизайна. С 1953 года премия служит своеобразным знаком качества продукта.       ');
/*!40000 ALTER TABLE `attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (98,'Led Lenser','images/catalog/brands/flag_led_lenser.jpg',''),(104,'Princeton Tec','images/catalog/brands/flag_prinstone.jpg','');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `updated` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_categories_categories1_idx` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=237;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (80,10,'Классические','',1,'2014-03-10 00:17:34','2014-03-10 00:17:34');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Финляндия','images/catalog/countries/fin_flag.jpg'),(2,'Норвегия','images/catalog/countries/norv_flag.jpg'),(3,'Швеция','images/catalog/countries/flag_sweden_knives.jpg'),(4,'Сальвадор','images/catalog/countries/salva_flag.jpg'),(5,'ЮАР','images/catalog/countries/aur_flag.jpg'),(6,'Непал','images/catalog/countries/nepal_flag.jpg'),(7,'Германия','images/catalog/countries/nemetskie_nozhi.jpg'),(8,'Япония','images/catalog/countries/japonskie_nozhi.jpg'),(9,'Италия','images/catalog/countries/italyanskie_nozhi.jpg'),(10,'Швейцария','images/catalog/countries/shveicarskie_nozhi.jpg'),(11,'США','images/catalog/countries/amerikanskie_nozhi.jpg'),(12,'Испания','images/catalog/countries/ispanskie_nozhi.jpg'),(13,'Франция','images/catalog/countries/france.jpg'),(14,'Великобритания','images/catalog/countries/gb.jpg');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity_types`
--

DROP TABLE IF EXISTS `entity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entity_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entity_types`
--

LOCK TABLES `entity_types` WRITE;
/*!40000 ALTER TABLE `entity_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `entity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `create_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `direction` tinyint(1) NOT NULL DEFAULT '0',
  `additional` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `key_entity_id` (`entity_id`),
  KEY `key_entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(2) unsigned NOT NULL,
  `width` int(5) unsigned NOT NULL,
  `height` int(5) unsigned NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `th` tinyint(1) NOT NULL,
  `weight` bigint(20) NOT NULL,
  `full_key` varchar(255) NOT NULL,
  `orig_name` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `full_key` (`full_key`),
  UNIQUE KEY `name` (`name`,`type`,`width`,`height`,`th`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images_garbage`
--

DROP TABLE IF EXISTS `images_garbage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images_garbage` (
  `id` int(11) NOT NULL,
  `createdate` datetime DEFAULT '1970-01-01 00:00:00',
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images_garbage`
--

LOCK TABLES `images_garbage` WRITE;
/*!40000 ALTER TABLE `images_garbage` DISABLE KEYS */;
/*!40000 ALTER TABLE `images_garbage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES (1,'Handhelds'),(2,'Professional Divers'),(3,'Led Lenser');
/*!40000 ALTER TABLE `models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES (1,'seo'),(2,'static_pages');
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` varchar(5000) NOT NULL,
  `author_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `slug` varchar(270) NOT NULL,
  `img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (2,'третья новость','третья новостьтретья новостьтретья новостьтретья новостьтретья новость',1,'2014-03-20 03:27:56','2014-03-20 03:27:56','triet-ia-novost','');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderproducts`
--

DROP TABLE IF EXISTS `orderproducts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderproducts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `quantity` int(10) NOT NULL DEFAULT '1',
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderproducts`
--

LOCK TABLES `orderproducts` WRITE;
/*!40000 ALTER TABLE `orderproducts` DISABLE KEYS */;
INSERT INTO `orderproducts` VALUES (1,1,3,1,2035),(2,1,8323,1,33360),(3,2,2,1,2035),(4,2,8273,1,8304),(5,3,2083,1,374),(6,3,2218,1,2050),(7,4,2,1,2035),(8,4,3,1,2035);
/*!40000 ALTER TABLE `orderproducts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_address` varchar(255) DEFAULT NULL,
  `delivery` varchar(150) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_phone` varchar(50) NOT NULL,
  `comments` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,0,'hyhyg','31/03/2014 с 10 до 18','sfdr','sfrfd@ya.ru','+7414141414',''),(2,0,'artem.kl@rambler.r','15/04/2014 с 10 до 18','Артем','artem.klimovich@gmail.com','79165626020',''),(3,0,'Севастопольский 5','22/06/2014 с 18 до 22','Андрей','','+79167904981',''),(4,0,'Город улица 45.','04/07/2014 с 18 до 22','asas','dfdfdf@dfd.ru','+75652233665',''),(5,0,'Пинск','09/09/2014 ','','','','');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pages_mid_idx` (`module_id`),
  KEY `name` (`name`),
  CONSTRAINT `fk_pages_mid` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages_static_pages`
--

DROP TABLE IF EXISTS `pages_static_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages_static_pages` (
  `static_page_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  PRIMARY KEY (`static_page_id`,`page_id`),
  KEY `fk_pages_static_pages_pid_idx` (`page_id`),
  CONSTRAINT `fk_pages_static_pages_pid` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_pages_static_pages_spid` FOREIGN KEY (`static_page_id`) REFERENCES `static_pages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages_static_pages`
--

LOCK TABLES `pages_static_pages` WRITE;
/*!40000 ALTER TABLE `pages_static_pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages_static_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `name` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(300) NOT NULL,
  `content` text NOT NULL,
  `articul` varchar(50) NOT NULL,
  `code` varchar(50) NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `model_id` int(11) NOT NULL,
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `product_model_index` (`model_id`),
  KEY `product_country_index` (`country_id`),
  KEY `product_brand_index` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=521;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,80,'Фонарь светодиодный для дайвинга Princeton Tec ECO FLARE EF-2-RR','','','EF-2-RR','22219',750,1,'2014-03-10 23:00:40',1,'images/catalog/products/cDkmx4rl.jpg',11,104),(2,80,'Фонарь сигнальный стробоскопический для дайвинга AS-10-RR-CP','','','AS-10-RR-CP','14181',2035,1,'2014-03-10 23:00:41',1,'images/catalog/products/as-10-rr-cp_enl_otredaktirovano-1.jpg',NULL,104),(3,80,'Фонарь сигнальный стробоскопический для дайвинга AS-10-BK-CP','','','AS-10-BK-CP','14182',2035,1,'2014-03-10 23:00:41',1,'images/catalog/products/store_apendix_large3599_1176_otredaktirovano-1.jpg',NULL,104),(4,80,'Фонарь светодиодный для дайвинга IMPXL-BK','','','IMPXL-BK','14183',2255,1,'2014-03-10 23:00:41',1,'images/catalog/products/vtsts_otredaktirovano-1.jpg',NULL,104),(5,80,'Фонарь для дайвинга светодиодный D14 7456-M','','','7456-M','19769',2508,1,'2014-03-10 23:00:41',2,'images/catalog/products/207456_2193_01111_kopija.jpg',7,98),(6,80,'Фонарь для дайвинга 7486-М','','','Код товара:','14144',2585,1,'2014-03-10 23:00:42',3,'images/catalog/products/rpavrpkuru_otredaktirovano-1.jpg',NULL,98),(7,80,'Фонарь светодиодный для дайвинга Princeton Tec MINIWAVE-II LED BLACK TEC-4CII-BK','','','TEC-4CII-BK','21961',3200,1,'2014-03-10 23:00:43',1,'images/catalog/products/OjK3KyRh.jpg',11,104),(8,80,'Фонарь светодиодный для дайвинга Princeton Tec Torrent LED Yellow TORR-NY','','','TORR-NY','21960',3650,1,'2014-03-10 23:00:43',1,'images/catalog/products/08aWGqog.jpg',11,104),(9,80,'Фонарь светодиодный для дайвинга TORR-BK','','','TORR-BK','14184',3685,1,'2014-03-10 23:00:44',1,'images/catalog/products/mkarekr_otredaktirovano-1.jpg',NULL,104),(10,80,'Фонарь для дайвинга 7456-М','','','Код товара:','14145',3795,1,'2014-03-10 23:00:44',3,'images/catalog/products/8dDakVA3.jpg',NULL,98);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productsattributes`
--

DROP TABLE IF EXISTS `productsattributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsattributes` (
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_id`),
  UNIQUE KEY `product_attribute_id_uniq` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productsattributes`
--

LOCK TABLES `productsattributes` WRITE;
/*!40000 ALTER TABLE `productsattributes` DISABLE KEYS */;
INSERT INTO `productsattributes` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(2,6),(3,7),(4,8),(4,9),(4,10),(5,11),(5,12),(5,13),(5,14),(5,15),(6,16),(6,17),(6,18),(7,19),(7,20),(7,21),(7,22),(7,23),(8,24),(8,25),(8,26),(8,27),(8,28),(9,29),(9,30),(9,31),(10,32),(10,33),(10,34);
/*!40000 ALTER TABLE `productsattributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productsdiscounts`
--

DROP TABLE IF EXISTS `productsdiscounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsdiscounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `discount` int(2) NOT NULL DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ended` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productsdiscounts`
--

LOCK TABLES `productsdiscounts` WRITE;
/*!40000 ALTER TABLE `productsdiscounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `productsdiscounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_fields`
--

DROP TABLE IF EXISTS `seo_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_fields`
--

LOCK TABLES `seo_fields` WRITE;
/*!40000 ALTER TABLE `seo_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_fields_values`
--

DROP TABLE IF EXISTS `seo_fields_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_fields_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` mediumtext,
  PRIMARY KEY (`id`),
  KEY `fk_seo_fields_values_fid_idx` (`field_id`),
  KEY `fk_seo_fields_values_mid_idx` (`module_id`),
  KEY `fk_seo_fields_values_pid_idx` (`page_id`),
  CONSTRAINT `fk_seo_fields_values_fid` FOREIGN KEY (`field_id`) REFERENCES `seo_fields` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_seo_fields_values_mid` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_seo_fields_values_pid` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_fields_values`
--

LOCK TABLES `seo_fields_values` WRITE;
/*!40000 ALTER TABLE `seo_fields_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `seo_fields_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `name_rus` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_search` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_service` (`parent_id`),
  CONSTRAINT `services_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=5461 COMMENT='list services for permissions and admin statistic';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'users','Пользователи',NULL,0),(2,'category','Категория',NULL,0),(3,'products','Продукция',NULL,0);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicesimages`
--

DROP TABLE IF EXISTS `servicesimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicesimages` (
  `service_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  KEY `service_id` (`service_id`,`item_id`,`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicesimages`
--

LOCK TABLES `servicesimages` WRITE;
/*!40000 ALTER TABLE `servicesimages` DISABLE KEYS */;
/*!40000 ALTER TABLE `servicesimages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `static_pages`
--

DROP TABLE IF EXISTS `static_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(45) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created` (`created`),
  KEY `updated` (`updated`),
  KEY `index_author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `static_pages`
--

LOCK TABLES `static_pages` WRITE;
/*!40000 ALTER TABLE `static_pages` DISABLE KEYS */;
INSERT INTO `static_pages` VALUES (1,'responsibility','Корпоративная ответственность','<p>Информация о корпоративной ответственности фирмы</p>','2014-03-13 07:18:42','2014-03-16 12:16:18',1),(2,'vacancy','Вакансии','<p>Перечисление вакансий компании</p>','2014-03-13 07:49:14','2014-03-16 12:16:18',1),(3,'invest','Инвесторам','<P>Мы предлагаем широкие перспективы для инвестирования</p>','2014-03-13 07:50:29','2014-03-16 12:16:18',1),(4,'partner','Партнерская программа','<p>Наша партнерская программа очень хороша!</p>','2014-03-13 07:51:20','2014-03-16 12:16:18',1),(5,'faq','FAQ','<p>Самые актуальные вопросы и ответы</p>','2014-03-13 07:52:11','2014-03-16 12:16:18',1),(6,'feedback','Обратная связь','<p>Похоже здесь должна быть страница контактов</p>','2014-03-13 07:54:28','2014-03-16 12:16:18',1),(7,'moneyback','Возвраты','<p>Условия возвратов продукции клиентами','2014-03-13 07:58:33','2014-03-16 12:16:18',1);
/*!40000 ALTER TABLE `static_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_adresses`
--

DROP TABLE IF EXISTS `user_adresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_adresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_user_adresses_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_adresses`
--

LOCK TABLES `user_adresses` WRITE;
/*!40000 ALTER TABLE `user_adresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_adresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `password` varchar(70) NOT NULL,
  `email` varchar(120) NOT NULL,
  `reg_date` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `token` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_token` (`id`,`token`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=106;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Name','Surname','fcea920f7412b5da7be0cf42b8c93759','user@domain.com','2013-09-10 15:48:00',1,NULL),(2,'test','test2','$2y$10$YAViDxPvnycUTJ0wnpLjY.y/rFzZdszd1ZiTtopuujFzDG.rM3NIO','test@test.com','2014-03-06 07:31:16',1,NULL),(3,'asdasd','asdfds','$2y$10$fWfPUtiOToS9M542F4RCKuRdLUtdyDf114HeiIYVnbKxo7.kKpvu2','aa@aa.com','2014-03-11 07:58:06',1,NULL),(4,'Ana','tolik','$2y$10$xDsYSw2WNm7NsmniPkpXuub6rB7X9qvyUPL4hMihEnQ9mYP9T29QC','aa3@aa.com','2014-03-17 01:28:04',1,NULL),(5,'Дмитрий','Коротков','$2y$10$O4UDGaNi/LyKG1TnsMPboeGYRx6.ftwDUX3in3ObyTy.k29qXGozi','komsomolez73@gmail.com','2014-08-02 05:29:29',1,NULL),(6,'АНДРЕЙ','В','$2y$10$hkbFLVpKl1UJ.cW0MyQYIO987fvbbJdLDOG0yHfztqc6kgAU5aSvm','andy-v@rambler.ru','2014-08-29 20:17:15',1,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-09  0:35:17
