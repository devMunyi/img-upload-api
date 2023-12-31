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


// will store the image in its original version
function upload_file($fname,$tmpName,$upload_dir)
{
    $ext=pathinfo($fname, PATHINFO_EXTENSION);
    // $nfileName=generateRandomString(25).'.'."$ext";
    $nfileName = $fname;

    $filePath = $upload_dir.$nfileName;

    $result = move_uploaded_file($tmpName, $filePath); //var_dump($result);
    if (!$result)
    {
        return null;
    }
    elseif($result)
    {
        return $nfileName;
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
        $old_image = imagecreatefromstring("$updir"."$img");
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

function upload_and_resize_image($fname, $tmpName, $upload_dir, $w = 800, $h = 800)
{
    $ext = pathinfo($fname, PATHINFO_EXTENSION);
    $nfileName = "resized_" . $fname; // Add prefix "resized_" to the file name
    $resizedFilePath = $upload_dir . $nfileName;
    $originalFilePath = $upload_dir . $fname;

    // Move the file to the upload directory
    move_uploaded_file($tmpName, $originalFilePath);

    // Resize image if it's a JPEG or PNG
    if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
        $maxWidth = $w; // Maximum width for resized image
        $maxHeight = $h; // Maximum height for resized image

        // Create a new image from the uploaded file
        if ($ext === 'jpg' || $ext === 'jpeg') {
            if(imagecreatefromstring($originalFilePath)){
                $sourceImage = imagecreatefromstring($originalFilePath);
            }elseif(imagecreatefrompng($originalFilePath)){
                $sourceImage = imagecreatefrompng($originalFilePath);
            }
        } elseif ($ext === 'png') {

            if(imagecreatefrompng($originalFilePath)){
                $sourceImage = imagecreatefrompng($originalFilePath);
            }elseif(imagecreatefromstring($originalFilePath)){
                $sourceImage = imagecreatefromstring($originalFilePath);
            }
        }

        if ($ext === 'jpg' || $ext === 'jpeg') {
            $sourceImage = imagecreatefromjpeg($originalFilePath);
        } elseif ($ext === 'png') {
            $sourceImage = imagecreatefrompng($originalFilePath);
        }

        if (!$sourceImage) {
            throw new Exception("Failed to create the source image.");
        }

        if (!$sourceImage) {
            throw new Exception("Failed to create the source image.");
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
                imagejpeg($resizedImage, $resizedFilePath, 90); // Adjust the quality (90) as needed
            } elseif ($ext === 'png') {
                imagepng($resizedImage, $resizedFilePath, 9); // Adjust the compression level (9) as needed
            }

            // Free up memory
            imagedestroy($resizedImage);
        }

        // Free up memory
        imagedestroy($sourceImage);
    }

    // Check if both files exist and return the file names if successful, otherwise return null
    if (file_exists($resizedFilePath) && file_exists($originalFilePath)) {
        return array(
            'resized' => $nfileName,
            'original' => $fname
        );
    } else {
        return null;
    }
}
function resize_multiple_dir_images($fname, $tmpName, $upload_dir, $w = 800, $h = 800)
{
    try {
        $ext = pathinfo($fname, PATHINFO_EXTENSION);
        $nfileName = $fname; // Add prefix "resized_" to the file name
        $resizedFilePath = "$upload_dir/$nfileName";
        $originalFilePath = "$tmpName";
        // echo "Resized File Path $resizedFilePath <br>";
        // echo "Original File Path $originalFilePath <br>";
        // return;

        // Move the file to the upload directory
        // if (!move_uploaded_file($tmpName, $originalFilePath)) {
        //     throw new Exception("Failed to move the uploaded file.");
        // }

        // Resize image if it's a JPEG or PNG
        if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
            $maxWidth = $w; // Maximum width for resized image
            $maxHeight = $h; // Maximum height for resized image

            // Create a new image from the uploaded file
            if ($ext === 'jpg' || $ext === 'jpeg') {
                if(imagecreatefromstring($originalFilePath)){
                    $sourceImage = imagecreatefromstring($originalFilePath);
                }elseif(imagecreatefrompng($originalFilePath)){
                    $sourceImage = imagecreatefrompng($originalFilePath);
                }
            } elseif ($ext === 'png') {
    
                if(imagecreatefrompng($originalFilePath)){
                    $sourceImage = imagecreatefrompng($originalFilePath);
                }elseif(imagecreatefromstring($originalFilePath)){
                    $sourceImage = imagecreatefromstring($originalFilePath);
                }
            }

            if (!$sourceImage) {
                throw new Exception("Failed to create the source image.");
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

                if (!$resizedImage) {
                    throw new Exception("Failed to create the resized image.");
                }

                // Resize the original image to the new dimensions
                if (!imagecopyresampled(
                    $resizedImage, // Destination image resource
                    $sourceImage, // Source image resource
                    0, 0, // Destination x, y coordinates
                    0, 0, // Source x, y coordinates
                    $newWidth, $newHeight, // Destination width, height
                    $sourceWidth, $sourceHeight // Source width, height
                )) {
                    throw new Exception("Failed to resize the image.");
                }

                // Save the resized image to the file path
                if ($ext === 'jpg' || $ext === 'jpeg') {
                    if (!imagejpeg($resizedImage, $resizedFilePath, 90)) { // Adjust the quality (90) as needed
                        throw new Exception("Failed to save the resized image.");
                    }
                } elseif ($ext === 'png') {
                    if (!imagepng($resizedImage, $resizedFilePath, 9)) { // Adjust the compression level (9) as needed
                        throw new Exception("Failed to save the resized image.");
                    }
                }

                // Free up memory
                imagedestroy($resizedImage);
            }

            // Free up memory
            imagedestroy($sourceImage);
        }

        // Check if both files exist and return the file names if successful, otherwise return null
        if (file_exists($resizedFilePath) && file_exists($originalFilePath)) {
            return array(
                'resized' => $nfileName,
                'original' => $fname
            );
        } else {
            throw new Exception("One or both files do not exist.");
        }
    } catch (Exception $e) {
        // Handle exceptions here, you can log the error or return an error message as needed.
        
        echo $e->getMessage();
        return null;
    }
}


function is_image($file)
{
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($file_extension, $image_extensions);
}

?>