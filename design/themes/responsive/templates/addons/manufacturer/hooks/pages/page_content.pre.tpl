{if $page.description && $page.page_type == $smarty.const.PAGE_TYPE_MANUFACTURER}
    {if $page.main_pair}
        <div class="ty-manufacturer__img-block">
            {include file="common/image.tpl" obj_id=$page.page_id images=$page.main_pair}
        </div>
    {/if}
{/if}
