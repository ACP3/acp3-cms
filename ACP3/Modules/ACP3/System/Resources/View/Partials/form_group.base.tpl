<div class="form-group">
    <label for="{block FORM_GROUP_LABEL_ID}{$options.0.id}{/block}"
           class="col-sm-2 control-label{if (isset($required) && $required === true) || (isset($labelRequired) && $labelRequired === true)} required{/if}">
        {$label}
    </label>

    <div class="{if !empty($cssSelector)}{$cssSelector}{else}col-sm-10{/if}">
        {block FORM_GROUP_FORM_FIELD}{/block}
        {if !empty($help)}
            <p class="help-block">{$help}</p>
        {/if}
    </div>
</div>
