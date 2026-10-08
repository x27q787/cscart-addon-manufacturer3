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

$schema['central']['website']['items']['manufacturer'] = [
    'attrs' => [
        'class' => 'is-addon'
    ],
    'href' => 'nomenclature.manage',
    'alt' => 'nomenclature.manage'
        . ',nomenclature.update?node_id=%NODE_ID%'
        . ',nomenclature.add?parent_id=%NODE_ID%'
        . ',nomenclature.add',
    'position' => 150
];

return $schema;