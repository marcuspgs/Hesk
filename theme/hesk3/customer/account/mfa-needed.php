<?php
global $hesk_settings, $hesklang;
/**
 * @var array $messages - Feedback messages to be displayed, if any
 * @var array $model - Relevant information for the page:
 *   - token - A verification token used by the backend for security purposes
 *   - verifyMethod - 'EMAIL' if user is authenticating via email, or 'AUTH-APP' if authenticating via an authenticator app
 *   - email - The user's email address
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-mfa-needed');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);
define('LOAD_CSS_MODAL',1);

define('LOAD_JS_JQUERY_MODAL',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['customer_login'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <h1 class="article__heading article__heading--form">
                    <span class="icon-in-circle" aria-hidden="true">
                        <svg class="icon icon-document">
                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-team"></use>
                        </svg>
                    </span>
                    <span class="ml-1"><?php echo $hesklang['customer_login']; ?></span>
                </h1>
                <section class="ticket__body_block">
                    <div id="mfa-verify">
                        <form action="login.php" method="post" name="form1" id="verify-form" class="form ticket-create" novalidate>
                            <?php hesk3_show_messages($messages); ?>
                            <section class="form-groups centered">
                                <div class="form-group">
                                    <label class="label"><?php echo $hesklang['mfa_verification_code']; ?></label>
                                    <input type="text" name="verification-code" maxlength="6"
                                           id="verify-input"
                                           class="form-control"
                                           placeholder="000000"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                            </section>
                            <div class="form-footer">
                                <input type="hidden" name="a" value="mfa_verify">
                                <input type="hidden" name="mfa-method" value="<?php echo $model['verifyMethod']; ?>">
                                <button id="verify-submit" type="submit" class="btn btn-full" ripple="ripple"><?php echo $hesklang['mfa_verify']; ?></button>
                            </div>
                        </form>
                        <?php if ($model['verifyMethod'] === 'EMAIL'): ?>
                            &nbsp;
                            <form action="login.php" class="form" id="send-another-email-form" method="post" name="send-another-email-form" novalidate>
                                <button class="btn btn-link underline" type="submit">
                                    <?php echo $hesklang['mfa_send_another_email']; ?>
                                </button>
                                <input type="hidden" name="a" value="mfa_backup_email">
                            </form>
                            <br>
                        <?php endif; ?>
                        <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('verify-another-way');HESK_FUNCTIONS.toggleLayerDisplay('mfa-verify')"  class="underline">
                            <?php echo $hesklang['mfa_verify_another_way']; ?>
                        </a>
                    </div>
                    <div id="verify-another-way" style="display: none">
                        <ul>
                            <?php if ($model['verifyMethod'] === 'AUTH-APP'): ?>
                                <li>
                                    <div class="flex">
                                        <div class="mfa-alt-icon" aria-hidden="true">
                                            <svg class="icon icon-mail">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-mail"></use>
                                            </svg>
                                        </div>
                                        <div class="mfa-alt-text">
                                            <form action="login.php" class="form" id="email-backup-form" method="post" name="email-backup-form" novalidate>
                                                <button class="btn btn-link underline" type="submit">
                                                    <?php echo sprintf($hesklang['mfa_verify_another_way_email'], hesk_maskEmailAddress($model['email'])); ?>
                                                </button>
                                                <input type="hidden" name="a" value="mfa_backup_email">
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
                                        <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('backup-code-field')" class="underline">
                                            <?php echo $hesklang['mfa_verify_another_way_code']; ?>
                                        </a>
                                        <div id="backup-code-field" style="display: none">
                                            &nbsp;
                                            <form action="login.php" class="form" id="backup-form" method="post" name="backup-form" novalidate>
                                                <div class="form-group">
                                                    <label for="backupCode"><?php echo $hesklang['mfa_backup_code']; ?>:</label>
                                                    <input type="text" class="form-control" id="backupCode" name="backup-code" minlength="8" maxlength="9" autocomplete="off">
                                                </div>
                                                <div class="form__submit mfa">
                                                    <button class="btn btn-full" ripple="ripple" type="submit" id="backup-code-submit">
                                                        <?php echo $hesklang['s']; ?>
                                                    </button>
                                                </div>
                                                <input type="hidden" name="a" value="mfa_backup_code">
                                                <input type="hidden" name="mfa-method" value="<?php echo $model['verifyMethod']; ?>">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <p style="text-align: center">
                            <a href="javascript:HESK_FUNCTIONS.toggleLayerDisplay('verify-another-way');HESK_FUNCTIONS.toggleLayerDisplay('mfa-verify')" class="underline">
                                <?php echo $hesklang['back']; ?>
                            </a>
                        </p>
                    </div>
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
