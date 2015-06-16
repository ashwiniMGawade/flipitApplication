<?php

echo '--> Transfering all shops';

/* prepare publication items query */
$stmt_outer = $org_outer->prepare("
    SELECT
        pp.pub_Id,
        pp.pub_Name,
        pp.pub_StartDate,
        pp.pub_EndDate,
        pp.pub_usr_Id,
        pp.pub_CreateDate,
        pp.pub_ChangeDate
    FROM pub_Publications pp
    WHERE pp.pub_cat_Id = 37
    "
);
if ($org_outer->error) {
    echo "ERROR (PUB_ID $wc_orig_id) (OUTER): {$org_outer->error}\n";
}
$stmt_outer->bind_result(
    $wc_orig_id,
    $pubname,
    $startdate,
    $enddate,
    $wc_orig_user,
    $created_at,
    $updated_at
);
$stmt_outer->execute();

/* prepare publication data query */
$stmt_inner = $org_inner->prepare("
    SELECT
        pd.dat_Name,
        pd.dat_Data,
        paa.att_Name
    FROM pub_Data pd
    INNER JOIN pub_ats_Attributes paa
            ON paa.att_Id = pd.dat_att_Id
    WHERE pd.dat_pub_Id = ?
    "
);
$stmt_inner->bind_param('i', $wc_orig_id);
$stmt_inner->bind_result($var_name, $var_data, $var_field);

/* prepare insert query */
$stmt_site = $site->prepare("
    INSERT INTO shop
    (
        name,
        permalink,
        metadescription,
        usergenratedcontent,
        notes,
        deeplink,
        deeplinkstatus,
        refurl,
        actualurl,
        affliateprogram,
        title,
        subTitle,
        overritetitle,
        overritesubtitle,
        overritebrowsertitle,
        shoptext,
        views,
        howtouse,
        Deliverytime,
        returnPolicy,
        freeDelivery,
        deliveryCost,
        status,
        offlinesicne,
        accoutmanagerid,
        accountManagerName,
        contentmanagerid,
        contentManagerName,
        logoid,
        screenshotid,
        howtousesmallimageid,
        howtousebigimageid,
        affliatenetworkid,
        howtousepageid,
        keywordlink,
        deleted,
        created_at,
        updated_at,
        wc_orig_id,
        wc_orig_user
    ) VALUES (?, ?, ?, 1, '', '', 0, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, 0, NULL, NULL,
    0, 0.0, 1, NULL, NULL, 'MIGRATED', NULL, 'MIGRATED', ?, 600, NULL,
    NULL, NULL, NULL, '', 0, ?, ?, ?, ?)
    "
);

if ($site->error) {
    echo "ERROR (PUB_ID $wc_orig_id): {$site->error}\n";
}

$stmt_site->bind_param('sssssssssssiissii',
    $shop_name,
    $name,
    $meta_descrip,
    $referer_url,
    $url,
    $browser_title,
    $subtitle,
    $browser_title,
    $subtitle,
    $browser_title,
    $full_description,
    $views,
    $image_id,
    $created_at,
    $updated_at,
    $wc_orig_id,
    $wc_orig_user
);

/* go through all offers */
while ($stmt_outer->fetch()) {
    $stmt_inner->execute();
    if ($org_inner->error) {
        echo "ERROR (PUB_ID $wc_orig_id) {$org_inner->error}\n";
    }

    // echo "Reading pub id: $wc_orig_id\n";
    $has_rows = false;
    /* go through all pub_Data fields */
    while ($stmt_inner->fetch()) {
        $has_rows = true;

        /* filter values from old database */
        if (!empty($var_data)) {
            $$var_name = filter_val2($var_name, $var_field, $var_data);
        }

        /* check if printable */
        if ($var_field == 'Logo') {

            /* determine logo file name */
            $logo_path_rest = 'images/upload/shop/';
            $logo_file_name = $wc_orig_id . '_' . $var_name;

            if (preg_match('/\.([^.]*)$/', $var_name, $matches)) {
                $ext = $matches[count($matches) - 1];
            }

            $logo_abs_path_parent = $logo_base_path . $logo_path_rest;
            $logo_abs_path = $logo_abs_path_parent . $logo_file_name;
            $logo_rel_path = $logo_path_rest . $logo_file_name;

            /* check if directory exists */
            if (!is_dir($logo_abs_path_parent)) {
                if (is_file($logo_abs_path_parent)) {
                    die("ERROR: file offer exists in upload folder\n");
                }
                mkdir($logo_abs_path_parent);
            }

            /* write the file */
            $fh = fopen($logo_abs_path, 'w');
            fwrite($fh, $var_data);
            fclose($fh);

            /* put file path into database */
            $stmt_logo = $site_logo->prepare("
                INSERT INTO image (
                    type,
                    ext,
                    path,
                    name,
                    created_at,
                    updated_at
                ) VALUES ('LG', ?, ?, ?, NOW(), NOW())
                "
            );
            $stmt_logo->bind_param('sss', $ext, $logo_path_rest, $logo_file_name);
            $stmt_logo->execute();
            $image_id = $site_logo->insert_id;
            $stmt_logo->close();


        }
    }

    if (is_null($updated_at)) {
        $updated_at = $created_at;
    }

    if (!empty($title)) {
        $the_title = $title;
    } else {
        $the_title = $pubname;
    }

    $views = rand(2400, 2600);

    if ($has_rows) {
        $stmt_site->execute();
    }
    if ($site->error) {
        echo "ERROR (PUB_ID $wc_orig_id) {$site->error}\n";
    }

    /* reset all fields */
    $shop_name = null;
    $name = null;
    $meta_descrip = null;
    $referer_url = null;
    $url = null;
    $browser_title = '';
    $subtitle = '';
    $full_description = null;
    $views = null;
    $image_id = null;
    $created_at = null;
    $updated_at = null;
    $wc_orig_id = null;
    $wc_orig_user = null;

    echo '.';
}

echo "\n--> Resolving relationships between offers and shops ...";

$site->query("
    UPDATE		offer o
    INNER JOIN	shop s
            ON	o.`wc_shop_id` = s.`wc_orig_id`
    SET			o.`shopid` = s.`id`
");

echo "Resolved relationships between offers and shops\n";

function filter_val2($var_name, $var_field, $var_data)
{
    // echo "name: $var_name, field: $var_field, data: $var_data\n";

    switch ($var_field) {
    default:
        return $var_data;
    }
}
