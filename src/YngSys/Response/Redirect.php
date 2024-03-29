<?php
declare(strict_types = 1);

namespace Yng\Response;

use Yng\Cookie;
use Yng\Request;
use Yng\Response;
use Yng\Session;

/**
 * 重定向响应
 */
class Redirect extends Response
{

    protected $request;

    public function __construct(Cookie $cookie, Request $request, Session $session, $data = '', int $code = 302)
    {
        $this->init((string) $data, $code);

        $this->cookie  = $cookie;
        $this->request = $request;
        $this->session = $session;

        $this->cacheControl('no-cache,must-revalidate');
    }

    public function data($data)
    {
        $this->header['Location'] = $data;
        return parent::data($data);
    }

    /**
     * 处理数据
     * @access protected
     * @param  mixed $data 要处理的数据
     * @return string
     */
    protected function output($data): string
    {
        return '';
    }

    /**
     * 重定向传值（通过Session）
     * @access protected
     * @param  string|array  $name 变量名或者数组
     * @param  mixed         $value 值
     * @return $this
     */
    public function with($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->session->flash($key, $val);
            }
        } else {
            $this->session->flash($name, $value);
        }

        return $this;
    }

    /**
     * 记住当前url后跳转
     * @access public
     * @return $this
     */
    public function remember($complete = false)
    {
        $this->session->set('redirect_url', $this->request->url($complete));

        return $this;
    }

    /**
     * 跳转到上次记住的url
     * @access public
     * @return $this
     */
    public function restore()
    {
        if ($this->session->has('redirect_url')) {
            $this->data = $this->session->get('redirect_url');
            $this->session->delete('redirect_url');
        }

        return $this;
    }
}
