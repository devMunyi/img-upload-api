<?php 

function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function upload_file($fname, $tmpName, $upload_dir)
{
    $ext = pathinfo($fname, PATHINFO_EXTENSION);
    $nfileName = generateRandomString(25) . '.' . $ext;
    $filePath = $upload_dir . $nfileName;

    // Resize image if it's a JPEG or PNG
    if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
        $maxWidth = 800; // Maximum width for resized image
        $maxHeight = 800; // Maximum height for resized image

        // Create a new image from the uploaded file
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $sourceImage = imagecreatefromjpeg($tmpName);
        } elseif ($ext === 'png') {
            $sourceImage = imagecreatefrompng($tmpName);
        }

        // Get the dimensions of the original image
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Calculate the proportional resize dimensions
        if ($sourceWidth > $maxWidth || $sourceHeight > $maxHeight) {
            $aspectRatio = $sourceWidth / $sourceHeight;

            if ($sourceWidth > $sourceHeight) {
                $newWidth = $maxWidth;
                $newHeight = $maxWidth / $aspectRatio;
            } else {
                $newHeight = $maxHeight;
                $newWidth = $maxHeight * $aspectRatio;
            }

            // Create a new blank image with the desired dimensions
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Resize the original image to the new dimensions
            imagecopyresampled(
                $resizedImage, // Destination image resource
                $sourceImage, // Source image resource
                0, 0, // Destination x, y coordinates
                0, 0, // Source x, y coordinates
                $newWidth, $newHeight, // Destination width, height
                $sourceWidth, $sourceHeight // Source width, height
            );

            // Save the resized image to the file path
            if ($ext === 'jpg' || $ext === 'jpeg') {
                imagejpeg($resizedImage, $filePath, 90); // Adjust the quality (90) as needed
            } elseif ($ext === 'png') {
                imagepng($resizedImage, $filePath, 9); // Adjust the compression level (9) as needed
            }

            // Free up memory
            imagedestroy($resizedImage);
        } else {
            // Save the original image without resizing
            move_uploaded_file($tmpName, $filePath);
        }

        // Free up memory
        imagedestroy($sourceImage);
    } else {
        // Move the file to the upload directory without resizing
        move_uploaded_file($tmpName, $filePath);
    }

    // Return the file name if successful, otherwise return null
    if (file_exists($filePath)) {
        return $nfileName;
    } else {
        return null;
    }
}


function makeThumbnails($updir, $img,$w,$h,$fname){
    $thumbnail_width = $w;
    $thumbnail_height = $h;
    $thumb_beforeword = "thumb";
    $ext=fileext_fetch($img);
    $arr_image_details = getimagesize("$updir"."$img"); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == 1) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    
    if ($arr_image_details[2] == 2) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == 3) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }

    if ($imgt == "ImageJPEG") {
        $old_image = imagecreatefromjpeg("$updir"."$img");
    }

    if($imgt == "ImagePNG"){
        $old_image = imagecreatefrompng("$updir"."$img");
    }

    if($imgt == "ImageGIF"){
        $old_image = imagecreatefromgif("$updir"."$img");
    }

        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);

        imagealphablending($new_image,false);
        imagesavealpha($new_image,true);

        $transparency=imagecolorallocatealpha($new_image,255,255,255,127);
        imagefilledrectangle($new_image,0,0,$w,$h,$transparency);

        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width,
         $original_height);
        $imgt($new_image, "$updir"."$fname" . ".$ext");

}


function fileext_fetch($filename)
{
    $ext=pathinfo($filename, PATHINFO_EXTENSION);
    return $ext;
}

function file_type($filename, $search_array)
{
    $ext=pathinfo($filename, PATHINFO_EXTENSION);
    $ext=strtolower($ext);
    if((!in_array("$ext", $search_array)))
    {
        return 0;
    }
    else
    {
        return 1;
    }
}
?>