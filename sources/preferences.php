<?php
function PageMain() {
	global $TMPL, $db, $conf;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	// Set the cookie life time
	$time = time()+3600*24*30;
	$exp_time = time()-3600*24*30;
	
	// Set the Cookie for Adult filter
	if(isset($_POST['adult']) || isset($_POST['highlight']) || isset($_POST['link'])) {
		setcookie('adult', $_POST['adult'], $time);
		setcookie('highlight', $_POST['highlight'], $time);
		setcookie('link', $_POST['link'], $time);
		header('Location: '.$conf['url'].'/index.php?a=preferences');
	}
	
	// Check the radio button with the current option
	if($_COOKIE['adult'] == 'Off') {
		$TMPL['off'] = 'checked';
	} elseif ($_COOKIE['adult'] == 'Moderate') {
		$TMPL['moderate'] = 'checked';
	} else {
		$TMPL['strict'] = 'checked';
	}
	if($_COOKIE['highlight'] == 'EnableHighlighting') {
		$TMPL['highOn'] = 'checked';
	} else {
		$TMPL['highOff'] = 'checked';
	}
	if($_COOKIE['link'] == '_blank') {
		$TMPL['linkOn'] = 'checked';
	} else {
		$TMPL['linkOff'] = 'checked';
	}
	$TMPL['url'] = $conf['url'];
	$TMPL['title'] = $resultSettings[0];

	$skin = new skin('preferences/content');
	return $skin->make();
}
?>