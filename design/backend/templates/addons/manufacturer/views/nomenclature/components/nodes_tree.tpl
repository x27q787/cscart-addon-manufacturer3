{if $nodes}
    {if !$checkbox_name}
        {$checkbox_name = "node_ids[]"}
    {/if}
    {foreach from=$nodes item="node"}
        {math equation="x*14" x=$node.level|default:0 assign="shift"}
        {if $language_direction == 'rtl'}
            {$direction = 'right'}
        {else}
            {$direction = 'left'}
        {/if}

        <tr class="cm-row-status-{$node.status|lower} cm-longtap-target longtap-selection multiple-table-row{if $node.has_children} cm-combination-container{/if}"
            data-ca-longtap-action="setCheckBox"
            data-ca-longtap-target="input.cm-item"
            data-ca-id="{$node.node_id}"
        >
            <td class="left mobile-hide" width="6%">
                <input type="checkbox" name="{$checkbox_name}" id="checkbox_{$node.node_id}" value="{$node.node_id}" class="cm-item cm-item-status-{$node.status|lower} hide" />
            </td>
            <td class="row-status" width="70%" data-th="{__("name")}">
                <div class="text-over" style="padding-{$direction}: {$shift}px;">
                    {if $node.has_children}
                        <span alt="{__("expand_sublist_of_items")}" title="{__("expand_sublist_of_items")}" id="on_node_{$node.node_id}" class="cm-combination"><span class="icon-caret-right"></span></span>
                        <span alt="{__("collapse_sublist_of_items")}" title="{__("collapse_sublist_of_items")}" id="off_node_{$node.node_id}" class="cm-combination hidden"><span class="icon-caret-down"></span></span>
                    {/if}
                    <a href="{"nomenclature.update?node_id=`$node.node_id`"|fn_url}" class="link--monochrome" id="node_title_{$node.node_id}">
                        {if $node.node_type == 'G'}<i class="icon-folder-close muted"></i>{/if}
                        {$node.name}
                    </a>
                </div>
            </td>
            {if !$picker}
            <td width="10%" class="mobile-hide" data-th="{__("status")}">
                {include file="common/select_popup.tpl" id=$node.node_id status=$node.status hidden=false object_id_name="node_id" table="nomenclature_nodes" popup_additional_class="dropleft"}
            </td>
            <td width="10%" class="right" data-th="{__("actions")}">
                {capture name="tools_list"}
                    <li>{btn type="list" text=__("edit") href="nomenclature.update?node_id=`$node.node_id`"}</li>
                    <li>{btn type="list" text=__("nomenclature.add") href="nomenclature.add?parent_id=`$node.node_id`"}</li>
                    <li class="divider"></li>
                    <li>{btn type="list" text=__("delete") class="cm-confirm" href="nomenclature.delete?node_id=`$node.node_id`" method="POST"}</li>
                {/capture}
                <div class="hidden-tools">
                    {dropdown content=$smarty.capture.tools_list}
                </div>
            </td>
            {/if}
        </tr>

        {if $node.has_children}
            <tr class="cm-combination hidden" id="node_{$node.node_id}">
                <td colspan="{if $picker}2{else}4{/if}">
                    {include file="addons/manufacturer/views/nomenclature/components/nodes_tree.tpl" nodes=$node.children checkbox_name=$checkbox_name picker=$picker}
                </td>
            </tr>
        {/if}
    {/foreach}
{/if}