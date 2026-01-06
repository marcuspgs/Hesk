<?php
global $hesk_settings, $hesklang;
/**
 * @var array $currentCategory
 * @var array $subcategories
 * @var string $subcategoriesWidth
 * @var string $parentLink
 * @var array $articlesInCategory
 * @var array $serviceMessages
 * @var boolean $noSearchResults
 * @var array $topArticles
 * @var array $latestArticles
 * @var array $latestArticles
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-view-category');

define('ALERTS',1);
define('KBSEARCH',1);
define('RATING',1);

define('OUTPUT_SEARCH_STYLING',1);
define('RENDER_COMMON_ELEMENTS',1);

define('VIEW_CATEGORY_CSS',1); // TODO absolutelyRework

define('TMP_TITLE',1); // TODO absolutelyRework

define('OUTPUT_SEARCH_JAVASCRIPT',1);

$service_message_type_to_class = array(
    '0' => 'none',
    '1' => 'success',
    '2' => '', // Info has no CSS class
    '3' => 'warning',
    '4' => 'danger'
);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
);
// In this case there are also some dynamically generated breadcrumbs we need to create
foreach ($hesk_settings['public_kb_categories'][$currentCategory['id']]['parents'] as $parent_id) {
    $BREADCRUMBS[] = array('url' => 'knowledgebase.php' . (($parent_id > 1) ? "?category={$parent_id}" : ""), 'title' => $hesk_settings['public_kb_categories'][$parent_id]['name']);
}
$BREADCRUMBS[] = array('title' => $currentCategory['name']);
/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div class="help-search">
                    <?php if ($currentCategory['id'] == 1 && $hesk_settings['kb_enable'] == 2): ?>
                        <h1 class="search__title"><?php echo $hesklang['how_can_we_help']; ?></h1>
                    <?php endif; ?>
                    <?php displayKbSearch(); ?>
                </div>
                <?php if ($noSearchResults): ?>
                    <div class="main__content notice-flash" style="padding: 0px;">
                        <div role="alert" class="notification orange">
                            <p><b role="alert"><?php echo $hesklang['no_results_found']; ?></b></p>
                            <?php echo $hesklang['nosr']; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php hesk3_show_messages($serviceMessages); ?>
                <div class="content">
                    <?php if ($currentCategory['id'] == 1): ?>
                        <div class="block__head">
                            <span class="icon-in-circle" aria-hidden="true">
                                <svg class="icon icon-knowledge">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                                </svg>
                            </span>
                            <h2 class="h-3 ml-1"><?php echo $currentCategory['name']; ?></h2>
                        </div>
                    <?php else: ?>
                        <div class="block__head" style="padding-bottom: 32px; text-align: left ! important; display: block;">
                            <h2 class="h-3 kb--folder">
                                <svg class="icon icon-knowledge">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                                </svg>
                                <a href="knowledgebase.php">
                                    <span><?php echo $hesk_settings['public_kb_categories'][1]['name']; ?></span>
                                </a>
                                <svg class="icon icon-chevron-right">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-right"></use>
                                </svg>
                                <?php foreach ($hesk_settings['public_kb_categories'][$currentCategory['id']]['parents'] as $parent_id): ?>
                                    <?php if ($parent_id == 1) {continue;} ?>
                                    <svg class="icon icon-folder">
                                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-folder"></use>
                                    </svg>
                                    <a href="knowledgebase.php?category=<?php echo $parent_id; ?>">
                                        <span><?php echo $hesk_settings['public_kb_categories'][$parent_id]['name']; ?></span>
                                    </a>
                                    <svg class="icon icon-chevron-right">
                                        <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-chevron-right"></use>
                                    </svg>
                                <?php endforeach; ?>
                                <svg class="icon icon-folder">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-folder"></use>
                                </svg>
                                <?php echo $currentCategory['name']; ?>
                            </h2>
                        </div>
                    <?php endif; ?>
                    <?php
                    if (count($subcategories) > 0):
                    ?>
                    <div class="topics">
                        <?php foreach ($subcategories as $subcategory): ?>
                        <div class="topics__block">
                            <h2 class="topics__title">
                                <svg class="icon icon-folder">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-folder"></use>
                                </svg>
                                <span>
                                    <a class="title-link" href="knowledgebase.php?category=<?php echo $subcategory['subcategory']['id']; ?>">
                                        <?php echo $subcategory['subcategory']['name']; ?>
                                    </a>
                                </span>
                            </h2>
                            <ul class="topics__list">
                                <?php foreach ($subcategory['articles'] as $article): ?>
                                <li>
                                    <a href="knowledgebase.php?article=<?php echo $article['id']; ?>">
                                        <?php echo $article['subject']; ?>
                                    </a>
                                </li>
                                <?php
                                endforeach;
                                if ($subcategory['displayShowMoreLink']):
                                ?>
                                <li class="text-bold">
                                    <a href="knowledgebase.php?category=<?php echo $subcategory['subcategory']['id']; ?>">
                                        <?php echo $hesklang['m']; ?> &raquo;
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if (count($articlesInCategory) > 0): ?>
                <article class="article" <?php if (count($subcategories) == 0) echo 'style="margin-top: -20px"'; ?>>
                    <div class="block__head">
                        <h3 class="h-3 text-center"><?php echo $hesklang['ac']; ?></h3>
                    </div>
                    <?php foreach ($articlesInCategory as $article): ?>
                    <a href="knowledgebase.php?article=<?php echo $article['id']; ?>" class="preview">
                        <span class="icon-in-circle" aria-hidden="true">
                            <svg class="icon icon-knowledge">
                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                            </svg>
                        </span>
                        <div class="preview__text">
                            <h4 class="preview__title"><?php echo $article['subject']; ?></h4>
                            <p class="navlink__descr"><?php echo $article['content_preview']; ?></p>
                        </div>
                        <?php if ($hesk_settings['kb_views'] || $hesk_settings['kb_rating']): ?>
                            <div class="rate">
                                <?php if ($hesk_settings['kb_views']): ?>
                                    <div style="margin-right: 10px; display: -ms-flexbox; display: flex;">
                                        <svg class="icon icon-eye-close">
                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-eye-close"></use>
                                        </svg>
                                        <span class="lightgrey"><?php echo $article['views_formatted']; ?></span>
                                    </div>
                                <?php
                                endif;
                                if ($hesk_settings['kb_rating']): ?>
                                    <?php echo hesk3_get_customer_rating($article['rating']); ?>
                                    <?php if ($hesk_settings['kb_views']) echo '<span class="lightgrey">('.$article['votes_formatted'].')</span>'; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </a>
                    <!--[if IE]>
                        <p>&nbsp;</p>
                    <![endif]-->
                    <?php endforeach; ?>
                </article>
                <?php
                endif;

                // No sub-categories and no articles in this category
                if ( ! count($articlesInCategory) && ! count($subcategories)):
                ?>
                <div class="main__content notice-flash">
                    <div role="status" class="notification blue text-center">
                        <?php echo $hesklang['noac']; ?><br><br>
                        <a class="link" href="javascript:history.go(-1)"><?php echo $hesklang['back']; ?></a>
                    </div>
                </div>
                <?php
                endif;

                if (count($topArticles) > 0 || count($latestArticles) > 0):
                ?>
                <article class="article">
                    <div class="tabbed__head">
                        <ul class="tabbed__head_tabs">
                            <?php
                            if (count($topArticles) > 0):
                                ?>
                                <li class="current" data-link="tab1">
                                    <span><?php echo $hesklang['popart']; ?></span>
                                </li>
                            <?php
                            endif;
                            if (count($latestArticles) > 0):
                                ?>
                                <li data-link="tab2">
                                    <span><?php echo $hesklang['latart']; ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="tabbed__tabs">
                        <?php if (count($topArticles) > 0): ?>
                            <div class="tabbed__tabs_tab is-visible" data-tab="tab1">
                                <?php foreach ($topArticles as $article): ?>
                                    <a href="knowledgebase.php?article=<?php echo $article['id']; ?>" class="preview">
                                        <span class="icon-in-circle" aria-hidden="true">
                                            <svg class="icon icon-knowledge">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                                            </svg>
                                        </span>
                                        <div class="preview__text">
                                            <h4 class="preview__title"><?php echo $article['subject'] ?></h4>
                                            <p>
                                                <span class="lightgrey"><?php echo $hesklang['kb_cat']; ?>:</span>
                                                <span class="ml-1"><?php echo $article['category']; ?></span>
                                            </p>
                                            <p class="navlink__descr">
                                                <?php echo $article['content_preview']; ?>
                                            </p>
                                        </div>
                                        <?php if ($hesk_settings['kb_views'] || $hesk_settings['kb_rating']): ?>
                                            <div class="rate">
                                                <?php if ($hesk_settings['kb_views']): ?>
                                                    <div style="margin-right: 10px; display: -ms-flexbox; display: flex;">
                                                        <svg class="icon icon-eye-close">
                                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-eye-close"></use>
                                                        </svg>
                                                        <span class="lightgrey"><?php echo $article['views_formatted']; ?></span>
                                                    </div>
                                                <?php
                                                endif;
                                                if ($hesk_settings['kb_rating']): ?>
                                                    <?php echo hesk3_get_customer_rating($article['rating']); ?>
                                                    <?php if ($hesk_settings['kb_views']) echo '<span class="lightgrey">('.$article['votes_formatted'].')</span>'; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <!--[if IE]>
                                        <p>&nbsp;</p>
                                    <![endif]-->
                                <?php endforeach; ?>
                            </div>
                        <?php
                        endif;
                        if (count($latestArticles) > 0):
                            ?>
                            <div class="tabbed__tabs_tab <?php echo count($topArticles) === 0 ? 'is-visible' : ''; ?>" data-tab="tab2">
                                <?php foreach ($latestArticles as $article): ?>
                                    <a href="knowledgebase.php?article=<?php echo $article['id']; ?>" class="preview">
                                        <span class="icon-in-circle" aria-hidden="true">
                                            <svg class="icon icon-knowledge">
                                                <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-knowledge"></use>
                                            </svg>
                                        </span>
                                        <div class="preview__text">
                                            <h4 class="preview__title"><?php echo $article['subject'] ?></h4>
                                            <p>
                                                <span class="lightgrey"><?php echo $hesklang['kb_cat']; ?>:</span>
                                                <span class="ml-1"><?php echo $article['category']; ?></span>
                                            </p>
                                            <p class="navlink__descr">
                                                <?php echo $article['content_preview']; ?>
                                            </p>
                                        </div>
                                        <?php if ($hesk_settings['kb_views'] || $hesk_settings['kb_rating']): ?>
                                            <div class="rate">
                                                <?php if ($hesk_settings['kb_views']): ?>
                                                    <div style="margin-right: 10px; display: -ms-flexbox; display: flex;">
                                                        <svg class="icon icon-eye-close">
                                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-eye-close"></use>
                                                        </svg>
                                                        <span class="lightgrey"><?php echo $article['views_formatted']; ?></span>
                                                    </div>
                                                <?php
                                                endif;
                                                if ($hesk_settings['kb_rating']): ?>
                                                    <?php echo hesk3_get_customer_rating($article['rating']); ?>
                                                    <?php if ($hesk_settings['kb_views']) echo '<span class="lightgrey">('.$article['votes_formatted'].')</span>'; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <!--[if IE]>
                                        <p>&nbsp;</p>
                                    <![endif]-->
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endif; ?>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
