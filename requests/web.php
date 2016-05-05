<?php
include("../includes/config.php");
include("../includes/functions.php");
$db = @mysqli_connect($conf['host'], $conf['user'], $conf['pass'], $conf['name']);
mysqli_query($db, 'SET NAMES utf8');

if(!$db) {	
	echo "Failed to connect to MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
}

$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));

if(isset($_POST['loadmore'])) {
	
	$request = str_replace(array('\\', 'https://api.datamarket.azure.com/Data.ashx/Bing/Search/Web?Query='), array('', 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query='), $_POST['loadmore']);
	
	$process = curl_init($request);
    curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($process, CURLOPT_USERPWD,  $resultSettings[1] . ":" . $resultSettings[1]);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($process);
	curl_close($process);
	
	$jsonobj = json_decode($response);
	
	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->Url.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a> '; 
		$url = highlightKeyword($value->DisplayUrl);
		$desc = highlightKeyword($value->Description);
		
		// More from...
		$find   = 'site:';
		$more = strpos($_POST['u'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->Url);
			$moreurl = '<a href="'.$conf['url'].'/?a=web&q='.$_POST['p'].' site:'.$hostUrl['host'].'">more from '.$hostUrl['host'].'</a>';
		} else {
			$moreurl = '';
		}
		$domain = parse_url($value->Url);
		
		echo '<div class="result_c">
		<div class="results_title"><h3>'.highlightKeyword($title).'</h3></div>
		<div class="results_url">'.$url.'</div>
		<div class="results_description">'.$desc.'</div>
		<div class="results_more">'.$moreurl.'</div>
		</div>';
	}
}
if(!empty($jsonobj->d->__next)) {
echo 
	'<div class="morebox">
		<button type="button" class="btn btn-primary" id="'.$jsonobj->d->__next.'">Load More</button>
	</div>';
}
mysqli_close($db);
?>