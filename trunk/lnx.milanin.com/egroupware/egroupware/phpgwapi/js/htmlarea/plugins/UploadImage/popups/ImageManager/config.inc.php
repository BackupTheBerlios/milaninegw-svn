<?

//************************** BEGIN CONFIGURATION *****************************//

//example, this is the actual file system path
//of the web server document root. e.g.
// Filesystem == /home/web/www.yourdomain.com 
$BASE_DIR = $GLOBALS[UploadImageBaseDir];
$BASE_URL = $GLOBALS[UploadImageBaseURL];
$BASE_ROOT = $GLOBALS[UploadImageRelativePath];

var_dump($BASE_DIR);
var_dump($BASE_URL);
var_dump($BASE_ROOT);

//$BASE_DIR = $_SERVER['DOCUMENT_ROOT'];
//$BASE_DIR = '/home/www';

//the path where the browser sees the document root (i.e. http://www.yourdomain.com/)
//$BASE_URL = 'https://192.168.0.2/';

//this is where the images will be stored relative to the $BASE_DIR (and $BASE_URL)
//this directory MUST be readable AND writable by the web server.
//$BASE_ROOT = ''; 

//The image manipulation library to use, either GD or ImageMagick or NetPBM
//valid definitions are 'GD' or 'IM' or 'NetPBM'.
define('IMAGE_CLASS', 'GD'); // 

//After defining which library to use, if it is NetPBM or IM, you need to
//specify where the binary for the selected library are. And of course
//your server and PHP must be able to execute them (i.e. safe mode is OFF).
//If you have safe mode ON, or don't have the binaries, your choice is
//GD only. GD does not require the following definition.
define('IMAGE_TRANSFORM_LIB_PATH', '/usr/bin/netpbm/');
//define('IMAGE_TRANSFORM_LIB_PATH', '"D:\\Program Files\\ImageMagick\\');


//In safe mode, directory creation is not permitted.
$SAFE_MODE = false;

//************************** END CONFIGURATION *****************************//

$IMG_ROOT = $BASE_ROOT;

if(strrpos($BASE_DIR, '/')!= strlen($BASE_DIR)-1) 
	$BASE_DIR .= '/';

if(strrpos($BASE_URL, '/')!= strlen($BASE_URL)-1) 
$BASE_URL .= '/';

//Built in function of dirname is faulty
//It assumes that the directory nane can not contain a . (period)
function dir_name($dir) 
{
	$lastSlash = intval(strrpos($dir, '/'));
	if($lastSlash == strlen($dir)-1){
		return substr($dir, 0, $lastSlash);
	}
	else
		return dirname($dir);
}

?>
