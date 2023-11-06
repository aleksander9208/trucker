<?php

namespace Sprint\Migration;


use CAgent;

class Version20231030113501 extends Version
{
    protected $description = "Добавляем агента для экспорта сообщение из kafka";

    protected $moduleVersion = "4.4.1";

    public function up()
    {
        CAgent::AddAgent(
            "\Taxcom\Library\Agents\MessageKafkaConsurmeAgents::run();",
            "taxcom.library",
            "N",
            "60",
        );
    }

    public function down()
    {
        CAgent::RemoveAgent(
            "\Taxcom\Library\Agents\MessageKafkaConsurmeAgents::run();",
            "taxcom.library",
        );
    }
}
