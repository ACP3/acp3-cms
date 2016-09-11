{extends file="asset:System/ajax-form.tpl"}

{block AJAX_FORM_CONTENT_BEFORE}
    {redirect_message}
{/block}
{block AJAX_FORM_CONTENT}
    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-1" data-toggle="tab">{lang t="system|general"}</a></li>
            <li><a href="#tab-2" data-toggle="tab">{lang t="system|date"}</a></li>
            <li><a href="#tab-3" data-toggle="tab">{lang t="system|maintenance"}</a></li>
            <li><a href="#tab-5" data-toggle="tab">{lang t="system|performance"}</a></li>
            <li><a href="#tab-6" data-toggle="tab">{lang t="system|email"}</a></li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade in active">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="homepage" value=$form.homepage required=true label={lang t="system|homepage"} help={lang t="system|homepage_description"}}
                {include file="asset:System/Partials/form_group.select.tpl" options=$entries required=true label={lang t="system|records_per_page"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="flood" value=$form.flood required=true label={lang t="system|flood_barrier"} help={lang t="system|flood_barrier_description"}}
                {include file="asset:System/Partials/form_group.select.tpl" options=$wysiwyg required=true label={lang t="system|editor"}}
                <div class="form-group">
                    <label for="language" class="col-sm-2 control-label required">{lang t="system|language"}</label>

                    <div class="col-sm-10">
                        <select class="form-control" name="language" id="language" required>
                            <option value="">{lang t="system|pls_select"}</option>
                            {foreach $languages as $row}
                                <option value="{$row.iso}"{if $row.selected} selected="selected"{/if}>{$row.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$mod_rewrite required=true label={lang t="system|mod_rewrite"} help={lang t="system|mod_rewrite_description"}}
            </div>
            <div id="tab-2" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_long" value=$form.date_format_long required=true maxlength=20 label={lang t="system|date_format_long"} help={lang t="system|php_date_function"}}
                {include file="asset:System/Partials/form_group.input_text.tpl" name="date_format_short" value=$form.date_format_short required=true maxlength=20 label={lang t="system|date_format_short"} help={lang t="system|php_date_function"}}
                <div class="form-group">
                    <label for="date-time-zone" class="col-sm-2 control-label required">{lang t="system|time_zone"}</label>

                    <div class="col-sm-10">
                        <select class="form-control" name="date_time_zone" id="date-time-zone" required>
                            {foreach $time_zones as $key => $values}
                                <optgroup label="{$key}">
                                    {foreach $values as $country => $value}
                                        <option value="{$country}"{$value.selected}>{$country}</option>
                                    {/foreach}
                                </optgroup>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="tab-3" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$maintenance required=true label={lang t="system|maintenance_mode"}}
                {include file="asset:System/Partials/form_group.textarea.tpl" name="maintenance_message" value=$form.maintenance_message required=true label={lang t="system|maintenance_msg"}}
            </div>
            <div id="tab-5" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.button_group.tpl" options=$cache_images required=true label={lang t="system|cache_images"}}
                {include file="asset:System/Partials/form_group.input_number.tpl" name="cache_lifetime" value=$form.cache_lifetime required=true label={lang t="system|cache_lifetime"}}
            </div>
            <div id="tab-6" class="tab-pane fade">
                {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_type required=true label={lang t="system|mailer_type"}}
                <div id="mailer-smtp-1">
                    {include file="asset:System/Partials/form_group.input_number.tpl" name="mailer_smtp_host" value=$form.mailer_smtp_host labelRequired=true label={lang t="system|mailer_smtp_hostname"}}
                    {include file="asset:System/Partials/form_group.input_number.tpl" name="mailer_smtp_port" value=$form.mailer_smtp_port labelRequired=true label={lang t="system|mailer_smtp_port"}}
                    {include file="asset:System/Partials/form_group.select.tpl" options=$mailer_smtp_security required=true label={lang t="system|mailer_smtp_security"}}
                    {include file="asset:System/Partials/form_group.button_group.tpl" options=$mailer_smtp_auth required=true label={lang t="system|mailer_smtp_auth"}}
                    <div id="mailer-smtp-2">
                        {include file="asset:System/Partials/form_group.input_number.tpl" name="mailer_smtp_user" value=$form.mailer_smtp_user labelRequired=true label={lang t="system|mailer_smtp_username"}}
                        {include file="asset:System/Partials/form_group.input_password.tpl" name="mailer_smtp_password" value=$form.mailer_smtp_password label={lang t="system|mailer_smtp_password"}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {include file="asset:System/Partials/form_group.submit.tpl" form_token=$form_token}
    {javascripts}
        {include_js module="system" file="admin/index.configuration"}
    {/javascripts}
{/block}
