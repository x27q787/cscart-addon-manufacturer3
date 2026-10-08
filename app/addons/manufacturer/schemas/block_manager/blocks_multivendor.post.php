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
use Tygh\Enum\YesNo;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array> $schema
 */
$schema['manufacturer']['is_managed_by'] = ['ROOT'];

$schema['vendor_manufacturer'] = [
    'content' => [
        'items' => [
            'type'           => 'enum',
            'object'         => 'nomenclature_nodes',
            'items_function' => 'fn_manufacturer_get_items_for_block',
            'remove_indent'  => true,
            'hide_label'     => true,
            'fillings'       => [
                'manufacturer.recent_posts_scroller' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'get_image'  => true,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                ],
                'manufacturer.recent_posts' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                ],
                'manufacturer.text_links' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                    'settings' => [
                        'parent_node_id' => [
                            'type'          => 'picker',
                            'default_value' => '0',
                            'picker'        => 'addons/manufacturer/pickers/nodes/picker.tpl',
                            'picker_params' => [
                                'multiple'     => false,
                                'use_keys'     => YesNo::NO,
                                'default_name' => __('root_level'),
                            ],
                        ],
                        'limit' => [
                            'type' => 'input',
                            'default_value' => 10
                        ],
                    ],
                ],
            ],
        ],
    ],
    'templates' => 'addons/manufacturer/blocks',
    'wrappers'  => 'blocks/wrappers',
    'cache'     => [
        'update_handlers'  => ['nomenclature_nodes', 'nomenclature_node_descriptions', 'nomenclature_links'],
        'request_handlers' => ['%NODE_ID%', '%COMPANY_ID%']
    ],
    'brief_info_function' => 'fn_block_get_manufacturer_info',
    'is_managed_by' => ['ROOT', 'VENDOR']
];

return $schema;