<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('system/acp_list'));
$breadcrumb->assign(lang('system', 'acp_maintenance'), uri('system/acp_maintenance'));
$breadcrumb->assign(lang('system', 'acp_update_check'));

$file = @file_get_contents('http://www.goratsch-webdesign.de/acp3/update.txt');
if ($file) {
	$content = explode('||', $file);
	$content[2] = CONFIG_VERSION;

	if (version_compare($content[2], $content[0], '>=')) {
		$tpl->assign('update_text', lang('system', 'acp3_up_to_date'));
	} else {
		$tpl->assign('update_text', sprintf(lang('system', 'acp3_not_up_to_date'), '<a href="' . $content[1] . '" onclick="window.open(this.href); return false">', '</a>'));
	}
	$tpl->assign('update', $content);
}
$content = $tpl->fetch('system/acp_update_check.html');
?>