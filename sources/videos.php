<?php
function PageMain() {
	global $TMPL, $db, $conf, $adult;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	// Put the "a" into a template as default option when user doesn't select the search type 
	$TMPL['search'] = $_GET['a'];
	$TMPL['query'] = $_GET['q'];
	
	$query = $_GET['q'];
	if(empty($_GET['q'])) {
		header('Location: '.$conf['url']);
	}
	
	if($_COOKIE['highlight'] !== 'EnableHighlighting') {
		$_COOKIE['highlight'] = '';
	}
	
	// Video Query Options
	if($_GET['e'] == 'short') {
		$extra = 'Duration:Short';
	} elseif ($_GET['e'] == 'average') {
		$extra = 'Duration:Medium';
	} elseif ($_GET['e'] == 'long') {
		$extra = 'Duration:Long';
	} elseif ($_GET['e'] == 'standard') {
		$extra = 'Aspect:Standard';
	} elseif ($_GET['e'] == 'widescreen') {
		$extra = 'Aspect:Widescreen';
	} elseif ($_GET['e'] == 'low') {
		$extra = 'Resolution:Low';
	} elseif ($_GET['e'] == 'medium') {
		$extra = 'Resolution:Medium';
	} elseif ($_GET['e'] == 'high') {
		$extra = 'Resolution:High';
	}
	if(isset($_GET['e'])) { // If extra filter is set
		$filterActive = '&VideoFilters=%27'.$extra.'%27';
	}
	
	// Query bing, get the content & decode the json	
	$page = $_GET['page'];
	if(!isset($page) || empty($page) || $page < 0) {
		$page = 0;
	} elseif($page > 1000) {
		$page = 1000;
	}
	$per_page = 30;

	$request = 'https://api.datamarket.azure.com/Bing/Search/Video?$format=json&Query=%27'.str_replace(' ', '%20', $query).'%27&Adult=%27'.(($adult) ? $adult : $_COOKIE['adult']).'%27&Options=%27'.$_COOKIE['highlight'].'%27&$top='.$per_page.$filterActive;

	$process = curl_init($request);
	curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($process, CURLOPT_USERPWD,  $resultSettings[1] . ":" . $resultSettings[1]);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($process);
	
	$jsonobj = json_decode($response);
	
	// If _GET['e'] is set, then mention it on the search page
	if(!empty($_GET['e'])) {
		$extraMsg = ' using filter <strong>'.$_GET['e'].'</strong>.';
		$extraurl = '&e='.$_GET['e'];
	}

	$TMPL['resNumber'] = 'Showing results for <strong>'.strip_tags($query).'</strong>'.$extraMsg;

	$TMPL_old = $TMPL; $TMPL = array();
	
	// Start the Results template
	$skin = new skin('videos/rows'); $all = '';
	
	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->Title.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a><br />'; 
		$TMPL['title'] = highlightKeyword($title);
		$TMPL['detalis'] = gmdate("H:i:s", $value->RunTime / 1000).' &bull; '.fsize($value->Thumbnail->FileSize);
		$TMPL['thumbnail'] = '<a href="'.$value->MediaUrl.'" target="'.$_COOKIE['link'].'"><img src="'.$value->Thumbnail->MediaUrl.'" class="img_title" title="'.$value->Title.'" width="160" height="145" /></a>';
		$TMPL['videoLink'] = $value->MediaUrl.'" target="'.$_COOKIE['link'];
		
		// More from...
		$find   = 'site:';
		$more = strpos($_GET['q'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->MediaUrl);
			$TMPL['more'] = '<a href="'.$conf['url'].'/index.php?a='.$_GET['a'].'&q='.$query.' site:'.$hostUrl['host'].'" class="btn">More</a>';
		} else {
			$TMPL['more'] = '';
		}
		$all .= $skin->make();
	}
	
	$skin = new skin('videos/sidebar'); $sidebar = '';
	$TMPL['url'] = $conf['url'].'/index.php?a='.$_GET['a'].'&q='.$query.'&page=0&e=';
	$sidebar .= $skin->make();
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['url'] = $conf['url'];
	$TMPL['rows'] = $all;
	$TMPL['title'] = htmlentities($_GET['q'], ENT_QUOTES).' - Videos - '.$resultSettings[0];
	$TMPL['sidebar'] = $sidebar;
	$TMPL['idlive'] = $jsonobj->d->__next;
	$TMPL['hide'] = (empty($jsonobj->d->__next)) ? 'style="display: none"' : '';

	$TMPL['results'] = 'results_img';

	$skin = new skin('videos/content');
	return $skin->make();
}
?>