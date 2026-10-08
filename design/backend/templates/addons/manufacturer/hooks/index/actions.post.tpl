{*
    CS-Cart 4.18.x does not call a hook named "pages:adv_buttons" — the $adv_buttons block
    on the pages toolbar is assembled inside views/pages/*.tpl via {capture}. To expose the
    quick "add child" actions without overriding the core templates, the buttons live in a
    dedicated template (addons/manufacturer/hooks/pages/adv_buttons.post.tpl) and are rendered
    here through the built-in "index:actions" hook located inside the navbar__actions-bar.
    The view knows the current page type as $page_type (update) or $search.page_type (manage).
*}
{if $runtime.controller == 'pages' && $runtime.mode|in_array:['manage', 'update']}
    {if $runtime.mode == 'update'}
        {$current_page_type = $page_type|default:''}
    {else}
        {$current_page_type = $search.page_type|default:''}
    {/if}
    {if $current_page_type == $smarty.const.PAGE_TYPE_MANUFACTURER}
        {include file="addons/manufacturer/hooks/pages/adv_buttons.post.tpl"
            current_page_type=$current_page_type
            manufacturer_elem_type=$manufacturer_elem_type|default:''}
    {/if}
{/if}