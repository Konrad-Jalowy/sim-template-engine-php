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

        // Load the content of the template file
        $templateContent = file_get_contents($templatePath);
        
        // Define the regular expression pattern for {** **}
        $pattern = '/\{\*\*(.*?)\*\*\}/';

        // Use preg_replace_callback with a closure
        $result = preg_replace_callback($pattern, function ($matches) {
            // Wrap the matched content with <strong> tags
            return '<strong>' . $matches[1] . '</strong>';
        }, $templateContent);

        // Perform variable substitution after replacing {** **}
        extract($data, EXTR_SKIP);
        //var_dump($data);
        $pattern2 = '/\{\!\!\s*(.*?)\s*\!\!\}/';
        $result = preg_replace_callback($pattern2, function ($matches) use ($data) {
            // Replace {!! variable !!} with the corresponding data value
            
            // $match_trimmed = trim($matches[1]);
            //var_dump($match_trimmed);
            
            return $data[$matches[1]] ?? '';
        }, $result);
        // Return the result directly
        
        return $result;
    }

    private function resolve(string $path): string
    {
        return "{$this->basePath}/{$path}.php";
    }
}

// Example usage:
$engine = new SimpleTemplateEngine(__DIR__);
echo $engine->render('temp3', ['name' => 'World', 'title' => 'mytitle']);