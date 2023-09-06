<?php
declare(strict_types = 1);

namespace Yng\Contract;

/**
 * 视图驱动接口
 */
interface TemplateHandlerInterface
{
    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists(string $template): bool;

    /**
     * 渲染模板文件
     * @access public
     * @param  string $template 模板文件
     * @param  array  $data 模板变量
     * @return void
     */
    public function render(string $template, array $data = []): void;

    /**
     * 渲染模板内容
     * @access public
     * @param  string $content 模板内容
     * @param  array  $data 模板变量
     * @return void
     */
    public function display(string $content, array $data = []): void;

    /**
     * 配置模板引擎
     * @access private
     * @param  array $config 参数
     * @return void
     */
    public function config(array $config): void;

    /**
     * 获取模板引擎配置
     * @access public
     * @param  string $name 参数名
     * @return void
     */
    public function getConfig(string $name);
}
