<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'menu_items/functions.php';

$pages_list = menuItemsList();

if (count($pages_list) > 0) {
	$tpl->assign('pages_list', $pages_list);
}
$content = modules::fetchTemplate('menu_items/adm_list.tpl');
