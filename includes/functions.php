<?php
function getSettings($querySettings) {
	$querySettings = "SELECT * from settings";
	return $querySettings;
}

function highlightKeyword($sources) {
	$HighlightPatterns = array(
		'<strong>' => "/(\xEE\x80\x80)/uix",
		'</strong>' => "/(\xEE\x80\x81)/uix");
	$Formatted = preg_replace(array_values($HighlightPatterns), array_keys($HighlightPatterns), $sources);
return $Formatted;
}
function fsize($bytes) { #Determine the size of the file, and print a human readable value
   if ($bytes < 1024) return $bytes.' B';
   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KiB';
   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MiB';
   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GiB';
   else return round($bytes / 1099511627776, 2).' TiB';
}
function getUrl($url) {
	if(@function_exists('curl_init')) {
		$cookie = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT	, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
		curl_setopt($ch, CURLOPT_MAXREDIRS, 15);			
		$site = curl_exec($ch);
		curl_close($ch);
		} else {
		global $site;
		$site = file_get_contents($url);
	}
	return $site;
}
function format_time($t,$f=':') // t = seconds, f = separator 
{
  return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
}
?>