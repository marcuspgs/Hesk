<?php
global $hesk_settings, $hesklang;
/**
 * @var array $customerUserContext - User info for the customer.
 * @var array|null $pendingEmailChange - Indicates if the user has a pending email change. Will contain an array with the new email address, or `null` otherwise.
 * @var boolean $userCanChangeEmail - Indicates if the user is permitted to change their own email address
 * @var array $messages - Feedback messages to display, if any
 * @var array $serviceMessages - Service messages to display, if any
 * @var array $validationFailures - Fields that vailed validation when updating profile
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-profile');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['customer_profile'])
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
                <div class="ticket ticket--profile">
                    <section class="ticket__body_block naked">
                        <div class="profile__info">
                            <h1><?php echo $hesklang['customer_edit_profile']; ?></h1>
                            <?php if ( ! $userCanChangeEmail): ?>
                                <div class="form-group">
                                    <label class="label"><?php echo $hesklang['email']; ?>:</label>
                                    <?php echo $customerUserContext['email']; ?>
                                </div>
                            <?php endif; ?>
                            <form action="profile.php" method="post" aria-label="<?php echo $hesklang['customer_edit_profile']; ?>" class="form ticket-create" novalidate>
                                <div class="form-group required">
                                    <label class="label" for="name"><?php echo $hesklang['name']; ?>:</label>
                                    <input type="text" id="name" name="name" maxlength="255"
                                           class="form-control <?php if (in_array('name', $validationFailures)) {echo 'isError';} ?>"
                                           value="<?php echo $customerUserContext['name']; ?>"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                                <?php if ($hesk_settings['can_sel_lang']): ?>
                                <div class="form-group">
                                    <label class="label" for="preferred-language"><?php echo $hesklang['chol']; ?>:</label>
                                    <select class="" name="language" id="preferred-language">
                                    <?php
                                    foreach ($hesk_settings['languages'] as $lang => $info) {
                                        echo '<option value="'.$lang.'" ' . ($lang == $customerUserContext['language'] ? 'selected' : '') . '>'.$lang.'</option>';
                                    }
                                    ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="profile__control">
                                    <div class="profile__edit">
                                        <input type="hidden" name="action" value="profile">
                                        <button type="submit" class="btn btn-full wider">
                                            <?php echo $hesklang['save_changes']; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                    <section class="ticket__body_block naked">
                        <div class="profile__info">
                            <h2><?php echo $hesklang['customer_edit_pass']; ?></h2>
                            <form action="profile.php" method="post" aria-label="<?php echo $hesklang['customer_edit_pass']; ?>" class="form ticket-create" novalidate>
                                <?php hesk_show_info($hesklang['cur_pass2'] . '<br><br>' . $hesklang['cur_pass3'], ' ', false); ?>
                                <div class="form-group required">
                                    <label class="label" for="current-password"><?php echo $hesklang['cur_pass']; ?></label>
                                    <input type="password" id="current-password" name="current-password" maxlength="255"
                                           class="form-control <?php if (in_array('current-password', $validationFailures)) {echo 'isError';} ?>"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                                <div class="form-group required">
                                    <label class="label" for="password"><?php echo $hesklang['new_pass']; ?></label>
                                    <input type="password" id="password" name="password" maxlength="255"
                                           class="form-control <?php if (in_array('password', $validationFailures)) {echo 'isError';} ?>"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                                <div class="form-group required">
                                    <label class="label" for="confirm-password"><?php echo $hesklang['confirm_new_pass']; ?></label>
                                    <input type="password" id="confirm-password" name="confirm-password" maxlength="255"
                                           class="form-control <?php if (in_array('confirm-password', $validationFailures)) {echo 'isError';} ?>"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="label"><?php echo $hesklang['pwdst']; ?></label>
                                    <div style="border: 1px solid var(--white-8); width: 100%; height: 14px">
                                        <div id="progressBar" style="font-size: 1px; height: 12px; width: 0px; border: none;">
                                        </div>
                                    </div>
                                </div>
                                <div class="profile__control">
                                    <div class="profile__edit">
                                        <input type="hidden" name="action" value="password">
                                        <button type="submit" class="btn btn-full wider">
                                            <?php echo $hesklang['save_changes']; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
                <div class="ticket ticket--profile">
                    <?php if ($userCanChangeEmail): ?>
                    <section class="ticket__body_block naked">
                        <div class="profile__info">
                            <h2><?php echo $hesklang['customer_change_email']; ?></h2>
                            <?php
                            if ( ! is_null($pendingEmailChange)) {
                                hesk_show_notice(sprintf($hesklang['customer_change_email_pending'], $pendingEmailChange['new_email']) . ($pendingEmailChange['email_sent_too_recently'] ? '' : '<br><br>' . $hesklang['customer_change_resend']), ' ', false);
                            }
                            ?>
                            <form action="profile.php" method="post" aria-label="<?php echo $hesklang['customer_change_email']; ?>" class="form ticket-create" novalidate>
                                <div class="form-group required">
                                    <label class="label" for="email"><?php echo $hesklang['email']; ?></label>
                                    <input type="email" id="email" name="email" maxlength="255"
                                           class="form-control <?php if (in_array('email', $validationFailures)) {echo 'isError';} ?>"
                                           value="<?php echo $customerUserContext['email']; ?>"
                                           required>
                                    <div class="form-control__error"><?php echo $hesklang['this_field_is_required']; ?></div>
                                </div>
                                <div class="profile__control">
                                    <div class="profile__edit">
                                        <input type="hidden" name="action" value="email">
                                        <button type="submit" class="btn btn-full wider">
                                            <?php echo $hesklang['save_changes']; ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                    <?php endif; ?>
                    <section class="ticket__body_block naked">
                        <div class="profile__info">
                            <h2><?php echo $hesklang['mfa']; ?></h2>
                            <div class="subtext">
                                <?php if ($customerUserContext['mfa_enrollment'] === '0'): ?>
                                    <?php if ($hesk_settings['require_mfa_customers'] && strlen($customerUserContext['email'])): ?>
                                    <div class="text-success">
                                        <?php echo sprintf($hesklang['mfa_enabled'], $hesklang['mfa_method_email']); ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="text-danger">
                                        <?php echo $hesklang['mfa_disabled']; ?>
                                    </div>
                                    <?php endif; ?>
                                <?php elseif ($customerUserContext['mfa_enrollment'] === '1'): ?>
                                    <div class="text-success">
                                        <?php echo sprintf($hesklang['mfa_enabled'], $hesklang['mfa_method_email']); ?>
                                    </div>
                                <?php elseif ($customerUserContext['mfa_enrollment'] === '2'): ?>
                                    <div class="text-success">
                                        <?php echo sprintf($hesklang['mfa_enabled'], $hesklang['mfa_method_auth_app']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="profile__control">
                            <div class="profile__edit">
                                <a href="manage_mfa.php">
                                    <button class="btn btn-full wider">
                                        <?php echo $hesklang['mfa_manage_profile']; ?>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
<script>

    document.addEventListener("DOMContentLoaded", function() {
        $('input[name="password"]').keyup(function() {
            HESK_FUNCTIONS.checkPasswordStrength(this.value);
        });
    });
    <?php if ($hesk_settings['can_sel_lang']): ?>
    document.addEventListener("DOMContentLoaded", function() {
        $('#preferred-language').selectize();
    });
    <?php endif; ?>
</script>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
