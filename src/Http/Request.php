<?php
/**
 * Created by PhpStorm.
 * User: janhuang
 * Date: 15/1/28
 * Time: 下午3:48
 * Github: https://www.github.com/janhuang 
 * Coding: https://www.coding.net/janhuang
 * sf: http://segmentfault.com/u/janhuang
 * Blog: http://segmentfault.com/blog/janhuang
 */

namespace FastD\Http;

use FastD\Http\File\Upload\UploadInterface;
use FastD\Http\Session\Session;
use FastD\Http\Attribute\FilesAttribute;
use FastD\Http\Attribute\CookiesAttribute;
use FastD\Http\Attribute\HeaderAttribute;
use FastD\Http\Attribute\QueryAttribute;
use FastD\Http\Attribute\RequestAttribute;
use FastD\Http\Attribute\ServerAttribute;
use FastD\Http\Session\Storage\SessionStorageInterface;

/**
 * Class Request
 *
 * @package FastD\Http
 */
class Request
{
    /**
     * $_GET
     *
     * @var QueryAttribute
     */
    public $query;

    /**
     * $_POST
     *
     * @var RequestAttribute
     */
    public $request;

    /**
     * $_FILES
     *
     * @var FilesAttribute
     */
    public $files;

    /**
     * $_COOKIE
     *
     * @var CookiesAttribute
     */
    public $cookies;

    /**
     * $_SERVER
     *
     * @var ServerAttribute
     */
    public $server;

    /**
     * Http request headers or response headers.
     *
     * new HeaderAttribute($sever->getHeaders());
     *
     * @var HeaderAttribute
     */
    public $header;

    /**
     * Session management.
     *
     * $_SESSION
     *
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Request
     */
    private static $requestFactory;

    /**
     * The http request is has once request object.
     *
     * @param $get
     * @param $post
     * @param $files
     * @param $cookie
     * @param $server
     */
    public function __construct(array $get = [], array $post = [], array $files = [], array $cookie = [], array $server = [])
    {
        $this->initialize($get, $post, $files, $cookie, $server);
    }

    /**
     * The http request is has once request object.
     *
     * @param $get
     * @param $post
     * @param $files
     * @param $cookie
     * @param $server
     */
    public function initialize(array $get = [], array $post = [], array $files = [], array $cookie = [], array $server = [])
    {
        $this->query    = new QueryAttribute($get);
        $this->request  = new RequestAttribute($post);
        $this->files    = new FilesAttribute($files);
        $this->cookies  = new CookiesAttribute($cookie);
        $this->server   = new ServerAttribute($server);
        $this->header   = $this->server->getHeader();
    }

    /**
     * @return string
     */
    public function getSchemeAndHost()
    {
        return $this->getScheme() . '://' . $this->getHost();
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->server->getScheme();
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->server->getHost();
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->server->isSecure();
    }

    /**
     * Get user client request ip.
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->server->getClientIp();
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->server->getRequestUri();
    }

    /**
     * @return bool|string
     */
    public function getBaseUrl()
    {
        return $this->server->getBaseUrl();
    }

    /**
     * @return string
     */
    public function getPathInfo()
    {
        return $this->server->getPathInfo();
    }

    /**
     * @return float
     */
    public function getRequestTime()
    {
        return $this->server->getRequestTime();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->server->getMethod();
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->server->getFormat();
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return $this->header->isXmlHttpRequest();
    }

    /**
     * @param $method
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * @param SessionStorageInterface $sessionStorageInterface
     * @return Session
     */
    public function getSessionHandle(SessionStorageInterface $sessionStorageInterface = null)
    {
        if (null === $this->session) {
            $this->session = new Session($sessionStorageInterface);
        }

        return $this->session;
    }

    /**
     * @param $name
     * @return array|int|string
     */
    public function getSession($name)
    {
        return $this->getSessionHandle()->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setSession($name, $value)
    {
        return $this->getSessionHandle()->set($name, $value);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSession($name)
    {
        return $this->getSessionHandle()->has($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function clearSession($name)
    {
        return $this->getSessionHandle()->clear($name);
    }

    /**
     * @param $name
     * @return Cookie\Cookie
     */
    public function getCookie($name)
    {
        return $this->cookies->get($name);
    }

    /**
     * @param        $name
     * @param null   $value
     * @param int    $expire
     * @param string $path
     * @param null   $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     * @return CookiesAttribute
     */
    public function setCookie($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        return $this->cookies->set($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasCookie($name)
    {
        return $this->cookies->has($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function clearCookie($name)
    {
        return $this->cookies->clear($name);
    }

    /**
     * @param UploadInterface $uploadInterface
     * @param array $config
     * @return UploadInterface
     */
    public function getUploader(UploadInterface $uploadInterface = null, array $config = [])
    {
        return $this->files->getUploader($uploadInterface, $config);
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->header->getUserAgent();
    }

    /**
     * @return resource|string
     */
    public function getContent()
    {
        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * Create one http request handle.
     *
     * @return Request|static
     */
    public static function createRequestHandle()
    {
        if (null === self::$requestFactory) {
            self::$requestFactory = new static($_GET, $_POST, $_FILES, $_COOKIE, $_SERVER);

            if (in_array(self::$requestFactory->server->hasGet('REQUEST_METHOD', 'GET'), array('PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD'))
            ) {
                parse_str(self::$requestFactory->getContent(), $arguments);
                self::$requestFactory->request = new RequestAttribute($arguments);
            }
        }

        return self::$requestFactory;
    }
}