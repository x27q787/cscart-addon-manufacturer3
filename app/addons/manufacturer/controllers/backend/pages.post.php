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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($mode == 'add') {

        $page_type = Tygh::$app['view']->getTemplateVars('page_type');
        if ($page_type == PAGE_TYPE_MANUFACTURER) {
            // Ensure the menu matcher sees page_type=M so the "Manufacturers" item wins.
            $_REQUEST['page_type'] = PAGE_TYPE_MANUFACTURER;

            // IMPORTANT: do NOT overwrite parent_id from $parent_pages here. $parent_pages
            // is the full flat list used by the parent selector, and reset() on it returns an
            // arbitrary first element — that used to drop the group/category into a wrong branch.
            // The core already sets $page_data['parent_id'] = $_REQUEST['parent_id'] (see pages.php,
            // add mode) BEFORE this post-controller runs, so we simply keep it.
            $page_data = Tygh::$app['view']->getTemplateVars('page_data');
            if (empty($page_data['parent_id']) && !empty($_REQUEST['parent_id'])) {
                $page_data['parent_id'] = $_REQUEST['parent_id'];
                Tygh::$app['view']->assign('page_data', $page_data);
            }
            $parent_id = !empty($_REQUEST['parent_id']) ? (int) $_REQUEST['parent_id'] : 0;

            // Element type for the new M page:
            //  - root (parent_id == 0) is always a Manufacturer (M)
            //  - otherwise take the requested type (C = category, G = product group)
            $m_type = !empty($_REQUEST['m_type']) ? (string) $_REQUEST['m_type'] : '';
            if (empty($parent_id)) {
                $manufacturer_elem_type = 'M';
            } else {
                $manufacturer_elem_type = in_array($m_type, ['M', 'C', 'G'], true) ? $m_type : 'C';
            }
            Tygh::$app['view']->assign('manufacturer_elem_type', $manufacturer_elem_type);

            // Dynamic page title (add) — override the type labels used by update.tpl
            $page_type_data = Tygh::$app['view']->getTemplateVars('page_type_data');
            if (!empty($page_type_data)) {
                $page_type_data['new_name'] = fn_manufacturer_get_type_title('add', $manufacturer_elem_type);
                $page_type_data['add_name'] = fn_manufacturer_get_type_title('add', $manufacturer_elem_type);
                Tygh::$app['view']->assign('page_type_data', $page_type_data);
            }

            // Active menu section must stay on "Manufacturers", not default "Pages"
            Registry::set('navigation.dynamic.active_section', 'website_manufacturer');

            if (Registry::get('addons.discussion.status') == 'A') {
                Tygh::$app['view']->assign('discussion', array(
                    'type' => 'C'
                ));
            }
        }
    } elseif ($mode == 'update') {

        $page_type = Tygh::$app['view']->getTemplateVars('page_type');
        if ($page_type == PAGE_TYPE_MANUFACTURER && !empty($_REQUEST['page_id'])) {
            // Ensure the menu matcher sees page_type=M so the "Manufacturers"
            // item wins over the default "Pages" item in the left menu.
            $_REQUEST['page_type'] = PAGE_TYPE_MANUFACTURER;

            $elem_type = db_get_field(
                'SELECT manufacturer_elem_type FROM ?:pages WHERE page_id = ?i',
                (int) $_REQUEST['page_id']
            );
            $elem_type = in_array($elem_type, ['M', 'C', 'G'], true) ? $elem_type : 'C';
            Tygh::$app['view']->assign('manufacturer_elem_type', $elem_type);

            // Dynamic type labels used by the "tools_list"/boxes on the edit page
            $page_type_data = Tygh::$app['view']->getTemplateVars('page_type_data');
            if (!empty($page_type_data)) {
                $page_type_data['edit_name'] = fn_manufacturer_get_type_title('edit', $elem_type);
                $page_type_data['add_name'] = fn_manufacturer_get_type_title('add', $elem_type);
                Tygh::$app['view']->assign('page_type_data', $page_type_data);
            }

            // Keep the left-hand menu focused on "Manufacturers"
            Registry::set('navigation.dynamic.active_section', 'website_manufacturer');
        }
    } elseif ($mode == 'manage') {
        Registry::set('navigation.dynamic.active_section', 'website_manufacturer');
        Tygh::$app['view']->assign(
            'is_managing_manufacturer',
            (isset($_REQUEST['page_type']) && $_REQUEST['page_type'] == PAGE_TYPE_MANUFACTURER)
        );
    }

    return;
}

//
// Save: make sure page_data[manufacturer_elem_type] reaches the core writer.
// Hidden input in the form already provides it; here we just guarantee a sane
// default for the M mode (root => M, otherwise C) in case it is missing.
//
if ($mode == 'update' || $mode == 'add') {

    $is_manufacturer = (
        !empty($_REQUEST['page_type'])
        && $_REQUEST['page_type'] == PAGE_TYPE_MANUFACTURER
    );

    if ($is_manufacturer && !empty($_REQUEST['page_id'])) {
        fn_attach_image_pairs('manufacturer_image', IMAGE_TYPE_MANUFACTURER_PAGE, (int) $_REQUEST['page_id'], DESCR_SL);
    }

    if ($is_manufacturer && !empty($_REQUEST['page_data'])
        && empty($_REQUEST['page_data']['manufacturer_elem_type'])
    ) {
        $parent_id = !empty($_REQUEST['page_data']['parent_id']) ? (int) $_REQUEST['page_data']['parent_id'] : 0;
        $_REQUEST['page_data']['manufacturer_elem_type'] = empty($parent_id) ? 'M' : 'C';
    }
}

if ($mode == 'delete') {

    $page_id = !empty($_REQUEST['page_id']) ? (int) $_REQUEST['page_id'] : 0;
    if ($page_id) {
        fn_delete_image_pairs($page_id, IMAGE_TYPE_MANUFACTURER_PAGE);
    }
}