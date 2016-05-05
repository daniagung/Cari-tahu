<?php
function PageMain() {
	global $TMPL, $db, $conf;
	$resultSettings = mysqli_fetch_row(mysqli_query($db, getSettings($querySettings)));
	
	$time = time()+86400;
	$exp_time = time()-86400;
	
	$TMPL['loginForm'] = '
	<form action="'.$conf['url'].'/index.php?a=admin" method="post" class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="username">Username</label>
			<div class="controls">
				<input class="input-xlarge" type="text" id="username" name="username" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="password">Password</label>
			<div class="controls">
				<input class="input-xlarge" type="password" id="password" name="password" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" name="login" class="btn">Log-In</button>
			</div>
		</div>
	</form>
	<div class="alert"><strong>Note:</strong> The password is case-sensitive.</div>';
	$TMPL['contentTitle'] = '<h3>Admin Panel</h3>';
	if(isset($_POST['login'])) { // Set cookies for Log-in.
		header("Location: ".$conf['url']."/index.php?a=admin");
		$username = $_POST['username'];
		$password = md5($_POST['password']);
		
		setcookie("adminUser", $username, $time);
		setcookie("adminPass", $password, $time);
				
		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"', 
		mysqli_real_escape_string($db, $_COOKIE['adminUser']), 
		mysqli_real_escape_string($db, $_COOKIE['adminPass'])
		);
	} elseif(isset($_COOKIE['adminUser']) && isset($_COOKIE['adminPass'])) { // If cookie admin & pass is set, check for credentials
		$query = sprintf('SELECT * from admin where username = "%s" and password ="%s"', mysqli_real_escape_string($db, $_COOKIE['adminUser']), mysqli_real_escape_string($db, $_COOKIE['adminPass']));
		if(mysqli_fetch_row(mysqli_query($db, $query))) { // If true - Logged-in
			$TMPL['contentTitle'] = '<a a href="'.$conf['url'].'/index.php?a=admin" class="btn">Welcome <strong>'.$_COOKIE['adminUser'].'</strong></a> <a href="'.$conf['url'].'/index.php?a=admin" class="btn">General</a> <a href="'.$conf['url'].'/index.php?a=admin&b=security" class="btn">Security</a> <a href="'.$conf['url'].'/index.php?a=admin&logout=1" class="btn btn-inverse">Log Out</a>';
			$TMPL['loginForm'] = '';
			
			$TMPL_old = $TMPL; $TMPL = array();
			$TMPL['url'] = $conf['url']; 
			if($_GET['b'] == 'security') { // Security Admin Tab
				$skin = new skin('admin/security'); $settings = '';
				if(isset($_POST['pwd']) && !empty($_POST['pwd'])) { // If is set post && password is not empty then save the password
					$pwd = md5($_POST['pwd']);
					$query = sprintf("UPDATE `admin` SET password = '%s' WHERE username = '%s'", mysqli_real_escape_string($db, md5($_POST['pwd'])), mysqli_real_escape_string($db, $_COOKIE['adminUser']));
					mysqli_query($db, $query);
					header("Location: ".$conf['url']."/index.php?a=admin");
				}
				$TMPL['url1'] = $conf['url'];
				$settings .= $skin->make();
			} else {
				$skin = new skin('admin/general'); $settings = '';
				// Current Values
				$TMPL['currentAPI'] = $resultSettings[1]; $TMPL['currentTitle'] = $resultSettings[0]; $TMPL['ad1'] = $resultSettings[2]; $TMPL['ad2'] = $resultSettings[3]; $TMPL['ad3'] = $resultSettings[4];

				// Updating the Values
				if(isset($_POST['title']) || isset($_POST['apikey']) || isset($_POST['ads1']) || isset($_POST['ads2']) || isset($_POST['ads3'])) {
					$query = sprintf("UPDATE `settings` SET title = '%s', app = '%s', ad1 = '%s', ad2 = '%s', ad3 = '%s'", 
									mysqli_real_escape_string($db, $_POST['title']),
									mysqli_real_escape_string($db, $_POST['apikey']),
									mysqli_real_escape_string($db, $_POST['ads1']),
									mysqli_real_escape_string($db, $_POST['ads2']),
									mysqli_real_escape_string($db, $_POST['ads3']));
					mysqli_query($db, $query);
					header("Location: ".$conf['url']."/index.php?a=admin");
				}
				$settings .= $skin->make();
			}
			$TMPL = $TMPL_old; unset($TMPL_old);
			$TMPL['settings'] = $settings;
			
			if(isset($_GET['logout']) == 1) { // Log-out (unset cookies)
				setcookie('adminUser', '', $exp_time);
				setcookie('adminPass', '', $exp_time);
				header("Location: ".$conf['url']."/index.php?a=admin");
			}
		} else { // Not Logged-in
			$TMPL['error'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Invalid username or password. Remember that the password is case-sensitive.</div>';
			unset($_COOKIE['adminUser']);
			unset($_COOKIE['adminPass']);
		}			
	}
	
	$TMPL['title'] = 'Admin - '.$resultSettings[0];

	$skin = new skin('admin/content');
	return $skin->make();
}
?>