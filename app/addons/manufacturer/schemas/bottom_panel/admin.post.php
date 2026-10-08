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

$schema['nomenclature.manage'] = [
    'from' => [
        'dispatch'  => 'nomenclature.manage',
    ],
    'to_customer' => [
        'dispatch' => 'nomenclature.view',
        'node_id' => function () {
            $first = db_get_field(
                'SELECT node_id FROM ?:nomenclature_nodes WHERE parent_id = 0 AND status = ?s ORDER BY position ASC LIMIT 1',
                NOMENCLATURE_STATUS_ACTIVE
            );
            return !empty($first) ? (int) $first : false;
        }
    ]
];

return $schema;