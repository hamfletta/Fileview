<?php
	function file_ext_strip($filename){
		return preg_replace('/\.[^.]*$/', '', $filename);
	}
	$folder = $_GET["folder"];
	$file = $_GET["file"];
	$filename = file_ext_strip($file);
	$exploded = explode(".", $file);
	array_pop($exploded);
	$fullURL = $folder."/converted/".$filename.".pdf";
	$URI = $_SERVER['REQUEST_URI'];



?>

<IFRAME width="100%" height="100%" frameborder=0 src="<?php echo $fullURL; ?> ">
	
</IFRAME>

