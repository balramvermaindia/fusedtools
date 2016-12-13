<?php 
function httpGet($url)
{
	$ch = curl_init();  
 
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
 
	$output=curl_exec($ch);
	$info=curl_getinfo($ch);
	curl_close($ch);
	if($info['http_code']==200)
	{
		return "done";
	}
	else
	{
		return "error";
	}
}
 
//echo httpGet("http://localhost/fusedtools/public/refresh_token");
echo httpGet("http://fusedtools.com/public/refresh_token");


 ?>
