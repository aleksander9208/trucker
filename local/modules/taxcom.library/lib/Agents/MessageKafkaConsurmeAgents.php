<?php

declare(strict_types=1);

namespace Taxcom\Library\Agents;

use Bitrix\Main\IO\FileNotFoundException;
use Edidata\Epl\Service\File\ExportMessageKafkaConsumer;

/**
 * Агент запуска парсера соообщений
 */
class MessageKafkaConsurmeAgents extends AbstractAgent
{
    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function execute(): void
    {
        $export = new ExportMessageKafkaConsumer();
        $export->export();
    }
}