<?php
global $hesk_settings, $hesklang;
/**
 * @var array $model - A model of relevant data:
 *   - string[] 'backupCodes': An array of backup codes if the user enrolled in MFA.  Empty array otherwise.
 * @var array $messages - Feedback messages to be displayed, if any
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-manage-mfa');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);

define('LOAD_JS_JQUERY_MODAL',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('url' => "profile.php", 'title' => $hesklang['customer_profile']),
    array('title' => $hesklang['mfa'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div style="margin-bottom: 20px;">
                    <?php hesk3_show_messages($messages); ?>
                </div>
                <h1 class="article__heading article__heading--form">
                    <span class="icon-in-circle" aria-hidden="true">
                        <svg class="icon icon-document">
                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-lock"></use>
                        </svg>
                    </span>
                    <span class="ml-1"><?php echo $hesklang['mfa']; ?></span>
                </h1>
                <form id="complete-form" action="manage_mfa.php" method="post" name="form1" id="formNeedValidation" class="form form-submit-ticket ticket-create" novalidate>
                    <div data-step="3">
                        <ul class="step-bar no-click">
                            <li data-link="1" data-all="3">
                                <?php echo $hesklang['mfa_step_method']; ?>
                            </li>
                            <li data-link="2" data-all="3">
                                <?php echo $hesklang['mfa_step_verification']; ?>
                            </li>
                            <li data-link="3" data-all="3">
                                <?php echo $hesklang['mfa_step_complete']; ?>
                            </li>
                        </ul>
                    </div>
                    <div class="step-item step-3">
                        <?php if (count($model['backupCodes']) !== 0) {
                            $backup_codes = implode("\n", array_map(function($code, $key) { return str_pad(($key+1), 2, ' ', STR_PAD_LEFT) . '. ' . substr($code, 0, 4) . '-' . substr($code, 4); }, $model['backupCodes'], array_keys($model['backupCodes'])));
                            hesk_show_success('<div class="shield-icon"><svg class="icon icon-anonymize"><use xlink:href="'.HESK_PATH.'img/sprite.svg#icon-anonymize"></use></svg></div>' . $hesklang['mfa_configured'], ' ', false);
                            hesk_show_info('<p style="margin-top:10px">'.$hesklang['mfa_backup_codes_description'].'</p><pre style="margin-top:20px; font-family: monospace; font-size: 16px;">'.$backup_codes.'</pre>', $hesklang['mfa_backup_codes_header'] . '<br>', false);
                        } else {
                            hesk_show_info($hesklang['mfa_removed'], ' ', false);
                        } ?>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <div class="verify-back">
                            <a href="profile.php" class="btn btn-full" ripple="ripple">
                                <?php echo $hesklang['view_profile']; ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
