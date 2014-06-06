<?php
/**
 * Upload Handler
 *
 * @author TAIMT
 * @date 2014-05-04
 * @copyright (c) 2014, MAI THE TAI
 */

use Phalcon\Mvc\User\Component;

class UploadHandler extends Component {

	/**
	 * get image size
	 * @param  string $source
	 * @return string. example "300,400"
	 */
	public function get_image_size($source) {
    	//get the image size
    	$size = getimagesize($source);

    	//determine dimensions
    	$width = $size[0];
    	$height = $size[1];
	    if ($width && $height) {
	      	return sprintf("%s,%s", $width, $height);
	    } else {
	      	return nil;
	    }
    
  	}
	/**
	 * resize video
	 * @param  string $filePath
	 * @param  string $fileName
	 * @param  string $extension
	 * @return string
	 */
	public function resize_video($filePath, $fileName, $extension) {
      	$name = sprintf('%s-resize', $fileName);
      	$path  =  dirname($filePath) . DS .$name.'.'.$extension;

      	if (!file_exists($path)) {
          	$FFMPEG_Command = sprintf(
              	"/usr/local/bin/ffmpeg -i \"%s\" -b 400000 \"%s\" >/dev/null 2>/dev/null;",
              	$filePath, $path
          	);
          	shell_exec($FFMPEG_Command);
      	}

      	if (!file_exists($path))
          	return null;

      	return $path;
    }

    /**
     * create video thumbnail
     * @param  string  $filePath
     * @param  string  $fileName
     * @param  integer $ScreenShortSecond
     * @return string
     */
	public function videoThumnail($filePath, $fileName, $ScreenShortSecond = 0) {
      	$dirPath = dirname($filePath);
      	$thumbnailPath  =  $dirPath . DS .$fileName.'.jpg';

      	if (!file_exists($thumbnailPath)) {
          	$rotate_remove_cmd = sprintf(
              	"/usr/local/bin/exiftool '-*rotation*' \"%s\";",
              	$filePath
          	);
          	exec($rotate_remove_cmd, $output, $value);
          	$rotate_exif = split(':',$output[0]);
          	$rotate = $rotate_exif[1];
          	$FFMPEG_Command = sprintf(
                "/usr/local/bin/ffmpeg -i \"%s\" -y -ss \"00:00:%02d\" -f image2 \"%s\" >/dev/null 2>/dev/null;",
                $filePath, 0 + $ScreenShortSecond, $thumbnailPath
            );

          	shell_exec($FFMPEG_Command);

          	// modify orientation attribute of image
          	switch ($rotate) {
				case 90:
					$transpose = 6;
					break;
				case 180:
					$transpose = 3;
					break;
				case 270:
					$transpose = 8;
					break;
				default:
				# code...
				break;
			}

          	$modify_oriention_cmd = sprintf(
              	"/usr/local/bin/exiftool -Orientation=\"%d\" -n \"%s\" -overwrite_original;",
              	$transpose, $thumbnailPath
          	);
          	exec($modify_oriention_cmd);

          	// if ($rotate == 90) {
           //  	$this->RotateVideoThumbnail(480, $thumbnailPath, 'png', $thumbnailPath);
          	// }
      	}

      	if (!file_exists($thumbnailPath))
          	return null;

      	return $thumbnailPath;
    }

    /**
     * resize image
     * @param  string $originPath
     * @param  string $thumbnailPath
     * @param  integer $thumb_width
     * @param  integer $thumb_height
     * @param  string $type
     * @return string
     */
	protected function resizeImage($originPath, $thumbnailPath, $thumb_width, $thumb_height, $type)
    {
		$type = strtolower($type);

		if ($type == "jpg" || $type == "jpeg") {
			$image = imagecreatefromjpeg($originPath);
		}

		if ($type == "gif") {
			$image = imagecreatefromgif($originPath);
		}

		if ($type == "png") {
			$image = imagecreatefrompng($originPath);
		}

		$filename = $thumbnailPath;

		$width = imagesx($image);
		$height = imagesy($image);

		$original_aspect = $width / $height;
		// $thumb_height = $thumb_width / $original_aspect;
		$thumb_aspect = $thumb_width / $thumb_height;

		if ( $original_aspect >= $thumb_aspect )
		{
			// If image is wider than thumbnail (in aspect ratio sense)
			$new_height = $thumb_height;
			$new_width = $width / ($height / $thumb_height);
		}
		else
		{
			// If the thumbnail is wider than the image
			$new_width = $thumb_width;
			$new_height = $height / ($width / $thumb_width);
		}

		$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

		// Resize and crop
		imagecopyresampled($thumb,
		                 $image,
		                 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
		                 0 - ($new_height - $thumb_height) / 2, // Center the image vertically
		                 0, 0,
		                 $new_width, $new_height,
		                 $width, $height);

		if ($type == "jpg" || $type = "jpeg") {
			imagejpeg($thumb, $filename, 100);
		}

		if ($type == "gif") {
			imagegif($thumb, $filename, 100);
		}

		if ($type == "png") {
			imagepng($thumb, $filename, 100);
		}

		return $filename;
    }


	/**
	* ImageHandler - ResizeToDimension()
	*
	*   Resizes an image to fit into a specifie dimension
	*
	*   EXAMPLE USAGE:
	*
	*   $ImageHandler->ResizeToDimension(200, "file.jpg", "png", "images");
	*
	* @param  int     $dimension - dimension to fit into
	* @param  string    $source - image source
	* @param  string    $extension - image source file type
	* @param  string    $destination - destination directory
	*
	*/

	public function ResizeToDimension($dimension, $source, $extension, $destination)
	{

		//get the image size
		$size = getimagesize($source);

		//determine dimensions
		$width = $size[0];
		$height = $size[1];

		//determine what the file extension of the source
		//image is
		switch($extension)
		{

			//its a gif
			case 'gif': case 'GIF':
			  //create a gif from the source
			  $sourceImage = imagecreatefromgif($source);
			  break;
			case 'jpg': case 'JPG': case 'jpeg':
			  //create a jpg from the source
			  $sourceImage = imagecreatefromjpeg($source);
			  break;
			case 'png': case 'PNG':
			  //create a png from the source
			  $sourceImage = imagecreatefrompng($source);
			  break;

		}

		// find the largest dimension of the image
		// then calculate the resize perc based upon that dimension
		$percentage = ( $width >= $height ) ? 100 / $width * $dimension : 100 / $height * $dimension;

		// define new width / height
		$newWidth = $width / 100 * $percentage;
		$newHeight = $height / 100 * $percentage;

		// create a new image
		$destinationImage = imagecreatetruecolor($newWidth, $newHeight);

		// copy resampled
		imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		//exif only supports jpg in our supported file types
		if ($extension == "jpg" || $extension == "jpeg")
		{

			//fix photos taken on cameras that have incorrect
			//dimensions
			$exif = exif_read_data($source);

			if (isset($exif['Orientation'])) {
				//get the orientation
				$ort = $exif['Orientation'];

				//determine what oreientation the image was taken at
				switch($ort)
				{

				    case 2: // horizontal flip

				    	$this->ImageFlip($dimg);

				      	break;

				    case 3: // 180 rotate left

				        $destinationImage = imagerotate($destinationImage, 180, 0);

				      	break;

				    case 4: // vertical flip

				        $this->ImageFlip($dimg);

				      	break;

				    case 5: // vertical flip + 90 rotate right

				        $this->ImageFlip($destinationImage);

				        $destinationImage = imagerotate($destinationImage, -90, 0);

				      break;

				    case 6: // 90 rotate right

				        $destinationImage = imagerotate($destinationImage, -90, 0);

				      break;

				    case 7: // horizontal flip + 90 rotate right

				        $this->ImageFlip($destinationImage);

				        $destinationImage = imagerotate($destinationImage, -90, 0);

				      	break;

				    case 8: // 90 rotate left

				        $destinationImage = imagerotate($destinationImage, 90, 0);

				      	break;

				}
			}

		}

		// create the jpeg
		imagejpeg($destinationImage, $destination, 100);

		return $destination;
	}

	/**
	 * rotate image
	 * @param integer $dimension
	 * @param string $source
	 * @param string $extension
	 * @param string $destination
	 */
	public function RotateVideoThumbnail($dimension, $source, $extension, $destination)
	{

		//get the image size
		$size = getimagesize($source);

		//determine dimensions
		$width = $size[0];
		$height = $size[1];

		//determine what the file extension of the source
		//image is
		var_dump($source);
		$sourceImage = imagecreatefrompng($source);

		// find the largest dimension of the image
		// then calculate the resize perc based upon that dimension
		$percentage = ( $width >= $height ) ? 100 / $width * $dimension : 100 / $height * $dimension;

		// define new width / height
		$newWidth = $width / 100 * $percentage;
		$newHeight = $height / 100 * $percentage;

		// create a new image
		$destinationImage = imagecreatetruecolor($newWidth, $newHeight);

		// copy resampled
		imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		$destinationImage = imagerotate($destinationImage, -90, -1);

		// create the png
		imagepng($destinationImage, $destination, 9);

		return $destination;
	}


	/**
	* ImageHandler - ImageFlip()
	*
	*   Resizes an image to set width and height
	*
	*   EXAMPLE USAGE:
	*
	*   $ImageHandler->Resize(200, "file.jpg", "png", "images");
	*
	* @param  string    $image (image to flip)
	* @param  int     $x
	* @param  int     $y
	* @param  int     $width
	* @param  int     $height
	*
	*/

	public function ImageFlip(&$image, $x = 0, $y = 0, $width = null, $height = null)
	{

	    if ($width  < 1) $width  = imagesx($image);
	    if ($height < 1) $height = imagesy($image);

	    // Truecolor provides better results, if possible.
	    if (function_exists('imageistruecolor') && imageistruecolor($image))
	    {

	        $tmp = imagecreatetruecolor(1, $height);

	    }
	    else
	    {

	        $tmp = imagecreate(1, $height);

	    }

	    $x2 = $x + $width - 1;

	    for ($i = (int)floor(($width - 1) / 2); $i >= 0; $i--)
	    {

	        // Backup right stripe.
	        imagecopy($tmp, $image, 0, 0, $x2 - $i, $y, 1, $height);

	        // Copy left stripe to the right.
	        imagecopy($image, $image, $x2 - $i, $y, $x + $i, $y, 1, $height);

	        // Copy backuped right stripe to the left.
	        imagecopy($image, $tmp, $x + $i,  $y, 0, 0, 1, $height);

	    }

	    imagedestroy($tmp);

	    return true;

	}
}