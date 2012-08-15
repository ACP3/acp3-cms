<div class="container-fluid">
	<div class="row-fluid">
{if isset($categories)}
		<div class="span6">
{if ACP3_Modules::check('newsletter', 'create')}
			<a href="{uri args="newsletter/create"}">{lang t="newsletter|create"}</a>
{/if}
		</div>
		<div class="span6" style="text-align: right">
			<form action="{uri args="news/list"}" method="post" class="form-inline">
				{$categories}
				<input type="submit" name="submit" value="{lang t="common|submit"}" class="btn">
			</form>
		</div>
{/if}
	</div>
</div>
{if isset($news)}
{$pagination}
{foreach $news as $row}
<div class="dataset-box">
	<div class="header">
		<div class="f-right small">
			{$row.date}
		</div>
		{$row.headline}
	</div>
	<div class="content">
		{$row.text}
{if $row.allow_comments}
		<p class="align-center">
			<a href="{uri args="news/details/id_`$row.id`"}#comments">{lang t="comments|comments"}</a>
			<span>({$row.comments})</span>
		</p>
{/if}
	</div>
</div>
{/foreach}
{else}
<div class="alert alert-block align-center">
	<h5>{lang t="common|no_entries"}</h5>
</div>
{/if}