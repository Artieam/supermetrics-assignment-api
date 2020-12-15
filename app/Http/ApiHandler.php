<?php

declare(strict_types=1);

namespace app\Http;

class ApiHandler
{
    private array $routes;

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Execute api method
     *
     * @param string $method
     * @param array $params
     *
     * @return array
     */
    public function execCommand(string $method, array $params = []): array
    {
        // get the actual function name (if necessary) and the class it belongs to.
        $returnArray = $this->getCommand($method);

        // if we don't get a function back, then raise the error
        if ($returnArray['success'] === false) {
            return $returnArray;
        }

        $class = $returnArray['dataArray']['class'];
        $methodName = $returnArray['dataArray']['method'];

        // Execute User Profile Commands
        return (new $class())->$methodName($params);
    }

    /**
     * load up all public facing functions
     */
    private function loadRoutes(): void
    {
        $this->routes = [
            'posts' => ['class' => PostController::class, 'method' => 'index'],
            'avrPostLenMonth' => ['class' => PostController::class, 'method' => 'avrPostLenMonth'],
            'longestPostMonth' => ['class' => PostController::class, 'method' => 'longestPostMonth'],
            'totalPostWeekly' => ['class' => PostController::class, 'method' => 'totalPostWeekly'],
            'avrPostNumUserMonth' => ['class' => PostController::class, 'method' => 'avrPostNumUserMonth'],
            'avrPostNumMonthUser' => ['class' => PostController::class, 'method' => 'avrPostNumMonthUser'],
        ];
    }

    /**
     * get the actual function name and the class it belongs to.
     *
     * @param string $method
     *
     * @return array
     */
    private function getCommand(string $method): array
    {
        if (isset($this->routes[$method])) {
            $dataArray['class'] = $this->routes[$method]['class'];
            $dataArray['method'] = $this->routes[$method]['method'];
            $returnArray = AppResponse::getResponse('200');
            $returnArray['dataArray'] = $dataArray;
        } else {
            $returnArray = AppResponse::getResponse('405');
        }

        return $returnArray;
    }
}
