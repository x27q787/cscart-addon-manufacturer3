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
$schema['manufacturer']['is_managed_by'] = ['ROOT'];

$schema['vendor_manufacturer'] = [
    'content' => [
        'items' => [
            'type'           => 'enum',
            'object'         => 'pages',
            'items_function' => 'fn_get_pages',
            'remove_indent'  => true,
            'hide_label'     => true,
            'fillings'       => [
                'manufacturer.recent_posts_scroller' => [
                    'params' => [
                        'simple'     => true,
                        'sort_by'    => 'timestamp',
                        'sort_order' => 'desc',
                        'status'     => ObjectStatuses::ACTIVE,
                        'page_type'  => PAGE_TYPE_MANUFACTURER,
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
                        'page_type'  => PAGE_TYPE_MANUFACTURER,
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
                        'page_type'  => PAGE_TYPE_MANUFACTURER,
                        'request'    => [
                            'company_id' => '%COMPANY_ID%',
                        ],
                    ],
                    'settings' => [
                        'parent_page_id' => [
                            'type'          => 'picker',
                            'default_value' => '0',
                            'picker'        => 'pickers/pages/picker.tpl',
                            'picker_params' => [
                                'multiple'     => false,
                                'use_keys'     => 'N',
                                'default_name' => __('root_level'),
                                'extra_url'    => '&page_type=' . PAGE_TYPE_MANUFACTURER
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
        'update_handlers'  => ['pages', 'page_descriptions'],
        'request_handlers' => ['%PAGE_ID%', '%COMPANY_ID%']
    ],
    'brief_info_function' => 'fn_block_get_manufacturer_info',
    'is_managed_by' => ['ROOT', 'VENDOR']
];

return $schema;
