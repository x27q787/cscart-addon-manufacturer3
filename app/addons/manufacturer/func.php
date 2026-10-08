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
use Tygh\Tools\Url;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * Installs the addon: ensures the nomenclature_links table is indexed by product_id.
 *
 * @return bool
 */
function fn_manufacturer_install()
{
    $is_index_exists = db_get_row("SHOW INDEX FROM ?:nomenclature_links WHERE Key_name = 'product_id'");
    if (empty($is_index_exists)) {
        db_query("ALTER TABLE ?:nomenclature_links ADD INDEX product_id (`product_id`)");
    }

    return true;
}

/**
 * Uninstalls the addon. Nomenclature tables are dropped by addon.xml queries.
 *
 * @return bool
 */
function fn_manufacturer_uninstall()
{
    return true;
}

/**
 * Returns a list of nomenclature nodes (tree).
 *
 * @param array $params  Search params
 * @param int   $items_per_page  Page size (0 = all)
 * @param string $lang_code
 *
 * @return array [nodes (flat list, may contain 'children'), total_count, pagination]
 */
function fn_manufacturer_get_nodes($params = [], $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $params = array_merge([
        'parent_id' => 0,
        'node_id'   => 0,
        'status'    => '',
        'get_tree'  => false,
        'page'      => 1,
        'sort_by'   => 'position',
        'sort_order'=> 'asc',
    ], $params);

    $condition = '1=1';
    $join = db_quote(
        ' LEFT JOIN ?:nomenclature_node_descriptions as ?:nomenclature_node_descriptions'
        . ' ON ?:nomenclature_node_descriptions.node_id = ?:nomenclature_nodes.node_id'
        . ' AND ?:nomenclature_node_descriptions.lang_code = ?s',
        $lang_code
    );

    if (!empty($params['node_id'])) {
        $condition .= db_quote(' AND ?:nomenclature_nodes.node_id = ?i', $params['node_id']);
    }
    if (!empty($params['parent_id'])) {
        $condition .= db_quote(' AND ?:nomenclature_nodes.parent_id = ?i', $params['parent_id']);
    }
    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:nomenclature_nodes.status = ?s', $params['status']);
    }

    $sortings = [
        'position' => '?:nomenclature_nodes.position',
        'name'     => '?:nomenclature_node_descriptions.name',
        'timestamp'=> '?:nomenclature_nodes.timestamp',
    ];
    $sort_by = isset($sortings[$params['sort_by']]) ? $sortings[$params['sort_by']] : $sortings['position'];
    $sort_order = strtoupper($params['sort_order']) === 'DESC' ? 'DESC' : 'ASC';

    $limit = '';
    $total = 0;
    if ($items_per_page > 0) {
        $total = db_get_field('SELECT COUNT(*) FROM ?:nomenclature_nodes WHERE ?p', $condition);
        $limit = db_paginate($params['page'], $items_per_page);
    }

    $nodes = db_get_hash_array(
        'SELECT ?:nomenclature_nodes.*, ?:nomenclature_node_descriptions.name,'
        . ' ?:nomenclature_node_descriptions.description, ?:nomenclature_node_descriptions.seo_name'
        . ' FROM ?:nomenclature_nodes ?p WHERE ?p ORDER BY ?p ?p',
        'node_id', $join, $condition, "$sort_by $sort_order", $limit
    );

    if (!empty($nodes)) {
        $node_ids = array_keys($nodes);

        $counts = db_get_hash_single_array(
            'SELECT node_id, COUNT(*) as cnt FROM ?:nomenclature_links'
            . ' WHERE node_id IN (?n) GROUP BY node_id',
            ['node_id', 'cnt'], $node_ids
        );
        foreach ($nodes as $node_id => $node) {
            $nodes[$node_id]['products_count'] = !empty($counts[$node_id]) ? (int) $counts[$node_id] : 0;
        }

        if (!empty($params['get_image'])) {
            $images = fn_get_image_pairs($node_ids, IMAGE_TYPE_MANUFACTURER_PAGE, 'M', true, false, $lang_code);
            foreach ($node_ids as $node_id) {
                $nodes[$node_id]['main_pair'] = !empty($images[$node_id]) ? reset($images[$node_id]) : [];
            }
        }
    }

    if (!empty($params['get_tree'])) {
        $nodes = fn_manufacturer_build_tree($nodes, (int) $params['parent_id']);
    }

    return [$nodes, $total, fn_paginate($total, $params['page'], $items_per_page)];
}

/**
 * Builds a nested tree from a flat node list.
 *
 * @param array $nodes  Flat list keyed by node_id
 * @param int   $root_id
 *
 * @return array
 */
function fn_manufacturer_build_tree($nodes, $root_id = 0)
{
    $tree = [];
    foreach ($nodes as $node_id => $node) {
        $node['children'] = [];
        $nodes[$node_id] = $node;
    }
    foreach ($nodes as $node_id => $node) {
        if ((int) $node['parent_id'] === (int) $root_id) {
            $tree[$node_id] = $node;
        } else {
            if (isset($nodes[$node['parent_id']])) {
                $nodes[$node['parent_id']]['children'][$node_id] = $node;
            } else {
                $tree[$node_id] = $node;
            }
        }
    }
    return $tree;
}

/**
 * Returns a single nomenclature node with descriptions.
 *
 * @param int    $node_id
 * @param string $lang_code
 *
 * @return array|false
 */
function fn_manufacturer_get_node_data($node_id, $lang_code = CART_LANGUAGE)
{
    $node = db_get_row(
        'SELECT ?:nomenclature_nodes.*, ?:nomenclature_node_descriptions.name,'
        . ' ?:nomenclature_node_descriptions.description, ?:nomenclature_node_descriptions.seo_name'
        . ' FROM ?:nomenclature_nodes'
        . ' LEFT JOIN ?:nomenclature_node_descriptions'
        . ' ON ?:nomenclature_node_descriptions.node_id = ?:nomenclature_nodes.node_id'
        . ' AND ?:nomenclature_node_descriptions.lang_code = ?s'
        . ' WHERE ?:nomenclature_nodes.node_id = ?i',
        $lang_code, $node_id
    );

    if (empty($node)) {
        return false;
    }

    $node['main_pair'] = fn_get_image_pairs($node_id, IMAGE_TYPE_MANUFACTURER_PAGE, 'M', true, false, $lang_code);

    return $node;
}

/**
 * Returns the list of product ids linked to a node (or to a node subtree).
 *
 * @param int   $node_id
 * @param bool  $recursive  include products of child nodes
 *
 * @return int[]
 */
function fn_manufacturer_get_node_product_ids($node_id, $recursive = false)
{
    $node_ids = [$node_id];
    if ($recursive) {
        $node_ids = array_merge($node_ids, fn_manufacturer_get_child_node_ids($node_id));
    }

    return db_get_fields(
        'SELECT product_id FROM ?:nomenclature_links WHERE node_id IN (?n)',
        $node_ids
    );
}

/**
 * Returns all descendant node ids of a node.
 *
 * @param int $node_id
 *
 * @return int[]
 */
function fn_manufacturer_get_child_node_ids($node_id)
{
    $node_ids = db_get_fields(
        'SELECT node_id FROM ?:nomenclature_nodes WHERE id_path LIKE ?l',
        '%/' . (int) $node_id . '/%'
    );
    $direct = db_get_fields(
        'SELECT node_id FROM ?:nomenclature_nodes WHERE parent_id = ?i',
        $node_id
    );

    return array_values(array_unique(array_merge($node_ids, $direct)));
}

/**
 * Creates or updates a nomenclature node.
 *
 * @param int   $node_id     0 for create
 * @param array $node_data   Node data incl. 'descriptions' (lang_code => fields)
 * @param array $product_ids Product ids to link (many-to-many)
 *
 * @return int node_id
 */
function fn_manufacturer_update_node($node_id, $node_data, $product_ids = [])
{
    $node_data = array_merge([
        'parent_id' => 0,
        'status'    => NOMENCLATURE_STATUS_ACTIVE,
        'position'  => 0,
    ], $node_data);

    $node_id = (int) $node_id;
    if ($node_id) {
        db_query('UPDATE ?:nomenclature_nodes SET ?u WHERE node_id = ?i', $node_data, $node_id);
    } else {
        $node_data['timestamp'] = isset($node_data['timestamp']) ? $node_data['timestamp'] : TIME;
        $node_id = db_query('INSERT INTO ?:nomenclature_nodes ?e', $node_data);
        db_query('UPDATE ?:nomenclature_nodes SET id_path = ?s WHERE node_id = ?i', $node_id, $node_id);
    }

    if (isset($node_data['parent_id'])) {
        fn_manufacturer_rebuild_id_path($node_id);
    }

    if (isset($node_data['descriptions'])) {
        foreach ($node_data['descriptions'] as $lang_code => $desc) {
            $desc = array_merge([
                'name'        => '',
                'description' => '',
                'seo_name'    => '',
            ], $desc);
            db_query(
                'REPLACE INTO ?:nomenclature_node_descriptions ?e',
                [
                    'node_id'     => $node_id,
                    'lang_code'   => $lang_code,
                    'name'        => $desc['name'],
                    'description' => $desc['description'],
                    'seo_name'    => $desc['seo_name'],
                ]
            );
        }
    }

    if (!empty($product_ids)) {
        fn_manufacturer_replace_node_products($node_id, $product_ids);
    }

    fn_attach_image_pairs('manufacturer_image', IMAGE_TYPE_MANUFACTURER_PAGE, $node_id, DESCR_SL);

    return $node_id;
}

/**
 * Rebuilds the id_path of a node and all its descendants.
 *
 * @param int $node_id
 */
function fn_manufacturer_rebuild_id_path($node_id)
{
    $parents = [];
    $current = $node_id;
    while ($current) {
        $parent = db_get_row('SELECT parent_id FROM ?:nomenclature_nodes WHERE node_id = ?i', $current);
        if (empty($parent)) {
            break;
        }
        if (in_array($current, $parents, true)) {
            break; // guard against loops
        }
        array_unshift($parents, $current);
        $current = (int) $parent['parent_id'];
    }

    $id_path = implode('/', $parents);
    db_query('UPDATE ?:nomenclature_nodes SET id_path = ?s WHERE node_id = ?i', $id_path, $node_id);

    $children = db_get_fields('SELECT node_id FROM ?:nomenclature_nodes WHERE parent_id = ?i', $node_id);
    foreach ($children as $child_id) {
        fn_manufacturer_rebuild_id_path($child_id);
    }
}

/**
 * Replaces the whole product set linked to a node.
 *
 * @param int   $node_id
 * @param int[] $product_ids
 */
function fn_manufacturer_replace_node_products($node_id, $product_ids)
{
    $product_ids = array_map('intval', (array) $product_ids);
    $product_ids = array_values(array_unique(array_filter($product_ids)));

    db_query('DELETE FROM ?:nomenclature_links WHERE node_id = ?i', $node_id);

    $rows = [];
    foreach ($product_ids as $product_id) {
        $rows[] = [
            'node_id'    => $node_id,
            'product_id' => $product_id,
        ];
    }
    if ($rows) {
        db_query('INSERT INTO ?:nomenclature_links ?m', $rows);
    }
}

/**
 * Deletes a nomenclature node (with its children, links and image pairs).
 *
 * @param int $node_id
 */
function fn_manufacturer_delete_node($node_id)
{
    $node_ids = array_merge([$node_id], fn_manufacturer_get_child_node_ids($node_id));

    foreach ($node_ids as $id) {
        db_query('DELETE FROM ?:nomenclature_nodes WHERE node_id = ?i', $id);
        db_query('DELETE FROM ?:nomenclature_node_descriptions WHERE node_id = ?i', $id);
        db_query('DELETE FROM ?:nomenclature_links WHERE node_id = ?i', $id);
        fn_delete_image_pairs($id, IMAGE_TYPE_MANUFACTURER_PAGE);
    }
}

/**
 * Returns a product picker - friendly list of product ids by node.
 *
 * @param int $node_id
 * @param string $lang_code
 *
 * @return int[]
 */
function fn_manufacturer_get_node_products($node_id)
{
    return db_get_fields(
        'SELECT product_id FROM ?:nomenclature_links WHERE node_id = ?i ORDER BY link_id',
        $node_id
    );
}

/**
 * Returns the number of products linked to a node (incl. children optionally).
 *
 * @param int  $node_id
 * @param bool $recursive
 *
 * @return int
 */
function fn_manufacturer_get_node_products_count($node_id, $recursive = false)
{
    $product_ids = fn_manufacturer_get_node_product_ids($node_id, $recursive);
    return count($product_ids);
}

/**
 * Frontend: fetches products linked to a node for the dynamic features table.
 *
 * @param int $node_id
 * @param array $params
 *
 * @return array ['products', 'params', 'total']
 */
function fn_manufacturer_get_frontend_products($node_id, $params = [])
{
    $params = array_merge([
        'items_per_page' => Registry::get('addons.manufacturer.frontend_items_per_page') ?: 20,
        'page'           => 1,
        'sort_by'        => 'product',
        'sort_order'     => 'asc',
        'get_features'   => true,
    ], $params);

    $product_ids = fn_manufacturer_get_node_product_ids($node_id, true);

    if (empty($product_ids)) {
        return [[], $params, 0];
    }

    $p_ids = array_slice($product_ids, ($params['page'] - 1) * $params['items_per_page'], $params['items_per_page']);
    $p_ids = array_values($p_ids);

    $products = fn_get_products([
        'pid'            => $p_ids,
        'status'         => 'A',
        'extend'         => ['description', 'E'],
        'sort_by'        => $params['sort_by'],
        'sort_order'     => $params['sort_order'],
        'items_per_page' => count($p_ids),
    ]);

    return [$products, $params, count($product_ids)];
}

/**
 * Hooks the get_products_pre to filter by nomenclature node when requested.
 */
function fn_manufacturer_get_products_pre(&$params, $items_per_page, $lang_code)
{
    if (!empty($params['nomenclature_node_id'])) {
        $product_ids = fn_manufacturer_get_node_product_ids((int) $params['nomenclature_node_id'], true);
        if ($product_ids) {
            $params['pid'] = $product_ids;
        } else {
            $params['pid'] = [0];
        }
    }
}

/**
 * Extends get_products result with linked nomenclature node info.
 */
function fn_manufacturer_get_products_post(&$products, $params, $lang_code)
{
    if (empty($products)) {
        return;
    }
    $product_ids = array_keys($products);
    $links = db_get_hash_single_array(
        'SELECT product_id, GROUP_CONCAT(node_id) as node_ids'
        . ' FROM ?:nomenclature_links WHERE product_id IN (?n) GROUP BY product_id',
        ['product_id', 'node_ids'], $product_ids
    );
    foreach ($products as $product_id => $product) {
        $products[$product_id]['nomenclature_node_ids'] = !empty($links[$product_id])
            ? explode(',', $links[$product_id])
            : [];
    }
}

/**
 * Adds linked nomenclature data to product data.
 */
function fn_manufacturer_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (empty($product_data['product_id'])) {
        return;
    }
    $product_data['nomenclature_node_ids'] = db_get_fields(
        'SELECT node_id FROM ?:nomenclature_links WHERE product_id = ?i',
        $product_data['product_id']
    );
}

/**
 * Deletes nomenclature links when a product is removed.
 */
function fn_manufacturer_delete_product_post($product_id, $status)
{
    db_query('DELETE FROM ?:nomenclature_links WHERE product_id = ?i', $product_id);
}

/**
 * Copies nomenclature links on product clone.
 */
function fn_manufacturer_clone_product($product_id, $new_product_id)
{
    $node_ids = db_get_fields('SELECT node_id FROM ?:nomenclature_links WHERE product_id = ?i', $product_id);
    if ($node_ids) {
        $rows = [];
        foreach ($node_ids as $node_id) {
            $rows[] = [
                'node_id'    => $node_id,
                'product_id' => $new_product_id,
            ];
        }
        db_query('INSERT INTO ?:nomenclature_links ?m', $rows);
    }
}

/**
 * Returns the seo name (slug) of a node if set, otherwise falls back to node_id.
 *
 * @param int    $node_id
 * @param string $lang_code
 *
 * @return string
 */
function fn_manufacturer_get_node_seo_name($node_id, $lang_code = CART_LANGUAGE)
{
    $seo_name = db_get_field(
        'SELECT seo_name FROM ?:nomenclature_node_descriptions WHERE node_id = ?i AND lang_code = ?s',
        $node_id, $lang_code
    );
    return !empty($seo_name) ? $seo_name : (string) $node_id;
}

/**
 * Returns the list of feature (characteristic) names found among the products
 * linked to a node. Used to build the dynamic header of the features table.
 *
 * @param int   $node_id
 * @param bool  $recursive
 *
 * @return array [feature_id => feature_name]
 */
function fn_manufacturer_get_node_features($node_id, $recursive = true)
{
    $product_ids = fn_manufacturer_get_node_product_ids($node_id, $recursive);
    if (empty($product_ids)) {
        return [];
    }

    $features = db_get_hash_single_array(
        'SELECT ?:product_features.feature_id, ?:product_features_descriptions.description'
        . ' FROM ?:product_features'
        . ' INNER JOIN ?:product_features_descriptions'
        . '   ON ?:product_features_descriptions.feature_id = ?:product_features.feature_id'
        . '  AND ?:product_features_descriptions.lang_code = ?s'
        . ' INNER JOIN ?:product_features_values'
        . '   ON ?:product_features_values.feature_id = ?:product_features.feature_id'
        . ' WHERE ?:product_features.status = ?s'
        . '   AND ?:product_features_values.product_id IN (?n)'
        . '   AND ?:product_features_values.lang_code = ?s'
        . ' GROUP BY ?:product_features.feature_id'
        . ' ORDER BY ?:product_features.position ASC',
        ['feature_id', 'description'],
        CART_LANGUAGE, NOMENCLATURE_STATUS_ACTIVE, $product_ids, CART_LANGUAGE
    );

    return $features;
}

/**
 * Returns feature values indexed by product_id for a set of features.
 *
 * @param int[] $product_ids
 * @param int[] $feature_ids
 * @param string $lang_code
 *
 * @return array [product_id => [feature_id => value (string)]]
 */
function fn_manufacturer_get_features_matrix($product_ids, $feature_ids, $lang_code = CART_LANGUAGE)
{
    if (empty($product_ids) || empty($feature_ids)) {
        return [];
    }

    $matrix = [];
    foreach ($product_ids as $product_id) {
        $matrix[$product_id] = array_fill_keys($feature_ids, '');
    }

    $variants = db_get_array(
        'SELECT ?:product_features_values.product_id, ?:product_features_values.feature_id,'
        . ' ?:product_features_values.value, ?:product_features_values.value_int,'
        . ' ?:product_features_values.variant_id, ?:product_feature_variants_descriptions.variant'
        . ' FROM ?:product_features_values'
        . ' LEFT JOIN ?:product_feature_variants_descriptions'
        . '   ON ?:product_feature_variants_descriptions.variant_id = ?:product_features_values.variant_id'
        . '  AND ?:product_feature_variants_descriptions.lang_code = ?s'
        . ' WHERE ?:product_features_values.product_id IN (?n)'
        . '   AND ?:product_features_values.feature_id IN (?n)'
        . '   AND ?:product_features_values.lang_code = ?s',
        $lang_code, $product_ids, $feature_ids, $lang_code
    );

    foreach ($variants as $v) {
        $pid = (int) $v['product_id'];
        $fid = (int) $v['feature_id'];
        if (!empty($v['variant'])) {
            $matrix[$pid][$fid] = $v['variant'];
        } elseif ($v['value'] !== '' && $v['value'] !== null) {
            $matrix[$pid][$fid] = $v['value'];
        } elseif ($v['value_int'] !== '' && $v['value_int'] !== null) {
            $matrix[$pid][$fid] = $v['value_int'];
        }
    }

    return $matrix;
}