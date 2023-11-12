<?php

declare(strict_types=1);

namespace Taxcom\Library\DTO;

use Taxcom\Library\Interface\DTO\ShippingDTO;

/**
 * Объект перевозчика
 */
class Shipping
{

    public function __construct(array $msg)
    {
        echo "<pre style='disp3lay: none;' alt='arResult'>";
        print_r($msg);
        echo "</pre>";

        $this->status = $msg['status'];
        $this->root = $msg['root'];
        $this->date = $msg['loading_date'];
    }

    protected static function setCarrier()
    {

    }
}