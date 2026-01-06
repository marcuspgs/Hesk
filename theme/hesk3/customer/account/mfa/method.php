<?php
global $hesk_settings, $hesklang;
/**
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
                <form action="manage_mfa.php" method="post" name="form1" id="formNeedValidation" class="form form-submit-ticket ticket-create" novalidate>
                    <?php hesk_show_info($hesklang['mfa_introduction']); ?>
                    <?php if ($customerUserContext['mfa_enrollment'] > 0) {
                        hesk_show_notice($hesklang['mfa_reset_warning']);
                    } ?>
                    <div data-step="1">
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
                    <div class="step-item step-1">
                        <div><strong><?php echo $hesklang['mfa_select_method_colon']; ?><br>&nbsp;</strong></div>
                        <div class="radio-list">
                            <div class="radio-custom">
                                <input type="radio" id="mfa_method_email" name="mfa-method" value="1" <?php echo intval($_SESSION['customer']['mfa_enrollment']) === 1 ? 'checked' : ''; ?>>
                                <label for="mfa_method_email">
                                    <strong><?php echo $hesklang['mfa_method_email']; ?></strong><br>
                                    <span><?php echo sprintf($hesklang['mfa_method_email_subtext'], $_SESSION['customer']['email']); ?><br>&nbsp;</span>
                                </label>
                            </div>
                            <div class="radio-custom">
                                <input type="radio" id="mfa_method_auth_app" name="mfa-method" value="2" <?php echo intval($_SESSION['customer']['mfa_enrollment']) === 2 ? 'checked' : ''; ?>>
                                <label for="mfa_method_auth_app">
                                    <strong><?php echo $hesklang['mfa_method_auth_app']; ?></strong><br>
                                    <span><?php echo $hesklang['mfa_method_auth_app_subtext']; ?><br>&nbsp;</span>
                                </label>
                            </div>
                            <?php if ($hesk_settings['require_mfa'] === 0): ?>
                            <div class="radio-custom">
                                <input type="radio" id="mfa_method_none" name="mfa-method" value="0" <?php echo intval($_SESSION['customer']['mfa_enrollment']) === 0 ? 'checked' : ''; ?>>
                                <label for="mfa_method_none">
                                    <strong><?php echo $hesklang['mfa_method_none']; ?></strong><br>
                                    <span><?php echo $hesklang['mfa_method_none_subtext']; ?><br>&nbsp;</span>
                                </label>
                            </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="current-step" value="1">
                        <button type="submit" class="btn btn-full next" ripple="ripple"><?php echo $hesklang['wizard_next']; ?></button>
                    </div>
                </form>
                <?php
                if ($customerUserContext['mfa_enrollment'] > 0):
                    $res = hesk_dbQuery("SELECT COUNT(*) FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."mfa_backup_codes` WHERE `user_id`=".intval($_SESSION['customer']['id']) . " AND `user_type`='CUSTOMER'");
                    $num = hesk_dbResult($res,0,0);
                ?>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <form action="manage_mfa.php" method="post" name="form2" id="mfaBackupCodesForm" class="form form-submit-ticket ticket-create" novalidate>
                    <div class="step-item step-1">
                        <div>
                            <strong><?php echo $hesklang['mfa_backup_codes']; ?></strong>
                            <div class="tooltype right out-close">
                                <svg class="icon icon-info">
                                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                                </svg>
                                <div class="tooltype__content">
                                    <div class="tooltype__wrapper">
                                        <?php echo $hesklang['mfa_backup_codes_info']; ?>
                                    </div>
                                </div>
                            </div>
                            <br>&nbsp;
                        </div>
                    </div>
                    <div>
                        <p><?php echo $hesklang['mfa_backup_codes_num']; ?></p>
                        <p><?php echo sprintf($hesklang['mfa_backup_codes_num2'], $num); ?></p>
                            <div class="form-group">
                                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                                <button type="submit" name="new_codes" value="Y" class="btn btn--blue-border" ripple="ripple"><?php echo $hesklang['mfa_new_codes']; ?></button>
                                <button type="submit" name="delete_codes" value="Y" class="btn btn--blue-border" ripple="ripple"><?php echo $hesklang['mfa_del_codes']; ?></button>
                            </div>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
