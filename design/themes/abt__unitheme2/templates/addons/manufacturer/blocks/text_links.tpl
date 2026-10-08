{** block-description:manufacturer.text_links **}

{assign var="parent_id" value=$block.content.items.parent_node_id}
{if $items}
<div class="ty-manufacturer-text-links">
    <ul>
    {foreach from=$items item="node" name="fe_manufacturer"}
        <li class="ty-manufacturer-text-links__item">
            <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}" class="ty-manufacturer-text-links__a">{$node.name}</a>
        </li>
    {/foreach}
    </ul>
    {if $parent_id}
        <div class="ty-mtb-xs ty-left">
            {include file="buttons/button.tpl" but_href="nomenclature.view?node_id=`$parent_id`" but_text=__("view_all") but_role="text" but_meta="ty-btn__secondary ty-manufacturer-text-links__button"}
        </div>
    {/if}
</div>
{/if}