<?php

declare(strict_types=1);

namespace Cooking\Recipe\Web\Lib;

use Exception;

abstract class AbstractView
{
    public function render(string $template, array $assigns)
    {
        $name = (new \ReflectionClass($this))->getShortName();

        $filename = \getcwd() . '/apps/web' . '/Templates/'
            . \strtolower(\str_replace('View', '', $name)) . '/' . $template;
  
        if (\file_exists($filename)) {
            ob_start();
            require_once $filename;

            return ob_get_clean();
        }

        throw new Exception('template ' . $template . ' not exists');
    }
}