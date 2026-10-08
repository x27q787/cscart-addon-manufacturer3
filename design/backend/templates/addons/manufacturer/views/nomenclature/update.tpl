{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="nomenclature_update_form" enctype="multipart/form-data" class="cm-ajax cm-ajax-full-render" data-ca-ajax-full-render-event="nomenclature_update">

    <input type="hidden" name="node_id" value="{$node_id}" />
    <input type="hidden" name="node_data[parent_id]" value="{$node_data.parent_id|default:0}" />

    <div class="tabs cm-j-tabs">
        <ul class="nav nav-tabs">
            <li id="tab_general" class="cm-js active"><a>{__("general")}</a></li>
            <li id="tab_products" class="cm-js"><a>{__("nomenclature.products")}</a></li>
        </ul>
    </div>

    <div class="cm-tabs-content" id="content_tab_general">
        <fieldset>
            <div class="control-group">
                <label class="control-label cm-required" for="elm_node_name">{__("name")}:</label>
                <div class="controls">
                    <input type="text" id="elm_node_name" name="node_data[name]" value="{$node_data.name}" class="input-large" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_node_seo_name">{__("seo_name")}:</label>
                <div class="controls">
                    <input type="text" id="elm_node_seo_name" name="node_data[seo_name]" value="{$node_data.seo_name}" class="input-large" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("node_type")}:</label>
                <div class="controls">
                    <select name="node_data[node_type]" id="elm_node_type" class="input-large">
                        <option value="M" {if $node_data.node_type == 'M'}selected="selected"{/if}>{__("nomenclature.type_manufacturer")}</option>
                        <option value="C" {if $node_data.node_type == 'C'}selected="selected"{/if}>{__("nomenclature.type_category")}</option>
                        <option value="G" {if $node_data.node_type == 'G'}selected="selected"{/if}>{__("nomenclature.type_group")}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("parent_node")}:</label>
                <div class="controls">
                    <select name="node_data[parent_id]" id="elm_parent_id" disabled="disabled">
                        <option value="0" {if $node_data.parent_id == 0}selected="selected"{/if}>{__("root_level")}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("status")}:</label>
                <div class="controls">
                    <select name="node_data[status]" id="elm_node_status">
                        <option value="A" {if $node_data.status == 'A'}selected="selected"{/if}>{__("active")}</option>
                        <option value="D" {if $node_data.status == 'D'}selected="selected"{/if}>{__("disabled")}</option>
                    </select>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="elm_node_description">{__("description")}:</label>
                <div class="controls">
                    <textarea id="elm_node_description" name="node_data[description]" class="input-large" rows="8" cols="55">{$node_data.description}</textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">{__("image")}:</label>
                <div class="controls">
                    {include file="common/attach_images.tpl" image_name="nomenclature_main" image_object_type=$smarty.const.IMAGE_TYPE_NOMENCLATURE_MAIN image_pair=$node_data.main_pair no_detailed=true hide_titles=true}
                </div>
            </div>
        </fieldset>
    </div>

    <div class="cm-tabs-content hidden" id="content_tab_products">
        <fieldset>
            <div class="control-group">
                <label class="control-label">{__("nomenclature.products")}:</label>
                <div class="controls">
                    {include file="pickers/products/picker.tpl" data_id="nomenclature_products" input_name="product_ids[]" item_ids=$product_ids type="links" placement="left" display="checkbox"}
                </div>
            </div>
        </fieldset>
    </div>

    {capture name="buttons"}
        {include file="buttons/save.tpl" but_name="dispatch[nomenclature.update]" but_role="submit-link" but_target_form="nomenclature_update_form"}
        {if $node_id}
            {include file="buttons/button.tpl" but_href="nomenclature.manage" but_text=__("cancel") but_role="text"}
        {/if}
    {/capture}

</form>

{/capture}

{include file="common/mainbox.tpl" title_start=__("nomenclature") title_end=$node_data.name|default:__("nomenclature.add") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}