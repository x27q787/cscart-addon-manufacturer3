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

/**
 * Returns node items for a Block Manager block (source for the manufacturer blocks).
 *
 * @param array $params
 *
 * @return array [items, params]
 */
function fn_manufacturer_get_items_for_block($params)
{
    $lang_code = !empty($params['lang_code']) ? $params['lang_code'] : CART_LANGUAGE;

    $default_params = [
        'status'      => 'A',
        'node_id'     => 0,
        'sort_by'     => 'timestamp',
        'sort_order'  => 'desc',
        'get_image'   => false,
        'limit'       => 0,
        'parent_node_id' => 0,
    ];

    $params = array_merge($default_params, $params);

    // Support both node_id (current page context) and parent_node_id (block setting)
    $root_id = !empty($params['node_id']) ? (int) $params['node_id'] : (int) $params['parent_node_id'];

    $where = '?:nomenclature_nodes.parent_id = ?i';
    $where_params = [(int) $root_id];

    if (!empty($params['status'])) {
        $where .= ' AND ?:nomenclature_nodes.status = ?s';
        $where_params[] = $params['status'];
    }

    $sortings = [
        'name'      => '?:nomenclature_node_descriptions.name',
        'timestamp' => '?:nomenclature_nodes.timestamp',
        'position'  => '?:nomenclature_nodes.position',
    ];

    $sort_by = !empty($sortings[$params['sort_by']]) ? $sortings[$params['sort_by']] : '?:nomenclature_nodes.timestamp';
    $sort_order = strtoupper($params['sort_order']) == 'DESC' ? 'DESC' : 'ASC';

    $fields = [
        '?:nomenclature_nodes.*',
        '?:nomenclature_node_descriptions.name',
        '?:nomenclature_node_descriptions.description',
    ];

    // Limit
    $limit = !empty($params['limit']) ? (int) $params['limit'] : 0;

    // Tree or flat
    if (!empty($params['get_tree'])) {
        $items = fn_manufacturer_get_nodes_tree(['status' => $params['status'], 'lang_code' => $lang_code], (int) $root_id);
    } else {
        $query = 'SELECT ?p FROM ?:nomenclature_nodes'
            . ' LEFT JOIN ?:nomenclature_node_descriptions'
                . ' ON ?:nomenclature_node_descriptions.node_id = ?:nomenclature_nodes.node_id'
                . ' AND ?:nomenclature_node_descriptions.lang_code = ?s'
            . ' WHERE ' . $where
            . ' ORDER BY ?p ?p'
            . ($limit ? ' LIMIT ' . $limit : '');

        $items = db_get_hash_array(
            $query,
            'node_id',
            implode(', ', $fields),
            $lang_code,
            ...$where_params,
            $sort_by,
            $sort_order
        );
    }

    if (!empty($items) && !empty($params['get_image'])) {
        $node_ids = array_keys($items);
        $images = fn_get_image_pairs($node_ids, IMAGE_TYPE_NOMENCLATURE_MAIN, 'M', true, false, $lang_code);

        foreach ($items as $node_id => $node) {
            $items[$node_id]['main_pair'] = !empty($images[$node_id]) ? reset($images[$node_id]) : [];
        }
    }

    return [$items, $params];
}

/**
 * Brief info for the block manager "blocks" list.
 *
 * @param array  $block
 * @param string $lang_code
 *
 * @return array
 */
function fn_block_get_manufacturer_info(array $block, $lang_code = CART_LANGUAGE)
{
    $items = isset($block['content']['items']) ? $block['content']['items'] : [];
    $filling = isset($items['filling']) ? (string) $items['filling'] : '' ;
    $limit = isset($items['limit']) ? $items['limit'] : (isset($block['properties']['limit']) ? $block['properties']['limit'] : 0);
    $filling_text = fn_is_lang_var_exists($filling) ? __($filling, [], $lang_code) : '';
    $content = ($filling_text) ? sprintf('%s, %s', $filling_text, __('n_manufacturers', [$limit], $lang_code)) : __('n_manufacturers', [$limit], $lang_code);

    return [
        'content' => $content,
    ];
}