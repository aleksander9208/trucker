$(document).ready(function() {

    /**
     * Стилизация инпута в фильтре
     */
    $(window).keyup(function(e){
        var target = $('.filter_checkbox input:focus');
        if (e.keyCode == 9 && $(target).length){
            $(target).parent().addClass('focused');
        }
    });

    $('.filter_checkbox input').focusout(function(){
        $(this).parent().removeClass('focused');
    });

    $(window).keyup(function(e){
        var target = $('.filter_list-label input:focus');
        if (e.keyCode == 9 && $(target).length){
            $(target).parent().addClass('focused_type');
        }
    });

    $('.filter_list-label input').focusout(function(){
        $(this).parent().removeClass('focused_type');
    });

    /**
     * Отображаем режим выбора
     * проблемных организаций
     */
    $('.filter_top-organizations span').click(function () {
        if ($('.filter_list-type-organizations').css('display') === 'none') {
            $('.filter_list-type-organizations').css('display','flex');
        } else {
            $('.filter_list-type-organizations').hide();
        }
    });

    /**
     * Получаем информацию в
     * инфо бар по перевозки
     */
    $(document).on('click', '.info_bar-content', function() {
        const id = $(this).parents('.main-grid-row').attr('data-id');

        /** Скрываем блоки */
        hideBlock();

        BX.ajax({
            url: '/api/v1/vhs/vitrina/' + id,
            method: 'POST',
            data: '',
            timeout: 2000,
            dataType: 'json',
            onsuccess: function(response){
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
                    //     $('#checklist_forwarder').hide();
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

                if(response.status === 'error') {
                    $('#error').html(response.errors[0].message);
                }
            },
        });
    });

    $(document).on('click', '#link_archiv', function() {
        const id = $(this).attr('data-id');

        /** Скрываем блоки */
        hideBlock();

        BX.ajax({
            url: '/api/v1/vhs/vitrina/archiv/' + id,
            method: 'POST',
            data: '',
            timeout: 2000,
            dataType: 'json',
            onsuccess: function(response){
                if (response.status === 'success') {

                }

                if(response.status === 'error') {
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
        if (carriage.CONT_CHECK != null) {
            $('#contract').show();
            if (carriage.CONT_CHECK_ERROR !== false) {
                $('#detail_status-transportation').addClass('detail-status_good').html(carriage.CONT_CHECK);
            } else {
                $('#detail_status-transportation').addClass('detail-status_error').html('Выполнено ' + carriage.CONT_CHECK);
            }
        } else {
            $('#detail_status-transportation').html('').removeClass();
        }
        /** Договор перевозки */
        if (carriage.CONT_TRANSPORT_STATUS === 'passed') {
            $('#transport_link').show();
        }
        if (carriage.CONT_TRANSPORT_LINK != null) {
            $('#transport_link').removeClass('status-info_confirmation_error');
            $('#transport_file').show().attr("href", carriage.CONT_TRANSPORT_LINK);
        } else {
            $('#transport_link').addClass('status-info_confirmation_error');
            $('#transport_file').hide().attr("href", '');
        }
        /** Договор транспортной экспедиции */
        if (carriage.CONT_EXP_STATUS === 'passed') {
            $('#contract_link').show();
        }
        if (carriage.CONT_EXP_LINK != null) {
            $('#contract_link').removeClass('status-info_confirmation_error');
            $('#contract_file').show().attr("href", carriage.CONT_EXP_LINK);
        } else {
            $('#contract_link').addClass('status-info_confirmation_error');
            $('#contract_file').hide().attr("href", '');
        }
        /** Заказ (разовая договор-заявка) */
        if (carriage.CONT_ORDER_ONE_TIME_STATUS === 'passed') {
            $('#one_time_link').show();
        }
        if (carriage.CONT_ORDER_ONE_TIME != null) {
            $('#one_time_link').removeClass('status-info_confirmation_error');
            $('#one_time_file').show().attr("href", carriage.CONT_ORDER_ONE_TIME);
        } else {
            $('#one_time_link').addClass('status-info_confirmation_error');
            $('#one_time_file').hide().attr("href", '');
        }
    }

    /** Оформление перевозки */
    function executionDocuments(carriage) {
        /** Оформление перевозки */
        if (carriage.DOC_CHECK != null) {
            $('#execution_documents').show();
            if (carriage.DOC_CHECK_ERROR !== false) {
                $('#documents_check').addClass('detail-status_good').html(carriage.DOC_CHECK);
            } else {
                $('#documents_check').addClass('detail-status_error').html('Выполнено ' + carriage.DOC_CHECK);
            }
        } else {
            $('#documents_check').html('').removeClass();
        }
        /** Заявка на перевозку */
        if (carriage.DOC_APP_TRANSPORT_STATUS === 'passed') {
            $('#documents_link').show();
        }
        if (carriage.DOC_APP_TRANSPORT_LINK != null) {
            $('#documents_link').show().removeClass('status-info_confirmation_error');
            $('#documents_file').show().attr("href", carriage.DOC_APP_TRANSPORT_LINK);
        } else {
            $('#documents_link').addClass('status-info_confirmation_error');
            $('#documents_file').hide().attr("href", '');
        }
        /** Подписанная ЭТрН */
        if (carriage.DOC_EPD_STATUS === 'passed') {
            $('#epd_link').show();
        }
        if (carriage.DOC_EPD_LINK != null) {
            $('#epd_link').show().removeClass('#epd_link').removeClass('status-info_confirmation_error');
            $('#epd_file').show().attr("href", carriage.DOC_EPD_LINK);
        } else {
            $('#epd_link').addClass('status-info_confirmation_error');
            $('#epd_file').hide().attr("href", '');
        }
        /** Подтверждение договорных отношений с водителем */
        if (carriage.DOC_DRIVER_APP_STATUS === 'passed') {
            $('#driver_link').show();
        }
        if (carriage.DOC_DRIVER_APP_LINK != null) {
            $('#driver_link').show().removeClass('status-info_confirmation_error');
            $('#driver_file').show().attr("href", carriage.DOC_DRIVER_APP_LINK);
        } else {
            $('#driver_link').addClass('status-info_confirmation_error');
            $('#driver_file').hide().attr("href", '');
        }
        /** Поручение экспедитору */
        if (carriage.DOC_EXP_STATUS === 'passed') {
            $('#exp_link').show();
        }
        if (carriage.DOC_EXP_LINK != null) {
            $('#exp_link').show().removeClass('status-info_confirmation_error');
            $('#exp_file').attr("href", carriage.DOC_EXP_LINK);
        } else {
            $('#exp_link').addClass('status-info_confirmation_error');
            $('#exp_file').hide().attr("href", '');
        }
        /** Экспедиторская расписка */
        if (carriage.DOC_EXP_RECEIPT_STATUS === 'passed') {
            $('#receipt_link').show();
        }
        if (carriage.DOC_EXP_RECEIPT_LINK != null) {
            $('#receipt_link').show().removeClass('status-info_confirmation_error');
            $('#receipt_file').show().attr("href", carriage.DOC_EXP_RECEIPT_LINK);
        } else {
            $('#receipt_link').addClass('status-info_confirmation_error');
            $('#receipt_file').hide().attr("href", '');
        }
    }

    /** Автоматические проверки */
    function automaticChecks(carriage) {
        /** Автоматические проверки */
        if (carriage.AUTO_CHECKS != null) {
            $('#automatic').show();
            if (carriage.AUTO_CHECK_ERROR !== false) {
                $('#auto_check').addClass('detail-status_good').html(carriage.AUTO_CHECKS);
            } else {
                $('#auto_check').addClass('detail-status_error').html('Выполнено ' + carriage.AUTO_CHECKS);
            }
        } else {
            $('#auto_check').html('').removeClass();
        }
        /** Стоимость перевозки соответствует рыночным ценам */
        if (carriage.AUTO_PRICES_STATUS === 'passed') {
            $('#prices_link').show();
        }
        if (carriage.AUTO_PRICES !== false) {
            $('#prices_link').show().removeClass('status-info_confirmation_error');
            $('#prices_file').show();
        } else {
            $('#prices_link').addClass('status-info_confirmation_error');
            $('#prices_file').hide();
        }
        /** Подтверждения перевозки через геомониторинг */
        if (carriage.AUTO_GEO_STATUS === 'passed') {
            $('#geo_link').show();
        }
        if (carriage.AUTO_GEO != null) {
            $('#geo_link').show().removeClass('status-info_confirmation_error');
            $('#geo_file').show().attr("href", carriage.AUTO_GEO);
        } else {
            $('#geo_link').addClass('status-info_confirmation_error');
            $('#geo_file').hide().attr("href", '');
        }
    }

    /** Бухгалтерские документы */
    function accounting(carriage) {
        /** Бухгалтерские документы */
        if (carriage.ACC_CHECKS != null) {
            $('#accounting').show();
            if (carriage.ACC_CHECKS_ERROR !== false) {
                $('#accounting_check').addClass('detail-status_good').html(carriage.ACC_CHECKS);
            } else {
                $('#accounting_check').addClass('detail-status_error').html('Выполнено ' + carriage.AUTO_CHECKS);
            }
        } else {
            $('#accounting_check').html('').removeClass();
        }
        /** Счёт */
        if (carriage.ACC_INVOICE_STATUS === 'passed') {
            $('#invoice_link').show();
        }
        if (carriage.ACC_INVOICE_LINK != null) {
            $('#invoice_link').show().removeClass('status-info_confirmation_error');
            $('#invoice_file').show().attr("href", carriage.ACC_INVOICE_LINK);
        } else {
            $('#invoice_link').addClass('status-info_confirmation_error');
            $('#invoice_file').hide().attr("href", '');
        }
        /** Акт о приемке выполненных работ по услуге */
        if (carriage.ACC_ACT_ACC_STATUS === 'passed') {
            $('#act_link').show();
        }
        if (carriage.ACC_ACT_ACC_LINK != null) {
            $('#act_link').show().removeClass('status-info_confirmation_error');
            $('#act_file').show().attr("href", carriage.ACC_ACT_ACC_LINK);
        } else {
            $('#act_link').addClass('status-info_confirmation_error');
            $('#act_file').hide().attr("href", '');
        }
        /** Акт о приемке выполненных работ, включающий несколько перевозок */
        if (carriage.ACC_ACT_MULTI_TRANSPORT_STATUS === 'passed') {
            $('#multi_link').show();
        }
        if (carriage.ACC_ACT_MULTI_TRANSPORT_LINK_VALUE != null) {
            $('#multi_link').show().removeClass('status-info_confirmation_error');
            $('#multi_file').show().attr("href", carriage.ACC_ACT_MULTI_TRANSPORT_LINK_VALUE);
        } else {
            $('#multi_link').addClass('status-info_confirmation_error');
            $('#multi_file').hide().attr("href", '');
        }
        /** Реестр на перевозки */
        if (carriage.ACC_TRANSPORT_REG_STATUS === 'passed') {
            $('#reg_link').show();
        }
        if (carriage.ACC_TRANSPORT_REG_LINK != null) {
            $('#reg_link').show().removeClass('status-info_confirmation_error');
            $('#reg_file').show().attr("href", carriage.ACC_TRANSPORT_REG_LINK);
        } else {
            $('#reg_link').addClass('status-info_confirmation_error');
            $('#reg_file').hide().attr("href", '');
        }
        /** Счёт-фактура */
        if (carriage.ACC_TAX_INVOICE_STATUS === 'passed') {
            $('#tax_link').show();
        }
        if (carriage.ACC_TAX_INVOICE_LINK != null) {
            $('#tax_link').show().removeClass('status-info_confirmation_error');
            $('#tax_file').show().attr("href", carriage.ACC_TAX_INVOICE_LINK);
        } else {
            $('#tax_link').addClass('status-info_confirmation_error');
            $('#tax_file').hide().attr("href", '');
        }
        /** УПД */
        if (carriage.ACC_UPD_STATUS === 'passed') {
            $('#upd_link').show();
        }
        if (carriage.ACC_UPD_LINK != null) {
            $('#upd_link').show().removeClass('status-info_confirmation_error');
            $('#upd_file').show().attr("href", carriage.ACC_UPD_LINK);
        } else {
            $('#upd_link').addClass('status-info_confirmation_error');
            $('#upd_file').hide().attr("href", '');
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
        if (carriage.DONKEY_LIC_PLATE != null) {
            $('#donkey_plate').html(carriage.DONKEY_LIC_PLATE);
        } else {
            $('#donkey_plate').html('');
        }
        /** СТС тягач */
        if (carriage.DONKEY_STS_STATUS === 'passed') {
            $('#donkey_link').show();
        }
        if (carriage.DONKEY_STS_LINK != null) {
            $('#donkey_link').show().removeClass('status-info_confirmation_error');
            $('#donkey_file').show().attr("href", carriage.DONKEY_STS_LINK);
        } else {
            $('#donkey_link').addClass('status-info_confirmation_error');
            $('#donkey_file').hide().attr("href", '');
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
        if (carriage.TRAILER_LIC_PLATE != null) {
            $('#trailer_plate').html(carriage.TRAILER_LIC_PLATE);
        } else {
            $('#trailer_plate').html('');
        }
        /** СТС прицеп */
        if (carriage.TRAILER_STS_STATUS === 'passed') {
            $('#trailer_ctc_link').show();
        }
        if (carriage.TRAILER_STS_LINK != null) {
            $('#trailer_ctc_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_ctc_file').show().attr("href", carriage.TRAILER_STS_LINK);
        } else {
            $('#trailer_ctc_link').addClass('status-info_confirmation_error');
            $('#trailer_ctc_file').hide().attr("href", '');
        }
        /** Договор аренды прицеп */
        if (carriage.TRAILER_RENT_AGR_STATUS === 'passed') {
            $('#trailer_rent_link').show();
        }
        if (carriage.TRAILER_RENT_AGR_LINK != null) {
            $('#trailer_rent_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_rent_file').show().attr("href", carriage.TRAILER_RENT_AGR_LINK);
        } else {
            $('#trailer_rent_link').addClass('status-info_confirmation_error');
            $('#trailer_rent_file').hide().attr("href", '');
        }
    }

    /** Блок второго прицепа */
    function trailerSec(carriage) {
        /** Подтверждения владения второго прицеп */
        if (carriage.TRAILER_SEC_CHECKS != null) {
            $('#trailer_sec').show();
            if (carriage.TRAILER_SEC_CHECKS_ERROR !== false) {
                $('#trailer_sec_check').addClass('detail-status_good').html(carriage.TRAILER_SEC_CHECKS);
            } else {
                $('#trailer_sec_check').addClass('detail-status_error').html('Выполнено ' + carriage.TRAILER_SEC_CHECKS);
            }
        } else {
            $('#trailer_sec_check').html('').removeClass();
        }
        /** Номерной знак второго прицеп */
        if (carriage.TRAILER_SEC_LIC_PLATE != null) {
            $('#trailer_sec_plate').html(carriage.TRAILER_SEC_LIC_PLATE);
        } else {
            $('#trailer_sec_plate').html('');
        }
        /** СТС второго прицеп */
        if (carriage.TRAILER_SEC_STS_STATUS === 'passed') {
            $('#trailer_sec_ctc_link').show();
        }
        if (carriage.TRAILER_SEC_STS_LINK != null) {
            $('#trailer_sec_ctc_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_sec_ctc_file').show().attr("href", carriage.TRAILER_SEC_STS_LINK);
        } else {
            $('#trailer_sec_ctc_link').addClass('status-info_confirmation_error');
            $('#trailer_sec_ctc_file').hide().attr("href", '');
        }
        /** Договор аренды второго прицепа */
        if (carriage.TRAILER_SEC_RENT_STATUS === 'passed') {
            $('#trailer_sec_rent_link').show();
        }
        if (carriage.TRAILER_SEC_RENT_LINK != null) {
            $('#trailer_sec_rent_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_sec_rent_file').show().attr("href", carriage.TRAILER_SEC_RENT_LINK);
        } else {
            $('#trailer_sec_rent_link').addClass('status-info_confirmation_error');
            $('#trailer_sec_rent_file').hide().attr("href", '');
        }
        /** Договор с лизинговой компанией второго (прицеп) */
        if (carriage.TRAILER_SEC_LEASING_COMPANY_STATUS === 'passed') {
            $('#trailer_sec_lias_link').show();
        }
        if (carriage.TRAILER_SEC_LEASING_COMPANY_LINK != null) {
            $('#trailer_sec_lias_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_sec_lias_file').show().attr("href", carriage.TRAILER_SEC_LEASING_COMPANY_LINK);
        } else {
            $('#trailer_sec_lias_link').addClass('status-info_confirmation_error');
            $('#trailer_sec_lias_file').hide().attr("href", '');
        }
        /** Свидетельство о браке второго (прицепа) */
        if (carriage.TRAILER_SEC_CERTIFICATE_STATUS === 'passed') {
            $('#trailer_sec_cer_link').show();
        }
        if (carriage.TRAILER_SEC_CERTIFICATE_LINK != null) {
            $('#trailer_sec_cer_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_sec_cer_file').show().attr("href", carriage.TRAILER_SEC_CERTIFICATE_LINK);
        } else {
            $('#trailer_sec_cer_link').addClass('status-info_confirmation_error');
            $('#trailer_sec_cer_file').hide().attr("href", '');
        }
        /** Договор безвозмездного использования второго (прицепа) */
        if (carriage.TRAILER_SEC_FREE_USAGE_STATUS === 'passed') {
            $('#trailer_sec_usage_link').show();
        }
        if (carriage.TRAILER_SEC_FREE_USAGE_LINK != null) {
            $('#trailer_sec_usage_link').show().removeClass('status-info_confirmation_error');
            $('#trailer_sec_usage_file').show().attr("href", carriage.TRAILER_SEC_FREE_USAGE_LINK);
        } else {
            $('#trailer_sec_usage_link').addClass('status-info_confirmation_error');
            $('#trailer_sec_usage_file').hide().attr("href", '');
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
        if (carriage.TRUCK_LIC_PLATE != null) {
            $('#truck_plate').html(carriage.TRUCK_LIC_PLATE);
        } else {
            $('#truck_plate').html('');
        }
        /** СТС грузовик */
        if (carriage.TRUCK_STS_STATUS === 'passed') {
            $('#truck_sts_link').show();
        }
        if (carriage.TRUCK_STS_LINK != null) {
            $('#truck_sts_link').show().removeClass('status-info_confirmation_error');
            $('#truck_sts_file').show().attr("href", carriage.TRUCK_STS_LINK);
        } else {
            $('#truck_sts_link').addClass('status-info_confirmation_error');
            $('#truck_sts_file').hide().attr("href", '');
        }
        /** Договор аренды грузовик */
        if (carriage.TRUCK_RENT_STATUS === 'passed') {
            $('#truck_rent').show();
        }
        if (carriage.TRUCK_RENT_LINK != null) {
            $('#truck_rent').show().removeClass('status-info_confirmation_error');
            $('#truck_link').show().attr("href", carriage.TRUCK_RENT_LINK);
        } else {
            $('#truck_rent').addClass('status-info_confirmation_error');
            $('#truck_link').hide().attr("href", '');
        }
        /** Договор с лизинговой компанией грузовик */
        if (carriage.TRUCK_LEASING_COMPANY_STATUS === 'passed') {
            $('#truck_leas_link').show();
        }
        if (carriage.TRUCK_LEASING_COMPANY_LINK != null) {
            $('#truck_leas_link').show().removeClass('status-info_confirmation_error');
            $('#truck_leas_file').show().attr("href", carriage.TRUCK_LEASING_COMPANY_LINK);
        } else {
            $('#truck_leas_link').addClass('status-info_confirmation_error');
            $('#truck_leas_file').hide().attr("href", '');
        }
        /** Свидетельство о браке грузовик */
        if (carriage.TRUCK_CERTIFICATE_STATUS === 'passed') {
            $('#truck_cert_link').show();
        }
        if (carriage.TRUCK_CERTIFICATE_LINK != null) {
            $('#truck_cert_link').show().removeClass('status-info_confirmation_error');
            $('#truck_cert_file').show().attr("href", carriage.TRUCK_CERTIFICATE_LINK);
        } else {
            $('#truck_cert_link').addClass('status-info_confirmation_error');
            $('#truck_cert_file').hide().attr("href", '');
        }
        /** Договор безвозмездного использования грузовик */
        if (carriage.TRUCK_FREE_USAGE_STATUS === 'passed') {
            $('#truck_usage_link').show();
        }
        if (carriage.TRUCK_FREE_USAGE_LINK != null) {
            $('#truck_usage_link').show().removeClass('status-info_confirmation_error');
            $('#truck_usage_file').show().attr("href", carriage.TRUCK_FREE_USAGE_LINK);
        } else {
            $('#truck_usage_link').addClass('status-info_confirmation_error');
            $('#truck_usage_file').hide().attr("href", '');
        }
    }
});