<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!$auth->isUser() || !validate::isNumber(USER_ID)) {
	redirect('errors/403');
} else {
	breadcrumb::assign($lang->t('users', 'users'), uri('users'));
	breadcrumb::assign($lang->t('users', 'home'), uri('users/home'));
	breadcrumb::assign($lang->t('users', 'edit_settings'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (!is_file('languages/' . db::escape($form['language'], 2) . '/info.xml'))
			$errors[] = $lang->t('users', 'select_language');
		if (!validate::isNumber($form['entries']))
			$errors[] = $lang->t('system', 'select_entries_per_page');
		if (empty($form['date_format_long']) || empty($form['date_format_short']))
			$errors[] = $lang->t('system', 'type_in_date_format');
		if (!is_numeric($form['time_zone']))
			$errors[] = $lang->t('common', 'select_time_zone');
		if (!validate::isNumber($form['dst']))
			$errors[] = $lang->t('common', 'select_daylight_saving_time');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'date_format_long' => db::escape($form['date_format_long']),
				'date_format_short' => db::escape($form['date_format_short']),
				'time_zone' => $form['time_zone'],
				'dst' => $form['dst'],
				'language' => db::escape($form['language'], 2),
				'entries' => (int) $form['entries'],
			);

			$bool = $db->update('users', $update_values, 'id = \'' . USER_ID . '\'');

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('users/home'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $db->select('date_format_long, date_format_short, time_zone, dst, language, entries', 'users', 'id = \'' . USER_ID . '\'');

		// Sprache
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], db::escape($user[0]['language'], 3));
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		$tpl->assign('languages', $languages);

		// Einträge pro Seite
		$i = 0;
		for ($j = 10; $j <= 50; $j = $j + 10) {
			$entries[$i]['value'] = $j;
			$entries[$i]['selected'] = selectEntry('entries', $j, $auth->entries);
			$i++;
		}
		$tpl->assign('entries', $entries);

		// Zeitzonen
		$tpl->assign('time_zone', timeZones($user[0]['time_zone']));

		// Sommerzeit an/aus
		$dst[0]['value'] = '1';
		$dst[0]['checked'] = selectEntry('dst', '1', $user[0]['dst'], 'checked');
		$dst[0]['lang'] = $lang->t('common', 'yes');
		$dst[1]['value'] = '0';
		$dst[1]['checked'] = selectEntry('dst', '0', $user[0]['dst'], 'checked');
		$dst[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('dst', $dst);

		$user[0]['date_format_long'] = db::escape($user[0]['date_format_long'], 3);
		$user[0]['date_format_short'] = db::escape($user[0]['date_format_short'], 3);

		$tpl->assign('form', isset($form) ? $form : $user[0]);
		$content = $tpl->fetch('users/edit_settings.html');
	}
}