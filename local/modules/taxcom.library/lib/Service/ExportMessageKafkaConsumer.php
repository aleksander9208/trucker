<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

use Exception;
use JsonException;
use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\TopicConf;

/**
 * Экспорт файлов перевозчика
 */
class ExportMessageKafkaConsumer
{
    /** @var string Адрес подключение к кафке */
    public const BROKER = '172.125.125.36:9092,172.125.125.37:9092,172.125.125.44:9092';

    /** @var string Топик с сообщениями */
    public const TOPIC = 'tracker';

    /** @var string */
    public const NO_MESSAGE = 'Новых сообщений нет';

    /**
     * Экспорт писем
     *
     * @return void
     * @throws JsonException|Exception
     */
    public function export(): void
    {
        $conf = new Conf();
        $conf->set('group.id', 'group_1');
        $conf->set('log_level', (string) LOG_DEBUG);
        $conf->set('debug', 'all');

        $rk = new Consumer($conf);
        $rk->setLogLevel(LOG_DEBUG);
        $rk->addBrokers(self::BROKER);

        $topicConf = new TopicConf();
        $topicConf->set("auto.commit.enable", 'false');
        $topicConf->set('offset.store.method', 'file');
        $topicConf->set("auto.offset.reset", 'smallest');

        $topic = $rk->newTopic(self::TOPIC, $topicConf);

        for ($partition = 0; $partition <= 4; $partition++) {
            $topic->consumeStart($partition, RD_KAFKA_OFFSET_STORED);

            while(true) {
                $msg = $topic->consume($partition, 1000);

                if($msg->err) {
                    var_dump($msg->errstr() );
                    break;
                }

                if($msg !== null) {
                    $carrier = json_decode($msg->payload, true,);

                    if (is_array($carrier)) {
                        (new ParserCarrier($carrier, $msg->payload))->isParser();
                    }
                    $topic->offsetStore($msg->partition, ($msg->offset+1) );
                } else {
                    var_dump(self::NO_MESSAGE);
                    break;
                }
            }
        }
    }
}