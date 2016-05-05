<?php
error_reporting(0);
#error_reporting(E_ALL ^ E_NOTICE);

$conf = $TMPL = array();
$conf['host'] = 'ap-cdbr-azure-southeast-b.cloudapp.net';
$conf['user'] = 'b60b48d375f3f8';
$conf['pass'] = '519d3cdd';
$conf['name'] = 'caritahu';
$conf['url'] = 'http://caritahu.azurewebsites.net'; 

$action = array('admin'			=> 'admin',
				'preferences'	=> 'preferences',
				
				// Start the results
				'web'			=> 'web',
				'images'		=> 'images',
				'videos'		=> 'videos',
				'news'			=> 'news',
				
				// Start the ToS pages
				'privacy'       => 'page',
				'disclaimer'	=> 'page',
				'contact'       => 'page',
				'tos'			=> 'page'
				);
?>