{** block-description:manufacturer.recent_posts **}

{if $items}

<div class="ty-manufacturer-sidebox">
    <ul class="ty-manufacturer-sidebox__list">
{foreach from=$items item="node"}
        <li class="ty-manufacturer-sidebox__item">
            <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}">{$node.name}</a>
        </li>
{/foreach}
    </ul>
</div>

{/if}
