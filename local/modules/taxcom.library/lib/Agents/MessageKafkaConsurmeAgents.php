<?php

declare(strict_types=1);

namespace Taxcom\Library\Agents;

use JsonException;
use Taxcom\Library\Service\ExportMessageKafkaConsumer;

/**
 * Агент запуска парсера соообщений
 */
class MessageKafkaConsurmeAgents extends AbstractAgent
{
    /**
     * @return void
     * @throws JsonException
     */
    protected function execute(): void
    {
        $export = new ExportMessageKafkaConsumer();
        $export->export();
    }
}