{if $current_page_type == $smarty.const.PAGE_TYPE_MANUFACTURER}

    {* Determine the type of the current element:
       - on update: the page being edited (manufacturer_elem_type)
       - on manage: the ancestor under which we are going to add (from parent_id) *}
    {assign var="cur_elem_type" value="M"}

    {if $runtime.mode == 'update' && !empty($manufacturer_elem_type)}
        {assign var="cur_elem_type" value=$manufacturer_elem_type}
    {elseif $runtime.mode == 'manage' && !empty($_REQUEST['parent_id'])}
        {assign var="cur_elem_type" value="C"}
    {/if}

    {* Product groups (G) are terminal nodes — no children allowed *}
    {if $cur_elem_type != 'G'}

        {assign var="target_parent_id" value=0}
        {if $runtime.mode == 'update' && !empty($_REQUEST['page_id'])}
            {assign var="target_parent_id" value=$_REQUEST['page_id']}
        {elseif $runtime.mode == 'manage' && !empty($_REQUEST['parent_id'])}
            {assign var="target_parent_id" value=$_REQUEST['parent_id']}
        {/if}

        <div class="btn-group">
            {* Add subcategory — only meaningful under a Manufacturer or a Category *}
            {if $cur_elem_type != 'M' && $cur_elem_type != 'G'}
                {include file="common/tools.tpl"
                    tool_href="pages.add?page_type=`$smarty.const.PAGE_TYPE_MANUFACTURER`&parent_id=`$target_parent_id`&m_type=C"
                    tool_override_meta="btn nav__actions-btn-primary"
                    prefix="top"
                    hide_tools=true
                    title=__("manufacturer.add_subcategory")
                    link_text="+ {__('manufacturer.add_subcategory')}"
                    icon="icon-plus"
                }
            {elseif $cur_elem_type == 'M'}
                {include file="common/tools.tpl"
                    tool_href="pages.add?page_type=`$smarty.const.PAGE_TYPE_MANUFACTURER`&parent_id=`$target_parent_id`&m_type=C"
                    tool_override_meta="btn nav__actions-btn-primary"
                    prefix="top"
                    hide_tools=true
                    title=__("manufacturer.add_category")
                    link_text="+ {__('manufacturer.add_category')}"
                    icon="icon-plus"
                }
            {/if}

            {include file="common/tools.tpl"
                tool_href="pages.add?page_type=`$smarty.const.PAGE_TYPE_MANUFACTURER`&parent_id=`$target_parent_id`&m_type=G"
                tool_override_meta="btn nav__actions-btn-primary"
                prefix="top"
                hide_tools=true
                title=__("manufacturer.add_product_group")
                link_text="+ {__('manufacturer.add_product_group')}"
                icon="icon-plus"
            }
        </div>

    {/if}
{/if}