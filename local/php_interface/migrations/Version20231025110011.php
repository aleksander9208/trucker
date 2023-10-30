<?php

namespace Sprint\Migration;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\InvalidOperationException;
use Bitrix\Main\UrlRewriter;

class Version20231025110011 extends Version
{
    protected $description = "Добавление роутинга в файл .settings.php";

    protected $moduleVersion = "4.4.1";

    /**
     * @return void
     * @throws ArgumentNullException
     * @throws InvalidOperationException
     */
    public function up(): void
    {
        $this->installRouting();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->unInstallRouting();
    }

    /**
     * @throws ArgumentNullException
     * @throws InvalidOperationException
     */
    public function installRouting(): void
    {
        $routes = $this->getRoutingFiles();
        if (!empty($routes)) {
            $this->copyRoutingFiles();

            $config = Configuration::getInstance();
            $routing = $config->get('routing');

            $config->add('routing', [
                'config' => array_unique(
                    array_merge(
                        (array)$routing['config'],
                        array_column($routes, 'name')
                    )
                )
            ]);
            $config->saveConfiguration();
        }

        foreach (static::getRewriteRules() as $rule) {
            UrlRewriter::add(SITE_ID, $rule);
        }
    }

    /**
     * @return void
     * @throws ArgumentNullException
     * @throws InvalidOperationException
     */
    public function unInstallRouting(): void
    {
        $routes = $this->getRoutingFiles();
        if (!empty($routes)) {
            $config = Configuration::getInstance();
            $routing = $config->get('routing');

            $config->add('routing', [
                'config' => array_diff(
                    $routing['config'],
                    $routes
                )
            ]);
            $config->saveConfiguration();
        }

        foreach (static::getRewriteRules() as $rule) {
            UrlRewriter::delete(SITE_ID, ['CONDITION' => $rule['CONDITION']]);
        }
    }

    /**
     * @return array[]
     */
    public static function getRewriteRules() : array
    {
        return [
            [
                'CONDITION' => '#^/api/v1/#',
                'RULE' => '',
                'ID' => '',
                'PATH' => '/api/index.php',
                'SORT' => 100,
            ]
        ];
    }

    /**
     * @return void
     */
    protected function copyRoutingFiles(): void
    {
        $routes = $this->getRoutingFiles();
        if (empty($routes)) {
            return;
        }

        $sourceDir = Application::getDocumentRoot() . '/local/routes';
        $destDir = Application::getDocumentRoot() . '/local/routes';
        foreach ($routes as $route) {
            $sourceFile = $sourceDir . '/' . $route['origin'];
            $destFile = $destDir . '/' . $route['name'];

            if (file_exists($destFile)) {
                continue;
            }

            @copy($sourceFile, $destFile);
            @chmod($destFile, BX_FILE_PERMISSIONS);
        }
    }

    /**
     * @return array
     */
    protected function getRoutingFiles() : array
    {
        $routingFiles = static::getDirFiles(Application::getDocumentRoot() . '/local/routes');
        if (empty($routingFiles)) {
            return [];
        }

        return array_map(
            static function ($item) {
                return [
                    'name' => sprintf('%s', $item),
                    'origin' => $item
                ];
            },
            $routingFiles
        );
    }

    /**
     * @param string $dir
     * @return array
     */
    public static function getDirFiles(string $dir) : array
    {
        return (array)array_diff(scandir($dir), ['.', '..']);
    }
}
