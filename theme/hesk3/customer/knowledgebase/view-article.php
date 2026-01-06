<?php
global $hesk_settings, $hesklang;
/**
 * @var array $article
 * @var array $attachments
 * @var boolean $showRating
 * @var string $categoryLink
 * @var array $relatedArticles
 * @var bool $customerLoggedIn - `true` if a customer is logged in, `false` otherwise
 * @var array $customerUserContext - User info for a customer if logged in.  `null` if a customer is not logged in.
 */

// This guard is used to ensure that users can't hit this outside of actual HESK code
if (!defined('IN_SCRIPT')) {
    die();
}
define('EXTRA_PAGE_CLASSES','page-view-article');

define('ALERTS',1);
define('KBSEARCH',1);
define('RATING',1);
define('LOAD_PRISM',1);

define('OUTPUT_SEARCH_STYLING',1);
define('RENDER_COMMON_ELEMENTS',1);

define('TMP_TITLE',1); // TODO absolutelyRework

define('OUTPUT_SEARCH_JAVASCRIPT',1);

global $BREADCRUMBS;
$BREADCRUMBS = array(
    array('url' => $hesk_settings['site_url'], 'title' => $hesk_settings['site_title']),
    array('url' => $hesk_settings['hesk_url'], 'title' => $hesk_settings['hesk_title']),
);

// In this case there are also some dynamically generated breadcrumbs we need to create
foreach ($hesk_settings['public_kb_categories'][$article['catid']]['parents'] as $parent_id) {
    $BREADCRUMBS[] = array('url' => 'knowledgebase.php' . (($parent_id > 1) ? "?category={$parent_id}" : ""), 'title' => $hesk_settings['public_kb_categories'][$parent_id]['name']);
}
$BREADCRUMBS[] = array('url' => 'knowledgebase.php' . (($article['catid'] > 1) ? "?category={$article['catid']}" : ""), 'title' => $hesk_settings['public_kb_categories'][$article['catid']]['name']);
$BREADCRUMBS[] = array('title' => $article['subject']);

/* Print header */
require_once(TEMPLATE_PATH . 'customer/inc/header.inc.php');
?>
        <div class="main__content">
            <div class="contr">
                <div class="help-search">
                    <?php displayKbSearch(); ?>
                </div>
                <?php hesk3_show_messages($serviceMessages); ?>
                <div class="ticket ticket--article">
                    <div class="ticket__body">
                        <article class="ticket__body_block naked">
                            <h1><?php echo $article['subject']; ?></h1>
                            <div class="block--description browser-default">
                                <?php echo $article['content']; ?>
                            </div>
                            <?php if (count($attachments)): ?>
                            <div class="block--uploads">
                                <?php foreach ($attachments as $attachment): ?>
                                &raquo;
                                <svg class="icon icon-attach">
                                    <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-attach"></use>
                                </svg>
                                <a title="<?php echo $hesklang['dnl']; ?>" href="download_attachment.php?kb_att=<?php echo $attachment['id']; ?>" rel="nofollow">
                                    <?php echo $attachment['name']; ?>
                                </a>
                                <br>
                                <?php
                                endforeach;
                                ?>
                            </div>
                            <?php
                            endif;
                            if ($showRating):
                            ?>
                            <div id="rate-me" class="ticket__block-footer">
                                <span><?php echo $hesklang['rart']; ?></span>
                                <a href="javascript:" onclick="HESK_FUNCTIONS.rate('rate_kb.php?rating=5&amp;id=<?php echo $article['id']; ?>','article-rating');document.getElementById('rate-me').innerHTML='<?php echo hesk_slashJS($hesklang['tyr']); ?>';" class="link" rel="nofollow">
                                    <?php echo $hesklang['yes_title_case']; ?>
                                </a>
                                <span>|</span>
                                <a href="javascript:" onclick="HESK_FUNCTIONS.rate('rate_kb.php?rating=1&amp;id=<?php echo $article['id']; ?>','article-rating');document.getElementById('rate-me').innerHTML='<?php echo hesk_slashJS($hesklang['tyr']); ?>';" class="link" rel="nofollow">
                                    <?php echo $hesklang['no_title_case']; ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </article>
                    </div>
                    <div class="ticket__params">
                        <section class="params--block details">
                            <h2 class="accordion-title">
                                <span><?php echo $hesklang['ad']; ?></span>
                            </h2>
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['aid']; ?>:</div>
                                    <div class="value"><?php echo $article['id']; ?></div>
                                </div>
                                <div class="row">
                                    <div class="title"><?php echo $hesklang['category']; ?>:</div>
                                    <div class="value">
                                        <a href="<?php echo $categoryLink; ?>" class="link">
                                            <?php echo $article['cat_name']; ?>
                                        </a>
                                    </div>
                                </div>
                                <?php if ($hesk_settings['kb_date']): ?>
                                    <div class="row">
                                        <div class="title"><?php echo $hesklang['dta']; ?>:</div>
                                        <div class="value"><?php echo hesk_date($article['dt'], true); ?></div>
                                    </div>
                                <?php
                                endif;
                                if ($hesk_settings['kb_views']): ?>
                                <div class="row">
                                    <div class="title">
                                        <?php echo $hesklang['views']; ?>:
                                    </div>
                                    <div class="value">
                                        <?php echo $article['views_formatted']; ?>
                                    </div>
                                </div>
                                <?php
                                endif;
                                if ($hesk_settings['kb_rating']):
                                ?>
                                <div class="row">
                                    <div class="title">
                                        <?php echo $hesklang['rating']; ?>
                                        <?php if ($hesk_settings['kb_views']) echo ' ('.$hesklang['votes'].')'; ?>:
                                    </div>
                                    <div class="value">
                                        <div id="article-rating" class="rate">
                                            <?php echo hesk3_get_customer_rating($article['rating']); ?>
                                            <?php if ($hesk_settings['kb_views']) echo ' <span class="lightgrey">('.$article['votes_formatted'].')</span>'; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div style="text-align:right">
                                    <a href="javascript:history.go(<?php echo isset($_GET['rated']) ? '-2' : '-1'; ?>)" class="link">
                                        <svg class="icon icon-back go-back">
                                            <use xlink:href="<?php echo TEMPLATE_PATH; ?>customer/img/sprite.svg#icon-back"></use>
                                        </svg>
                                        <?php echo $hesklang['back']; ?>
                                    </a>
                                </div>
                            </div>
                        </section>
                        <?php if (count($relatedArticles) > 0): ?>
                        <section class="params--block">
                            <h2 class="accordion-title">
                                <span><?php echo $hesklang['relart']; ?></span>
                            </h2>
                            <div class="accordion-body">
                                <ul class="list">
                                    <?php foreach ($relatedArticles as $id => $subject): ?>
                                    <li>
                                        <a href="knowledgebase.php?article=<?php echo $id; ?>">
                                            <?php echo $subject; ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </section>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="divider"></div>
            </div>
        </div>
<?php
/* Print Footer */
require_once(TEMPLATE_PATH . 'customer/inc/footer.inc.php');
?>
