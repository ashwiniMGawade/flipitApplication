<?php

echo '--> Transferring all visitors';

$stmt_outer = $org_outer->prepare("
    SELECT
        usr_Password,
        usr_Email,
        usr_CreateDate,
        usr_IsActive
    FROM mem_Users
    WHERE usr_utp_Id = 4
    ");

$stmt_outer->bind_result(
    $password,
    $email,
    $created_at,
    $status
);


$stmt_site = $site->prepare("
    INSERT INTO visitor (
        email,
        password,
        status,
        deleted,
        currentlogin,
        lastlogin,
        created_at,
        updated_at
    )
    VALUES (
        ?, ?, ?, 0, NOW(), NOW(), ?, NOW()
    )
    ");

if ($site->error) {
    echo "ERROR (PUB_ID $wc_orig_id) (OUTER): {$site->error}\n";
}

$stmt_site->bind_param('ssis',
    $email,
    $password,
    $status,
    $created_at
);

$stmt_outer->execute();

while ($stmt_outer->fetch()) {
    $stmt_site->execute();
    echo '.';
}

echo "All visitors transferred\n";

$stmt_site->close();
$stmt_outer->close();
