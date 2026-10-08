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
 * Registers the nomenclature nodes ('n') as a SEO object type.
 *
 * @var array $schema
 */

$schema['n'] = array(
    'tree' => true,
    'path_function' => function ($object_id) {
        $path = db_get_field('SELECT id_path FROM ?:nomenclature_nodes WHERE node_id = ?i', $object_id);
        $apath = explode('/', (string) $path);
        array_pop($apath);

        return implode('/', $apath);
    },
    'parent_type' => 'n',
    'name' => 'nomenclature',
    'picker' => 'addons/manufacturer/pickers/nodes/picker.tpl',
    'picker_params' => array(
        'multiple' => false,
        'use_keys' => 'N',
    ),
    'table' => '?:nomenclature_node_descriptions',
    'description' => 'nomenclature',
    'dispatch' => 'nomenclature.view',
    'item' => 'node_id',
    'condition' => '',
    'not_shared' => true,
    'tree_options' => array('nomenclature', 'nomenclature_nohtml'),
    'html_options' => array('nomenclature_file', 'nomenclature'),
    'pager' => true,
    'option' => 'seo_nomenclature_type',
    'exist_function' => function ($node_id) {
        return (bool) db_get_field(
            'SELECT node_id FROM ?:nomenclature_nodes WHERE node_id = ?i',
            (int) $node_id
        );
    },
);

return $schema;