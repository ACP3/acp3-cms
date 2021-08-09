{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {tabset identifier="role-admin-edit-form"}
        {tab title={lang t="system|general_statements"}}
            {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=100 label={lang t="system|name"}}
            {if !empty($parent)}
                {include file="asset:System/Partials/form_group.select.tpl" options=$parent required=true label={lang t="permissions|superior_role"}}
            {/if}
        {/tab}
        {tab title={lang t="permissions|permissions"}}
            {foreach $modules as $moduleName => $values}
                <fieldset>
                    <legend>{lang t="`$moduleName`|`$moduleName`"}</legend>
                    {foreach $values.resources as $resource}
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{lang t="`$moduleName`|`$resource.area`_`$resource.controller`_`$resource.page`"}</label>

                            <div class="col-sm-8">
                                <div class="btn-group" data-toggle="buttons">
                                    {foreach $resource.select as $row}
                                        <label for="resource-{$resource.resource_id}-{$row.value}" class="btn btn-default{if !empty($row.selected)} active{/if}">
                                            <input type="radio" name="resources[{$resource.resource_id}]" id="resource-{$resource.resource_id}-{$row.value}" value="{$row.value}"{$row.selected}>
                                            {$row.lang}
                                            {if $row.value === 2}
                                                <small>({$resource.calculated})</small>
                                            {/if}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </fieldset>
            {/foreach}
        {/tab}
    {/tabset}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/permissions"}}
{/block}
