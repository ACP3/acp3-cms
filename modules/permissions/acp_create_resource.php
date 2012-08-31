<?php
/**
** Access Control List
*
* @author Tino Goratsch
* @package ACP3
* @subpackage Modules
*/

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('permissions', 'acp_list_resources'), $uri->route('acp/permissions/list_resources'))
		   ->append($lang->t('permissions', 'acp_create_resource'));

if (isset($_POST['submit']) === true) {
	if (empty($_POST['modules']) || ACP3_Modules::isInstalled($_POST['modules']) === false)
		$errors['modules'] = $lang->t('permissions', 'select_module');
	if (empty($_POST['resource']) || preg_match('=/=', $_POST['resource']) || ACP3_Validate::isInternalURI($_POST['modules'] . '/' . $_POST['resource'] . '/') === false)
		$errors['resource'] = $lang->t('permissions', 'type_in_resource');
	if (empty($_POST['privileges']) || ACP3_Validate::isNumber($_POST['privileges']) === false)
		$errors['privileges'] = $lang->t('permissions', 'select_privilege');
	if (ACP3_Validate::isNumber($_POST['privileges']) && $db->countRows('*', 'acl_resources', 'id = \'' . $_POST['privileges'] . '\'') == 0)
		$errors['privileges'] = $lang->t('permissions', 'privilege_does_not_exist');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$module_id = $db->select('id', 'modules', 'name = \'' . $db->escape($_POST['modules']) . '\'');
		$insert_values = array(
			'id' => '',
			'module_id' => $module_id[0]['id'],
			'page' => $db->escape($_POST['resource']),
			'params' => '',
			'privilege_id' => $_POST['privileges'],
		);
		$bool = $db->insert('acl_resources', $insert_values);

		ACP3_ACL::setResourcesCache();

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/permissions/list_resources');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$modules = ACP3_Modules::getActiveModules();
	foreach ($modules as $row) {
		$modules[$row['name']]['selected'] = selectEntry('modules', $row['name']);
	}
	$tpl->assign('modules', $modules);

	$privileges = ACP3_ACL::getAllPrivileges();
	$c_privileges = count($privileges);
	for ($i = 0; $i < $c_privileges; ++$i) {
		$privileges[$i]['selected'] = selectEntry('privileges', $privileges[$i]['id']);
	}
	$tpl->assign('privileges', $privileges);

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('resource' => ''));

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('permissions/acp_create_resource.tpl'));
}