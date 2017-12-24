DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`parent_id` int(11) NOT NULL,
`alias` varchar(50) NOT NULL,
`name` varchar(255) NOT NULL,
`description` mediumtext,
`status` tinyint(1) NOT NULL,
`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `parent_id` (`parent_id`,`alias`,`name`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`category_id` int(11) unsigned NOT NULL,
`name` varchar(255) NOT NULL,
`description` mediumtext,
`price` double NOT NULL DEFAULT '0',
`status` tinyint(1) NOT NULL DEFAULT '0',
`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`updated` timestamp NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `category_id` (`category_id`),
KEY `name` (`name`),
KEY `price` (`price`),
KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Структура таблицы `categories_products`
--

DROP TABLE IF EXISTS `categories_products`;
CREATE TABLE IF NOT EXISTS `categories_products` (
`category_id` int(11) NOT NULL,
`product_id` int(11) NOT NULL,
PRIMARY KEY (`category_id`,`product_id`),
KEY `fk_categories_products_cid_idx` (`category_id`),
KEY `fk_categories_products_pid_idx` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `categories_products`
--
ALTER TABLE `categories_products`
ADD CONSTRAINT `fk_categories_products_cid` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_categories_products_pid` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

INSERT INTO `julya-defproject.local`.`acl_permissions` (`name`) VALUES ('categories index');
INSERT INTO `julya-defproject.local`.`acl_permissions` (`name`) VALUES ('categories add');
INSERT INTO `julya-defproject.local`.`acl_permissions` (`name`) VALUES ('categories edit');
INSERT INTO `julya-defproject.local`.`acl_permissions` (`name`) VALUES ('categories delete');

