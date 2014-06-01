<?php

/* de map van met deze scripts moet in de public folder van je kortingscode
 * instantie staan, in verband met de images */

require('../../public/migration/functions.phption/migration/functions.php');

$logo_base_path = dirname(__FILE__) . '/../';


$db = init_db(
    'localhost',
    'root',
    'letigre',
    array(
        'org_outer' => 'kortingscode_org',
        'org_inner' => 'kortingscode_org',
        'site' => 'kortingscode_site',
        'site_logo' => 'kortingscode_site',
        'user' => 'kortingscode_user'
    ),
    null, /* either port, as int ...*/
    '/tmp/mysql.sock' /* or unix socket as path string. use either port or this */
);

$org_outer = $db['org_outer'];
$org_inner = $db['org_inner'];
$site = $db['site'];
$site_logo = $db['site_logo'];
$user../../public/migration/setup.phpser'];

requ../../public/migration/offers.phpication/migrat../../public/migration/shops.phps.php/applicat../../public/migration/categories.phpsetup.php');
../../public/migration/visitors.phpation/migratio../../public/migration/editors.phps.phps.php');../../public/migration/wrapup.php../../application/mig../../application/migration/editors.phplication/migration/visitors.phphp');
require('categories.php');
requ../../application/migration/wrapup.phpors.php');
require('editors.php');
require('wrapup.php');

close_db($db);
