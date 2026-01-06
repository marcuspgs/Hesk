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
define('LOAD_TABS',1);

/* Get all the required files and functions */
require(HESK_PATH . 'hesk_settings.inc.php');
require(HESK_PATH . 'inc/common.inc.php');
require(HESK_PATH . 'inc/admin_functions.inc.php');
require(HESK_PATH . 'inc/profile_functions.inc.php');
hesk_load_database_functions();

hesk_session_start();
hesk_dbConnect();
hesk_isLoggedIn();

/* Check permissions for this feature */
hesk_checkPermission('can_man_permission_groups');

/* Possible user features */
$hesk_settings['features'] = array(
'can_view_tickets',     /* User can read tickets */
'can_reply_tickets',    /* User can reply to tickets */
'can_del_tickets',      /* User can delete tickets */
'can_edit_tickets',     /* User can edit tickets */
'can_merge_tickets',    /* User can merge tickets */
'can_link_tickets',     /* User can not linked ticket*/
'can_resolve',          /* User can resolve tickets */
'can_submit_any_cat',   /* User can submit a ticket to any category/department */
'can_del_notes',        /* User can delete ticket notes posted by other staff members */
'can_change_cat',       /* User can move ticket to any category/department */
'can_change_own_cat',   /* User can move ticket to a category/department he/she has access to */
'can_due_date',         /* User can set and modify due date */
'can_man_kb',           /* User can manage knowledgebase articles and categories */
'can_man_users',        /* User can create and edit staff accounts */
'can_view_users',       /* User can view staff accounts, but not create or edit them */
'can_man_cat',          /* User can manage categories/departments */
'can_man_canned',       /* User can manage canned responses */
'can_man_ticket_tpl',   /* User can manage ticket templates */
'can_man_settings',     /* User can manage help desk settings */
'can_add_archive',      /* User can mark tickets as "Tagged" */
'can_assign_self',      /* User can assign tickets to himself/herself */
'can_assign_others',    /* User can assign tickets to other staff members */
'can_view_unassigned',  /* User can view unassigned tickets */
'can_view_ass_others',  /* User can view tickets that are assigned to other staff */
'can_view_ass_by',      /* User can view tickets he/she assigned to others */
'can_run_reports',      /* User can run reports and see statistics (only allowed categories and self) */
'can_run_reports_full', /* User can run reports and see statistics (unrestricted) */
'can_export',           /* User can export own tickets to Excel */
'can_view_online',      /* User can view what staff members are currently online */
'can_ban_emails',       /* User can ban email addresses */
'can_unban_emails',     /* User can delete email address bans. Also enables "can_ban_emails" */
'can_ban_ips',          /* User can ban IP addresses */
'can_unban_ips',        /* User can delete IP bans. Also enables "can_ban_ips" */
'can_mute_emails',      /* User can mute email addresses */
'can_unmute_emails',    /* User can delete email address muted. Also enables "can_mute_emails" */
'can_privacy',          /* User can use privacy tools (Anonymize tickets) */
'can_service_msg',      /* User can manage service messages shown in customer interface */
'can_email_tpl',        /* User can manage email templates */
'can_man_customers',    /* User can create and edit customer accounts */
'can_merge_customers',  /* User can merge two or more customers*/
'can_view_customers',   /* User can view customer accounts, but not create or edit them */
'can_man_permission_groups' /* User can view and create permission groups */
);

/* Set default values */
$default_groupdata = array(
    'name' => '',
    'categories' => [],
    'features' => [],
    'users' => []
);

/* A list of all categories */
$hesk_settings['categories'] = array();
$res = hesk_dbQuery('SELECT `id`,`name` FROM `'.hesk_dbEscape($hesk_settings['db_pfix']).'categories` ORDER BY `cat_order` ASC');
while ($row=hesk_dbFetchAssoc($res))
{
	if ( hesk_okCategory($row['id'], 0) )
    {
		$hesk_settings['categories'][$row['id']] = $row['name'];
    }
}

/* Non-admin users may not create permission groups with more permissions than they have */
if (!$_SESSION['isadmin'])
{
    /* Can only add features he/she has access to */
	$hesk_settings['features'] = array_intersect( explode(',', $_SESSION['heskprivileges']) , $hesk_settings['features']);
}

/* Use any set values, default otherwise */
foreach ($default_groupdata as $k => $v) {
	if (!isset($_SESSION['groupdata'][$k])) {
    	$_SESSION['groupdata'][$k] = $v;
    }
}

$_SESSION['groupdata'] = hesk_stripArray($_SESSION['groupdata']);

/* What should we do? */
if ($action = hesk_REQUEST('a')) {
	if ($action == 'reset_form') {
		$_SESSION['edit_groupdata'] = TRUE;
		header('Location: ./manage_permission_groups.php');
	}
	elseif ($action === 'edit')       {edit_group();}
	elseif ( defined('HESK_DEMO') )  {hesk_process_messages($hesklang['ddemo'], 'manage_permission_groups.php', 'NOTICE');}
	elseif ($action === 'new')        {new_group();}
	elseif ($action === 'save')       {update_group();}
	elseif ($action === 'remove')     {remove();}
    else 							 {hesk_error($hesklang['invalid_action']);}
}

else
{

/* If one came from the Edit page make sure we reset user values */
if (isset($_SESSION['save_groupdata']))
{
	$_SESSION['groupdata'] = $default_groupdata;
    $_SESSION['use_sort_vars'] = true;
    unset($_SESSION['save_groupdata']);
}
if (isset($_SESSION['edit_groupdata']))
{
    $_SESSION['use_sort_vars'] = true;
	$_SESSION['groupdata'] = $default_groupdata;
    unset($_SESSION['edit_groupdata']);
}

/* Print header */
require_once(HESK_PATH . 'inc/header.inc.php');

// Loader file include for AJAX Request
require_once(HESK_PATH . 'inc/loader.inc.php');

/* Print main manage users page */
require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');

/* This will handle error, success and notice messages */
if (!hesk_SESSION(array('groupdata', 'errors'))) {
    hesk_handle_messages();
}

if (!isset($_SESSION['use_sort_vars']) && isset($_SESSION['sort_vars'])) {
    unset($_SESSION['sort_vars']);
}
$saved_search = hesk_SESSION_array('sort_vars');
$sort_column = isset($saved_search['sort_column']) ? $saved_search['sort_column'] : hesk_REQUEST('sort_column');
$sort_direction = isset($saved_search['sort_direction']) ? $saved_search['sort_direction'] : hesk_REQUEST('sort_direction');

// Now set the variables in the session for later
$_SESSION['sort_vars'] = [
    'sort_column' => $sort_column,
    'sort_direction' => $sort_direction
];
?>
<div class="main__content team">
    <section class="team__head">
        <h2>
            <?php echo $hesklang['permission_groups_title']; ?>
            <div class="tooltype right out-close">
                <svg class="icon icon-info">
                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-info"></use>
                </svg>
                <div class="tooltype__content">
                    <div class="tooltype__wrapper">
                        <?php echo $hesklang['permission_groups_intro']; ?>
                    </div>
                </div>
            </div>
        </h2>
        <button class="btn btn btn--blue-border" ripple="ripple" data-action="team-create"><?php echo $hesklang['permission_groups_new']; ?></button>
    </section>
    <div class="table-wrap">
        <div class="table">
            <table id="default-table" class="table sindu-table">
                <thead>
                <tr>
                    <th class="sindu-handle <?php echo $sort_column === 'name' ? hesk_mb_strtolower($sort_direction) : '' ?>">
                        <a href="<?php echo build_sort_url($sort_column, 'name', $sort_direction); ?>">
                            <div class="sort">
                                <span><?php echo $hesklang['permission_groups_name']; ?></span>
                                <i class="handle"></i>
                            </div>
                        </a>
                    </th>
                    <th><?php echo $hesklang['permission_groups_staff_count']; ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $query_sort_column = 'name';
                if ($sort_column !== null && $sort_column == 'name') {
                    $query_sort_column = $sort_column;
                }
                $query_sort_direction = $sort_direction === 'ASC' ? 'ASC' : 'DESC';
                $res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` 
                    ORDER BY `{$query_sort_column}` {$query_sort_direction}");
                $group_to_staff_count_rs = hesk_dbQuery("SELECT `group_id`, COUNT(1) AS `cnt` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members`
                    GROUP BY `group_id`");
                $group_to_staff_count = [];
                while ($row = hesk_dbFetchAssoc($group_to_staff_count_rs)) {
                    $group_to_staff_count[$row['group_id']] = $row['cnt'];
                }

                if (hesk_dbNumRows($res) === 0) {
                    echo '<tr><td colspan="3">';
                    hesk_show_notice($hesklang['permission_groups_none'], ' ', false);
                    echo '</td></tr>';
                }
                while ($group = hesk_dbFetchAssoc($res)) {
                    if (!key_exists($group['id'], $group_to_staff_count)) {
                        $group_to_staff_count[$group['id']] = 0;
                    }

                    $can_manage_this_group = compare_user_permissions($group['id']);

                    $table_row = '';
                    if (isset($_SESSION['selgroup']) && $group['id'] == $_SESSION['selgroup']) {
                        $table_row = 'class="ticket-new"';
                        unset($_SESSION['selgroup']);
                    }

                    $modal_body = $hesklang['permission_groups_delete_confirm'];

                    $group_id = $group['id'];
                    $modal_id = hesk_generate_old_delete_modal($hesklang['confirm_deletion'],
                        $modal_body,
                        'manage_permission_groups.php?a=remove&amp;id='.$group_id.'&amp;token='.hesk_token_echo(0));
                    $edit_remove_code = '';
                    if ($can_manage_this_group) {
                        $edit_remove_code = '
                        <a href="manage_permission_groups.php?a=edit&amp;id='.$group_id.'" class="edit tooltip" title="'.$hesklang['edit'].'">
                            <svg class="icon icon-edit-ticket">
                                <use xlink:href="' . HESK_PATH . 'img/sprite.svg#icon-edit-ticket"></use>
                            </svg>
                        </a>';
                        $edit_remove_code .= '
                        <a href="javascript:" data-group-id="'.$group['id'].'" class="edit tooltip copy-group" title="'.$hesklang['permission_groups_copy'].'">
                            <svg class="icon icon-merge">
                                <use xlink:href="' . HESK_PATH . 'img/sprite.svg#icon-merge"></use>
                            </svg>
                        </a>';

                        if ($group_to_staff_count[$group_id] === 0) {
                            $edit_remove_code .= '<a href="javascript:" data-modal="[data-modal-id=\''.$modal_id.'\']"
                                title="'.$hesklang['remove'].'"
                                class="delete tooltip">
                                <svg class="icon icon-delete">
                                    <use xlink:href="' . HESK_PATH . 'img/sprite.svg#icon-delete"></use>
                                </svg>
                            </a>';
                        } else {
                            $edit_remove_code .= '<a onclick="alert(\''.hesk_makeJsString($hesklang['permission_groups_delete_not_allowed']).'\')"
                                title="'.$hesklang['permission_groups_delete_not_allowed'].'"
                                class="delete tooltip not-allowed">
                                <svg class="icon icon-delete">
                                    <use xlink:href="' . HESK_PATH . 'img/sprite.svg#icon-delete"></use>
                                </svg>
                            </a>';
                        }
                    }

                    echo <<<EOC
<tr $table_row>
<td>$group[name]</td>
<td>$group_to_staff_count[$group_id]</td>

EOC;

                    echo <<<EOC
<td class="nowrap buttons"><p>$edit_remove_code</p></td>
</tr>

EOC;
                } // End while
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $('.copy-group').click(function() {
            $('#overlay_loader').fadeIn(300);
            const groupId = $(this).attr('data-group-id');
            $.ajax({
                url: 'ajax/permission-groups/index.php?id=' + groupId,
                type: 'get',
                success: function(res) {
                    for (const category of res.categories) {
                        $('#category_' + category).attr('checked', 'checked');
                    }
                    for (const feature of res.features) {
                        $('#feature_' + feature).attr('checked', 'checked');
                    }

                    $('#overlay_loader').fadeOut(300);
                    $('button[data-action="team-create"]').click();
                }
            });
        });
    </script>
</div>
<div class="right-bar team-create" <?php echo hesk_SESSION(array('groupdata','errors')) ? 'style="display: block"' : ''; ?>>
    <div class="right-bar__body form permission-group-stepper" data-step="1">
        <h3>
            <a href="manage_permission_groups.php?a=reset_form">
                <svg class="icon icon-back">
                    <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-back"></use>
                </svg>
                <span><?php echo $hesklang['permission_groups_create_title']; ?></span>
            </a>
        </h3>
        <?php
        if (hesk_SESSION(array('groupdata', 'errors'))) {
            hesk_handle_messages();
        }
        ?>
        <form name="form1" method="post" action="manage_permission_groups.php" class="form <?php echo hesk_SESSION(array('groupdata','errors')) ? 'invalid' : ''; ?>">
            <?php
            $steps = [$hesklang['permission_groups_create_general_info'], $hesklang['menu_users']];

            $errors = hesk_SESSION(['groupdata', 'errors']);
            $errors = is_array($errors) ? $errors : [];
            ?>
            <!-- TABS -->
            <ul class="step-bar">
                <?php
                $i = 1;
                foreach ($steps as $step_name) : ?>
                    <li data-link="<?php echo $i++; ?>" data-all="<?php echo count($steps); ?>">
                        <?php echo $step_name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
            $current_step = 1;
            ?>
            <div class="step-slider">
                <div class="step-item step-<?php echo $current_step++; ?>">
                    <h4><?php echo $hesklang['permission_groups_create_general_info']; ?></h4>
                    <div class="form-group">
                        <label for="group_name"><?php echo $hesklang['permission_groups_name']; ?></label>
                        <input type="text" class="form-control <?php echo in_array('name', $errors) ? 'isError' : ''; ?>" id="group_name" name="name" maxlength="255"
                               value="<?php echo $_SESSION['groupdata']['name']; ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo $hesklang['allowed_cat']; ?></label>
                        <?php foreach ($hesk_settings['categories'] as $catid => $catname): ?>
                            <div class="checkbox-custom <?php echo in_array('categories-features', $errors) ? 'isError' : ''; ?>">
                                <input type="checkbox" id="category_<?php echo $catid; ?>" name="categories[]" value="<?php echo $catid; ?>"
                                    <?php if (in_array($catid, $_SESSION['groupdata']['categories'])) { echo 'checked'; } ?>>
                                <label for="category_<?php echo $catid; ?>"><?php echo $catname; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-group">
                        <label><?php echo $hesklang['allow_feat']; ?></label>
                        <?php foreach ($hesk_settings['features'] as $k): ?>
                            <div class="checkbox-custom <?php echo in_array('categories-features', $errors) ? 'isError' : ''; ?>">
                                <input type="checkbox" id="feature_<?php echo $k; ?>" name="features[]" value="<?php echo $k; ?>"
                                    <?php if (in_array($k, $_SESSION['groupdata']['features'])) { echo 'checked'; } ?>>
                                <label for="feature_<?php echo $k; ?>"><?php echo $hesklang[$k]; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="step-item step-<?php echo $current_step++; ?>">
                    <?php
                    $non_admins_rs = hesk_dbQuery("SELECT `id`, `name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `isadmin` = '0' ORDER BY `name` ASC");
                    if (hesk_dbNumRows($non_admins_rs) === 0) {
                        hesk_show_notice($hesklang['permission_groups_create_users_none']);
                    } else {
                        echo '<h4>'.$hesklang['menu_users'].'</h4>';
                    }
                    while ($user = hesk_dbFetchAssoc($non_admins_rs)): ?>
                        <div class="checkbox-custom <?php echo in_array('users', $errors) ? 'isError' : ''; ?>">
                            <input type="checkbox" id="user_<?php echo $user['id']; ?>" name="users[]" value="<?php echo $user['id']; ?>"
                                <?php if (in_array($user['id'], $_SESSION['groupdata']['users'])) { echo 'checked'; } ?>>
                            <label for="user_<?php echo $user['id']; ?>"><?php echo $user['name']; ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Submit -->
            <div class="right-bar__footer">
                <input type="hidden" name="a" value="new">
                <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                <button type="button" class="btn btn-border" ripple="ripple" data-action="back"><?php echo $hesklang['wizard_back']; ?></button>
                <button type="button" class="btn btn-full next" data-action="next" ripple="ripple"><?php echo $hesklang['wizard_next']; ?></button>
                <button type="submit" class="btn btn-full save" data-action="save" ripple="ripple"><?php echo $hesklang['permission_groups_create_submit']; ?></button>
            </div>
        </form>
    </div>
</div>
<?php
unset($_SESSION['use_sort_vars']);

require_once(HESK_PATH . 'inc/footer.inc.php');
exit();

} // End else


/*** START FUNCTIONS ***/


function compare_user_permissions($group_id, $compare_categories = null, $compare_features = null)
{
	global $hesk_settings;

    // Do we need to get data from the database?
    if ($compare_categories === null)
    {
        $compare_categories = [];
        $res = hesk_dbQuery("SELECT `category_id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` WHERE `group_id`=".intval($group_id));
        while ($row = hesk_dbFetchAssoc($res)) {
            $compare_categories[] = $row['category_id'];
        }

        $compare_features = [];
        $res = hesk_dbQuery("SELECT `feature` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` WHERE `group_id`=".intval($group_id));
        while ($row = hesk_dbFetchAssoc($res)) {
            $compare_features[] = $row['feature'];
        }
    }

	/* Compare categories */
    foreach ($compare_categories as $catid) {
    	if (!array_key_exists($catid, $hesk_settings['categories'])) {
        	return false;
        }
    }

	/* Compare features */
    foreach ($compare_features as $feature) {
    	if (!in_array($feature, $hesk_settings['features'])) {
        	return false;
        }
    }
    return true;
} // END compare_user_permissions()


function edit_group()
{
	global $hesk_settings, $hesklang, $default_groupdata;

	$id = intval( hesk_GET('id') ) or hesk_error("$hesklang[int_error]: $hesklang[no_valid_id]");

    $_SESSION['edit_groupdata'] = TRUE;

    if (!isset($_SESSION['save_groupdata']))
    {
        $res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` WHERE `id` = ".intval($id)." LIMIT 1");
    	$_SESSION['groupdata'] = hesk_dbFetchAssoc($res);

        $cat_rs = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` WHERE `group_id` = ".intval($id));
        $_SESSION['groupdata']['categories'] = [];
        while ($row = hesk_dbFetchAssoc($cat_rs)) {
            $_SESSION['groupdata']['categories'][] = $row['category_id'];
        }

        $features_rs = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` WHERE `group_id` = ".intval($id));
        $_SESSION['groupdata']['features'] = [];
        while ($row = hesk_dbFetchAssoc($features_rs)) {
            $_SESSION['groupdata']['features'][] = $row['feature'];
        }

        $users_rs = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members` WHERE `group_id` = ".intval($id));
        $_SESSION['groupdata']['users'] = [];
        while ($row = hesk_dbFetchAssoc($users_rs)) {
            $_SESSION['groupdata']['users'][] = $row['user_id'];
        }

    }

	/* Make sure we have permission to edit this group */
	if (!compare_user_permissions($id, $_SESSION['groupdata']['categories'], $_SESSION['groupdata']['features']) )
	{
		hesk_process_messages($hesklang['npea'],'manage_users.php');
	}

    /* Print header */
	require_once(HESK_PATH . 'inc/header.inc.php');

	/* Print main manage users page */
	require_once(HESK_PATH . 'inc/show_admin_nav.inc.php');
	?>
    <div class="right-bar team-create" style="display: block">
        <div class="right-bar__body form permission-group-stepper" data-step="1">
            <h3>
                <a href="manage_permission_groups.php?a=reset_form">
                    <svg class="icon icon-back">
                        <use xlink:href="<?php echo HESK_PATH; ?>img/sprite.svg#icon-back"></use>
                    </svg>
                    <span><?php echo $hesklang['permission_groups_edit_title']; ?></span>
                </a>
            </h3>
            <?php
            if (hesk_SESSION(array('groupdata', 'errors'))) {
                /* This will handle error, success and notice messages */
                echo '<div style="margin: -24px -24px 10px -16px;">';
                hesk_handle_messages();
                echo '</div>';
            }
            ?>
            <form name="form1" method="post" action="manage_permission_groups.php" class="form <?php echo hesk_SESSION(array('groupdata','errors')) ? 'invalid' : ''; ?>">
                <?php
                $steps = [$hesklang['permission_groups_create_general_info'], $hesklang['menu_users']];

                $errors = hesk_SESSION(['groupdata', 'errors']);
                $errors = is_array($errors) ? $errors : [];
                ?>
                <!-- TABS -->
                <ul class="step-bar">
                    <?php
                    $i = 1;
                    foreach ($steps as $step_name) : ?>
                        <li data-link="<?php echo $i++; ?>" data-all="<?php echo count($steps); ?>">
                            <?php echo $step_name; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php
                $current_step = 1;
                ?>
                <div class="step-slider">
                    <div class="step-item step-<?php echo $current_step++; ?>">
                        <h4><?php echo $hesklang['permission_groups_create_general_info']; ?></h4>
                        <div class="form-group">
                            <label for="group_name"><?php echo $hesklang['permission_groups_name']; ?></label>
                            <input type="text" class="form-control <?php echo in_array('name', $errors) ? 'isError' : ''; ?>" id="group_name" name="name" maxlength="255"
                                   value="<?php echo $_SESSION['groupdata']['name']; ?>">
                        </div>
                        <div class="form-group">
                            <label><?php echo $hesklang['allowed_cat']; ?></label>
                            <?php foreach ($hesk_settings['categories'] as $catid => $catname): ?>
                                <div class="checkbox-custom <?php echo in_array('categories-features', $errors) ? 'isError' : ''; ?>">
                                    <input type="checkbox" id="category_<?php echo $catid; ?>" name="categories[]" value="<?php echo $catid; ?>"
                                        <?php if (in_array($catid, $_SESSION['groupdata']['categories'])) { echo 'checked'; } ?>>
                                    <label for="category_<?php echo $catid; ?>"><?php echo $catname; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-group">
                            <label><?php echo $hesklang['allow_feat']; ?></label>
                            <?php foreach ($hesk_settings['features'] as $k): ?>
                                <div class="checkbox-custom <?php echo in_array('categories-features', $errors) ? 'isError' : ''; ?>">
                                    <input type="checkbox" id="feature_<?php echo $k; ?>" name="features[]" value="<?php echo $k; ?>"
                                        <?php if (in_array($k, $_SESSION['groupdata']['features'])) { echo 'checked'; } ?>>
                                    <label for="feature_<?php echo $k; ?>"><?php echo $hesklang[$k]; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="step-item step-<?php echo $current_step++; ?>">
                        <?php
                        $non_admins_rs = hesk_dbQuery("SELECT `id`, `name` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `isadmin` = '0' ORDER BY `name` ASC");
                        if (hesk_dbNumRows($non_admins_rs) === 0) {
                            hesk_show_notice($hesklang['permission_groups_create_users_none']);
                        } else {
                            echo '<h4>'.$hesklang['menu_users'].'</h4>';
                        }
                        while ($user = hesk_dbFetchAssoc($non_admins_rs)): ?>
                            <div class="checkbox-custom <?php echo in_array('users', $errors) ? 'isError' : ''; ?>">
                                <input type="checkbox" id="user_<?php echo $user['id']; ?>" name="users[]" value="<?php echo $user['id']; ?>"
                                    <?php if (in_array($user['id'], $_SESSION['groupdata']['users'])) { echo 'checked'; } ?>>
                                <label for="user_<?php echo $user['id']; ?>"><?php echo $user['name']; ?></label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Submit -->
                <div class="right-bar__footer">
                    <input type="hidden" name="a" value="save">
                    <input type="hidden" name="groupid" value="<?php echo $id; ?>" />
                    <input type="hidden" name="token" value="<?php hesk_token_echo(); ?>">
                    <button type="button" class="btn btn-border" ripple="ripple" data-action="back"><?php echo $hesklang['wizard_back']; ?></button>
                    <button type="button" class="btn btn-full next" data-action="next" ripple="ripple"><?php echo $hesklang['wizard_next']; ?></button>
                    <button type="submit" class="btn btn-full save" data-action="save" ripple="ripple"><?php echo $hesklang['save_changes']; ?></button>
                </div>
            </form>
        </div>
    </div>

	<?php
	require_once(HESK_PATH . 'inc/footer.inc.php');
	exit();
} // End edit_group()


function new_group()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check('POST');

	$mygroup = hesk_validateGroupInfo();

    /* Check for duplicate group names */
	$result = hesk_dbQuery("SELECT 1 FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` WHERE `name` = '".hesk_dbEscape($mygroup['name'])."' LIMIT 1");
	if (hesk_dbNumRows($result) != 0)
	{
        // Stripping slashes because they're added in hesk_validateGroupInfo()
        hesk_process_messages(sprintf($hesklang['permission_groups_error_duplicate'], stripslashes($mygroup['name'])),'manage_permission_groups.php');
        return;
	}

    // Insert permission group
    hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` (`name`) VALUES ('".hesk_dbEscape($mygroup['name'])."')");
    $group_id = hesk_dbInsertID();

    // Insert categories
    if (!empty($mygroup['categories'])) {
        $categories_insert = [];
        foreach ($mygroup['categories'] as $category) {
            $categories_insert[] = '('.intval($group_id).', '.intval($category).')';
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` (`group_id`, `category_id`)
        VALUES ".implode(',', $categories_insert));
    }


    // Insert features
    if (!empty($mygroup['features'])) {
        $features_insert = [];
        foreach ($mygroup['features'] as $feature) {
            $features_insert[] = "(".intval($group_id).", '".hesk_dbEscape($feature)."')";
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` (`group_id`, `feature`)
        VALUES ".implode(',', $features_insert));
    }

    // Insert users
    if (!empty($mygroup['users'])) {
        $users_insert = [];
        foreach ($mygroup['users'] as $user) {
            $users_insert[] = '('.intval($group_id).', '.intval($user).')';
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members` (`group_id`, `user_id`)
        VALUES ".implode(',', $users_insert));
    }

    $_SESSION['selgroup'] = $group_id;

    unset($_SESSION['groupdata']);

    hesk_process_messages(sprintf($hesklang['permission_groups_create_success'], $mygroup['name']),'./manage_permission_groups.php','SUCCESS');
} // End new_group()


function update_group()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check('POST');

    $_SESSION['save_groupdata'] = TRUE;

	$tmp = intval( hesk_POST('groupid') ) or hesk_error("$hesklang[int_error]: $hesklang[no_valid_id]");

    $_SERVER['PHP_SELF'] = './manage_permission_groups.php?a=edit&id='.$tmp;
	$mygroup = hesk_validateGroupInfo();
    $mygroup['id'] = $tmp;

    // Make sure we have permission to edit this group
    if (!compare_user_permissions($mygroup['id']))
    {
        hesk_process_messages($hesklang['npea'],'manage_users.php');
    }

    /* Check for duplicate group names */
	$res = hesk_dbQuery("SELECT * FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` WHERE `name` = '".hesk_dbEscape($mygroup['name'])."' LIMIT 1");
	if (hesk_dbNumRows($res) === 1)
	{
    	$tmp = hesk_dbFetchAssoc($res);

        /* Duplicate? */
        if (intval($tmp['id']) !== $mygroup['id'])
        {
        	hesk_process_messages(sprintf($hesklang['permission_groups_error_duplicate'], $mygroup['name']),$_SERVER['PHP_SELF']);
        }

		/* Do we have permission to edit this group? */
		if (!compare_user_permissions($tmp['id']))
		{
			hesk_process_messages($hesklang['permission_groups_error_permissions'],'manage_permission_groups.php');
		}
	}

    // Update base group
    hesk_dbQuery("UPDATE `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups`
        SET `name` = '".hesk_dbEscape($mygroup['name'])."'
        WHERE `id` = ".intval($mygroup['id']));

    // Update categories
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` 
        WHERE `group_id` = ".intval($mygroup['id']));
    if (!empty($mygroup['categories'])) {
        $categories_insert = [];
        foreach ($mygroup['categories'] as $category) {
            $categories_insert[] = '('.intval($mygroup['id']).', '.intval($category).')';
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` (`group_id`, `category_id`)
        VALUES ".implode(',', $categories_insert));
    }

    // Update features
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` 
        WHERE `group_id` = ".intval($mygroup['id']));
    if (!empty($mygroup['features'])) {
        $features_insert = [];
        foreach ($mygroup['features'] as $feature) {
            $features_insert[] = "(".intval($mygroup['id']).", '".hesk_dbEscape($feature)."')";
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` (`group_id`, `feature`)
        VALUES ".implode(',', $features_insert));
    }

    // Update users
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members`
        WHERE `group_id` = ".intval($mygroup['id'])." AND `user_id` NOT IN (SELECT `id` FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."users` WHERE `active` = 0)");
    if (!empty($mygroup['users'])) {
        $users_insert = [];
        foreach ($mygroup['users'] as $user) {
            $users_insert[] = '('.intval($mygroup['id']).', '.intval($user).')';
        }
        hesk_dbQuery("INSERT INTO `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members` (`group_id`, `user_id`)
        VALUES ".implode(',', $users_insert));
    }

    unset($_SESSION['save_groupdata']);
    unset($_SESSION['groupdata']);

    $_SESSION['selgroup'] = $mygroup['id'];

    hesk_process_messages($hesklang['permission_groups_edit_success'],'./manage_permission_groups.php','SUCCESS');
} // End update_group()


function hesk_validateGroupInfo()
{
	global $hesk_settings, $hesklang;

    $hesk_error_buffer = '';
    $errors = array();

    if (hesk_input(hesk_POST('name'))) {
        $mygroup['name'] = hesk_input(hesk_POST('name'));
    } else {
        $hesk_error_buffer .= '<li>' . $hesklang['permission_groups_error_name'] . '</li>';
        $errors[] = 'name';
    }

    /* At least one category or one feature is required */
    $mygroup['categories'] = [];
    $mygroup['features'] = [];
    $mygroup['users'] = hesk_POST_array('users');


    $categories = hesk_POST_array('categories');
    $features = hesk_POST_array('features');
    if (empty($categories) && empty($features))
    {
        $hesk_error_buffer .= '<li>' . $hesklang['permission_groups_error_missing_category_feature'] . '</li>';
        $errors[] = 'categories-features';
    }
    else
    {
        foreach ($categories as $tmp)
        {
            if (is_array($tmp))
            {
                continue;
            }

            if ($tmp = intval($tmp))
            {
                $mygroup['categories'][] = $tmp;
            }
        }

        foreach ($features as $tmp)
        {
            if (in_array($tmp, $hesk_settings['features']))
            {
                $mygroup['features'][] = $tmp;
            }
        }
    }

    /* Save entered info in session so we don't lose it in case of errors */
	$_SESSION['groupdata'] = $mygroup;

    /* Any errors */
    if (strlen($hesk_error_buffer))
    {
        $_SESSION['groupdata']['errors'] = $errors;

        $hesk_error_buffer = $hesklang['rfm'].'<br /><br /><ul>'.$hesk_error_buffer.'</ul>';
    	hesk_process_messages($hesk_error_buffer, './manage_permission_groups.php');
    }

    // One needs view tickets permissions in one has reply to permission
    if (in_array('can_reply_tickets', $mygroup['features']) && !in_array('can_view_tickets', $mygroup['features']))
    {
        $mygroup['features'][] = 'can_view_tickets';
    }

	// "can_unban_emails" feature also enables "can_ban_emails"
	if ( in_array('can_unban_emails', $mygroup['features']) && ! in_array('can_ban_emails', $mygroup['features']) )
	{
        $mygroup['features'][] = 'can_ban_emails';
	}

    // "can_unmute_emails" feature also enables "can_mute_emails"
    if ( in_array('can_unmute_emails', $mygroup['features']) && ! in_array('can_mute_emails', $mygroup['features']) )
    {
        $mygroup['features'][] = 'can_mute_emails';
    }

    // "can_unban_ips" feature also enables "can_ban_ips"
    if ( in_array('can_unban_ips', $mygroup['features']) && ! in_array('can_ban_ips', $mygroup['features']) )
    {
        $mygroup['features'][] = 'can_ban_ips';
    }

	return $mygroup;
} // End hesk_validateGroupInfo()


function remove()
{
	global $hesk_settings, $hesklang;

	/* A security check */
	hesk_token_check();

	$mygroup = intval( hesk_GET('id' ) ) or hesk_error($hesklang['no_valid_id']);

    if (!compare_user_permissions($mygroup)) {
        hesk_process_messages($hesklang['permission_groups_error_permissions'],'manage_permission_groups.php');
        return;
    }

    // Delete all user mappings
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_members` WHERE `group_id` = ".$mygroup);

    // Delete all feature mappings
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_features` WHERE `group_id` = ".$mygroup);

    // Delete all category mappings
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_group_categories` WHERE `group_id` = ".$mygroup);

    // Delete permission group
    hesk_dbQuery("DELETE FROM `".hesk_dbEscape($hesk_settings['db_pfix'])."permission_groups` WHERE `id` = ".$mygroup);

    hesk_process_messages($hesklang['permission_groups_deleted'],'./manage_permission_groups.php','SUCCESS');
} // End remove()


function build_sort_url($current_sort_field, $sort_field, $current_sort_direction) {
    $target_sort_direction = $current_sort_direction === 'ASC' && $sort_field === $current_sort_field ? 'DESC' : 'ASC';
    $encoded_field = urlencode($sort_field);

    return "manage_users.php?sort_column={$encoded_field}&sort_direction={$target_sort_direction}";
}
?>
