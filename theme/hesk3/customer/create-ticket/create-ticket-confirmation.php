<?php
global $hesk_settings, $hesklang;
/**
 * @var string $trackingId
 * @var boolean $emailProvided
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-create-ticket-confirmation');

define('ALERTS',1);

define('RENDER_COMMON_ELEMENTS',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['ticket_submitted'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <?php hesk3_show_messages($serviceMessages); ?>
                <?php hesk3_show_messages($messages); ?>
                <div class="main__content notice-flash">
                    <div class="notification green">
                        <p><b><?php echo $hesklang['ticket_submitted']; ?></b></p>
                        <p>
                            <?php echo $hesklang['ticket_submitted_success']; ?>: <span class="font-weight-bold"><?php echo $trackingId; ?></span>
                            <br><br>
                            <?php
                            if (!$emailProvided) {
                                echo '<span style="color:var(--error-3);">' . $hesklang['write_down_notice'] . '</span>';
                            }
                            if ($emailProvided && $hesk_settings['notify_new'] && $hesk_settings['spam_notice']) {
                                echo '<span style="color:var(--error-3);">' . $hesklang['spam_inbox_notice'] . '</span>';
                            }
                            ?><br><br>
                            <a class="btn btn-full" ripple="ripple" href="<?php echo $hesk_settings['hesk_url']; ?>/ticket.php?track=<?php echo $trackingId ?>">
                                <?php echo $hesklang['view_your_ticket']; ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
