<?php

echo '--> Transfering all offers';

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
    WHERE pp.pub_cat_Id = 36
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
        CONVERT(pd.dat_Data USING latin1),
        paa.att_Name
    FROM pub_Data pd
    INNER JOIN pub_ats_Attributes paa
            ON paa.att_Id = pd.dat_att_Id
    WHERE pd.dat_pub_Id = ?
    "
);
if ($org_inner->error) {
    echo $org_inner->error;
}
$stmt_inner->bind_param('i', $wc_orig_id);
$stmt_inner->bind_result($var_name, $var_data, $var_field);

/* prepare insert query */
$stmt_site = $site->prepare("
    INSERT INTO offer
    (
        title,
        visability,
        discounttype,
        couponcode,
        refofferurl,
        refurl,
        startdate,
        enddate,
        exclusivecode,
        editorpicks,
        extendedoffer,
        extendedtitle,
        extendedurl,
        extendedmetadescription,
        extendedfulldescription,
        discount,
        discountvalueType,
        authorId,
        authorName,
        shopid,
        offerlogoid,
        maxlimit,
        maxcode,
        deleted,
        created_at,
        updated_at,
        userGenerated,
        approved,
        offline,
        wc_orig_id,
        wc_shop_id,
        wc_orig_user
    ) VALUES (?, 'DE', ?, ?, NULL, ?, ?, ?, ?, 0, 1, ?, NULL, ?, ?,
        ?, ?, NULL, 'MIGRATED', NULL, ?, 0, 0, 0, ?, ?, 0, 1, 0, ?, ?, ?)
    "
);

if ($site->error) {
    echo "ERROR (PUB_ID $wc_orig_id): {$site->error}\n";
}

$title = null;
$actioncode = null;
$referer_url = null;
$aanbevolen = null;
$meta_descrip = null;
$full_description = null;
$labeltext_eurodiscount = null;
$image_id = null;
$shop = null;
$discountType = null;
$discountValueType = null;

$stmt_success = $stmt_site->bind_param('ssssssisssisissiii',
    $the_title,
    $discountType,
    $actioncode,
    $referer_url,
    $startdate,
    $enddate,
    $aanbevolen,
    $the_title,
    $meta_descrip,
    $full_description,
    $discount,
    $discountValueType,
    $image_id,
    $created_at,
    $updated_at,
    $wc_orig_id,
    $shop,
    $wc_orig_user
);



$test_limit = 0;

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

        $f_vd = (string) filter_val($var_name, $var_field, $var_data);

        /* filter values from old database */
        if (!empty($f_vd)) {
            switch ($var_name) {
            case 'title':
                $title = $f_vd;
                break;
            case 'actioncode':
                $actioncode = $f_vd;
                break;
            case 'referer_url':
                $referer_url = $f_vd;
                break;
            case 'aanbevolen':
                $aanbevolen = $f_vd;
                break;
            case 'meta_descrip':
                $meta_descrip = $f_vd;
                break;
            case 'full_description':
                $full_description = $f_vd;
                break;
            case 'labeltext_eurodiscount':
                $labeltext_eurodiscount = $f_vd;
                break;
            case 'shop':
                $shop = $f_vd;
                break;
            }
        }

        /* check if printable */
        if ($var_field == 'Kortingsbon') {
            $discountType = 'PA';
            $discountValueType = '0';

            /* determine logo file name */
            $logo_path_rest = 'offer/';
            $logo_file_name = $wc_orig_id . '_' . $var_name;

            $logo_abs_path_parent = $logo_base_path . $logo_path_rest;
            $logo_abs_path = $logo_abs_path_parent . $logo_file_name;
            $logo_rel_path = $logo_path_rest . $logo_file_name;

            /* check if directory exists */
            if (!is_dir($logo_abs_path_parent)) {
                if (is_file($logo_abs_path_parent)) {
                    die("ERROR: file offer exists in upload folder\n");
                }
                echo "$logo_abs_path_parent\n";
                mkdir($logo_abs_path_parent);
            }

            /* write the file */
            $fh = fopen($logo_abs_path, 'w');
            fwrite($fh, $var_data);
            fclose($fh);

            /* put file path into database */
            $stmt_logo = $site_logo->prepare("
                INSERT INTO image (
                    path,
                    name,
                    created_at,
                    updated_at
                ) VALUES (?, ?, NOW(), NOW())
                "
            );
            $stmt_logo->bind_param('ss', $logo_rel_path, $logo_file_name);
            $stmt_logo->execute();
            $image_id = $site_logo->insert_id;
            $stmt_logo->close();
        }
    }
    /* check if euro: coupon */
    if (!empty($labeltext_eurodiscount)) {
        $discountType = 'CD';
        $discountValueType = '1';
        $discount = 100 * $labeltext_eurodiscount;

    /* check if percentage: coupon */
    } elseif (!empty($labeltext_percentage)) {
        $discountType = 'CD';
        $discountValueType = '2';
        $discount = $labeltext_percentage;
    } elseif (empty($discountType)) {
        $discountType = 'SL';
        $discountValueType = '0';
        $discount = 0;
    }

    if (is_null($updated_at)) {
        $updated_at = $created_at;
    }

    if (!empty($title)) {
        $the_title = $title;
    } else {
        $the_title = $pubname;
    }

    if ($has_rows) {

        $stmt_site->execute();
    }
    if ($site->error) {
        echo "ERROR (PUB_ID $wc_orig_id) {$site->error}\n";
    }

    /* reset all fields */
    //$data = array();
    $title = null;
    $actioncode = null;
    $referer_url = null;
    $aanbevolen = null;
    $meta_descrip = null;
    $full_description = null;
    $labeltext_eurodiscount = null;
    $image_id = null;
    $shop = null;
    $discountType = null;
    $discountValueType = null;

    echo '.';
}

echo "\n";

function filter_val($var_name, $var_field, $var_data)
{
    switch ($var_field) {
    case 'Aanbevolen actie':
        return ($var_data === 'on') ? 1 : 0;
        break;
    default:
        return $var_data;
    }
}
