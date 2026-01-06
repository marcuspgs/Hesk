<?php
/**
 *
 * This file is part of HESK - PHP Help Desk Software.
 *
 * (c) Copyright Klemen Stirn. All rights reserved.
 * https://www.hesk.com
 *
 * For the full copyright and license agreement information visit
 * https://www.hesk.com/eula.php
 *
 */

define('IN_SCRIPT',1);
define('HESK_PATH','../../../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
require_once(HESK_PATH . 'inc/customer_accounts.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
$hesk_settings['db_failure_response'] = 'json';
hesk_isLoggedIn();

//-- Grab source permission group
$id = intval(hesk_GET('id'));

$name = null;
$features = [];
$categories = [];

$name_rs = hesk_dbQuery("SELECT `name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` WHERE `id` = ".$id);
$name = hesk_dbFetchAssoc($name_rs);
$features_rs = hesk_dbQuery("SELECT `feature` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` WHERE `group_id` = ".$id);
while ($feature = hesk_dbFetchAssoc($features_rs)) {
    $features[] = $feature['feature'];
}
$categories_rs = hesk_dbQuery("SELECT `category_id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` WHERE `group_id` = ".$id);
while ($category = hesk_dbFetchAssoc($categories_rs)) {
    $categories[] = intval($category['category_id']);
}

header('Content-Type: application/json');
http_response_code(200);
print json_encode([
    'name' => hesk_html_entity_decode($name['name']),
    'features' => $features,
    'categories' => $categories
]);
exit();
