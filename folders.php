<?php 
//require_once('../pear/php/Cache/Lite.php');
   class folderList {
	    private $rootFolder = "";
		private $folderMap = array();
		function __construct() {
			$urlarray = parse_url($_SERVER['HTTP_REFERER']);
			$exp = explode("/", $urlarray['path']);
			array_pop($exp);
			$this -> rootFolder = implode("/", $exp);
			$config = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].$this -> rootFolder."/config.xml"); 
		
			// start the cache
			// 3 levels of cache!
			// Set a few options
		//	$options = array(
		//		'cacheDir' => '../temp/',
		//		'lifeTime' => 86400, // one day!
		//		'automaticSerialization' => TRUE
		//	);
			
			// Create a Cache_Lite object
		//	$cache = new Cache_Lite($options);
			
			// Test if there is a valid cache for this id
	//		if ($data = $cache->get("folderID")) {
	//			$this -> folderMap = $data;
			
	//		} else { 
				$this -> readFolderXML();
		//		$cache->save($this -> folderMap, "folderID");
		//	}
			
			
			

		}
		
		
		// findFolder 
		// param 1 - ID of folder to search
		// param 2 - parent ID of folder to search. Could be 0
		// return - the path (including root) to the file
		// useage - Will return the full path to a folder cached or rebuild the cache
		
		function findFolder($id, $pid){
			if (!array_key_exists($id,$this -> folderMap)){
				// folder has not been added yet or is invalid. Test parent!
				if (!array_key_exists($pid,$this -> folderMap)){
					// parent ID does not exist either. Rebuild and return root
					$this -> rebuildAll();
					$this -> saveFolderXML();
					return $this -> rootFolder;
				} else {
					// if parent exists, rebuild from that folder only!
					$this -> rebuild($this -> folderMap[$pid]);
					$this -> saveFolderXML();
					return $this -> folderMap[$pid];
				}
			} else{
				return $this -> folderMap[$id];
			}
		}
		
		// readFolderXML
		// useage - Will read in and create a hashmap of the folder XML
		
		function readFolderXML(){
			// confirm it exists first!
			if (file_exists($_SERVER['DOCUMENT_ROOT']."/".$this -> rootFolder."/folderList.xml")){
				
				$xml = simplexml_load_file(	$_SERVER['DOCUMENT_ROOT']."/".$this -> rootFolder."/folderList.xml");
				foreach($xml->children() as $child){
					
					$this -> folderMap[ (string) $child['id']] = (string) $child['name'];
				}
			}
		}
		
		function findFolderKey($value){
			return array_search($this -> rootFolder."/".$value, $this -> folderMap);
		}
		
		
		function findFolderKeyAbsolute($value){
			return array_search( $value, $this -> folderMap);
		}
		
		// saveFolderXML
		// useage - Will output the hashmap as XML so it can be loaded quickly
		
		function saveFolderXML(){
			$folders = new SimpleXMLElement("<folders></folders>");
			foreach ($this -> folderMap  as $id => $fname){
				
				$folder = $folders->addChild('folder');
				$folder->addAttribute('id', $id);
				$folder->addAttribute('name', $fname);
			}
			$fh = fopen($_SERVER['DOCUMENT_ROOT']."/".$this -> rootFolder."/folderList.xml", 'w');
			fwrite($fh, $folders -> asXML());
			fclose($fh);
		}
		
		// rebuildAll
		// useage - will call rebuild with the rootFolder!
		
		function rebuildAll(){
			$this -> rebuild($_SERVER['DOCUMENT_ROOT']."/".$this -> rootFolder);	
		}
		
		// rebuild
		// param 1 - The folder to start the rebuild
		// useage - This will recursivly read through folders and genearte a entry into the folderMap
		//          using the hash function as a key.
		function rebuild($folderStart){
			
				// add to folder map!
				$f = substr($folderStart, strlen($_SERVER['DOCUMENT_ROOT'])+1);
				
				$this -> folderMap[$this -> hashFolder($f)] = $f;
				// now see if there are any folders inside this one.
				$results = scandir($folderStart);
				
				foreach ($results as $result) {
					if ($result === '.' or $result === '..' or $result === 'converted') continue;
					if (is_dir($folderStart . '/' . $result)) {
						$this -> rebuild($folderStart . "/" . $result); 
					}
				}
								
		}
		
		function hashFolder($folderName){
			$h = hash('crc32', $folderName);
			if (array_key_exists($h,$this -> folderMap)){
				$h = $this -> hashFolder($h);
			}
			return 	$h;
		}
		
		function debug(){
			echo "Root folder - ".$this -> rootFolder."<br>";	
			var_dump($this -> folderMap);
		}
   }

?> 