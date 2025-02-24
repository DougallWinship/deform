<?php
// basic routing & bare-bones html template
use Deform\Html\Html;

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

const CLOUDFLARE_NORMALISE_URL = "https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css";

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

$requestUri = trim($_SERVER['REQUEST_URI'],'/');
$path = parse_url($requestUri, PHP_URL_PATH);

include dirname(__DIR__, 3) ."/vendor/autoload.php";

$contents = null;

if (!$path || $path==='index') {
    $path='/';
    $dir  = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::KEY_AS_FILENAME | RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveCallbackFilterIterator($dir,  function (\SplFileInfo $current, string $key, \RecursiveDirectoryIterator $iterator) {
        if ($iterator->hasChildren()) {
            return true;
        }
        if (
            $current->isFile()
            && str_ends_with($current->getFilename(), '.php')
            && $current->getFilename()!='index.php'
            && $current->getFilename()!='router.php'
        ) {
            return true;
        }
        return false;
    });
    $tree = new RecursiveTreeIterator($files);
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_LEFT,' ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '│ ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '├ ');
    $tree->setPrefixPart(\RecursiveTreeIterator::PREFIX_END_LAST, '└ ');

    $contents ="<nav><pre>";
    $contents.= "<a href='/'>index.php</a><br>";
    foreach ($tree as $filename => $withPath) {
        $pos = strpos($withPath,__DIR__) + strlen(__DIR__);
        $filePath = substr($withPath, $pos, -strlen($filename));
        if (str_ends_with($filename, '.php')) {
            $contents.= $tree->getPrefix()."<a href='".$filePath.substr($filename,0,-4)."'>".$filename. "</a>".PHP_EOL;
        }
        else {
            $contents.= $tree->getPrefix().$filename.PHP_EOL;
        }
    }
    $contents.="</pre></nav>";

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
        unset($_GET['path']);// no idea why php is making this a thing
        ob_start();
        include $file;
        $contents = ob_get_clean();
    }
}

renderLayout($path, $contents);

function renderLayout($title, $contents, $defaultCss=null)
{
    $head = [
        Html::title()->add($title),
        Html::meta(['charset' => 'utf-8']),
        Html::link(['rel' => 'stylesheet', 'href' => CLOUDFLARE_NORMALISE_URL]),
        Html::link(['rel' => 'stylesheet', 'href' => '/styles.css?version='.uniqid()]),
        Html::link(['rel' => 'icon', 'type' => 'image/x-icon', 'href'=>'http://deform-tests.test/favicon.ico']),
        Html::style()->add("{text-decoration:none}")
    ];
    $html = Html::html(['lang' => 'en'])->add([
        Html::head()->add($head),
        Html::body()->add($contents)
    ]);
    echo "<!DOCTYPE html>" . $html;
}
