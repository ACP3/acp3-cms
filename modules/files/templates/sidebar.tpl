<ul class="nav nav-list">
	<li class="nav-header">{lang t="files|latest_files"}</li>
{if isset($sidebar_files)}
{foreach $sidebar_files as $row}
	<li><a href="{uri args="files/details/id_`$row.id`"}" title="{$row.start} - {$row.link_title}">{$row.link_title_short}</a></li>
{/foreach}
{else}
	<li>{lang t="system|no_entries_short"}</li>
{/if}
</ul>