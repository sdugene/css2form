<?php
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);

	include('../src/CssManager.php');
	include('../src/Table.php');
	
	//$css = file_get_contents('http://beta.siteoffice.fr/siteoffice/web/library/css/admin.css');
	$css = '/* Bloc page */
#page-index {
    /* couleur des textes */
    color: inherit;
    /* couleur de fond */
    background-color: inherit;
}';
	echo $css;
	echo '<pre>'; $cssManager = \Css2Form\CssManager::getManager($css);
	var_dump($cssManager->getArray()); echo '</pre>';
	
	echo $cssManager->getCss();