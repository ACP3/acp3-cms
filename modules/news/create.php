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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!$validate->date($form))
		$errors[] = lang('common', 'select_date');
	if (strlen($form['headline']) < 3)
		$errors[] = lang('news', 'headline_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = lang('news', 'text_to_short');
	if (!$validate->is_number($form['cat']) || $validate->is_number($form['cat']) && $db->select('id', 'categories', 'id = \'' . $form['cat'] . '\'', 0, 0, 0, 1) != '1')
		$errors[] = lang('news', 'select_category');
	if (!empty($form['uri']) && (!$validate->is_number($form['target']) || strlen($form['link_title']) < 3))
		$errors[] = lang('news', 'complete_additional_hyperlink_statements');

	if (isset($errors)) {
		combo_box($errors);
	} else {
		$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
		$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

		$insert_values = array(
		'id' => '',
		'start' => $start_date,
		'end' => $end_date,
		'headline' => $db->escape($form['headline']),
		'text' => $db->escape($form['text'], 2),
		'category_id' => $form['cat'],
		'uri' => $db->escape($form['uri'], 2),
		'target' => $form['target'],
		'link_title' => $db->escape($form['link_title'])
		);

		$bool = $db->insert('news', $insert_values);

		$content = combo_box($bool ? lang('news', 'create_success') : lang('news', 'create_error'), uri('acp/news'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

	// Kategorien
	if (!$cache->check('categories_news')) {
		$cache->create('categories_news', $db->select('id, name, description', 'categories', 'module = \'news\'', 'name ASC'));
	}
	$categories = $cache->output('categories_news');
	$c_categories = count($categories);

	if ($c_categories > 0) {
		for ($i = 0; $i < $c_categories; $i++) {
			$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id']);
			$categories[$i]['name'] = $categories[$i]['name'];
		}
		$tpl->assign('categories', $categories);
	}

	// Linkziel
	$target[0]['value'] = '1';
	$target[0]['selected'] = select_entry('target', '1');
	$target[0]['lang'] = lang('common', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = select_entry('target', '2');
	$target[1]['lang'] = lang('common', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('news/create.html');
}
?>