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
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_view_tickets');
hesk_checkPermission('can_reply_tickets');

/* A security check */
hesk_token_check('POST');

/* Ticket ID */
$trackingID = hesk_cleanID() or die($hesklang['int_error'].': '.$hesklang['no_trackID']);

// Load priorities
require_once(HESK_PATH . 'inc/priorities.inc.php');

$priority = intval( hesk_POST('priority') );
if ( ! isset($hesk_settings['priorities'][$priority])) {
    hesk_error($hesklang['priority_e_id']);
}

// Get original ticket details
$res = hesk_dbQuery("SELECT `id`, `priority` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` WHERE `trackid`='".hesk_dbEscape($trackingID)."' LIMIT 1");
if (hesk_dbNumRows($res) != 1) {
    hesk_error($hesklang['ticket_not_found']);
}
$ticket = hesk_dbFetchAssoc($res);

if ($ticket['priority'] == $priority) {
    hesk_process_messages(sprintf($hesklang['ticket_already_priority'], $hesk_settings['priorities'][$priority]['name']),'admin_ticket.php?track='.$trackingID.'&Refresh='.rand(10000,99999),'NOTICE');
}

$revision = sprintf($hesklang['thist8'],hesk_date(),$hesk_settings['priorities'][$priority]['name'],addslashes($_SESSION['name']).' ('.$_SESSION['user'].')');
hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."tickets` SET `priority`='{$priority}', `history`=CONCAT(`history`,'".hesk_dbEscape($revision)."') WHERE `id`='".intval($ticket['id'])."'");

hesk_process_messages(sprintf($hesklang['chpri2'],$hesk_settings['priorities'][$priority]['name']),'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999),'SUCCESS');
