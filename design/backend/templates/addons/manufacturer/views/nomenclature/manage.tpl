{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="nomenclature_tree_form" enctype="multipart/form-data" class="cm-ajax cm-ajax-full-render" data-ca-ajax-full-render-event="nomenclature_manage">

    <input type="hidden" name="dispatch" value="nomenclature.multi_delete" />

    <div class="table-responsive-wrapper longtap-selection" data-ca-bulkedit-component="tableWrapper">
        {if $nodes_tree}
            <table class="table table-middle table--relative table-responsive" width="100%">
                <thead data-ca-bulkedit-default-object="true" data-ca-bulkedit-component="defaultObject">
                    <tr>
                        <th class="left mobile-hide" width="6%">
                            {include file="common/check_items.tpl" is_check_disabled=false}

                            <input type="checkbox"
                                class="bulkedit-toggler hide"
                                data-ca-bulkedit-disable="[data-ca-bulkedit-default-object=true]"
                                data-ca-bulkedit-enable="[data-ca-bulkedit-expanded-object=true]"
                            />
                        </th>
                        <th width="70%" class="left">{__("name")}</th>
                        <th width="10%" class="mobile-hide">{__("status")}</th>
                        <th width="10%" class="right">{__("actions")}</th>
                    </tr>
                </thead>
                <tbody>
                    {include file="addons/manufacturer/views/nomenclature/components/nodes_tree.tpl" nodes=$nodes_tree}
                </tbody>
            </table>
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
    </div>

    {if $nodes_tree}
        <div class="buttons-container">
            {include file="buttons/button.tpl"
                but_name="dispatch[nomenclature.multi_delete]"
                but_text=__("delete_selected")
                but_role="submit-link"
                but_meta="cm-confirm"
                but_target_form="nomenclature_tree_form"
            }
        </div>
    {/if}

</form>

{capture name="buttons"}
    {include file="buttons/add_close.tpl" is_dialog=false but_name="dispatch[nomenclature.add]" but_text=__("nomenclature.add") but_role="action"}
{/capture}

{if $nodes_tree}
    {capture name="adv_buttons"}
        {include file="common/tools.tpl" tool_href="nomenclature.add?parent_id=0" prefix="top" hide_tools=true title=__("nomenclature.add") icon="icon-plus" link_text="+ {__('nomenclature.add')}" tool_override_meta="btn nav__actions-btn-primary"}
    {/capture}
{/if}

{/capture}

{include file="common/mainbox.tpl" title_start=__("nomenclature") title_end=__("nomenclature.manage") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons}