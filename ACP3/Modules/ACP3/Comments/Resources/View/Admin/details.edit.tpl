{extends file="asset:System/layout.ajax-form.tpl"}

{block CONTENT_AJAX_FORM}
    {if !isset($form.user_id)}
        {include file="asset:System/Partials/form_group.input_text.tpl" name="name" value=$form.name required=true maxlength=20 label={lang t="system|name"}}
    {elseif $form.user_id != '0'}
        <input type="hidden" name="user_id" value="{$form.user_id}">
    {/if}
    {include file="asset:System/Partials/form_group.wysiwyg.tpl" name="message" value=$form.message required=true label={lang t="system|message"} editor="core.wysiwyg.textarea"}
    {include file="asset:System/Partials/form_group.submit_split.tpl" form_token=$form_token back_url={uri args="acp/comments/details/index/id_`$module_id`"}}
{/block}
