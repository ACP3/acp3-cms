{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset class="no-border">
		<dl>
			<dt><label for="mail">{lang t="common|email"}</label></dt>
			<dd><input type="email" name="mail" id="mail" maxlength="120" value="{$form.mail}" required></dd>
		</dl>
{$captcha}
	</fieldset>
{foreach $actions as $row}
	<label for="{$row.value}">
		<input type="radio" name="action" id="{$row.value}" value="{$row.value}"{$row.checked} class="checkbox">
		{$row.lang}
	</label>
{/foreach}
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>