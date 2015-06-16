<?php

$site->query("ALTER TABLE `offer` DROP COLUMN `wc_orig_id`");
$site->query("ALTER TABLE `offer` DROP INDEX (`wc_orig_id`)");

$site->query("ALTER TABLE `offer` DROP COLUMN `wc_shop_id`");
$site->query("ALTER TABLE `offer` DROP INDEX (`wc_shop_id`)");

$site->query("ALTER TABLE `offer` DROP COLUMN `wc_orig_user`");
$site->query("ALTER TABLE `offer` DROP INDEX (`wc_orig_user`)");

$site->query("ALTER TABLE `shop` DROP COLUMN `wc_orig_id`");
$site->query("ALTER TABLE `shop` DROP INDEX (`wc_orig_id`)");

$site->query("ALTER TABLE `shop` DROP COLUMN `wc_orig_user`");
$site->query("ALTER TABLE `shop` DROP INDEX (`wc_orig_user`)");

$site->query("ALTER TABLE `ref_offer_category` DROP COLUMN `orig_offer_id`");
$site->query("ALTER TABLE `ref_offer_category` DROP INDEX (`orig_offer_id`)");

$site->query("ALTER TABLE `ref_offer_category` DROP COLUMN `cat_name`");
$site->query("ALTER TABLE `ref_offer_category` DROP INDEX (`cat_name`)");

$site->query("ALTER TABLE `ref_shop_category` DROP COLUMN `orig_shop_id`");
$site->query("ALTER TABLE `ref_shop_category` DROP INDEX (`orig_shop_id`)");

$site->query("ALTER TABLE `ref_shop_category` DROP COLUMN `cat_name`");
$site->query("ALTER TABLE `ref_shop_category` DROP INDEX (`cat_name`)");

$site->query("ALTER TABLE `category` DROP INDEX (`name`)");

$user->query("ALTER TABLE `user` DROP COLUMN `wc_orig_user`");
$user->query("ALTER TABLE `user` DROP INDEX (`wc_orig_user`)");
