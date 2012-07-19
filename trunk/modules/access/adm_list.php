<?php
/**
 ** Access Control List
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$roles = ACP3_ACL::getAllRoles();
$c_roles = count($roles);

if ($c_roles > 0) {
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
	}
	$tpl->assign('roles', $roles);
	$tpl->assign('can_delete', ACP3_Modules::check('access', 'delete'));
	$tpl->assign('can_order', ACP3_Modules::check('access', 'order'));	
}
ACP3_View::setContent(ACP3_View::fetchTemplate('access/adm_list.tpl'));
