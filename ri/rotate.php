<?php

/*
	AUTOMATIC IMAGE ROTATOR
	Version 2.2 - December 4, 2003
	Copyright (c) 2002-2003 Dan P. Benjamin, Automatic, Ltd.
	All Rights Reserved.
	http://www.hiveware.com/imagerotator.php
	http://www.automaticlabs.com/
	
	DISCLAIMER
	Automatic, Ltd. makes no representations or warranties about
	the suitability of the software, either express or
	implied, including but not limited to the implied
	warranties of merchantability, fitness for a particular
	purpose, or non-infringement. Dan P. Benjamin and Automatic, Ltd.
	shall not be liable for any damages suffered by licensee
	as a result of using, modifying or distributing this
	software or its derivatives.
	
	ABOUT
	This PHP script will randomly select an image file from a
	folder of images on your webserver.  You can then link to it
	as you would any standard image file and you'll see a random
	image each time you reload.
	When you want to add or remove images from the rotation-pool,
	just add or remove them from the image rotation folder.

	INSTRUCTIONS
	1. Modify the $folder setting in the configuration section below.
	2. Add image types if needed (most users can ignore that part).
	3. Upload this file (rotate.php) to your webserver.  I recommend
	   uploading it to the same folder as your images.
	4. Link to the file as you would any normal image file, like this:
			<img src="http://example.com/rotate.php">

	5. You can also specify the image to display like this:
			<img src="http://example.com/rotate.php?img=gorilla.jpg">
		
		This would specify that an image named "gorilla.jpg" located
		in the image-rotation folder should be displayed.
	
	That's it, you're done.

*/

/* ------------------------- CONFIGURATION -----------------------

	Set $folder to the full path to the location of your images.
	For example: $folder = '/user/me/example.com/images/';
	If the rotate.php file will be in the same folder as your
	images then you should leave it set to $folder = '.';

*/

	$folder = '.';

/*	file types allowed */

    $extList = array();
	$extList['gif'] = 'image/gif';
	$extList['jpg'] = 'image/jpeg';
	$extList['jpeg'] = 'image/jpeg';
	$extList['png'] = 'image/png';
	
// You don't need to edit anything after this point.

// --------------------- END CONFIGURATION -----------------------

$img = null;

if (substr($folder,-1) != '/') {
	$folder = $folder.'/';
}

if (isset($_GET['img'])) {
	$imageInfo = pathinfo($_GET['img']);
	if (
	    isset( $extList[ strtolower( $imageInfo['extension'] ) ] ) &&
        file_exists( $folder.$imageInfo['basename'] )
    ) {
		$img = $folder.$imageInfo['basename'];
	}
} else {
	$fileList = array();
	$handle = opendir($folder);
	while ( false !== ( $file = readdir($handle) ) ) {
		$file_info = pathinfo($file);
		if (
		    isset( $extList[ strtolower( $file_info['extension'] ) ] )
		) {
			$fileList[] = $file;
		}
	}
	closedir($handle);

	if (count($fileList) > 0) {
		$imageNumber = time() % count($fileList);
		$img = $folder.$fileList[$imageNumber];
	}
}

if ($img!=null) {
	$imageInfo = pathinfo($img);
	$contentType = 'Content-type: '.$extList[ $imageInfo['extension'] ];
	header ($contentType);
	readfile($img);
} else {
	if ( function_exists('imagecreate') ) {
		header ("Content-type: image/png");
		$im = @imagecreate (100, 100)
		    or die ("Cannot initialize new GD image stream");
		$background_color = imagecolorallocate ($im, 255, 255, 255);
		$text_color = imagecolorallocate ($im, 0,0,0);
		imagestring ($im, 2, 5, 5,  "IMAGE ERROR", $text_color);
		imagepng ($im);
		imagedestroy($im);
	}
}

?>
