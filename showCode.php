<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php 

$fileName = $_GET["file"];
$fileType = end(explode(".", $fileName));

// need to open code file and read it in

$fh = fopen($_SERVER['DOCUMENT_ROOT'].$fileName, 'r');
$fileReadIn .= fread($fh, filesize($_SERVER['DOCUMENT_ROOT'].$fileName));
fclose($fh);
// replace /n for <br>
$fileReadIn = htmlentities($fileReadIn, ENT_QUOTES);
$fileReadIn = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $fileReadIn);
$fileReadIn = str_replace(" ", "&nbsp;", $fileReadIn);
//$fileReadIn = str_replace("\n", "</td></tr><tr class='codeTable'><td>", $fileReadIn);

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="scripts/shCore.js"></script>
<script type="text/javascript" src="scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="scripts/shBrushCpp.js"></script>
<script type="text/javascript" src="scripts/shBrushSql.js"></script>
<script type="text/javascript" src="scripts/shBrushPython.js"></script>
<link href="scripts/styles/shCore.css" rel="stylesheet" type="text/css" />
<link href="scripts/styles/shThemeDefault.css" rel="stylesheet" type="text/css" />
</head>

<body>



<pre class="brush: <?php echo $fileType;?>">
<?php echo $fileReadIn; ?>
</pre>




<script type="text/javascript">
     SyntaxHighlighter.all()
</script>
</body>
</html>