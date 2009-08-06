<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/
require_once '../config.php';

define('DESIGN_PATH', dirname(__FILE__) . '/../../designs/' . CONFIG_DESIGN . '/');

if ($_GET['g'] == 'css') {
	$modules = scandir(DESIGN_PATH);
	$styles = array();
	$styles['css'][] = DESIGN_PATH . '/layout.css';
	$styles['css'][] = DESIGN_PATH . '/jquery-ui.css';

	foreach ($modules as $module) {
		$path = DESIGN_PATH . $module . '/style.css';
		if (is_dir(DESIGN_PATH . $module) && $module != '.' && $module != '..' && is_file($path)) {
			$styles['css'][] = $path;
		}
	}
	return $styles;
} elseif ($_GET['g'] == 'js') {
	$modules = scandir(DESIGN_PATH);
	$scripts = array();
	$scripts['js'][] = DESIGN_PATH . '/jquery.js';
	$scripts['js'][] = DESIGN_PATH . '/jquery.cookie.js';
	$scripts['js'][] = DESIGN_PATH . '/jquery.ui.js';
	$scripts['js'][] = DESIGN_PATH . '/script.js';

	foreach ($modules as $module) {
		$path = DESIGN_PATH . $module . '/script.js';
		if (is_dir(DESIGN_PATH . $module) && $module != '.' && $module != '..' && is_file($path)) {
			$scripts['js'][] = $path;
		}
	}
	return $scripts;
}