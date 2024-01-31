<?php
class SimpleTemplateEngine
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function render(string $template, array $data = []): string
    {
        $templatePath = $this->resolve($template);

        if (!file_exists($templatePath)) {
            return "Template not found: $template";
        }

        ob_start();
        extract($data, EXTR_SKIP);
        include $templatePath;
        return ob_get_clean();
    }

    private function resolve(string $path): string
    {
        return "{$this->basePath}/{$path}.php";
    }
}

// Example usage:
$engine = new SimpleTemplateEngine(__DIR__);
echo $engine->render('temp', ['name' => 'World']);