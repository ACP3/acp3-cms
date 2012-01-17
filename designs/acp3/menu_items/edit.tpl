{if isset($error_msg)}
{$error_msg}
{/if}
<script type="text/javascript" src="{$DESIGN_PATH}menu_items/script.js"></script>
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="common|general_statements"}</legend>
		<dl>
				<dt><label for="start">{lang t="common|publication_period"}</label></dt>
				<dd>{$publication_period}</dd>
		</dl>
		<p>
			{lang t="common|date_description"}
		</p>
		<dl>
			<dt><label for="mode">{lang t="menu_items|page_type"}</label></dt>
			<dd>
				<select name="form[mode]" id="mode">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $mode as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="title">{lang t="menu_items|title"}</label></dt>
			<dd><input type="text" name="form[title]" id="title" value="{$form.title}" maxlength="120"></dd>
		</dl>
{if $enable_uri_aliases === true}
		<dl id="uri-alias">
			<dt>
				<label for="alias">{lang t="common|uri_alias"}</label>
				<span>{lang t="common|uri_alias_description"}</span>
			</dt>
			<dd><input type="text" name="form[alias]" id="alias" value="{$form.alias}"></dd>
		</dl>
{/if}
		<dl>
			<dt><label for="block_id">{lang t="menu_items|blocks"}</label></dt>
			<dd>
				<select name="form[block_id]" id="block_id">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $blocks as $row}
					<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="parent">{lang t="menu_items|superior_page"}</label></dt>
			<dd>
				<select name="form[parent]" id="parent">
					<option value="">{lang t="menu_items|no_superior_page"}</option>
{foreach $pages_list as $block => $pages}
					<optgroup label="{$block}">
{foreach $pages as $row}
						<option value="{$row.id}"{$row.selected}>{$row.spaces}{$row.title}</option>
{/foreach}
					</optgroup>
{/foreach}
				</select>
			</dd>
			<dt><label for="display-1">{lang t="menu_items|display_item"}</label></dt>
			<dd>
{foreach $display as $row}
				<label for="display-{$row.value}">
					<input type="radio" name="form[display]" id="display-{$row.value}" value="{$row.value}" class="checkbox"{$row.selected}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
		</dl>
	</fieldset>
	<fieldset id="page-type">
		<legend>{lang t="menu_items|page_type"}</legend>
		<dl id="module-container">
			<dt><label for="module">{lang t="menu_items|module"}</label></dt>
			<dd>
				<select name="form[module]" id="module">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $modules as $row}
					<option value="{$row.dir}"{$row.selected}>{$row.name}</option>
{/foreach}
				</select>
			</dd>
		</dl>
		<p id="link-hints">
			{lang t="menu_items|dynamic_page_hints"}
		</p>
		<dl id="link-container">
			<dt><label for="uri">{lang t="menu_items|uri"}</label></dt>
			<dd><input type="text" name="form[uri]" id="uri" value="{$form.uri}" maxlength="120"></dd>
		</dl>
{if isset($static_pages)}
		<dl id="static-pages-container">
			<dt><label for="static-pages">{lang t="static_pages|static_pages"}</label></dt>
			<dd>
				<select name="form[static_pages]" id="static-pages">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $static_pages as $row}
					<option value="{$row.id}"{$row.selected}>{$row.title}</option>
{/foreach}
				</select>
			</dd>
		</dl>
{/if}
		<dl>
			<dt><label for="target">{lang t="menu_items|target_page"}</label></dt>
			<dd>
				<select name="form[target]" id="target">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $target as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
	</div>
</form>