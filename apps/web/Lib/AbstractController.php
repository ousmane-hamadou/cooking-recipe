<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Lib;



const HTTP_GET = 'GET';

const HTTP_POST = 'POST';

/**
 * Commodités pour creer des controllers.
 */
class AbstractController extends AbstractResource implements ControllerInterface
{
    //---------------------------------------------------------------------------------------------
    private function callHooks(Conn $conn): Conn
    {
        $hooks = $this->hooks();
        $httpMethod = $conn->requestMethod();

        if (isset($hooks[$httpMethod])) {
            foreach ($hooks[$httpMethod] as $hook) {
                $fn = $hook[0];
                $props = $hook[1] ?? null;

                /**@var Conn */
                $conn = $fn($conn, $props);

                if ($conn->halted) {
                    break;
                }
            }
        }

        return $conn;
    }

    //---------------------------------------------------------------------------------------------

    public function init(mixed $opts): mixed
    {
        return $opts;
    }

    public function service(Conn $conn, mixed $opts): Conn
    {
        $action = $conn->getParam('action') ?? 'index';

        if (in_array($action, $this->ressources())) {
            $conn = match ($conn->requestMethod()) {
                HTTP_GET => match ($action) {
                    'index', 'show', 'edit', 'new' =>
                    $conn->setHandler([$action => [$this, $opts]]),
                    default => $conn->putStatus(400)->halt()
                },
                HTTP_POST => match ($action) {
                    'create', 'delete', 'update' =>
                    $conn->setHandler([$action => [$this, $opts]]),
                    default => $conn->putStatus(400)->halt()
                }
            };

            return $conn;
        }

        return $conn->putStatus(404);
    }

    protected function getFullQualifierName()
    {
        return self::class;
    }

    public function render(Conn $conn, string $template): Conn
    {
        $fqcn = \str_replace(['Controllers', 'Controller'], ['Views', 'View'], $this::class);

        if (!\class_exists($fqcn)) {
            throw new \LogicException('Controller must have View');
        }

        /**@var AbstractView */
        $view = new $fqcn();
        $body =  $view->render($template, [...$conn->getAssigns(), $conn->params()]);

        return $conn->setRespBody($body);
    }

    public function dispatch(Conn $conn, mixed $opts)
    {
        $this->callHooks($conn);

        foreach ($conn->getRespHeaders() as $key => $value) {
            header($key . ': ' . $value);
        }

        $handler = $conn->getHandler();

        if (!$handler) {
            $conn->putStatus(400);
            return;
        }

        $act = key($handler);

        [$cb, $opts] = $handler[$act];

        $conn = $cb->$act($conn, $opts);

        echo $conn->getRespBody();
    }

    public function hooks(): array
    {
        return [];
    }

    public function rootLayout(): string|false
    {
        return false;
    }

    public function allowed(Conn $conn, mixed $opts): Conn
    {
        return $conn->putStatus(404);
    }

    public function ressources(): array
    {
        return ['index'];
    }
}