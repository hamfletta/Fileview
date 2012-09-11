<?php
// TODO
// sort cahce out
// store create date
// force update if out of date.
// sort out file types!
class fileXMLReader{
	private $xml = false;
	private $xmlValid = false;
	private $folder = "";
	// constants
	const FOLDER_TYPE = "folder";
	const PDF_TYPE = "pdf";
	const IMAGE_TYPE = "image";
	const VIDEO_TYPE = "video";
	const POWERPOINT_TYPE = "pp";
	const WORD_TYPE = "word";
	const EXCEL_TYPE = "excel";
	const PUBLISHER_TYPE = "pub";
	const WEBPAGE_TYPE = "webpage";
	const CODE_TYPE = "code";
	const SWF_TYPE = "flash";
	const UNKNOWN_TYPE = "unknown";
	// TODO mime type mappings!
	
	// TODO map type to image!
	private $imageMap  = array(
		 'folder' => 'images/folder.png',
		 'pdf' => 'images/pdf.png',
		 'image' => 'images/image.png',
		 'flash' => 'images/webplus.png',
		 'excel' => 'images/excel.png',
		 'video' => 'images/video.png',
		 'code' => 'images/code.png',
		 'pp' => 'images/powerpoint.png',
		 'webpage' => 'images/webplus.png',
		 'word' => 'images/word.png',
		 'pub' => 'images/publisher.png',
		 'unknown' => 'images/default.png',
		 'file' => 'images/default.png'
	);
	
	private $exclusionList = array();
	
	private $helpMap  = array(
		 'folder' => 'na',
		 'pdf' => 'You can preview the PDF file by clicking on the preview button or download by right clicking and selecting save as.',
		 'image' => 'Click on the preview button to see the image in full. You may need to right click to save the image or copy.',
		 'excel' => 'You will need microsoft Excel installed or a open source equivilent. ',
		 'video' => 'You can download the video file and watch it on your local machine by clicking on the download button. If you see a preview button then you can click on it to veiw the video in your web browser. NOTE - This may not work on mobile devices and is not available for all videos.',
		 'pp' => 'To view this power point file you will need to download it. You will need Microsoft powerpoint or a open source equivilent.',
		 'word' => 'Word documents require microsoft word or a open source equivilent to open.',
		 'pub' => 'Publisher documents require microsoft publisher to be installed on your computer in order to open them.',
		 'webpage' => 'Left click on the download button to view the page. Right click and select save to download the HTML code. It will not download dependant files such as style sheets or images. These must be downloaded seperatly. ' ,
		 'code' => 'You should download the code sample and open it. You can also preview the code in the browser window by clicking preview. ',
		 'flash' => 'If you click on download you will be able to watch / use this flash file. If you want to save it you must right click and choose save as. ',
		 'unknown' => 'images/default.png',
		 'file' => 'images/default.png'
	);
	
	private $fileTypeMap = array(
		"pdf" => "pdf",
		"doc" => "word",
		"docx" => "word",
		"gif" => "image",
		"jpg" => "image",
		"png" => "image",
		"bmp" => "image",
		"ppt" => "pp",
		"pptx" => "pp",
		"xls" => "excel",
		"py" => "code",
		"js" => "code",
		"css" => "code",
		"java" => "code",
		"c" => "code",
		"html" => "webpage",
		"htm" => "webpage",
		"sql" => "code",
		"wmv" => "video",
		"mp4" => "video",
		"mov" => "video",
		"ogg" => "video",
		"webm" => "video",
		"pub" => "pub",
		"avi" => "video",
		"swf" => "flash",
		"mpg" => "video",
		"xlsx" => "excel"
	);
	// used for itterating
	public $name = "";
	public $type = "";
	public $desc = "";
	private $itterator = 0;
	
	private $rootFolder = "";
	
	function __construct($folderName) {
		$urlarray = parse_url($_SERVER['HTTP_REFERER']);
		
		$exp = explode("/", $urlarray['path']);
		array_pop($exp);
		$this -> rootFolder = implode("/", $exp);
		
		$this -> folder = $folderName;
		$this -> readXML($folderName);
		if (!$this -> xmlIsValid($folderName)){
			$this -> generateXML($folderName);	
		} 
	}
	
	function setRootFolder($url){
		$this -> rootFolder = $url;
	}
	// xmlIsValid 
	// param 1 - Name of the current folder
	// useage - This will check to see if the data is stale OR if the file does not exist!
	
	
	function xmlIsValid(){
		if (!$this -> xml) return false;
		// TODO check time stamp
		return true;
	}
	
	// readXML
	// param 1 - folder to find XML
	// useage - will look for a files.xml file and load if present
	function readXML($folderName){
		if(file_exists ($_SERVER['DOCUMENT_ROOT'].$folderName."/files.xml")){
			$this -> xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].$folderName."/files.xml");
		} else {
			$this -> xml = false;
		}
		
	}
	
	// loadexclusions
	
	function loadExclusions(){
		if(file_exists ($_SERVER['DOCUMENT_ROOT'].$this -> rootFolder."/config.xml")){
			$config = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].$this -> rootFolder."/config.xml");
			$nodes = $config->xpath('//config/exclusionList/excludeFile');

			foreach($nodes as $file){	
				$this -> exclusionList[(string) $file[0]] = (string)$file[0];
			}
		}
	}
	
	function testIfExluded($file){
		return array_key_exists($file, $this ->exclusionList );
	}
	
	// generateXML
	// param - folder name 
	// useage - generate XML for the current folder. 
	function generateXML($folderName){
		
		$this -> loadExclusions();
		// todo compare against existing XML
		$results = scandir($_SERVER['DOCUMENT_ROOT'].$folderName);
		$files = new SimpleXMLElement("<fileList></fileList>");	
		
		foreach ($results as $result) {
			
			if ($this -> testIfExluded($result)) continue;
			// see if node exists already
			if($this -> xml !== false){
				
				$match = $this -> xml->xpath("//file[name='$result']");
				if( count($match) >0){
					
					$this -> copyXMLNode($match[0],$files );
					continue;
				}
			} 
			if (is_dir($_SERVER['DOCUMENT_ROOT'].$folderName . '/' . $result)) {
				$this -> generateNode($result, fileXMLReader::FOLDER_TYPE, $files);
			} else {
				// get file type.
				$ft =  end(explode(".",$result));
				$ft = strtolower ($ft);
				if (array_key_exists ($ft, $this -> fileTypeMap)){
					$ft = $this -> fileTypeMap[$ft];
					
				} else { 
					$ft = fileXMLReader :: UNKNOWN_TYPE;
				}
				$this -> generateNode($result, $ft, $files);
			}
			
					 
			
			
		}
		$fh = fopen($_SERVER['DOCUMENT_ROOT'].$folderName."/files.xml", 'w');
		fwrite($fh, $files -> asXML());
		fclose($fh);
		// load newly created XML
		$this -> readXML($folderName);
	}
	
	
	function generateNode($name, $type, $parent){
			$current = $parent -> addChild("file");
			$current -> addChild("name", $name);
			$current -> addChild("type", $type);
			$current -> addChild("desc", "");
	}
	
	function copyXMLNode($node, $parent){
			$current = $parent -> addChild("file");
			$current -> addChild("name", $node -> name);
			$current -> addChild("type", $node -> type);
			$current -> addChild("desc", $node -> desc);
	}
	
	function resetItterator(){
		$this -> itterator = 0;
	}
	
	function selectNode($name){
		$match = $this -> xml->xpath("//file[name='$name']");
		if( count($match) >0){
			$this -> name = (string)$match[0] -> name;
			$this -> desc = (string)$match[0] -> desc;
			$this -> type = (string)$match[0] -> type;
		}
	}
	
	function nextNode(){
		
		$this -> itterator += 1;
		
		if ($this -> itterator > count($this -> xml) ){
			return false;
		} else {
			$current =  $this -> xml->xpath('/fileList/file['.$this -> itterator.']');
		
			$this -> name = (string)$current[0] -> name;
			$this -> desc = (string)$current[0] -> desc;
			$this -> type = (string)$current[0] -> type;
			return true;	
		}
	}
	
	function getImageForType($type){
		return $this -> imageMap[$type];
	}
	
	function getHelpByType($type){
		return $this -> helpMap[$type];
	}
	
	
	function file_ext_strip($filename){
		return preg_replace('/\.[^.]*$/', '', $filename);
	}

	// returns true if a file exists in converted folder
	function currentNodeHasPreview(){
		// check converted folder exists
		if (!file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> folder."/converted")) return false;
		// now check type and then check preview
		$filenamenoext = $this -> file_ext_strip($this -> name);
			
		if($this -> type === fileXMLReader::POWERPOINT_TYPE){
			// check to see is name_html folder exists!
			
			if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> folder."/converted/".$filenamenoext."_html")){
				return true;									   
			}
		}
		
		if($this -> type === fileXMLReader::VIDEO_TYPE){
			// check to see if flv video exists folder exists!
			// FLV is the fallback version for most browsers. If this exists then 
			// the video should play! (unless it is IOS or mobile)
			if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> folder."/converted/".$filenamenoext.".mp4")){
				return true;									   
			}
		}
		if($this -> type === fileXMLReader::WORD_TYPE){
			// check to see if flv video exists folder exists!
			// FLV is the fallback version for most browsers. If this exists then 
			// the video should play! (unless it is IOS or mobile)
			if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> folder."/converted/".$filenamenoext.".pdf")){
				return true;									   
			}
		}
		
		if($this -> type === fileXMLReader::PUBLISHER_TYPE){
			// check to see if flv video exists folder exists!
			// FLV is the fallback version for most browsers. If this exists then 
			// the video should play! (unless it is IOS or mobile)
			if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> folder."/converted/".$filenamenoext.".pdf")){
				return true;									   
			}
		}
		
		return false;
	}
	
	function updateDescription($name, $desc){
		$match = $this -> xml->xpath("//file[name='$name']");
	
		if( count($match) >0){
			$match[0] -> desc = $desc;
			$this -> generateXML($this -> folder);
		}
		
	}
}

?>