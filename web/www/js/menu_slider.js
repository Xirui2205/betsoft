$(document).ready(function () {

    // initial position
    var child = 1;
    var pathName = window.location.pathname;

    if (pathName.indexOf("sazky") != -1) {
        child = 1;
    } else if (pathName.indexOf("pobocky") != -1) {
        child = 4;
    } else if (pathName.indexOf("spoluprace") != -1) {
        child = 5;
    } else if (pathName.indexOf("bonusy") != -1) {
        child = 6;
    } else if (pathName.indexOf("souteze") != -1) {
        child = 7;
    } else if (pathName.indexOf("mobile")!= -1) {
        child = 8;
    }

    var item = $('nav ul li:nth-child(' + child + ') a');

    // insert slider
    $('nav ul').append('<div id="slider"></div>');

    // initially reset
    var width = item.width();

    // IE7 hack
    if (item.parent().position() == null ) {
        var left = 1;
    } else {
        var left = item.parent().position().left+1;
    }

    $('#slider').css({'left' : left, 'width' : width});

    // sliding
    $('nav ul li a').hover(function(){

        var width = $(this).width();
        // IE7 hack
        if (item.parent().position() == null ) {
            var left = 1;
        } else {
            var left = $(this).parent().position().left+1;
        }

        $('#slider').stop().animate({
            'left' : left,
            'width' : width
        });
    });
});
