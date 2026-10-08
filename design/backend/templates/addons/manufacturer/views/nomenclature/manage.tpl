{** Список узлов номенклатуры производителя — независимое дерево каталога **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="nomenclature_nodes_form" class="cm-ajax">
<input type="hidden" name="redirect_url" value="{"nomenclature.manage"|fn_url}" />

<div class="table-responsive-wrapper">
    <table class="table table-middle table--relative table-responsive" width="100%">
        <thead data-ca-bulkedit-default-object="true" data-ca-bulkedit-component="defaultObject">
            <tr>
                <th width="1%" class="mobile-hide">
                    <input type="checkbox" class="bulkedit-toggler hide"
                           data-ca-bulkedit-disable="[data-ca-bulkedit-default-object=true]" />
                </th>
                <th>{__("manufacturer.node")}</th>
                <th width="10%" class="mobile-hide">{__("status")}</th>
                <th width="10%" class="mobile-hide">{__("products_count")}</th>
                <th width="10%" class="mobile-hide">{__("position")}</th>
                <th width="10%">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$nodes item="node"}
            <tr class="cm-longtap-target"
                data-ca-longtap-action="setCheckBox"
                data-ca-longtap-target="input.cm-item"
                data-ca-id="{$node.node_id}">
                <td class="mobile-hide">
                    <input type="checkbox" name="node_ids[]" value="{$node.node_id}"
                           class="cm-item cm-item-status-{$node.status|lower} hide" />
                </td>
                <td data-th="{__("manufacturer.node")}">
                    <a class="row-status" href="{"nomenclature.update?node_id=`$node.node_id`"|fn_url}">
                        <span class="status-link">
                            {$node.name nofilter}
                            {if $node.children}
                                <span class="muted">({$node.children|count})</span>
                            {/if}
                        </span>
                    </a>
                </td>
                <td data-th="{__("status")}">
                    {include file="common/select_popup.tpl" id=$node.node_id status=$node.status
                        object_id=$node.node_id object_id_name="node_id"
                        table="nomenclature_nodes" st_return_url="nomenclature.manage"}
                </td>
                <td data-th="{__("products_count")}" class="mobile-hide">
                    {$node.products_count|default:0}
                </td>
                <td data-th="{__("position")}" class="mobile-hide">
                    <input type="text" size="4" name="nodes[{$node.node_id}][position]"
                           value="{$node.position}" class="input-hidden" disabled="disabled" />
                </td>
                <td data-th="{__("actions")}" class="right">
                    {capture name="tools_list"}
                        <li>{btn type="list" text=__("edit") href="nomenclature.update?node_id=`$node.node_id`"}</li>
                        <li>{btn type="list" text=__("delete") class="cm-confirm" href="nomenclature.delete?node_id=`$node.node_id`"}</li>
                    {/capture}
                    {include file="common/table_tools.tpl" tools_list=$smarty.capture.tools_list prefix=$node.node_id}
                </td>
            </tr>
        {foreachelse}
            <tr class="no-items">
                <td colspan="6"><p>{__("no_data")}</p></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

{include file="common/pagination.tpl"}

</form>

{/capture}

{capture name="adv_buttons"}
    {include file="common/tools.tpl"
        tool_href="nomenclature.update"
        tool_override_meta="btn btn-primary nav__actions-btn-primary"
        prefix="top"
        title=__("manufacturer.add_node")
        link_text=__("manufacturer.add_node")
        hide_tools=true}
{/capture}

{include file="common/mainbox.tpl"
    title=__("manufacturer.nodes")
    content=$smarty.capture.mainbox
    adv_buttons=$smarty.capture.adv_buttons
}