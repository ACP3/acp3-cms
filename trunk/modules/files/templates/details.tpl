<div class="dataset-box">
	<div class="header">
		<div class="f-right small">{$file.date}</div>
		{$file.link_title}
	</div>
	<div class="content">
		{$file.text}
		<div class="hyperlink">
			<a href="{uri args="files/details/id_`$file.id`/action_download"}" class="download-file">{lang t="files|download_file"} ({$file.size})</a>
		</div>
	</div>
</div>
{if isset($comments)}
{$comments}
{/if}