<?php

/* de map van met deze scripts moet in de public folder van je kortingscode
 * instantie staan, in verband met de images */

require('functions.php');

$logo_base_path = dirname(__FILE__) . '/../images/upload/';


$db = init_db(
    'localhost',
    'root',
    'root',
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
$user = $db['user'];

require('setup.php');
require('offers.php');
require('shops.php');
require('categories.php');
require('visitors.php');
require('editors.php');
require('wrapup.php');

close_db($db);
