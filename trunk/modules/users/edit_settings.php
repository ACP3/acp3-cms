<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (ACP3_CMS::$auth->isUser() === false || ACP3_Validate::isNumber(ACP3_CMS::$auth->getUserId()) === false) {
	ACP3_CMS::$uri->redirect('errors/403');
} else {
	$settings = ACP3_Config::getSettings('users');

	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
			   ->append(ACP3_CMS::$lang->t('users', 'home'), ACP3_CMS::$uri->route('users/home'))
			   ->append(ACP3_CMS::$lang->t('users', 'edit_settings'));

	if (isset($_POST['submit']) === true) {
		if ($settings['language_override'] == 1 && ACP3_CMS::$lang->languagePackExists($_POST['language']) === false)
			$errors['language'] = ACP3_CMS::$lang->t('users', 'select_language');
		if ($settings['entries_override'] == 1 && ACP3_Validate::isNumber($_POST['entries']) === false)
			$errors['entries'] = ACP3_CMS::$lang->t('common', 'select_records_per_page');
		if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
			$errors[] = ACP3_CMS::$lang->t('system', 'type_in_date_format');
		if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
			$errors['time-zone'] = ACP3_CMS::$lang->t('common', 'select_time_zone');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'date_format_long' => ACP3_CMS::$db->escape($_POST['date_format_long']),
				'date_format_short' => ACP3_CMS::$db->escape($_POST['date_format_short']),
				'time_zone' => $_POST['date_time_zone'],
			);
			if ($settings['language_override'] == 1)
				$update_values['language'] = $_POST['language'];
			if ($settings['entries_override'] == 1)
				$update_values['entries'] = (int) $_POST['entries'];

			$bool = ACP3_CMS::$db->update('users', $update_values, 'id = \'' . ACP3_CMS::$auth->getUserId() . '\'');

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$user = ACP3_CMS::$db->select('date_format_long, date_format_short, time_zone, language, entries', 'users', 'id = \'' . ACP3_CMS::$auth->getUserId() . '\'');

		ACP3_CMS::$view->assign('language_override', $settings['language_override']);
		ACP3_CMS::$view->assign('entries_override', $settings['entries_override']);

		// Sprache
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], ACP3_CMS::$db->escape($user[0]['language'], 3));
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		ACP3_CMS::$view->assign('languages', $languages);

		// Einträge pro Seite
		ACP3_CMS::$view->assign('entries', recordsPerPage((int) $user[0]['entries']));

		// Zeitzonen
		ACP3_CMS::$view->assign('time_zones', ACP3_CMS::$date->getTimeZones($user[0]['time_zone']));

		$user[0]['date_format_long'] = ACP3_CMS::$db->escape($user[0]['date_format_long'], 3);
		$user[0]['date_format_short'] = ACP3_CMS::$db->escape($user[0]['date_format_short'], 3);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $user[0]);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/edit_settings.tpl'));
	}
}