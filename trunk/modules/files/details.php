<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$time = $date->timestamp();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (validate::isNumber($uri->id) && $db->countRows('*', 'files', 'id = \'' . $uri->id . '\'' . $period) == '1') {
	require_once ACP3_ROOT . 'modules/files/functions.php';

	$file = getFilesCache($uri->id);

	if ($uri->action == 'download') {
		$path = 'uploads/files/';
		if (is_file($path . $file[0]['file'])) {
			// Schönen Dateinamen generieren
			$ext = strrchr($file[0]['file'], '.');
			$filename = makeStringUrlSafe($file[0]['link_title']) . $ext;

			header('Content-Type: application/force-download');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length:' . filesize($path . $file[0]['file']));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($path . $file[0]['file']);
			exit;
		} elseif (preg_match('/^([a-z]+):\/\//', $file[0]['file'])) {
			redirect(0, $file[0]['file']);
		} else {
		    redirect('errors/404');
		}
	} else {
		// Brotkrümelspur
		breadcrumb::assign($lang->t('files', 'files'), uri('files'));
		breadcrumb::assign($file[0]['category_name'], uri('files/files/cat_' . $file[0]['category_id']));
		breadcrumb::assign($file[0]['link_title']);

		$settings = config::output('files');

		$file[0]['size'] = !empty($file[0]['size']) ? $file[0]['size'] : $lang->t('files', 'unknown_filesize');
		$file[0]['date'] = $date->format($file[0]['start'], $settings['dateformat']);
		$tpl->assign('file', $file[0]);

		if ($settings['comments'] == 1 && $file[0]['comments'] == 1 && modules::check('comments', 'functions') == 1) {
			require_once ACP3_ROOT . 'modules/comments/functions.php';

			$tpl->assign('comments', commentsList('files', $uri->id));
		}
		$content = $tpl->fetch('files/details.html');
	}
} else {
	redirect('errors/404');
}