{** Редактирование узла номенклатуры (независимая структура, без страниц) **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="nomenclature_update_form"
      class="form-horizontal cm-ajax cm-disable-empty-files" enctype="multipart/form-data">
<input type="hidden" name="dispatch" value="nomenclature.update" />
<input type="hidden" name="node_id" value="{$node_id}" />

<div class="tabs cm-j-tabs">
    <ul class="nav nav-tabs">
        <li id="tab_general" class="cm-js active"><a>{__("general")}</a></li>
        <li id="tab_products" class="cm-js"><a>{__("manufacturer.products")}</a></li>
    </ul>
</div>

<div class="cm-tabs-content">
    <div id="content_tab_general">

        <div class="control-group">
            <label class="control-label cm-required" for="elm_node_name">{__("manufacturer.node_name")}</label>
            <div class="controls">
                <input type="text" id="elm_node_name" name="node_data[descriptions][{$lang_code}][name]"
                       value="{$node.name}" class="input-large" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_node_parent">{__("manufacturer.parent_node")}</label>
            <div class="controls">
                <select name="node_data[parent_id]" id="elm_node_parent">
                    <option value="0">-- {__("manufacturer.root_level")} --</option>
                    {foreach from=$all_nodes item="n"}
                        <option value="{$n.node_id}" {if $node.parent_id == $n.node_id}selected="selected"{/if}>
                            {$n.name}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_node_seo">{__("manufacturer.seo_name")}</label>
            <div class="controls">
                <input type="text" id="elm_node_seo" name="node_data[descriptions][{$lang_code}][seo_name]"
                       value="{$node.seo_name}" class="input-large" />
                <p class="muted">{__("manufacturer.seo_name_hint")}</p>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_node_status">{__("status")}</label>
            <div class="controls">
                <select name="node_data[status]" id="elm_node_status">
                    <option value="A" {if $node.status == "A"}selected="selected"{/if}>{__("active")}</option>
                    <option value="D" {if $node.status == "D"}selected="selected"{/if}>{__("disabled")}</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_node_position">{__("position")}</label>
            <div class="controls">
                <input type="text" id="elm_node_position" name="node_data[position]"
                       value="{$node.position}" size="5" />
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="elm_node_description">{__("description")}</label>
            <div class="controls">
                <textarea id="elm_node_description" name="node_data[descriptions][{$lang_code}][description]"
                          class="input-large" rows="8">{$node.description}</textarea>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">{__("manufacturer.image")}</label>
            <div class="controls">
                {include file="common/attach_images.tpl"
                    image_type="manufacturer_page"
                    image_name="manufacturer_image"
                    img_obj_id=$node_id
                    images=$node.main_pair
                }
            </div>
        </div>

    </div>

    <div id="content_tab_products" class="hidden">
        <div class="control-group">
            <label class="control-label">{__("manufacturer.linked_products")}</label>
            <div class="controls">
                {include file="pickers/products/picker.tpl"
                    data_id="nomenclature_products"
                    item_ids=$product_ids
                    input_name="product_ids"
                    type="links"
                    no_container=true
                }
            </div>
        </div>
    </div>
</div>

</form>

{/capture}

{capture name="buttons"}
    {include file="buttons/save_cancel.tpl"
        but_name="dispatch[nomenclature.update]"
        cancel_action="nomenclature.manage"}
{/capture}

{include file="common/mainbox.tpl"
    title="{if $node_id}{__("manufacturer.editing_node")}: {$node.name}{else}{__("manufacturer.adding_node")}{/if}"
    content=$smarty.capture.mainbox
    buttons=$smarty.capture.buttons
}