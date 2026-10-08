{if $node_data.main_pair}
    <div class="ty-nomenclature__img-block">
        {include file="common/image.tpl" obj_id=$node_id images=$node_data.main_pair}
    </div>
{/if}

{capture name="mainbox_title"}{$node_data.name}{/capture}

{capture name="mainbox"}

{if $node_data.description}
    <div class="ty-wysiwyg-content ty-mb-l">
        {$node_data.description nofilter}
    </div>
{/if}

{if $child_nodes}
    <div class="ty-manufacturer">
        {foreach from=$child_nodes item="child"}
            <div class="ty-manufacturer__item">
                <a href="{"nomenclature.view?node_id=`$child.node_id`"|fn_url}">
                    <div class="ty-manufacturer__img-block">
                        {include file="common/image.tpl" obj_id=$child.node_id images=$child.main_pair}
                    </div>
                </a>
                <h3 class="ty-manufacturer__title">
                    <a href="{"nomenclature.view?node_id=`$child.node_id`"|fn_url}">{$child.name}</a>
                </h3>
            </div>
        {/foreach}
    </div>
{/if}

{if $products}

    <div class="table-responsive-wrapper">
        <table class="table table-middle table--relative table-responsive ty-nomenclature__products" width="100%">
            <thead>
                <tr>
                    <th>{__("sku")}</th>
                    <th>{__("product")}</th>
                    {foreach from=$features item="feature"}
                        <th>{$feature.description}</th>
                    {/foreach}
                    <th>{__("price")}</th>
                    <th>{__("in_stock")}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$products item="product" key="product_id"}
                <tr class="cm-row-status-{$product.status|lower}">
                    <td data-th="{__("sku")}">{$product.product_code}</td>
                    <td data-th="{__("product")}">
                        <a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product}</a>
                    </td>
                    {foreach from=$features item="feature"}
                        <td data-th="{$feature.description}">
                            {if !empty($feature.values[$product_id])}
                                {$feature.values[$product_id] nofilter}
                            {else}
                                —
                            {/if}
                        </td>
                    {/foreach}
                    <td data-th="{__("price")}">
                        {include file="common/price.tpl" value=$product.price}
                    </td>
                    <td data-th="{__("in_stock")}">
                        {if $product.amount > 0}
                            <span class="ty-nomenclature__in-stock">{__("in_stock")}</span>
                        {else}
                            <span class="ty-nomenclature__out-of-stock">{__("out_of_stock")}</span>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>

    {include file="common/pagination.tpl"}

{elseif !$child_nodes}
    <p class="no-items">{__("no_data")}</p>
{/if}

{/capture}

{include file="common/mainbox.tpl" title=__("nomenclature") content=$smarty.capture.mainbox}