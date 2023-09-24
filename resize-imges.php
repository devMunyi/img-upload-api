<?php 
set_time_limit(600);
include_once ("./functions/helpers.php");
$src_dir = "./ug_uploads";
$dest_dir = "./ug_uploads/resized";

// Get a list of files in the source directory
$files = scandir($src_dir);

// Process images in batches
$batch_size = 50; // Adjust the batch size as needed
$batch_count = ceil(count($files) / $batch_size);

for ($batch = 0; $batch < $batch_count; $batch++) {
    // Slice the array to get a batch of files
    $batch_files = array_slice($files, $batch * $batch_size, $batch_size);

    // Loop through each file in the batch
    foreach ($batch_files as $file) {
        // Skip ".", "..", and non-image files
        if ($file === "." || $file === ".." || !is_image($file)) {
            continue;
        }

        // Get the full path of the source file
        $src_file = $src_dir . '/' . $file;

        // Resize and save the image using your resize_image function
        $result = resize_multiple_dir_images($file, $src_file, $dest_dir);

        // Check if resizing was successful
        if ($result !== null) {
            echo "Resized and saved: " . $result['resized'] . "<br>";
        }
    }
}


?>