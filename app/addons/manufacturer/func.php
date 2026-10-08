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

define('NOMENCLATURE_NODE_TYPE_MANUFACTURER', 'M');
define('NOMENCLATURE_NODE_TYPE_CATEGORY', 'C');
define('NOMENCLATURE_NODE_TYPE_GROUP', 'G');

/**
 * Returns full node data by node_id (with descriptions for the given language).
 *
 * @param int    $node_id
 * @param string $lang_code
 *
 * @return array
 */
function fn_manufacturer_get_node_data($node_id, $lang_code = CART_LANGUAGE)
{
    $node = db_get_row(
        'SELECT ?:nomenclature_nodes.*, ?:nomenclature_node_descriptions.name, ?:nomenclature_node_descriptions.description'
        . ' FROM ?:nomenclature_nodes'
        . ' LEFT JOIN ?:nomenclature_node_descriptions'
            . ' ON ?:nomenclature_node_descriptions.node_id = ?:nomenclature_nodes.node_id'
            . ' AND ?:nomenclature_node_descriptions.lang_code = ?s'
        . ' WHERE ?:nomenclature_nodes.node_id = ?i',
        $lang_code,
        $node_id
    );

    if (!empty($node)) {
        $node['main_pair'] = fn_get_image_pairs($node_id, IMAGE_TYPE_NOMENCLATURE_MAIN, 'M', true, false, $lang_code);
        $node['has_children'] = (bool) db_get_field(
            'SELECT COUNT(*) FROM ?:nomenclature_nodes WHERE parent_id = ?i',
            $node_id
        );
    }

    return $node;
}

/**
 * Returns the tree of nomenclature nodes.
 *
 * @param array $params
 * @param int   $parent_id
 *
 * @return array
 */
function fn_manufacturer_get_nodes_tree($params = [], $parent_id = 0)
{
    $lang_code = !empty($params['lang_code']) ? $params['lang_code'] : CART_LANGUAGE;

    $where = '?:nomenclature_nodes.parent_id = ?i';
    $where_params = [(int) $parent_id];

    if (!empty($params['status'])) {
        $where .= ' AND ?:nomenclature_nodes.status = ?s';
        $where_params[] = $params['status'];
    }

    if (fn_allowed_for('MULTIVENDOR') && !empty($params['company_id'])) {
        $where .= ' AND ?:nomenclature_nodes.company_id = ?i';
        $where_params[] = (int) $params['company_id'];
    }

    $query = 'SELECT ?:nomenclature_nodes.*, ?:nomenclature_node_descriptions.name, ?:nomenclature_node_descriptions.description'
        . ' FROM ?:nomenclature_nodes'
        . ' LEFT JOIN ?:nomenclature_node_descriptions'
            . ' ON ?:nomenclature_node_descriptions.node_id = ?:nomenclature_nodes.node_id'
            . ' AND ?:nomenclature_node_descriptions.lang_code = ?s'
        . ' WHERE ' . $where
        . ' ORDER BY ?:nomenclature_nodes.position ASC, ?:nomenclature_node_descriptions.name ASC';

    $nodes = db_get_hash_array($query, 'node_id', $lang_code, ...$where_params);

    foreach ($nodes as $node_id => $node) {
        $child_params = $params;
        $nodes[$node_id]['children'] = fn_manufacturer_get_nodes_tree($child_params, $node_id);
        $nodes[$node_id]['has_children'] = !empty($nodes[$node_id]['children']);
    }

    return $nodes;
}

/**
 * Updates (creates or edits) a nomenclature node.
 *
 * @param array $node_data
 * @param int   $node_id
 * @param string $lang_code
 *
 * @return int node_id
 */
function fn_manufacturer_update_node($node_data, $node_id = 0, $lang_code = DESCR_SL)
{
    $node_data = array_merge([
        'parent_id' => 0,
        'status'    => 'A',
        'node_type' => NOMENCLATURE_NODE_TYPE_MANUFACTURER,
        'position'  => 0,
    ], (array) $node_data);

    $node_id = (int) $node_id;

    $node_table_data = [
        'parent_id' => (int) $node_data['parent_id'],
        'status'    => !empty($node_data['status']) ? $node_data['status'] : 'A',
        'node_type' => !empty($node_data['node_type']) ? $node_data['node_type'] : NOMENCLATURE_NODE_TYPE_MANUFACTURER,
        'position'  => (int) $node_data['position'],
        'seo_name'  => !empty($node_data['seo_name']) ? $node_data['seo_name'] : '',
    ];

    if (fn_allowed_for('MULTIVENDOR')) {
        $node_table_data['company_id'] = !empty($node_data['company_id']) ? (int) $node_data['company_id'] : 0;
    }

    $name = !empty($node_data['name']) ? $node_data['name'] : '';

    if ($node_id) {
        db_query('UPDATE ?:nomenclature_nodes SET ?u WHERE node_id = ?i', $node_table_data, $node_id);
    } else {
        $node_id = db_query('INSERT INTO ?:nomenclature_nodes ?e', $node_table_data);
        // id_path: root nodes have id_path equal to node_id, children get parent path + own id
        $parent = db_get_row(
            'SELECT id_path FROM ?:nomenclature_nodes WHERE node_id = ?i',
            (int) $node_data['parent_id']
        );
        $id_path = !empty($parent['id_path']) ? $parent['id_path'] . '/' . $node_id : (string) $node_id;
        db_query('UPDATE ?:nomenclature_nodes SET id_path = ?s WHERE node_id = ?i', $id_path, $node_id);
    }

    db_query(
        'REPLACE INTO ?:nomenclature_node_descriptions ?e',
        [
            'node_id'     => $node_id,
            'lang_code'   => $lang_code,
            'name'        => $name,
            'description' => !empty($node_data['description']) ? $node_data['description'] : '',
        ]
    );

    if (!empty($node_data['seo_name']) && Registry::get('addons.seo.status') == 'A') {
        fn_create_seo_name($node_id, 'n', $node_data['seo_name'], 0, 'nomenclature.view', '', $lang_code, true);
    }

    fn_attach_image_pairs('nomenclature_main', IMAGE_TYPE_NOMENCLATURE_MAIN, $node_id, $lang_code);

    return $node_id;
}

/**
 * Deletes a nomenclature node (recursively) and its product links.
 *
 * @param int $node_id
 */
function fn_manufacturer_delete_node($node_id)
{
    $node_id = (int) $node_id;
    $children = db_get_fields('SELECT node_id FROM ?:nomenclature_nodes WHERE parent_id = ?i', $node_id);

    foreach ($children as $child_id) {
        fn_manufacturer_delete_node($child_id);
    }

    db_query('DELETE FROM ?:nomenclature_links WHERE node_id = ?i', $node_id);
    db_query('DELETE FROM ?:nomenclature_node_descriptions WHERE node_id = ?i', $node_id);
    db_query('DELETE FROM ?:nomenclature_nodes WHERE node_id = ?i', $node_id);

    fn_delete_image_pairs($node_id, IMAGE_TYPE_NOMENCLATURE_MAIN);

    if (Registry::get('addons.seo.status') == 'A') {
        fn_delete_seo_name($node_id, 'n', 'nomenclature.view');
    }
}

/**
 * Returns node_id of the first root node (used on the "all manufacturers" page).
 *
 * @return int
 */
function fn_manufacturer_get_first_node_id()
{
    return (int) db_get_field(
        'SELECT node_id FROM ?:nomenclature_nodes WHERE parent_id = 0 ORDER BY position ASC LIMIT 1'
    );
}

/**
 * Returns the node name (used by the picker templates).
 *
 * @param int    $node_id
 * @param string $lang_code
 *
 * @return string
 */
function fn_manufacturer_get_node_name($node_id, $lang_code = CART_LANGUAGE)
{
    $node_id = (int) $node_id;

    if (empty($node_id)) {
        return '';
    }

    return (string) db_get_field(
        'SELECT name FROM ?:nomenclature_node_descriptions WHERE node_id = ?i AND lang_code = ?s',
        $node_id,
        $lang_code
    );
}

/**
 * Returns product_ids linked to the node.
 *
 * @param int $node_id
 *
 * @return array
 */
function fn_manufacturer_get_node_product_ids($node_id)
{
    return db_get_fields(
        'SELECT product_id FROM ?:nomenclature_links WHERE node_id = ?i',
        (int) $node_id
    );
}

/**
 * Sets the product list for the node (replaces the whole link set).
 *
 * @param int   $node_id
 * @param array $product_ids
 */
function fn_manufacturer_set_node_products($node_id, array $product_ids)
{
    $node_id = (int) $node_id;
    $product_ids = array_map('intval', $product_ids);
    $product_ids = array_unique(array_filter($product_ids));

    db_query('DELETE FROM ?:nomenclature_links WHERE node_id = ?i', $node_id);

    foreach ($product_ids as $product_id) {
        db_query(
            'INSERT INTO ?:nomenclature_links ?e',
            [
                'product_id' => $product_id,
                'node_id'    => $node_id,
            ]
        );
    }
}

/**
 * Returns products of the node via the standard CS-Cart products query, enriched
 * with additional data (features, prices, availability).
 *
 * @param array $params
 * @param int   $node_id
 * @param string $lang_code
 *
 * @return array
 */
function fn_manufacturer_get_node_products($params = [], $node_id = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = [
        'page'           => 1,
        'items_per_page' => 0,
        'sort_by'        => 'timestamp',
        'sort_order'     => 'desc',
        'status'         => 'A',
        'extend'         => ['description'],
    ];

    $params = array_merge($default_params, $params);
    $node_id = (int) $node_id;

    if ($node_id) {
        $product_ids = fn_manufacturer_get_node_product_ids($node_id);
    } else {
        $product_ids = [];
    }

    if (empty($product_ids)) {
        return [[], $params];
    }

    // Impose the node product list on the standard product query through ?w with IN.
    $params['pid'] = $product_ids;
    $params['force_search'] = true;

    list($products, $params) = fn_get_products($params, $params['items_per_page'], $lang_code);

    // Enrich with features and prices — the standard CS-Cart way.
    $auth = Tygh::$app['session']['auth'];
    foreach ($products as $product_id => $product) {
        $products[$product_id] = fn_get_product_data($product_id, $auth, $lang_code);
    }

    return [$products, $params];
}

/**
 * Returns the list of product features (technical characteristics) for a product
 * product list, grouped by feature.
 *
 * @param array $products Products list (product_id => product_data)
 * @param string $lang_code
 *
 * @return array feature_id => feature_data with 'value' filled
 */
function fn_manufacturer_get_products_features(array $products, $lang_code = CART_LANGUAGE)
{
    if (empty($products)) {
        return [];
    }

    $product_ids = array_keys($products);
    $features = fn_get_product_features([
        'product_id'     => $product_ids,
        'variants'       => true,
        'existent_only'  => false,
        'plain'          => true,
    ], 0, $lang_code);

    $features = !empty($features) ? reset($features) : [];

    $result = [];

    foreach ($features as $feature_id => $feature) {
        if (empty($feature['feature_id'])) {
            continue;
        }

        $feature_id = (int) $feature['feature_id'];
        $result[$feature_id] = [
            'description' => !empty($feature['description']) ? $feature['description'] : $feature['internal_name'],
            'values'      => [],
        ];

        foreach ($products as $product_id => $product) {
            $value = '';
            if (!empty($product['product_features'][$feature_id])) {
                $pf = $product['product_features'][$feature_id];
                if (!empty($pf['variant'])) {
                    $value = is_array($pf['variant']) ? reset($pf['variant']) : $pf['variant'];
                } elseif (isset($pf['value'])) {
                    $value = $pf['value'];
                }
            }
            $result[$feature_id]['values'][$product_id] = $value;
        }
    }

    return $result;
}

/**
 * Migration from ?:pages (2.x architecture) to ?:nomenclature_nodes on install.
 * Idempotent: pages of type M/C/G are converted to nodes only if they were not
 * migrated yet (no nomenclature data present).
 */
function fn_manufacturer_migrate_from_pages()
{
    $has_nodes = db_get_field('SELECT COUNT(*) FROM ?:nomenclature_nodes');

    if (!empty($has_nodes)) {
        return true;
    }

    $pages = db_get_array(
        'SELECT p.page_id, p.parent_id, p.id_path, p.status, p.position, p.timestamp,'
        . ' p.manufacturer_elem_type, pd.page AS name, pd.description, pd.lang_code'
        . ' FROM ?:pages AS p'
        . ' LEFT JOIN ?:page_descriptions AS pd ON pd.page_id = p.page_id'
        . ' WHERE p.page_type = ?s'
        . ' ORDER BY p.parent_id ASC, p.position ASC',
        'M'
    );

    if (empty($pages)) {
        return true;
    }

    $node_id_map = [];

    foreach ($pages as $page) {
        $elem_type = !empty($page['manufacturer_elem_type'])
            ? $page['manufacturer_elem_type']
            : (empty($page['parent_id']) ? 'M' : 'C');

        if (!in_array($elem_type, ['M', 'C', 'G'], true)) {
            $elem_type = empty($page['parent_id']) ? 'M' : 'C';
        }

        $node_id = (int) $page['page_id'];
        $mapped_parent = !empty($node_id_map[$page['parent_id']])
            ? (int) $node_id_map[$page['parent_id']]
            : 0;

        db_query(
            'INSERT INTO ?:nomenclature_nodes ?e',
            [
                'node_id'    => $node_id,
                'parent_id'  => $mapped_parent,
                'id_path'    => $mapped_parent
                    ? (db_get_field('SELECT id_path FROM ?:nomenclature_nodes WHERE node_id = ?i', $mapped_parent) . '/' . $node_id)
                    : (string) $node_id,
                'status'     => $page['status'],
                'node_type'  => $elem_type,
                'position'   => (int) $page['position'],
                'timestamp'  => (int) $page['timestamp'],
            ]
        );

        $node_id_map[$page['page_id']] = $node_id;

        if (!empty($page['lang_code'])) {
            db_query(
                'REPLACE INTO ?:nomenclature_node_descriptions ?e',
                [
                    'node_id'     => $node_id,
                    'lang_code'   => $page['lang_code'],
                    'name'        => $page['name'],
                    'description' => $page['description'],
                ]
            );
        }

        // Migrate image pairs from the old 'manufacturer_page' type to 'nomenclature_main'
        $pairs = db_get_array(
            'SELECT * FROM ?:images_links WHERE object_id = ?i AND object_type = ?s',
            $node_id,
            'manufacturer_page'
        );

        foreach ($pairs as $pair) {
            $pair['object_type'] = IMAGE_TYPE_NOMENCLATURE_MAIN;
            $pair['pair_id'] = 0;
            db_query('INSERT INTO ?:images_links ?e', $pair);
        }
    }

    return true;
}

/**
 * Removes the old 2.x manufacturer pages from ?:pages and the helper column.
 * Called after the data migration, so the addon fully stops using the core
 * pages entity (page_type = 'M').
 */
function fn_manufacturer_remove_old_pages()
{
    $is_exists = db_get_row(
        'SHOW COLUMNS FROM ?:pages LIKE ?s',
        'manufacturer_elem_type'
    );
    if (!empty($is_exists)) {
        db_query('ALTER TABLE ?:pages DROP COLUMN manufacturer_elem_type');
    }

    $page_ids = db_get_fields(
        'SELECT page_id FROM ?:pages WHERE page_type = ?s',
        'M'
    );

    foreach ($page_ids as $page_id) {
        $page_id = (int) $page_id;
        $exists = db_get_field(
            'SELECT page_id FROM ?:pages WHERE page_id = ?i',
            $page_id
        );
        if ($exists) {
            fn_delete_page($page_id, true);
        }
    }
}

/**
 * Install helper: creates tables (defensive, in case queries section was skipped),
 * migrates data from the old pages architecture, and migrates SEO names.
 */
function fn_manufacturer_install()
{
    fn_manufacturer_migrate_from_pages();

    // Migrate SEO names for the old pages.view dispatch to nomenclature.view
    if (Registry::get('addons.seo.status') == 'A') {
        $seo_names = db_get_array(
            'SELECT * FROM ?:seo_names WHERE type = ?s AND dispatch = ?s',
            'p',
            'pages.view'
        );

        foreach ($seo_names as $seo_name) {
            $page_id = (int) $seo_name['object_id'];
            $dispatch_updated = db_get_field(
                'SELECT COUNT(*) FROM ?:seo_names WHERE object_id = ?i AND type = ?s AND dispatch = ?s',
                $page_id,
                'n',
                'nomenclature.view'
            );

            if ($dispatch_updated) {
                continue;
            }

            db_query(
                'UPDATE ?:seo_names SET type = ?s, dispatch = ?s WHERE object_id = ?i AND type = ?s AND dispatch = ?s',
                'n',
                'nomenclature.view',
                $page_id,
                'p',
                'pages.view'
            );
        }
    }

    // Full detach from the core pages entity: drop the old M-pages and helper column.
    fn_manufacturer_remove_old_pages();

    return true;
}

/**
 * Uninstall helper: removes nomenclature tables and data.
 */
function fn_manufacturer_uninstall()
{
    db_query('DROP TABLE IF EXISTS ?:nomenclature_links');
    db_query('DROP TABLE IF EXISTS ?:nomenclature_node_descriptions');
    db_query('DROP TABLE IF EXISTS ?:nomenclature_nodes');

    return true;
}

/**
 * Hook: delete_product_post — clean links when a product is removed.
 *
 * @param int  $product_id
 * @param bool $product_deleted
 */
function fn_manufacturer_delete_product_post($product_id, $product_deleted)
{
    if ($product_deleted) {
        db_query('DELETE FROM ?:nomenclature_links WHERE product_id = ?i', (int) $product_id);
    }
}

/**
 * Hook: get_product_data_post — expose the nodes a product belongs to.
 *
 * @param array  $product_data
 * @param array  $auth
 * @param bool   $preview
 * @param string $lang_code
 */
function fn_manufacturer_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (!empty($product_data['product_id'])) {
        $node_ids = db_get_fields(
            'SELECT node_id FROM ?:nomenclature_links WHERE product_id = ?i',
            (int) $product_data['product_id']
        );

        if (!empty($node_ids)) {
            $product_data['nomenclature_node_ids'] = $node_ids;
        }
    }
}

/**
 * Hook: get_products — allow filtering products by node through the params.
 *
 * @param array  $params
 * @param array  $fields
 * @param array  $sortings
 * @param string $condition
 * @param string $join
 * @param string $sorting
 * @param string $group_by
 * @param string $lang_code
 * @param string $having
 */
function fn_manufacturer_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by, &$lang_code, &$having)
{
    if (!empty($params['nomenclature_node_id'])) {
        $node_id = (int) $params['nomenclature_node_id'];
        $product_ids = fn_manufacturer_get_node_product_ids($node_id);

        if (empty($product_ids)) {
            $condition .= ' AND 1 = 0';
        } else {
            $condition .= ' AND ?:products.product_id IN (' . implode(',', $product_ids) . ')';
        }
    }
}