{** block-description:manufacturer_children **}
{** Дочерние страницы производителя (категории/серии) в виде плиток с картинками **}

{if $items}

<div class="ty-manufacturer">
    {foreach from=$items item="page"}
        <div class="ty-manufacturer__item">
            <a href="{"pages.view?page_id=`$page.page_id`"|fn_url}">
                <div class="ty-manufacturer__img-block">
                    {include file="common/image.tpl" obj_id=$page.page_id images=$page.main_pair}
                </div>
            </a>
            <h3 class="ty-manufacturer__title">
                <a href="{"pages.view?page_id=`$page.page_id`"|fn_url}">{$page.page}</a>
            </h3>
            {if $page.description}
                <div class="ty-manufacturer__description">
                    <div class="ty-wysiwyg-content">
                        <div>{$page.spoiler nofilter}</div>{* spoiler заполняется хуком post_get_pages по <!--CUT--> *}
                    </div>
                </div>
            {/if}
        </div>
    {/foreach}
</div>

{/if}