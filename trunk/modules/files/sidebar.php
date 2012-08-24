<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('files');

$time = $date->getCurrentDateTime();
$where = 'start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\'';
$files = $db->select('id, start, link_title', 'files', $where, 'start DESC', $settings['sidebar']);
$c_files = count($files);

if ($c_files > 0) {
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['start'] = $date->format($files[$i]['start']);
		$files[$i]['link_title'] = $db->escape($files[$i]['link_title'], 3);
		$files[$i]['link_title_short'] = shortenEntry($files[$i]['link_title'], 30, 5, '...');
	}
	$tpl->assign('sidebar_files', $files);
}

ACP3_View::displayTemplate('files/sidebar.tpl');
