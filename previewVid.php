<?php
	function file_ext_strip($filename){
		return preg_replace('/\.[^.]*$/', '', $filename);
	}
	
	$folder = $_GET["folder"];
	$file = $_GET["file"];
	$filename = file_ext_strip($file);
	
	$fullURL = $folder."/converted/".$filename;
	
		$URI = $_SERVER['REQUEST_URI'];
// strip off any query string
$URI = explode("?",$URI);
$URI = explode("/",$URI[0]);
array_pop($URI);
$URI = implode("/", $URI);
$url = (!empty($_SERVER['HTTPS'])) ? "https://www.".$_SERVER['SERVER_NAME'].$URI : "http://www.".$_SERVER['SERVER_NAME'].$URI;
$url.= "/";

?>

<IFRAME width="100%" height="100%" frameborder=0 src="<?php echo $url."/playVid.php?file=".$fullURL; ?> ">
	
</IFRAME>

