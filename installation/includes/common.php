<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

if (defined('IN_ACP3') === false)
	exit;

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

// Alle Fehler ausgeben
error_reporting(E_ALL);

include ACP3_ROOT . 'includes/globals.php';

require ACP3_ROOT . 'includes/bootstrap.php';
ACP3_CMS::defineDirConstants();
ACP3_CMS::includeAutoLoader();

$php_self = dirname(PHP_SELF);
define('INSTALLER_DIR', $php_self !== '/' ? $php_self . '/' : '/');
define('INSTALLER_INCLUDES_DIR', ACP3_ROOT . 'installation/includes/');

require INSTALLER_INCLUDES_DIR . 'functions.php';

// Smarty einbinden
include LIBRARIES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->compile_id = 'installation';
$tpl->setTemplateDir(ACP3_ROOT . 'installation/design/')
	->addPluginsDir(INSTALLER_INCLUDES_DIR . 'smarty_functions/')
	->setCompileDir(CACHE_DIR . 'tpl_compiled/')
	->setCacheDir(CACHE_DIR . 'tpl_cached/');
if (is_writable($tpl->getCompileDir()) === false || is_writable($tpl->getCacheDir()) === false) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

if (defined('IN_UPDATER') === false) {
	define('CONFIG_VERSION', '4.0 SVN');
	define('CONFIG_SEO_ALIASES', false);
	define('CONFIG_SEO_MOD_REWRITE', false);

	$pages = array(
		array(
			'file' => 'welcome',
			'selected' => '',
		),
		array(
			'file' => 'licence',
			'selected' => '',
		),
		array(
			'file' => 'requirements',
			'selected' => '',
		),
		array(
			'file' => 'configuration',
			'selected' => '',
		),
	);
	$uri = new ACP3_URI('install', 'welcome');
} else {
	ACP3_CMS::startupChecks();
	ACP3_CMS::initializeDoctrineDBAL();

	// Alte Versionen auf den Legacy Updater umleiten
	if (defined('CONFIG_LANG') === true) {
		$pages = array(
			array(
				'file' => 'db_update_legacy',
				'selected' => '',
			),
		);

		$uri = new ACP3_URI('install', 'db_update_legacy');
	} else {
		ACP3_Config::getSystemSettings();

		$pages = array(
			array(
				'file' => 'db_update',
				'selected' => '',
			),
		);

		$uri = new ACP3_URI('install', 'db_update');
	}

	ACP3_Cache::purge();
}

if (!empty($_POST['lang'])) {
	setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
	$uri->redirect($uri->mod . '/' . $uri->file);
}

if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
	is_file(ACP3_ROOT . 'installation/languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true) {
	define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
} else {
	define('LANG', ACP3_Lang::parseAcceptLanguage());
}
$tpl->assign('LANGUAGES', languagesDropdown(LANG));

$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('INSTALLER_DIR', INSTALLER_DIR);
$tpl->assign('ROOT_DIR', substr(INSTALLER_DIR, 0, -13));
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES));

$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'installation/languages/' . LANG . '.xml', '/language/info');
$tpl->assign('LANG_DIRECTION', isset($lang_info['direction']) ? $lang_info['direction'] : 'ltr');
$tpl->assign('LANG', LANG);

require INSTALLER_INCLUDES_DIR . 'classes/InstallerLang.class.php';
$lang = new ACP3_InstallerLang(LANG);

// Überprüfen, ob die angeforderte Seite überhaupt existiert
$i = 0;
$is_file = false;
foreach ($pages as $row) {
	if ($row['file'] === $uri->file) {
		$pages[$i]['selected'] = ' class="active"';
		$tpl->assign('TITLE', $lang->t($row['file']));
		$is_file = true;
		break;
	}
	++$i;
}
$tpl->assign('PAGES', $pages);

if ($is_file === true) {
	$content = '';
	include ACP3_ROOT . 'installation/pages/' . $uri->file . '.php';
	$tpl->assign('CONTENT', $content);
} else {
	$tpl->assign('TITLE', $lang->t('error_404'));
	$tpl->assign('CONTENT', $tpl->fetch('pages/404.tpl'));
}