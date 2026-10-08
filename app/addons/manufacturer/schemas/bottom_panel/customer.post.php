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

use Tygh\Tools\Url;

$schema['nomenclature.view'] = [
    'from' => [
        'dispatch' => 'nomenclature.view',
        'node_id'
    ],
    'to_admin' => function (Url $url) {
        $node_id = $url->getQueryParam('node_id');

        if (empty($node_id)) {
            return false;
        }

        return [
            'dispatch' => 'nomenclature.update',
            'node_id' => '%node_id%'
        ];
    }
];

return $schema;