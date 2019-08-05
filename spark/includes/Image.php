<?php

/*

	Class: Image
	Purpose: Provide imagemagick/gd functionality for thumbnail generation
	Revision: 1

*/

class Image {

	protected $targetDir			= '';
	protected $imageMagickDir	= '/usr/local/bin';
	protected $tempDir			= '/var/tmp';
	protected $fileHistory		= array();
	protected $tempFile			= '';
	protected $jpgQuality		= '80';
	protected $count				= 0;
	protected $imageData			= array();
	protected $error				= '';
	protected $verbose			= false;
	protected $largeImage		= '';


 	//	Function: __construct, Purpose: places uploaded file in $this->tempDir, stores imagedata in $this->imageData, $filedata = $_FILES['file1']
	public function __construct($filedata, $uniqueName){

		$this->tempFile = $uniqueName;
		if (!@copy($filedata['tmp_name'], $this->tempDir . '/tmp' . $this->count . '_' . $this->tempFile)){

			die("Imagemagick: Upload failed");

		}

		$this->largeImage = $this->tempFile;
		$this->fileHistory[] = $this->tempDir . '/tmp' . $this->count . '_' . $this->tempFile;
		$this->getSize();

	}


	//	Function: largeImage, Purpose: Copies the full version of the current image to the target location
	public function largeImage(){

		if (!@copy('/var/tmp/tmp0_' . $this->largeImage, $this->targetDir . '/lg_' . $this->largeImage)){

			die("Imagemagick: Large file copy failed");

		}

		return 'lg_' . $this->largeImage;

	}


	//	Function: setTargetDir, Purpose: sets dir where images are saved, httpd user must have +w access
	public function setTargetDir($target){

		if ($target == ''){

			$this->targetDir = $this->tempDir;

		}
		else {

			$this->targetDir = $target;

		}

		if ($this->verbose){

			echo "Set target dir to '{$this->targetDir}'\n";

		}
	}


	//	Function: getFilename, Purpose: returns current temp filename
	public function getFilename(){

		return $this->tempFile;

	}


	//	Function: getSize, Purpose: returns an array of the image's dimensions [0] = width, [1] = height
	public function getSize(){

		$command = $this->imageMagickDir."/identify -verbose '".$this->tempDir.'/tmp'.$this->count.'_'.$this->tempFile."'";
		exec($command, $returnarray, $returnvalue);

		if ($returnvalue){

			die("ImageMagick: Corrupt image");

		}
		else {

			$num = count($returnarray);

			for($i=0;$i<$num;$i++){

				$returnarray[$i] = trim($returnarray[$i]);

			}

			$this->imageData = $returnarray;

		}

		$num = count($this->imageData);

		for ($i=0;$i<$num;$i++){

			if (eregi('Geometry', $this->imageData[$i])){

				$tmp1 = explode(' ', $this->imageData[$i]);
				$tmp2 = explode('x', $tmp1[1]);
				$this->size = $tmp2;
				return $tmp2;

			}
		}
	}


	//	Function: resize, Purpose: resize image to required dimensions, $how == 'keep_aspect' or 'fit' (imagick version, ongoing depreciation)
	public function resize($x_size, $y_size, $how='keep_aspect'){

		if ($this->verbose){

			echo "resize:\n";

		}

		$method = $how=='keep_aspect'?'>':($how=='fit'?'!':'');

		if ($this->verbose){

			echo "  resize method: {$how}\n";

		}

		$command = "{$this->imageMagickDir}/convert -geometry '{$x_size}x{$y_size}{$method}' '{$this->tempDir}/tmp{$this->count}_{$this->tempFile}' '{$this->tempDir}/tmp".++$this->count."_{$this->tempFile}'";

		if ($this->verbose){

			echo "  Command: {$command}\n";

		}

		exec($command, $returnarray, $returnvalue);

		if ($returnvalue){

			$this->error .= "ImageMagick: resize failed\n";

			if ($this->verbose){

				echo "resize failed\n";

			}
		}
		else {

			$this->fileHistory[] = $this->tempDir.'/tmp'.$this->count.'_'.$this->tempFile;

		}
	}


	//	Function: createThumb, Purpose: resize image to required dimensions (gd version, ongoing implementation)
	public function createThumb($name,$filename,$width,$height){

		$src_img = imagecreatefromjpeg($name);

		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);

		if ($old_x > $old_y){

			$thumb_w = $width;
			$thumb_h = $old_y * ($height / $old_x);

		}

		if ($old_x < $old_y){

			$thumb_w = $old_x * ($width / $old_y);
			$thumb_h = $height;

		}

		if ($old_x == $old_y){

			$thumb_w = $width;
			$thumb_h = $height;

		}

		$dst_img = imagecreatetruecolor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);
		imagejpeg($dst_img,$filename);
		imagedestroy($dst_img); 
		imagedestroy($src_img);

	}


	//	Function: square, Purpose: makes the image square, $how == 'center', 'left' or 'right'
	public function square($how='center'){

		$this->size = $this->getSize();

		if ($how == 'center'){

			if ($this->size[0] > $this->size[1]){

				$line = $this->size[1].'x'.$this->size[1].'+'.round((($this->size[0] - $this->size[1]) / 2)).'+0';

			}
			else {

				$line = $this->size[0].'x'.$this->size[0].'+0+'.round((($this->size[1] - $this->size[0])) / 2);

			}
		}

		if ($how == 'left'){

			if ($this->size[0] > $this->size[1]){

				$line = $this->size[1].'x'.$this->size[1].'+0+0';

			}
			else {

				$line = $this->size[0].'x'.$this->size[0].'+0+0';

			}
		}

		if ($how == 'right'){

			if ($this->size[0] > $this->size[1]){

				$line = $this->size[1].'x'.$this->size[1].'+'.($this->size[0]-$this->size[1]).'+0';

			}
			else {

				$line = $this->size[0].'x'.$this->size[0].'+0+'.($this->size[1] - $this->size[0]);

			}
		}

		$command = "{$this->imageMagickDir}/convert -crop '{$line}' '{$this->tempDir}/tmp{$this->count}_{$this->tempFile}' '{$this->tempDir}/tmp".++$this->count."_{$this->tempFile}'";

		if ($this->verbose){

			echo "square:\n";
			echo "  Method: {$how}\n";
			echo "  Command: {$command}\n";

		}

		exec($command, $returnarray, $returnvalue);

		if ($returnvalue){

			$this->error .= "ImageMagick: square failed\n";

			if ($this->verbose){

				echo "square failed\n";

			}
		}
		else {

			$this->fileHistory[] = $this->tempDir.'/tmp'.$this->count.'_'.$this->tempFile;

		}
	}


	//	Function: convert, Purpose: converts the uploaded image to any filetype, determined by extension
	public function convert($format='jpg'){

		if ($this->verbose){

			echo "convert:\n";

		}

		$name = explode('.' , $this->tempFile);
		$name = "{$name[0]}.{$format}";

		if ($this->verbose){

			echo "  Desired format: {$format}\n";
			echo "  Constructed filename: {$name}\n";

		}

		$command = "{$this->imageMagickDir}/convert -colorspace RGB -quality {$this->jpgQuality} '{$this->tempDir}/tmp{$this->count}_{$this->tempFile}' '{$this->tempDir}/tmp".++$this->count."_{$name}'";

		if ($this->verbose){

			echo "  Command: {$command}\n";

		}

		exec($command, $returnarray, $returnvalue);
		$this->tempFile = $name;

		if ($returnvalue){

			$this->error .= "ImageMagick: convert failed\n";

			if ($this->verbose){

				echo "convert failed\n";

			}
		}
		else {

			$this->fileHistory[] = $this->tempDir.'/tmp'.$this->count.'_'.$this->tempFile;

		}
	}


	//	Function: save, Purpose: saves the image to $this->targetDir
	public function save($prefix='') {

		if ($this->verbose) {

			echo "save:\n";

		}
		if (!@copy($this->tempDir.'/tmp'.$this->count.'_'.$this->tempFile, $this->targetDir.'/'.$prefix.$this->tempFile)) {

			$this->error .= "ImageMagick: Couldn't save to {$this->targetDir}/'{$prefix}{$this->tempFile}\n";

			if ($this->verbose) {

				echo "save failed to {$this->targetDir}/{$prefix}{$this->tempFile}\n";

			}
		}
		else {

			if ($this->verbose) {

				echo "  saved to {$this->targetDir}/{$prefix}{$this->tempFile}\n";

			}
		}

		return $prefix.$this->tempFile;

	}


	//	Function: cleanup, Purpose: removes all contents of $this->tempdir (temp files etc)
	public function cleanup($offset = 0) {

		if ($this->verbose) {

			echo "cleanup:\n";

		}

		$num = count($this->fileHistory);

		for ($i=$offset;$i<$num;$i++) {

			if (!@unlink($this->fileHistory[$i])) {

				$this->error .= "ImageMagick: Removal of temporary file '{$this->fileHistory[$i]}' failed\n";

				if ($this->verbose) {

					echo "  Removal of temporary file '{$this->fileHistory[$i]}' failed\n";

				}
			}
			else {

				if ($this->verbose) {

					echo "  Deleted temp file: {$this->fileHistory[$i]}\n";

				}
			}
		}

		if ($this->verbose) {

			echo '</pre>';

		}
	}
}

?>