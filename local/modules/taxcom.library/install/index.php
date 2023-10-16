<?php

IncludeModuleLangFile(__FILE__);

/**
 * Библиотека для витирины ФНС
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

    public function DoInstall(): bool
    {
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall(): bool
    {
        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function InstallDB(): bool
    {

    }

    public function UnInstallDB(): bool
    {

    }

    public function InstallEvents(): bool
    {
        return true;
    }

    public function UnInstallEvents(): bool
    {
        return true;
    }

    public function InstallFiles(): bool
    {
        return true;
    }

    public function UnInstallFiles(): bool
    {
        return true;
    }
}