<?php

namespace Sprint\Migration;


class Version20231108214508 extends Version
{
    protected $description = "Удаляем не нужные свойства";

    protected $moduleVersion = "4.6.1";

    public const CODE = 'vitrina';

    public const TYPE_ID = 'content';

    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists(self::CODE, self::TYPE_ID);

        foreach (self::getPropertyCode() as $code) {
            $helper->Iblock()->deletePropertyIfExists($iblockId, $code);
        }
    }

    public function down()
    {
        //your code ...
    }

    public static function getPropertyCode()
    {
        return [
            'CONTRACT_TRANSPORTATION_LINK',
            'CONTRACT_ORDER_ONE_TIME_LINK',
            'CONTRACT_EXPEDITION_LINK',
            'DOCUMENTS_EPD_LINK',
            'DOCUMENTS_EXPEDITOR_LINK',
            'DOCUMENTS_EXPEDITOR_RECEIPT_LINK',
            'DOCUMENTS_DRIVER_APPROVALS_LINK',
            'DOCUMENTS_APPLICATION_TRANSPORTATION_LINK',
            'AUTOMATIC_PRICES',
            'AUTOMATIC_GEO_MONITORING',
            'ACCOUNTING_INVOICE_LINK',
            'ACCOUNTING_ACT_ACCEPTANCE_LINK',
            'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK',
            'ACCOUNTING_TRANSPORTATION_REGISTRY_LINK',
            'ACCOUNTING_TAX_INVOICE_LINK',
            'ACCOUNTING_UPD_LINK',
            'DONKEY_STS_LINK',
            'TRAILER_STS_LINK',
            'TRAILER_RENT_AGREEMENT_LINK',
            'TRAILER_SECONDARY_STS_LINK',
            'TRAILER_SECONDARY_RENT_AGREEMENT_LINK',
            'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK',
            'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK',
            'TRAILER_SECONDARY_FREE_USAGE_LINK',
            'TRUCK_STS_LINK',
            'TRUCK_RENT_AGREEMENT_LINK',
            'TRUCK_AGREEMENT_LEASING_COMPANY_LINK',
            'TRUCK_MARRIAGE_CERTIFICATE_LINK',
            'TRUCK_FREE_USAGE_LINK',
            'TRUCK_RENT_AGREEMENT_FOR_LINK',
            'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_LINK',
            'TRUCK_STS_FOR_LINK',
            'TRUCK_FREE_USAGE_FOR_LINK',
            'CONTRACT_EXPEDITION_FOR_LINK',
            'CONTRACT_TRANSPORTATION_FOR_LINK',
            'CONTRACT_ORDER_ONE_TIME_FOR_LINK',
            'DOCUMENTS_EPD_FOR_LINK',
            'DOCUMENTS_EXPEDITOR_FOR_LINK',
            'DOCUMENTS_EXPEDITOR_RECEIPT_FOR_LINK',
            'DOCUMENTS_DRIVER_APPROVALS_FOR_LINK',
            'DOCUMENTS_APPLICATION_TRANSPORTATION_FOR_LINK',
            'ACCOUNTING_INVOICE_FOR_LINK',
            'ACCOUNTING_ACT_ACCEPTANCE_FOR_LINK',
            'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_FOR_LINK',
            'ACCOUNTING_TRANSPORTATION_REGISTRY_FOR_LINK',
            'ACCOUNTING_TAX_INVOICE_FOR_LINK',
            'ACCOUNTING_UPD_FOR_LINK',
            'DONKEY_STS_FOR_LINK',
            'TRAILER_STS_FOR_LINK',
            'TRAILER_RENT_AGREEMENT_FOR_LINK',
            'TRAILER_SECONDARY_STS_FOR_LINK',
            'TRAILER_SECONDARY_RENT_AGREEMENT_FOR_LINK',
            'TRAILER_SEC_AGR_LEASING_COMPANY_FOR_LINK',
            'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FOR_LINK',
            'TRAILER_SECONDARY_FREE_USAGE_FOR_LINK',
            'TRUCK_STS_FOR_LINK',
            'TRUCK_RENT_AGREEMENT_FOR_LINK',
            'TRUCK_AGREEMENT_LEASING_COMPANY_FOR_LINK',
            'TRUCK_MARRIAGE_CERTIFICATE_FOR_LINK',
            'TRUCK_FREE_USAGE_FOR_LINK',
            'DOCUMENTS_APPLICATION_TRANSPORTATION_FORWARDER_LIN',
            'ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_FORWARDER_',
            'TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_FORWAR',
            'TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_FORWARDER_L',
        ];
    }
}
