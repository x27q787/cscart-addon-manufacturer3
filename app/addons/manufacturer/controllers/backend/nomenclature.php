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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $suffix = '';

    if ($mode == 'update') {
        $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;
        $node_data = !empty($_REQUEST['node_data']) ? $_REQUEST['node_data'] : [];

        $node_id = fn_manufacturer_update_node($node_data, $node_id);

        if (!empty($_REQUEST['product_ids'])) {
            fn_manufacturer_set_node_products($node_id, (array) $_REQUEST['product_ids']);
        } else {
            fn_manufacturer_set_node_products($node_id, []);
        }

        if (empty($_REQUEST['node_id'])) {
            fn_set_notification('N', __('notice'), __('text_changes_saved'));
        }

        $suffix = '.update?node_id=' . $node_id;
    }

    if ($mode == 'delete') {
        $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;

        if ($node_id) {
            fn_manufacturer_delete_node($node_id);
            fn_set_notification('N', __('notice'), __('text_changes_saved'));
        }

        $suffix = '.manage';
    }

    if ($mode == 'multi_delete') {
        $node_ids = !empty($_REQUEST['node_ids']) ? (array) $_REQUEST['node_ids'] : [];

        foreach ($node_ids as $node_id) {
            fn_manufacturer_delete_node((int) $node_id);
        }

        fn_set_notification('N', __('notice'), __('text_changes_saved'));
        $suffix = '.manage';
    }

    if ($mode == 'update_status') {
        $node_id = !empty($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        $status = !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'A';

        if ($node_id) {
            db_query(
                'UPDATE ?:nomenclature_nodes SET status = ?s WHERE node_id = ?i',
                $status,
                $node_id
            );
        }

        return [CONTROLLER_STATUS_OK];
    }

    return [CONTROLLER_STATUS_OK, 'nomenclature' . $suffix];
}

if ($mode == 'manage' || $mode == 'picker') {
    $params = $_REQUEST;

    if ($mode == 'picker') {
        $params['skip_view'] = 'Y';
    }

    $params['status'] = !empty($params['status']) ? $params['status'] : '';
    $tree = fn_manufacturer_get_nodes_tree($params, 0);

    Tygh::$app['view']->assign('nodes_tree', $tree);
    Tygh::$app['view']->assign('search', $params);

    if (!empty($_REQUEST['except_id'])) {
        Tygh::$app['view']->assign('except_id', (int) $_REQUEST['except_id']);
    }

    if ($mode == 'picker') {
        if (!empty($_REQUEST['combination_suffix'])) {
            Tygh::$app['view']->assign('combination_suffix', $_REQUEST['combination_suffix']);
        }
        Tygh::$app['view']->display('addons/manufacturer/pickers/nodes/picker_contents.tpl');
        exit;
    }
}

if ($mode == 'update' || $mode == 'add') {
    $node_id = ($mode == 'update' && !empty($_REQUEST['node_id'])) ? (int) $_REQUEST['node_id'] : 0;

    $node_data = [];
    $product_ids = [];

    if ($node_id) {
        $node_data = fn_manufacturer_get_node_data($node_id, DESCR_SL);
        $product_ids = fn_manufacturer_get_node_product_ids($node_id);
    } else {
        $node_data['parent_id'] = !empty($_REQUEST['parent_id']) ? (int) $_REQUEST['parent_id'] : 0;
        $node_data['node_type'] = !empty($_REQUEST['node_type']) ? $_REQUEST['node_type'] : 'M';
    }

    $parents = fn_manufacturer_get_nodes_tree(['status' => ''], 0);

    Tygh::$app['view']->assign('node_data', $node_data);
    Tygh::$app['view']->assign('node_id', $node_id);
    Tygh::$app['view']->assign('product_ids', $product_ids);
    Tygh::$app['view']->assign('parents_list', $parents);
}