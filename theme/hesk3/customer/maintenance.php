<?php
global $hesk_settings, $hesklang;
/**
 * @var boolean $heskInstalled
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-maintenance');

define('RENDER_COMMON_ELEMENTS',1);
define('IGNORE_NAVBAR_RENDER',1);

define('IGNORE_LOAD_JQUERY',1);
define('IGNORE_LOAD_HESK_FUNC',1);
define('IGNORE_LOAD_SVG4',1);
define('IGNORE_LOAD_SELECTIZE',1);
define('IGNORE_LOAD_APP',1);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div class="main__content notice-flash">
                    <div role="alert" class="notification orange">
                        <p><b><?php echo $heskInstalled ? $hesklang['hni1'] : $hesklang['mm1']; ?></b></p>
                        <p><?php echo $heskInstalled ? $hesklang['hni2'] : $hesklang['mm2']; ?></p>
                        <p><?php echo $heskInstalled ? $hesklang['hni3'] : $hesklang['mm3']; ?></p>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
