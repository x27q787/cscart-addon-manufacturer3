{if $page_type == $smarty.const.PAGE_TYPE_MANUFACTURER}

{$manufacturer_elem_type = $manufacturer_elem_type|default:'C'}

<input type="hidden" name="page_data[manufacturer_elem_type]" value="{$manufacturer_elem_type}" class="cm-no-hide-input" />

<div class="control-group">
    <label class="control-label">{__("manufacturer_elem_type")}:</label>
    <div class="controls">
        <span class="label {if $manufacturer_elem_type == 'M'}label-success{elseif $manufacturer_elem_type == 'G'}label-warning{else}label-info{/if}">
            {if $manufacturer_elem_type == 'M'}{__('manufacturer.type_manufacturer')}
            {elseif $manufacturer_elem_type == 'G'}{__('manufacturer.type_product_group')}
            {else}{__('manufacturer.type_category')}{/if}
        </span>
    </div>
</div>

{include file="common/subheader.tpl" title=__("manufacturer") target="#manufacturer_image"}
<div id="manufacturer_image" class="in collapse">
    <fieldset>
        <div class="control-group">
            <label class="control-label">{__("image")}:</label>
            <div class="controls">
                {include file="common/attach_images.tpl" image_name="manufacturer_image" image_object_type=$smarty.const.IMAGE_TYPE_MANUFACTURER_PAGE image_pair=$page_data.main_pair no_detailed=true hide_titles=true}
            </div>
        </div>
    </fieldset>
</div>

{/if}
