<?php
require_once('fileXMLreader.php');
$name = $_GET["name"];
$folder = $_GET["folder"];
$type = $_GET["type"];
$files = new fileXMLReader($folder);
$files -> selectNode($name);
$image = $files -> getImageForType($files -> type );


// get current path
$URI = $_SERVER['REQUEST_URI'];
// strip off any query string
$URI = explode("?",$URI);
$URI = explode("/",$URI[0]);
array_pop($URI);
$URI = implode("/", $URI);
$url = (!empty($_SERVER['HTTPS'])) ? "https://www.".$_SERVER['SERVER_NAME'].$URI : "http://www.".$_SERVER['SERVER_NAME'].$URI;
$url.= "/";

?>

<table width="100%" height="80%">
	<tr height="20px" > 
    	<td align="center" colspan="2"> <span class="fileViewTitle"> <?php echo $name; ?> </span> </td>
       
    </tr>
    <tr> 
    	<td width="60%"> 
        <table>
        <tr > <td  align="center">
        <?php
			if ($type === fileXMLReader::IMAGE_TYPE){
				echo "<image src='".$url.$folder."/".$name."' width='80%'>" ; 
			} else {
		
        		echo "<image src='".$url.$image."'>" ; 
         	} 
         ?>
        </td>
        <td> 
        	<?php echo $files -> getHelpByType($type); 
			echo "<br>";
			// now check type and see if there is any converted items for it
			if ($type === fileXMLReader::POWERPOINT_TYPE){
					if($files -> currentNodeHasPreview()){
						// we have a powerpoint preview!
						echo "<span class='previewClick' onclick=\"";
						echo "previewFile('".$url."previewPP.php?folder=".$folder."&file=".$name."')";
						echo "\"> preview powerpoint </span>";
					}
			} // publisher and word both use PDF for previews
			if ($type === fileXMLReader::WORD_TYPE || $type === fileXMLReader::PUBLISHER_TYPE){
					if($files -> currentNodeHasPreview()){
						// we have a word preview!
						echo "<span class='previewClick' onclick=\"";
						echo "previewFile('".$url."previewWord.php?folder=".$folder."&file=".$name."')";
						echo "\"> preview  document </span>";
					}
			}
			if ($type === fileXMLReader::VIDEO_TYPE){
					if($files -> currentNodeHasPreview()){
						// we have a vide preview!
						echo "<span class='previewClick' onclick=\"";
						echo "previewFile('".$url."previewVid.php?folder=".$folder."&file=".$name."')";
						echo "\"> preview Video </span>";
					}
			}
			
			if ($type === fileXMLReader::CODE_TYPE){
					
					// always show preview
					echo "<span class='previewClick' onclick=\"";
					echo "previewFile('".$url."previewCode.php?folder=".$folder."&file=".$name."')";
					echo "\"> preview Code </span>";
					
			}
			
			if ($type === fileXMLReader::IMAGE_TYPE){
					
					// open up a gallery!
					echo "<span class='previewClick' onclick=\"";
					echo "previewFile('".$url."previewImages.php?folder=".$folder."&file=".$name."')";
					echo "\"> preview Images </span>";
					
			}
			
			?>
        </td>
        </tr>
        </table>
        </td>
         <td rowspan="3"> 
    
        	<div class="fileViewDownload rounded">
            	<a href="<?php echo $folder.'/'.$name;?>">
            	<img src="<?php echo $url;?>image/Crystal_Clear__device_floppy_unmount.png" /><br />
                </a>
                Download
            </div>
        
        </td>
    </tr>
    <tr height="80" colspan="2"> 
    	<td> <span> <?php echo $files -> desc; ?> </span>
        <span id="editableBox"><img src="<?php echo $url;?>images/edit_icon.png" onclick="allowEdit('<?php echo $files -> desc; ?>', '<?php echo $name;?>');">  </span></td>
    </tr>
</table>

<input type='button' value="close" onclick="closeDIV()">