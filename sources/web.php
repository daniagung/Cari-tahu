<?php
function PageMain() {
	global $TMPL, $db, $conf, $adult;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	// Put the "a" into a template as default option when user doesn't select the search type 
	$TMPL['search'] = $_GET['a'];
	$TMPL['query'] = htmlspecialchars($_GET['q']);
	
	$query = $_GET['q'];
	if(empty($_GET['q'])) {
		header('Location: '.$conf['url']);
	}
	
	if($_COOKIE['highlight'] !== 'EnableHighlighting') {
		$_COOKIE['highlight'] = '';
	}
	
	// Image Query Options http://msdn.microsoft.com/en-us/library/dd560913
	if($_GET['e'] == 'doc') {
		$extra = 'DOC';
	} elseif ($_GET['e'] == 'dwf') {
		$extra = 'DWF';
	} elseif ($_GET['e'] == 'feed') {
		$extra = 'FEED';
	} elseif ($_GET['e'] == 'htm') {
		$extra = 'HTM';
	} elseif ($_GET['e'] == 'html') {
		$extra = 'HTML';
	} elseif ($_GET['e'] == 'pdf') {
		$extra = 'PDF';
	} elseif ($_GET['e'] == 'ppt') {
		$extra = 'PPT';
	} elseif ($_GET['e'] == 'rtf') {
		$extra = 'RTF';
	} elseif ($_GET['e'] == 'text') {
		$extra = 'TEXT';
	} elseif ($_GET['e'] == 'txt') {
		$extra = 'TXT';
	} elseif ($_GET['e'] == 'xls') {
		$extra = 'XLS';
	}
	
	if(isset($_GET['e'])) { // If extra filter is set
		$filterActive = '&WebFileType=%27'.$extra.'%27';
	}
	
	// Query bing, get the content & decode the json
	$per_page = 10;
	            
	$request = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27'.str_replace(' ', '%20', $query).'%27&Adult=%27'.(($adult) ? $adult : $_COOKIE['adult']).'%27&Options=%27'.$_COOKIE['highlight'].'%27&$top='.$per_page.$filterActive;

	$process = curl_init($request);
	curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($process, CURLOPT_USERPWD,  $resultSettings[1] . ":" . $resultSettings[1]);
	curl_setopt($process, CURLOPT_TIMEOUT, 30);
	curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($process, CURLOPT_SSL_VERIFYPEER, FALSE);
	$response = curl_exec($process);
	
	$jsonobj = json_decode($response);

	$TMPL['resNumber'] = 'Showing results for <strong>'.strip_tags($query).'</strong>';
	
	$TMPL_old = $TMPL; $TMPL = array();
	
	// Start the Results template
	$skin = new skin('web/rows'); $all = '';

	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->Url.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a> '; 
		$TMPL['title'] = highlightKeyword($title);
		$TMPL['url'] = highlightKeyword($value->DisplayUrl);
		$TMPL['desc'] = highlightKeyword($value->Description);
		
		// More from...
		$find   = 'site:';
		$more = strpos($_GET['q'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->Url);
			$TMPL['more'] = '<a href="'.$conf['url'].'/?a='.$_GET['a'].'&q='.$query.' site:'.$hostUrl['host'].'">more from '.$hostUrl['host'].'</a>';
		}
		$domain = parse_url($value->Url);
		$TMPL['url_d'] = 'http://'.$domain['host'];
		$all .= $skin->make();
	}
	
	$skin = new skin('web/sidebar'); $sidebar = '';
	$TMPL['url'] = $conf['url'].'/?a='.$_GET['a'].'&q='.$query.'&page=0&e=';
	$sidebar .= $skin->make();
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['url'] = $conf['url'];
	$TMPL['rows'] = $all;
	$TMPL['sidebar'] = $sidebar;
	$TMPL['title'] = htmlentities($_GET['q'], ENT_QUOTES).' - Web - '.$resultSettings[0];
	$TMPL['idlive'] = $jsonobj->d->__next;
	$TMPL['hide'] = (empty($jsonobj->d->__next)) ? 'style="display: none"' : '';
	
	$skin = new skin('web/content');
	return $skin->make();
}
?>