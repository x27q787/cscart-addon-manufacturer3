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
 * Registers nomenclature nodes as dynamic objects for Block Manager,
 * so blocks can have node-specific content.
 *
 * @var array $schema
 */

$schema['nomenclature_nodes'] = array(
    'admin_dispatch'    => 'nomenclature.update',
    'customer_dispatch' => 'nomenclature.view',
    'key'               => 'node_id',
    'picker'            => 'addons/manufacturer/pickers/nodes/picker.tpl',
    'picker_params'     => array(
        'type' => 'links',
    ),
);

return $schema;