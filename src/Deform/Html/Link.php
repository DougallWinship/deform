<?php
namespace Deform\Html;

/**
 * for convenient link generation
 *
 * @method Link target($target)
 * @method Link media($media)
 * @method Link rel($rel)
 * @method Link type($charset)
 * @method Link href($url)
 */
class Link extends HtmlTag
{

    private array $urlParts = [];

    /**
     * @param string|null $url
     * @throws \Exception
     * @return \Deform\Html\Link
     */
    public static function url(string $url = null): Link
    {
        $instance = new Link();
        $instance->setUrl($url);
        return $instance;
    }

    /**
     * @param array $options optional attributes
     *
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        parent::__construct("a", $options);
    }

    /**
     * @param string $url
     *
     * @return Link $this
     * @throws \Exception
     */
    public function setUrl(string $url) : Link
    {
        $this->urlParts = parse_url($url);
        if($this->urlParts == false) {
            throw new \Exception("Unable to parse url : " . $url);
        }
        return $this;
    }

    /**
     * @param string $scheme
     *
     * @return Link $this
     */
    public function setScheme(string $scheme) : Link
    {
        $this->urlParts["scheme"] = $scheme;
        return $this;
    }

    /**
     * alias of set scheme
     *
     * @param string $protocol
     *
     * @return link
     */
    public function setProtocol(string $protocol) : Link
    {
        return $this->setScheme($protocol);
    }

    /**
     * @param string $host
     *
     * @return Link $this
     */
    public function setHost(string $host) : Link
    {
        $this->urlParts["host"] = $host;
        return $this;
    }

    /**
     * @param string $port
     *
     * @return Link $this
     */
    public function setPort(string $port) : Link
    {
        $this->urlParts["port"] = $port;
        return $this;
    }

    /**
     * @param string $user
     * @param bool|string $password
     *
     * @return Link $this
     */
    public function setUser(string $user, $password = false) : Link
    {
        $this->urlParts["username"] = $user;
        if($password) {
            $this->urlParts["password"] = $password;
        }
        return $this;
    }

    /**
     * @param string $path
     *
     * @return Link $this
     */
    public function setPath(string $path) : Link
    {
        $this->urlParts["path"] = $path;
        return $this;
    }

    /**
     * @param string $query
     *
     * @return Link $this
     */
    public function setQuery(string $query) : Link
    {
        $this->urlParts["query"] = $query;
        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return Link $this
     */
    public function setFragment(string $fragment) : Link
    {
        $this->urlParts["fragment"] = $fragment;
        return $this;
    }

    /**
     * @param string $text
     *
     * @return Link $this
     */
    public function text(string $text) : Link
    {
        $this->reset($text);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $url = isset($this->urlParts["scheme"]) ? $this->urlParts["scheme"] . "://" : "http://";
        if(isset($this->urlParts["username"])) {
            $url = isset($this->urlParts["password"]) ? $this->urlParts["username"] . ":" . $this->urlParts["password"] . "@" : $this->urlParts["username"] . "@";
        }
        $url .= (isset($this->urlParts["host"])) ? $this->urlParts["host"] : $_SERVER["SERVER_NAME"];
        $url .= (isset($this->urlParts["port"])) ? ":" . $this->urlParts["port"] : "";
        $url .= (isset($this->urlParts["path"])) ? $this->urlParts["path"] : "";
        $url .= (isset($this->urlParts["query"])) ? "?" . $this->urlParts["query"] : "";
        $url .= (isset($this->urlParts["fragment"])) ? "#" . $this->urlParts["fragment"] : "";
        $this->href($url);
        if($this->isEmpty()) {
            $this->add($url);
        }
        return parent::__toString();
    }
}
