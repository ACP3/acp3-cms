{if isset($error_msg)}
    {$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8" class="form-horizontal">
    <div class="form-group">
        <label for="name" class="col-lg-2 control-label">{lang t="system|name"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="text" name="name" id="name" size="35" value="{$form.name}" required{$form.name_disabled}>
        </div>
    </div>
    <div class="form-group">
        <label for="mail" class="col-lg-2 control-label">{lang t="system|email_address"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="email" name="mail" id="mail" size="35" value="{$form.mail}"{$form.mail_disabled}>
        </div>
    </div>
    <div class="form-group">
        <label for="website" class="col-lg-2 control-label">{lang t="system|website"}</label>

        <div class="col-lg-10">
            <input class="form-control" type="url" name="website" id="website" size="35" value="{$form.website}"{$form.website_disabled}>
        </div>
    </div>
    <div class="form-group">
        <label for="message" class="col-lg-2 control-label">{lang t="system|message"}</label>

        <div class="col-lg-10">
            {if isset($emoticons)}{$emoticons}{/if}
            <textarea class="form-control" name="message" id="message" cols="50" rows="6" required>{$form.message}</textarea>
        </div>
    </div>
    {if isset($subscribe_newsletter)}
        <div class="form-group">
            <div class="col-lg-10">
                <div class="checkbox">
                    <label for="subscribe-newsletter">
                        <input type="checkbox" name="subscribe_newsletter" id="subscribe-newsletter" value="1"{$subscribe_newsletter}>
                        {$LANG_subscribe_to_newsletter}
                    </label>
                </div>
            </div>
        </div>
    {/if}
    {if isset($captcha)}
        {$captcha}
    {/if}
    <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
            <button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
            {$form_token}
        </div>
    </div>
</form>