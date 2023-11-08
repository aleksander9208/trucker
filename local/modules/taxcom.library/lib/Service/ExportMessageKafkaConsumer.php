<?php

declare(strict_types=1);

namespace Taxcom\Library\Service;

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
     * @throws JsonException
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
        ##$topicConf->set("enable.auto.offset.store", "true");
        #$topicConf->set("auto.commit.interval.ms", 0);
        #$topicConf->set("offset.store.sync.interval.ms", -1);
        #$topicConf->set("offset.store.method", 'broker');
        #$topicConf->set("request.required.acks", 1);
        $topicConf->set('offset.store.method', 'file');
        #$topicConf->set('offset.store.path', sys_get_temp_dir());
        $topicConf->set("auto.offset.reset", 'smallest');

        $topic = $rk->newTopic(self::TOPIC, $topicConf);
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);


        while(true) {
            $msg = $topic->consume(0, 1000);

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