<?php

use Bitrix\Main\Application;

IncludeModuleLangFile(__FILE__);

/**
 * Библиотека для витрины ФНС
 */
Class taxcom_library extends CModule
{

    public $MODULE_ID = "taxcom.library";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $errors;

    public function __construct()
    {
        $this->MODULE_VERSION = "1.0.0";
        $this->MODULE_VERSION_DATE = "13.10.2023";
        $this->MODULE_NAME = "Витрина ФНС";
        $this->MODULE_DESCRIPTION = "Библиотека методов для ФНС";
    }

    public function DoInstall(): void
    {
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
    }


    public function InstallFiles(): void
    {
        /** Копируем раздел для отдачи настроек */
        CopyDirFiles(
            __DIR__ .'/local/',
            Application::getDocumentRoot() .'/local',
            true,
            true
        );
    }

    public function UnInstallFiles(): void
    {
        DeleteDirFilesEx(Application::getDocumentRoot().'/local/routes');
    }
}