<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div class="poll">
		<h4>{$question}</h4>
{foreach $answers as $row}
		<label for="answer_{$row.id}" class="{$row.css_class}">
{if $multiple == '1'}
			<input type="checkbox" name="answer[]" id="answer_{$row.id}" value="{$row.id}">
{else}
			<input type="radio" name="answer" id="answer_{$row.id}" value="{$row.id}">
{/if}
			{$row.text}
		</label>
{/foreach}
	</div>
	<div class="form-bottom">
		<input type="submit" name="submit" value="{lang t="common|submit"}" class="form">
	</div>
</form>