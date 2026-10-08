{** Кнопка «В корзину» для строки таблицы характеристик **}

{if $product.product_id && $product.status == "A"}
    {if $product.amount > 0 || $settings.General.allow_negative_amount == "Y"}
        <form class="cm-ajax ty-manufacturer__add-to-cart" method="post"
              action="{""|fn_url}" name="add_to_cart_{$product.product_id}">
            <input type="hidden" name="result_ids" value="cart_status*,wish_list*" />
            <input type="hidden" name="product_data[{$product.product_id}][product_id]" value="{$product.product_id}" />
            <div class="ty-manufacturer__add-to-cart-qty">
                <label for="qty_{$product.product_id}">{__("qty")}:</label>
                <input type="text" class="input-micro" size="3" id="qty_{$product.product_id}"
                       name="product_data[{$product.product_id}][amount]" value="1" />
            </div>
            {include file="buttons/add_to_cart.tpl" but_id="button_cart_`$product.product_id`" but_name="dispatch[checkout.add]"}
        </form>
    {else}
        <span class="ty-qty-out-of-stock ty-control-group__item">{__("out_of_stock")}</span>
    {/if}
{/if}