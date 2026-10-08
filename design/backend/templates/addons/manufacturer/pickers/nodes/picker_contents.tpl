{if !$smarty.request.extra}
<script>
(function(_, $) {
    _.tr('text_items_added', '{__("text_items_added")|escape:"javascript"}');
    var display_type = '{$smarty.request.display|escape:javascript nofilter}';

    $.ceEvent('on', 'ce.formpost_nomenclature_nodes_form', function(frm, elm) {
        var nodes = {};

        if ($('input.cm-item:checked', frm).length > 0) {
            $('input.cm-item:checked', frm).each( function() {
                var id = $(this).val();
                nodes[id] = $('#node_title_' + id).text();
            });

            {literal}
            $.cePicker('add_js_item', frm.data('caResultId'), nodes, 'a', {
                '{node_id}': '%id',
                '{node}': '%item'
            });
            {/literal}

            if (display_type != 'radio') {
                $.ceNotification('show', {
                    type: 'N',
                    title: _.tr('notice'),
                    message: _.tr('text_items_added'),
                    message_state: 'I'
                });
            }
        }

        return false;
    });
}(Tygh, Tygh.$));
</script>
{/if}

<form action="{$smarty.request.extra|to_relative_url|fn_url}" data-ca-result-id="{$smarty.request.data_id}" method="post" name="nomenclature_nodes_form">

    {if $nodes_tree}
        <div class="items-container multi-level">
            {$random_value = rand()}
            {$combination_suffix = $combination_suffix|default:"_`$random_value`"}
            {include file="addons/manufacturer/views/nomenclature/components/nodes_tree.tpl"
                nodes=$nodes_tree
                header=true
                picker=true
                checkbox_name=$smarty.request.checkbox_name
                hide_delete_button=true
                display=$smarty.request.display
                combination_suffix=$combination_suffix}
        </div>
    {else}
        <div class="items-container"><p class="no-items">{__("no_data")}</p></div>
    {/if}

    {if $nodes_tree}
    <div class="buttons-container">
        {if $smarty.request.display == "radio"}
            {$but_close_text = __("choose")}
        {else}
            {$but_close_text = $button_names.but_close_text|default:__("add_pages_and_close")}
            {$but_text = $button_names.but_text|default:__("nomenclature.add")}
        {/if}
        {include file="buttons/add_close.tpl" is_js=$smarty.request.extra|fn_is_empty}
    </div>
    {/if}
</form>