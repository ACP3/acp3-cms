<form action="{uri args="acp/guestbook/delete"}" method="post">
	<div id="adm-list" class="navbar">
		<div class="navbar-inner navbar-text">
			<h2>{lang t="system|overview"}</h2>
			{check_access mode="link" path="acp/guestbook/settings" icon="32/advancedsettings" width="32" height="32"}
			{check_access mode="input" path="acp/guestbook/delete" icon="32/cancel" lang="system|delete_marked"}
		</div>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($guestbook)}
	<table id="acp-table" class="table table-striped table-hover">
		<thead>
			<tr>
{if $can_delete === true}
				<th style="width:3%"><input type="checkbox" id="mark-all" value="1"></th>
{/if}
				<th style="width:22%">{lang t="system|date"}</th>
				<th>{lang t="system|name"}</th>
				<th>{lang t="system|message"}</th>
				<th>{lang t="guestbook|ip"}</th>
				<th style="width:5%">{lang t="system|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $guestbook as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
{/if}
				<td>{$row.date_formatted}</td>
				<td>{check_access mode="link" path="acp/guestbook/edit/id_`$row.id`" title=$row.name}</td>
				<td>{$row.message}</td>
				<td>{$row.ip}</td>
				<td>{$row.id}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="alert align-center">
		<strong>{lang t="system|no_entries"}</strong>
	</div>
{/if}
</form>