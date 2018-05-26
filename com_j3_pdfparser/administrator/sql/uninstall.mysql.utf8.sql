DROP TABLE IF EXISTS `#__lpdfparser_`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_lpdfparser.%');

DROP TABLE IF EXISTS `#__product_templates`;

ALTER TABLE `#__hikashop_product` DROP `product_template_id`;  