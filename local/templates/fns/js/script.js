$(document).ready(function () {

    /**
     * Стилизация инпута в фильтре
     */
    // $(window).keyup(function (e) {
    //     var target = $('.filter_checkbox input:focus');
    //     if (e.keyCode == 9 && $(target).length) {
    //         $(target).parent().addClass('focused');
    //     }
    // });
    //
    // $('.filter_checkbox input').focusout(function () {
    //     $(this).parent().removeClass('focused');
    // });

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
    $('.filter_top-organizations').click(function () {
        if ($('.filter_list-type-organizations').css('display') === 'none') {
            $('.filter_list-type-organizations').css('display', 'flex');
        } else {
            $('.filter_list-type-organizations').hide();
        }
    });

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
                    if (carriage.AUTO_PRICES !== '') {
                        $('#deviation-price').show();
                        $('#carriage_deviation-price').html(carriage.AUTO_PRICES + '%');
                    }
                    // /** Статус перевозки */
                    // if (carriage.STATUS_SHIPPING === 'passed') {
                    //     $('#status_shipping').html('Проверка пройдена').removeClass().addClass('bar-status-good');
                    // } else if(carriage.STATUS_SHIPPING === 'in_progress') {
                    //     $('#status_shipping').html('Оформление документов').removeClass().addClass('bar-status');
                    // } else {
                    //     $('#status_shipping').html('Проверка не пройдена').removeClass().addClass('bar-status-error');
                    // }

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
                        $('#checklist_forwarder').show();
                        let forwarder = carriage.FORWARDER_INN ? carriage.FORWARDER_INN : '';
                        $('#carriage_forwarder').html(
                            'Экспедитор <span class="carriage_name">' +
                            carriage.FORWARDER + '</span><span id="carriage_owner_inn">' +
                            forwarder + '</span>'
                        )
                    } else {
                        $('#checklist_forwarder').hide();
                        $('#carriage_forwarder').html('');
                    }

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

    /**
     * Скачиваем файл по чеклисту
     */
    $(document).on('click', '#cheklist_file', function () {
        BX.ajax({
            url: '/api/v1/vhs/file',
            method: 'POST',
            data: {
                fields: {
                    LINK: $(this).attr('data-id'),
                    NAME: $(this).text(),
                }
            },
            timeout: 2000,
            dataType: 'json',
            onsuccess: function (response) {
                if (response.status === 'success') {
                    window.open(response.data.URL, "_blank");
                }

                if (response.status === 'error') {
                    $('#error').html(response.errors[0].message);
                }
            },
        });
    });

    /**
     * Скачиваем архив файлов одного
     * элемента
     */
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

    /**
     * Скачиваем архив выбранных документов
     */
    $(document).on('click', '#file_filter_download', function () {
        let id = [];

        $('#vitrina_grid_table .main-grid-row-checked').each(function( index ) {
            id.push($( this ).attr('data-id'));
        });

        BX.ajax({
            url: '/api/v1/vhs/archive',
            method: 'POST',
            data: {
                fields: {
                    ID: id,
                }
            },
            timeout: 2000,
            dataType: 'json',
            onsuccess: function (response) {
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
        $('#donkey_rent_link').hide();
        $('#donkey_lias_link').hide();
        $('#donkey_cer_link').hide();
        $('#donkey_usage_link').hide();

        $('#trailer').hide();
        $('#trailer_ctc_link').hide();
        $('#trailer_rent_link').hide();
        $('#trailer_leas_link').hide();
        $('#trailer_cert_link').hide();
        $('#trailer_usage_link').hide();

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

        $('#contract_for').hide();
        $('#transport_link_for').hide();
        $('#contract_link_for').hide();
        $('#one_time_link_for').hide();

        $('#execution_documents_for').hide();
        $('#documents_link_for').hide();
        $('#epd_link_for').hide();
        $('#driver_link_for').hide();
        $('#exp_link_for').hide();
        $('#receipt_link_for').hide();

        $('#automatic_for').hide();
        $('#prices_link_for').hide();
        $('#geo_link_for').hide();

        $('#accounting_for').hide();
        $('#invoice_link_for').hide();
        $('#act_link_for').hide();
        $('#multi_link_for').hide();
        $('#reg_link_for').hide();
        $('#tax_link_for').hide();
        $('#upd_link_for').hide();

        $('#donkey_for').hide();
        $('#donkey_link_for').hide();
        $('#donkey_rent_link_for').hide();
        $('#donkey_leas_link_for').hide();
        $('#donkey_cert_link_for').hide();
        $('#donkey_usage_link_for').hide();

        $('#trailer_for').hide();
        $('#trailer_ctc_link_for').hide();
        $('#trailer_rent_link_for').hide();
        $('#trailer_leas_link_for').hide();
        $('#trailer_cert_link_for').hide();
        $('#trailer_usage_link_for').hide();

        $('#trailer_sec_for').hide();
        $('#trailer_sec_ctc_link_for').hide();
        $('#trailer_sec_rent_link_for').hide();
        $('#trailer_sec_lias_link_for').hide();
        $('#trailer_sec_cer_link_for').hide();
        $('#trailer_sec_usage_link_for').hide();

        $('#truck_for').hide();
        $('#truck_sts_link_for').hide();
        $('#truck_rent_for').hide();
        $('#truck_leas_link_for').hide();
        $('#truck_cert_link_for').hide();
        $('#truck_usage_link_for').hide();
    }

    /** Подписанные договоры */
    function contract(carriage) {
        /** Подписанные договоры */
        if (carriage.CONTRACT_CHECK != null) {
            // detail-status_error - класс ошибки
            $('#contract').show();
            if (carriage.CONTRACT_CHECK === '1') {
                $('#detail_status-transportation').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#detail_status-transportation').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#detail_status-transportation').html('').removeClass();
        }
        /** Договор перевозки */
        if(carriage.CONTRACT_TRANSPORT_STATUS === 'passed') {
            $('#transport_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_TRANSPORT_STATUS === 'in_progress') {
            $('#transport_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_TRANSPORT_STATUS === 'false') {
            $('#transport_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_TRANSPORTATION_LINK != null || carriage.CONTRACT_TRANSPORTATION_EDM_LINK != null) {
            let CONT_TRANSPORT_LINK = '';

            if (carriage.CONTRACT_TRANSPORTATION_LINK != null) {
                const DESCRIPTION_CONT_TRANSPORT_LINK = carriage.CONTRACT_TRANSPORTATION_LINK.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_TRANSPORTATION_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_TRANSPORT_LINK[index] !== '') {
                        CONT_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_TRANSPORT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_TRANSPORT_LINK[index] === '') {
                    } else {
                        CONT_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_TRANSPORTATION_EDM_LINK != null) {
                $.each(carriage.CONTRACT_TRANSPORTATION_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_transport_link").html(CONT_TRANSPORT_LINK);
            $('#transport_file').show();
        } else {
            $('#transport_file').hide();
            $("#list_file_transport_link").html('');
        }
        /** Договор транспортной */
        if(carriage.CONTRACT_EXP_STATUS === 'passed') {
            $('#contract_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_EXP_STATUS === 'in_progress') {
            $('#contract_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_EXP_STATUS === 'false') {
            $('#contract_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_EXPEDITION_LINK != null || carriage.CONTRACT_EXPEDITION_EDM_LINK != null) {
            let CONT_EXP_LINK = '';

            if (carriage.CONTRACT_EXPEDITION_LINK != null) {
                const DESCRIPTION_CONT_EXP_LINK = carriage.CONTRACT_EXPEDITION_LINK.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_EXPEDITION_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_EXP_LINK[index] !== '') {
                        CONT_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_EXP_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_EXP_LINK[index] === '') {
                    } else {
                        CONT_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_EXPEDITION_EDM_LINK != null) {
                $.each(carriage.CONTRACT_EXPEDITION_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_contract_link").html(CONT_EXP_LINK);
            $('#contract_file').show();
        } else {
            $('#contract_file').hide();
            $("#list_file_contract_link").html();
        }
        /** Заказ (разовая договор-заявка) */
        if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS === 'passed') {
            $('#one_time_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS === 'in_progress') {
            $('#one_time_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS === 'false') {
            $('#one_time_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_ORDER_ONE_TIME_LINK != null || carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK != null) {
            let CONT_ORDER_ONE_TIME = '';

            if (carriage.CONTRACT_ORDER_ONE_TIME_LINK != null) {
                const DESCRIPTION_CONT_ORDER_ONE_TIME = carriage.CONTRACT_ORDER_ONE_TIME_LINK.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_ORDER_ONE_TIME_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_ORDER_ONE_TIME[index] !== '') {
                        CONT_ORDER_ONE_TIME += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_ORDER_ONE_TIME[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_ORDER_ONE_TIME[index] === '') {
                    } else {
                        CONT_ORDER_ONE_TIME += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK != null) {
                $.each(carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_ORDER_ONE_TIME += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_one_time_link").html(CONT_ORDER_ONE_TIME);
            $('#one_time_file').show();
        } else {
            $('#one_time_file').hide();
            $("#list_file_one_time_link").html();
        }

        /** Подписанные договоры экспедитора */
        if (carriage.CONTRACT_FOR_CHECK != null) {
            $('#contract_for').show();
            if (carriage.CONTRACT_FOR_CHECK === '1') {
                $('#detail_status-transportation_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#detail_status-transportation_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#detail_status-transportation_for').html('').removeClass();
        }

        /** Договор перевозки экспедитора */
        if(carriage.CONTRACT_TRANSPORT_STATUS_FOR === 'passed') {
            $('#transport_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_TRANSPORT_STATUS_FOR === 'in_progress') {
            $('#transport_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_TRANSPORT_STATUS_FOR === 'false') {
            $('#transport_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_TRANSPORT_LINK_FOR != null || carriage.CONTRACT_TRANSPORT_EDM_LINK_FOR != null) {
            let CONT_TRANSPORT_LINK_FOR = '';

            if (carriage.CONTRACT_TRANSPORT_LINK_FOR != null) {
                const DESCRIPTION_CONT_TRANSPORT_LINK_FOR = carriage.CONTRACT_TRANSPORT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_TRANSPORT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_TRANSPORT_LINK_FOR[index] !== '') {
                        CONT_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_TRANSPORT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_TRANSPORT_LINK_FOR[index] === '') {
                    } else {
                        CONT_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_TRANSPORT_EDM_LINK_FOR != null) {
                $.each(carriage.CONTRACT_TRANSPORT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_transport_link_for").html(CONT_TRANSPORT_LINK_FOR);
            $('#transport_file_for').show();
        } else {
            $('#transport_file_for').hide();
            $("#list_file_transport_link_for").html('');
        }
        /** Договор транспортной экспедиции */
        if(carriage.CONTRACT_EXP_STATUS_FOR === 'passed') {
            $('#contract_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_EXP_STATUS_FOR === 'in_progress') {
            $('#contract_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_EXP_STATUS_FOR === 'false') {
            $('#contract_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_EXP_LINK_FOR != null || carriage.CONTRACT_EXP_EDM_LINK_FOR != null) {
            let CONT_EXP_LINK_FOR = '';

            if (carriage.CONTRACT_EXP_LINK_FOR != null) {
                const DESCRIPTION_CONT_EXP_LINK_FOR = carriage.CONTRACT_EXP_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_EXP_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_EXP_LINK_FOR[index] !== '') {
                        CONT_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_EXP_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_EXP_LINK_FOR[index] === '') {
                    } else {
                        CONT_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_EXP_EDM_LINK_FOR != null) {
                $.each(carriage.CONTRACT_EXP_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_contract_link_for").html(CONT_EXP_LINK_FOR);
            $('#contract_file_for').show();
        } else {
            $('#contract_file_for').hide();
            $("#list_file_contract_link_for").html();
        }
        /** Заказ (разовая договор-заявка) экспедиора */
        if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS_FOR === 'passed') {
            $('#one_time_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS_FOR === 'in_progress') {
            $('#one_time_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.CONTRACT_ORDER_ONE_TIME_STATUS_FOR === 'false') {
            $('#one_time_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.CONTRACT_ORDER_ONE_TIME_LINK_FOR != null || carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK_FOR != null) {
            let CONT_ORDER_ONE_TIME_FOR = '';

            if (carriage.CONTRACT_ORDER_ONE_TIME_LINK_FOR != null) {
                const DESCRIPTION_CONT_ORDER_ONE_TIME_FOR = carriage.CONTRACT_ORDER_ONE_TIME_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.CONTRACT_ORDER_ONE_TIME_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_CONT_ORDER_ONE_TIME_FOR[index] !== '') {
                        CONT_ORDER_ONE_TIME_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_CONT_ORDER_ONE_TIME_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_CONT_ORDER_ONE_TIME_FOR[index] === '') {
                    } else {
                        CONT_ORDER_ONE_TIME_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK_FOR != null) {
                $.each(carriage.CONTRACT_ORDER_ONE_TIME_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        CONT_ORDER_ONE_TIME_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_one_time_link_for").html(CONT_ORDER_ONE_TIME_FOR);
            $('#one_time_file_for').show();
        } else {
            $('#one_time_file_for').hide();
            $("#list_file_one_time_link_for").html();
        }
    }

    /** Оформление перевозки */
    function executionDocuments(carriage) {
        /** Оформление перевозки */
        if (carriage.DOCUMENTS_CHECK != null) {
            $('#execution_documents').show();
            if (carriage.DOCUMENTS_CHECK === '1') {
                $('#documents_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#documents_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#documents_check').html('').removeClass();
        }
        /** Заявка на перевозку */
        if(carriage.DOCUMENTS_TRANSPORT_STATUS === 'passed') {
            $('#documents_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_TRANSPORT_STATUS === 'in_progress') {
            $('#documents_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_TRANSPORT_STATUS === 'false') {
            $('#documents_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK != null ||
            carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK != null
        ) {
            let DOC_APP_TRANSPORT_LINK = '';

            if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK != null) {
                const DESCRIPTION_DOC_APP_TRANSPORT_LINK = carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] !== '') {
                        DOC_APP_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_APP_TRANSPORT_LINK[index] === '') {
                    } else {
                        DOC_APP_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file"> Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK != null) {
                $.each(carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_APP_TRANSPORT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_documents_link").html(DOC_APP_TRANSPORT_LINK);
            $('#documents_file').show();
        } else {
            $('#documents_file').hide();
            $("#list_file_one_time_link").html();
        }
        /** Подписанная ЭТрН */
        if(carriage.DOCUMENTS_EPD_STATUS === 'passed') {
            $('#epd_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EPD_STATUS === 'in_progress') {
            $('#epd_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EPD_STATUS === 'false') {
            $('#epd_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EPD_LINK != null || carriage.DOCUMENTS_EPD_EDM_LINK != null) {
            let DOC_EPD_LINK = '';

            if (carriage.DOCUMENTS_EPD_LINK != null) {
                const DESCRIPTION_DOC_EPD_LINK = carriage.DOCUMENTS_EPD_LINK.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EPD_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EPD_LINK[index] !== '') {
                        DOC_EPD_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EPD_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EPD_LINK[index] === '') {
                    } else {
                        DOC_EPD_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EPD_EDM_LINK != null) {
                $.each(carriage.DOCUMENTS_EPD_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EPD_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_epd_link").html(DOC_EPD_LINK);
            $('#epd_file').show();
        } else {
            $('#epd_file').hide();
            $("#list_file_epd_link").html();
        }
        /** Подтверждения договорных отношений с водителем */
        if(carriage.DOCUMENTS_DRIVER_STATUS === 'passed') {
            $('#driver_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_DRIVER_STATUS === 'in_progress') {
            $('#driver_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_DRIVER_STATUS === 'false') {
            $('#driver_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_DRIVER_APPROVALS_LINK != null || carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK != null) {
            let DOC_DRIVER_APP_LINK = '';

            if (carriage.DOCUMENTS_DRIVER_APPROVALS_LINK != null) {
                const DESCRIPTION_DOC_DRIVER_APP_LINK = carriage.DOCUMENTS_DRIVER_APPROVALS_LINK.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_DRIVER_APPROVALS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_DRIVER_APP_LINK[index] !== '') {
                        DOC_DRIVER_APP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_DRIVER_APP_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_DRIVER_APP_LINK[index] === '') {
                    } else {
                        DOC_DRIVER_APP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK != null) {
                $.each(carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_DRIVER_APP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_driver_link").html(DOC_DRIVER_APP_LINK);
            $('#driver_file').show();
        } else {
            $('#driver_file').hide();
            $("#list_file_driver_link").html('');
        }
        /** Поручение экспедитору */
        if(carriage.DOCUMENTS_EXPEDITOR_STATUS === 'passed') {
            $('#exp_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EXPEDITOR_STATUS === 'in_progress') {
            $('#exp_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EXPEDITOR_STATUS === 'false') {
            $('#exp_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EXPEDITOR_LINK != null || carriage.DOCUMENTS_EXPEDITOR_EDM_LINK != null) {
            let DOC_EXP_LINK = '';

            if (carriage.DOCUMENTS_EXPEDITOR_LINK != null) {
                const DESCRIPTION_DOC_EXP_LINK = carriage.DOCUMENTS_EXPEDITOR_LINK.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EXPEDITOR_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EXP_LINK[index] !== '') {
                        DOC_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EXP_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EXP_LINK[index] === '') {
                    } else {
                        DOC_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EXPEDITOR_EDM_LINK != null) {
                $.each(carriage.DOCUMENTS_EXPEDITOR_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EXP_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_exp_link").html(DOC_EXP_LINK);
            $('#exp_file').show();
        } else {
            $('#exp_file').hide();
            $("#list_file_exp_link").html('');
        }
        /** Экспедиторская расписка */
        if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS === 'passed') {
            $('#receipt_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS === 'in_progress') {
            $('#receipt_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS === 'false') {
            $('#receipt_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK != null ||
            carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK != null
        ) {
            let DOC_EXP_RECEIPT_LINK = '';

            if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK != null) {
                const DESCRIPTION_DOC_EXP_RECEIPT_LINK = carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] !== '') {
                        DOC_EXP_RECEIPT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EXP_RECEIPT_LINK[index] === '') {
                    } else {
                        DOC_EXP_RECEIPT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK != null) {
                $.each(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EXP_RECEIPT_LINK += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_receipt_link").html(DOC_EXP_RECEIPT_LINK);
            $('#receipt_file').show();
        } else {
            $('#receipt_file').hide();
            $("#list_file_receipt_link").html();
        }

        /** Оформление перевозки экспедитор*/
        if (carriage.DOCUMENTS_FOR_CHECK != null) {
            $('#execution_documents_for').show();
            if (carriage.DOCUMENTS_FOR_CHECK === '1') {
                $('#documents_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#documents_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#documents_check_for').html('').removeClass();
        }
        /** Заявка на перевозку */
        if(carriage.DOCUMENTS_TRANSPORT_STATUS_FOR === 'passed') {
            $('#documents_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_TRANSPORT_STATUS_FOR === 'in_progress') {
            $('#documents_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_TRANSPORT_STATUS_FOR === 'false') {
            $('#documents_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR != null ||
            carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK_FOR != null
        ) {
            let DOC_APP_TRANSPORT_LINK_FOR = '';

            if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR != null) {
                const DESCRIPTION_DOC_APP_TRANSPORT_LINK_FOR = carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_APP_TRANSPORT_LINK_FOR[index] !== '') {
                        DOC_APP_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_APP_TRANSPORT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_APP_TRANSPORT_LINK_FOR[index] === '') {
                    } else {
                        DOC_APP_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file"> Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK_FOR != null) {
                $.each(carriage.DOCUMENTS_APPLICATION_TRANSPORTATION_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_APP_TRANSPORT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_documents_link_for").html(DOC_APP_TRANSPORT_LINK_FOR);
            $('#documents_file_for').show();
        } else {
            $('#documents_file_for').hide();
            $("#list_file_one_time_link_for").html();
        }
        /** Подписанная ЭТрН */
        if(carriage.DOCUMENTS_EPD_STATUS_FOR === 'passed') {
            $('#epd_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EPD_STATUS_FOR === 'in_progress') {
            $('#epd_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EPD_STATUS_FOR === 'false') {
            $('#epd_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EPD_LINK_FOR != null || carriage.DOCUMENTS_EPD_EDM_LINK_FOR != null) {
            let DOC_EPD_LINK_FOR = '';

            if (carriage.DOCUMENTS_EPD_LINK_FOR != null) {
                const DESCRIPTION_DOC_EPD_LINK_FOR = carriage.DOCUMENTS_EPD_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EPD_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EPD_LINK_FOR[index] !== '') {
                        DOC_EPD_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EPD_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EPD_LINK_FOR[index] === '') {
                    } else {
                        DOC_EPD_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EPD_EDM_LINK_FOR != null) {
                $.each(carriage.DOCUMENTS_EPD_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EPD_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_epd_link_for").html(DOC_EPD_LINK_FOR);
            $('#epd_file_for').show();
        } else {
            $('#epd_file_for').hide();
            $("#list_file_epd_link_for").html();
        }
        /** Подтверждение договорных отношений с водителем */
        if(carriage.DOCUMENTS_DRIVER_STATUS_FOR === 'passed') {
            $('#driver_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_DRIVER_STATUS_FOR === 'in_progress') {
            $('#driver_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_DRIVER_STATUS_FOR === 'false') {
            $('#driver_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_DRIVER_APPROVALS_LINK_FOR != null ||
            carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK_FOR != null
        ) {
            let DOC_DRIVER_APP_LINK_FOR = '';

            if (carriage.DOCUMENTS_DRIVER_APPROVALS_LINK_FOR != null) {
                const DESCRIPTION_DOC_DRIVER_APP_LINK_FOR = carriage.DOCUMENTS_DRIVER_APPROVALS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_DRIVER_APPROVALS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_DRIVER_APP_LINK_FOR[index] !== '') {
                        DOC_DRIVER_APP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_DRIVER_APP_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_DRIVER_APP_LINK_FOR[index] === '') {
                    } else {
                        DOC_DRIVER_APP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK_FOR != null) {
                $.each(carriage.DOCUMENTS_DRIVER_APPROVALS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_DRIVER_APP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_driver_link_for").html(DOC_DRIVER_APP_LINK_FOR);
            $('#driver_file_for').show();
        } else {
            $('#driver_file_for').hide();
            $("#list_file_driver_link_for").html('');
        }
        /** Поручение экспедитору */
        if(carriage.DOCUMENTS_EXPEDITOR_STATUS_FOR === 'passed') {
            $('#exp_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EXPEDITOR_STATUS_FOR === 'in_progress') {
            $('#exp_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EXPEDITOR_STATUS_FOR === 'false') {
            $('#exp_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EXPEDITOR_LINK_FOR != null || carriage.DOCUMENTS_EXPEDITOR_EDM_LINK_FOR != null) {
            let DOC_EXP_LINK_FOR = '';

            if (carriage.DOCUMENTS_EXPEDITOR_LINK_FOR != null) {
                const DESCRIPTION_DOC_EXP_LINK_FOR = carriage.DOCUMENTS_EXPEDITOR_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EXPEDITOR_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EXP_LINK_FOR[index] !== '') {
                        DOC_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EXP_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EXP_LINK_FOR[index] === '') {
                    } else {
                        DOC_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EXPEDITOR_EDM_LINK_FOR != null) {
                $.each(carriage.DOCUMENTS_EXPEDITOR_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EXP_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_exp_link_for").html(DOC_EXP_LINK_FOR);
            $('#exp_file_for').show();
        } else {
            $('#exp_file_for').hide();
            $("#list_file_exp_link_for").html('');
        }
        /** Экспедиторская расписка */
        if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_FOR === 'passed') {
            $('#receipt_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_FOR === 'in_progress') {
            $('#receipt_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_STATUS_FOR === 'false') {
            $('#receipt_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR != null ||
            carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK_FOR != null
        ) {
            let DOC_EXP_RECEIPT_LINK_FOR = '';

            if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR != null) {
                const DESCRIPTION_DOC_EXP_RECEIPT_LINK_FOR = carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DOC_EXP_RECEIPT_LINK_FOR[index] !== '') {
                        DOC_EXP_RECEIPT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">' + DESCRIPTION_DOC_EXP_RECEIPT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DOC_EXP_RECEIPT_LINK_FOR[index] === '') {
                    } else {
                        DOC_EXP_RECEIPT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK_FOR != null) {
                $.each(carriage.DOCUMENTS_EXPEDITOR_RECEIPT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DOC_EXP_RECEIPT_LINK_FOR += '<li><span data-id="' + value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_receipt_link_for").html(DOC_EXP_RECEIPT_LINK_FOR);
            $('#receipt_file_for').show();
        } else {
            $('#receipt_file_for').hide();
            $("#list_file_receipt_link_for").html();
        }
    }

    /** Автоматические проверки */
    function automaticChecks(carriage) {
        /** Автоматические проверки */
        if (carriage.AUTOMATIC_CHECKS != null) {
            $('#automatic').show();
            if (carriage.AUTOMATIC_CHECKS === '1') {
                $('#auto_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#auto_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#auto_check').html('').removeClass();
        }
        /** Стоимость перевозки соответствует рыночным ценам */
        if(carriage.AUTOMATIC_PRICES_STATUS === 'passed') {
            $('#prices_link .status-info_confirmation_title').html('Стоимость перевозки соответствует рыночной цены');
            $('#prices_link').show().removeClass().addClass('status-info_confirmation');
            $('#prices_file').show();
        } else if(carriage.AUTOMATIC_PRICES_STATUS === 'in_progress') {
            $('#prices_link .status-info_confirmation_title').html('Стоимость перевозки в процессе получения');
            $('#prices_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
            $('#prices_file').hide();
        } else if(carriage.AUTOMATIC_PRICES_STATUS === 'failed') {
            const price = carriage.AUTO_PRICES.split("/");
            if (price[0] !== '') {
                $('#prices_link .status-info_confirmation_title').html('Стоимость перевозки ниже '+ price[0] +'% от рыночной цены');
            } else {
                $('#prices_link .status-info_confirmation_title').html('Стоимость перевозки не соответствует рыночным ценам');
            }
            $('#prices_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
            $('#prices_file').hide();
        }

        /** Подтверждения перевозки через геомониторинг */
        if(carriage.AUTOMATIC_GEO_MONITORING_STATUS === 'passed') {
            $('#geo_link').show().removeClass().addClass('status-info_confirmation');
            $('#geo_file').show();
        } else if(carriage.AUTOMATIC_GEO_MONITORING_STATUS === 'in_progress') {
            $('#geo_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
            $('#geo_file').hide();
        } else if(carriage.AUTOMATIC_GEO_MONITORING_STATUS === 'failed') {
            $('#geo_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
            $('#geo_file').hide();
        }

        /** Автоматические проверки экспедитор*/
        if (carriage.AUTOMATIC_FOR_CHECKS != null) {
            $('#automatic_for').show();
            if (carriage.AUTOMATIC_FOR_CHECKS === '1') {
                $('#auto_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#auto_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#auto_check_for').html('').removeClass();
        }
        /** Стоимость перевозки соответствует рыночным ценам */
        if(carriage.AUTOMATIC_PRICES_STATUS_FOR === 'passed') {
            $('#prices_link_for .status-info_confirmation_title').html('Стоимость перевозки соответствует рыночной цены');
            $('#prices_link_for').show().removeClass().addClass('status-info_confirmation');
            $('#prices_file_for').show();
        } else if(carriage.AUTOMATIC_PRICES_STATUS_FOR === 'in_progress') {
            $('#prices_link_for .status-info_confirmation_title').html('Стоимость перевозки в процессе получения');
            $('#prices_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
            $('#prices_file_for').hide();
        } else if(carriage.AUTOMATIC_PRICES_STATUS_FOR === 'failed') {
            const price = carriage.AUTO_PRICES.split("/");
            const priceFor = price[1] ? price[1] : price[0];

            if (priceFor) {
                $('#prices_link_for .status-info_confirmation_title').html('Стоимость перевозки ниже '+ priceFor +'% от рыночной цены');
            } else {
                $('#prices_link_for .status-info_confirmation_title').html('Стоимость перевозки не соответствует рыночным ценам');
            }

            $('#prices_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
            $('#prices_file_for').hide();
        }

        /** Подтверждения перевозки через геомониторинг */
        if(carriage.AUTOMATIC_GEO_MONITORING_STATUS_FOR === 'passed') {
            $('#geo_link_for').show().removeClass().addClass('status-info_confirmation');
            $('#geo_file_for').show();
        } else if(carriage.AUTOMATIC_GEO_MONITORING_STATUS_FOR === 'in_progress') {
            $('#geo_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
            $('#geo_file_for').hide();
        } else if(carriage.AUTOMATIC_GEO_MONITORING_STATUS_FOR === 'failed') {
            $('#geo_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
            $('#geo_file_for').hide();
        }
    }

    /** Бухгалтерские документы */
    function accounting(carriage) {
        /** Бухгалтерские документы */
        if (carriage.ACCOUNTING_CHECKS != null) {
            $('#accounting').show();
            if (carriage.ACCOUNTING_CHECKS === '1') {
                $('#accounting_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#accounting_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#accounting_check').html('').removeClass();
        }
        /** Счёт */
        if (carriage.ACCOUNTING_INVOICE_STATUS === 'passed' && carriage.ACCOUNTING_CHECKS === '1') {
            $('#invoice_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#invoice_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_INVOICE_LINK != null || carriage.ACCOUNTING_INVOICE_EDM_LINK != null) {
            let ACC_INVOICE_LINK = '';

            if (carriage.ACCOUNTING_INVOICE_LINK != null) {
                const DESCRIPTION_ACC_INVOICE_LINK = carriage.ACCOUNTING_INVOICE_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_INVOICE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_INVOICE_LINK[index] !== '') {
                        ACC_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_INVOICE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_INVOICE_LINK[index] === '') {
                    } else {
                        ACC_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_INVOICE_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_INVOICE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_invoice_link").html(ACC_INVOICE_LINK);
            $('#invoice_file').show();
        } else {
            $('#invoice_file').hide();
            $("#list_file_invoice_link").html();
        }
        /** Акт о приемке выполненных работ по услуге */
        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_STATUS === 'passed'  && carriage.ACCOUNTING_CHECKS === '1') {
            $('#act_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#act_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK != null || carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK != null) {
            let ACC_ACT_ACC_LINK = '';

            if (carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK != null) {
                const DESCRIPTION_ACC_ACT_ACC_LINK = carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_ACT_ACC_LINK[index] !== '') {
                        ACC_ACT_ACC_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_ACT_ACC_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_ACT_ACC_LINK[index] === '') {
                    } else {
                        ACC_ACT_ACC_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_ACT_ACC_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_act_link").html(ACC_ACT_ACC_LINK);
            $('#act_file').show();
        } else {
            $('#act_file').hide();
            $("#list_file_act_link").html();
        }
        /** Акт о приемке выполненных работ, включающий несколько перевозок */
        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS === 'passed' && carriage.ACCOUNTING_CHECKS === '1') {
            $('#multi_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#multi_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK != null ||
            carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK != null
        ) {
            let ACC_ACT_MULTI_TRANSPORT_LINK = '';

            if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK != null) {
                const DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK = carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK[index] !== '') {
                        ACC_ACT_MULTI_TRANSPORT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK[index] === '') {
                    } else {
                        ACC_ACT_MULTI_TRANSPORT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_ACT_MULTI_TRANSPORT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_multi_link").html(ACC_ACT_MULTI_TRANSPORT_LINK);
            $('#multi_file').show();
        } else {
            $('#multi_file').hide();
            $("#list_file_multi_link").html();
        }
        /** Реестр на перевозки */
        if (carriage.ACCOUNTING_TRANSPORT_REGISTRY_STATUS === 'passed' && carriage.ACCOUNTING_CHECKS === '1') {
            $('#reg_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#reg_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK != null ||
            carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK != null
        ) {
            let ACC_TRANSPORT_REG_LINK = '';

            if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK != null) {
                const DESCRIPTION_ACC_TRANSPORT_REG_LINK = carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] !== '') {
                        ACC_TRANSPORT_REG_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_TRANSPORT_REG_LINK[index] === '') {
                    } else {
                        ACC_TRANSPORT_REG_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_TRANSPORT_REG_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_reg_link").html(ACC_TRANSPORT_REG_LINK);
            $('#reg_file').show();
        } else {
            $('#reg_file').hide();
            $("#list_file_reg_link").html();
        }
        /** Счёт-фактура */
        if (carriage.ACCOUNTING_TAX_INVOICE_STATUS === 'passed' && carriage.ACCOUNTING_CHECKS === '1') {
            $('#tax_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#tax_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_TAX_INVOICE_LINK != null || carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK != null) {
            let ACC_TAX_INVOICE_LINK = '';

            if (carriage.ACCOUNTING_TAX_INVOICE_LINK != null) {
                const DESCRIPTION_ACC_TAX_INVOICE_LINK = carriage.ACCOUNTING_TAX_INVOICE_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_TAX_INVOICE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_TAX_INVOICE_LINK[index] !== '') {
                        ACC_TAX_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_TAX_INVOICE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_TAX_INVOICE_LINK[index] === '') {
                    } else {
                        ACC_TAX_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_TAX_INVOICE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_tax_link").html(ACC_TAX_INVOICE_LINK);
            $('#tax_file').show();
        } else {
            $('#tax_file').hide();
            $("#list_file_tax_link").html();
        }

        /** УПД */
        if (carriage.ACCOUNTING_UPD_STATUS === 'passed' && carriage.ACCOUNTING_CHECKS === '1') {
            $('#upd_link').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_CHECKS === '0') {
            $('#upd_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_UPD_LINK != null || carriage.ACCOUNTING_UPD_EDM_LINK != null) {
            let ACC_UPD_LINK = '';

            if (carriage.ACCOUNTING_UPD_LINK != null) {
                const DESCRIPTION_ACC_UPD_LINK = carriage.ACCOUNTING_UPD_LINK.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_UPD_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_UPD_LINK[index] !== '') {
                        ACC_UPD_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_UPD_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_UPD_LINK[index] === '') {
                    } else {
                        ACC_UPD_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_UPD_EDM_LINK != null) {
                $.each(carriage.ACCOUNTING_UPD_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_UPD_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_upd_link").html(ACC_UPD_LINK);
            $('#upd_file').show();
        } else {
            $('#upd_file').hide();
            $("#list_file_upd_link").html();
        }

        /** Бухгалтерские документы экспедитора */
        if (carriage.ACCOUNTING_FOR_CHECKS != null) {
            $('#accounting_for').show();
            if (carriage.ACCOUNTING_FOR_CHECKS === '1') {
                $('#accounting_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#accounting_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#accounting_check_for').html('').removeClass();
        }
        /** Счёт */
        if (carriage.ACCOUNTING_INVOICE_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#invoice_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#invoice_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_INVOICE_LINK_FOR != null || carriage.ACCOUNTING_INVOICE_EDM_LINK_FOR != null) {
            let ACC_INVOICE_LINK_FOR = '';

            if (carriage.ACCOUNTING_INVOICE_LINK_FOR != null) {
                const DESCRIPTION_ACC_INVOICE_LINK_FOR = carriage.ACCOUNTING_INVOICE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_INVOICE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_INVOICE_LINK_FOR[index] !== '') {
                        ACC_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_INVOICE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_INVOICE_LINK_FOR[index] === '') {
                    } else {
                        ACC_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_INVOICE_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_INVOICE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_invoice_link_for").html(ACC_INVOICE_LINK_FOR);
            $('#invoice_file_for').show();
        } else {
            $('#invoice_file_for').hide();
            $("#list_file_invoice_link_for").html();
        }
        /** Акт о приемке выполненных работ по услуге */
        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#act_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#act_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR != null || carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK_FOR != null) {
            let ACC_ACT_ACC_LINK_FOR = '';

            if (carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR != null) {
                const DESCRIPTION_ACC_ACT_ACC_LINK_FOR = carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_ACT_ACCEPTANCE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_ACT_ACC_LINK_FOR[index] !== '') {
                        ACC_ACT_ACC_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_ACT_ACC_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_ACT_ACC_LINK_FOR[index] === '') {
                    } else {
                        ACC_ACT_ACC_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_ACT_ACCEPTANCE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_ACT_ACC_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_act_link_for").html(ACC_ACT_ACC_LINK_FOR);
            $('#act_file_for').show();
        } else {
            $('#act_file_for').hide();
            $("#list_file_act_link_for").html();
        }

        /** Акт о приемке выполненных работ, включающий несколько перевозок */
        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORT_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#multi_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#multi_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR != null ||
            carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK_FOR != null
        ) {
            let ACC_ACT_MULTI_TRANSPORT_LINK_FOR = '';

            if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR != null) {
                const DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK_FOR = carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK_FOR[index] !== '') {
                        ACC_ACT_MULTI_TRANSPORT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_ACT_MULTI_TRANSPORT_LINK_FOR[index] === '') {
                    } else {
                        ACC_ACT_MULTI_TRANSPORT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_ACT_MULTIPLE_TRANSPORTATIONS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_ACT_MULTI_TRANSPORT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_multi_link_for").html(ACC_ACT_MULTI_TRANSPORT_LINK_FOR);
            $('#multi_file_for').show();
        } else {
            $('#multi_file_for').hide();
            $("#list_file_multi_link_for").html();
        }

        /** Реестр на перевозки */
        if (carriage.ACCOUNTING_TRANSPORT_REGISTRY_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#reg_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#reg_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR != null  ||
            carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK_FOR != null
        ) {
            let ACC_TRANSPORT_REG_LINK_FOR = '';

            if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR != null) {
                const DESCRIPTION_ACC_TRANSPORT_REG_LINK_FOR = carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_TRANSPORT_REG_LINK_FOR[index] !== '') {
                        ACC_TRANSPORT_REG_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_TRANSPORT_REG_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_TRANSPORT_REG_LINK_FOR[index] === '') {
                    } else {
                        ACC_TRANSPORT_REG_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_TRANSPORTATION_REGISTRY_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_TRANSPORT_REG_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_reg_link_for").html(ACC_TRANSPORT_REG_LINK_FOR);
            $('#reg_file_for').show();
        } else {
            $('#reg_file_for').hide();
            $("#list_file_reg_link_for").html();
        }

        /** Счёт-фактура */
        if (carriage.ACCOUNTING_TAX_INVOICE_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#tax_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#tax_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }

        if (carriage.ACCOUNTING_TAX_INVOICE_LINK_FOR != null || carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK_FOR != null) {
            let ACC_TAX_INVOICE_LINK_FOR = '';

            if (carriage.ACCOUNTING_TAX_INVOICE_LINK_FOR != null) {
                const DESCRIPTION_ACC_TAX_INVOICE_LINK_FOR = carriage.ACCOUNTING_TAX_INVOICE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_TAX_INVOICE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_TAX_INVOICE_LINK_FOR[index] !== '') {
                        ACC_TAX_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_TAX_INVOICE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_TAX_INVOICE_LINK_FOR[index] === '') {
                    } else {
                        ACC_TAX_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_TAX_INVOICE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_TAX_INVOICE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_tax_link_for").html(ACC_TAX_INVOICE_LINK_FOR);
            $('#tax_file_for').show();
        } else {
            $('#tax_file_for').hide();
            $("#list_file_tax_link_for").html();
        }

        /** УПД */
        if (carriage.ACCOUNTING_UPD_STATUS_FOR === 'passed' && carriage.ACCOUNTING_FOR_CHECKS === '1') {
            $('#upd_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if (carriage.ACCOUNTING_FOR_CHECKS === '0') {
            $('#upd_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        }
        if (carriage.ACCOUNTING_UPD_LINK_FOR != null || carriage.ACCOUNTING_UPD_EDM_LINK_FOR != null) {
            let ACC_UPD_LINK_FOR = '';

            if (carriage.ACCOUNTING_UPD_LINK_FOR != null) {
                const DESCRIPTION_ACC_UPD_LINK_FOR = carriage.ACCOUNTING_UPD_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.ACCOUNTING_UPD_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_ACC_UPD_LINK_FOR[index] !== '') {
                        ACC_UPD_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_ACC_UPD_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_ACC_UPD_LINK_FOR[index] === '') {
                    } else {
                        ACC_UPD_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.ACCOUNTING_UPD_EDM_LINK_FOR != null) {
                $.each(carriage.ACCOUNTING_UPD_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        ACC_UPD_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_upd_link_for").html(ACC_UPD_LINK_FOR);
            $('#upd_file_for').show();
        } else {
            $('#upd_file_for').hide();
            $("#list_file_upd_link_for").html();
        }
    }

    /** Блок тягача */
    function donkey(carriage) {
        /** Подтверждения владения тягач */
        if (carriage.DONKEY_CHECKS != null) {
            $('#donkey').show();
            if (carriage.DONKEY_CHECKS === '1') {
                $('#donkey_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#donkey_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
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
        if(carriage.DONKEY_STS_STATUS === 'passed') {
            $('#donkey_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_STS_STATUS === 'in_progress') {
            $('#donkey_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_STS_STATUS === 'false') {
            $('#donkey_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_STS_LINK != null || carriage.DONKEY_STS_EDM_LINK != null) {
            let DONKEY_STS_LINK = '';

            if (carriage.DONKEY_STS_LINK != null) {
                const DESCRIPTION_DONKEY_STS_LINK = carriage.DONKEY_STS_LINK.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_STS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_STS_LINK[index] !== '') {
                        DONKEY_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_STS_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_STS_LINK[index] === '') {
                    } else {
                        DONKEY_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_STS_EDM_LINK != null) {
                $.each(carriage.DONKEY_STS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_link").html(DONKEY_STS_LINK);
            $('#donkey_file').show();
        } else {
            $('#donkey_file').hide();
            $("#list_file_donkey_link").html();
        }

        /** Договор аренды тягач */
        if(carriage.DONKEY_RENT_AGREEMENT_STATUS === 'passed') {
            $('#donkey_rent_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_RENT_AGREEMENT_STATUS === 'in_progress') {
            $('#donkey_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_RENT_AGREEMENT_STATUS === 'false') {
            $('#donkey_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_RENT_AGREEMENT_LINK != null || carriage.DONKEY_RENT_AGREEMENT_EDM_LINK != null) {
            let DONKEY_RENT_AGR_LINK = '';

            if (carriage.DONKEY_RENT_AGREEMENT_LINK != null) {
                const DESCRIPTION_DONKEY_RENT_AGR_LINK = carriage.DONKEY_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_RENT_AGR_LINK[index] !== '') {
                        DONKEY_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_RENT_AGR_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_RENT_AGR_LINK[index] === '') {
                    } else {
                        DONKEY_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_RENT_AGREEMENT_EDM_LINK != null) {
                $.each(carriage.DONKEY_RENT_AGREEMENT_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_rent_link").html(DONKEY_RENT_AGR_LINK);
            $('#donkey_rent_file').show();
        } else {
            $('#donkey_rent_file').hide();
            $("#list_file_donkey_rent_link").html();
        }
        /** Договор с лизинговой компанией */
        if(carriage.DONKEY_LEASING_COMPANY_STATUS === 'passed') {
            $('#donkey_lias_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_LEASING_COMPANY_STATUS === 'in_progress') {
            $('#donkey_lias_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_LEASING_COMPANY_STATUS === 'false') {
            $('#donkey_lias_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK != null ||
            carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK != null
        ) {
            let DONKEY_SEC_LEASING_COMPANY_LINK = '';

            if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK != null) {
                const DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK = carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK[index] !== '') {
                        DONKEY_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK[index] === '') {
                    } else {
                        DONKEY_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK != null) {
                $.each(carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_lias_link").html(DONKEY_SEC_LEASING_COMPANY_LINK);
            $('#donkey_lias_file').show();
        } else {
            $('#donkey_lias_file').hide();
            $("#list_file_donkey_lias_link").html();
        }

        /** Свидетельство о браке */
        if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS === 'passed') {
            $('#donkey_cer_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS === 'in_progress') {
            $('#donkey_cer_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS === 'false') {
            $('#donkey_cer_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK != null ||
            carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK != null
        ) {
            let DONKEY_SEC_CERTIFICATE_LINK = '';

            if (carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK != null) {
                const DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK = carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK[index] !== '') {
                        DONKEY_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK[index] === '') {
                    } else {
                        DONKEY_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK != null) {
                $.each(carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_cer_link").html(DONKEY_SEC_CERTIFICATE_LINK);
            $('#donkey_cer_file').show();
        } else {
            $('#donkey_cer_file').hide();
            $("#list_file_donkey_cer_link").html();
        }
        /** Договор безвозмездного использования */
        if(carriage.DONKEY_FREE_USAGE_STATUS === 'passed') {
            $('#donkey_usage_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_FREE_USAGE_STATUS === 'in_progress') {
            $('#donkey_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_FREE_USAGE_STATUS === 'false') {
            $('#donkey_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_FREE_USAGE_LINK != null ||
            carriage.DONKEY_FREE_USAGE_EDM_LINK != null
        ) {
            let DONKEY_SEC_FREE_USAGE_LINK = '';

            if (carriage.DONKEY_FREE_USAGE_LINK != null) {
                const DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK = carriage.DONKEY_FREE_USAGE_LINK.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK[index] !== '') {
                        DONKEY_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK[index] === '') {
                    } else {
                        DONKEY_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_FREE_USAGE_EDM_LINK != null) {
                $.each(carriage.DONKEY_FREE_USAGE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_usage_link").html(DONKEY_SEC_FREE_USAGE_LINK);
            $('#donkey_usage_file').show();
        } else {
            $('#donkey_usage_file').hide();
            $("#list_file_donkey_usage_link").html();
        }

        /** Подтверждения владения тягач экспедитор*/
        if (carriage.DONKEY_FOR_CHECKS != null) {
            $('#donkey_for').show();
            if (carriage.DONKEY_FOR_CHECKS === '1') {
                $('#donkey_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#donkey_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#donkey_check_for').html('').removeClass();
        }
        /** Номерной знак тягач */
        if (carriage.DONKEY_LICENSE_PLATE_FOR != null) {
            $('#donkey_plate_for').html(carriage.DONKEY_LICENSE_PLATE_FOR);
        } else {
            $('#donkey_plate_for').html('');
        }
        /** СТС тягач */
        if(carriage.DONKEY_STS_STATUS_FOR === 'passed') {
            $('#donkey_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_STS_STATUS_FOR === 'in_progress') {
            $('#donkey_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_STS_STATUS_FOR === 'false') {
            $('#donkey_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_STS_LINK_FOR != null || carriage.DONKEY_STS_EDM_LINK_FOR != null) {
            let DONKEY_STS_LINK_FOR = '';

            if (carriage.DONKEY_STS_LINK_FOR != null) {
                const DESCRIPTION_DONKEY_STS_LINK_FOR = carriage.DONKEY_STS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_STS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_STS_LINK_FOR[index] !== '') {
                        DONKEY_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_STS_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_STS_LINK_FOR[index] === '') {
                    } else {
                        DONKEY_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_STS_EDM_LINK_FOR != null) {
                $.each(carriage.DONKEY_STS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_link_for").html(DONKEY_STS_LINK_FOR);
            $('#donkey_file_for').show();
        } else {
            $('#donkey_file_for').hide();
            $("#list_file_donkey_link_for").html();
        }

        /** Договор аренды тягач */
        if(carriage.DONKEY_RENT_STATUS_FOR === 'passed') {
            $('#donkey_rent_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_RENT_STATUS_FOR === 'in_progress') {
            $('#donkey_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_RENT_STATUS_FOR === 'false') {
            $('#donkey_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_RENT_AGREEMENT_LINK_FOR != null || carriage.DONKEY_RENT_AGREEMENT_EDM_LINK_FOR != null) {
            let DONKEY_RENT_AGR_LINK_FOR = '';

            if (carriage.DONKEY_RENT_AGREEMENT_LINK_FOR != null) {
                const DESCRIPTION_DONKEY_RENT_AGR_LINK_FOR = carriage.DONKEY_RENT_AGREEMENT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_RENT_AGREEMENT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_RENT_AGR_LINK_FOR[index] !== '') {
                        DONKEY_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_RENT_AGR_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_RENT_AGR_LINK_FOR[index] === '') {
                    } else {
                        DONKEY_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_RENT_AGREEMENT_EDM_LINK_FOR != null) {
                $.each(carriage.DONKEY_RENT_AGREEMENT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_rent_link_for").html(DONKEY_RENT_AGR_LINK_FOR);
            $('#donkey_rent_file_for').show();
        } else {
            $('#donkey_rent_file_for').hide();
            $("#list_file_donkey_rent_link_for").html();
        }

        /** Договор с лизинговой компанией */
        if(carriage.DONKEY_LEASING_COMPANY_STATUS_FOR === 'passed') {
            $('#donkey_leas_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_LEASING_COMPANY_STATUS_FOR === 'in_progress') {
            $('#donkey_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_LEASING_COMPANY_STATUS_FOR === 'false') {
            $('#donkey_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR != null ||
            carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null
        ) {
            let DONKEY_SEC_LEASING_COMPANY_LINK_FOR = '';

            if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR != null) {
                const DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK_FOR = carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_AGREEMENT_LEASING_COMPANY_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK_FOR[index] !== '') {
                        DONKEY_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_LEASING_COMPANY_LINK_FOR[index] === '') {
                    } else {
                        DONKEY_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null) {
                $.each(carriage.DONKEY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_leas_link_for").html(DONKEY_SEC_LEASING_COMPANY_LINK_FOR);
            $('#donkey_leas_file_for').show();
        } else {
            $('#donkey_leas_file_for').hide();
            $("#list_file_donkey_leas_link_for").html();
        }

        /** Свидетельство о браке */
        if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS_FOR === 'passed') {
            $('#donkey_cert_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS_FOR === 'in_progress') {
            $('#donkey_cert_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_MARRIAGE_CERTIFICATE_STATUS_FOR === 'false') {
            $('#donkey_cert_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR != null ||
            carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null
        ) {
            let DONKEY_SEC_CERTIFICATE_LINK_FOR = '';

            if (carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR != null) {
                const DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK_FOR = carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DONKEY_MARRIAGE_CERTIFICATE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK_FOR[index] !== '') {
                        DONKEY_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_CERTIFICATE_LINK_FOR[index] === '') {
                    } else {
                        DONKEY_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null) {
                $.each(carriage.DONKEY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_cert_link_for").html(DONKEY_SEC_CERTIFICATE_LINK_FOR);
            $('#donkey_cert_file_for').show();
        } else {
            $('#donkey_cert_file_for').hide();
            $("#list_file_donkey_cert_link_for").html();
        }
        /** Договор безвозмездного использования */
        if(carriage.DONKEY_FREE_USAGE_STATUS_FOR === 'passed') {
            $('#donkey_usage_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.DONKEY_FREE_USAGE_STATUS_FOR === 'in_progress') {
            $('#donkey_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.DONKEY_FREE_USAGE_STATUS_FOR === 'false') {
            $('#donkey_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.DONKEY_FREE_USAGE_LINK_FOR != null ||
            carriage.DONKEY_FREE_USAGE_EDM_LINK_FOR != null
        ) {
            let DONKEY_SEC_FREE_USAGE_LINK_FOR = '';

            if (carriage.DONKEY_FREE_USAGE_LINK_FOR != null) {
                const DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK_FOR = carriage.DONKEY_FREE_USAGE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK_FOR[index] !== '') {
                        DONKEY_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_DONKEY_SEC_FREE_USAGE_LINK_FOR[index] === '') {
                    } else {
                        DONKEY_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.DONKEY_FREE_USAGE_EDM_LINK_FOR != null) {
                $.each(carriage.DONKEY_FREE_USAGE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        DONKEY_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_donkey_usage_link_for").html(DONKEY_SEC_FREE_USAGE_LINK_FOR);
            $('#donkey_usage_file_for').show();
        } else {
            $('#donkey_usage_file_for').hide();
            $("#list_file_donkey_usage_link_for").html();
        }
    }

    /** Блок прицепа */
    function trailer(carriage) {
        /** Подтверждения владения прицеп */
        if (carriage.TRAILER_CHECKS != null) {
            $('#trailer').show();
            if (carriage.TRAILER_CHECKS === '1') {
                $('#trailer_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#trailer_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
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
        if(carriage.TRAILER_STS_STATUS === 'passed') {
            $('#trailer_ctc_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_STS_STATUS === 'in_progress') {
            $('#trailer_ctc_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_STS_STATUS === 'false') {
            $('#trailer_ctc_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_STS_LINK != null || carriage.TRAILER_STS_EDM_LINK != null) {
            let TRAILER_STS_LINK = '';

            if (carriage.TRAILER_STS_LINK != null) {
                const DESCRIPTION_TRAILER_STS_LINK = carriage.TRAILER_STS_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_STS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_STS_LINK[index] !== '') {
                        TRAILER_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_STS_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_STS_LINK[index] === '') {
                    } else {
                        TRAILER_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_STS_EDM_LINK != null) {
                $.each(carriage.TRAILER_STS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_ctc_link").html(TRAILER_STS_LINK);
            $('#trailer_ctc_file').show();
        } else {
            $('#trailer_ctc_file').hide();
            $("#list_file_trailer_ctc_link").html();
        }
        /** Договор аренды прицеп */
        if(carriage.TRAILER_RENT_AGREEMENT_STATUS === 'passed') {
            $('#trailer_rent_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_RENT_AGREEMENT_STATUS === 'in_progress') {
            $('#trailer_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_RENT_AGREEMENT_STATUS === 'false') {
            $('#trailer_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_RENT_AGREEMENT_LINK != null || carriage.TRAILER_RENT_AGREEMENT_EDM_LINK != null) {
            let TRAILER_RENT_AGR_LINK = '';

            if (carriage.TRAILER_RENT_AGREEMENT_LINK != null) {
                const DESCRIPTION_TRAILER_RENT_AGR_LINK = carriage.TRAILER_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_RENT_AGR_LINK[index] !== '') {
                        TRAILER_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_RENT_AGR_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_RENT_AGR_LINK[index] === '') {
                    } else {
                        TRAILER_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_RENT_AGREEMENT_EDM_LINK != null) {
                $.each(carriage.TRAILER_RENT_AGREEMENT_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_RENT_AGR_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_rent_link").html(TRAILER_RENT_AGR_LINK);
            $('#trailer_rent_file').show();
        } else {
            $('#trailer_rent_file').hide();
            $("#list_file_trailer_rent_link").html();
        }
        /** Договор с лизинговой компанией */
        if(carriage.TRAILER_LEASING_COMPANY_STATUS === 'passed') {
            $('#trailer_leas_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_LEASING_COMPANY_STATUS === 'in_progress') {
            $('#trailer_leas_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_LEASING_COMPANY_STATUS === 'false') {
            $('#trailer_leas_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_AGR_LEASING_COMPANY_LINK != null ||
            carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK != null
        ) {
            let TRAILER_SEC_LEASING_COMPANY_LINK = '';

            if (carriage.TRAILER_AGR_LEASING_COMPANY_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK = carriage.TRAILER_AGR_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_AGR_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK != null) {
                $.each(carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_leas_link").html(TRAILER_SEC_LEASING_COMPANY_LINK);
            $('#trailer_leas_file').show();
        } else {
            $('#trailer_leas_file').hide();
            $("#list_file_trailer_leas_link").html();
        }
        /** Свидетельство о браке */
        if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS === 'passed') {
            $('#trailer_cert_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS === 'in_progress') {
            $('#trailer_cert_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS === 'false') {
            $('#trailer_cert_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK != null ||
            carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK != null
        ) {
            let TRAILER_SEC_CERTIFICATE_LINK = '';

            if (carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK = carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK != null) {
                $.each(carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_cert_link").html(TRAILER_SEC_CERTIFICATE_LINK);
            $('#trailer_cert_file').show();
        } else {
            $('#trailer_cert_file').hide();
            $("#list_file_trailer_cert_link").html();
        }
        /** Договор безвозмездного использования */
        if(carriage.TRAILER_FREE_USAGE_STATUS === 'passed') {
            $('#trailer_usage_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_FREE_USAGE_STATUS === 'in_progress') {
            $('#trailer_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_FREE_USAGE_STATUS === 'false') {
            $('#trailer_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_FREE_USAGE_LINK != null ||
            carriage.TRAILER_FREE_USAGE_EDM_LINK != null
        ) {
            let TRAILER_SEC_FREE_USAGE_LINK = '';

            if (carriage.TRAILER_FREE_USAGE_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK = carriage.TRAILER_FREE_USAGE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_FREE_USAGE_EDM_LINK != null) {
                $.each(carriage.TRAILER_FREE_USAGE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_usage_link").html(TRAILER_SEC_FREE_USAGE_LINK);
            $('#trailer_usage_file').show();
        } else {
            $('#trailer_usage_file').hide();
            $("#list_file_trailer_usage_link").html();
        }

        /** Подтверждения владения прицеп экспедитор*/
        if (carriage.TRAILER_FOR_CHECKS != null) {
            $('#trailer_for').show();
            if (carriage.TRAILER_FOR_CHECKS === '1') {
                $('#trailer_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#trailer_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#trailer_check_for').html('').removeClass();
        }
        /** Номерной знак прицеп */
        if (carriage.TRAILER_LICENSE_PLATE_FOR != null) {
            $('#trailer_plate_for').html(carriage.TRAILER_LICENSE_PLATE_FOR);
        } else {
            $('#trailer_plate_for').html('');
        }
        /** СТС прицеп */
        if(carriage.TRAILER_STS_STATUS_FOR === 'passed') {
            $('#trailer_ctc_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_STS_STATUS_FOR === 'in_progress') {
            $('#trailer_ctc_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_STS_STATUS_FOR === 'false') {
            $('#trailer_ctc_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_STS_LINK_FOR != null || carriage.TRAILER_STS_EDM_LINK_FOR != null) {
            let TRAILER_STS_LINK_FOR = '';

            if (carriage.TRAILER_STS_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_STS_LINK_FOR = carriage.TRAILER_STS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_STS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_STS_LINK_FOR[index] !== '') {
                        TRAILER_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_STS_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_STS_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_STS_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_STS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_ctc_link_for").html(TRAILER_STS_LINK_FOR);
            $('#trailer_ctc_file_for').show();
        } else {
            $('#trailer_ctc_file_for').hide();
            $("#list_file_trailer_ctc_link_for").html();
        }
        /** Договор аренды прицеп */
        if(carriage.TRAILER_RENT_AGREEMENT_STATUS_FOR === 'passed') {
            $('#trailer_rent_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_RENT_AGREEMENT_STATUS_FOR === 'in_progress') {
            $('#trailer_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_RENT_AGREEMENT_STATUS_FOR === 'false') {
            $('#trailer_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_RENT_AGREEMENT_LINK_FOR != null || carriage.TRAILER_RENT_AGREEMENT_EDM_LINK_FOR != null) {
            let TRAILER_RENT_AGR_LINK_FOR = '';

            if (carriage.TRAILER_RENT_AGREEMENT_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_RENT_AGR_LINK_FOR = carriage.TRAILER_RENT_AGREEMENT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_RENT_AGREEMENT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_RENT_AGR_LINK_FOR[index] !== '') {
                        TRAILER_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_RENT_AGR_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_RENT_AGR_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_RENT_AGREEMENT_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_RENT_AGREEMENT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_RENT_AGR_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_rent_link_for").html(TRAILER_RENT_AGR_LINK_FOR);
            $('#trailer_rent_file_for').show();
        } else {
            $('#trailer_rent_file_for').hide();
            $("#list_file_trailer_rent_link_for").html();
        }
        /** Договор с лизинговой компанией */
        if(carriage.TRAILER_LEASING_COMPANY_STATUS_FOR === 'passed') {
            $('#trailer_leas_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_LEASING_COMPANY_STATUS_FOR === 'in_progress') {
            $('#trailer_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_LEASING_COMPANY_STATUS_FOR === 'false') {
            $('#trailer_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_AGR_LEASING_COMPANY_LINK_FOR != null ||
            carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_LEASING_COMPANY_LINK_FOR = '';

            if (carriage.TRAILER_AGR_LEASING_COMPANY_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR = carriage.TRAILER_AGR_LEASING_COMPANY_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_AGR_LEASING_COMPANY_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR[index] !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_AGR_LEASING_COMPANY_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_leas_link_for").html(TRAILER_SEC_LEASING_COMPANY_LINK_FOR);
            $('#trailer_leas_file_for').show();
        } else {
            $('#trailer_leas_file_for').hide();
            $("#list_file_trailer_leas_link_for").html();
        }
        /** Свидетельство о браке */
        if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS_FOR === 'passed') {
            $('#trailer_cert_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS_FOR === 'in_progress') {
            $('#trailer_cert_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_MARRIAGE_CERTIFICATE_STATUS_FOR === 'false') {
            $('#trailer_cert_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR != null ||
            carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_CERTIFICATE_LINK_FOR = '';

            if (carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR = carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_MARRIAGE_CERTIFICATE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_MARRIAGE_CERTIFICATE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_cert_link_for").html(TRAILER_SEC_CERTIFICATE_LINK_FOR);
            $('#trailer_cert_file_for').show();
        } else {
            $('#trailer_cert_file_for').hide();
            $("#list_file_trailer_cert_link_for").html();
        }
        /** Договор безвозмездного использования */
        if(carriage.TRAILER_FREE_USAGE_STATUS_FOR === 'passed') {
            $('#trailer_usage_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_FREE_USAGE_STATUS_FOR === 'in_progress') {
            $('#trailer_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_FREE_USAGE_STATUS_FOR === 'false') {
            $('#trailer_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_FREE_USAGE_LINK_FOR != null ||
            carriage.TRAILER_FREE_USAGE_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_FREE_USAGE_LINK_FOR = '';

            if (carriage.TRAILER_FREE_USAGE_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR = carriage.TRAILER_FREE_USAGE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_FREE_USAGE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_FREE_USAGE_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_FREE_USAGE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_usage_link_for").html(TRAILER_SEC_FREE_USAGE_LINK_FOR);
            $('#trailer_usage_file_for').show();
        } else {
            $('#trailer_usage_file_for').hide();
            $("#list_file_trailer_usage_link_for").html();
        }
    }

    /** Блок второго прицепа */
    function trailerSec(carriage) {
        /** Подтверждения владения второго прицеп */
        if (carriage.TRAILER_SECONDARY_CHECKS != null) {
            $('#trailer_sec').show();
            if (carriage.TRAILER_SECONDARY_CHECKS === '1') {
                $('#trailer_sec_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#trailer_sec_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
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
        if(carriage.TRAILER_SECONDARY_STS_STATUS === 'passed') {
            $('#trailer_sec_ctc_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_STS_STATUS === 'in_progress') {
            $('#trailer_sec_ctc_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_STS_STATUS === 'false') {
            $('#trailer_sec_ctc_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_STS_LINK != null || carriage.TRAILER_SECONDARY_STS_EDM_LINK != null) {
            let TRAILER_SEC_STS_LINK = '';

            if (carriage.TRAILER_SECONDARY_STS_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_STS_LINK = carriage.TRAILER_SECONDARY_STS_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_STS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_STS_LINK[index] !== '') {
                        TRAILER_SEC_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_STS_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_STS_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_STS_EDM_LINK != null) {
                $.each(carriage.TRAILER_SECONDARY_STS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_STS_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_ctc_link").html(TRAILER_SEC_STS_LINK);
            $('#trailer_sec_ctc_file').show();
        } else {
            $('#trailer_sec_ctc_file').hide();
            $("#list_file_trailer_sec_ctc_link").html();
        }
        /** Договор аренды второго прицепа */
        if(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_STATUS === 'passed') {
            $('#trailer_sec_rent_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_STATUS === 'in_progress') {
            $('#trailer_sec_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_STATUS === 'false') {
            $('#trailer_sec_rent_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK != null ||
            carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK != null
        ) {
            let TRAILER_SEC_RENT_LINK = '';

            if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_RENT_LINK = carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_RENT_LINK[index] !== '') {
                        TRAILER_SEC_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_RENT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_RENT_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK != null) {
                $.each(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_rent_link").html(TRAILER_SEC_RENT_LINK);
            $('#trailer_sec_rent_file').show();
        } else {
            $('#trailer_sec_rent_file').hide();
            $("#list_file_trailer_sec_rent_link").html();
        }

        /** Договор с лизинговой компанией второго (прицеп) */
        if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS === 'passed') {
            $('#trailer_sec_lias_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS === 'in_progress') {
            $('#trailer_sec_lias_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS === 'false') {
            $('#trailer_sec_lias_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK != null ||
            carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK != null
        ) {
            let TRAILER_SEC_LEASING_COMPANY_LINK = '';

            if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK = carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK != null) {
                $.each(carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_lias_link").html(TRAILER_SEC_LEASING_COMPANY_LINK);
            $('#trailer_sec_lias_file').show();
        } else {
            $('#trailer_sec_lias_file').hide();
            $("#list_file_trailer_sec_lias_link").html();
        }

        /** Свидетельство о браке второго (прицепа) */
        if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS === 'passed') {
            $('#trailer_sec_cer_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS === 'in_progress') {
            $('#trailer_sec_cer_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS === 'false') {
            $('#trailer_sec_cer_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK != null ||
            carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK != null
        ) {
            let TRAILER_SEC_CERTIFICATE_LINK = '';

            if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK = carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK != null) {
                $.each(carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_cer_link").html(TRAILER_SEC_CERTIFICATE_LINK_FOR);
            $('#trailer_sec_cer_file').show();
        } else {
            $('#trailer_sec_cer_file').hide();
            $("#list_file_trailer_sec_cer_link").html();
        }

        /** Договор безвозмездного использования второго (прицепа) */
        if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS === 'passed') {
            $('#trailer_sec_usage_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS === 'in_progress') {
            $('#trailer_sec_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS === 'false') {
            $('#trailer_sec_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_FREE_USAGE_LINK != null ||
            carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK != null
        ) {
            let TRAILER_SEC_FREE_USAGE_LINK = '';

            if (carriage.TRAILER_SECONDARY_FREE_USAGE_LINK != null) {
                const DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK = carriage.TRAILER_SECONDARY_FREE_USAGE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK != null) {
                $.each(carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_usage_link").html(TRAILER_SEC_FREE_USAGE_LINK);
            $('#trailer_sec_usage_file').show();
        } else {
            $('#trailer_sec_usage_file').hide();
            $("#list_file_trailer_sec_usage_link").html();
        }

        /** Подтверждения владения второго прицеп экспедитора */
        if (carriage.TRAILER_SECONDARY_FOR_CHECKS != null) {
            $('#trailer_sec_for').show();
            if (carriage.TRAILER_SECONDARY_FOR_CHECKS === '1') {
                $('#trailer_sec_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#trailer_sec_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#trailer_sec_check_for').html('').removeClass();
        }
        /** Номерной знак второго прицеп */
        if (carriage.TRAILER_SECONDARY_LICENSE_PLATE_FOR != null) {
            $('#trailer_sec_plate_for').html(carriage.TRAILER_SECONDARY_LICENSE_PLATE_FOR);
        } else {
            $('#trailer_sec_plate_for').html('');
        }
        /** СТС второго прицеп */
        if(carriage.TRAILER_SECONDARY_STS_STATUS_FOR === 'passed') {
            $('#trailer_sec_ctc_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_STS_STATUS_FOR === 'in_progress') {
            $('#trailer_sec_ctc_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_STS_STATUS_FOR === 'false') {
            $('#trailer_sec_ctc_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_STS_LINK_FOR != null ||
            carriage.TRAILER_SECONDARY_STS_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_STS_LINK_FOR = '';

            if (carriage.TRAILER_SECONDARY_STS_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_STS_LINK_FOR = carriage.TRAILER_SECONDARY_STS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_STS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_STS_LINK_FOR[index] !== '') {
                        TRAILER_SEC_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_STS_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_STS_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_STS_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_SECONDARY_STS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_STS_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_ctc_link_for").html(TRAILER_SEC_STS_LINK_FOR);
            $('#trailer_sec_ctc_file_for').show();
        } else {
            $('#trailer_sec_ctc_file_for').hide();
            $("#list_file_trailer_sec_ctc_link_for").html();
        }
        /** Договор аренды второго прицепа */
        if(carriage.TRAILER_SECONDARY_RENT_AGR_STATUS_FOR === 'passed') {
            $('#trailer_sec_rent_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_RENT_AGR_STATUS_FOR === 'in_progress') {
            $('#trailer_sec_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_RENT_AGR_STATUS_FOR === 'false') {
            $('#trailer_sec_rent_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR != null ||
            carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_RENT_LINK_FOR = '';

            if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_RENT_LINK_FOR = carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_RENT_LINK_FOR[index] !== '') {
                        TRAILER_SEC_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_RENT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_RENT_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_SECONDARY_RENT_AGREEMENT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_rent_link_for").html(TRAILER_SEC_RENT_LINK_FOR);
            $('#trailer_sec_rent_file_for').show();
        } else {
            $('#trailer_sec_rent_file_for').hide();
            $("#list_file_trailer_sec_rent_link_for").html();
        }
        /** Договор с лизинговой компанией второго (прицеп) */
        if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS_FOR === 'passed') {
            $('#trailer_sec_lias_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS_FOR === 'in_progress') {
            $('#trailer_sec_lias_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_LEASING_COMPANY_STATUS_FOR === 'false') {
            $('#trailer_sec_lias_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR != null ||
            carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_LEASING_COMPANY_LINK_FOR = '';

            if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR = carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR[index] !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_LEASING_COMPANY_LINK[index] === '') {
                    } else {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_SECONDARY_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_lias_link_for").html(TRAILER_SEC_LEASING_COMPANY_LINK_FOR);
            $('#trailer_sec_lias_file_for').show();
        } else {
            $('#trailer_sec_lias_file_for').hide();
            $("#list_file_trailer_sec_lias_link_for").html();
        }
        /** Свидетельство о браке второго (прицепа) */
        if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS_FOR === 'passed') {
            $('#trailer_sec_cer_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS_FOR === 'in_progress') {
            $('#trailer_sec_cer_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_CERTIFICATE_STATUS_FOR === 'false') {
            $('#trailer_sec_cer_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR != null ||
            carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_CERTIFICATE_LINK_FOR = '';

            if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR = carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_CERTIFICATE_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_SECONDARY_MARRIAGE_CERTIFICATE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_cer_link_for").html(TRAILER_SEC_CERTIFICATE_LINK_FOR);
            $('#trailer_sec_cer_file_for').show();
        } else {
            $('#trailer_sec_cer_file_for').hide();
            $("#list_file_trailer_sec_cer_link_for").html();
        }
        /** Договор безвозмездного использования второго (прицепа) */
        if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS_FOR === 'passed') {
            $('#trailer_sec_usage_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS_FOR === 'in_progress') {
            $('#trailer_sec_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRAILER_SECONDARY_FREE_USAGE_STATUS_FOR === 'false') {
            $('#trailer_sec_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRAILER_SECONDARY_FREE_USAGE_LINK_FOR != null ||
            carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK_FOR != null
        ) {
            let TRAILER_SEC_FREE_USAGE_LINK_FOR = '';

            if (carriage.TRAILER_SECONDARY_FREE_USAGE_LINK_FOR != null) {
                const DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR = carriage.TRAILER_SECONDARY_FREE_USAGE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRAILER_SECONDARY_FREE_USAGE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRAILER_SEC_FREE_USAGE_LINK_FOR[index] === '') {
                    } else {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK_FOR != null) {
                $.each(carriage.TRAILER_SECONDARY_FREE_USAGE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRAILER_SEC_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_trailer_sec_usage_link_for").html(TRAILER_SEC_FREE_USAGE_LINK_FOR);
            $('#trailer_sec_usage_file_for').show();
        } else {
            $('#trailer_sec_usage_file_for').hide();
            $("#list_file_trailer_sec_usage_link_for").html();
        }
    }

    /** Блок грузовика */
    function truck(carriage) {
        /** Подтверждение владения грузовик */
        if (carriage.TRUCK_CHECKS != null) {
            $('#truck').show();
            if (carriage.TRUCK_CHECKS === '1') {
                $('#truck_check').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#truck_check').removeClass().addClass('detail-status_expectation').html('На оформлении');
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
        if(carriage.TRUCK_STS_STATUS === 'passed') {
            $('#truck_sts_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_STS_STATUS === 'in_progress') {
            $('#truck_sts_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_STS_STATUS === 'false') {
            $('#truck_sts_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_STS_LINK != null || carriage.TRUCK_STS_EDM_LINK != null) {
            let TRUCK_STS_LINKS = '';

            if (carriage.TRUCK_STS_LINK != null) {
                const DESCRIPTION_TRUCK_STS_LINK = carriage.TRUCK_STS_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_STS_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_STS_LINK[index] !== '') {
                        TRUCK_STS_LINKS += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + DESCRIPTION_TRUCK_STS_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_STS_LINK[index] === '') {
                    } else {
                        TRUCK_STS_LINKS += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_STS_EDM_LINK != null) {
                $.each(carriage.TRUCK_STS_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_STS_LINKS += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_sts_link").html(TRUCK_STS_LINKS);
            $('#truck_sts_file').show();
        } else {
            $('#truck_sts_file').hide();
            $("#list_file_truck_sts_link").html();
        }
        /** Договор аренды грузовик */
        if(carriage.TRUCK_RENT_AGREEMENT_STATUS === 'passed') {
            $('#truck_rent').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_RENT_AGREEMENT_STATUS === 'in_progress') {
            $('#truck_rent').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_RENT_AGREEMENT_STATUS === 'false') {
            $('#truck_rent').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_RENT_AGREEMENT_LINK != null || carriage.TRUCK_RENT_AGREEMENT_EDM_LINK != null) {
            let TRUCK_RENT_LINK = '';

            if (carriage.TRUCK_RENT_AGREEMENT_LINK != null) {
                const DESCRIPTION_TRUCK_RENT_LINK = carriage.TRUCK_RENT_AGREEMENT_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_RENT_AGREEMENT_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_RENT_LINK[index] !== '') {
                        TRUCK_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_RENT_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_RENT_LINK[index] === '') {
                    } else {
                        TRUCK_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_RENT_AGREEMENT_EDM_LINK != null) {
                $.each(carriage.TRUCK_RENT_AGREEMENT_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_RENT_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_rent").html(TRUCK_RENT_LINK);
            $('#truck_link').show();
        } else {
            $('#truck_link').hide();
            $("#list_file_truck_rent").html();
        }
        /** Договор с лизинговой компанией грузовик */
        if(carriage.TRUCK_LEASING_COMPANY_STATUS === 'passed') {
            $('#truck_leas_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_LEASING_COMPANY_STATUS === 'in_progress') {
            $('#truck_leas_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_LEASING_COMPANY_STATUS === 'false') {
            $('#truck_leas_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK != null ||
            carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK != null
        ) {
            let TRUCK_LEASING_COMPANY_LINK = '';

            if (carriage.TRUCK_RENT_AGREEMENT_LINK != null) {
                const DESCRIPTION_TRUCK_LEASING_COMPANY_LINK = carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] !== '') {
                        TRUCK_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK[index] === '') {
                    } else {
                        TRUCK_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK != null) {
                $.each(carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_LEASING_COMPANY_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_leas_link").html(TRUCK_LEASING_COMPANY_LINK);
            $('#truck_leas_file').show();
        } else {
            $('#truck_leas_file').hide();
            $("#list_file_truck_leas_link").html();
        }
        /** Свидетельство о браке грузовик */
        if(carriage.TRUCK_CERTIFICATE_STATUS === 'passed') {
            $('#truck_cert_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_CERTIFICATE_STATUS === 'in_progress') {
            $('#truck_cert_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_CERTIFICATE_STATUS === 'false') {
            $('#truck_cert_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK != null ||
            carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK != null
        ) {
            let TRUCK_CERTIFICATE_LINK = '';

            if (carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK != null) {
                const DESCRIPTION_TRUCK_CERTIFICATE_LINK = carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] !== '') {
                        TRUCK_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_CERTIFICATE_LINK[index] === '') {
                    } else {
                        TRUCK_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK != null) {
                $.each(carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_CERTIFICATE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_cert_link").html(TRUCK_CERTIFICATE_LINK);
            $('#truck_cert_file').show();
        } else {
            $('#truck_cert_file').hide();
            $("#list_file_truck_cert_link").html();
        }
        /** Договор безвозмездного использования грузовик */
        if(carriage.TRUCK_FREE_USAGE_STATUS === 'passed') {
            $('#truck_usage_link').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_FREE_USAGE_STATUS === 'in_progress') {
            $('#truck_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_FREE_USAGE_STATUS === 'false') {
            $('#truck_usage_link').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_FREE_USAGE_LINK != null || carriage.TRUCK_FREE_USAGE_EDM_LINK != null) {
            let TRUCK_FREE_USAGE_LINK = '';

            if (carriage.TRUCK_FREE_USAGE_LINK != null) {
                const DESCRIPTION_TRUCK_FREE_USAGE_LINK = carriage.TRUCK_FREE_USAGE_LINK.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_FREE_USAGE_LINK.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] !== '') {
                        TRUCK_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_FREE_USAGE_LINK[index] === '') {
                    } else {
                        TRUCK_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_FREE_USAGE_EDM_LINK != null) {
                $.each(carriage.TRUCK_FREE_USAGE_EDM_LINK.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_FREE_USAGE_LINK += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_usage_link").html(TRUCK_FREE_USAGE_LINK);
            $('#truck_usage_file').show();
        } else {
            $('#truck_usage_file').hide();
            $("#list_file_truck_usage_link").html();
        }

        /** Подтверждение владения грузовик экспедитор*/
        if (carriage.TRUCK_FOR_CHECKS != null) {
            $('#truck_for').show();
            if (carriage.TRUCK_FOR_CHECKS === '1') {
                $('#truck_check_for').removeClass().addClass('detail-status_good').html('Выполнено');
            } else {
                $('#truck_check_for').removeClass().addClass('detail-status_expectation').html('На оформлении');
            }
        } else {
            $('#truck_check_for').html('').removeClass();
        }
        /** Номерной знак грузовик */
        if (carriage.TRUCK_LICENSE_PLATE_FOR != null) {
            $('#truck_plate_for').html(carriage.TRUCK_LICENSE_PLATE_FOR);
        } else {
            $('#truck_plate_for').html('');
        }
        /** СТС грузовик */
        if(carriage.TRUCK_STS_STATUS_FOR === 'passed') {
            $('#truck_sts_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_STS_STATUS_FOR === 'in_progress') {
            $('#truck_sts_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_STS_STATUS_FOR === 'false') {
            $('#truck_sts_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_STS_LINK_FOR != null || carriage.TRUCK_STS_EDM_LINK_FOR != null) {
            let TRUCK_STS_LINKS_FOR = '';

            if (carriage.TRUCK_STS_LINK_FOR != null) {
                const DESCRIPTION_TRUCK_STS_LINK_FOR = carriage.TRUCK_STS_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_STS_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_STS_LINK_FOR[index] !== '') {
                        TRUCK_STS_LINKS_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + DESCRIPTION_TRUCK_STS_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_STS_LINK_FOR[index] === '') {
                    } else {
                        TRUCK_STS_LINKS_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_STS_EDM_LINK_FOR != null) {
                $.each(carriage.TRUCK_STS_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_STS_LINKS_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_sts_link_for").html(TRUCK_STS_LINKS_FOR);
            $('#truck_sts_file_for').show();
        } else {
            $('#truck_sts_file_for').hide();
            $("#list_file_truck_sts_link_for").html();
        }
        /** Договор аренды грузовик */
        if(carriage.TRUCK_RENT_AGR_STATUS_FOR === 'passed') {
            $('#truck_rent_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_RENT_AGR_STATUS_FOR === 'in_progress') {
            $('#truck_rent_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_RENT_AGR_STATUS_FOR === 'false') {
            $('#truck_rent_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_RENT_AGREEMENT_LINK_FOR != null || carriage.TRUCK_RENT_AGREEMENT_EDM_LINK_FOR != null) {
            let TRUCK_RENT_LINK_FOR = '';

            if (carriage.TRUCK_RENT_AGREEMENT_LINK_FOR != null) {
                const DESCRIPTION_TRUCK_RENT_LINK_FOR = carriage.TRUCK_RENT_AGREEMENT_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_RENT_AGREEMENT_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_RENT_LINK_FOR[index] !== '') {
                        TRUCK_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_RENT_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_RENT_LINK[index] === '') {
                    } else {
                        TRUCK_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_RENT_AGREEMENT_EDM_LINK_FOR != null) {
                $.each(carriage.TRUCK_RENT_AGREEMENT_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_RENT_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_rent_for").html(TRUCK_RENT_LINK_FOR);
            $('#truck_link_for').show();
        } else {
            $('#truck_link_for').hide();
            $("#list_file_truck_rent_for").html();
        }
        /** Договор с лизинговой компанией грузовик */
        if(carriage.TRUCK_AGR_LEASING_COMPANY_STATUS_FOR === 'passed') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_AGR_LEASING_COMPANY_STATUS_FOR === 'in_progress') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_AGR_LEASING_COMPANY_STATUS_FOR === 'false') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR != null ||
            carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null
        ) {
            let TRUCK_LEASING_COMPANY_LINK_FOR = '';

            if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR != null) {
                const DESCRIPTION_TRUCK_LEASING_COMPANY_LINK_FOR = carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_AGREEMENT_LEASING_COMPANY_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK_FOR[index] !== '') {
                        TRUCK_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_LEASING_COMPANY_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_LEASING_COMPANY_LINK_FOR[index] === '') {
                    } else {
                        TRUCK_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR != null) {
                $.each(carriage.TRUCK_AGREEMENT_LEASING_COMPANY_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_LEASING_COMPANY_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_leas_link_for").html(TRUCK_LEASING_COMPANY_LINK_FOR);
            $('#truck_leas_file_for').show();
        } else {
            $('#truck_leas_file_for').hide();
            $("#list_file_truck_leas_link_for").html();
        }
        /** Свидетельство о браке грузовик */
        if(carriage.TRUCK_CERTIFICATE_STATUS_FOR === 'passed') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_CERTIFICATE_STATUS_FOR === 'in_progress') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_CERTIFICATE_STATUS_FOR === 'false') {
            $('#truck_leas_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR != null ||
            carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null
        ) {
            let TRUCK_CERTIFICATE_LINK_FOR = '';

            if (carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR != null) {
                const DESCRIPTION_TRUCK_CERTIFICATE_LINK_FOR = carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_MARRIAGE_CERTIFICATE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_CERTIFICATE_LINK_FOR[index] !== '') {
                        TRUCK_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_CERTIFICATE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_CERTIFICATE_LINK_FOR[index] === '') {
                    } else {
                        TRUCK_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK_FOR != null) {
                $.each(carriage.TRUCK_MARRIAGE_CERTIFICATE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_CERTIFICATE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_cert_link_for").html(TRUCK_CERTIFICATE_LINK_FOR);
            $('#truck_cert_file_for').show();
        } else {
            $('#truck_cert_file_for').hide();
            $("#list_file_truck_cert_link_for").html();
        }
        /** Договор безвозмездного использования грузовик */
        if(carriage.TRUCK_FREE_USAGE_STATUS_FOR === 'passed') {
            $('#truck_usage_link_for').show().removeClass().addClass('status-info_confirmation');
        } else if(carriage.TRUCK_FREE_USAGE_STATUS_FOR === 'in_progress') {
            $('#truck_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_progress');
        } else if(carriage.TRUCK_FREE_USAGE_STATUS_FOR === 'false') {
            $('#truck_usage_link_for').show().removeClass().addClass('status-info_confirmation status-info_confirmation_error');
        }

        if (carriage.TRUCK_FREE_USAGE_LINK_FOR != null || carriage.TRUCK_FREE_USAGE_EDM_LINK_FOR != null) {
            let TRUCK_FREE_USAGE_LINK_FOR = '';

            if (carriage.TRUCK_FREE_USAGE_LINK_FOR != null) {
                const DESCRIPTION_TRUCK_FREE_USAGE_LINK_FOR = carriage.TRUCK_FREE_USAGE_LINK_FOR.DESCRIPTION.split(",");

                $.each(carriage.TRUCK_FREE_USAGE_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (DESCRIPTION_TRUCK_FREE_USAGE_LINK_FOR[index] !== '') {
                        TRUCK_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">' + DESCRIPTION_TRUCK_FREE_USAGE_LINK_FOR[index] + '</span></li>';
                    } else if (DESCRIPTION_TRUCK_FREE_USAGE_LINK_FOR[index] === '') {
                    } else {
                        TRUCK_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    }
                });
            }

            if (carriage.TRUCK_FREE_USAGE_EDM_LINK_FOR != null) {
                $.each(carriage.TRUCK_FREE_USAGE_EDM_LINK_FOR.VALUE.split(","), function (index, value) {
                    if (value !== '') {
                        TRUCK_FREE_USAGE_LINK_FOR += '<li><span data-id="' +  value + '" id="cheklist_file">Файл ' + (index + 1) + '</span></li>';
                    } else if (value === '') {
                    }
                });
            }

            $("#list_file_truck_usage_link_for").html(TRUCK_FREE_USAGE_LINK_FOR);
            $('#truck_usage_file_for').show();
        } else {
            $('#truck_usage_file_for').hide();
            $("#list_file_truck_usage_link_for").html();
        }
    }


});