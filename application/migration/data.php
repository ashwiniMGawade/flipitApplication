<?php

function migrate_offers()
{
    global $org_outer;
    global $org_inner;
    global $site;
    global $site_logo;
    global $user;

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

    $stmt_site->bind_param('ssssssisssisissiii',
        $the_title,
        $discountType,
        $actioncode,
        $referer_url,
        $startdate,
        $enddate,
        $aanbevolen,
        $title,
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

            /* filter values from old database */
            $$var_name = filter_val($var_name, $var_field, $var_data);

            /* check if printable */
            if ($var_field == 'Kortingsbon') {
                echo "Kortingsbon!\n";
                $discountType = 'PA';
                $discountValueType = '0';

                /* determine logo file name */
                $logo_base_path = '/Users/meyerdev/Sites/kortingscode_sven/kortingscode.nl/public';
                $logo_path_rest = 'images/upload/offer/';
                $logo_file_name = $wc_orig_id . '_' . $var_name;

                $logo_abs_path = $logo_base_path . $logo_path_rest . $logo_file_name;
                $logo_rel_path = $logo_path_rest . $logo_file_name;

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

                /*
                $stmt_logo = $site_logo->prepare("
                    INSERT INTO media
                    (
                        name,
                        fileurl,
                        mediaimageid,
                        authorName,
                        authorId,
                        created_at,
                        updated_at
                    ) VALUES (?, ?, ?, '', 0, NOW(), NOW())
                    "
                );
                $stmt_logo->bind_param('ssi',
                    $logo_file_name,
                    $logo_file_name,
                    $image_id
                );
                $stmt_logo->execute();
                $stmt_logo->close();
                 */

            }
        }
        /* check if euro: coupon */
        if (!empty($labeltext_eurodiscount)) {
            $discountType = 'CD';
            $discountValueType = '1';
            $discount = $labeltext_eurodiscount;

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
        $title = null;
        $actioncode = null;
        $referer_url = null;
        $startdate = null;
        $enddate = null;
        $aanbevolen = null;
        $meta_descrip = null;
        $full_description = null;
        $labeltext_eurodiscount = null;
        $image_id = null;
        $shop = null;

        $discountType = null;
        $discountValueType = null;
    }
}

function filter_val($var_name, $var_field, $var_data)
{
    // echo "name: $var_name, field: $var_field, data: $var_data\n";

    switch ($var_field) {
    case 'Aanbevolen actie':
        return ($var_data === 'on') ? 1 : 0;
        break;
    default:
        return $var_data;
    }
}
