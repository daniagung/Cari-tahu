<?php
function PageMain() {
	global $TMPL, $db, $conf;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
		
	$TMPL_old = $TMPL; $TMPL = array();
	$skin = new skin('welcome/rows'); $all = '';

	$TMPL = $TMPL_old; unset($TMPL_old);
	$TMPL['rows'] = $all;
	
	$TMPL['ad1'] = $resultSettings[2];
	$TMPL['ad2'] = $resultSettings[3];
	$TMPL['ad3'] = $resultSettings[4];
	
	$TMPL['url'] = $conf['url'];
	$TMPL['title'] = $resultSettings[0];
	
	$skin = new skin('welcome/content');
	return $skin->make();
}
?>