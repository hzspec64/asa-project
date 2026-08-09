<?php

function createImageVersions($source, $destination, $filename)
{
    $info = getimagesize($source);

    if (!$info) {
        return false;
    }

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    // Create source image
    switch ($mime) {

        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($source);
            $extension = 'jpg';
            break;

        case 'image/png':
            $sourceImage = imagecreatefrompng($source);
            $extension = 'png';
            break;

        case 'image/webp':
            $sourceImage = imagecreatefromwebp($source);
            $extension = 'webp';
            break;

        default:
            return false;
    }


    /*
     * LARGE
     * Maximum width: 1600px
     */
    $largeWidth = min($width, 1600);
    $largeHeight = round(
        $height * ($largeWidth / $width)
    );

    $largeImage = imagecreatetruecolor(
        $largeWidth,
        $largeHeight
    );

    imagecopyresampled(
        $largeImage,
        $sourceImage,
        0,
        0,
        0,
        0,
        $largeWidth,
        $largeHeight,
        $width,
        $height
    );

    $largePath = $destination . '/' . $filename . '-large.' . $extension;

    saveImage(
        $largeImage,
        $largePath,
        $mime
    );

    imagedestroy($largeImage);



    /*
     * SMALL
     * Maximum width: 800px
     */
    $smallWidth = min($width, 800);
    $smallHeight = round(
        $height * ($smallWidth / $width)
    );

    $smallImage = imagecreatetruecolor(
        $smallWidth,
        $smallHeight
    );

    imagecopyresampled(
        $smallImage,
        $sourceImage,
        0,
        0,
        0,
        0,
        $smallWidth,
        $smallHeight,
        $width,
        $height
    );

    $smallPath = $destination . '/' . $filename . '-small.' . $extension;

    saveImage(
        $smallImage,
        $smallPath,
        $mime
    );

    imagedestroy($smallImage);



    /*
     * SQUARE
     * Center crop → 400 × 400
     */
    $squareSize = min($width, $height);

    $cropX = ($width - $squareSize) / 2;
    $cropY = ($height - $squareSize) / 2;

    $squareImage = imagecreatetruecolor(
        400,
        400
    );

    imagecopyresampled(
        $squareImage,
        $sourceImage,
        0,
        0,
        $cropX,
        $cropY,
        400,
        400,
        $squareSize,
        $squareSize
    );

    $squarePath = $destination . '/' . $filename . '-square.' . $extension;

    saveImage(
        $squareImage,
        $squarePath,
        $mime
    );

    imagedestroy($squareImage);


    imagedestroy($sourceImage);

    return [
        'large' => $filename . '-large.' . $extension,
        'small' => $filename . '-small.' . $extension,
        'square' => $filename . '-square.' . $extension
    ];
}



function saveImage($image, $path, $mime)
{
    switch ($mime) {

        case 'image/jpeg':
            imagejpeg($image, $path, 85);
            break;

        case 'image/png':
            imagepng($image, $path, 6);
            break;

        case 'image/webp':
            imagewebp($image, $path, 85);
            break;
    }
}