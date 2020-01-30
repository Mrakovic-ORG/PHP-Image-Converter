<?php
/**
 * MIT License
 *
 * Copyright (c) 2020 Mrakovic
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * @param      $originalFile
 * @param      $outputFileExt
 * @param bool $compress
 *
 * @return string
 * @throws Exception
 */
function img_converter($originalFile, $outputFileExt, $compress = FALSE) {
    // Store $originalFile details into $ext
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $ext = $finfo->buffer($originalFile);

    // explode to get image type/ext
    $ext_from_mime = explode('/', $ext);

    // make sure it is an image
    if ($ext_from_mime[0] === 'image') {

        // register $image as string image
        $image = imagecreatefromstring($originalFile);

        // Register output file name
        $outputFile = random_int(1000000, 9999999) . '.' . $outputFileExt;

        // Get conversion
        if ($outputFileExt === 'jpeg') {
            imagejpeg($image, $outputFile, 100);
        } elseif ($outputFileExt === 'png') {
            imagepng($image, $outputFile, 9);
        } elseif ($outputFileExt === 'gif') {
            imagegif($image, $outputFile);
        } elseif ($outputFileExt === 'bmp') {
            imagebmp($image, $outputFile, $compress);
        } else {
            return 'Allowed conversion: jpeg,png,gif,bmp';
        }

        // Finally destroy image
        imagedestroy($image);

        $mimes = [
            IMAGETYPE_JPEG => 'image/jpg',
            IMAGETYPE_PNG  => 'image/png',
            IMAGETYPE_GIF  => 'image/gif',
            IMAGETYPE_BMP  => 'image/bmp'
        ];

        // Get image type to compare with $mimes
        $image_type = exif_imagetype($outputFile);
        // Store image as base64 into $base64_image before deleting image path from system
        $base64_image = base64_encode(file_get_contents($outputFile));
        $image_mimetype = $mimes[$image_type];
        unlink($outputFile);
        // Return a valid base64 image
        return 'data:' . $image_mimetype . ';base64,' . $base64_image;
    }
    return 'Not a valid image.';
}

if (!isset($argv[1])) {
    die('Please chose a file to convert.');
}

if (!isset($argv[2])) {
    die('Please chose conversion type example: jpeg,png,gif,bmp');
}

try {
    echo img_converter(file_get_contents($argv[1]), $argv[2]);
} catch (Exception $e) {
    Throw new RuntimeException('Error while trying to convert image. (L' . $e->getLine() . ')');
}
