{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2">{lang t="static_pages|page_statements"}</a></li>
			<li><a href="#tab-3">{lang t="common|seo"}</a></li>
		</ul>
		<div id="tab-1">
			{$publication_period}
		</div>
		<div id="tab-2">
			<dl>
				<dt><label for="title">{lang t="static_pages|title"}</label></dt>
				<dd><input type="text" name="title" id="title" value="{$form.title}" maxlength="120"></dd>
			</dl>
			<dl>
				<dt><label for="text">{lang t="static_pages|text"}</label></dt>
				<dd>{wysiwyg name="text" value="`$form.text`" height="250" advanced="1"}</dd>
			</dl>
		</div>
		<div id="tab-3">
			{$SEO_FORM_FIELDS}
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>