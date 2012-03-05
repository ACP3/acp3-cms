<form action="{uri args="acp/menu_items/delete"}" method="post">
	<div id="adm-list">
		{check_access mode="link" action="menu_items|create" uri="acp/menu_items/create" icon="32/kmenuedit" width="32" height="32"}
		{check_access mode="link" action="menu_items|adm_list_blocks" uri="acp/menu_items/adm_list_blocks" icon="32/blockdevice" width="32" height="32"}
		{check_access mode="input" action="menu_items|delete" icon="32/cancel" lang="common|delete_marked"}
		<h2>{lang t="common|overview"}</h2>
	</div>
{if isset($redirect_message)}
{$redirect_message}
{/if}
{if isset($pages_list)}
{assign var="can_delete" value=ACP3_Modules::check("menu_items", "delete")}
{assign var="can_order" value=ACP3_Modules::check("menu_items", "order")}
	<table class="acp-table">
		<thead>
			<tr>
{if $can_delete === true}
				<th><input type="checkbox" id="mark-all" value="1" class="checkbox"></th>
{/if}
				<th>{lang t="menu_items|page_type"}</th>
				<th style="width:30%">{lang t="menu_items|title"}</th>
{if $can_order === true}
				<th>{lang t="common|order"}</th>
{/if}
				<th style="width:3%">{lang t="common|id"}</th>
			</tr>
		</thead>
		<tbody>
{foreach $pages_list as $block => $pages}
			<tr>
				<td colspan="{if $can_order === true}5{else}4{/if}" class="sub-table-header">{$block}</td>
			</tr>
{foreach $pages as $row}
			<tr>
{if $can_delete === true}
				<td><input type="checkbox" name="entries[]" value="{$row.id}" class="checkbox"></td>
{/if}
				<td>{$row.mode_formated}</td>
				<td style="padding-left:10px;text-align:left">{$row.spaces}{check_access mode="link" action="menu_items|edit" uri="acp/menu_items/edit/id_`$row.id`" title=$row.title}</td>
{if $can_order === true}
				<td>
{if !$row.last}
					<a href="{uri args="acp/menu_items/order/id_`$row.id`/action_down"}" title="{lang t="common|move_down"}">{icon path="16/down" width="16" height="16" alt="{lang t="common|move_down"}"}</a>
{/if}
{if !$row.first}
					<a href="{uri args="acp/menu_items/order/id_`$row.id`/action_up"}" title="{lang t="common|move_up"}">{icon path="16/up" width="16" height="16" alt="{lang t="common|move_up"}"}</a>
{/if}
{if $row.first && $row.last}
					{icon path="16/editdelete" width="16" height="16" alt="{lang t="common|move_impossible"}" title="{lang t="common|move_impossible"}"}
{/if}
				</td>
{/if}
				<td>{$row.id}</td>
			</tr>
{/foreach}
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