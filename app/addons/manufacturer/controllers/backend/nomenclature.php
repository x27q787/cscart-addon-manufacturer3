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

$lang_code = DESCR_SL;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $suffix = '.manage';

    if ($mode == 'update') {

        $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;
        $node_data = !empty($_REQUEST['node_data']) ? $_REQUEST['node_data'] : [];
        $product_ids = !empty($_REQUEST['product_ids']) ? (array) $_REQUEST['product_ids'] : [];

        $node_id = fn_manufacturer_update_node($node_id, $node_data, $product_ids);

        $suffix = '.update?node_id=' . $node_id;
        fn_set_notification('N', __('notice'), __('manufacturer.node_saved'));
    }

    if ($mode == 'delete') {

        $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;
        if ($node_id) {
            fn_manufacturer_delete_node($node_id);
            fn_set_notification('N', __('notice'), __('manufacturer.node_deleted'));
        }
    }

    if ($mode == 'delete_range') {

        $node_ids = !empty($_REQUEST['node_ids']) ? (array) $_REQUEST['node_ids'] : [];
        foreach ($node_ids as $node_id) {
            fn_manufacturer_delete_node((int) $node_id);
        }
        if ($node_ids) {
            fn_set_notification('N', __('notice'), __('manufacturer.nodes_deleted'));
        }
    }

    return [CONTROLLER_STATUS_OK, 'nomenclature' . $suffix];
}

if ($mode == 'manage') {

    $params = $_REQUEST;
    list($nodes, $total, $pagination) = fn_manufacturer_get_nodes($params, Registry::get('addons.manufacturer.items_per_page') ?: 20, $lang_code);

    Tygh::$app['view']->assign('nodes', $nodes);
    Tygh::$app['view']->assign('total', $total);
    Tygh::$app['view']->assign('pagination', $pagination);
    Tygh::$app['view']->assign('search', $params);
    Tygh::$app['view']->assign('items_per_page', Registry::get('addons.manufacturer.items_per_page') ?: 20);

} elseif ($mode == 'update') {

    $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;
    $node = $node_id ? fn_manufacturer_get_node_data($node_id, $lang_code) : [];

    if ($node_id && empty($node)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $product_ids = $node_id ? fn_manufacturer_get_node_products($node_id) : [];

    list($all_nodes) = fn_manufacturer_get_nodes(['get_tree' => false], 0, $lang_code);

    Tygh::$app['view']->assign('node', $node);
    Tygh::$app['view']->assign('node_id', $node_id);
    Tygh::$app['view']->assign('product_ids', $product_ids);
    Tygh::$app['view']->assign('all_nodes', $all_nodes);
    Tygh::$app['view']->assign('lang_code', $lang_code);
}

return [CONTROLLER_STATUS_OK];