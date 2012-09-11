
<?php
require_once('folders.php');
require_once('fileXMLreader.php');
$folders = new folderList();
// get ID and PID from query string or default to root
$id = $_GET["id"];
$pid = $_GET["pid"];


// read in folders and files
$folderPath = $folders -> findFolder($id, $pid);
$files = new fileXMLReader($folderPath);
$config = simplexml_load_file("config.xml"); 
$icrow =  $config -> iconRowCount;

// if parent ID is -1 or 0 then find the parent of ID 
// assuming ID is set!

// remove last folder from the full path
$t = explode("/",$folderPath);
array_pop($t);
array_pop($t);
$parentOfParent = $folders -> findFolderKeyAbsolute(implode("/", $t));
if (empty($parentOfParent)) $parentOfParent = -1;

// get current path
$URI = $_SERVER['REQUEST_URI'];
$URI = explode("/",$URI);
array_pop($URI);
$URI = implode("/", $URI);
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$URI : "http://".$_SERVER['SERVER_NAME'].$URI;
$url.= "/";
?>


<html>

<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<link rel="stylesheet" type="text/css" href="<?php echo $url;?>css/style.css" />
<script language="javascript" src="<?php echo $url;?>../scripts/jquery-1.5.2.min.js"> </script>
<script language="javascript" src="<?php echo $url;?>../scripts/dynamicLoading.js"> </script>


<script language="javascript">
	function openFile(name, type, folder){
		
		$("#fileViewBackgroundHide").fadeIn('slow');
		$("#fileViewPreviewBox").fadeIn('slow');
		// use ajax to load up central div
		loadPageNoCacheWithCallback("fileViewPreviewBox", "<?php echo $url;?>displayFile.php?name=" + name + "&type="+type+"&folder="+folder, callback);
	}
	
	function callback(){
		
	}
	
	function closeDIV(){
		$("#fileViewBackgroundHide").fadeOut('slow');
		$("#fileViewPreviewBox").fadeOut('slow', function(){
			$("#fileViewBackgroundHide").hide();
			$("#fileViewPreviewBox").hide();
			$("#fileViewPreviewBox").html("<?php echo $url;?><img src='images/loading.gif'>");
		});
		
		
	}
	
	function closePreview(){
		$("#fileViewFullScreenPreviewClose").fadeOut('fast');
		$("#fileViewFullScreenPreview").fadeOut('slow', function(){
			$("#fileViewFullScreenPreview").hide();
			$("#fileViewFullScreenPreviewClose").hide();
			$("#fileViewPreviewBox").fadeTo('medium', 1);
			$("#fileViewFullScreenPreview").html("<?php echo $url;?><img src='images/loading.gif'>");
		});
		
		
	}
	
	function previewFile(URL){
			$("#fileViewPreviewBox").fadeTo('medium', 0.5, function(){
				$("#fileViewFullScreenPreview").fadeIn('slow');
				loadPageNoCacheWithCallback("fileViewFullScreenPreview", URL, callback);
				$("#fileViewFullScreenPreviewClose").fadeIn('slow');
																	});
			
	}
</script>

</head>
<body>





<div id="fileViewNavigationBar">
	<a href="displayFolder.php">
	<img src="<?php echo $url;?>images/home.png">
    </a>
    <a href="displayFolder.php?id=<?php echo $pid; ?>&pid=<?php echo $parentOfParent;?>">
    <img src="<?php echo $url;?>images/back.png">
    </a>
</div>

<div id="fileViewMain"> 
	<table class='fileViewTable'>
    	<tr class='fileViewTableRow'>
<?php
	$i = 0;
	$files -> resetItterator();
	$width = round(100 / $icrow);
	$pid = $folders -> findFolderKeyAbsolute($folderPath);
	while($files -> nextNode()){
			if ($i % $icrow == 0){
				echo "</tr><tr class='fileViewTableRow'>";
			}
			echo "<td width='".$width."%'>";
			
			$image = $files -> getImageForType($files -> type );
			if ($files -> type === fileXMLReader::FOLDER_TYPE){
				$id = $folders -> findFolderKeyAbsolute($folderPath ."/".$files -> name);
				echo "<a href=>";
				echo "<img src='".$url.$image."' onclick='loadDisplayFolder(".$url."displayFolder.php?id=".$id."&pid=".$pid.");'> <br>". $files -> name . "</a>";
				
			} else {
				echo "<img src='".$url.$image."' onclick='openFile(\"".$files -> name."\",\"".$files -> type."\",\"".$folderPath ."\")'> <br>". $files -> name ;
			}
			echo "</td>";
			$i += 1;
	}
?>
	</tr>
	</table>
</div>



<div id="fileViewBackgroundHide">
&nbsp;
</div>

<div id="fileViewFullScreenPreview">
<div id="fileViewLoading"> <img src="<?php echo $url;?>images/loading.gif"> </div>
</div>

<div id="fileViewFullScreenPreviewClose" class="rounded">
 	<span onClick="closePreview()"> X </span>
</div>

<div id="fileViewPreviewBox" class="rounded">
  <div id="fileViewLoading"> <img src="<?php echo $url;?>images/loading.gif"> </div>
</div>

</body>
</html>