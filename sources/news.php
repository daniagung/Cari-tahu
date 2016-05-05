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
	
	// News Query Options
	if($_GET['e'] == 'business') {
		$extra = '&News.Category=rt_Business';
	} elseif ($_GET['e'] == 'entertainment') {
		$extra = '&News.Category=rt_Entertainment ';
	} elseif ($_GET['e'] == 'health') {
		$extra = '&News.Category=rt_Health';
	} elseif ($_GET['e'] == 'politics') {
		$extra = '&News.Category=rt_rt_Politics';
	} elseif ($_GET['e'] == 'sports') {
		$extra = '&News.Category=rt_Sports';
	} elseif ($_GET['e'] == 'us') {
		$extra = '&News.Category=rt_US';
	} elseif ($_GET['e'] == 'world') {
		$extra = '&News.Category=rt_World';
	} elseif ($_GET['e'] == 'science') {
		$extra = '&News.Category=rt_ScienceAndTechnology';
	} elseif($_GET['e'] == 'date') {
		$extra = '&News.SortBy=Date';
	} elseif($_GET['e'] == 'relevance') {
		$extra = '&News.SortBy=Relevance';
	}
					
	// Query bing, get the content & decode the json	
	$page = $_GET['page'];
	if(!isset($page) || empty($page) || $page < 0) {
		$page = 0;
	} elseif($page > 1000) {
		$page = 1000;
	}
	$per_page = 10;
	
	$request = 'https://api.datamarket.azure.com/Bing/Search/News?$format=json&Query=%27'.str_replace(' ', '%20', $query).'%27&Adult=%27'.(($adult) ? $adult : $_COOKIE['adult']).'%27&Options=%27'.$_COOKIE['highlight'].'%27&$top='.$per_page.$filterActive;
	
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
	$skin = new skin('news/rows'); $all = '';

	foreach($jsonobj->d->results as $value) {
		$title = '<a href="'.$value->Url.'" target="'.$_COOKIE['link'].'">'.$value->Title.'</a><br />'; 
		$TMPL['title'] = highlightKeyword($title);
		$TMPL['url'] = highlightKeyword($value->Source);
		$TMPL['desc'] = highlightKeyword($value->Description);
		$TMPL['date'] = str_replace(array('T', 'Z'), array(' ',''), $value->Date);
		
		// More from...
		$find   = 'site:';
		$more = strpos($_GET['q'], $find);
		if($more === false) {
			$hostUrl = parse_url($value->Url);
			$TMPL['more'] = '<a href="'.$conf['url'].'/?a='.$_GET['a'].'&q='.$query.' site:'.$hostUrl['host'].'">more from '.$hostUrl['host'].'</a>';
		}
		$all .= $skin->make();
	}
	
	$skin = new skin('news/sidebar'); $sidebar = '';
	$TMPL['url'] = $conf['url'].'/?a='.$_GET['a'].'&q='.$query.'&page=0&e=';
	$sidebar .= $skin->make();
	
	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['url'] = $conf['url'];
	$TMPL['rows'] = $all;
	$TMPL['title'] = htmlentities($_GET['q'], ENT_QUOTES).' - News - '.$resultSettings[0];
	$TMPL['sidebar'] = $sidebar;
	$TMPL['idlive'] = $jsonobj->d->__next;
	$TMPL['hide'] = (empty($jsonobj->d->__next)) ? 'style="display: none"' : '';
	
	$TMPL['results'] = 'results_web';

	$skin = new skin('news/content');
	return $skin->make();
}
?>