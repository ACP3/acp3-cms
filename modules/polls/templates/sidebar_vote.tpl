<ul class="nav nav-list">
	<li class="nav-header">{lang t="polls|latest_poll"}</li>
{if isset($sidebar_poll_question)}
	<li>
		<h5>{$sidebar_poll_question.question}</h5>
		<form action="{uri args="polls/vote/id_`$sidebar_poll_question.id`"}" method="post" accept-charset="UTF-8">
			<ul class="unstyled">
				<li>
{foreach $sidebar_poll_answers as $row}
{if $sidebar_poll_question.multiple == '1'}
					<label for="answer-{$row.id}" class="checkbox">
						<input type="checkbox" name="answer[]" id="answer-{$row.id}" value="{$row.id}">
{else}
					<label for="answer-{$row.id}" class="radio">
						<input type="radio" name="answer" id="answer-{$row.id}" value="{$row.id}">
{/if}
						{$row.text}
					</label>
{/foreach}
				</li>
			</ul>
			<div>
				<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
			</div>
		</form>
	</li>
</div>
{else}
	<li>{lang t="common|no_entries_short"}</li>
{/if}
</ul>