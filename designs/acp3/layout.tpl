<!DOCTYPE html>
<html lang="{$LANG}">
<head>
<title>{$PAGE_TITLE} :: {$TITLE}</title>
{$META}
<link rel="stylesheet" type="text/css" href="{$MIN_STYLESHEET}">
<script type="text/javascript" src="{$MIN_JAVASCRIPT}"></script>
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_news"}" title="{$PAGE_TITLE} - {lang t="news|news"}">
<link rel="alternate" type="application/rss+xml" href="{uri args="feeds/list/feed_files"}" title="{$PAGE_TITLE} - {lang t="files|files"}">
</head>

<body>
	<div id="box">
		<div id="header">
			<h1 id="page-title">{$PAGE_TITLE}</h1>
			{navbar block="main"}
		</div>
		<div id="breadcrumb">
			{$BREADCRUMB}
		</div>
		<div id="sidebar-left">
			<h4>Navigation</h4>
			{navbar block="sidebar"}
			{load_module module="users|sidebar"}
		</div>
		<div id="sidebar-right">
			{load_module module="news|sidebar"}
			{load_module module="files|sidebar"}
			{load_module module="gallery|sidebar"}
			{load_module module="polls|sidebar"}
		</div>
		<div id="content">
			<h1>{$TITLE}</h1>
			{$CONTENT}
		</div>
		<div id="footer"></div>
	</div>
</body>
</html>