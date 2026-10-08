<ul>
{foreach from=$pages_tree item=page}
    {math equation="x*14" x=$page.level|default:0 assign="shift"}
    {$expanded = $page.page_id|in_array:$runtime.active_page_ids}
    {$comb_id = "page_`$page.page_id`"}

    {if $language_direction == 'rtl'}
        {$direction = 'right'}
    {else}
        {$direction = 'left'}
    {/if}

    <li {if $page.active}class="active"{/if}>
    {strip}
        <div class="link">
            {if $page.subpages}
                <span alt="{__("expand_sublist_of_items")}" title="{__("expand_sublist_of_items")}" id="on_{$comb_id}" class="cm-combination{if $expanded} hidden{/if}" ><span class="icon-caret-right cs-dark-theme-invert"> </span></span>
                <span alt="{__("collapse_sublist_of_items")}" title="{__("collapse_sublist_of_items")}" id="off_{$comb_id}" class="cm-combination{if !$expanded} hidden{/if}" ><span class="icon-caret-down cs-dark-theme-invert"> </span></span>
            {/if}
            {* Wrapper keeps the indentation ladder recursive and per-level;
               it mirrors the standard CS-Cart approach instead of relying on <li> padding. *}
            <div class="table-tree-wrap" style="padding-{$direction}: {$shift}px;">
                <a href="{"pages.update?page_id=`$page.page_id`&come_from=`$come_from`"|fn_url}" {if $page.status == "N"}class="manage-root-item-disabled"{/if} id="page_title_{$page.page_id}" title="{$page.page}">{if $page.manufacturer_elem_type == 'G'}<i class="icon-folder-close muted"> </i>{/if}{$page.page}</a>
            </div>
        </div>
    {/strip}
    </li>
{if $page.subpages}
    <li class="{if !$expanded} hidden{/if}" id="{$comb_id}">
        {include file="views/pages/components/pages_links_tree.tpl" pages_tree=$page.subpages parent_id=$page.page_id}
    </li>
{/if}

{/foreach}
</ul>