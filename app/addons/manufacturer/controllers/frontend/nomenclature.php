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

if ($mode == 'view') {

    $node_id = !empty($_REQUEST['node_id']) ? (int) $_REQUEST['node_id'] : 0;

    if (empty($node_id)) {
        $node_id = fn_manufacturer_get_first_node_id();
    }

    $node_data = fn_manufacturer_get_node_data($node_id, CART_LANGUAGE);

    if (empty($node_data) || $node_data['status'] != 'A') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    // Tree of child nodes (categories/groups) for the sidebar
    $child_nodes = fn_manufacturer_get_nodes_tree(['status' => 'A', 'lang_code' => CART_LANGUAGE], $node_id);

    // Products of the node (only product groups carry product lists)
    $params = [
        'page'           => !empty($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1,
        'items_per_page' => Registry::get('settings.Appearance.elements_per_page'),
        'sort_by'        => !empty($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'timestamp',
        'sort_order'     => !empty($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'desc',
    ];

    list($products, $search) = fn_manufacturer_get_node_products($params, $node_id, CART_LANGUAGE);

    // Technical characteristics (features) for the product table
    $features = fn_manufacturer_get_products_features($products, CART_LANGUAGE);

    Tygh::$app['view']->assign('node_data', $node_data);
    Tygh::$app['view']->assign('node_id', $node_id);
    Tygh::$app['view']->assign('child_nodes', $child_nodes);
    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('features', $features);

    Tygh::$app['view']->assign('show_qty', false);
    Tygh::$app['view']->assign('show_features', true);
    Tygh::$app['view']->assign('show_price', true);
    Tygh::$app['view']->assign('show_sku', true);
    Tygh::$app['view']->assign('show_add_to_cart', false);
    Tygh::$app['view']->assign('show_amount', true);
    Tygh::$app['view']->assign('show_list_buttons', false);
    Tygh::$app['view']->assign('show_discount_label', true);
    Tygh::$app['view']->assign('show_old_price', true);
    Tygh::$app['view']->assign('show_clean_price', true);

    // Page meta from the SEO object
    if (Registry::get('addons.seo.status') == 'A') {
        $seo_name = fn_get_seo_name($node_id, 'n', CART_LANGUAGE);
        if (!empty($seo_name)) {
            Tygh::$app['view']->assign('seo_name', $seo_name);
        }
    }
}