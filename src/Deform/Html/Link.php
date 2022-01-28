<?php

declare(strict_types=1);

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
     * @return Link
     */
    public static function url(string $url = null): Link
    {
        $instance = new Link();
        $instance->setUrl($url);
        return $instance;
    }

    /**
     * @param array $options optional attributes
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        parent::__construct("a", $options);
    }

    /**
     * @param string $url
     * @return Link $this
     * @throws \Exception
     */
    public function setUrl(string $url): Link
    {
        $urlParts = parse_url($url);
        if ($urlParts === false) {
            throw new \Exception("Unable to parse url : " . $url);
        }
        $this->urlParts = $urlParts;
        return $this;
    }

    /**
     * @param string $scheme
     * @return Link $this
     */
    public function setScheme(string $scheme): Link
    {
        $this->urlParts["scheme"] = $scheme;
        return $this;
    }

    /**
     * alias of set scheme
     * @param string $protocol
     * @return link
     */
    public function setProtocol(string $protocol): Link
    {
        return $this->setScheme($protocol);
    }

    /**
     * @param string $host
     * @return Link $this
     */
    public function setHost(string $host): Link
    {
        $this->urlParts["host"] = $host;
        return $this;
    }

    /**
     * @param string $port
     * @return Link $this
     */
    public function setPort(string $port): Link
    {
        $this->urlParts["port"] = $port;
        return $this;
    }

    /**
     * @param string $user
     * @param bool|string $password
     * @return Link $this
     */
    public function setUser(string $user, $password = false): Link
    {
        $this->urlParts["username"] = $user;
        if ($password) {
            $this->urlParts["password"] = $password;
        }
        return $this;
    }

    /**
     * @param string $path
     * @return Link $this
     */
    public function setPath(string $path): Link
    {
        $this->urlParts["path"] = $path;
        return $this;
    }

    /**
     * @param string $query
     * @return Link $this
     */
    public function setQuery(string $query): Link
    {
        $this->urlParts["query"] = $query;
        return $this;
    }

    /**
     * @param string $fragment
     * @return Link $this
     */
    public function setFragment(string $fragment): Link
    {
        $this->urlParts["fragment"] = $fragment;
        return $this;
    }

    /**
     * @param string $text
     * @return Link $this
     * @throws \Exception
     */
    public function text(string $text): Link
    {
        $this->reset($text);
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        if (!isset($this->urlParts['host'])) {
            throw new \Exception("You can't generate a link without specifying a host");
        }
        $url = isset($this->urlParts["scheme"])
            ? $this->urlParts["scheme"] . "://"
            : "https://";
        if (isset($this->urlParts["username"])) {
            $url = isset($this->urlParts["password"])
                ? $this->urlParts["username"] . ":" . $this->urlParts["password"] . "@"
                : $this->urlParts["username"] . "@";
        }
        $url .= $this->urlParts["host"];
        $url .= (isset($this->urlParts["port"]))
            ? ":" . $this->urlParts["port"]
            : "";
        $url .= (isset($this->urlParts["path"]))
            ? $this->urlParts["path"]
            : "";
        $url .= (isset($this->urlParts["query"]))
            ? "?" . $this->urlParts["query"]
            : "";
        $url .= (isset($this->urlParts["fragment"]))
            ? "#" . $this->urlParts["fragment"]
            : "";
        $this->href($url);
        if (!$this->hasChildren()) {
            $this->add($url);
        }
        return parent::__toString();
    }
}
