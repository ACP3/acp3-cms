{extends file="asset:`$LAYOUT`"}

{block CONTENT}
    {if !empty($files)}
        {include file="asset:System/Partials/pagination.tpl" pagination=$pagination}
        {foreach $files as $row}
            <div class="dataset-box">
                <div class="navbar navbar-default">
                    <div class="navbar-header">
                        <h3 class="navbar-brand">
                            <a href="{uri args="files/index/details/id_`$row.id`"}">
                                {$row.title}
                                {if !empty($row.size)}
                                    ({$row.size})
                                {else}
                                    ({lang t="files|unknown_filesize"})
                                {/if}
                            </a>
                        </h3>
                    </div>
                    <time class="navbar-text small pull-right" datetime="{date_format date=$row.start format="c"}">
                        {date_format date=$row.start format=$dateformat}
                    </time>
                </div>
            </div>
        {/foreach}
    {else}
        {include file="asset:System/Partials/no_results.tpl"}
    {/if}
{/block}