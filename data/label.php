<?php

$dir = ""; //server path

// Open a directory, and read its contents
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){
		if (mime_content_type($file) == "application/pdf") {
			//$file_ext 	= explode(".",$file);
			if (!file_exists($dir.$file.".jpg")) {
      			echo "filename:" . $file_ext[0] ."   " .mime_content_type($dir.$file)."<br>\n";
			exec("convert -density 300 -trim ".$dir.$file." -quality 100 ".$dir.$file.".jpg");
			exec("convert ".$dir.$file.".jpg -resize 300 ".$dir.$file.".jpg");
			}	
    		}
	}
    closedir($dh);
  }
}

die();

?>
