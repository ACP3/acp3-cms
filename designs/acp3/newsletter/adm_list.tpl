<form action="{uri args="acp/newsletter/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="newsletter|compose" uri="acp/newsletter/compose" icon="32/mail_new" width="32" height="32"}
		{check_access mode="link" action="newsletter|adm_list_archive" uri="acp/newsletter/adm_list_archive" icon="32/mail" width="32" height="32"}
		{check_access mode="link" action="newsletter|settings" uri="acp/newsletter/settings" icon="32/advancedsettings" width="32" height="32"}
		{check_access mode="input" action="newsletter|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="newsletter|newsletter_accounts"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($accounts)}
{$pagination}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="common|email"}</th>
				<th>{lang t="newsletter|status"}</th>
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $accounts as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{$row.mail}</td>
				<td>
{if !empty($row.has)}
					<a href="{uri args="acp/newsletter/adm_activate/id_`$row.id`"}" title="{lang t="newsletter|activate_account"}">{icon path="16/cancel"}</a>
{else}
					{icon path="16/apply"}
{/if}
				</td>
				<td>{$row.id}</td>
			</tr>
{/foreach}
		</tbody>
	</table>
{if $can_delete === true}
{mark name="entries"}
{/if}
{else}
	<div class="error-box">
		<h5>{lang t="common|no_entries"}</h5>
	</div>
{/if}
</form>