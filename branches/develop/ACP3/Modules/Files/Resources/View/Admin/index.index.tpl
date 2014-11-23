{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/files/index/delete"}" method="post">
        <nav id="adm-list" class="navbar navbar-default" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                    <span class="sr-only">{lang t="system|toggle_navigation"}</span>
                    <span class="icon-bar"></span> <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">{lang t="system|overview"}</span>
            </div>
            <div class="collapse navbar-collapse navbar-ex2-collapse">
                <div class="navbar-text pull-right">
                    {check_access mode="link" path="acp/files/index/create" icon="32/download" width="32" height="32"}
                    {check_access mode="link" path="acp/files/index/settings" icon="32/advancedsettings" width="32" height="32"}
                    {check_access mode="input" path="acp/files/index/delete" icon="32/cancel" lang="system|delete_marked"}
                </div>
            </div>
        </nav>
        {redirect_message}
        {if isset($files)}
            <table id="acp-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    {if $can_delete === true}
                        <th style="width:3%"><input type="checkbox" id="mark-all" value="1" {mark name="entries"}></th>
                    {/if}
                    <th style="width:22%">{lang t="system|publication_period"}</th>
                    <th>{lang t="files|title"}</th>
                    <th>{lang t="files|filename"}</th>
                    <th>{lang t="files|filesize"}</th>
                    <th style="width:5%">{lang t="system|id"}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $files as $row}
                    <tr>
                        {if $can_delete === true}
                            <td><input type="checkbox" name="entries[]" value="{$row.id}"></td>
                        {/if}
                        <td>{date_range start=$row.start end=$row.end}</td>
                        <td>{check_access mode="link" path="acp/files/index/edit/id_`$row.id`" title=$row.title}</td>
                        <td>{check_access mode="link" path="files/index/details/id_`$row.id`/action_download" lang="files|download_file" title=$row.file}</td>
                        <td>{if !empty($row.size)}{$row.size}{else}{lang t="files|unknown_filesize"}{/if}</td>
                        <td>{$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {if $can_delete === true}
                {include file="asset:system/mark.tpl"}
            {/if}
            {include file="asset:system/datatable.tpl" dt=$datatable_config}
        {else}
            <div class="alert alert-warning text-center">
                <strong>{lang t="system|no_entries"}</strong>
            </div>
        {/if}
    </form>
{/block}