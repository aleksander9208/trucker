<?php

declare(strict_types=1);

namespace Taxcom\Library\Agents;

/**
 * Базовый класс для описания агентов
 */
abstract class AbstractAgent
{
    /** Запрет вызова конструктора напрямую */
    protected function __construct()
    {
    }

    /**
     * Запускает выполнение агента
     * @return string
     */
    public static function run(): string
    {
        try {
            $obAgent = new static();
            $obAgent->execute();
        } catch (\Exception $e) {
            $mess = implode(PHP_EOL, [
                '---------- Agent Error ----------',
                $e->getMessage(),
                mydump($e->getTrace()),
                '---------- Agent Error End ----------'
            ]);

            AddMessage2Log($mess);
        }

        return static::class . "::run();";
    }

    /**
     * Выполняет агента
     * @return void
     */
    abstract protected function execute();
}