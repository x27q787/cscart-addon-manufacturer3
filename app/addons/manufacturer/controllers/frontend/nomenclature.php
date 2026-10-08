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

$lang_code = CART_LANGUAGE;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'view') {

    $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;
    $node = fn_manufacturer_get_node_data($node_id, $lang_code);

    if (empty($node) || $node['status'] != NOMENCLATURE_STATUS_ACTIVE) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $params = $_REQUEST;
    list($products, $search, $total) = fn_manufacturer_get_frontend_products($node_id, $params);

    $features = [];
    $features_matrix = [];

    $product_ids = array_keys($products);
    if ($product_ids) {
        $features = fn_manufacturer_get_node_features($node_id, true);
        $features_matrix = fn_manufacturer_get_features_matrix($product_ids, array_keys($features), $lang_code);
    }

    $child_nodes = [];
    list($child_nodes) = fn_manufacturer_get_nodes([
        'parent_id' => $node_id,
        'status'    => NOMENCLATURE_STATUS_ACTIVE,
        'get_tree'  => true,
    ], 0, $lang_code);

    Tygh::$app['view']->assign('node', $node);
    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('features', $features);
    Tygh::$app['view']->assign('features_matrix', $features_matrix);
    Tygh::$app['view']->assign('child_nodes', $child_nodes);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('total', $total);
    Tygh::$app['view']->assign('pagination', fn_paginate($total, $search['page'], $search['items_per_page']));
}

return [CONTROLLER_STATUS_OK];