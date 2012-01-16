{if isset($error_msg)}
{$error_msg}
{/if}
<form action="{$REQUEST_URI}" method="post" accept-charset="UTF-8">
	<div id="tabs">
		<ul>
			<li><a href="#tab-1">{lang t="system|general"}</a></li>
			<li><a href="#tab-2">{lang t="common|date"}</a></li>
			<li><a href="#tab-3">{lang t="system|maintenance"}</a></li>
			<li><a href="#tab-4">{lang t="common|seo"}</a></li>
			<li><a href="#tab-5">{lang t="system|performance"}</a></li>
		</ul>
		<div id="tab-1">
			<dl>
				<dt><label for="entries">{lang t="system|entries_per_page"}</label></dt>
				<dd>
					<select name="form[entries]" id="entries">
{foreach $entries as $row}
						<option value="{$row.value}"{$row.selected}>{$row.value}</option>
{/foreach}
					</select>
				</dd>
				<dt>
					<label for="flood">{lang t="system|flood_barrier"}</label>
					<span>({lang t="system|flood_barrier_description"})</span>
				</dt>
				<dd><input type="number" name="form[flood]" id="flood" value="{$form.flood}" maxlength="3"></dd>
				<dt>
					<label for="homepage">{lang t="system|homepage"}</label>
					<span>({lang t="system|homepage_description"})</span>
				</dt>
				<dd><input type="text" name="form[homepage]" id="homepage" value="{$form.homepage}"></dd>
				<dt><label for="wysiwyg">{lang t="system|editor"}</label></dt>
				<dd>
					<select name="form[wysiwyg]" id="wysiwyg">
{foreach $wysiwyg as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
			</dl>
		</div>
		<div id="tab-2" class="ui-tabs-hide">
			<dl>
				<dt>
					<label for="date-format-long">{lang t="common|date_format_long"}</label>
					<span>({lang t="system|php_date_function"})</span>
				</dt>
				<dd><input type="text" name="form[date_format_long]" id="date-format-long" value="{$form.date_format_long}" maxlength="20"></dd>
				<dt><label for="date-format-short">{lang t="common|date_format_short"}</label></dt>
				<dd><input type="text" name="form[date_format_short]" id="date-format-short" value="{$form.date_format_short}" maxlength="20"></dd>
				<dt><label for="date-time-zone">{lang t="common|time_zone"}</label></dt>
				<dd>
					<select name="form[date_time_zone]" id="date-time-zone">
{foreach $time_zone as $row}
						<option value="{$row.value}"{$row.selected}>{$row.lang}</option>
{/foreach}
					</select>
				</dd>
				<dt><label for="date-dst-1">{lang t="common|daylight_saving_time"}</label></dt>
				<dd>
{foreach $dst as $row}
					<label for="date-dst-{$row.value}">
						<input type="radio" name="form[date_dst]" id="date-dst-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
		<div id="tab-3" class="ui-tabs-hide">
			<dl>
				<dt><label for="maintenance-mode-1">{lang t="system|maintenance_mode"}</label></dt>
				<dd>
{foreach $maintenance as $row}
					<label for="maintenance-mode-{$row.value}">
						<input type="radio" name="form[maintenance_mode]" id="maintenance-mode-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt><label for="maintenance-message">{lang t="system|maintenance_msg"}</label></dt>
				<dd><textarea name="form[maintenance_message]" id="maintenance-message" cols="50" rows="6">{$form.maintenance_message}</textarea></dd>
			</dl>
		</div>
		<div id="tab-4" class="ui-tabs-hide">
			<dl>
				<dt><label for="seo-title">{lang t="system|title"}</label></dt>
				<dd><input type="text" name="form[seo_title]" id="seo-title" value="{$form.seo_title}" maxlength="120"></dd>
				<dt><label for="seo-meta-description">{lang t="common|description"}</label></dt>
				<dd><input type="text" name="form[seo_meta_description]" id="seo-meta-description" value="{$form.seo_meta_description}" maxlength="120"></dd>
				<dt>
					<label for="seo-meta-keywords">{lang t="common|keywords"}</label>
					<span>({lang t="common|keywords_separate_with_commas"})</span>
				</dt>
				<dd><textarea name="form[seo_meta_keywords]" id="seo-meta-keywords" cols="50" rows="6">{$form.seo_meta_keywords}</textarea></dd>
				<dt><label for="seo-aliases-1">{lang t="system|enable_seo_aliases"}</label></dt>
				<dd>
{foreach $aliases as $row}
					<label for="seo-aliases-{$row.value}">
						<input type="radio" name="form[seo_aliases]" id="seo-aliases-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt>
					<label for="seo-mod-rewrite-1">{lang t="system|mod_rewrite"}</label>
					<span>({lang t="system|mod_rewrite_description"})</span>
				</dt>
				<dd>
{foreach $mod_rewrite as $row}
					<label for="seo-mod-rewrite-{$row.value}">
						<input type="radio" name="form[seo_mod_rewrite]" id="seo-mod-rewrite-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
			</dl>
		</div>
		<div id="tab-5" class="ui-tabs-hide">
			<dl>
				<dt><label for="cache-images-1">{lang t="system|cache_images"}</label></dt>
				<dd>
{foreach $cache_images as $row}
					<label for="cache_images-{$row.value}">
						<input type="radio" name="form[cache_images]" id="cache-images-{$row.value}" value="{$row.value}" class="checkbox"{$row.checked}>
						{$row.lang}
					</label>
{/foreach}
				</dd>
				<dt>
					<label for="cache-minify">{lang t="system|minify_cache_lifetime"}</label>
					<span>({lang t="system|minify_cache_lifetime_description"})</span>
				</dt>
				<dd><input type="text" name="form[cache_minify]" id="cache-minify" value="{$form.cache_minify}" maxlength="20"></dd>
			</dl>
		</div>
	</div>
	<div class="form-bottom">
		<input type="submit" value="{lang t="common|submit"}" class="form">
	</div>
</form>