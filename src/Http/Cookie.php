<?php

namespace Yng\Framework\Http;

class Cookie
{
    /**
     * @var string
     */
    protected string $name;
    /**
     * @var
     */
    protected       $value;
    /**
     * @var array
     */
    protected array $options;

    /**
     * @param string $name
     * @param        $value
     * @param array  $options
     */
    public function __construct(string $name, $value, array $options = [])
    {
        $this->name    = $name;
        $this->value   = $value;
        $this->options = array_merge(config('http.cookie'), $options);
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $cookie = $this->name . '=' . $this->value;
        foreach (array_filter($this->options) as $key => $value) {
            $cookie .= sprintf('; %s=%s', $key, $value);
        }
        return $cookie;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
