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
	
	$request = str_replace(array('\\', 'https://api.datamarket.azure.com/Data.ashx/Bing/Search/Video?Query='), array('', 'https://api.datamarket.azure.com/Bing/Search/Video?$format=json&Query='), $_POST['loadmore']);

	$process = curl_init($request);
    curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($process, CURLOPT_USERPWD,  $resultSettings[1] . ":" . $resultSettings[1]);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($process);
	curl_close($process);
	
	$jsonobj = json_decode($response);
	
	echo '<div class="thumbnails">';
	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->Title.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a><br />'; 
		highlightKeyword($title);
		$details = gmdate("H:i:s", $value->RunTime / 1000).' &bull; '.fsize($value->Thumbnail->FileSize);
		$thumbnail = '<a href="'.$value->MediaUrl.'" target="'.$_COOKIE['link'].'"><img src="'.$value->Thumbnail->MediaUrl.'" class="img_title" title="'.$value->Title.'" width="160" height="145" /></a>';
		$videoLink = $value->MediaUrl.'" target="'.$_COOKIE['link'].'"';
		
		// More from...
		$find   = 'site:';
		$more = strpos($_POST['u'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->MediaUrl);
			$moreurl = '<a href="'.$conf['url'].'/index.php?a=videos&q='.$_POST['u'].' site:'.$hostUrl['host'].'" class="btn">More</a>';
		} else {
			$moreurl = '';
		}

		echo '<li><div class="thumbnail">
		'.$thumbnail.'
		<div class="caption">
			<p><div class="img_details">'.$details.'</div></p>
			<p><a href="'.$videoLink.' class="btn btn-success">Watch</a> '.$moreurl.'</p>
		</div>
		</div></li>';
	}
	echo '</div>';
}
if(!empty($jsonobj->d->__next)) {
echo 
	'<div class="morebox">
		<button type="button" class="btn btn-primary" id="'.$jsonobj->d->__next.'">Load More</button>
	</div>';
}
mysqli_close($db);
?>