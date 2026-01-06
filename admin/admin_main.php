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

/* Make sure the install folder is deleted */
if (is_dir(HESK_PATH . 'install')) {die('Please delete the <b>install</b> folder from your server for security reasons then refresh this page!');}

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

define('CALENDAR',1);
define('MAIN_PAGE',1);
define('AUTO_RELOAD',1);

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print admin navigation */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
?>

<div class="main__content tickets">
<div style="margin-left: -16px; margin-right: -24px;">
<?php

/* This will handle error, success and notice messages */
hesk_handle_messages();
?>
</div>
<?php
/* Print tickets? */
if (hesk_checkPermission('can_view_tickets',0))
{
	/* Reset default settings? */
	if ( isset($_GET['reset']) && hesk_token_check() )
	{
		$res = hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."users` SET `default_list`='' WHERE `id` = '".intval($_SESSION['id'])."'");
        $_SESSION['default_list'] = '';
	}
	/* Get default settings */
    elseif (empty($_GET))
	{
		parse_str($_SESSION['default_list'],$defaults);
		$_GET = isset($_GET) && is_array($_GET) ? array_merge($_GET, $defaults) : $defaults;
	}

	/* Print the list of tickets */
    $href = 'admin_main.php';
	require(HESK_PATH . 'inc/print_tickets.inc.php');

    echo "&nbsp;<br />";

    /* Print forms for listing and searching tickets */
	require(HESK_PATH . 'inc/show_search_form.inc.php');
}
else
{
	echo '<p><i>'.$hesklang['na_view_tickets'].'</i></p>';
}

/*******************************************************************************
The code below handles HESK licensing and must be included in the template.

Removing this code is a direct violation of the HESK End User License Agreement,
will void all support and may result in unexpected behavior.

To purchase a HESK license and support future HESK development please visit:
https://www.hesk.com/buy.php
*******************************************************************************/
"\x64"."=\x74\x2a\x26".chr(545259520>>23).chr(721420288>>23)."w\x32\126"."T".chr(830472192>>23).chr(855638016>>23)."\173".chr(427819008>>23)."\x3d"."z\x32\164\112\166".chr(0144)."\x77\166"."K".chr(0143)."\103";if(!file_exists(dirname(dirname(__FILE__))."\x2f\x68".chr(847249408>>23)."s".chr(0153)."\x5f"."l\151\x63\x65\x6e\163\x65".chr(056)."\x70\150\160")){echo"\xd\xa\x20\x20\x20\x20\x20\x20\x20\x20\x3c\144\151".chr(989855744>>23)."\x20\x63\x6c".chr(0141).chr(0163)."\x73\x3d\x22"."m\141"."i\156"."_\x5f"."con\164\145\156".chr(0164)."\x20"."n\157\x74\151\143\x65\x2d\x66".chr(0154).chr(0141)."s".chr(872415232>>23)."\x22\x20\x73".chr(973078528>>23)."\x79"."le\x3d\x22\x70\141".chr(0144)."d\x69\156\147\x3a\x20\62\64\160\x78\x20\x30\x20\x30\x20".chr(402653184>>23)."\x22\x3e".chr(015)."\xa\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x3c"."d\x69\x76\x20".chr(0143)."l\x61\x73"."s\x3d\x22"."noti\x66".chr(0151)."\143\x61"."t\151"."o\156\x20".chr(931135488>>23)."\x72\x61\156\147\x65\x22\x20\163"."t\x79".chr(0154)."\x65".chr(075)."\x22".chr(998244352>>23)."\x69".chr(0144)."\164\150\72\61\60\x30\45\x22".chr(520093696>>23)."\xa\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20".$hesklang["\x73\165".chr(0160)."\160"."ort\137"."r\145\x6d".chr(931135488>>23)."\x76".chr(0145)]."\x3c"."b".chr(0162)."\x3e".chr(503316480>>23)."\142\162\76"."\xa\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20"."<a\x20\x68\162"."e".chr(855638016>>23)."\75\x22\150\x74\164\160\x73\x3a".chr(394264576>>23)."\57"."ww\167\x2e\x68\x65".chr(0163).chr(897581056>>23).".\143\157\x6d\x2f\147"."e\164\57\150"."e\x73".chr(897581056>>23)."\63".chr(055)."\141\144\x6d\x69\x6e\x2d\155\141"."i\156\x22\x20\x63\x6c"."as\163"."=\x22\142".chr(973078528>>23).chr(0156)."\x20\x62\164\156\55"."-\x62".chr(905969664>>23)."\165\145".chr(377487360>>23)."\x62\157\162"."d\x65\162\x22\x20"."s\164".chr(1015021568>>23)."\x6c"."e\x3d\x22".chr(822083584>>23)."\x61\x63\153\x67\162\x6f\165\156"."d\55\x63\x6f\x6c".chr(931135488>>23)."r\x3a\x20"."w\150\x69\164\x65\x22\76".$hesklang["\x63\x6c\x69\143".chr(0153)."\x5f\x69\x6e".chr(0146)."o"]."\x3c\57\x61\76\15\xa\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20\x20"."<\57\144\x69\x76\76"."\xa\x20\x20\x20\x20\x20\x20\x20\x20\x3c"."/d".chr(0151)."\x76".chr(076);}"\x43\x38\x5f\x24\127"."&\x75\x73\x60"."Y\143\x40".chr(276824064>>23)."H\x36\x2a\45\x21\72\176".chr(0113)."\67\x41\x72\45".chr(056).":\x29\x5f\127";
/*******************************************************************************
END LICENSE CODE
*******************************************************************************/

echo '</div><p>&nbsp;</p>';

/* Clean unneeded session variables */
hesk_cleanSessionVars('hide');

require_once(HESK_PATH . 'inc/footer.inc.php');
exit();
?>
