<?php
global $hesk_settings, $hesklang;
/**
 * @var array $customerUserContext - User info for the customer.
 * @var string $trackingId
 * @var string $email
 * @var boolean $rememberEmail
 * @var boolean $displayForgotTrackingIdForm
 * @var boolean $submittedForgotTrackingIdForm
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-form');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);
define('LOAD_CSS_MODAL',1);

define('LOAD_JS_JQUERY_MODAL',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['view_ticket'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div style="margin-bottom: 20px;">
                    <?php hesk3_show_messages($serviceMessages); ?>
                    <?php
                    if (!$submittedForgotTrackingIdForm) {
                        hesk3_show_messages($messages);
                    }
                    ?>
                </div>
                <h1 class="article__heading article__heading--form">
                    <span class="icon-in-circle" aria-hidden="true">
                        <svg class="icon icon-document">
                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-document"></use>
                        </svg>
                    </span>
                    <span class="ml-1"><?php echo $hesklang['view_existing']; ?></span>
                </h1>
                <form action="ticket.php" method="get" name="form2" id="formNeedValidation" class="form form-submit-ticket ticket-create" novalidate>
                    <section class="form-groups centered">
                        <div class="form-group required">
                            <label class="label" for="track"><?php echo $hesklang['ticket_trackID']; ?></label>
                            <input type="text" id="track" name="track" maxlength="20" class="form-control" value="<?php echo $trackingId; ?>" required>
                            <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                        </div>
                        <?php
                        $tmp = '';
                        if ($hesk_settings['email_view_ticket'])
                        {
                            $tmp = 'document.form1.email.value=document.form2.e.value;';

                            if ($hesk_settings['require_email']):
                            ?>
                            <div class="form-group required">
                                <label class="label" for="e"><?php echo $hesklang['email']; ?></label>
                                <input type="email" class="form-control" id="e" name="e" size="35" value="<?php echo $email; ?>" required>
                                <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                            </div>
                            <?php else: ?>
                            <div class="form-group">
                                <label class="label" for="e"><?php echo $hesklang['email']; ?></label>
                                <input type="email" class="form-control" id="e"  name="e" size="35" value="<?php echo $email; ?>">
                            </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <div class="checkbox-custom">
                                    <input type="hidden" name="f" value="1">
                                    <input type="checkbox" name="r" value="Y" id="inputRememberMyEmail" <?php if ($rememberEmail) { ?>checked<?php } ?>>
                                    <label for="inputRememberMyEmail"><?php echo $hesklang['rem_email']; ?></label>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </section>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-full" ripple="ripple"><?php echo $hesklang['view_ticket']; ?></button>
                        <a href="ticket.php?forgot=1#modal-contents" data-modal="#forgot-modal" title="<?php echo $hesklang['opens_dialog']; ?>" role="button" class="link"><?php echo $hesklang['forgot_tid']; ?></a>
                    </div>
                    </form>

                    <!-- Start ticket reminder form -->
                    <div id="forgot-modal" role="dialog" aria-modal="true" aria-label="<?php echo $hesklang['forgot_tid']; ?>" class="<?php echo !$displayForgotTrackingIdForm ? 'modal' : ''; ?>">
                        <div id="modal-contents" class="<?php echo !$displayForgotTrackingIdForm ? '' : 'notification orange'; ?>" style="padding-bottom:15px">
                            <?php
                            if ($submittedForgotTrackingIdForm) {
                                hesk3_show_messages($messages);
                            }
                            ?>
                            <b><?php echo $hesklang['forgot_tid']; ?></b><br><br>
                            <?php echo $hesklang['tid_mail']; ?>
                            <form action="index.php" method="post" name="form1" id="form1" class="form">
                                <div class="form-group">
                                    <label class="label screen-reader-text skiplink" for="forgot-email"><?php echo $hesklang['email']; ?></label>
                                    <input id="forgot-email" type="email" class="form-control" name="email" value="<?php echo $email; ?>">
                                </div>
                                <div class="form-group">
                                    <div class="radio-custom">
                                        <input type="radio" name="open_only" id="open_only1" value="1" <?php echo $hesk_settings['open_only'] ? 'checked' : ''; ?>>
                                        <label for="open_only1">
                                            <?php echo $hesklang['oon1']; ?>
                                        </label>
                                    </div>
                                    <div class="radio-custom">
                                        <input type="radio" name="open_only" id="open_only0" value="0" <?php echo !$hesk_settings['open_only'] ? 'checked' : ''; ?>>
                                        <label for="open_only0">
                                            <?php echo $hesklang['oon2']; ?>
                                        </label>
                                    </div>
                                </div>

                                <?php
                                // Use Invisible reCAPTCHA?
                                if ($hesk_settings['secimg_use'] && $hesk_settings['recaptcha_use'] == 1) {
                                    define('RECAPTCHA',1);
                                    ?>
                                    <div class="g-recaptcha" data-sitekey="<?php echo $hesk_settings['recaptcha_public_key']; ?>" data-bind="forgot-tid-submit" data-callback="recaptcha_submitForm"></div>
                                <?php
                                } elseif ($hesk_settings['secimg_use']) {
                                ?>
                                <div class="captcha-remind">
                                    <div class="form-group">
                                        <?php
                                        // Use reCAPTCHA V2?
                                        if ($hesk_settings['recaptcha_use'] == 2) {
                                            define('RECAPTCHA',1);
                                            ?>
                                            <div class="g-recaptcha" data-sitekey="<?php echo $hesk_settings['recaptcha_public_key']; ?>"></div>
                                        <?php } else { ?>
                                            <img name="secimg" src="print_sec_img.php?<?php echo rand(10000,99999); ?>" width="150" height="40" alt="<?php echo $hesklang['sec_img']; ?>" title="<?php echo $hesklang['sec_img']; ?>" style="vertical-align:text-bottom">
                                            <a class="btn btn-refresh" href="javascript:void(0)" onclick="javascript:document.form1.secimg.src='print_sec_img.php?'+ ( Math.floor((90000)*Math.random()) + 10000);">
                                                <svg class="icon icon-refresh">
                                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-refresh"></use>
                                                </svg>
                                            </a>
                                            <label class="required" for="mysecnum"><?php echo $hesklang['sec_enter']; ?></label>
                                            <input type="text" id="mysecnum" name="mysecnum" size="20" maxlength="5" autocomplete="off" class="form-control">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                                }
                                ?>

                                <input type="hidden" name="a" value="forgot_tid">
                                <input type="hidden" id="js" name="forgot" value="<?php echo (hesk_GET('forgot') ? '1' : '0'); ?>">
                                <button id="forgot-tid-submit" type="submit" class="btn btn-full"><?php echo $hesklang['tid_send']; ?></button>
                            </form>
                        </div>
                    </div>
                    <!-- End ticket reminder form -->
            </div>
        </div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#select_category').selectize();
        $('a[data-modal]').on('click', function() {
            $($(this).data('modal')).modal();
            return false;
        });
        <?php if ($submittedForgotTrackingIdForm) { ?>
        $('#forgot-modal').modal();
        $('#forgot-email').select();
        <?php } ?>
    });
</script>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
