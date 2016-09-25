<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Http\Bag;

/**
 * Class ServerBag
 *
 * @package FastD\Http\Bag
 */
class ServerBag extends Bag
{
    /**
     * @var string
     */
    protected $pathInfo;

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var HeaderBag
     */
    protected $headerBag;

    /**
     * ServerAttribute constructor.
     * @param array $bag
     */
    public function __construct(array $bag)
    {
        parent::__construct($bag);
    }

    /**
     * @return HeaderBag
     */
    public function getHeaderBag()
    {
        return $this->headerBag;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->hasGet('REQUEST_SCHEME', 'http');
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->hasGet('SERVER_PORT', ('https' === $this->getScheme() ? 443 : 80));
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return 'https' === $this->getScheme() ? true : false;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->hasGet('SERVER_PROTOCOL', 'HTTP/1.1');
    }

    /**
     * @return string
     */
    public function getProtocolVersion()
    {
        $protocol = $this->getProtocol();

        return substr($protocol, 5);
    }

    /**
     * @return array|int|string
     */
    public function getHost()
    {
        return $this->hasGet('SERVER_NAME', $this->hasGet('HTTP_HOST', 'localhost'));
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        if ($this->pathInfo) {
            return $this->pathInfo;
        }

        $this->pathInfo = $this->hasGet('PATH_INFO', $this->preparePathInfo());

        if ('' != pathinfo($this->pathInfo, PATHINFO_EXTENSION)) {
            $this->pathInfo = substr($this->pathInfo, 0, strpos($this->pathInfo, '.'));
        }

        $this->pathInfo = empty($this->pathInfo) ? '/' : $this->pathInfo;

        return $this->pathInfo;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->hasGet('REQUEST_URI', $this->prepareRequestUri());
        }

        return $this->requestUri;
    }

    /**
     * @return bool|string
     */
    protected function prepareRequestUri()
    {
        return $this->get('PHP_SELF');
    }

    /**
     * The symfony prepareBaseUrl method.
     *
     * @return array|int|string
     */
    protected function prepareBaseUrl()
    {
        $filename = $this->has('SCRIPT_FILENAME') ? basename($this->get('SCRIPT_FILENAME')) : '';

        if ($this->has('SCRIPT_NAME') && basename($this->get('SCRIPT_NAME') === $filename)) {
            $baseUrl = $this->get('SCRIPT_NAME');
        } elseif ($this->has('PHP_SELF') && basename($this->get('PHP_SELF') === $filename)) {
            $baseUrl = $_SERVER['PHP_SELF'];
        } elseif ($this->has('ORIG_SCRIPT_NAME') && basename($this->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->get('ORIG_SCRIPT_NAME'); // 1and1 shared hosting compatibility
        } else {
            $path    = $this->hasGet('PHP_SELF', '');
            $file    = $this->hasGet('SCRIPT_FILENAME', '');
            $segs    = explode('/', trim($file, '/'));
            $segs    = array_reverse($segs);
            $index   = 0;
            $last    = count($segs);
            $baseUrl = '';
            do {
                $seg     = $segs[$index];
                $baseUrl = '/' . $seg . $baseUrl;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
        }

        $requestUri = $this->getRequestUri();

        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        if (0 === strpos($requestUri, dirname($baseUrl))) {
            return ('' === ($baseUrl = rtrim(dirname($baseUrl), '/'))) ? '/' : $baseUrl;
        }

        $truncatedRequestUri = $requestUri;
        if (($pos = strpos($requestUri, '?')) !== false) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
            return '/';
        }

        if ((strlen($requestUri) >= strlen($baseUrl))
            && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
        {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    /**
     * @return string
     */
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri()) || $baseUrl === $requestUri) {
            return '/';
        }

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if (false === ($pathInfo = substr($requestUri, strlen($baseUrl)))) {
            return '/';
        }

        return $pathInfo;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }
        return $this->baseUrl;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->hasGet('REQUEST_METHOD', 'GET');
    }

    /**
     * @param $method
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }
}