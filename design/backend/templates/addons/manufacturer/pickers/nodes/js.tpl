{if $node_id}
    {assign var="node" value=$node_id|fn_manufacturer_get_node_name|default:"`$ldelim`node`$rdelim`"}
{else}
    {assign var="node" value=$default_name}
{/if}

{if $multiple}
<tr {if !$clone}id="{$holder}_{$node_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
    {if $position_field}<td data-th="&nbsp;"><input type="text" name="{$input_name}[{$node_id}]" value="{math equation="a*b" a=$position b=10}" size="3" class="input-micro"{if $clone} disabled="disabled"{/if} /></td>{/if}
    
    <td data-th="&nbsp;"><a href="{"nomenclature.update?node_id=`$node_id`"|fn_url}">{$node}</a></td>

    <td data-th="&nbsp;">
        <div class="hidden-tools">
        {if !$hide_delete_button && !$view_only}
            {capture name="tools_list"}
                <li>{btn type="list" text=__("remove") onclick="Tygh.$.cePicker('delete_js_item', '{$holder}', '{$node_id}', 'a'); return false;"}</li>
            {/capture}
            {dropdown content=$smarty.capture.tools_list}
        {/if}
        </div>
    </td>
    {if !$hide_input}
        <input {if $input_id}id="{$input_id}"{/if} type="hidden" name="{$input_name}" value="{$node_id}" />
    {/if}
</tr>
{else}
    <span {if !$clone}id="{$holder}_{$node_id}" {/if}class="cm-js-item no-margin{if $clone} cm-clone hidden{/if}">
    {if !$first_item && $single_line}<span class="cm-comma{if $clone} hidden{/if}">,&nbsp;&nbsp;</span>{/if}
    <input class="cm-picker-value-description {$extra_class}" type="text" value="{$node}" {if $display_input_id}id="{$display_input_id}"{/if} size="10" name="node_name" readonly="readonly" {$extra}>&nbsp;
    </span>
{/if}