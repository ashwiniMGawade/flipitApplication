<?php

echo '--> Transferring users';

$stmt_outer = $org_outer->prepare("
    SELECT
        usr_Id,
        usr_IsActive,
        usr_Password,
        usr_FirstName,
        usr_MiddleName,
        usr_LastName,
        usr_EMail,
        IFNULL(usr_LastLoginDate, NOW()),
        usr_CreateDate,
        IFNULL(usr_ChangeDate, NOW())
    FROM mem_Users
    WHERE usr_utp_Id IN (2,6)
");

$stmt_outer->bind_result(
    $wc_orig_user,
    $status,
    $password,
    $firstname,
    $middlename,
    $lastname,
    $email,
    $lastlogin,
    $created_at,
    $updated_at
);

if ($org_outer->error) {
    echo "{$org_outer->error}\n";
}


$stmt_outer->execute();

$stmt_user = $user->prepare("
    INSERT INTO user (
        firstname,
        lastname,
        email,
        password,
        status,
        roleid,
        deleted,
        currentlogin,
        lastlogin,
        created_at,
        updated_at,
        wc_orig_user
    ) VALUES (?, ?, ?, ?, ?, 4, 0, ?, ?, ?, ?, ?)
");

if ($user->error) {
    echo "{$user->error}\n";
}

$stmt_user->bind_param('ssssissssi',
    $firstname_middlename,
    $lastname,
    $email,
    $password,
    $status,
    $lastlogin,
    $lastlogin,
    $created_at,
    $updated_at,
    $wc_orig_user
);

while ($stmt_outer->fetch()) {
    if (!empty($middlename)) {
        $firstname_middlename = $firstname . ' ' . $middlename;
    } else {
        $firstname_middlename = $firstname;
    }

    $stmt_user->execute();

    if ($user->error) {
        echo "{$user->error}\n";
    }

    echo '.';
}

echo "Users transferred\n";

$stmt_user->close();
$stmt_outer->close();

echo '--> Resolving relationships between offers/shops and users ...';

$site->query("
    UPDATE		`kortingscode_user`.`user` u
    INNER JOIN	`offer` o
            ON	o.`wc_orig_user` = u.`wc_orig_user`
    SET			o.`authorId` = u.`id`
");

$site->query("
    UPDATE		`kortingscode_user`.`user` u
    INNER JOIN	`shop` s
            ON	s.`wc_orig_user` = u.`wc_orig_user`
    SET			s.`accoutmanagerid` = u.`id`,
                s.`contentmanagerid` = u.`id`
");


echo "Relationships between offers/shops and users resolved.\n";
