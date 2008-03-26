<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	if (!empty($_FILES['picture']['tmp_name'])) {
		$file['tmp_name'] = $_FILES['picture']['tmp_name'];
		$file['name'] = $_FILES['picture']['name'];
		$file['size'] = $_FILES['picture']['size'];
	}

	if (empty($form['code']))
		$errors[] = lang('emoticons', 'type_in_code');
	if (empty($form['description']))
		$errors[] = lang('emoticons', 'type_in_description');
	if (!isset($file) || empty($file['size']) || !$validate->isPicture($file['tmp_name']))
		$errors[] = lang('emoticons', 'select_picture');

	if (isset($errors)) {
		$tpl->assign('error_msg', combo_box($errors));
	} else {
		$result = move_file($file['tmp_name'], $file['name'], 'emoticons');

		$insert_values = array(
		'id' => '',
		'code' => $db->escape($form['code']),
		'description' => $db->escape($form['description']),
		'img' => $result['name'],
		);

		$bool = $db->insert('emoticons', $insert_values);

		$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

		$content = combo_box($bool ? lang('emoticons', 'create_success') : lang('emoticons', 'create_error'), uri('acp/emoticons'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('emoticons/create.html');
}
?>