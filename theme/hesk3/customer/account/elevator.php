<?php
global $hesk_settings, $hesklang;
/**
 * @var string $message - Instructions message to be displayed
 * @var array $messages - Feedback messages to be displayed, if any
 * @var array $customerUserContext - The logged in user's context
 * @var string $verificationMethod - The method of verification (possible value: 'PASSWORD', 'EMAIL', 'AUTH-APP')
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-elevator');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);
define('LOAD_CSS_MODAL',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['elevator_header'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <section class="contr" style="margin-top: 20px;">
                <h1 class="article__heading article__heading--form">
                    <span class="icon-in-circle" aria-hidden="true">
                        <svg class="icon icon-document">
                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-lock"></use>
                        </svg>
                    </span>
                    <span class="ml-1"><?php echo $hesklang['elevator_header']; ?></span>
                </h1>
                <section class="ticket__body_block">
                    <div id="mfa-verify">
                        <?php hesk3_show_messages($messages); ?>
                        <p><?php echo $message; ?></p>
                        <form action="elevator.php" method="post" name="form1" id="verify-form" class="form ticket-create" novalidate>
                            <section class="form-groups">
                                <div class="form-group">
                                    <?php if ($verificationMethod === 'PASSWORD'): ?>
                                        <label class="label" for="regInputPassword"><?php echo $hesklang['pass']; ?></label>
                                        <input type="password" name="verification-code"
                                               id="regInputPassword"
                                               class="form-control"
                                               required>
                                        <div class="input-group-append--icon passwordIsHidden">
                                            <svg class="icon icon-eye-close">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>img/sprite.svg#icon-eye-close"></use>
                                            </svg>
                                        </div>
                                    <?php else: ?>
                                        <label class="label" for="verify-input"><?php echo $hesklang['mfa_code']; ?></label>
                                        <input name="verification-code" id="verify-input" type="text" class="form-control" maxlength="6" placeholder="000000" autocomplete="off">
                                    <?php endif; ?>
                                </div>
                            </section>
                            <div class="form-footer">
                                <button id="verify-submit" type="submit" class="btn btn-full" ripple="ripple"><?php echo $hesklang['mfa_verify']; ?></button>
                                <input type="hidden" name="mfa-method" value="<?php echo $verificationMethod; ?>">
                                <input type="hidden" name="a" value="verify">
                            </div>
                        </form>
                        <?php if ($verificationMethod === 'EMAIL'): ?>
                            <form action="elevator.php" class="form" id="send-another-email-form" method="post" name="send-another-email-form" novalidate>
                                <button class="btn btn-link" type="submit">
                                    <?php echo $hesklang['mfa_send_another_email']; ?>
                                </button>
                                <input type="hidden" name="a" value="backup_email">
                            </form>
                        <?php endif; ?>
                        <?php if ($verificationMethod !== 'PASSWORD'): ?>
                            <br>
                            <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('verify-another-way');HESK_FUNCTIONS.toggleLayerDisplay('mfa-verify')">
                                <?php echo $hesklang['mfa_verify_another_way']; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php if ($verificationMethod !== 'PASSWORD'): ?>
                        <div id="verify-another-way" style="display: none">
                            <ul>
                                <?php if ($verificationMethod === 'AUTH-APP'): ?>
                                    <li>
                                        <div class="flex">
                                            <div class="mfa-alt-icon" aria-hidden="true">
                                                <svg class="icon icon-mail">
                                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-mail"></use>
                                                </svg>
                                            </div>
                                            <div class="mfa-alt-text">
                                                <form action="elevator.php" class="form" id="email-backup-form" method="post" name="email-backup-form" novalidate>
                                                    <button class="btn btn-link" type="submit">
                                                        <?php echo sprintf($hesklang['mfa_verify_another_way_email'], hesk_maskEmailAddress($customerUserContext['email'])); ?>
                                                    </button>
                                                    <input type="hidden" name="a" value="backup_email">
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <div class="flex">
                                        <div class="mfa-alt-icon" aria-hidden="true">
                                            <svg class="icon icon-lock">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-lock"></use>
                                            </svg>
                                        </div>
                                        <div class="mfa-alt-text">
                                            <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('backup-code-field')">
                                                <?php echo $hesklang['mfa_verify_another_way_code']; ?>
                                            </a>
                                            <div id="backup-code-field" style="display: none">
                                                <form action="elevator.php" class="form" id="backup-form" method="post" name="backup-form" novalidate>
                                                    <div class="form-group">
                                                        <label for="backupCode"><?php echo $hesklang['mfa_backup_code']; ?></label>
                                                        <input type="text" class="form-control" id="backupCode" name="backup-code" minlength="8" maxlength="9" autocomplete="off">
                                                    </div>
                                                    <div class="form__submit mfa">
                                                        <button class="btn btn-full" ripple="ripple" type="submit" id="backup-code-submit">
                                                            <?php echo $hesklang['s']; ?>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="a" value="do_backup_code_verification">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            &nbsp;

                            <p style="text-align: center">
                                <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('verify-another-way');HESK_FUNCTIONS.toggleLayerDisplay('mfa-verify')">
                                    <?php echo $hesklang['back']; ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var $verifyForm = $('#verify-form');
        var $backupForm = $('#backup-form');
        $verifyForm.preventDoubleSubmission();
        $backupForm.preventDoubleSubmission();
        $('#verify-input').keyup(function() {
            if (this.value.length === 6) {
                $('#verify-form').submit();
            }
        });
        $('#backupCode').keyup(function() {
            if (this.value.length === 8 || this.value.length === 9) {
                $('#backup-form').submit();
            }
        });
        $verifyForm.submit(function() {
            $('#verify-submit').attr('disabled', 'disabled')
                .addClass('disabled');
        });
        $backupForm.submit(function() {
            $('#backup-code-submit').attr('disabled', 'disabled')
                .addClass('disabled');
        });
    });
</script>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
