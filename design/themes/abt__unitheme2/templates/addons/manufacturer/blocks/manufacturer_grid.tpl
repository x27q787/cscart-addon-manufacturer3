{** block-description:manufacturer_children **}
{** Дочерние узлы номенклатуры (категории/группы) в виде плиток с картинками — UniTheme2 **}

{if $items}

<div class="ty-manufacturer">
    {foreach from=$items item="node"}
        <div class="ty-manufacturer__item">
            <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}">
                <div class="ty-manufacturer__img-block">
                    {include file="common/image.tpl" obj_id=$node.node_id images=$node.main_pair}
                </div>
            </a>
            <h3 class="ty-manufacturer__title">
                <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}">{$node.name}</a>
            </h3>
            {if $node.description}
                <div class="ty-manufacturer__description">
                    <div class="ty-wysiwyg-content">
                        {$node.description nofilter}
                    </div>
                </div>
            {/if}
        </div>
    {/foreach}
</div>

{/if}