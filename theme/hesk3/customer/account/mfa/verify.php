<?php
global $hesk_settings, $hesklang;
/**
 * @var string $mfaMethod - The MFA method the user chose.  'EMAIL' for email, 'AUTH-APP' for authenticator app
 * @var array $model - A model of relevant data:
 *   - If $mfaMethod === 'EMAIL':
 *     - string 'emailSent' - `true` if email was successfully sent, `false` otherwise.
 *   - If $mfaMethod === 'AUTH-APP':
 *     - string 'secret' - The auth app secret
 *     - string 'qrCodeUri' - A base-64 encoded image that can be used to show a QR code
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
                <form id="verify-form" action="manage_mfa.php" method="post" name="form1" id="formNeedValidation" class="form form-submit-ticket ticket-create" novalidate>
                    <div data-step="2">
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
                    <div class="step-item step-2">
                        <?php if ($mfaMethod === 'EMAIL') { ?>
                            <div>
                                <h2><?php echo sprintf($hesklang['mfa_verification_header'], $hesklang['mfa_method_email']); ?></h2>
                                <?php
                                if ($model['emailSent']) {
                                    hesk_show_notice(sprintf($hesklang['mfa_verification_email_intro'], $_SESSION['customer']['email']), ' ', false);
                                }
                                ?>
                            </div>
                        <?php } elseif ($mfaMethod === 'AUTH-APP') { ?>
                            <div>
                                <h2><?php echo sprintf($hesklang['mfa_verification_header'], $hesklang['mfa_method_auth_app']); ?></h2>
                                <p><?php echo $hesklang['mfa_verification_auth_app_intro']; ?></p>
                                <img src="<?php echo $model['qrCodeUri']; ?>" alt="QR Code">
                                <?php
                                hesk_show_info(sprintf($hesklang['mfa_verification_auth_app_cant_scan'], chunk_split($model['secret'], 4, ' ')), ' ', false);
                                ?>
                                <p>&nbsp;</p>
                                <p><?php echo $hesklang['mfa_verification_auth_app_enter_code']; ?><br>&nbsp;</p>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label><?php echo $hesklang['mfa_code']; ?></label>
                            <input name="verification-code" id="verify-input" type="text" class="form-control" maxlength="6" placeholder="000000" autocomplete="off">
                            <input type="hidden" name="current-step" value="2">
                            <input type="hidden" name="mfa-method" value="<?php echo $mfaMethod; ?>">
                            <button type="submit" class="btn btn-full" ripple="ripple"><?php echo $hesklang['mfa_verify']; ?></button>
                        </div>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <a href="manage_mfa.php">
                            <button type="button" class="btn btn--blue-border"><?php echo $hesklang['wizard_back']; ?></button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
