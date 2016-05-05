<?php
function PageMain() {
	global $TMPL, $db;
	
	$title = array( 'privacy'    => 'Privacy Policy',
					'tos'		 => 'Terms of User',
					'about'		 => 'About',
					'disclaimer' => 'Disclaimer',
					'contact'    => 'Contact');
	if(!empty($_GET['a']) && isset($title[$_GET['a']])) {
		$a = $_GET['a'];
		
		$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
		
		$TMPL['title'] = "{$title[$a]} - ".$resultSettings[0]."";
		$skin = new skin("page/$a");
		return $skin->make();
	} else {
		local_redirect('/');
	}
}
?>