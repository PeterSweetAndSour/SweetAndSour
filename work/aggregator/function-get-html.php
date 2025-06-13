<?php
function get_HTML($url, $origin_header) {
	$ch = curl_init( $url );
	curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__)."\cacert.pem");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Android 4.4; Tablet; rv:41.0) Gecko/41.0 Firefox/41.0");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$headers = [
		"Accept: */*",
		"Accept-Encoding: keep-alive",
		"Accept-Language: en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7,vi;q=0.6",
		"Content-Type: application/x-www-form-urlencoded",
		"Origin: " . $origin_header
	];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$html_content = curl_exec( $ch );

	// check for errors
	if ($html_content === false) {
		// handle the error
		$error = curl_error($ch);
		echo "cURL error: " . $error;
		return false;
	}

	// close cURL session
	curl_close($ch);

	$html = str_get_html($html_content);
	return $html;
}
?>