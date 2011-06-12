<?php
class FileManager{
	private $size;
	private $file_size;
	private $uploaded_file=null;
	private $isImage=false;
	private $mime;
	private $width;
	private $height;
	private $file;
	private $filename;
	
	
	function __construct($file){
		if(isset($_FILES[$file]) and $_FILES[$file]['tmp_name']){
			$this->filename=$_FILES[$file]['name'];
			$this->uploaded_file=$_FILES[$file]['tmp_name'];
			$this->file_size=$_FILES[$file]['size'];
			$this->mime=mime_content_type($_FILES[$file]['tmp_name'],$_FILES[$file]['name']);
			$this->size=getimagesize($_FILES[$file]['tmp_name']);
			
			$types=array('image/jpeg'=>'jpeg','image/jpg'=>'jpeg','image/png'=>'png','image/gif'=>'gif');
				
			if($this->size and isset($types[$this->mime])){
				$isImage=true;
				$this->width=$this->size[0];
				$this->height=$this->size[1];
				
				$imagecreatefrom='imagecreatefrom'.$types[$this->mime];
				$this->file=$imagecreatefrom($_FILES[$file]['tmp_name']);
			}
		}
	}
	
	function exist(){
		return $this->uploaded_file!=null;
	}
	
	function getSize(){
		return $this->file_size;
	}
	
	function getFileName(){
		return $this->filename;
	}
	
	function getMime(){
		return $this->mime;
	}
	
	function getImage(){
		return $this->mime;
	}
	
	function getWidth(){
		return $this->width;
	}
	
	function getHeight(){
		return $this->height;
	}
	
	function isImage(){
		return $this->isImage;
	}
	
	
	function resize($width=null,$height=null){
		if($width and (!$height or ($width/$height<$this->width/$this->height)))
			$height=$this->height*$width/$this->width;
		else $width=$this->width*$height/$this->height;
		
		$temp = imagecreatetruecolor($width, $height);
		imagecopyresampled($temp, $this->file, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		$this->file=$temp;
		$this->width=$width;
		$this->height=$height;
	}
	
	function applyWatermark(){
		$watermark=imagecreatefrompng('img/watermark.png');
		$size=getimagesize('img/watermark.png');
		imagecopyresampled($this->file, $watermark, 0, 0, 0, 0, $this->width, $this->height,$size[0], $size[1]);
	}
	
	function getContent(){
		return file_get_contents($this->uploaded_file);
	}
	
	function saveTo($file){
		return move_uploaded_file($this->uploaded_file,$file);
	}
	
	function getJpeg($quality=90){
		$tmp=tempnam(null,null);
		imagejpeg($this->file,$tmp,$quality);
		return file_get_contents($tmp);
	}
}

	
if(!function_exists('mime_content_type')) {

    function mime_content_type($dir,$filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
		$arr=explode('.',$filename);
		$ext=array_pop($arr);
        $ext = strtolower($ext);
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

?>