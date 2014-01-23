<?php

echo '--> Transferring all categories';

require('../../public/migration/mapping.phpcation/migration/mapping.php');

/* ALL CATS */

$stmt_site = $site->prepare("
    INSERT INTO category (
        name,
        deleted,
        status,
        created_at,
        updated_at
    ) VALUES (
        ?, 0, 1, NOW(), NOW()
    )
    ");

$stmt_site->bind_param('s', $cat_name);

foreach ($new_categories as $cat_name) {
    $stmt_site->execute();
    echo '.';
}

$stmt_site->close();

echo "categories transferred\n";

/* OFFER */

echo "--> Tranferring offer-category relationships";

/* prepare publication items query */
$stmt_outer = $org_outer->prepare("
    SELECT
        pp.pub_Id,
        ac.cat_Name
    FROM pub_Publications pp
    INNER JOIN aaw_cat_Publications acp
            ON acp.pst_pub_Id = pp.pub_Id
    INNER JOIN aaw_Categories ac
            ON ac.cat_Id = acp.pst_cat_Id
    WHERE   pp.pub_cat_Id = 36
        AND pp.pub_StartDate > \"2011-01-01\"
    "
);

if ($org_outer->error) {
    echo "ERROR (PUB_ID $wc_orig_id) (OUTER): {$org_outer->error}\n";
}
$stmt_outer->bind_result(
    $wc_orig_id,
    $cat_old_name
);

$stmt_outer->execute();

/* prepare offer insert query */
$stmt_site = $site->prepare("
    INSERT INTO ref_offer_category
    (
        offerid,
        categoryid,
        created_at,
        updated_at,
        orig_offer_id,
        cat_name
    ) VALUES (1, 1, NOW(), NOW(), ?, ?)
    "
);

if ($site->error) {
    echo "ERROR (PUB_ID $wc_orig_id): {$site->error}\n";
}

$stmt_site->bind_param('is',
    $wc_orig_id,
    $cat_name
);

$test_limit = 0;

/* go through all offers */
while ($stmt_outer->fetch()) {
    $cat_name = $old_2_new[$cat_old_name];

    $stmt_site->execute();
    if ($site->error) {
        echo "ERROR (PUB_ID $wc_orig_id) {$site->error}\n";
    }

    echo '.';
}

$stmt_outer->close();
$stmt_site->close();

echo "offer-category relationships transferred\n";

/* SHOP */

echo "--> Tranferring shop-category relationships";

/* prepare publication items query */
$stmt_outer = $org_outer->prepare("
    SELECT
        pp.pub_Id,
        ac.cat_Name
    FROM pub_Publications pp
    INNER JOIN aaw_cat_Publications acp
            ON acp.pst_pub_Id = pp.pub_Id
    INNER JOIN aaw_Categories ac
            ON ac.cat_Id = acp.pst_cat_Id
    WHERE pp.pub_cat_Id = 37
    "
);

if ($org_outer->error) {
    echo "ERROR (PUB_ID $wc_orig_id) (OUTER): {$org_outer->error}\n";
}
$stmt_outer->bind_result(
    $wc_orig_id,
    $cat_old_name
);

$stmt_outer->execute();

/* prepare offer insert query */
$stmt_site = $site->prepare("
    INSERT INTO ref_shop_category
    (
        shopid,
        categoryid,
        created_at,
        updated_at,
        orig_shop_id,
        cat_name
    ) VALUES (1, 1, NOW(), NOW(), ?, ?)
    "
);

if ($site->error) {
    echo "ERROR (PUB_ID $wc_orig_id): {$site->error}\n";
}

$stmt_site->bind_param('is',
    $wc_orig_id,
    $cat_name
);

$test_limit = 0;

/* go through all offers */
while ($stmt_outer->fetch()) {
    $cat_name = $old_2_new[$cat_old_name];

    $stmt_site->execute();

    if ($site->error) {
        echo "ERROR (PUB_ID $wc_orig_id) {$site->error}\n";
    }

    echo '.';
}

echo "offer-category relationships transferred\n";

$stmt_outer->close();
$stmt_site->close();

echo "--> Resolving relationships (both) ...";

$site->query("
    UPDATE ref_offer_category roc
    INNER JOIN category c
            ON roc.`cat_name`       = c.`name`
    INNER JOIN offer o
            ON roc.`orig_offer_id`  = o.`wc_orig_id`
    SET
        roc.`offerid`       = o.`id`,
        roc.`categoryid`    = c.`id`
");

$site->query("
    UPDATE ref_shop_category rsc
    INNER JOIN category c
            ON rsc.`cat_name`       = c.`name`
    INNER JOIN shop s
            ON rsc.`orig_shop_id`  = s.`wc_orig_id`
    SET
        rsc.`shopid`       = s.`id`,
        rsc.`categoryid`    = c.`id`
");

echo "Relationships resolved\n";
