<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);

// allowed origins
include_once ("./allowed-ips-or-origins.php");

// Check the request method  
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Check the 'Host' header to determine the origin
$origin =  $_SERVER['REMOTE_ADDR'];

if ($requestMethod != 'POST') {
    http_response_code(404);
    exit;
}

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);

    // Only set Access-Control-Allow-Methods for POST requests
    if ($requestMethod === 'POST') {
        header("Access-Control-Allow-Methods: $requestMethod");
    }

    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    // header("Content-Type: application/json; charset=UTF-8");
} else {
    // echo "origin => $origin";
    http_response_code(403);
    exit;
}


// Include the functions
include('../functions/helpers.php');

// Get the file data from the request
$file_data = file_get_contents('php://input');

// Decode the file data as JSON
$original_file_info = json_decode($file_data);

$original_file_info = $_FILES['file_'];
$file_name = $original_file_info['name'];
$file_size = $original_file_info['size'];
$file_tmp = $original_file_info['tmp_name'];
$file_name2 = $file_name;
$file_tmp2 = $file_tmp;


$thumb_info = $_FILES['thumb_'] ?? 0;
if($thumb_info !== 0){
    $thumb_name = $thumb_info['name'];
    $thumb_tmp = $thumb_info['tmp_name'];
}

$allowed_formats_array = ['jpg','jpeg','png'];
$allowed_formats = 'jpg, jpeg and png';

if ($file_size > 100) {
    if ((file_type($file_name, $allowed_formats_array)) == 0) {
        exit(json_encode(['payload' => '', 'message' => "This file format is not allowed. Only $allowed_formats"]));
    }
} else {
    exit(json_encode(['payload' => '', 'message' => 'File not attached or has invalid size']));
}

// uploads directory
$upload_dest = '../uploads_/';

// Upload original file
$or_file_upload = upload_and_resize_image($file_name, $file_tmp, $upload_dest);

// Check if the original file upload was successful
if ($or_file_upload == null) {
    // Error uploading file
    exit(json_encode(['payload' => '', 'message' => 'Upload Failed. Please retry!']));
}

// Upload the thumb file
if($thumb_info !== 0){
    $thumb_upload = upload_file($thumb_name, $thumb_tmp, $upload_dest);

    // Check if the thumb file upload was successful
    if ($thumb_upload == null) {
        // Error uploading file
        exit(json_encode(['payload' => '', 'message' => 'File thumb upload Failed. Please retry!']));
    }
}



// Get the file name only
// $file_name_only = pathinfo($upload, PATHINFO_FILENAME);
// if($make_thumbnail == 1){
//     // Create the thumbnail
//    try{
//     makeThumbnails($upload_dest, $upload, 100, 100, 'thumb_' . $file_name_only);
//    }catch(Exception $e){
//     echo $e->getMessage();
//     exit(json_encode(['payload' => '', 'message' => 'Upload Failed. Please retry!']));
//    }
   
// }
// resize the image to have an optimized version for faster load on webpage
// $file_name_only = pathinfo($o_file_upload, PATHINFO_FILENAME);
// $resized_file_upload = upload_and_resize_image($file_name, );
// if($resized_file_upload == null){
//     exit(json_encode(['payload' => '', 'message' => 'File thumb upload failed. Please retry!']));
// }

// Get the file name only
// $file_name_only = pathinfo($upload, PATHINFO_FILENAME);
// if($make_thumbnail == 1){
//     // Create the thumbnail
//    try{
//     makeThumbnails($upload_dest, $upload, 100, 100, 'thumb_' . $file_name_only);
//    }catch(Exception $e){
//     echo $e->getMessage();
//     exit(json_encode(['payload' => '', 'message' => 'Upload Failed. Please retry!']));
//    }
   
// }

// return a uploaded file name alongside success message
exit(json_encode(['payload' => $or_file_upload, 'message' => 'Upload Okay! Submit Now', 'filename' => $file_name]));

?>
