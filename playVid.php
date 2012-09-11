<?php
	
	$file = $_GET['file'];
	$URI = $_SERVER['REQUEST_URI'];
	// strip off any query string
	$URI = explode("?",$URI);
	$URI = explode("/",$URI[0]);
	array_pop($URI);
	$URI = implode("/", $URI);
	$url = (!empty($_SERVER['HTTPS'])) ? "https://www.".$_SERVER['SERVER_NAME'].$URI : "http://www.".$_SERVER['SERVER_NAME'].$URI;
	$ser = (!empty($_SERVER['HTTPS'])) ? "https://www.".$_SERVER['SERVER_NAME'] : "http://www.".$_SERVER['SERVER_NAME'];
?>

<html>
	<head>
		<script language="javascript">
			function supports_video() {
			  return !!document.createElement('video').canPlayType;
			}

			function supports_webm_video() {
			  if (!supports_video()) { return false; }
			  var v = document.createElement("video");
			  return v.canPlayType('video/webm; codecs="vp8, vorbis"');
			}
			function supports_h264_baseline_video() {
			  if (!supports_video()) { return false; }
			  var v = document.createElement("video");
			  return v.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');
			}
			function supports_ogg_theora_video() {
			  if (!supports_video()) { return false; }
			  var v = document.createElement("video");
			  return v.canPlayType('video/ogg; codecs="theora, vorbis"');
			}

		</script>
	</head>
<body>
<div id="htmlvid">
	<video width="100%" height="100%" controls preload autoplay>
		<source src="<?php echo $ser.$file; ?>.mp4"  type="video/mp4" />
		<source src="<?php echo $ser.$file; ?>.webm"  type="video/webm" />
	</video>
</div>
<div id="flashVid">
	
	
	<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='100%' height='100%' id='single1' name='single1'>
<param name='movie' value='<?php echo $url; ?>scripts/player.swf'>
<param name='allowfullscreen' value='true'>
<param name='allowscriptaccess' value='always'>
<param name='wmode' value='transparent'>
<param name='flashvars' value='file=<?php echo $ser.$file;?>.mp4'>
<embed
type='application/x-shockwave-flash'
id='single2'
name='single2'
src='<?php echo $url; ?>scripts/player.swf'
width='600'
height='420'
bgcolor='undefined'
allowscriptaccess='always'
allowfullscreen='true'
wmode='transparent'
flashvars='file=<?php echo $ser.$file;?>.mp4'
/>
</object>

</div>
<table width="100%"><tr><td align="right"> <input type="button" value="close" onClick="parent.hideVideo();"> </td></tr> </table>
</body>

<script language="javascript">
	if (supports_h264_baseline_video() != ""){
		
		document.getElementById("flashVid").innerHTML = "";
	} else {
		
		document.getElementById("htmlvid").innerHTML = "";		
	}
</script>
</html>
