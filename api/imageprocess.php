<?php

error_reporting(0);
define('PW_DEBUG',FALSE);

ini_set("gd.jpeg_ignore_warning", 1);

function pw_resizeimage($img,$size,$newwidth,$newheight=-1){
	$width=$size[0];
	$height=$size[1]; 
    if ($newheight==-1){
        $newheight = $height*($newwidth/$width);
    }
    $tci = imagecreatetruecolor($newwidth, $newheight);
    //-- alpha work
    imagealphablending($tci, false);
    imagesavealpha($tci,true);
    $transparent = imagecolorallocatealpha($tci, 255, 255, 255, 127);
    imagefilledrectangle($tci, 0, 0, $newwidth, $newheight, $transparent);
    //-- alpha work --
	imagecopyresampled($tci, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	return $tci;
}

function pw_fix_orientation($filename,$destination,$quality=100){
    $info = getimagesize($filename);
    $img=false;
    $image_type="jpg";

	if ($info['mime'] == 'image/jpeg') {
		$img = imagecreatefromjpeg($filename);
        $image_type="jpg";
    }else if ($info['mime'] == 'image/gif') {
		$img = imagecreatefromgif($filename);
        $image_type="gif";
    }else if ($info['mime'] == 'image/png') {
        $img = imagecreatefrompng($filename);
        $image_type="png";
    }

    if ($img==false){
        return $filename;
    }

    $exif=array();
    try{
	    $exif = exif_read_data($filename);
    }catch(Exception $ex){
    }
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $img = imagerotate($img, 180, 0);
                break;

            case 6:
                $img = imagerotate($img, -90, 0);
                break;

            case 8:
                $img = imagerotate($img, 90, 0);
                break;
        }
    }
    //imagejpeg($img, $destination, $quality);
    if ($image_type=="jpg"){
        imagejpeg($img, $destination, $quality);
    }else if ($image_type=="gif"){
        imagegif($img,$destination);
    }else if ($image_type=="png"){
        imagepng($img,$destination);
    }
    return $destination;
}

function pw_rotate_image($source,$destination,$rotate=90){
    $info = getimagesize($source);

	if ($info['mime'] == 'image/jpeg') 
		$image = imagecreatefromjpeg($source);

	else if ($info['mime'] == 'image/gif') 
		$image = imagecreatefromgif($source);

	else if ($info['mime'] == 'image/png') 
		$image = imagecreatefrompng($source);

    $image=imagerotate($image,$rotate,0);
	imagejpeg($image, $destination, $quality);
}

function pw_compress_image($source, $destination,$newwidth,$newheight,$quality) {

    $info = getimagesize($source);
    $image_type="jpg";
    $image=false;

	if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
        $image_type="jpg";
    }
	else if ($info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
        $image_type="gif";
    }
	else if ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
        $image_type="png";
    }

    if ($image==false){
        return $source;
    }

    if ($info[0]<$newwidth){
        $newwidth=$info[0];
    }
    $image=pw_resizeimage($image,$info,$newwidth,$newheight);

    if ($image_type=="jpg"){
        imagejpeg($image, $destination, $quality);
    }else if ($image_type=="gif"){
        imagegif($image,$destination);
    }else if ($image_type=="png"){
        imagepng($image,$destination);
    }
	return $destination;
}

function pw_png_transparency($image,$destination){
    if ($image!=false){
        imagealphablending($image, false);
        imagesavealpha($image,true);
        imagepng($image,$destination);
    }
}

?>