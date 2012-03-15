{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="common|publication_period"}</a></li>
			<li><a href="#tab-2">{lang t="polls|poll"}</a></li>
		</ul>
		<div id="tab-1">
			{$publication_period}
		</div>
		<div id="tab-2">
			<dl>
				<dt><label for="question">{lang t="polls|question"}</label></dt>
				<dd><input type="text" name="question" id="question" value="{$question}" maxlength="120"></dd>
			</dl>
{foreach $answers as $row}
			<dl>
				<dt><label for="answer_{$row.number}">{lang t="polls|answer"} {$row.number+1}</label></dt>
				<dd><input type="text" name="answers[]" id="answer_{$row.number}" value="{$row.value}" maxlength="120"></dd>
			</dl>
{/foreach}
			<dl>
				<dt><label for="multiple">{lang t="common|options"}</label></dt>
				<dd style="margin:0 20px">
					<label for="multiple">
						<input type="checkbox" name="multiple" id="multiple" value="1" class="checkbox"{$multiple}>
						{lang t="polls|multiple_choice"}
					</label>
				</dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
{if !$disable}
		<input type="submit" name="add_answer" value="{lang t="polls|add_answer"}" class="form">
{/if}
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
		<input type="reset" value="{lang t="common|reset"}" class="form">
		{$form_token}
	</div>
</form>