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

use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array> $schema
 */
$schema['manufacturer'] = [
    'content' => [
        'items' => [
            'type'           => 'enum',
            'object'         => 'nomenclature_nodes',
            'items_function' => 'fn_manufacturer_get_nodes',
            'remove_indent'  => true,
            'hide_label'     => true,
            'fillings' => [
                'manufacturer.recent_posts_scroller' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'position',
                        'sort_order' => 'asc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'get_tree'   => true,
                    ],
                ],
                'manufacturer.recent_posts' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'position',
                        'sort_order' => 'asc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'get_tree'   => true,
                    ]
                ],
                'manufacturer.text_links' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'position',
                        'sort_order' => 'asc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'get_tree'   => true,
                    ],
                    'settings' => [
                        'parent_node_id' => [
                            'type'          => 'input',
                            'default_value' => '0',
                        ],
                        'limit' => [
                            'type'          => 'input',
                            'default_value' => 10
                        ],
                    ],
                ],
            ],
        ],
    ],
    'templates' => 'addons/manufacturer/blocks',
    'wrappers'  => 'blocks/wrappers',
    'cache' => [
        'update_handlers'  => ['nomenclature_nodes', 'nomenclature_node_descriptions', 'nomenclature_links'],
        'request_handlers' => ['%NODE_ID%', '%COMPANY_ID%']
    ],
    'brief_info_function' => 'fn_block_get_manufacturer_info'
];

return $schema;