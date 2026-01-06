<?php
global $hesk_settings, $hesklang;
/**
 * @var string $error
 * @var boolean $showDebugWarning
 * @var string $breadcrumbLink
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-error');

define('RENDER_COMMON_ELEMENTS',1);
define('IGNORE_NAVBAR_RENDER',1);

define('IGNORE_LOAD_JQUERY',1);
define('IGNORE_LOAD_HESK_FUNC',1);
define('IGNORE_LOAD_SVG4',1);
define('IGNORE_LOAD_SELECTIZE',1);
define('IGNORE_LOAD_APP',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $breadcrumbLink, 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['error'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div class="main__content notice-flash">
                    <div role="alert" class="notification red">
                        <b><?php echo $hesklang['error']; ?>:</b> <?php echo $error; ?>
                        <?php if ($showDebugWarning): ?>
                            <p class="text-danger text-bold" style="margin-top:10px">&nbsp;<br><?php echo $hesklang['warn']; ?></p>
                            <?php echo $hesklang['dmod']; ?>
                        <?php endif; ?>
                        <p class="text-center">
                            &nbsp;<br>
                            <a class="link" href="javascript:history.go(-1)"><?php echo $hesklang['back']; ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
