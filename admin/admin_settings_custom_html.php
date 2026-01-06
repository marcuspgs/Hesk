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

define('LOAD_TABS',1);

define('LOAD_CUSTOMER_THEME_VARS',1); // Need this here to show off calculated colors as examples and make them work properly

// Make sure the install folder is deleted
if (is_dir(HESK_PATH . 'install')) {die('Please delete the <b>install</b> folder from your server for security reasons then refresh this page!');}

// Get all the required files and functions
require(HESK_PATH . 'hesk_settings.inc.php');

// Save the default language for the settings page before choosing user's preferred one
$hesk_settings['language_default'] = $hesk_settings['language'];
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
require(HESK_PATH . 'inc/setup_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

// Check permissions for this feature
hesk_checkPermission('can_man_settings');

// Is this feature disabled?
if (file_exists(HESK_PATH . 'disable_custom_html_ui.txt')) {
    hesk_error($hesklang['custom_html_disabled']);
}

// What should we do?
if ( $action = hesk_REQUEST('a') ) {
    if ( defined('HESK_DEMO') ) {hesk_process_messages($hesklang['ddemo'], 'admin_settings_custom_html.php', 'NOTICE');}
    elseif ($action == 'save')   {hesk_save_custom_html();}
}

// Print header
require_once(HESK_PATH . 'inc/header.inc.php');

// Print main manage users page
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

// Demo mode? Hide values of sensitive settings
if ( defined('HESK_DEMO') )
{
    require_once(HESK_PATH . 'inc/admin_settings_demo.inc.php');
}

$enable_save_settings = 1;

/* This will handle error, success and notice messages */
hesk_handle_messages();
?>
<div class="main__content settings admin_settings_theme">

    <?php require_once(HESK_PATH . 'inc/admin_settings_status.inc.php'); ?>

    <form method="post" action="admin_settings_custom_html.php" name="form1" onsubmit="return hesk_checkFields()">
        <div class="settings__form form">
            <section class="settings__form_block">
                <h3><?php echo $hesklang['custom_head']; ?></h3>
                <div style="margin-left:40px; margin-bottom:50px;">
                    <p><?php echo $hesklang['custom_head_desc']; ?></span></p>
                    <?php
                    $template_file = HESK_PATH . 'head.txt';
                    if (file_exists($template_file) && is_writable($template_file)) {
                    ?>
                        <textarea class="form-control" id="head" name="head" style="width:100%;height:300px;resize: both;"><?php echo htmlspecialchars(file_get_contents($template_file)); ?></textarea>
                    <?php
                    } else {
                        $enable_save_settings = 0;
                        hesk_show_notice(sprintf($hesklang['file_missing_not_writable'], $template_file));
                    }
                    ?>
                </div>

                <h3><?php echo $hesklang['custom_header']; ?></h3>
                <div style="margin-left:40px; margin-bottom:50px;">
                    <p><?php echo sprintf($hesklang['custom_header_desc'], 'https://www.hesk.com/knowledgebase/?article=62'); ?></span></p>
                    <?php
                    $template_file = HESK_PATH . 'header.txt';
                    if (file_exists($template_file) && is_writable($template_file)) {
                    ?>
                        <textarea class="form-control" id="header" name="header" style="width:100%;height:300px;resize: both;"><?php echo htmlspecialchars(file_get_contents($template_file)); ?></textarea>
                    <?php
                    } else {
                        $enable_save_settings = 0;
                        hesk_show_notice(sprintf($hesklang['file_missing_not_writable'], $template_file));
                    }
                    ?>
                </div>

                <h3><?php echo $hesklang['custom_footer']; ?></h3>
                <div style="margin-left:40px; margin-bottom:50px;">
                    <p><?php echo $hesklang['custom_footer_desc']; ?></span></p>
                    <?php
                    $template_file = HESK_PATH . 'footer.txt';
                    if (file_exists($template_file) && is_writable($template_file)) {
                    ?>
                        <textarea class="form-control" id="footer" name="footer" style="width:100%;height:300px;resize: both;"><?php echo htmlspecialchars(file_get_contents($template_file)); ?></textarea>
                    <?php
                    } else {
                        $enable_save_settings = 0;
                        hesk_show_notice(sprintf($hesklang['file_missing_not_writable'], $template_file));
                    }
                    ?>
                </div>
            </section>

            <div class="settings__form_submit">
                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                <input type="hidden" name="a" value="save">
                <button style="display: inline-flex" type="submit" id="submitbutton" class="btn btn-full" ripple="ripple"
                    <?php echo $enable_save_settings ? '' : 'disabled'; ?>>
                    <?php echo $hesklang['save_changes']; ?>
                </button>

                <a style="height: 40px" href="admin_settings_theme.php" class="btn btn--blue-border" ripple="ripple">
                    <?php echo $hesklang['cancel']; ?> / <?php echo $hesklang['back']; ?>
                </a>

                <?php if (!$enable_save_settings): ?>
                    <p>&nbsp;</p>
                    <div role="alert" class="notification red">
                        <?php echo $hesklang['uanble_not_writable']; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script src="<?php echo HESK_PATH; ?>js/jquery-ui.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


function hesk_save_custom_html()
{
    global $hesk_settings, $hesklang;

    $template_file = HESK_PATH . 'head.txt';
    if ( ! file_exists($template_file) || ! is_writable($template_file)) {
        hesk_process_messages($hesklang['uanble_not_writable'], 'admin_settings_custom_html.php');
    }

    $template_file = HESK_PATH . 'header.txt';
    if ( ! file_exists($template_file) || ! is_writable($template_file)) {
        hesk_process_messages($hesklang['uanble_not_writable'], 'admin_settings_custom_html.php');
    }

    $template_file = HESK_PATH . 'footer.txt';
    if ( ! file_exists($template_file) || ! is_writable($template_file)) {
        hesk_process_messages($hesklang['uanble_not_writable'], 'admin_settings_custom_html.php');
    }

    $file_content = hesk_get_html(hesk_POST('head'));
    file_put_contents(HESK_PATH . 'head.txt', '<!-- ' . hesk_htmlspecialchars_decode($hesklang['custom_head_cmnt']) . " -->\n\n" . ltrim(hesk_sanitize_html($file_content)));

    $file_content = hesk_get_html(hesk_POST('header'));
    file_put_contents(HESK_PATH . 'header.txt', '<!-- ' . hesk_htmlspecialchars_decode($hesklang['custom_header_cmnt']) . " -->\n\n" . ltrim(hesk_sanitize_html($file_content)));

    $file_content = hesk_get_html(hesk_POST('footer'));
    file_put_contents(HESK_PATH . 'footer.txt', '<!-- ' . hesk_htmlspecialchars_decode($hesklang['custom_footer_cmnt']) . " -->\n\n" . ltrim(hesk_sanitize_html($file_content)));

    hesk_process_messages($hesklang['custom_html_saved'], 'NOREDIRECT', 'SUCCESS');

} // END hesk_save_custom_html()


function hesk_sanitize_html($in)
{
    $replace_from = array("\t","<?","?>","$","<%");
    $replace_to   = array("","&lt;?","?&gt;","\$","&lt;%");

    $in = str_replace($replace_from,$replace_to,$in);
    $in = preg_replace('/\<script(.*)\>(.*)\<\/script\>/Uis','<!-- scripts have been removed -->',$in);
    $in = preg_replace('/\<\!\-\-(.*)\-\-\>/Uis','',$in);
    return $in;
} // END hesk_sanitize_html()


function hesk_get_html($in)
{
    $replace_from = array("\t","<?","?>","$","<%");
    $replace_to   = array("","&lt;?","?&gt;","\$","&lt;%");

    if (HESK_SLASH) {
        $in = trim($in);
    } else {
        $in = trim(stripslashes($in));
    }

    $in = str_replace($replace_from,$replace_to,$in);
    $in = preg_replace('/\<script(.*)\>(.*)\<\/script\>/Uis',"<script$1></script>",$in);
    $in = preg_replace('/\<\!\-\-(.*)\-\-\>/Uis','',$in);
    return $in;
} // END hesk_get_html()


function hesk_revert_html($in)
{
    $replace_from = array("&lt;","&gt;");
    $replace_to = array("<",">");
    $in = str_replace($replace_from,$replace_to,$in);
    return $in;
} // END hesk_revert_html()

