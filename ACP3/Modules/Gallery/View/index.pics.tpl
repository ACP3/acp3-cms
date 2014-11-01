{if isset($pictures)}
    {$i=1}
    {if $overlay == 1}
        {include_js module="gallery" file="pics" depends="fancybox"}
        {foreach $pictures as $row}
            {if $i % 4 === 0}
                <div class="row">
            {/if}
                <div class="col-sm-3">
                    <a href="{$row.uri}" class="thumbnail" data-fancybox-group="gallery"{if !empty($row.description)} title="{$row.description}"{/if}>
                        <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
                    </a>
                </div>
            {if $i % 4 === 0}
                </div>
            {/if}
            {$i=$i+1}
        {/foreach}
    {else}
        {foreach $pictures as $row}
            {if $i % 4 === 0}
                <div class="row">
            {/if}
            <div class="col-sm-3">
                <a href="{$row.uri}" class="thumbnail">
                    <img src="{uri args="gallery/index/image/id_`$row.id`/action_thumb"}" alt="" width="{$row.width}" height="{$row.height}">
                </a>
            </div>
            {if $i % 4 === 0}
                </div>
            {/if}
            {$i=$i+1}
        {/foreach}
    {/if}
{else}
    <div class="alert alert-warning text-center">
        <strong>{lang t="gallery|no_pictures"}</strong>
    </div>
{/if}