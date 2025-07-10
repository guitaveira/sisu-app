<?php
namespace app\controllers;
use Illuminate\Database\Eloquent\Model;

class Controller
{

    /**
     * @var string
     */
    protected string $controllerName = "";


    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
             session_start();
        $reflection = new \ReflectionClass($this);
        $className =  $reflection->getShortName();
        $lowerClassName = strtolower($className);
        $this->controllerName = str_replace('controller', '', $lowerClassName);
    }

    /**
     * @param array $formInput The raw input array (e.g., $_POST).
     * @return array The sanitized key-value pairs.
     */
    protected function formToArray(array $formInput): array
    {
        $dict = [];
        foreach ($formInput as $key => $value) {
            $dict[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $dict;
    }

    /**
     * @param object $model An object that has a `fill` method.
     * @return void
     */
    protected function loadForm(Model &$model): void
    {
        $sanitizedData = $this->formToArray($_POST);
        $model->fill($sanitizedData);
    }

    /**
     * @param string $path The action/method to redirect to (e.g., 'index', 'show').
     * @param array|null $params Optional query parameters to append to the URL.
     * @return void
     */
    protected function redirect(string $path, ?array $params = null): void
    {
        http_response_code(302);
        $url = "/app/{$this->controllerName}/{$path}";
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        header("Location: " . $url);
    }

    /**
     * @return string|void
     */
    public function notFound()
    {
        http_response_code(404);
        $templatePath = getcwd() . '/views/public/404.php';
        if (file_exists($templatePath))
            include $templatePath;

    }
}
