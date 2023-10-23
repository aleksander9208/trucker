$(document).ready(function() {
    $(window).keyup(function(e){
        var target = $('.filter_checkbox input:focus');
        if (e.keyCode == 9 && $(target).length){
            $(target).parent().addClass('focused');
        }
    });

    $('.filter_checkbox input').focusout(function(){
        $(this).parent().removeClass('focused');
    });
});