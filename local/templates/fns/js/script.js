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
            $('.filter_list-type-organizations').show();
        } else {
            $('.filter_list-type-organizations').hide();
        }
    });

    /**
     * Получаем информацию в
     * инфо бар по перевозки
     */
    $('.info_bar-content').click(function() {
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

                    let [year, month, day] = carriage.DATE_SHIPMENT_VALUE.split("-");

                    /**
                     * Данные перевозки для инфо бара
                     */
                    $('#carriage_id').html(carriage.NAME);
                    $('#carriage_date').html(day + '.' + month + '.' + year);
                    $('#carriage_deviation-price').html(carriage.DEVIATION_MARKET_PRICE_VALUE + '%');

                    if (carriage.CARGO_OWNER_VALUE != null) {
                        $('#carriage_owner').html(
                            'Грузовладелец <span class="carriage_name">' +
                            carriage.CARGO_OWNER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.CARGO_OWNER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_owner').html('');
                    }

                    if (carriage.CARRIER_VALUE != null) {
                        $('#carriage_carrier').html(
                            'Перевозчик <span class="carriage_name">' +
                            carriage.CARRIER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.CARRIER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_carrier').html('');
                    }

                    if (carriage.FORWARDER_VALUE != null) {
                        $('#carriage_forwarder').html(
                            'Экспедитор <span class="carriage_name">' +
                            carriage.FORWARDER_VALUE + '</span><span id="carriage_owner_inn">' +
                            carriage.FORWARDER_INN_VALUE + '</span>'
                        )
                    } else {
                        $('#carriage_forwarder').html('');
                    }

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