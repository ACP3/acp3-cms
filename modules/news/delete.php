<?php
/**
 * News
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

if (is_array($entries)) {
	$marked_entries = '';
	foreach ($entries as $entry) {
		$marked_entries.= $entry . '|';
	}
	$content = combo_box(lang('news', 'confirm_delete'), uri('acp/news/adm_list/action_delete/entries_' . $marked_entries), uri('acp/news'));
} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
	$marked_entries = explode('|', $entries);
	$bool = 0;
	$bool2 = 0;
	foreach ($marked_entries as $entry) {
		if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'news', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
			$bool = $db->delete('news', 'id = \'' . $entry . '\'');
			$bool2 = $db->delete('comments', 'module = \'news\' AND entry_id = \'' . $entry . '\'');
			// News Cache löschen
			$cache->delete('news_details_id_' . $entry);
		}
	}
	$content = combo_box($bool && $bool2 ? lang('news', 'delete_success') : lang('news', 'delete_error'), uri('acp/news'));
} else {
	redirect('errors/404');
}
?>