<?php
use Deform\Html\Link;

class Deform
{

    /** @var Deform */
    private static $instance;

    /**
     * @param string $projectRootDir
     * @param string $bootstrapFile
     * @throws Exception
     */
    public function __construct(string $projectRootDir, string $bootstrapFile)
    {
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        spl_autoload_register([$this, 'loadClass']);
        $composerAutoloadFile = $projectRootDir.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
        if (is_file($composerAutoloadFile)) {
            include $composerAutoloadFile;
        }
        else {
            throw new \Exception("Failed to find composer autoloaded!");
        }
        if (!file_exists($bootstrapFile)) {
            throw new \Exception("Failed to find bootstrap file : ".$bootstrapFile);
        }
        include $bootstrapFile;// ensures the bootstrap is in this class context!
    }

    /**
     * @param string $projectRootDir
     * @param string $bootstrapFile
     * @throws Exception
     */
    public static function init(string $projectRootDir, string $bootstrapFile)
    {
        if (self::$instance===null) {
            self::$instance = new self($projectRootDir, $bootstrapFile);
        }
    }

    /**
     * @param int $errNo
     * @param string $errStr
     * @param string $errFile
     * @param int $errLine
     * @throws ErrorException
     */
    public function errorHandler(int $errNo, string $errStr, string $errFile, int $errLine)
    {
        throw new \ErrorException('ERROR : '.$errStr, $errNo,0, $errFile, $errLine);
    }

    /**
     * @param Throwable $exc
     */
    public function exceptionHandler(Throwable $exc)
    {
        $this->renderException($exc);
        die();
    }

    /**
     * @param \Throwable $exc
     */
    protected function renderException(\Throwable $exc)
    {
        echo "<div style='padding:4px;background-color:#ff6666;color:white'>[" .$exc->getCode()."] ".$exc->getMessage()." : ".$exc->getFile()."(".$exc->getLine().")<br><pre>".$exc->getTraceAsString()."</pre></div>";
    }

    /**
     * @param string $class
     * @return bool
     */
    public function loadClass(string $class) : bool
    {
        if (DIRECTORY_SEPARATOR!='\\') {
            $class = str_replace('\\', '/', $class);
        }
        $file = dirname( __DIR__).DIRECTORY_SEPARATOR.$class.".php";
        if (file_exists($file)) {
            require $file;
            return true;
        }
        return false;
    }
}
