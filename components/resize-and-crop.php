<?php
function resizeAndCropImage($sourcePath, $destinationPath, $size = 200) {
    list($width, $height, $type) = getimagesize($sourcePath);

    $minSide = min($width, $height);
    $newImage = imagecreatetruecolor($size, $size);
    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    imagecopyresampled(
        $newImage, $sourceImage, 
        0, 0, 
        ($width - $minSide) / 2, ($height - $minSide) / 2, 
        $size, $size, 
        $minSide, $minSide
    );

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $destinationPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $destinationPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $destinationPath);
            break;
    }

    // Cleanup
    imagedestroy($newImage);
    imagedestroy($sourceImage);

    return true;
}
?>