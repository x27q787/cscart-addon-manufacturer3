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

$schema['addons/manufacturer/blocks/recent_posts_scroller.tpl'] = array (
    'fillings' => array('manufacturer.recent_posts_scroller'),
    'params' => array (
        'plain' => true,
        'request' => array (
            'nomenclature_node_id' => '%NODE_ID%',
        ),
    ),
    'settings' => array (
        'limit' => array (
            'type' => 'input',
            'default_value' => 3
        ),
        'not_scroll_automatically' => array (
            'type' => 'checkbox',
            'default_value' => 'Y'
        ),
        'speed' =>  array (
            'type' => 'input',
            'default_value' => 400,
            'tooltip'       => __('tooltip_carousel_speed')
        ),
        'pause_delay' =>  array (
            'type' => 'input',
            'default_value' => 3
        ),
        'item_quantity' =>  array (
            'type' => 'input',
            'default_value' => 3
        ),
        'outside_navigation' => array (
            'type' => 'checkbox',
            'default_value' => 'Y'
        ),
    ),
);

$schema['addons/manufacturer/blocks/recent_posts.tpl'] = array (
    'fillings' => array('manufacturer.recent_posts'),
    'params' => array (
        'plain' => true,
        'request' => array (
            'nomenclature_node_id' => '%NODE_ID%',
        ),
    )
);

$schema['addons/manufacturer/blocks/text_links.tpl'] = array (
    'fillings' => array('manufacturer.text_links'),
    'params' => array (
        'plain' => true
    )
);

$schema['addons/manufacturer/blocks/manufacturer_grid.tpl'] = array (
    'fillings' => array('manufacturer_children'),
    'params' => array (
        'plain' => true,
        'get_image' => true
    )
);

return $schema;