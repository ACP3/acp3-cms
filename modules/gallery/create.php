<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (strlen($form['name']) < 3)
		$errors[] = $lang->t('gallery', 'type_in_gallery_name');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$start_date = $date->timestamp($form['start']);
		$end_date = $date->timestamp($form['end']);

		$insert_values = array(
			'id' => '',
			'start' => $start_date,
			'end' => $end_date,
			'name' => $db->escape($form['name']),
		);

		$bool = $db->insert('gallery', $insert_values);

		$content = comboBox($bool ? $lang->t('gallery', 'create_success') : $lang->t('gallery', 'create_error'), uri('acp/gallery'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', datepicker('start'));
	$tpl->assign('end_date', datepicker('end'));

	$tpl->assign('form', isset($form) ? $form : array('name' => ''));

	$content = $tpl->fetch('gallery/create.html');
}
?>