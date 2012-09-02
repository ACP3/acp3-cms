<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
	$entries = $_POST['entries'];
elseif (ACP3_Validate::deleteEntries(ACP3_CMS::$uri->entries) === true)
	$entries = ACP3_CMS::$uri->entries;

if (!isset($entries)) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'no_entries_selected')));
} elseif (is_array($entries) === true) {
	$marked_entries = implode('|', $entries);
	ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', 'confirm_delete'), ACP3_CMS::$uri->route('acp/newsletter/delete/entries_' . $marked_entries . '/action_confirmed/'), ACP3_CMS::$uri->route('acp/newsletter/list_accounts')));
} elseif (ACP3_CMS::$uri->action === 'confirmed') {
	$marked_entries = explode('|', $entries);
	$bool = false;
	foreach ($marked_entries as $entry) {
		$bool = ACP3_CMS::$db->delete('newsletter_accounts', 'id = \'' . $entry . '\'');
	}
	setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/newsletter/list_accounts');
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
