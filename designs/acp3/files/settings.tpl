{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<fieldset>
		<legend>{lang t="files|settings"}</legend>
		<dl>
			<dt><label for="date-format">{lang t="common|date_format"}</label></dt>
			<dd>
				<select name="form[dateformat]" id="date-format">
					<option value="">{lang t="common|pls_select"}</option>
{foreach $dateformat as $row}
					<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
				</select>
			</dd>
			<dt><label for="sidebar-entries">{lang t="common|sidebar_entries_to_display"}</label></dt>
			<dd>
				<select name="form[sidebar]" id="sidebar-entries">
					<option>{lang t="common|pls_select"}</option>
{foreach $sidebar_entries as $row}
					<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
				</select>
			</dd>
{if isset($comments)}
			<dt><label for="comments-1">{lang t="common|allow_comments"}</label></dt>
			<dd>
{foreach $comments as $row}
				<label for="comments-{$row.value}">
					<input type="radio" name="form[comments]" id="comments-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
					{$row.lang}
				</label>
{/foreach}
			</dd>
{/if}
		</dl>
	</fieldset>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
		{$form_token}
	</div>
</form>