<?php
global $hesk_settings, $hesklang;
/**
 * @var array $messages - Feedback messages to be displayed, if any
 * @var array $model - User information (name, email, language)
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-register-success');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['customer_register'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div style="margin-bottom: 20px;">
                    <?php hesk3_show_messages($serviceMessages); ?>
                    <?php hesk3_show_messages($messages); ?>
                </div>
                <div class="main__content notice-flash">
                    <div class="notification orange">
                        <p><b><?php echo $hesklang['customer_registration_check_your_email']; ?></b></p>
                        <p><?php echo $hesklang['customer_registration_check_your_email_content']; ?></p>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
