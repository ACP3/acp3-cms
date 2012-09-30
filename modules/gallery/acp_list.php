<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$galleries = ACP3_CMS::$db2->fetchAll('SELECT g.id, g.start, g.end, g.name, COUNT(p.gallery_id) AS pictures FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$can_delete = ACP3_Modules::check('gallery', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['period'] = ACP3_CMS::$date->period($galleries[$i]['start'], $galleries[$i]['end']);
	}
	ACP3_CMS::$view->assign('galleries', $galleries);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}
ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('gallery/acp_list.tpl'));
