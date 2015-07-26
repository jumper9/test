<?PHP 
namespace wunderman\wunderforms;

include(__DIR__."/security.php");

define("APP_NAMESPACE","\\wunderman\\wunderforms");
define("HTML_PREFIX","");

include(APP_PATH."/../conf-server/conf-server.php");

date_default_timezone_set('America/Buenos_Aires');

define("VERSION", "v1.0.0");
define ("S3_PREFIX","https://wunderforms-prod.s3.amazonaws.com");

