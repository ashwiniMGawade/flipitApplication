<?php

$site->query("DELETE FROM `popular_shop`");
$site->query("DELETE FROM `popular_code`");
$site->query("DELETE FROM `view_count`");
$site->query("DELETE FROM `ref_offer_category`");
$site->query("DELETE FROM `ref_offer_page`");
$site->query("DELETE FROM `ref_shop_category`");
$site->query("DELETE FROM `ref_article_store`");
$site->query("DELETE FROM `term_and_condition`");
$site->query("DELETE FROM `signupfavoriteshop`");
$site->query("DELETE FROM `offer`");
$site->query("DELETE FROM `shop`");
$site->query("DELETE FROM `category`");
$site->query("DELETE FROM `visitor`");

$site->query("ALTER TABLE `popular_shop` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `popular_code` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `view_count` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `ref_offer_category` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `ref_offer_page` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `ref_shop_category` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `ref_article_store` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `term_and_condition` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `signupfavoriteshop` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `offer` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `shop` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `category` AUTO_INCREMENT=1");
$site->query("ALTER TABLE `visitor` AUTO_INCREMENT=1");

$site->query("ALTER TABLE `offer` ADD COLUMN `wc_orig_id` INT");
$site->query("ALTER TABLE `offer` ADD INDEX (`wc_orig_id`)");

$site->query("ALTER TABLE `offer` ADD COLUMN `wc_shop_id` INT");
$site->query("ALTER TABLE `offer` ADD INDEX (`wc_shop_id`)");

$site->query("ALTER TABLE `offer` ADD COLUMN `wc_orig_user` INT");
$site->query("ALTER TABLE `offer` ADD INDEX (`wc_orig_user`)");

$site->query("ALTER TABLE `shop` ADD COLUMN `wc_orig_id` INT");
$site->query("ALTER TABLE `shop` ADD INDEX (`wc_orig_id`)");

$site->query("ALTER TABLE `shop` ADD COLUMN `wc_orig_user` INT");
$site->query("ALTER TABLE `shop` ADD INDEX (`wc_orig_user`)");

$site->query("ALTER TABLE `ref_offer_category` ADD COLUMN `orig_offer_id` INT");
$site->query("ALTER TABLE `ref_offer_category` ADD INDEX (`orig_offer_id`)");

$site->query("ALTER TABLE `ref_offer_category` ADD COLUMN `cat_name` VARCHAR(50)");
$site->query("ALTER TABLE `ref_offer_category` ADD INDEX (`cat_name`)");

$site->query("ALTER TABLE `ref_shop_category` ADD COLUMN `orig_shop_id` INT");
$site->query("ALTER TABLE `ref_shop_category` ADD INDEX (`orig_shop_id`)");

$site->query("ALTER TABLE `ref_shop_category` ADD COLUMN `cat_name` VARCHAR(50)");
$site->query("ALTER TABLE `ref_shop_category` ADD INDEX (`cat_name`)");

$site->query("ALTER TABLE `category` ADD INDEX (`name`)");

$user->query("ALTER TABLE `user` ADD COLUMN `wc_orig_user` INT");
$user->query("ALTER TABLE `user` ADD INDEX (`wc_orig_user`)");
