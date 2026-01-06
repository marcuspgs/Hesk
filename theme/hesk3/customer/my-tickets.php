<?php
global $hesk_settings, $hesklang;


/**
 * @var array $customerUserContext - User info for the customer.
 * @var array $tickets - Page of tickets to be displayed
 * @var array $ticketCounts - Count of open and closed tickets.  Array indices:
 *     open - Number of open tickets in search results
 *     closed - Number of closed tickets in search results
 * @var array $admins - List of staff members
 * @var string $searchCriteria - Search criteria the user entered.  Empty string if no search was performed.
 * @var string $searchType - Search type (trackid, subject, message) from the user.  Empty string if no search was performed.
 * @var string $status - Ticket status to filter down by. Either 'ALL' (open and closed), 'OPEN', or 'CLOSED'.  Default: 'ALL'
 * @var array $ordering - Column and direction ticket results are sorted by.
 *     orderBy - The column tickets are currently ordered by
 *     orderDirection - Direction results are ordered (asc or desc)
 * @var array $paging - Array of paging-related details.  Can be used to build a proper pager component.
 *     pageNumber - Current page number of results
 *     pageSize - The requested page size
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-my-tickets');

define('ALERTS',1);
define('MY_TICKETS_SEARCH',1);
define('PAGER',1);

define('RENDER_COMMON_ELEMENTS',1);

define('OUTPUT_SEARCH_JAVASCRIPT',1);

$totalCount = $ticketCounts['open'] + $ticketCounts['closed'];
$totalNumberOfPages = intval($totalCount / $paging['pageSize']);
if ($totalCount % $paging['pageSize'] !== 0) {
    $totalNumberOfPages++;
}

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
    array('title' => $hesklang['customer_my_tickets_heading'])
);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <?php hesk3_show_messages($serviceMessages); ?>
                <div class="help-search">
                    <h1 class="search__title"><?php echo $hesklang['customer_my_tickets_heading']; ?></h1>
                    <?php displayMyTicketsSearch($searchType, $searchCriteria); ?>
                </div>
                <div class="table-wrap">
                    <table id="default-table" class="table sindu-table">
                        <thead>
                        <tr>
                            <?php
                            foreach ($hesk_settings['customer_ticket_list'] as $id => $field):
                                if ( ! key_exists($field, $hesk_settings['possible_customer_ticket_list'])) {
                                    unset($hesk_settings['customer_ticket_list'][$id]);
                                    continue;
                                }
                            ?>
                            <th class="sindu-handle <?php echo $ordering['orderBy'] === $field ? hesk_mb_strtolower($ordering['orderDirection']) : '' ?>">
                                <a href="<?php echo build_sort_url($field, $ordering, $searchType, $searchCriteria, $paging); ?>" aria-label="<?php echo ($hesklang['sort_by'] . ' ' .  $field); ?>">
                                    <div class="sort">
                                        <span><?php echo $hesk_settings['possible_customer_ticket_list'][$field]; ?></span>
                                        <i class="handle"></i>
                                    </div>
                                </a>
                            </th>
                            <?php endforeach; ?>
                            <th class="sindu-handle <?php echo $ordering['orderBy'] === 'priority' ? hesk_mb_strtolower($ordering['orderDirection']) : '' ?>">
                                <a href="<?php echo build_sort_url('priority', $ordering, $searchType, $searchCriteria, $paging); ?>" aria-label="<?php echo ($hesklang['sort_by'] . ' ' .  $hesklang['priority']); ?>">
                                    <div class="sort">
                                        <span><?php echo $hesklang['priority']; ?></span>
                                        <i class="handle"></i>
                                    </div>
                                </a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (count($tickets) === 0): ?>
                            <td colspan="<?php echo count($hesk_settings['customer_ticket_list'])+1; ?> "><span role="alert"><?php echo $hesklang['no_results_found']; ?></span></td>
                        <?php endif; ?>
                        <?php foreach ($tickets as $ticket):
                            ?>
                            <tr <?php if (intval($ticket['status_id']) === 2) { echo 'class="new"'; } ?>>
                                <?php
                                    // Show Sequential ID and link it to the ticket page
                                    if ( in_array('id', $hesk_settings['customer_ticket_list']) )
                                        if ($hesk_settings['sequential']): ?>
                                    <td><?php echo $ticket['id']; ?></td>
                                <?php endif; ?>
                                <?php
                                // Show Tracking ID and link it to the ticket page
                                if ( in_array('trackid', $hesk_settings['customer_ticket_list']) )
                                {
                                ?>
                                    <td>
                                        <a href="ticket.php?track=<?php echo stripslashes($ticket['trackid']); ?>">
                                            <?php echo $ticket['trackid']; ?>
                                        </a>
                                    </td>
                                <?php
                                }
                                ?>
                                <?php
                                    // Show date submitted
                                    if ( in_array('dt', $hesk_settings['customer_ticket_list']) )
                                    {
                                        switch ($hesk_settings['submittedformat'])
                                        {
                                            case 1:
                                                $ticket['dt'] = hesk_date($ticket['dt'], true, true, true, $hesk_settings['format_timestamp']);
                                                break;
                                            case 2:
                                                $ticket['dt'] = hesk_time_lastchange($ticket['dt']);
                                                break;
                                            case 3:
                                                $ticket['dt'] = hesk_date($ticket['dt'], true, true, true, $hesk_settings['format_date']);
                                                break;
                                            case 4:
                                                $ticket['dt'] = hesk_date($ticket['dt'], true, true, true, $hesk_settings['format_submitted']);
                                                break;
                                            default:
                                                $ticket['dt'] = hesk_time_since( strtotime($ticket['dt']) );
                                        }
                                        echo '<td>'.$ticket['dt'].'</td>';
                                    }
                                ?>
                                <?php
                                    if ( in_array('lastchange', $hesk_settings['customer_ticket_list']) )
                                    {
                                ?>
                                    <td><?php echo $ticket['lastchange']; ?></td>
                                <?php } ?>
                                <?php
                                    if ( in_array('category', $hesk_settings['customer_ticket_list']) )
                                    {
                                        $ticket['category_name'] = isset($hesk_settings['categories'][$ticket['category']]) ? $hesk_settings['categories'][$ticket['category']] : $hesklang['catd'];
                                        echo '<td class="category-'.intval($ticket['category']).'">'.$ticket['category_name'].'</td>';
                                    }
                                ?>
                                <?php
                                // Show customer name
                                if ( in_array('name', $hesk_settings['customer_ticket_list']) )
                                {
                                    echo '<td>'.$ticket['u_name'];

                                    if (intval($ticket['customer_count']) > 1) {
                                        echo '<span class="customer-count">'.sprintf($hesklang['customer_count_x_more'], intval($ticket['customer_count']) - 1).'</span>';
                                    }

                                    echo '</td>';
                                }
                                // Show customer email
                                if ( in_array('email', $hesk_settings['customer_ticket_list']) )
                                {
                                    echo '<td>' . (strlen($ticket['u_email']) ? '<a href="mailto:'.$ticket['u_email'].'">'.$hesklang['clickemail'].'</a>' : '');

                                    if (intval($ticket['email_count']) > 1) {
                                        $subtraction_amount = strlen($ticket['u_email']) ? 1 : 0;
                                        echo '<span class="customer-count">'.sprintf($hesklang['customer_count_x_more'], intval($ticket['email_count']) - $subtraction_amount).'</span>';
                                    }

                                    echo '</td>';
                                }
                                ?>
                                <?php
                                // Show Subject
                                if ( in_array('subject', $hesk_settings['customer_ticket_list']) )
                                {
                                ?>
                                <td>
                                    <a href="ticket.php?track=<?php echo stripslashes($ticket['trackid']); ?>">
                                        <?php echo $ticket['subject']; ?>
                                    </a>
                                </td>
                                <?php } ?>
                                <?php
                                // Show Status
                                if ( in_array('status', $hesk_settings['customer_ticket_list']) )
                                {
                                ?>
                                <td><?php echo $ticket['status']; ?></td>
                                <?php } ?>
                                <?php
                                    // Show ticket owner
                                if ( in_array('owner', $hesk_settings['customer_ticket_list']) )
                                {
                                    if ($ticket['owner'])
                                    {
                                        $ticket['owner'] = isset($admins[$ticket['owner']]) ? $admins[$ticket['owner']] : $hesklang['unas'];
                                    }
                                    else
                                    {
                                        $ticket['owner'] = $hesklang['unas'];
                                    }
                                    echo '<td>'.$ticket['owner'].'</td>';
                                }

                                // Show number of all replies
                                if ( in_array('replies', $hesk_settings['customer_ticket_list']) )
                                {
                                    echo '<td>'.$ticket['replies'].'</td>';
                                }

                                // Show number of staff replies
                                if ( in_array('staffreplies', $hesk_settings['customer_ticket_list']) )
                                {
                                    echo '<td>'.$ticket['staffreplies'].'</td>';
                                }

                                // Show last replier
                                if ( in_array('lastreplier', $hesk_settings['customer_ticket_list']) )
                                {
                                    if ($ticket['lastreplier'])
                                    {
                                        $ticket['repliername'] = isset($admins[$ticket['replierid']]) ? $admins[$ticket['replierid']] : $hesklang['staff'];
                                    }
                                    else
                                    {
                                        $customer_name = $ticket['lastreplier_customername'] === null ? $customerUserContext['name'] : $ticket['lastreplier_customername'];
                                        $ticket['repliername'] = $customer_name;
                                    }
                                    echo '<td>'.$ticket['repliername'].'</td>';
                                }

                                // Show time worked
                                if ( in_array('time_worked', $hesk_settings['customer_ticket_list']) )
                                {
                                    echo '<td>'.$ticket['time_worked'].'</td>';
                                }

                                // Show due date
                                if (in_array('due_date', $hesk_settings['customer_ticket_list'])) {
                                    $due_date = $hesklang['none'];
                                    if ($ticket['due_date'] != null) {
                                        $due_date = hesk_date($ticket['due_date'], false, true, false);
                                        $due_date = date($hesk_settings['format_date'], $due_date);
                                    }

                                    echo '<td>'.$due_date.'</td>';
                                }

                                // Print custom fields
                                foreach ($hesk_settings['custom_fields'] as $key => $value) {
                                    if ($value['use'] && in_array($key, $hesk_settings['customer_ticket_list']) ) {
                                        echo '<td>'.($value['type'] == 'date' ? hesk_custom_date_display_format($ticket[$key], $value['value']['date_format']) : $ticket[$key]).'</td>';
                                    }
                                }

                                ?>
                                <td class="has-flex-item">
                                    <?php $data_style = 'border-top-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';border-left-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';border-bottom-color:'.$hesk_settings['priorities'][$ticket['priority']]['color'].';' ?>
                                   <div class="value with-label priority" data-value="<?php echo $hesk_settings['priorities'][$ticket['priority']]['name']; ?>">
                                    <div class="priority_img" style="<?php echo $data_style; ?>"></div>
                                    <span class="ml5"><?php echo $hesk_settings['priorities'][$ticket['priority']]['name']; ?></span>
                                   </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="pager">
                        <?php echo sprintf($hesklang['tickets_on_pages'], $totalCount, $totalNumberOfPages); ?>
                        <?php
                        output_pager($totalNumberOfPages, $paging['pageNumber'], "my_tickets.php?search-by={$searchType}&search={$searchCriteria}");
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
<?php
function build_sort_url($sortField, $ordering, $searchType, $searchCriteria, $paging) {
    $originalUrl = "my_tickets.php?search-by={$searchType}&search={$searchCriteria}&page-number={$paging['pageNumber']}&page-size={$paging['pageSize']}&order-by={$ordering['orderBy']}&order-direction={$ordering['orderDirection']}";

    $targetSortDirection = $ordering['orderDirection'] === 'asc' && $sortField === $ordering['orderBy'] ? 'desc' : 'asc';
    $encodedField = urlencode($sortField);

    $new_url = str_replace("order-by={$ordering['orderBy']}", "order-by={$encodedField}", $originalUrl);
    $new_url = str_replace("order-direction={$ordering['orderDirection']}", "order-direction=", $new_url);
    return str_replace("order-direction=", "order-direction={$targetSortDirection}", $new_url);
}
