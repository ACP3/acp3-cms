<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (!validate::date($form['start'], $form['end']))
		$errors[] = $lang->t('common', 'select_date');
	if (empty($form['question']))
		$errors[] = $lang->t('polls', 'type_in_question');
	$i = 0;
	foreach ($form['answers'] as $row) {
		if (!empty($row))
			$i++;
	}
	if ($i <= 1)
		$errors[] = $lang->t('polls', 'type_in_answer');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} elseif (!validate::formToken()) {
		view::setContent(comboBox($lang->t('common', 'form_already_submitted')));
	} else {
		$start = $date->timestamp($form['start']);
		$end = $date->timestamp($form['end']);
		$question = $db->escape($form['question']);

		$insert_values = array(
			'id' => '',
			'start' => $start,
			'end' => $end,
			'question' => $question,
			'multiple' => isset($form['multiple']) ? '1' : '0',
			'user_id' => $auth->getUserId(),
		);

		$bool = $db->insert('polls', $insert_values);

		if ($bool) {
			$poll_id = $db->select('id', 'polls', 'start = \'' . $start . '\' AND end = \'' . $end . '\' AND question = \'' . $question . '\'', 'id DESC', 1);
			foreach ($form['answers'] as $row) {
				if (!empty($row)) {
					$insert_answer = array(
						'id' => '',
						'text' => $db->escape($row),
						'poll_id' => $poll_id[0]['id'],
					);
					$bool2 = $db->insert('poll_answers', $insert_answer);
				}
			}
		}

		$session->unsetFormToken();

		view::setContent(comboBox($bool && $bool2 ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('acp/polls')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$answers = array();
	if (isset($_POST['form']['answers'])) {
		// Bisherige Antworten
		$i = 0;
		foreach ($_POST['form']['answers'] as $row) {
			$answers[$i]['number'] = $i;
			$answers[$i]['value'] = $row;
			$i++;
		}
		// Neue Antwort nur hinzufügen, wenn die vorangegangene nicht leer ist
		if (count($_POST['form']['answers']) <= 9 && !empty($_POST['form']['answers'][$i - 1]) && isset($_POST['form']) === false) {
			$answers[$i]['number'] = $i;
			$answers[$i]['value'] = '';
		}
	} else {
		$answers[0]['number'] = 0;
		$answers[0]['value'] = '';
		$answers[1]['number'] = 1;
		$answers[1]['value'] = '';
	}

	// Übergabe der Daten an Smarty
	$tpl->assign('publication_period', $date->datepicker(array('start', 'end')));
	$tpl->assign('question', isset($_POST['form']['question']) ? $_POST['form']['question'] : '');
	$tpl->assign('answers', $answers);
	$tpl->assign('multiple', selectEntry('multiple', '1', '0', 'checked'));
	$tpl->assign('disable', count($answers) < 10 ? false : true);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('polls/create.tpl'));
}
