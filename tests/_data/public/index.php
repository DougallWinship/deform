<?php
// basic routing & bare-bones html template
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

set_error_handler(function(int $errNo, string $errStr, string $errFile, int $errLine) {
    throw new \ErrorException('ERROR : '.$errStr, $errNo,0, $errFile, $errLine);
});
set_exception_handler(function(\Throwable $exc) {
    ob_end_clean();
    http_response_code(500);
    $errorMessage = "<div style='padding:4px;background-color:#ff6666;color:white'>"
        . "[" . $exc->getCode() . "] " . $exc->getMessage() . " : " . $exc->getFile() . "(" . $exc->getLine() . ")<br>"
        . "<pre style='margin:0;color:#ddd'>" . $exc->getTraceAsString() . "</pre></div>";
    renderLayout("Exception : ".$exc->getMessage(), $errorMessage);
});

$path = trim($_SERVER['REQUEST_URI'],'/');

include dirname(dirname(dirname(__DIR__)))."/vendor/autoload.php";

$contents = null;

if (!$path || $path==='index') {
    $contents = "<h1>Home</h1>";
    $dir  = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveCallbackFilterIterator($dir,  function (\SplFileInfo $current, string $key, \RecursiveDirectoryIterator $iterator) {
        if ($iterator->hasChildren()) {
            return true;
        }
        if ($current->isFile() && substr($current->getFilename(),-4)=='.php') {
            return true;
        }
        return false;
    });
    $tree = new RecursiveTreeIterator($files);
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_LEFT,' ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '│ ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '├ ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_LAST, '└ ');

    $contents.="<pre>";
    foreach ($tree as $filename => $withPath) {
        $pos = strpos($withPath,__DIR__) + strlen(__DIR__);
        $path = substr($withPath, $pos, -strlen($filename));
        if (substr($filename,-4)=='.php') {
            $contents.= $tree->getPrefix()."<a href='".$path.substr($filename,0,-4)."'>".$filename. "</a>".PHP_EOL;
        }
        else {
            $contents.= $tree->getPrefix().$filename.PHP_EOL;
        }
    }
    $contents.="</pre>";

}
else {
    $file = __DIR__ . DIRECTORY_SEPARATOR . $path . '.php';
    if (!file_exists($file)) {
        $file = __DIR__ . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . 'index.php';
        if (!file_exists($file)) {
            http_response_code(404);
            $contents = "404 : failed to route path";
        }
    }
    if ($contents===null) {
        ob_start();
        include $file;
        $contents = ob_get_clean();
    }
}
renderLayout($path, $contents);

function renderLayout($title, $contents) {
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>$title</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>
<body>
$contents
</body>
</html>
HTML;
}
?>
