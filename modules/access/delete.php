<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']))
	$entries = $_POST['entries'];
elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
	$entries = $modules->gen['entries'];

if (!isset($entries)) {
	$content = combo_box(array(lang('common', 'no_entries_selected')));
} elseif (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('access', 'confirm_delete'), uri('acp/access/delete/entries_' . $marked_entries), uri('acp/access'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$level_undeletable = 0;

	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'access', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			if ($entry == '1' || $entry == '2' || $entry == '3') {
				$level_undeletable = 1;
			} else {
				$bool = $db->delete('access', 'id = \'' . $entry . '\'');
			}
		}
	}
	if ($level_undeletable) {
		$text = lang('access', 'access_level_undeletable');
	} else {
		$text = $bool ? lang('access', 'delete_success') : lang('access', 'delete_error');
	}
	$content = combo_box($text, uri('acp/access'));
}
?>