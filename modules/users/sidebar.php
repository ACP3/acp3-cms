<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

if ($auth->isUser()) {
	// Module einholen
	$mod_list = modules::modulesList();
	$nav_mods = array();
	$access_system = false;

	foreach ($mod_list as $name => $info) {
		$dir = $info['dir'];
		if (modules::check($dir, 'adm_list') == 1 && $dir != 'acp') {
			if ($dir == 'system') {
				$access_system = true;
			} elseif ($dir == 'home') {
				$tpl->assign('access_home', true);
			} else {
				$nav_mods[$name]['name'] = $name;
				$nav_mods[$name]['dir'] = $dir;
			}
		}
	}
	if (!empty($nav_mods)) {
		$tpl->assign('nav_mods', $nav_mods);
	}

	if ($access_system) {
		$i = 0;
		if (modules::check('system', 'configuration') == 1) {
			$nav_system[$i]['page'] = 'configuration';
			$nav_system[$i]['name'] = $lang->t('system', 'configuration');
		}
		if (modules::check('system', 'server_config') == 1) {
			$i++;
			$nav_system[$i]['page'] = 'server_config';
			$nav_system[$i]['name'] = $lang->t('system', 'server_config');
		}
		if (modules::check('system', 'extensions') == 1) {
			$i++;
			$nav_system[$i]['page'] = 'extensions';
			$nav_system[$i]['name'] = $lang->t('system', 'extensions');
		}
		if (modules::check('system', 'maintenance') == 1) {
			$i++;
			$nav_system[$i]['page'] = 'maintenance';
			$nav_system[$i]['name'] = $lang->t('system', 'maintenance');
		}
		$tpl->assign('nav_system', $nav_system);
	}

	$tpl->display('users/sidebar_user_menu.html');
} else {
	if (defined('IN_ADM'))
		$tpl->assign('uri', uri('acp/users/login'));
	elseif (defined('IN_ACP3'))
		$tpl->assign('uri', uri('users/login'));

	$tpl->assign('redirect_uri', isset($_POST['form']['redirect_uri']) ? $_POST['form']['redirect_uri'] : base64_encode(htmlentities($_SERVER['REQUEST_URI'])));

	$tpl->display('users/sidebar_login.html');
}
