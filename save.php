<?php
	require_once("secure/secure.php");
	require_once('fileXMLreader.php');
	
	echo $_POST["refURL"];
	$urlarray = parse_url($_POST['refURL']);
		
	$exp = explode("/", $urlarray['path']);
	array_pop($exp);
	$urlRef = implode("/", $exp);
		
	if(testEdit()){
		$folderPath = $_POST["folder"];
		$files = new fileXMLReader($folderPath);
		$files -> setRootFolder($urlRef);
		// now instruct fileXML to update itself
		$files -> updateDescription($_POST["filename"], $_POST["descBox"]);
	} else {
		echo "You do not have permission to save on this server";	
	}
	
	
	// clear session - session will be generated on EVERY save!
	session_unset();
    session_destroy();
    session_write_close();
?>

<script language="javascript">
	self.close();
</script>