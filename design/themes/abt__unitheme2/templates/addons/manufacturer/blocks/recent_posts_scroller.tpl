{** block-description:manufacturer.recent_posts_scroller **}

{if $items}

{assign var="obj_prefix" value="`$block.block_id`000"}

{if $block.properties.outside_navigation == "Y"}
    <div class="owl-theme ty-owl-controls">
        <div class="owl-controls clickable owl-controls-outside" id="owl_outside_nav_{$block.block_id}">
            <div class="owl-buttons">
                <div id="owl_prev_{$obj_prefix}" class="owl-prev">{include_ext file="common/icon.tpl" class="ty-icon-left-open-thin"}</div>
                <div id="owl_next_{$obj_prefix}" class="owl-next">{include_ext file="common/icon.tpl" class="ty-icon-right-open-thin"}</div>
            </div>
        </div>
    </div>
{/if}

<div class="ty-mb-l">
    <div class="ty-manufacturer-recent-posts-scroller">
        <div id="scroll_list_{$block.block_id}" class="owl-carousel ty-scroller-list">

        {foreach from=$items item="node"}
            <div class="ty-manufacturer-recent-posts-scroller__item">

                <div class="ty-manufacturer-recent-posts-scroller__img-block">
                    <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}">
                        {include file="common/image.tpl" obj_id=$node.node_id images=$node.main_pair}
                    </a>
                </div>

                <a href="{"nomenclature.view?node_id=`$node.node_id`"|fn_url}">{$node.name}</a>

            </div>
        {/foreach}

        </div>
    </div>
</div>

{include file="common/scroller_init_with_quantity.tpl" prev_selector="#owl_prev_`$obj_prefix`" next_selector="#owl_next_`$obj_prefix`"}

{/if}
