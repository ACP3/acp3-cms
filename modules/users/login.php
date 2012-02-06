<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
if ($auth->isUser()) {
	$uri->redirect(0, ROOT_DIR);
} elseif (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	$result = $auth->login($form['nickname'], $form['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
	if ($result == 1) {
		if (isset($form['redirect_uri'])) {
			$uri->redirect(base64_decode($form['redirect_uri']));
		} elseif (defined('IN_ADM')) {
			$uri->redirect($uri->redirect ? base64_decode($uri->redirect) : 'acp');
		} else {
			$uri->redirect(0, ROOT_DIR);
		}
	} else {
		$error[] = $lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong');
		$tpl->assign('error_msg', comboBox($error));
	}
}
view::setContent(view::fetchTemplate('users/login.tpl'));