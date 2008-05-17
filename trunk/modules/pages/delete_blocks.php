<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

breadcrumb::assign(lang('common', 'acp'), uri('acp'));
breadcrumb::assign(lang('pages', 'pages'), uri('acp/pages'));
breadcrumb::assign(lang('pages', 'adm_list_blocks'), uri('acp/pages/adm_list_blocks'));
breadcrumb::assign(lang('pages', 'delete_blocks'));

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (preg_match('/^([\d|]+)$/', $uri->entries))
	$entries = $uri->entries;

if (!isset($entries)) {
	$content = comboBox(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = implode('|', $entries);
	$content = comboBox(lang('pages', 'confirm_delete'), uri('acp/pages/delete_blocks/entries_' . $marked_entries), uri('acp/pages_adm_list_blocks'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && $uri->confirmed) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && validate::isNumber($entry) && $db->select('id', 'pages_blocks', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('pages_blocks', 'id = \'' . $entry . '\'');
		}
	}
	$content = comboBox($bool ? lang('pages', 'delete_block_success') : lang('pages', 'delete_block_error'), uri('acp/pages/adm_list_blocks'));
}
?>