<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_manufacturer_page_object_by_type(&$types)
{
    $types[PAGE_TYPE_MANUFACTURER] = [
        'content' => 'manufacturer',
        'single' => 'manufacturer.item',
        'name' => 'manufacturer.list',
        'add_name' => 'manufacturer.add',
        'edit_name' => 'manufacturer.editing',
        'new_name' => 'manufacturer.new',
        'exclusive' => true, // indicates that this page type should not be combined with other pages
        'hide_fields' => [
            'position' => true,
        ]
    ];
}

/**
 * Idempotent install helper: adds the ?:pages.manufacturer_elem_type column only if it
 * does not exist yet. This avoids the fatal "Duplicate column name" (1060) error that a
 * plain ALTER in addon.xml would raise on re-install (when the column was left behind by a
 * previous partial install).
 *
 * NOTE: we deliberately do NOT touch the addon status here — the <status>active</status>
 * tag in addon.xml already activates the addon. Calling fn_update_addon_status() during
 * install is forbidden: the addon is not yet fully registered in ?:addons, which causes a
 * fatal error and breaks the install.
 *
 * @return bool
 */
function fn_manufacturer_install()
{
    // NOTE: db_get_column() does NOT exist in this CS-Cart build (see AGENTS.md).
    // We always probe the column via a raw SHOW COLUMNS query before any ALTER,
    // so the install never fails with "Duplicate column name (1060)".
    $is_exists = db_get_row(
        "SHOW COLUMNS FROM ?:pages LIKE 'manufacturer_elem_type'"
    );

    if (empty($is_exists)) {
        db_query(
            "ALTER TABLE ?:pages ADD manufacturer_elem_type char(1) NOT NULL DEFAULT 'C'"
        );
    }

    return true;
}

function fn_manufacturer_remove_pages()
{
    // Old custom tables of the 1.x architecture (manufacturer_categories,
    // manufacturer_catalog_tree) are not created in 2.x and must NOT be dropped
    // or referenced here. We only clean up the native ?:pages tree.

    // Drop the manufacturer_elem_type column if it still exists (idempotent).
    $is_exists = db_get_row(
        "SHOW COLUMNS FROM ?:pages LIKE 'manufacturer_elem_type'"
    );
    if (!empty($is_exists)) {
        db_query("ALTER TABLE ?:pages DROP COLUMN manufacturer_elem_type");
    }

    // Use a hard-coded 'M' instead of PAGE_TYPE_MANUFACTURER: during uninstall the core
    // does not load this addon's config.php, so the constant is undefined. In PHP 8.2 an
    // undefined constant raises a Fatal Error and aborts the uninstall.
    $pages = db_get_fields("SELECT page_id FROM ?:pages WHERE page_type = ?s ", 'M');

    foreach ($pages as $page_id) {
        $exists = db_get_field('SELECT page_id FROM ?:pages WHERE page_id = ?i', (int) $page_id);
        if ($exists) {
            fn_delete_page($page_id, $recurse = true);
        }
    }
}

/**
 * Uninstall the addon. Column drop and manufacturer-page removal are fully
 * idempotent (guarded by SHOW COLUMNS / existence checks), so uninstall never
 * fails on a partially cleaned database.
 *
 * @return bool
 */
function fn_manufacturer_uninstall()
{
    fn_manufacturer_remove_pages();

    return true;
}

/**
 * Returns a language key for the manufacturer element type (M/C/G) used to build
 * dynamic page titles ("new"/"edit").
 *
 * @param string $mode      'add' or 'edit'
 * @param string $elem_type M - manufacturer, C - category, G - product group
 *
 * @return string
 */
function fn_manufacturer_get_type_title($mode, $elem_type)
{
    $map = [
        'M' => ['manufacturer.new', 'manufacturer.editing'],
        'C' => ['manufacturer.category_new', 'manufacturer.category_editing'],
        'G' => ['manufacturer.product_group_new', 'manufacturer.product_group_editing'],
    ];

    $idx = ($mode === 'edit' || $mode === 'update') ? 1 : 0;
    $titles = isset($map[$elem_type]) ? $map[$elem_type] : $map['C'];

    return isset($titles[$idx]) ? $titles[$idx] : $titles[0];
}

function fn_manufacturer_post_get_pages(&$pages, $params, $lang_code)
{
    $manufacturer_pages = array();
    foreach ($pages as $idx => $page) {
        if (!empty($page['page_type']) && $page['page_type'] == PAGE_TYPE_MANUFACTURER) {
            // Normalize the element type so templates always get a reliable marker
            // (M - manufacturer, C - category, G - product group). Falls back to 'C'
            // when the column was added later / pages predate it / value is empty.
            $elem_type = isset($page['manufacturer_elem_type']) ? (string) $page['manufacturer_elem_type'] : '';
            if (!in_array($elem_type, ['M', 'C', 'G'], true)) {
                $elem_type = empty($page['parent_id']) ? 'M' : 'C';
            }
            $pages[$idx]['manufacturer_elem_type'] = $elem_type;

            $manufacturer_pages[$idx] = $page['page_id'];
            if (!empty($page['description'])) {
                if (strpos($page['description'], MANUFACTURER_CUT) !== false) {
                    [$pages[$idx]['spoiler']] = explode(MANUFACTURER_CUT, $page['description'], 2);
                } else {
                    $pages[$idx]['spoiler'] = $page['description'];
                }
            }

            if (!empty($page['subpages'])) {
                fn_manufacturer_post_get_pages($pages[$idx]['subpages'], $params, $lang_code);
            }
        }
    }

    if (!empty($manufacturer_pages)) {

        $images = array();
        if (!empty($params['get_image'])) {
            $images = fn_get_image_pairs($manufacturer_pages, IMAGE_TYPE_MANUFACTURER_PAGE, 'M', true, false, $lang_code);
        }

        foreach ($manufacturer_pages as $idx => $page_id) {
            $pages[$idx]['main_pair'] = !empty($images[$page_id]) ? reset($images[$page_id]) : array();
        }
    }
}

function fn_manufacturer_get_page_data(&$page_data, $lang_code, $preview, $area)
{
    if ($page_data['page_type'] == PAGE_TYPE_MANUFACTURER) {
        $page_data['main_pair'] = fn_get_image_pairs($page_data['page_id'], IMAGE_TYPE_MANUFACTURER_PAGE, 'M', true, false, $lang_code);
    }
}

function fn_manufacturer_get_pages_pre(&$params, $items_per_page, $lang_code)
{
    if (!empty($params['manufacturer_page_id']) && empty($params['parent_page_id'])) {
        $parent_id = db_get_field("SELECT parent_id FROM ?:pages WHERE page_id = ?i AND page_type = ?s", $params['manufacturer_page_id'], PAGE_TYPE_MANUFACTURER);
        if ($parent_id) {
            $params['parent_id'] = $parent_id;
        } elseif ($parent_id !== '') {
            $params['parent_id'] = $params['manufacturer_page_id'];
        }
    }
}

function fn_manufacturer_get_pages(&$params, $join, $condition, $fields, $group_by, &$sortings, $lang_code)
{
    if (!empty($params['page_type']) && $params['page_type'] == PAGE_TYPE_MANUFACTURER) {
        if (!empty($params['get_tree'])) {
            $sortings['multi_level'] = array(
                '?:pages.parent_id',
                '?:pages.timestamp',
            );
        }
        db_sort($params, $sortings, 'timestamp', 'desc');
    }
}

function fn_manufacturer_update_page_post($page_data, $page_id, $lang_code, $create, $old_page_data)
{
    if (!empty($page_data['page_type']) && $page_data['page_type'] == PAGE_TYPE_MANUFACTURER) {
        fn_attach_image_pairs('manufacturer_image', IMAGE_TYPE_MANUFACTURER_PAGE, $page_id, $lang_code);
    }
}

function fn_manufacturer_delete_page($page_id)
{
    fn_delete_image_pairs($page_id, IMAGE_TYPE_MANUFACTURER_PAGE);
}

function fn_manufacturer_clone_page($page_id, $new_page_id)
{
    fn_clone_image_pairs($new_page_id, $page_id, IMAGE_TYPE_MANUFACTURER_PAGE);
}

function fn_manufacturer_sanitize_html($purifier_config, $raw_html)
{
    /** @var $purifier_config \HTMLPurifier_Config */
    $purifier_config->set('HTML.AllowedComments', array_merge(
        (array) $purifier_config->get('HTML.AllowedComments'),
        array('CUT')
    ));
}

/**
 * Generates feed items from manufacturer pages
 *
 * @param array $items_data      Feed items
 * @param array $additional_data Feed properties (title, description, etc.)
 * @param array $block_data      Block settings
 * @param string $lang_code      Two-letter language code
 */
function fn_manufacturer_generate_rss_feed(&$items_data, &$additional_data, &$block_data, &$lang_code)
{
    if (!empty($block_data['content']['filling']) && $block_data['content']['filling'] == 'manufacturer') {
        $parent_id = (int) $block_data['properties']['filling']['manufacturer']['parent_page_id'];
        $max_items = !empty($block_data['properties']['max_item']) ? $block_data['properties']['max_item'] : Registry::get('settings.Appearance.elements_per_page');

        [$pages] = fn_get_pages([
            'parent_id' => $parent_id,
            'page_type' => PAGE_TYPE_MANUFACTURER,
            'status' => 'A'
        ], $max_items, $lang_code);

        $additional_data['title'] = !empty($block_data['properties']['feed_title']) ? $block_data['properties']['feed_title'] : __('manufacturer');
        if ($parent_id) {
            $page_data = fn_get_page_data($parent_id, $lang_code);
            $additional_data['title'] .= !empty($page_data['page']) ? '::' . $page_data['page'] : '';
        }

        $additional_data['description'] = !empty($block_data['properties']['feed_description']) ? $block_data['properties']['feed_description'] : $additional_data['title'];
        $additional_data['link'] = fn_url('', 'C', 'http', $lang_code);
        $additional_data['language'] = $lang_code;
        $additional_data['lastBuildDate'] = !empty($pages[0]['timestamp']) ? $pages[0]['timestamp'] : TIME;

        foreach ($pages as $page_id => $page_data) {
            $items_data[] = array(
                'title' => $page_data['page'],
                'link' => fn_url('pages.view?page_id=' . $page_id),
                'description' => $page_data['description'],
                'pubDate' => fn_format_rss_time($page_data['timestamp'])
            );
        }
    }
}

/**
 * Returns page_id of the first root manufacturer entry.
 *
 * @return int
 */
function fn_manufacturer_get_first_page_id()
{
    return db_get_field(
        'SELECT page_id FROM ?:pages WHERE parent_id = 0 AND page_type = ?s AND page_id = id_path ORDER BY position ASC LIMIT 1',
        PAGE_TYPE_MANUFACTURER
    );
}

/**
 * The "storefront_rest_api_set_page_icons" hook handler.
 *
 * Actions performed:
 *  - Sets page icons.
 *
 * @see fn_storefront_rest_api_set_page_icons
 */
function fn_manufacturer_storefront_rest_api_set_page_icons(&$page, $sizes)
{
    if (!empty($page['main_pair'])) {
        $page['main_pair']['icons'] = fn_storefront_rest_api_generate_icons(
            $page['main_pair']['icon'],
            $sizes['main_pair']
        );
    }
}
