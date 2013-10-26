<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">{lang t="polls|latest_poll"}</h3>
	</div>
	<div class="panel-body">
		{if isset($sidebar_polls)}
			<h5>{$sidebar_polls.title}</h5>
			<form action="{uri args="polls/vote/id_`$sidebar_polls.id`"}" method="post" accept-charset="UTF-8">
				{foreach $sidebar_poll_answers as $row}
					{if $sidebar_polls.multiple == '1'}
						<div class="checkbox">
							<label for="answer-{$row.id}-sidebar">
								<input type="checkbox" name="answer[]" id="answer-{$row.id}-sidebar" value="{$row.id}">
								{$row.text}
							</label>
						</div>
					{else}
						<div class="radio">
							<label for="answer-{$row.id}-sidebar">
								<input type="radio" name="answer" id="answer-{$row.id}-sidebar" value="{$row.id}">
								{$row.text}
							</label>
						</div>
					{/if}
				{/foreach}
				<button type="submit" name="submit" class="btn btn-primary">{lang t="system|submit"}</button>
			</form>
		{else}
			<div class="list-group" style="margin-bottom: 0">
				<span class="list-group-item">{lang t="system|no_entries_short"}</span>
			</div>
		{/if}
	</div>
</div>