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

    // Define the regular expression pattern for {!! !!}
    $pattern2 = '/\{\!\!\s*(.*?)\s*\!\!\}/';
    $result = preg_replace_callback($pattern2, function ($matches) use ($data) {
        // Replace {!! variable !!} with the corresponding data value
        

        if (str_contains(trim($matches[1]), "|")) {
            $arr = explode("|", trim($matches[1]));
            $term = $data[trim($arr[0])] ?? '';

            while (count($arr) > 1) {
               
                $filter_to_use = trim(array_pop($arr));
                
                $term = self::filter($term, $filter_to_use);
                
               
            }
        }
        else {
            $term = $data[trim($matches[1])] ?? '';
        }

        return $term;
    }, $result);

    $pattern3 = '/\{\?\?\s*(.*?)\s*\?\?\}/';
        $result = preg_replace_callback($pattern3, function ($matches) use ($data) {
            // Evaluate PHP code blocks
            ob_start();
            eval($matches[1]);
            $output = ob_get_clean();
            return $output;
        }, $result);
    return $result;
    
}

    private function resolve(string $path): string
    {
        return "{$this->basePath}/{$path}.php";
    }

    public static function filter($content, $filter) {
        $output = "";
        switch($filter) {
            case "uppercase":
                $output = strtoupper($content);
                break;
            case "lowercase":
                $output = strtolower($content);
                break;
            case "ucfirst":
                $output = ucfirst($content);
                break;
            case "ucwords": 
                $output = ucwords($content);
                break;
            case "md5":
                $output = md5($content);
                break;
        }
        return $output;
    }
}

// Example usage:
$engine = new SimpleTemplateEngine(__DIR__);
echo $engine->render('temp3', ['name' => 'World', 'title' => 'mytitle']);