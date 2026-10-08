{** block-description:manufacturer_children **}
{** Дочерние страницы производителя (категории/серии) в виде плиток с картинками — UniTheme2 **}

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
                        <div>{$node.description nofilter}</div>{* spoiler заполняется хуком post_get_pages по <!--CUT--> *}
                    </div>
                </div>
            {/if}
        </div>
    {/foreach}
</div>

{/if}