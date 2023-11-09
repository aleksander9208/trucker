$(document).ready(function () {

    /**
     * Стилизация инпута в фильтре
     */
    $(window).keyup(function (e) {
        var target = $('.filter_checkbox input:focus');
        if (e.keyCode == 9 && $(target).length) {
            $(target).parent().addClass('focused');
        }
    });

    $('.filter_checkbox input').focusout(function () {
        $(this).parent().removeClass('focused');
    });

    $(window).keyup(function (e) {
        var target = $('.filter_list-label input:focus');
        if (e.keyCode == 9 && $(target).length) {
            $(target).parent().addClass('focused_type');
        }
    });

    $('.filter_list-label input').focusout(function () {
        $(this).parent().removeClass('focused_type');
    });

    /**
     * Отображаем режим выбора
     * проблемных организаций
     */
    $('.filter_top-organizations span').click(function () {
        if ($('.filter_list-type-organizations').css('display') === 'none') {
            $('.filter_list-type-organizations').css('display', 'flex');
        } else {
            $('.filter_list-type-organizations').hide();
        }
    });

    /**
     * Получаем информацию в
     * инфо бар по перевозки
     */
    $(document).on('click', '.info_bar-content', function () {
        const id = $(this).parents('.main-grid-row').attr('data-id');

        /** Скрываем блоки */
        hideBlock();

        BX.ajax({
            url: '/api/v1/vhs/vitrina/' + id,
            method: 'POST',
            data: '',
            timeout: 2000,
            dataType: 'json',
            onsuccess: function (response) {
                if (response.status === 'success') {
                    const carriage = response.data;

                    let [year, month, day] = carriage.DATE.split("-");

                    /** Данные перевозки для инфо бара */
                    $('#link_archiv').attr('data-id', carriage.ID);
                    /** Номер перевозки */
                    $('#carriage_id').html(carriage.NAME);
                    /** Дата перевозки */
                    $('#carriage_date').html(day + '.' + month + '.' + year);
                    /** Рыночная цена */
                    if (carriage.AUTO_PRICES != null) {
                        $('#deviation-price').show();
                        $('#carriage_deviation-price').html(carriage.AUTO_PRICES + '%');
                    }

                    /** Грузовладелец */
                    if (carriage.CARGO != null) {
                        let cargo = carriage.CARGO_INN ? carriage.CARGO_INN : '';
                        $('#carriage_owner').html(
                            'Грузовладелец <span class="carriage_name">' +
                            carriage.CARGO + '</span><span id="carriage_owner_inn">' +
                            cargo + '</span>'
                        )
                    } else {
                        $('#carriage_owner').html('');
                    }
                    /** Перевозчик */
                    if (carriage.CARRIER != null) {
                        let carrier = carriage.CARRIER_INN ? carriage.CARRIER_INN : '';
                        $('#carriage_carrier').html(
                            'Перевозчик <span class="carriage_name">' +
                            carriage.CARRIER + '</span><span id="carriage_owner_inn">' +
                            carrier + '</span>'
                        )
                    } else {
                        $('#carriage_carrier').html('');
                    }
                    /** Экспедитор */
                    if (carriage.FORWARDER != null) {
                        let forwarder = carriage.FORWARDER_INN ? carriage.FORWARDER_INN : '';
                        $('#carriage_forwarder').html(
                            'Экспедитор <span class="carriage_name">' +
                            carriage.FORWARDER + '</span><span id="carriage_owner_inn">' +
                            forwarder + '</span>'
                        )
                    } else {
                        $('#carriage_forwarder').html('');
                    }

                    /** Чек-лист с перевозчиком */

                    /** Чек-лист с экспедитором */
                    // if (carriage.CHECKLIST_FORWARDER != null) {
                    //     $('#checklist_forwarder').show();
                    // } else {
                    $('#checklist_forwarder').hide();
                    // }

                    contract(carriage);
                    executionDocuments(carriage);
                    automaticChecks(carriage);
                    accounting(carriage);
                    donkey(carriage);
                    trailer(carriage);
                    trailerSec(carriage);
                    truck(carriage);
                }

                if (response.status === 'error') {
                    $('#error').html(response.errors[0].message);
                }
            },
        });
    });

    $(document).on('click', '#link_archiv', function () {
        const id = $(this).attr('data-id');

        BX.ajax({
            url: '/api/v1/vhs/vitrina/archiv/' + id,
            method: 'POST',
            data: '',
            timeout: 2000,
            dataType: 'json',
            onsuccess: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    window.location.href = response.data.URL;
                }

                if (response.status === 'error') {
                    $('#error').html(response.errors[0].message);
                }
            },
        });
    });

    /** Скрываем блоки */
    function hideBlock() {
        $('#deviation-price').hide();

        $('#contract').hide();
        $('#transport_link').hide();
        $('#contract_link').hide();
        $('#one_time_link').hide();

        $('#execution_documents').hide();
        $('#documents_link').hide();
        $('#epd_link').hide();
        $('#driver_link').hide();
        $('#exp_link').hide();
        $('#receipt_link').hide();

        $('#automatic').hide();
        $('#prices_link').hide();
        $('#geo_link').hide();

        $('#accounting').hide();
        $('#invoice_link').hide();
        $('#act_link').hide();
        $('#multi_link').hide();
        $('#reg_link').hide();
        $('#tax_link').hide();
        $('#upd_link').hide();

        $('#donkey').hide();
        $('#donkey_link').hide();

        $('#trailer').hide();
        $('#trailer_ctc_link').hide();
        $('#trailer_rent_link').hide();

        $('#trailer_sec').hide();
        $('#trailer_sec_ctc_link').hide();
        $('#trailer_sec_rent_link').hide();
        $('#trailer_sec_lias_link').hide();
        $('#trailer_sec_cer_link').hide();
        $('#trailer_sec_usage_link').hide();

        $('#truck').hide();
        $('#truck_sts_link').hide();
        $('#truck_rent').hide();
        $('#truck_leas_link').hide();
        $('#truck_cert_link').hide();
        $('#truck_usage_link').hide();
    }

    /** Подписанные договоры */
    function contract(carriage) {
        /** Подписанные договоры */
        if (carriage.CONTRACT_CHECK != null) {
            $('#contract').show();
            if (carriage.CONTRACT_CHECK_ERROR !== false) {
                $('#detail_status-transportation').addClass('detail-status_good').html(carriage.CONTRACT_CHECK);
            } else {
                $('#detail_status-transportation').addClass('detail-status_error').html('Выполнено ' + carriage.CONTRACT_CHECK);
            }
        } else {
            $('#detail_status-transportation').html('').removeClass();
        }
        /** Договор перевозки */
        if (carriage.CONTRACT_TRANSPORT_STATUS === 'passed' ||
            carriage.CONTRACT_TRANSPORT_STATUS === 'in_progress'
        ) {
            $('#transport_link').show();
        }
        if (carriage.CONTRACT_TRANSPORTATION_LINK != null) {
            let CONT_TRANSPORT_LINK = '';
            const DESCRIPTION_CONT_TRANSPORT_LINK = carriage.CONTRACT_TRANSPORTATION_LINK.DESCRIPTION.split(",");

            $.each(carriage.CONTRACT_TRANSPORTATION_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_CONT_TRANSPORT_LINK[index] !== '') {
                    CONT_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_CONT_TRANSPORT_LINK[index] + '</li>';
                } else if (DESCRIPTION_CONT_TRANSPORT_LINK[index] === '') {
                } else {
                    CONT_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#transport_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_transport_link").html(CONT_TRANSPORT_LINK);
            $('#transport_file').show();
        } else {
            $('#transport_file').hide();
            $('#transport_link').addClass('status-info_confirmation_error');
            $("#list_file_transport_link").html('');
        }
        /** Договор транспортной экспедиции */
        if (carriage.CONTRACT_EXP_STATUS === 'passed' ||
            carriage.CONTRACT_EXP_STATUS === 'in_progress'
        ) {
            $('#contract_link').show();
        }
        if (carriage.CONTRACT_EXPEDITION_LINK != null) {
            let CONT_EXP_LINK = '';
            const DESCRIPTION_CONT_EXP_LINK = carriage.CONTRACT_EXPEDITION_LINK.DESCRIPTION.split(",");

            $.each(carriage.CONTRACT_EXPEDITION_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_CONT_EXP_LINK[index] !== '') {
                    CONT_EXP_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_CONT_EXP_LINK[index] + '</li>';
                } else if (DESCRIPTION_CONT_EXP_LINK[index] === '') {
                } else {
                    CONT_EXP_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#contract_link').removeClass('status-info_confirmation_error');
            $("#list_file_contract_link").html(CONT_EXP_LINK);
            $('#contract_file').show();
        } else {
            $('#contract_file').hide();
            $('#contract_link').addClass('status-info_confirmation_error');
            $("#list_file_contract_link").html();
        }
        /** Заказ (разовая договор-заявка) */
        if (carriage.CONTRACT_ORDER_ONE_TIME_STATUS === 'passed' ||
            carriage.CONTRACT_ORDER_ONE_TIME_STATUS === 'in_progress'
        ) {
            $('#one_time_link').show();
        }
        if (carriage.CONTRACT_ORDER_ONE_TIME_LINK != null) {
            let CONT_ORDER_ONE_TIME = '';
            const DESCRIPTION_CONT_ORDER_ONE_TIME = carriage.CONTRACT_ORDER_ONE_TIME_LINK.DESCRIPTION.split(",");

            $.each(carriage.CONTRACT_ORDER_ONE_TIME_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_CONT_ORDER_ONE_TIME[index] !== '') {
                    CONT_ORDER_ONE_TIME += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_CONT_ORDER_ONE_TIME[index] + '</li>';
                } else if (DESCRIPTION_CONT_ORDER_ONE_TIME[index] === '') {
                } else {
                    CONT_ORDER_ONE_TIME += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#one_time_link').removeClass('status-info_confirmation_error');
            $("#list_file_one_time_link").html(CONT_ORDER_ONE_TIME);
            $('#one_time_file').show();
        } else {
            $('#one_time_file').hide();
            $('#one_time_link').addClass('status-info_confirmation_error');
            $("#list_file_one_time_link").html();
        }
    }

    /** Оформление перевозки */
    function executionDocuments(carriage) {
        /** Оформление перевозки */
        if (carriage.DOCUMENTS_CHECK != null) {
            $('#execution_documents').show();
            if (carriage.DOCUMENTS_CHECK_ERROR !== false) {
                $('#documents_check').addClass('detail-status_good').html(carriage.DOCUMENTS_CHECK);
            } else {
                $('#documents_check').addClass('detail-status_error').html('Выполнено ' + carriage.DOCUMENTS_CHECK);
            }
        } else {
            $('#documents_check').html('').removeClass();
        }
        /** Заявка на перевозку */
        if (carriage.DOCUMENTS_TRANSPORT_STATUS === 'passed' ||
            carriage.DOCUMENTS_TRANSPORT_STATUS === 'in_progress'
        ) {
            $('#documents_link').show();
        }
        if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK != null) {
            let DOC_APP_TRANSPORT_LINK = '';
            const DESCRIPTION_DOC_APP_TRANSPORT_LINK = carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK.DESCRIPTION.split(",");

            $.each(carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] !== '') {
                    DOC_APP_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] === '') {
                } else {
                    DOC_APP_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#documents_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_documents_link").html(DOC_APP_TRANSPORT_LINK);
            $('#documents_file').show();
        } else {
            $('#documents_file').hide();
            $('#documents_link').addClass('status-info_confirmation_error');
            $("#list_file_one_time_link").html();
        }
        /** Подписанная ЭТрН */
        if (carriage.DOCUMENTS_EPD_STATUS === 'passed' ||
            carriage.DOCUMENTS_EPD_STATUS === 'in_progress'
        ) {
            $('#epd_link').show();
        }
        if (carriage.DOCUMENTS_EPD_LINK != null) {
            let DOC_EPD_LINK = '';
            const DESCRIPTION_DOC_EPD_LINK = carriage.DOCUMENTS_EPD_LINK.DESCRIPTION.split(",");

            $.each(carriage.DOCUMENTS_EPD_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DOC_EPD_LINK[index] !== '') {
                    DOC_EPD_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DOC_EPD_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_EPD_LINK[index] === '') {
                } else {
                    DOC_EPD_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#epd_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_epd_link").html(DOC_EPD_LINK);
            $('#epd_file').show();
        } else {
            $('#epd_file').hide();
            $('#epd_link').addClass('status-info_confirmation_error');
            $("#list_file_epd_link").html();
        }
        /** Подтверждение договорных отношений с водителем */
        if (carriage.DOCUMENTS_DRIVER_STATUS === 'passed' ||
            carriage.DOCUMENTS_DRIVER_STATUS === 'in_progress'
        ) {
            $('#driver_link').show();
        }
        if (carriage.DOCUMENTS_DRIVER_APPROVALS_LINK != null) {
            let DOC_DRIVER_APP_LINK = '';
            const DESCRIPTION_DOC_DRIVER_APP_LINK = carriage.DOCUMENTS_DRIVER_APPROVALS_LINK.DESCRIPTION.split(",");

            $.each(carriage.DOCUMENTS_DRIVER_APPROVALS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DOC_DRIVER_APP_LINK[index] !== '') {
                    DOC_DRIVER_APP_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DOC_DRIVER_APP_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_DRIVER_APP_LINK[index] === '') {
                } else {
                    DOC_DRIVER_APP_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#driver_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_driver_link").html(DOC_DRIVER_APP_LINK);
            $('#driver_file').show();
        } else {
            $('#driver_file').hide();
            $('#driver_link').addClass('status-info_confirmation_error');
            $("#list_file_driver_link").html('');
        }
        /** Поручение экспедитору */
        if (carriage.DOCUMENTS_EXPEDITOR_STATUS === 'passed' ||
            carriage.DOCUMENTS_EXPEDITOR_STATUS === 'in_progress'
        ) {
            $('#exp_link').show();
        }
        if (carriage.DOCUMENTS_EXPEDITOR_LINK != null) {
            let DOC_EXP_LINK = '';
            const DESCRIPTION_DOC_EXP_LINK = carriage.DOCUMENTS_EXPEDITOR_LINK.DESCRIPTION.split(",");

            $.each(carriage.DOCUMENTS_EXPEDITOR_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DOC_EXP_LINK[index] !== '') {
                    DOC_EXP_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DOC_EXP_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_EXP_LINK[index] === '') {
                } else {
                    DOC_EXP_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#exp_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_exp_link").html(DOC_EXP_LINK);
            $('#exp_file').show();
        } else {
            $('#exp_file').hide();
            $('#exp_link').addClass('status-info_confirmation_error');
            $("#list_file_exp_link").html('');
        }
        /** Экспедиторская расписка */
        if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS === 'passed' ||
            carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS === 'in_progress'
        ) {
            $('#receipt_link').show();
        }
        if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK != null) {
            let DOC_EXP_RECEIPT_LINK = '';
            const DESCRIPTION_DOC_EXP_RECEIPT_LINK = carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK.DESCRIPTION.split(",");

            $.each(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] !== '') {
                    DOC_EXP_RECEIPT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] === '') {
                } else {
                    DOC_EXP_RECEIPT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#receipt_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_receipt_link").html(DOC_EXP_RECEIPT_LINK);
            $('#receipt_file').show();
        } else {
            $('#receipt_file').hide();
            $('#receipt_link').addClass('status-info_confirmation_error');
            $("#list_file_receipt_link").html();
        }
    }

    /** Автоматические проверки */
    function automaticChecks(carriage) {
        /** Автоматические проверки */
        if (carriage.AUTOMATIC_CHECKS != null) {
            $('#automatic').show();
            if (carriage.AUTOMATIC_CHECK_ERROR !== false) {
                $('#auto_check').addClass('detail-status_good').html(carriage.AUTOMATIC_CHECKS);
            } else {
                $('#auto_check').addClass('detail-status_error').html('Выполнено ' + carriage.AUTOMATIC_CHECKS);
            }
        } else {
            $('#auto_check').html('').removeClass();
        }
        /** Стоимость перевозки соответствует рыночным ценам */
        if (carriage.AUTOMATIC_PRICES_STATUS === 'passed' ||
            carriage.AUTOMATIC_PRICES_STATUS === 'in_progress'
        ) {
            $('#prices_link').show();
        }
        if (carriage.AUTOMATIC_PRICES !== false) {
            $('#prices_link').show().removeClass('status-info_confirmation_error');
            $('#prices_file').show();
        } else {
            $('#prices_link').addClass('status-info_confirmation_error');
            $('#prices_file').hide();
        }
        /** Подтверждения перевозки через геомониторинг */
        if (carriage.AUTOMATIC_GEO_MONITORING_STATUS === 'passed' ||
            carriage.AUTOMATIC_GEO_MONITORING_STATUS === 'in_progress'
        ) {
            $('#geo_link').show();
        }
        if (carriage.AUTOMATIC_GEO_MONITORING != null) {
            $('#geo_link').show().removeClass('status-info_confirmation_error');
            $('#geo_file').show();
        } else {
            $('#geo_link').addClass('status-info_confirmation_error');
            $('#geo_file').hide();
        }
    }

    /** Бухгалтерские документы */
    function accounting(carriage) {
        /** Бухгалтерские документы */
        if (carriage.ACCOUNTING_CHECKS != null) {
            $('#accounting').show();
            if (carriage.ACCOUNTING_CHECKS_ERROR !== false) {
                $('#accounting_check').addClass('detail-status_good').html(carriage.ACCOUNTING_CHECKS);
            } else {
                $('#accounting_check').addClass('detail-status_error').html('Выполнено ' + carriage.ACCOUNTING_CHECKS);
            }
        } else {
            $('#accounting_check').html('').removeClass();
        }
        /** Счёт */
        if (carriage.ACCOUNTING_INVOICE_STATUS === 'passed' ||
            carriage.ACCOUNTING_INVOICE_STATUS === 'in_progress'
        ) {
            $('#invoice_link').show();
        }
        if (carriage.ACCOUNTING_INVOICE_LINK != null) {
            let ACC_INVOICE_LINK = '';
            const DESCRIPTION_ACC_INVOICE_LINK = carriage.ACCOUNTING_INVOICE_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_INVOICE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_INVOICE_LINK[index] !== '') {
                    ACC_INVOICE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_INVOICE_LINK[index] + '</li>';
                } else if (DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] === '') {
                } else {
                    ACC_INVOICE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#invoice_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_invoice_link").html(ACC_INVOICE_LINK);
            $('#invoice_file').show();
        } else {
            $('#invoice_file').hide();
            $('#invoice_link').addClass('status-info_confirmation_error');
            $("#list_file_invoice_link").html();
        }
        /** Акт о приемке выполненных работ по услуге */
        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_STATUS === 'passed' ||
            carriage.ACCOUNTING_ACT_ACCEPTANCE_STATUS === 'in_progress'
        ) {
            $('#act_link').show();
        }
        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK != null) {
            let ACC_ACT_ACC_LINK = '';
            const DESCRIPTION_ACC_ACT_ACC_LINK = carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_ACT_ACC_LINK[index] !== '') {
                    ACC_ACT_ACC_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_ACT_ACC_LINK[index] + '</li>';
                } else if (DESCRIPTION_ACC_ACT_ACC_LINK[index] === '') {
                } else {
                    ACC_ACT_ACC_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#act_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_act_link").html(ACC_ACT_ACC_LINK);
            $('#act_file').show();
        } else {
            $('#act_file').hide();
            $('#act_link').addClass('status-info_confirmation_error');
            $("#list_file_act_link").html();
        }
        /** Акт о приемке выполненных работ, включающий несколько перевозок */
        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS === 'passed' ||
            carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS === 'in_progress'
        ) {
            $('#multi_link').show();
        }
        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK != null) {
            let ACC_ACT_MULTI_TRANSPORT_LINK = '';
            const DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK = carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_ACT_ACC_LINK[index] !== '') {
                    ACC_ACT_MULTI_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK[index] + '</li>';
                } else if (DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK[index] === '') {
                } else {
                    ACC_ACT_MULTI_TRANSPORT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#multi_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_multi_link").html(ACC_ACT_MULTI_TRANSPORT_LINK);
            $('#multi_file').show();
        } else {
            $('#multi_file').hide();
            $('#multi_link').addClass('status-info_confirmation_error');
            $("#list_file_multi_link").html();
        }
        /** Реестр на перевозки */
        if (carriage.ACCOUNTING_TRANSPORT_REGISTRY_STATUS === 'passed' ||
            carriage.ACCOUNTING_TRANSPORT_REGISTRY_STATUS === 'in_progress'
        ) {
            $('#reg_link').show();
        }
        if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK != null) {
            let ACC_TRANSPORT_REG_LINK = '';
            const DESCRIPTION_ACC_TRANSPORT_REG_LINK = carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] !== '') {
                    ACC_TRANSPORT_REG_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] + '</li>';
                } else if (DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] === '') {
                } else {
                    ACC_TRANSPORT_REG_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#reg_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_reg_link").html(ACC_TRANSPORT_REG_LINK);
            $('#reg_file').show();
        } else {
            $('#reg_file').hide();
            $('#reg_link').addClass('status-info_confirmation_error');
            $("#list_file_reg_link").html();
        }
        /** Счёт-фактура */
        if (carriage.ACCOUNTING_TAX_INVOICE_STATUS === 'passed' ||
            carriage.ACCOUNTING_TAX_INVOICE_STATUS === 'in_progress'
        ) {
            $('#tax_link').show();
        }
        if (carriage.ACCOUNTING_TAX_INVOICE_LINK != null) {
            let ACC_TAX_INVOICE_LINK = '';
            const DESCRIPTION_ACC_TAX_INVOICE_LINK = carriage.ACCOUNTING_TAX_INVOICE_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_TAX_INVOICE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_TAX_INVOICE_LINK[index] !== '') {
                    ACC_TAX_INVOICE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_TAX_INVOICE_LINK[index] + '</li>';
                } else if (DESCRIPTION_ACC_TAX_INVOICE_LINK[index] === '') {
                } else {
                    ACC_TAX_INVOICE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#tax_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_tax_link").html(ACC_TAX_INVOICE_LINK);
            $('#tax_file').show();
        } else {
            $('#tax_file').hide();
            $('#tax_link').addClass('status-info_confirmation_error');
            $("#list_file_tax_link").html();
        }
        /** УПД */
        if (carriage.ACCOUNTING_UPD_STATUS === 'passed' ||
            carriage.ACCOUNTING_UPD_STATUS === 'in_progress'
        ) {
            $('#upd_link').show();
        }
        if (carriage.ACCOUNTING_UPD_LINK != null) {
            let ACC_UPD_LINK = '';
            const DESCRIPTION_ACC_UPD_LINK = carriage.ACCOUNTING_UPD_LINK.DESCRIPTION.split(",");

            $.each(carriage.ACCOUNTING_UPD_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_ACC_UPD_LINK[index] !== '') {
                    ACC_UPD_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_ACC_UPD_LINK[index] + '</li>';
                } else if (DESCRIPTION_ACC_UPD_LINK[index] === '') {
                } else {
                    ACC_UPD_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#upd_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_upd_link").html(ACC_UPD_LINK);
            $('#upd_file').show();
        } else {
            $('#upd_file').hide();
            $('#upd_link').addClass('status-info_confirmation_error');
            $("#list_file_upd_link").html();
        }
    }

    /** Блок тягача */
    function donkey(carriage) {
        /** Подтверждения владения тягач */
        if (carriage.DONKEY_CHECKS != null) {
            $('#donkey').show();
            if (carriage.DONKEY_CHECKS_ERROR !== false) {
                $('#donkey_check').addClass('detail-status_good').html(carriage.DONKEY_CHECKS);
            } else {
                $('#donkey_check').addClass('detail-status_error').html('Выполнено ' + carriage.DONKEY_CHECKS);
            }
        } else {
            $('#donkey_check').html('').removeClass();
        }
        /** Номерной знак тягач */
        if (carriage.DONKEY_LICENSE_PLATE != null) {
            $('#donkey_plate').html(carriage.DONKEY_LICENSE_PLATE);
        } else {
            $('#donkey_plate').html('');
        }
        /** СТС тягач */
        if (carriage.DONKEY_STS_STATUS === 'passed' ||
            carriage.DONKEY_STS_STATUS === 'in_progress'
        ) {
            $('#donkey_link').show();
        }
        if (carriage.DONKEY_STS_LINK != null) {
            let DONKEY_STS_LINK = '';
            const DESCRIPTION_DONKEY_STS_LINK = carriage.DONKEY_STS_LINK.DESCRIPTION.split(",");

            $.each(carriage.DONKEY_STS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_DONKEY_STS_LINK[index] !== '') {
                    DONKEY_STS_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_DONKEY_STS_LINK[index] + '</li>';
                } else if (DESCRIPTION_DONKEY_STS_LINK[index] === '') {
                } else {
                    DONKEY_STS_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#donkey_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_donkey_link").html(DONKEY_STS_LINK);
            $('#donkey_file').show();
        } else {
            $('#donkey_file').hide();
            $('#donkey_link').addClass('status-info_confirmation_error');
            $("#list_file_donkey_link").html();
        }
    }

    /** Блок прицепа */
    function trailer(carriage) {
        /** Подтверждения владения прицеп */
        if (carriage.TRAILER_CHECKS != null) {
            $('#trailer').show();
            if (carriage.TRAILER_CHECKS_ERROR !== false) {
                $('#trailer_check').addClass('detail-status_good').html(carriage.TRAILER_CHECKS);
            } else {
                $('#trailer_check').addClass('detail-status_error').html('Выполнено ' + carriage.TRAILER_CHECKS);
            }
        } else {
            $('#trailer_check').html('').removeClass();
        }
        /** Номерной знак прицеп */
        if (carriage.TRAILER_LICENSE_PLATE != null) {
            $('#trailer_plate').html(carriage.TRAILER_LICENSE_PLATE);
        } else {
            $('#trailer_plate').html('');
        }
        /** СТС прицеп */
        if (carriage.TRAILER_STS_STATUS === 'passed' ||
            carriage.TRAILER_STS_STATUS === 'in_progress'
        ) {
            $('#trailer_ctc_link').show();
        }
        if (carriage.TRAILER_STS_LINK != null) {
            let TRAILER_STS_LINK = '';
            const DESCRIPTION_TRAILER_STS_LINK = carriage.TRAILER_STS_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_STS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_STS_LINK[index] !== '') {
                    TRAILER_STS_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_STS_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_STS_LINK[index] === '') {
                } else {
                    TRAILER_STS_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_ctc_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_ctc_link").html(TRAILER_STS_LINK);
            $('#trailer_ctc_file').show();
        } else {
            $('#trailer_ctc_file').hide();
            $('#trailer_ctc_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_ctc_link").html();
        }
        /** Договор аренды прицеп */
        if (carriage.TRAILER_RENT_AGREEMENT_STATUS === 'passed' ||
            carriage.TRAILER_RENT_AGREEMENT_STATUS === 'in_progress'
        ) {
            $('#trailer_rent_link').show();
        }
        if (carriage.TRAILER_RENT_AGREEMENT_LINK != null) {
            let TRAILER_RENT_AGR_LINK = '';
            const DESCRIPTION_TRAILER_RENT_AGR_LINK = carriage.TRAILER_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_RENT_AGR_LINK[index] !== '') {
                    TRAILER_RENT_AGR_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_RENT_AGR_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_RENT_AGR_LINK[index] === '') {
                } else {
                    TRAILER_RENT_AGR_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_rent_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_rent_link").html(TRAILER_RENT_AGR_LINK);
            $('#trailer_rent_file').show();
        } else {
            $('#trailer_rent_file').hide();
            $('#trailer_rent_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_rent_link").html();
        }
    }

    /** Блок второго прицепа */
    function trailerSec(carriage) {
        /** Подтверждения владения второго прицеп */
        if (carriage.TRAILER_SECONDARY_CHECKS != null) {
            $('#trailer_sec').show();
            if (carriage.TRAILER_SECONDARY_CHECKS_ERROR !== false) {
                $('#trailer_sec_check').addClass('detail-status_good').html(carriage.TRAILER_SECONDARY_CHECKS);
            } else {
                $('#trailer_sec_check').addClass('detail-status_error').html('Выполнено ' + carriage.TRAILER_SECONDARY_CHECKS);
            }
        } else {
            $('#trailer_sec_check').html('').removeClass();
        }
        /** Номерной знак второго прицеп */
        if (carriage.TRAILER_SECONDARY_LICENSE_PLATE != null) {
            $('#trailer_sec_plate').html(carriage.TRAILER_SECONDARY_LICENSE_PLATE);
        } else {
            $('#trailer_sec_plate').html('');
        }
        /** СТС второго прицеп */
        if (carriage.TRAILER_SECONDARY_STS_STATUS === 'passed' ||
            carriage.TRAILER_SECONDARY_STS_STATUS === 'in_progress'
        ) {
            $('#trailer_sec_ctc_link').show();
        }
        if (carriage.TRAILER_SECONDARY_STS_LINK != null) {
            let TRAILER_SEC_STS_LINK = '';
            const DESCRIPTION_TRAILER_SEC_STS_LINK = carriage.TRAILER_SECONDARY_STS_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_SECONDARY_STS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_SEC_STS_LINK[index] !== '') {
                    TRAILER_SEC_STS_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_SEC_STS_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_SEC_STS_LINK[index] === '') {
                } else {
                    TRAILER_SEC_STS_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_sec_ctc_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_ctc_link").html(TRAILER_SEC_STS_LINK);
            $('#trailer_sec_ctc_file').show();
        } else {
            $('#trailer_sec_ctc_file').hide();
            $('#trailer_sec_ctc_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_ctc_link").html();
        }
        /** Договор аренды второго прицепа */
        if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_STATUS === 'passed' ||
            carriage.TRAILER_SECONDARY_RENT_AGREEMENT_STATUS === 'in_progress'
        ) {
            $('#trailer_sec_rent_link').show();
        }
        if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK != null) {
            let TRAILER_SEC_RENT_LINK = '';
            const DESCRIPTION_TRAILER_SEC_RENT_LINK = carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_SEC_RENT_LINK[index] !== '') {
                    TRAILER_SEC_RENT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_SEC_RENT_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_SEC_RENT_LINK[index] === '') {
                } else {
                    TRAILER_SEC_RENT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_sec_rent_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_rent_link").html(TRAILER_SEC_RENT_LINK);
            $('#trailer_sec_rent_file').show();
        } else {
            $('#trailer_sec_rent_file').hide();
            $('#trailer_sec_rent_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_rent_link").html();
        }
        /** Договор с лизинговой компанией второго (прицеп) */
        if (carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS === 'passed' ||
            carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS === 'in_progress'
        ) {
            $('#trailer_sec_lias_link').show();
        }
        if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK != null) {
            let TRAILER_SEC_LEASING_COMPANY_LINK = '';
            const DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK = carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] !== '') {
                    TRAILER_SEC_LEASING_COMPANY_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] === '') {
                } else {
                    TRAILER_SEC_LEASING_COMPANY_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_sec_lias_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_lias_link").html(TRAILER_SEC_LEASING_COMPANY_LINK);
            $('#trailer_sec_lias_file').show();
        } else {
            $('#trailer_sec_lias_file').hide();
            $('#trailer_sec_lias_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_lias_link").html();
        }
        /** Свидетельство о браке второго (прицепа) */
        if (carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS === 'passed' ||
            carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS === 'in_progress'
        ) {
            $('#trailer_sec_cer_link').show();
        }
        if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK != null) {
            let TRAILER_SEC_CERTIFICATE_LINK = '';
            const DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK = carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] !== '') {
                    TRAILER_SEC_CERTIFICATE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] === '') {
                } else {
                    TRAILER_SEC_CERTIFICATE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_sec_cer_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_cer_link").html(TRAILER_SEC_CERTIFICATE_LINK);
        } else {
            $('#trailer_sec_cer_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_cer_link").html();
        }
        /** Договор безвозмездного использования второго (прицепа) */
        if (carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS === 'passed' ||
            carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS === 'in_progress'
        ) {
            $('#trailer_sec_usage_link').show();
        }
        if (carriage.TRAILER_SECONDARY_FREE_USAGE_LINK != null) {
            let TRAILER_SEC_FREE_USAGE_LINK = '';
            const DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK = carriage.TRAILER_SECONDARY_FREE_USAGE_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRAILER_SECONDARY_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] !== '') {
                    TRAILER_SEC_FREE_USAGE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] === '') {
                } else {
                    TRAILER_SEC_FREE_USAGE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#trailer_sec_usage_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_usage_link").html(TRAILER_SEC_FREE_USAGE_LINK);
            $('#trailer_sec_usage_file').show();
        } else {
            $('#trailer_sec_usage_file').hide();
            $('#trailer_sec_usage_link').addClass('status-info_confirmation_error');
            $("#list_file_trailer_sec_usage_link").html();
        }
    }

    /** Блок грузовика */
    function truck(carriage) {
        /** Подтверждение владения грузовик */
        if (carriage.TRUCK_CHECKS != null) {
            $('#truck').show();
            if (carriage.TRUCK_CHECKS_ERROR !== false) {
                $('#truck_check').addClass('detail-status_good').html(carriage.TRUCK_CHECKS);
            } else {
                $('#truck_check').addClass('detail-status_error').html('Выполнено ' + carriage.TRUCK_CHECKS);
            }
        } else {
            $('#truck_check').html('').removeClass();
        }
        /** Номерной знак грузовик */
        if (carriage.TRUCK_LICENSE_PLATE != null) {
            $('#truck_plate').html(carriage.TRUCK_LICENSE_PLATE);
        } else {
            $('#truck_plate').html('');
        }
        /** СТС грузовик */
        if (carriage.TRUCK_STS_STATUS === 'passed' ||
            carriage.TRUCK_STS_STATUS === 'in_progress'
        ) {
            $('#truck_sts_link').show();
        }
        if (carriage.TRUCK_STS_LINK != null) {
            let TRUCK_STS_LINK = '';
            const DESCRIPTION_TRUCK_STS_LINK = carriage.TRUCK_STS_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRUCK_STS_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRUCK_STS_LINK[index] !== '') {
                    TRUCK_STS_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + DESCRIPTION_TRUCK_STS_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRUCK_STS_LINK[index] === '') {
                } else {
                    TRUCK_STS_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#truck_sts_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_truck_sts_link").html(TRUCK_STS_LINK);
            $('#truck_sts_file').show();
        } else {
            $('#truck_sts_file').hide();
            $('#truck_sts_link').addClass('status-info_confirmation_error');
            $("#list_file_truck_sts_link").html();
        }
        /** Договор аренды грузовик */
        if (carriage.TRUCK_RENT_AGREEMENT_STATUS === 'passed' ||
            carriage.TRUCK_RENT_AGREEMENT_STATUS === 'in_progress'
        ) {
            $('#truck_rent').show();
        }
        if (carriage.TRUCK_RENT_AGREEMENT_LINK != null) {
            let TRUCK_RENT_LINK = '';
            const DESCRIPTION_TRUCK_RENT_LINK = carriage.TRUCK_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRUCK_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRUCK_RENT_LINK[index] !== '') {
                    TRUCK_RENT_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRUCK_RENT_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRUCK_RENT_LINK[index] === '') {
                } else {
                    TRUCK_RENT_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#truck_rent').show().removeClass('status-info_confirmation_error');
            $("#list_file_truck_rent").html(TRUCK_RENT_LINK);
            $('#truck_link').show();
        } else {
            $('#truck_link').hide();
            $('#truck_rent').addClass('status-info_confirmation_error');
            $("#list_file_truck_rent").html();
        }
        /** Договор с лизинговой компанией грузовик */
        if (carriage.TRUCK_LEASING_COMPANY_STATUS === 'passed' ||
            carriage.TRUCK_LEASING_COMPANY_STATUS === 'in_progress'
        ) {
            $('#truck_leas_link').show();
        }
        if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK != null) {
            let TRUCK_LEASING_COMPANY_LINK = '';
            const DESCRIPTION_TRUCK_LEASING_COMPANY_LINK = carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] !== '') {
                    TRUCK_LEASING_COMPANY_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] === '') {
                } else {
                    TRUCK_LEASING_COMPANY_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#truck_leas_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_truck_leas_link").html(TRUCK_LEASING_COMPANY_LINK);
            $('#truck_leas_file').show();
        } else {
            $('#truck_leas_file').hide();
            $('#truck_leas_link').addClass('status-info_confirmation_error');
            $("#list_file_truck_leas_link").html();
        }
        /** Свидетельство о браке грузовик */
        if (carriage.TRUCK_CERTIFICATE_STATUS === 'passed' ||
            carriage.TRUCK_CERTIFICATE_STATUS === 'in_progress'
        ) {
            $('#truck_cert_link').show();
        }
        if (carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK != null) {
            let TRUCK_CERTIFICATE_LINK = '';
            const DESCRIPTION_TRUCK_CERTIFICATE_LINK = carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] !== '') {
                    TRUCK_CERTIFICATE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] === '') {
                } else {
                    TRUCK_CERTIFICATE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#truck_cert_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_truck_cert_link").html(TRUCK_CERTIFICATE_LINK);
            $('#truck_cert_file').show();
        } else {
            $('#truck_cert_file').hide();
            $('#truck_cert_link').addClass('status-info_confirmation_error');
            $("#list_file_truck_cert_link").html();
        }
        /** Договор безвозмездного использования грузовик */
        if (carriage.TRUCK_FREE_USAGE_STATUS === 'passed' ||
            carriage.TRUCK_FREE_USAGE_STATUS === 'in_progress'
        ) {
            $('#truck_usage_link').show();
        }
        if (carriage.TRUCK_FREE_USAGE_LINK != null) {
            let TRUCK_FREE_USAGE_LINK = '';
            const DESCRIPTION_TRUCK_FREE_USAGE_LINK = carriage.TRUCK_FREE_USAGE_LINK.DESCRIPTION.split(",");

            $.each(carriage.TRUCK_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                if (DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] !== '') {
                    TRUCK_FREE_USAGE_LINK += '<li><a href="' + value + '" target="_blank">' + DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] + '</li>';
                } else if (DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] === '') {
                } else {
                    TRUCK_FREE_USAGE_LINK += '<li><a href="' + value + '" target="_blank">Файл ' + (index + 1) + '</li>';
                }
            });

            $('#truck_usage_link').show().removeClass('status-info_confirmation_error');
            $("#list_file_truck_usage_link").html(TRUCK_FREE_USAGE_LINK);
            $('#truck_usage_file').show();
        } else {
            $('#truck_usage_file').hide();
            $('#truck_usage_link').addClass('status-info_confirmation_error');
            $("#list_file_truck_usage_link").html();
        }
    }
});