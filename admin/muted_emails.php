<?php
/**
 *
 * This file is part of HESK - PHP Help Desk Software.
 *
 * (c) Copyright Klemen Stirn. All rights reserved.
 * https://www.hesk.com
 *
 * For the full copyright and license agreement information visit
 * https://www.hesk.com/eula.php
 *
 */

define('IN_SCRIPT',1);
define('HESK_PATH','../');

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');

hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_mute_emails');
$can_unmute = hesk_checkPermission('can_unmute_emails', 0);

// Define required constants
define('LOAD_TABS',1);

// What should we do?
if ( $action = hesk_REQUEST('a') )
{
    if ( defined('HESK_DEMO') ) {hesk_process_messages($hesklang['ddemo'], 'muted_emails.php', 'NOTICE');}
    elseif ($action == 'mute') {mute_email();}
    elseif ($action == 'unmute' && $can_unmute) {unmute_email();}
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

/* Print main manage users page */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

/* This will handle error, success and notice messages */
hesk_handle_messages();
?>

<div class="main__content tools">
    <h2>
        <?php echo $hesklang['mute_emails']; ?>
        <div class="tooltype right out-close">
            <svg class="icon icon-info">
                <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
            </svg>
            <div class="tooltype__content">
                <div class="tooltype__wrapper">
                    <?php echo $hesklang['mute_email_intro']; ?>
                </div>
            </div>
        </div>
    </h2>
    <form action="muted_emails.php" method="post" name="form1">
        <div class="tools__add-mail form">
            <div class="form-group">
                <input type="text" name="email" class="form-control" maxlength="255" placeholder="<?php echo htmlspecialchars($hesklang['mute_an_email']); ?>" autofocus>
                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>" />
                <input type="hidden" name="a" value="mute" />
                <button type="submit" class="btn btn--blue-border" ripple="ripple"><?php echo $hesklang['save_mute_email']; ?></button>
            </div>
            <div class="mail--examples"><?php echo $hesklang['banex']; ?> john@example.com, @example.com</div>
        </div>
    </form>
    <?php
        // Get muted emails from database
        $res = hesk_dbQuery('SELECT * FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'muted_emails` ORDER BY `email` ASC');
        $num = hesk_dbNumRows($res);
    ?>
    <div class="table-wrapper email">
        <table id="default-table" class="table sindu-table">
            <thead>
            <tr>
                <th><?php echo $hesklang['email']; ?></th>
                <th><?php echo $hesklang['muted_by']; ?></th>
                <th><?php echo $hesklang['date']; ?></th>
                <?php if ($can_unmute): ?>
                    <th><?php echo $hesklang['opt']; ?></th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php if ($num < 1): ?>
            <tr>
                <td colspan="<?php echo $can_unmute ? 4 : 3; ?>"><?php echo $hesklang['no_mute_emails']; ?></td>
            </tr>
            <?php
            else:
                // List of staff
                if ( ! isset($admins) )
                {
                    $admins = array();
                    $res2 = hesk_dbQuery("SELECT `id`,`name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users`");
                    while ($row=hesk_dbFetchAssoc($res2))
                    {
                        $admins[$row['id']]=$row['name'];
                    }
                }

                while ($mute = hesk_dbFetchAssoc($res)):
                    $table_row = '';
                    if (isset($_SESSION['mute_email']['id']) && $mute['id'] == $_SESSION['mute_email']['id'])
                    {
                        $table_row = 'class="ticket-new"';
                        unset($_SESSION['mute_email']['id']);
                    }
                ?>
                <tr <?php echo $table_row; ?>>
                    <td><?php echo $mute['email']; ?></td>
                    <td><?php echo isset($admins[$mute['muted_by']]) ? $admins[$mute['muted_by']] : $hesklang['e_udel']; ?></td>
                    <td><?php echo hesk_date($mute['dt']); ?></td>
                    <?php if ($can_unmute): ?>
                    <td class="unmute">
                        <?php $modal_id = hesk_generate_old_delete_modal($hesklang['confirm_deletion'],
                            $hesklang['del_mute_confirm'],
                            'muted_emails.php?a=unmute&amp;id='. $mute['id'] .'&amp;token='. hesk_token_echo(0)); ?>
                        <a title="<?php echo $hesklang['del_mute']; ?>" href="javascript:" data-modal="[data-modal-id='<?php echo $modal_id; ?>']">
                            <?php echo $hesklang['del_mute']; ?>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile;
                endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
require_once(HESK_PATH . 'inc/footer.inc.php');
exit();


/*** START FUNCTIONS ***/

function mute_email()
{
    global $hesk_settings, $hesklang;

    // A security check
    hesk_token_check();

    // Get the email
    $email = hesk_emailCleanup( strtolower( hesk_input( hesk_REQUEST('email') ) ) );

    // Nothing entered?
    if ( ! strlen($email) )
    {
        hesk_process_messages($hesklang['enter_mute_email'],'muted_emails.php');
    }

    // Only allow one email to be entered
    $email = ($index = strpos($email, ',')) ? substr($email, 0,  $index) : $email;
    $email = ($index = strpos($email, ';')) ? substr($email, 0,  $index) : $email;

    // We don't need *@ to mute domains, remove the star if present
    if (strpos($email, '*@') === 0) {
        $email = ltrim($email, '*');
    }

    // Validate email address
    $hesk_settings['multi_eml'] = 0;

    if ( ! hesk_validateEmail($email, '', 0) && ! verify_email_domain($email) )
    {
        hesk_process_messages($hesklang['valid_mute_email'],'muted_emails.php');
    }

    // Redirect either to muted emails or ticket page from now on
    $redirect_to = ($trackingID = hesk_cleanID()) ? 'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999) : 'muted_emails.php';

    // Prevent duplicate rows
    if ( $_SESSION['mute_email']['id'] = hesk_isMutedEmail($email) )
    {
        hesk_process_messages( sprintf($hesklang['email_mute_exists'], $email) ,$redirect_to,'NOTICE');
    }

    // Type; 0 = domain, 1 = email
    $type = ($email[0] == '@') ? 0 : 1;

    // Insert the email address into database
    hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."muted_emails` (`email`, `type`, `muted_by`) VALUES ('".hesk_dbEscape($email)."', {$type}, '".intval($_SESSION['id'])."')");

    // Remember email that got muted
    $_SESSION['mute_email']['id'] = hesk_dbInsertID();

    // Show success
    hesk_process_messages( sprintf($hesklang['email_muted'], $email) ,$redirect_to,'SUCCESS');

} // End mute_email()


function unmute_email()
{
    global $hesk_settings, $hesklang;

    // A security check
    hesk_token_check();

    // Delete from mutes
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."muted_emails` WHERE `id`=" . intval( hesk_GET('id') ) );

    // Redirect either to muted emails or ticket page from now on
    $redirect_to = ($trackingID = hesk_cleanID()) ? 'admin_ticket.php?track='.$trackingID.'&Refresh='.mt_rand(10000,99999) : 'muted_emails.php';

    // Show success
    hesk_process_messages($hesklang['email_unmuted'],$redirect_to,'SUCCESS');

} // End unmute_email()


function verify_email_domain($domain)
{
    // Does it start with an @?
    $atIndex = strrpos($domain, "@");
    if ($atIndex !== 0)
    {
        return false;
    }

    // Get the domain and domain length
    $domain = substr($domain, 1);
    $domainLen = strlen($domain);

    // Check domain part length
    if ($domainLen < 1 || $domainLen > 254)
    {
        return false;
    }

    // Check domain part characters
    if ( ! preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain) )
    {
        return false;
    }

    // Domain part mustn't have two consecutive dots
    if ( strpos($domain, '..') !== false )
    {
        return false;
    }

    // All OK
    return true;

} // END verify_email_domain()

?>
