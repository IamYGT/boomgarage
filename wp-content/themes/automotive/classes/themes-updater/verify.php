<?php
$json = @file_get_contents($_POST['url']);

if($http_response_header[0] == "HTTP/1.1 403 Forbidden"){
	echo "error";
}  else {
	echo "success";
}
?>