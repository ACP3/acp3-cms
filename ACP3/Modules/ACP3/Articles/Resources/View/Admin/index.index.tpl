{extends file="asset:layout.tpl"}

{block CONTENT}
    <form action="{uri args="acp/articles/index/delete"}" method="post">
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
                    {check_access mode="link" path="acp/articles/index/create" class="glyphicon glyphicon-plus text-success"}
                    {if isset($articles)}
                        {check_access mode="button" path="acp/articles/index/delete" class="glyphicon glyphicon-remove text-danger" lang="system|delete_marked"}
                    {/if}
                </div>
            </div>
        </nav>
        {redirect_message}
        {$grid}
    </form>
{/block}