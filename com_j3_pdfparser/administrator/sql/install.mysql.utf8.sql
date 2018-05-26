CREATE TABLE IF NOT EXISTS `#__lpdfparser_` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`order_number` VARCHAR(255)  NOT NULL ,
`username` VARCHAR(255)  NOT NULL ,
`from_address` VARCHAR(255)  NOT NULL ,
`to_address` VARCHAR(255)  NOT NULL ,
`subject` VARCHAR(255)  NOT NULL ,
`mail_body` VARCHAR(255)  NOT NULL ,
`created_at` DATETIME NOT NULL ,
`downloadlink` VARCHAR(255)  NOT NULL ,
 `hika_order_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Pdf Parser','com_lpdfparser.pdfparser','{"special":{"dbtable":"#__lpdfparser_","key":"id","type":"Pdfparser","prefix":"LpdfparserTable"}}', '{"formFile":"administrator\/components\/com_lpdfparser\/models\/forms\/pdfparser.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_lpdfparser.pdfparser')
) LIMIT 1;
 

CREATE TABLE IF NOT EXISTS `#__hikashop_product` (
  `product_id` int(11) unsigned NOT NULL,
  `product_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_quantity` int(11) NOT NULL DEFAULT '-1',
  `product_code` varchar(255) NOT NULL,
  `product_published` tinyint(4) NOT NULL DEFAULT '0',
  `product_hit` int(11) unsigned NOT NULL DEFAULT '0',
  `product_created` int(11) unsigned NOT NULL DEFAULT '0',
  `product_sale_start` int(10) unsigned NOT NULL DEFAULT '0',
  `product_sale_end` int(10) unsigned NOT NULL DEFAULT '0',
  `product_delay_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_tax_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_type` varchar(255) NOT NULL DEFAULT '',
  `product_vendor_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_manufacturer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_url` varchar(255) NOT NULL,
  `product_weight` decimal(12,3) unsigned NOT NULL DEFAULT '0.000',
  `product_keywords` text NOT NULL,
  `product_weight_unit` varchar(255) NOT NULL DEFAULT 'kg',
  `product_modified` int(10) unsigned NOT NULL DEFAULT '0',
  `product_meta_description` text NOT NULL,
  `product_dimension_unit` varchar(255) NOT NULL DEFAULT 'm',
  `product_width` decimal(12,3) NOT NULL DEFAULT '0.000',
  `product_length` decimal(12,3) NOT NULL DEFAULT '0.000',
  `product_height` decimal(12,3) NOT NULL DEFAULT '0.000',
  `product_max_per_order` int(10) unsigned DEFAULT '0',
  `product_access` varchar(255) NOT NULL DEFAULT 'all',
  `product_group_after_purchase` varchar(255) NOT NULL DEFAULT '',
  `product_min_per_order` int(10) unsigned DEFAULT '0',
  `product_contact` smallint(5) unsigned NOT NULL DEFAULT '0',
  `product_display_quantity_field` smallint(5) NOT NULL DEFAULT '0',
  `product_last_seen_date` int(10) unsigned DEFAULT '0',
  `product_sales` int(10) unsigned DEFAULT '0',
  `product_waitlist` smallint(5) unsigned NOT NULL DEFAULT '0',
  `product_layout` varchar(255) NOT NULL DEFAULT '',
  `product_average_score` float NOT NULL,
  `product_total_vote` int(11) NOT NULL DEFAULT '0',
  `product_page_title` varchar(255) NOT NULL DEFAULT '',
  `product_alias` varchar(255) NOT NULL DEFAULT '',
  `product_price_percentage` decimal(15,7) NOT NULL DEFAULT '0.0000000',
  `product_msrp` decimal(15,7) DEFAULT '0.0000000',
  `product_canonical` varchar(255) NOT NULL DEFAULT '',
  `product_warehouse_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_quantity_layout` varchar(255) NOT NULL DEFAULT '',
  `product_sort_price` decimal(17,5) NOT NULL DEFAULT '0.00000',
  `product_description_raw` text,
  `product_description_type` varchar(255) DEFAULT NULL,
  `product_option_method` smallint(5) unsigned NOT NULL DEFAULT '0',
  `product_condition` varchar(255) DEFAULT NULL,
  `screensize` text,
  `os` text,
  `bluetooth` text,
  `ram` text,
  `public` text,
  `genre` text
  /*`product_template_id` int(11) NOT NULL*/
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `#__hikashop_product` ADD COLUMN `product_template_id` int NOT NULL;
  
CREATE TABLE IF NOT EXISTS `#__product_templates` (
  `template_id` int(11) NOT NULL,
  `template_name` text NOT NULL,
  `createdon` datetime NOT NULL,
  `updatedon` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `#__product_templates` (`template_id`, `template_name`, `createdon`, `updatedon`) VALUES
(1, 'pdf_template_one.php', '2018-03-19 12:04:01', '0000-00-00 00:00:00'),
(2, 'pdf_template_two.php', '2018-03-19 12:04:18', '0000-00-00 00:00:00');
