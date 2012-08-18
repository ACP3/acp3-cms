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

getRedirectMessage();

$newsletter = $db->select('id, date, subject, status', 'newsletter_archive', 0, 'id DESC', POS, $auth->entries);
$c_newsletter = count($newsletter);

if ($c_newsletter > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'newsletter_archive')));

	for ($i = 0; $i < $c_newsletter; ++$i) {
		$newsletter[$i]['date'] = $date->format($newsletter[$i]['date']);
		$newsletter[$i]['subject'] = $db->escape($newsletter[$i]['subject'], 3);
		$newsletter[$i]['status'] = str_replace(array('0', '1'), array($lang->t('newsletter', 'not_yet_sent'), $lang->t('newsletter', 'already_sent')), $newsletter[$i]['status']);
	}
	$tpl->assign('newsletter', $newsletter);
	$tpl->assign('can_delete', ACP3_Modules::check('newsletter', 'acp_delete'));
	$tpl->assign('can_send', ACP3_Modules::check('newsletter', 'acp_send'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('newsletter/acp_list.tpl'));
