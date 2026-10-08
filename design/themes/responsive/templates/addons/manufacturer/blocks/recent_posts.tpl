{** block-description:manufacturer.recent_posts **}

{if $items}

<div class="ty-manufacturer-sidebox">
    <ul class="ty-manufacturer-sidebox__list">
{foreach from=$items item="page"}
        <li class="ty-manufacturer-sidebox__item">
            <a href="{"pages.view?page_id=`$page.page_id`"|fn_url}">{$page.page}</a>
        </li>
{/foreach}
    </ul>
</div>

{/if}