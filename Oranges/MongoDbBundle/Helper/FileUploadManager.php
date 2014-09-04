<?php
namespace Oranges\MongoDbBundle\Helper;

/**
 File Manager for storing files in the Ajent file system cloud.

 @author R.J. Keller <rjkeller@pixonite.com>
*/
class FileUploadManager
{
	/**
	 Stores the specified file upload in GridFS.
	
	 @param $upload_field_name - The name of the $_POST[] field where the user
		uploaded the file to.
	 @param $database - The name of the MongoDB database to store the file in.
	 @param $metadata - Meta data to retrieve the file later.
	 @return $id - The ID # of the file uploaded.
	*/
	public static function storeUpload($upload_field_name, $database, $metadata)
	{
		$mongo = MongoDb::getMongo();
		$db = $mongo->selectDB($database);

		$grid = $db->getGridFS();

		$id = $grid->storeUpload($upload_field_name, $metadata);
		return $id;
	}

	/**
	 Stores the specified file in GridFS.
	
	 @param $upload_field_name - The name of the file to store in GridFS.
	 @param $database - The name of the MongoDB database to store the file in.
	 @param $metadata - Meta data to retrieve the file later.
	 @return $id - The ID # of the file uploaded.
	*/
	public static function storeFile($file_name, $database, $metadata)
	{
		$mongo = MongoDb::getMongo();
		$db = $mongo->selectDB($database);

		$grid = $db->getGridFS();

		$id = $grid->storeFile($file_name, $metadata);
		return $id;
	}

	/**
	 Uses the PHP GD library to resize an image file to the specified width
	 and height.
	*/
	public static function resizeImageUpload($upload_field_name, $max_width, $max_height)
	{
		$filename = $_FILES[$upload_field_name]['tmp_name'];
		// Get new dimensions
		list($width, $height) = getimagesize($filename);
		
		$width_percent = $max_width / $width;
		$height_percent = $max_height / $height;
		
		$final_percent = 0.00;
		if ($width_percent < $height_percent)
			$final_percent = $width_percent;
		else
			$final_percent = $height_percent;
		
		$new_width = $width * $final_percent;
		$new_height = $height * $final_percent;

		// Resample
		$image_p = imagecreatetruecolor($max_width, $max_height);
		
		$info = getimagesize($filename);

		$image = null;
		if ($info['mime'] == "image/png")
			$image = imagecreatefrompng($filename);
		else if ($info['mime'] == "image/gif")
			$image = imagecreatefromgif($filename);
		else
			$image = imagecreatefromjpeg($filename);

		imagefill($image_p, 0, 0, imagecolorallocate($image_p, 255,255,255));
		imagecopyresampled($image_p, $image, ($max_width - $new_width) / 2, ($max_height - $new_height) / 2, 0, 0, $new_width, $new_height, $width, $height);

		// Output
		imagepng($image_p, $filename, 0);
	}
}