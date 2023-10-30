<?php

declare(strict_types=1);

namespace Taxcom\Library\Trait;

use Bitrix\Main\Engine\ActionFilter;

/**
 * Траит для базавого класса контроллеров
 */
trait ConfigureActionsTrait
{
    /**
     * @param callable $callback
     * @param array $result
     * @return array
     */
    protected function iterateActions(callable $callback, array $result = []) : array
    {
        $refClass = new \ReflectionClass($this);
        $methods = $refClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (substr_compare($method->getName(), 'Action', -6) !== 0) {
                continue;
            }

            $methodName = substr($method->getName(), 0, -6);
            $config = call_user_func($callback, $methodName);
            if (!empty($config)) {
                if (!isset($result[$methodName])) {
                    $result[$methodName] = [];
                }

                $result[$methodName] = array_merge_recursive($result[$methodName], $config);
            }
        }

        return $result;
    }

    /**
     * @param array $result
     * @return array
     */
    protected function unsetHttpMethodPrefilter(array $result = []) : array
    {
        return $this->iterateActions(function () {
            return [
                '-prefilters' => [
                    ActionFilter\HttpMethod::class,
                    ActionFilter\Csrf::class
                ]
            ];
        }, $result);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function configureGetActions(array $result = []): array
    {
        return $this->iterateActions(function (string $method) {
            $result = [];
            if (substr($method, 0, 3) == 'get') {
                $result['+prefilters'] = [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_GET]
                    )
                ];
            }

            return $result;
        }, $result);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function configureAddActions(array $result = []): array
    {
        return $this->iterateActions(function (string $method) {
            $result = [];
            if (substr($method, 0, 3) == 'add') {
                $result['+prefilters'] = [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_POST]
                    )
                ];
            }

            return $result;
        }, $result);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function configureUpdateActions(array $result = []): array
    {
        return $this->iterateActions(function (string $method) {
            $result = [];
            if (substr($method, 0, 6) == 'update') {
                $result['+prefilters'] = [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_POST]
                    )
                ];
            }

            return $result;
        }, $result);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function configureDeleteActions(array $result = []): array
    {
        return $this->iterateActions(function (string $method) {
            $result = [];
            if (substr($method, 0, 6) == 'delete') {
                $result['+prefilters'] = [
                    new ActionFilter\HttpMethod(
                        [ActionFilter\HttpMethod::METHOD_DELETE]
                    )
                ];
            }

            return $result;
        }, $result);
    }
}