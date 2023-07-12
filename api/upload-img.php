<?php

// // Headers
header('Access-Control-Allow-Origin: *');
// header('Content-Type: application/json');
// header('Access-Control-Allow-Methods: POST');
// // header(
// //     'Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With'
// // );

// Include the functions
include('../functions/helpers.php');

// Get the file data from the request
$file_data = file_get_contents('php://input');

// Decode the file data as JSON
$file_info = json_decode($file_data);

// $name = $_POST['name'];
$file_info = $_FILES['file_'];
$file_name = $file_info['name'];
$file_size = $file_info['size'];
$file_tmp = $file_info['tmp_name'];
$make_thumbnail = $_POST['make_thumbnail'];

$allowed_formats_array = ['jpg','jpeg','png'];
$allowed_formats = 'jpg, jpeg and png';

if ($file_size > 100) {
    if ((file_type($file_name, $allowed_formats_array)) == 0) {
        echo json_encode(['payload' => '', 'message' => "This file format is not allowed. Only $allowed_formats"]);
        exit();
    }
} else {
    echo json_encode(['payload' => '', 'message' => 'File not attached or has invalid size']);
    exit();
}

$upload_dest = '../uploads_/';

// Upload the file
$upload = upload_file($file_name, $file_tmp, $upload_dest);

// Check if the upload was successful
if ($upload == null) {
    // Error uploading file
    echo json_encode(['payload' => '', 'message' => 'Upload Failed. Please retry!']);
    exit();
}

// Get the file name only
$file_name_only = pathinfo($upload, PATHINFO_FILENAME);
if($make_thumbnail == 1){
    // Create the thumbnail
   makeThumbnails($upload_dest, $upload, 100, 100, 'thumb_' . $file_name_only);
}

echo json_encode(['payload' => $upload, 'message' => 'Upload Okay! Submit Now', 'filename' => $file_name]);

?>
