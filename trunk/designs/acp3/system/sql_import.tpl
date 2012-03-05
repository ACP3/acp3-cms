{if isset($sql_queries)}
<pre>
{foreach $sql_queries as $row}
<span style="color:#{$row.color}">{$row.query}</span>
{/foreach}
</pre>
{else}
{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" enctype="multipart/form-data" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="system|sql_import"}</legend>
		<dl>
			<dt><label for="text">{lang t="system|text"}</label></dt>
			<dd><textarea name="text" id="text" cols="50" rows="6">{$form.text}</textarea></dd>
			<dt><label for="file">{lang t="system|file"}</label></dt>
			<dd><input type="file" name="file" id="file"></dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>
{/if}