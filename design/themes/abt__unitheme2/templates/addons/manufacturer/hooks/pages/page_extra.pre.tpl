{if $page.page_type == $smarty.const.PAGE_TYPE_MANUFACTURER}

{if $subpages}

    {capture name="mainbox_title"}{/capture}

    <div class="ty-manufacturer">
        {include file="common/pagination.tpl"}

        {foreach from=$subpages item="subpage"}
            <div class="ty-manufacturer__item">
                <a href="{"pages.view?page_id=`$subpage.page_id`"|fn_url}">
                    <h2 class="ty-manufacturer__title">
                        {$subpage.page}
                    </h2>
                </a>
                {if $subpage.main_pair}
                <a href="{"pages.view?page_id=`$subpage.page_id`"|fn_url}">
                    <div class="ty-manufacturer__img-block">
                        {include file="common/image.tpl" obj_id=$subpage.page_id images=$subpage.main_pair}
                    </div>
                </a>
                {/if}
                <div class="ty-manufacturer__description">
                    <div class="ty-wysiwyg-content">
                        <div>{$subpage.spoiler nofilter}</div>
                    </div>
                    <div class="ty-manufacturer__read-more ty-mt-l">
                        <a class="ty-btn ty-btn__secondary" href="{"pages.view?page_id=`$subpage.page_id`"|fn_url}">{__("manufacturer.read_more")}</a>
                    </div>
                </div>
            </div>
        {/foreach}

        {include file="common/pagination.tpl"}
    </div>

{/if}

{if $page.description}
    {capture name="mainbox_title"}<span class="ty-manufacturer__title" {live_edit name="page:page:{$page.page_id}"}>{$page.page}</span>{/capture}
{/if}

{/if}
