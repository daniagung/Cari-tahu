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
	
	// Image Query Options http://msdn.microsoft.com/en-us/library/dd560913
	if($_GET['e'] == 'small') {
		$extra = 'Size:small';
	} elseif ($_GET['e'] == 'medium') {
		$extra = 'Size:medium';
	} elseif ($_GET['e'] == 'large') {
		$extra = 'Size:large';
	} elseif ($_GET['e'] == 'square') {
		$extra = 'Aspect:Square';
	} elseif ($_GET['e'] == 'wide') {
		$extra = 'Aspect:Wide';
	} elseif ($_GET['e'] == 'tall') {
		$extra = 'Aspect:Tall';
	} elseif ($_GET['e'] == 'color') {
		$extra = 'Color:Color';
	} elseif ($_GET['e'] == 'monochrome') {
		$extra = 'Color:Monochrome';
	} elseif ($_GET['e'] == 'photo') {
		$extra = 'Style:Photo';
	} elseif ($_GET['e'] == 'graphics') {
		$extra = 'Style:Graphics';
	} elseif ($_GET['e'] == 'face') {
		$extra = 'Face:Face';
	} elseif ($_GET['e'] == 'portrait') {
		$extra = 'Face:Portrait';
	} elseif ($_GET['e'] == 'other') {
		$extra = 'Face:Other';
	}
	if(isset($_GET['e'])) { // If extra filter is set
		$filterActive = '&ImageFilters='.urlencode('\''.$extra.'\'');
	}
	// Query bing, get the content & decode the json	
	$per_page = 30;
	
	$request = 'https://api.datamarket.azure.com/Bing/Search/Image?$format=json&Query=%27'.str_replace(' ', '%20', $query).'%27&Adult=%27'.(($adult) ? $adult : $_COOKIE['adult']).'%27&Options=%27'.$_COOKIE['highlight'].'%27&$top='.$per_page.$filterActive;
	
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
	$skin = new skin('images/rows'); $all = '';

	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->SourceUrl.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a><br />'; 
		$TMPL['title'] = highlightKeyword($title);
		$TMPL['url'] = $value->SourceUrl.'" target="'.$_COOKIE['link'].'"';
		$TMPL['detalis'] = $value->Width.' x '.$value->Height.' &bull; '.fsize($value->FileSize);
		$TMPL['thumbnail'] = '<a href="'.$value->MediaUrl.'" target="'.$_COOKIE['link'].'"><img src="'.$value->Thumbnail->MediaUrl.'" class="img_title" title="'.$value->Title.'" width="160" height="145" /></a>';
		$TMPL['imageLink'] = $value->MediaUrl.'" target="'.$_COOKIE['link'].'"';
		
		// More from...
		$find   = 'site:';
		$more = strpos($_GET['q'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->SourceUrl);
			$TMPL['more'] = '<a href="'.$conf['url'].'/?a='.$_GET['a'].'&q='.$query.' site:'.$hostUrl['host'].'">more from '.$hostUrl['host'].'</a>';
		} else {
			$TMPL['more'] = '';
		}
		$all .= $skin->make();
	}
	
	$skin = new skin('images/sidebar'); $sidebar = '';
	$TMPL['url'] = $conf['url'].'/?a='.$_GET['a'].'&q='.$query.'&page=0&e=';
	$sidebar .= $skin->make();
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['url'] = $conf['url'];
	$TMPL['rows'] = $all;
	$TMPL['title'] = htmlentities($_GET['q'], ENT_QUOTES).' - Images - '.$resultSettings[0];
	$TMPL['sidebar'] = $sidebar;
	$TMPL['idlive'] = $jsonobj->d->__next;
	$TMPL['hide'] = (empty($jsonobj->d->__next)) ? 'style="display: none"' : '';

	$TMPL['results'] = 'results_img';

	$skin = new skin('images/content');
	return $skin->make();
}
?>