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

        BX.ajax({
            url: '/api/v1/vhs/vitrina/' + id,
            method: 'POST',
            data: '',
            timeout: 2000,
            dataType: 'json',
            onsuccess: function(response){
                if (response.status === 'success') {
                    const carriage = response.data;

                    let [year, month, day] = carriage.DATE_VALUE.split("-");

                    /**
                     * Данные перевозки для инфо бара
                     */
                    /** Номер перевозки */
                    $('#carriage_id').html(carriage.NAME);
                    /** Дата перевозки */
                    $('#carriage_date').html(day + '.' + month + '.' + year);
                    /** Рыночная цена */
                    $('#carriage_deviation-price').html(carriage.DEVIATION_MARKET_PRICE_VALUE + '%');
                    /** Грузовладелец */
                    if (carriage.CARGO_OWNER_VALUE != null) {
                        $('#carriage_owner').html(
                            'Грузовладелец <span class="carriage_name">' +
                            carriage.CARGO_OWNER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.CARGO_OWNER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_owner').html('');
                    }
                    /** Перевозчик */
                    if (carriage.CARRIER_VALUE != null) {
                        $('#carriage_carrier').html(
                            'Перевозчик <span class="carriage_name">' +
                            carriage.CARRIER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.CARRIER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_carrier').html('');
                    }
                    /** Экспедитор */
                    if (carriage.FORWARDER_VALUE != null) {
                        $('#carriage_forwarder').html(
                            'Экспедитор <span class="carriage_name">' +
                            carriage.FORWARDER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.FORWARDER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_forwarder').html('');
                    }
                    /** Ссылка на архив документов */
                    if (carriage.LINK_DOCUMENT != null) {
                        $('#link_archiv').attr("href", carriage.LINK_DOCUMENT).show();
                    } else {
                        $('#link_archiv').attr("href", '').hide();
                    }

                    /** Чек-лист с перевозчиком */
                    // if (carriage.CHECKLIST_CARRIER != null) {
                    //
                    // } else {
                    //
                    // }

                    /** Чек-лист с экспедитором */
                    if (carriage.CHECKLIST_FORWARDER != null) {
                        $('#checklist_forwarder').show();
                    } else {
                        $('#checklist_forwarder').hide();
                    }

                    /** Подписанные договоры */
                    if (carriage.CONTRACT_VALUE != null) {
                        if (carriage.CONTRACT_CHECK !== false) {
                            $('#detail_status-transportation').addClass('detail-status_good').html(carriage.CONTRACT_VALUE);
                        } else {
                            $('#detail_status-transportation').addClass('detail-status_error').html('Выполнено ' + carriage.CONTRACT_VALUE);
                        }
                    } else {
                        $('#detail_status-transportation').html('').removeClass();
                    }

                    /** Договор перевозки */
                    if (carriage.CONTRACT_LINK_VALUE != null) {
                        $('#status_transportation-link').removeClass('status-info_confirmation_error');
                        $('#status_transportation-file').show().attr("href", carriage.CONTRACT_LINK_VALUE);
                    } else {
                        $('#status_transportation-link').addClass('status-info_confirmation_error');
                        $('#status_transportation-file').hide().attr("href", '');
                    }
                    /** Оформление перевозки */
                    if (carriage.DOCUMENTS_VALUE != null) {
                        if (carriage.DOCUMENTS_CHECK !== false) {
                            $('#documents_check').addClass('detail-status_good').html(carriage.DOCUMENTS_VALUE);
                        } else {
                            $('#documents_check').addClass('detail-status_error').html('Выполнено ' + carriage.DOCUMENTS_VALUE);
                        }
                    } else {
                        $('#documents_check').html('').removeClass();
                    }
                    /** Заявка на перевозку */
                    if (carriage.DOCUMENTS_LINK != null) {
                        $('#documents_link').removeClass('status-info_confirmation_error');
                        $('#documents_file').show().attr("href", carriage.DOCUMENTS_LINK);
                    } else {
                        $('#documents_link').addClass('status-info_confirmation_error');
                        $('#documents_file').hide().attr("href", '');
                    }
                    /** Подписанная ЭТрН */
                    if (carriage.EPD_LINK != null) {
                        $('#epd_link').removeClass('status-info_confirmation_error');
                        $('#epd_file').show().attr("href", carriage.EPD_LINK);
                    } else {
                        $('#epd_link').addClass('status-info_confirmation_error');
                        $('#epd_file').hide().attr("href", '');
                    }
                    /** Стоимость перевозки соответствует рыночным ценам */
                    if (carriage.PRICES_LINK != null) {
                        $('#prices_link').removeClass('status-info_confirmation_error');
                        $('#prices_file').show().attr("href", carriage.PRICES_LINK);
                    } else {
                        $('#prices_link').addClass('status-info_confirmation_error');
                        $('#prices_file').hide().attr("href", '');
                    }
                    /** Подтверждения перевозки через геомониторинг */
                    if (carriage.GEO_MONITORING_LINK != null) {
                        $('#geo_link').removeClass('status-info_confirmation_error');
                        $('#geo_file').show().attr("href", carriage.GEO_MONITORING_LINK);
                    } else {
                        $('#geo_link').addClass('status-info_confirmation_error');
                        $('#geo_file').hide().attr("href", '');
                    }
                    /** Подтверждение договорных отношений с водителем */
                    if (carriage.DRIVER_APPROVALS_LINK != null) {
                        $('#driver_link').removeClass('status-info_confirmation_error');
                        $('#driver_file').show().attr("href", carriage.DRIVER_APPROVALS_LINK);
                    } else {
                        $('#driver_link').addClass('status-info_confirmation_error');
                        $('#driver_file').hide().attr("href", '');
                    }
                    /** Подтверждения владения ТС тягач */
                    if (carriage.TRUCK_VALUE != null) {
                        if (carriage.TRUCK_CHECK !== false) {
                            $('#truck_check').addClass('detail-status_good').html(carriage.TRUCK_VALUE);
                        } else {
                            $('#truck_check').addClass('detail-status_error').html('Выполнено ' + carriage.TRUCK_VALUE);
                        }
                    } else {
                        $('#truck_check').html('').removeClass();
                    }
                    /** Договор аренды тягач */
                    if (carriage.RENT_AGREEMENT_LINK != null) {
                        $('#truck_rent').removeClass('status-info_confirmation_error');
                        $('#truck_link').show().attr("href", carriage.RENT_AGREEMENT_LINK);
                    } else {
                        $('#truck_rent').addClass('status-info_confirmation_error');
                        $('#truck_link').hide().attr("href", '');
                    }
                    /**  */
                    if (carriage.STS_LINK != null) {
                        $('#truck_sts').removeClass('status-info_confirmation_error');
                        $('#sts_link').show().attr("href", carriage.STS_LINK);
                    } else {
                        $('#truck_sts').addClass('status-info_confirmation_error');
                        $('#sts_link').hide().attr("href", '');
                    }
                    /**  */
                    /**  */
                    /**  */
                    /**
                     * TODO доделать вывод документов
                     */



                }

                if(response.status === 'error') {
                    $('#error').html(response.errors[0].message);
                }
            },
        });
    });
});