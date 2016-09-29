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
 * Class HeaderBag
 *
 * @package FastD\Http\Bag
 */
class HeaderBag extends Bag
{
    protected $headers = [];

    /**
     * HeaderBag constructor.
     *
     * @param array $headers
     */
    public function __construct(array $headers)
    {
        $bag = [];

        $self = $this;

        array_walk($headers, function ($value, $key) use (&$bag, $self) {
            $value = explode(',', $value);
            $self->headers[$key] = $value;
            $key = str_replace(['http_', '_'], ['', '-'], strtolower($key));
            $bag[$key] = $value;
        });

        parent::__construct($bag);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $name = str_replace('_', '-', strtolower($name));

        $info = explode('-', $name);

        array_map(function ($value) {
            return ucfirst($value);
        }, $info);

        $value = explode(',', $value);

        $this->headers[implode('-', $info)] = $value;

        parent::set($name, $value);

        return $this;
    }

    /**
     * @param $name
     * @param bool $raw
     * @param null $callback
     * @return mixed|string
     */
    public function get($name, $raw = false, $callback = null)
    {
        return parent::get(strtolower(str_replace('_', '-', $name)), $raw, $callback);
    }

    /**
     * @return null|string
     */
    public function getUserAgent()
    {
        return $this->hasGet('user-agent', null);
    }

    /**
     * @return null|string
     */
    public function getAccept()
    {
        return $this->hasGet('accept', null);
    }

    /**
     * @return null|string
     */
    public function getAcceptEncoding()
    {
        return $this->hasGet('accept-encoding', null);
    }

    /**
     * @return null|string
     */
    public function getAcceptLanguage()
    {
        return $this->hasGet('accept-language', null);
    }

    /**
     * @return null|string
     */
    public function getReferer()
    {
        return $this->hasGet('referer', null);
    }

    /**
     * @return null|string
     */
    public function getHost()
    {
        return $this->hasGet('host', null);
    }

    /**
     * @return null|string
     */
    public function getConnection()
    {
        return $this->hasGet('connection', null);
    }

    /**
     * @return null|string
     */
    public function getCacheControl()
    {
        return $this->hasGet('cache-control', null);
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return 'xmlhttprequest' === strtolower($this->hasGet('x-requested-with', ''));
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        foreach ([
                     'client-ip',
                     'x-forwarded-for',
                     'x-forwarded',
                     'forwarded-for',
                     'forwarded',
                     'remote-addr'
                 ] as $value) {
            if ($this->has($value)) {
                return $this->get($value);
            }
        }

        return 'unknown';
    }

    /**
     * Return http response header.
     *
     * @return string
     */
    public function __toString()
    {
        $header = '';

        foreach ($this->headers as $name => $value) {
            $header .= sprintf('%s: %s', ucfirst($name), implode(',', $value)) . "\r\n";
        }

        return $header;
    }
}