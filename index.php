<?php
require_once('./includes/config.php');
require_once('./includes/skins.php');
require_once('./includes/functions.php');

$db = @mysqli_connect($conf['host'], $conf['user'], $conf['pass'], $conf['name']);
mysqli_query($db, 'SET NAMES utf8');

if(!$db) {	
	echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
}
	
if(isset($_GET['a']) && isset($action[$_GET['a']])) {
	$page_name = $action[$_GET['a']];
	$TMPL['headerClass'] = 'header';
	$TMPL['contentClass'] = 'container';
	$TMPL['footerClass'] = 'footer';
} else {
	$page_name = 'welcome';
	$TMPL['headerClass'] = 'headerIndex';
	$TMPL['contentClass'] = 'contentIndex';
	$TMPL['footerClass'] = 'footerIndex';
}
if($_GET['a'] == 'preferences') {
	$TMPL['header_left'] = 'User Preferences';
} elseif($_GET['a'] == 'admin' || $_GET['a'] == 'disclaimer' || $_GET['a'] == 'privacy' || $_GET['a'] == 'tos' || $_GET['a'] == 'contact') {
	$TMPL['header_left'] = ucwords($_GET['a']);
} elseif($_GET['a'] == 'web' || $_GET['a'] == 'images' || $_GET['a'] == 'videos' || $_GET['a'] == 'news') {
	$TMPL['header_left'] = ucwords($_GET['a']).' Results';
}

$_GET['q'] = htmlspecialchars($_GET['q']);

if($_GET['a'] == 'web') {
	$TMPL['activeWeb'] = " active";
} elseif($_GET['a'] == 'images') {
	$TMPL['activeImages'] = " active";
} elseif($_GET['a'] == 'videos') {
	$TMPL['activeVideos'] = " active";
} elseif($_GET['a'] == 'news') {
	$TMPL['activeNews'] = " active";
}
if($_GET['a'] == 'web' || $_GET['a'] == 'images' || $_GET['a'] == 'videos' || $_GET['a'] == 'news') {
	$TMPL['urlWeb'] = $conf['url'].'/index.php?a=web&q='.$_GET['q'];
	$TMPL['urlImages'] = $conf['url'].'/index.php?a=images&q='.$_GET['q'];
	$TMPL['urlVideos'] = $conf['url'].'/index.php?a=videos&q='.$_GET['q'];
	$TMPL['urlNews'] = $conf['url'].'/index.php?a=news&q='.$_GET['q'];
} else {
	$TMPL['urlWeb'] = $TMPL['urlImages'] = $TMPL['urlVideos'] = $TMPL['urlNews'] = "#";
}

$conf['url'] = $conf['url'];
$confMail = $conf['mail'];

// Set the cookie life time
$time = time()+86400;
$exp_time = time()-86400;
$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));

// If the user don't set the adult filter, set a default one
if(!isset($_COOKIE['adult'])) {
	setcookie('adult', 'strict', $time);
	$adult = 'strict';
}
// If the user don't set the highlight filter, set a default one
if(!isset($_COOKIE['highlight'])) {
	setcookie('highlight', 'EnableHighlighting', $time);
}
// If the user don't set the highlight filter, set a default one
if(!isset($_COOKIE['link'])) {
	setcookie('link', '_self', $time);
}
require_once("./sources/{$page_name}.php");

$TMPL['content'] = PageMain();

$TMPL['site_title'] = $resultSettings[0];

$TMPL['url'] = $conf['url'];

$skin = new skin('wrapper');
echo $skin->make();

mysqli_close($db);
?>