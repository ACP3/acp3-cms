<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];
		if (isset($form['external'])) {
			$file = $form['file_external'];
		} else {
			$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
			$file['name'] = $_FILES['file_internal']['name'];
			$file['size'] = $_FILES['file_internal']['size'];
		}
		$i = 0;

		if (!$validate->date($form))
			$errors[$i++] = lang('common', 'select_date');
		if (strlen($form['link_title']) < 3)
			$errors[$i++] = lang('dl', 'type_in_link_title');
		if (isset($_POST['external']) && empty($file))
			$errors[$i++] = lang('dl', 'type_in_external_resource');
		if (!isset($_POST['external']) && (empty($file['tmp_name']) || $file['size'] == '0'))
			$errors[$i++] = lang('dl', 'select_internal_resource');
		if (strlen($form['text']) < 3)
			$errors[$i++] = lang('dl', 'description_to_short');
		if (!ereg('[0-9]', $form['cat']) || ereg('[0-9]', $form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[$i++] = lang('dl', 'select_category');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			if (is_array($file)) {
				$result = move_file($file['tmp_name'], $file['name'], 'dl');
				$new_file = $result['name'];
				$filesize = $result['size'];
			} elseif (is_string($file)) {
				$new_file = $file;
				$filesize = 0;
			}
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$insert_values = array(
				'id' => '',
				'start' => $start_date,
				'end' => $end_date,
				'cat' => $form['cat'],
				'file' => $new_file,
				'size' => $filesize,
				'link_title' => $db->escape($form['link_title']),
				'text' => $db->escape($form['text'], 2),
			);

			$bool = $db->insert('dl', $insert_values);

			$content = combo_box($bool ? lang('dl', 'create_success') : lang('dl', 'create_error'), uri('acp/dl'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];
		if (isset($form['external'])) {
			$file = $form['file_external'];
		} elseif (!empty($_FILES['file_internal']['name'])) {
			$file['tmp_name'] = $_FILES['file_internal']['tmp_name'];
			$file['name'] = $_FILES['file_internal']['name'];
			$file['size'] = $_FILES['file_internal']['size'];
		}
		$i = 0;

		if (!$validate->date($form))
			$errors[$i++] = lang('common', 'select_date');
		if (strlen($form['link_title']) < 3)
			$errors[$i++] = lang('dl', 'type_in_link_title');
		if (isset($form['external']) && empty($file))
			$errors[$i++] = lang('dl', 'type_in_external_resource');
		if (!isset($form['external']) && isset($file) && is_array($file) && (empty($file['tmp_name']) || $file['size'] == '0'))
			$errors[$i++] = lang('dl', 'select_internal_resource');
		if (strlen($form['text']) < 3)
			$errors[$i++] = lang('dl', 'description_to_short');
		if (!ereg('[0-9]', $form['cat']) || ereg('[0-9]', $form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
			$errors[$i++] = lang('dl', 'select_category');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$new_file_sql = null;
			// Falls neue Datei angegeben wurde, Änderungen durchführen
			if (isset($file)) {
				if (is_array($file)) {
					$result = move_file($file['tmp_name'], $file['name'], 'dl');
					$new_file = $result['name'];
					$filesize = $result['size'];
				} elseif (is_string($file)) {
					$new_file = $file;
					$filesize = 0;
				}
				// SQL Query für die Änderungen
				$new_file_sql = array(
					'file' => $new_file,
					'size' => $filesize,
				);
			}
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'cat' => $form['cat'],
				'link_title' => $db->escape($form['link_title']),
				'text' => $db->escape($form['text'], 2),
			);
			if (is_array($new_file_sql)) {
				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('dl', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('dl_details_id_' . $modules->id, $db->select('d.id, d.start, d.end, d.file, d.size, d.link_title, d.text, c.id AS cat_id, c.name AS cat_name', 'dl AS d, ' . CONFIG_DB_PRE . 'categories AS c', 'd.id = \'' . $modules->id . '\' AND d.cat = c.id'));

			$content = combo_box($bool ? lang('dl', 'edit_success') : lang('dl', 'edit_error'), uri('acp/dl'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('dl', 'confirm_delete'), uri('acp/dl/adm_list/action_delete/entries_' . $marked_entries), uri('acp/dl'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'dl', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					// Datei ebenfalls löschen
					$file = $db->select('file', 'dl', 'id = \'' . $entry . '\'');
					if (is_file('files/dl/' . $file[0]['file'])) {
						unlink('files/dl/' . $file[0]['file']);
					}
					$bool = $db->delete('dl', 'id = \'' . $entry . '\'');

					$cache->delete('dl_details_id_' . $entry);
				}
			}
			$content = combo_box($bool ? lang('dl', 'delete_success') : lang('dl', 'delete_error'), uri('acp/dl'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>