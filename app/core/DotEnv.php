<?php
/**
 * Class DotEnv để tải biến môi trường từ file .env
 */
class DotEnv
{
    /**
     * Đường dẫn đến file .env
     * @var string
     */
    protected $path;

    /**
     * Constructor
     * @param string $path Đường dẫn đến file .env
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            return;
        }

        $this->path = $path;
    }

    /**
     * Tải biến môi trường từ file .env
     */
    public function load(): void
    {
        if (!is_readable($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
} 