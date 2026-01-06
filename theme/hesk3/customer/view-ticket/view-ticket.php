<?php
global $hesk_settings, $hesklang;
/**
 * @var array $customerUserContext - User info for the customer.
 * @var array $messages
 * @var array $serviceMessages
 * @var array $ticket
 * @var boolean $ticketJustReopened
 * @var string $trackingID
 * @var int $numberOfReplies
 * @var array $category
 * @var array $replies
 * @var string $email
 * @var array $customFieldsBeforeMessage
 * @var array $customFieldsAfterMessage
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-view-ticket');

define('ALERTS',1);
define('CUSTOM_FIELDS',1);
define('ADD_REPLY',1);
define('LOAD_CSS_DROPZONE',1);
define('LOAD_CSS_ZEBRA',1);
define('LOAD_PRISM_OPTIONAL',1); // TODO absolutelyRework - actually just double check if it makes sense


define('RENDER_COMMON_ELEMENTS',1);
define('RENDER_HIDDEN_TICKET_FIELDS',1);

define('LOAD_JS_DROPZONE',1);
define('LOAD_JS_ZEBRA',1);

define('FOOTER_DONT_CLOSE_HTML',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('url' => ($customerUserContext !== null ? 'my_tickets.php' : 'ticket.php'), 'title' => $hesklang['customer_my_tickets_heading']),
    array('title' => ($hesk_settings['new_top'] && $ticket['replies'] ? $ticket['subject'] : $hesklang['your_ticket']))
);

// Note: we need to pass this data to login-navbar-elements.php for an edge case rendering
$hesk_settings['hidden_data'] = array(
    'ticket' => $ticket,
    'email' => $email
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
                <div class="ticket">
                    <div class="ticket__body">
                        <?php
                        // Print "Submit a reply" form?
                        if (($ticket['locked'] != 1 && $ticket['status'] != 3 && $hesk_settings['reply_top'] == 1) ||
                            $ticketJustReopened) {
                            showReplyForm($trackingID, $email, $ticketJustReopened);
                        }

                        if ($hesk_settings['new_top']) {
                            displayReplies($replies, $trackingID);
                        }
                        ?>
                        <article class="ticket__body_block original-message">
                            <h1><?php echo $ticket['subject']; ?></h1>
                            <div class="block--head">
                                <div class="d-flex">
                                    <div class="contact grid">
                                        <div class="requester-header">
                                            <span><?php echo $hesklang['m_from']; ?>:</span>
                                        </div>
                                        <div class="requester">
                                            <?php
                                            $requesters = array_filter($ticket['customers'], function($customer) { return $customer['customer_type'] === 'REQUESTER'; });
                                            $requesters = array_values($requesters); // Re-index keys
                                            if (count($requesters) && $requesters[0]['email'] === ''): ?>
                                                <div class="dropdown customer left out-close">
                                                    <label>
                                                        <svg class="icon icon-person">
                                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-person"></use>
                                                        </svg>
                                                        <span><?php echo $requesters[0]['name']; ?></span>
                                                    </label>
                                                </div>
                                            <?php else:
                                                if (count($requesters)):
                                                    $requester = $requesters[0];
                                                    ?>
                                                    <div class="dropdown customer left out-close">
                                                        <label>
                                                            <svg class="icon icon-person">
                                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-person"></use>
                                                            </svg>
                                                            <span><?php echo (strlen($requester['name']) ? $requester['name'] : ( ! empty($requester['email']) ? $requester['email'] : $hesklang['pde'] )) ; ?></span>
                                                            <svg class="icon icon-chevron-down">
                                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-down"></use>
                                                            </svg>
                                                        </label>
                                                        <ul class="dropdown-list">
                                                            <li class="noclose">
                                                                <span class="title"><?php echo $hesklang['email']; ?>:</span>
                                                                <span class="value">
                                                            <a href="mailto:<?php echo $requester['email']; ?>"><?php echo $requester['email']; ?></a>
                                                        </span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            &raquo; <time class="timeago tooltip" datetime="<?php echo date("c", strtotime($ticket['dt'])) ; ?>" title="<?php echo hesk_date($ticket['dt'], true); ?>"><?php echo hesk_date($ticket['dt'], true); ?></time>
                                        </div>
                                        <?php
                                        $ccs = array_filter($ticket['customers'], function($customer) { return $customer['customer_type'] === 'FOLLOWER'; });

                                        if (count($ccs)):
                                        ?>
                                        <div class="cc-header">
                                            <span><?php echo $hesklang['cc']; ?>:</span>
                                        </div>
                                        <div class="cc">
                                            <?php foreach ($ccs as $cc): ?>
                                                <div class="dropdown customer left out-close">
                                                    <label>
                                                        <svg class="icon icon-person">
                                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-person"></use>
                                                        </svg>
                                                        <span><?php echo $cc['name'] === '' ? $cc['email'] : $cc['name']; ?></span>
                                                        <svg class="icon icon-chevron-down">
                                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-down"></use>
                                                        </svg>
                                                    </label>
                                                    <ul class="dropdown-list">
                                                        <li class="noclose">
                                                            <span class="title"><?php echo $hesklang['email']; ?>:</span>
                                                            <span class="value">
                                                        <a href="mailto:<?php echo $cc['email']; ?>"><?php echo $cc['email']; ?></a>
                                                    </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <a title="<?php echo $hesklang['btn_print']; ?>" href="print.php?track=<?php echo $ticket['trackid'].$hesk_settings['e_query']; ?>" target="_blank" class="btn btn-action tooltip">
                                    <svg class="icon icon-print">
                                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-print"></use>
                                    </svg>
                                </a>
                            </div>
                            <?php
                            hesk3_output_custom_fields_for_display($customFieldsBeforeMessage);
                            if ($ticket['message_html'] != ''):
                                ?>
                            <div class="block--description browser-default">
                                <p><?php echo $ticket['message_html']; ?></p>
                            </div>
                            <?php
                            endif;
                            hesk3_output_custom_fields_for_display($customFieldsAfterMessage);
                            listAttachments($ticket['attachments'], $trackingID);
                            ?>
                        </article>
                        <?php
                        if (!$hesk_settings['new_top']) {
                            displayReplies($replies, $trackingID);
                        }

                        if ($ticket['locked'] != 1 && $ticket['status'] != 3 && !$hesk_settings['reply_top'] && !$ticketJustReopened) {
                            showReplyForm($trackingID, $email, false);
                        }
                        ?>
                    </div>
                    <div class="ticket__params">
                        <section class="params--block details  collapsed-on-xs">
                            <h2 class="accordion-title">
                                <span><?php echo $hesklang['ticket_details']; ?></span>
                                <a href="ticket.php?track=<?php echo $ticket['trackid'].$hesk_settings['e_query']; ?>" class="btn link">
                                    <svg class="icon icon-refresh">
                                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-refresh"></use>
                                    </svg>
                                    <span class="ml-1"><?php echo $hesklang['refresh_page']; ?></span>
                                </a>
                            </h2>
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['trackID']; ?>:</div>
                                    <div class="value"><?php echo $trackingID; ?></div>
                                </div>
                                <?php if ($hesk_settings['sequential']): ?>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['seqid']; ?>:</div>
                                    <div class="value"><?php echo $ticket['id']; ?></div>
                                </div>
                                <?php endif; ?>
                                <?php
                                if ($ticket['status'] == 3) {
                                    $status_action = ($ticket['locked'] != 1 && $hesk_settings['custopen']) ? '[<a class="link" href="change_status.php?track='.$trackingID.$hesk_settings['e_query'].'&amp;s=2&amp;Refresh='.rand(10000,99999).'&amp;token='.hesk_token_echo(0).'">'.$hesklang['open_action'].'</a>]' : '';
                                } elseif ($hesk_settings['custclose']) {
                                    $status_action = '[<a class="link" href="change_status.php?track='.$trackingID.$hesk_settings['e_query'].'&amp;s=3&amp;Refresh='.rand(10000,99999).'&amp;token='.hesk_token_echo(0).'">'.$hesklang['close_action'].'</a>]';
                                } else {
                                    $status_action = '';
                                }
                                ?>
                                <div class="row" <?php echo strlen($status_action) ? 'style="margin-bottom: 10px;"' : ''; ?>>
                                    <div class="title"><?php echo $hesklang['ticket_status']; ?>:</div>
                                    <div class="value"><?php echo hesk_get_ticket_status($ticket['status']); ?></div>
                                </div>
                                <?php if (strlen($status_action)): ?>
                                <div class="row">
                                    <div class="title">&nbsp;</div>
                                    <div class="value center"><?php echo $status_action; ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['created_on']; ?>:</div>
                                    <div class="value"><?php echo hesk_date($ticket['dt'], true); ?></div>
                                </div>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['last_update']; ?>:</div>
                                    <div class="value"><?php echo hesk_date($ticket['lastchange'], true); ?></div>
                                </div>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['last_replier']; ?>:</div>
                                    <div class="value"><?php echo $ticket['repliername']; ?></div>
                                </div>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['category']; ?>:</div>
                                    <div class="value"><?php echo $category['name']; ?></div>
                                </div>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['replies']; ?>:</div>
                                    <div class="value"><?php echo $numberOfReplies; ?></div>
                                </div>
                                <?php if ($hesk_settings['cust_urgency']): ?>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['priority']; ?>:</div>
                                    <?php $data_style = 'border-top-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';border-left-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';border-bottom-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';' ?>
                                    <div class="value with-label priority" data-value="<?php echo $hesk_settings['priorities'][$ticket['priority']]['name']; ?>">
                                    <div class="priority_img" style="<?php echo $data_style; ?>"></div><span class="ml5"><?php echo $hesk_settings['priorities'][$ticket['priority']]['name']; ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');

/*
 * Note: In this case, we have to make sure we load all footer scripts first, as otherwise it breaks some of the custom JS page code
 */
if ($hesk_settings['time_display']): ?>
    <script src="./js/timeago/jquery.timeago.js?<?php echo $hesk_settings['hesk_version']; ?>"></script>
    <?php if ($hesklang['TIMEAGO_LANG_FILE'] != 'jquery.timeago.en.js'): ?>
        <script type="text/javascript" src="./js/timeago/locales/<?php echo $hesklang['TIMEAGO_LANG_FILE']; ?>?<?php echo $hesk_settings['hesk_version']; ?>"></script>
    <?php endif; ?>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            $("time.timeago").timeago();
        });
    </script>
<?php endif;

// Helper functions
function displayReplies($replies, $trackingId) {
    global $hesklang, $hesk_settings;

    foreach ($replies as $reply) {
        /* Store unread reply IDs for later */
        if ($reply['staffid'] && !$reply['read']) {
            $unread_replies[] = $reply['id'];
        }
        ?>
        <article class="ticket__body_block <?php if ($reply['staffid']) { ?>response<?php } ?>">
            <div class="block--head">
                <div class="d-flex">
                    <div class="contact">
                        <?php echo $hesklang['reply_by']; ?>
                        <b><?php echo $reply['name']; ?></b>
                        &raquo;
                        <time class="timeago tooltip" datetime="<?php echo date("c", strtotime($reply['dt'])) ; ?>" title="<?php echo hesk_date($reply['dt'], true); ?>"><?php echo hesk_date($reply['dt'], true); ?></time>
                    </div>
                </div>
                <a title="<?php echo $hesklang['btn_print']; ?>" href="print.php?track=<?php echo $trackingId.$hesk_settings['e_query']; ?>" target="_blank" class="btn btn-action tooltip">
                    <svg class="icon icon-print">
                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-print"></use>
                    </svg>
                </a>
            </div>
            <div class="block--description browser-default">
                <p><?php echo $reply['message_html']; ?></p>
            </div>
            <?php listAttachments($reply['attachments'], $trackingId); ?>
            <?php if ($hesk_settings['rating'] && $reply['staffid']): ?>
            <div class="ticket__block-footer">
                <?php
                if ($reply['rating'] == 1) {
                    echo $hesklang['rnh'];
                } elseif ($reply['rating'] == 5) {
                    echo $hesklang['rh'];
                } else { ?>
                    <div id="rating<?php echo $reply['id']; ?>">
                        <span><?php echo $hesklang['r']; ?></span>
                        <a href="javascript:" onclick="HESK_FUNCTIONS.rate('rate.php?rating=5&amp;id=<?php echo $reply['id']; ?>&amp;track=<?php echo $trackingId; ?>','rating<?php echo $reply['id']; ?>')" class="link">
                            <?php echo $hesklang['yes_title_case']; ?>
                        </a>
                        <span>|</span>
                        <a href="javascript:" onclick="HESK_FUNCTIONS.rate('rate.php?rating=1&amp;id=<?php echo $reply['id']; ?>&amp;track=<?php echo $trackingId; ?>','rating<?php echo $reply['id']; ?>')" class="link">
                            <?php echo $hesklang['no_title_case']; ?>
                        </a>
                    </div>
                <?php }
                ?>
            </div>
            <?php endif; ?>
        </article>
        <?php
    }
}

function listAttachments($attachments, $trackingId) {
    global $hesk_settings, $hesklang;

    /* Attachments disabled or not available */
    if (!$hesk_settings['attachments']['use'] || ! strlen($attachments) ) {
        return false;
    }
    ?>
    <div class="block--uploads">
    <?php
    /* List attachments */
    $att=explode(',',substr($attachments, 0, -1));
    foreach ($att as $myatt) {
        list($att_id, $att_name) = explode('#', $myatt);
        ?>
        &raquo;
        <svg class="icon icon-attach">
            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-attach"></use>
        </svg>
        <a title="<?php echo $hesklang['dnl']; ?>" href="download_attachment.php?att_id=<?php echo $att_id; ?>&amp;track=<?php echo $trackingId.$hesk_settings['e_query']; ?>">
            <?php echo $att_name; ?>
        </a>
        <br>
        <?php
    }
    ?>
    </div>
    <?php

    return true;
}
