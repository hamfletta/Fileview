
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
$origPage = $folderPath;
$URI = explode("/",$URI);
array_pop($URI);
$URI = implode("/", $URI);


$url = (!empty($_SERVER['HTTPS'])) ? "https://www.".$_SERVER['SERVER_NAME'].$URI : "http://www.".$_SERVER['SERVER_NAME'].$URI;
$url.= "/";
?>


<html>

<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<link rel="stylesheet" type="text/css" href= />


<script language="javascript">
	var selectedFile = null;
	// added to ensure library files are available!
	var headID = document.getElementsByTagName("head")[0];         
	var newScript = document.createElement('script');
	newScript.type = 'text/javascript';
	newScript.src = "<?php echo $url;?>../scripts/jquery-1.5.2.min.js";
	headID.appendChild(newScript);

	
	// add css
	var cssNode = document.createElement('link');
	cssNode.type = 'text/css';
	cssNode.rel = 'stylesheet';
	cssNode.href = "<?php echo $url;?>css/style.css";
	cssNode.media = 'screen';
	headID.appendChild(cssNode);
	
	// add close preveiw box
	var newdiv = document.createElement('div');
	newdiv.setAttribute('id','fileViewFullScreenPreviewClose');
	newdiv.setAttribute('class','rounded');
	newdiv.innerHTML = "<span onClick='closePreview()'> X </span>";
	var bodyID = document.getElementsByTagName("body")[0];
	bodyID.appendChild(newdiv);
	
	
	function openFile(name, type, folder){
		
		$("#fileViewBackgroundHide").fadeIn('slow');
		$("#fileViewPreviewBox").fadeIn('slow');
		// use ajax to load up central div
		loadDisplayFolder("fileViewPreviewBox", "<?php echo $url;?>displayFile.php?name=" + name + "&type="+type+"&folder="+folder);
	}
	
	
	
	function closeDIV(){
		$("#fileViewBackgroundHide").fadeOut('slow');
		$("#fileViewPreviewBox").fadeOut('slow', function(){
			$("#fileViewBackgroundHide").hide();
			$("#fileViewPreviewBox").hide();
			$("#fileViewPreviewBox").html("<img src='<?php echo $url;?>images/loading.gif'>");
		});
		
		
	}
	
	function closePreview(){
		$("#fileViewFullScreenPreviewClose").fadeOut('fast');
		$("#fileViewFullScreenPreview").fadeOut('slow', function(){
			$("#fileViewFullScreenPreview").hide();
			$("#fileViewFullScreenPreviewClose").hide();
			$("#fileViewPreviewBox").fadeTo('medium', 1);
			$("#fileViewFullScreenPreview").html("<img src='<?php echo $url;?>images/loading.gif'>");
		});
		
		
	}
	
	function previewFile(URL){
			$("#fileViewPreviewBox").fadeTo('medium', 0.5, function(){
				$("#fileViewFullScreenPreview").fadeIn('slow');
				loadDisplayFolder("fileViewFullScreenPreview", URL);
				$("#fileViewFullScreenPreviewClose").fadeIn('slow');
																	});
			
	}
	
	function allowEdit(desc, name){
		selectedFile = name;
		var html = "<textarea id='descTextarea' rows=4 cols=30>"+desc+" </textarea>";
		html = html + "<img src='<?php echo $url;?>images/save_icon.gif' onclick='submitform();'>";
		
		$("#editableBox").html(html);
		
		// request key from server.
		loadDisplayFolder("resp", "<?php echo $url;?>secure/edit.php");
	}
	
	function submitform(){
		var html = "<form name = 'editForm' method='post' action='<?php echo $url;?>save.php'>";
		html = html + "<input type='hidden' name='filename' value='"+selectedFile+"'>";
		html = html + "<input type='hidden' name='folder' value='<?php echo  $folderPath;?>'>";
		var textValue = $("#descTextarea").val();
		html = html + "<input type='hidden' name='descBox' value='" + textValue +"'>";
		html = html + "<input type='hidden' name='refURL' value='<?php echo $_SERVER['HTTP_REFERER']; ?>'>";
		
		html = html + "</form>";
		
		var nw = window.open("","saveEdit");
		nw.document.write(html);
		nw.document.editForm.submit();
		
	}
	
</script>

</head>
<body>





<div id="fileViewNavigationBar">
	
	<img src="<?php echo $url;?>images/home.png"  <?php echo "onclick='loadDisplayFolder(\"fileView\",\"".$url."displayFolder.php\");'>"?>

    
    <img src="<?php echo $url;?>images/back.png" <?php echo "onclick='loadDisplayFolder(\"fileView\",\"".$url."displayFolder.php?id=".$pid."&pid=".$parentOfParent."\");'>"?>
    
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
				
				echo "<img src='".$url.$image."' onclick='loadDisplayFolder(\"fileView\",\"".$url."displayFolder.php?id=".$id."&pid=".$pid."\");'> <br>". $files -> name;
				
			} else {
				echo "<img src='".$url.$image."' onclick='openFile(\"".$files -> name."\",\"".$files -> type."\",\"".$folderPath ."\")'> <br>". $files -> name ;
			}
			echo "</td>";
			$i += 1;
	}
?>
	</tr>
	</table>
    <br> 
	
</div>



<div id="fileViewBackgroundHide">
&nbsp;
</div>

<div id="fileViewFullScreenPreview">
<div id="fileViewLoading"> <img src="<?php echo $url;?>images/loading.gif"> </div>
</div>


<div id="fileViewPreviewBox" class="rounded">
  <div id="fileViewLoading"> <img src="<?php echo $url;?>images/loading.gif"> </div>
</div>

<div id="resp"></div>

</body>
</html>