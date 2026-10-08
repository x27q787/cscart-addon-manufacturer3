{** Страница технической группы (узла номенклатуры) — динамическая таблица характеристик **}

{$node_id = $node.node_id}

{capture name="mainbox_title"}<span class="ty-manufacturer__title">{$node.name}</span>{/capture}

<div class="ty-manufacturer ty-manufacturer-view">

    {if $node.main_pair}
        <div class="ty-manufacturer__img-block">
            {include file="common/image.tpl" obj_id=$node_id images=$node.main_pair}
        </div>
    {/if}

    {if $node.description}
        <div class="ty-manufacturer__description ty-wysiwyg-content">
            {$node.description nofilter}
        </div>
    {/if}

    {if $child_nodes}
        <div class="ty-manufacturer__children">
            <h3>{__("manufacturer.child_nodes")}</h3>
            <div class="ty-manufacturer">
                {foreach from=$child_nodes item="child"}
                    <div class="ty-manufacturer__item">
                        <a href="{"nomenclature.view?node_id=`$child.node_id`"|fn_url}">
                            <h4 class="ty-manufacturer__title">{$child.name}</h4>
                        </a>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}

    {if $products}
        <div class="ty-manufacturer__products">
            <h3>{__("manufacturer.products")} ({$total})</h3>

            {include file="common/pagination.tpl"}

            <div class="table-responsive-wrapper">
                <table class="ty-table ty-manufacturer__products-table table-responsive">
                    <thead>
                    <tr>
                        <th data-th="{__("sku")}">{__("sku")}</th>
                        <th data-th="{__("product")}">{__("product")}</th>
                        {foreach from=$features item="feature_name" key="feature_id"}
                            <th data-th="{$feature_name}">{$feature_name}</th>
                        {/foreach}
                        <th data-th="{__("price")}">{__("price")}</th>
                        <th data-th="{__("add_to_cart")}">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$products item="product"}
                        <tr>
                            <td data-th="{__("sku")}">{$product.product_code}</td>
                            <td data-th="{__("product")}">
                                <a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
                                    {$product.product}
                                </a>
                                {if $product.main_pair}
                                    {include file="common/image.tpl" obj_id=$product.product_id images=$product.main_pair}
                                {/if}
                            </td>
                            {foreach from=$features item="feature_name" key="feature_id"}
                                <td data-th="{$feature_name}">
                                    {$features_matrix[$product.product_id][$feature_id]|default:""}
                                </td>
                            {/foreach}
                            <td data-th="{__("price")}">
                                {include file="common/price.tpl" value=$product.price}
                            </td>
                            <td data-th="{__("add_to_cart")}">
                                {include file="addons/manufacturer/views/nomenclature/components/add_to_cart.tpl"
                                    product=$product}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>

            {include file="common/pagination.tpl"}

        </div>
    {else}
        <p class="ty-manufacturer__empty">{__("manufacturer.no_products")}</p>
    {/if}

</div>