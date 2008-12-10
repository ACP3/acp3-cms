<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'guestbook', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = $lang->t('common', 'message_to_short');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
			);

			$bool = $db->update('guestbook', $update_values, 'id = \'' . $uri->id . '\'');

			$content = comboBox($bool ? $lang->t('guestbook', 'edit_success') : $lang->t('guestbook', 'edit_error'), uri('acp/guestbook'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$guestbook = $db->select('name, message', 'guestbook', 'id = \'' . $uri->id . '\'');

		if (modules::check('emoticons', 'functions')) {
			include_once ACP3_ROOT . 'modules/emoticons/functions.php';

			//Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		$tpl->assign('form', isset($form) ? $form : $guestbook[0]);

		$content = $tpl->fetch('guestbook/edit.html');
	}
} else {
	redirect('errors/404');
}
?>