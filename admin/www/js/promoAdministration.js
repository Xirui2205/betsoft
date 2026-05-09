var prevWindows = [];
var titleDomSelector = 'div.mbanner .layer1 h2';
var textDomSelector = 'div.mbanner .layer1 div.text';
var imgSrcDomSelector = 'div.mbanner .layer3 img';

$(window).unload(function() {
    for (win in prevWindows) {
        prevWindows[win].close();
    }
})

function showFullPromo(element, i) {
	$(element).parent().html('<div class="show-hide-promo" onClick="showShortPromo(this, ' + i + ')">^ ^ ^</div>');
	$('.hideMe' + i).show();
}

function showShortPromo(element, i) {
	$(element).parent().html('<div class="show-hide-promo" onClick="showFullPromo(this, ' + i + ')">v v v</div>');
	$('.hideMe' + i).hide();
}



function showShortPromos() {
	$('.hidden').hide();
	$('.show-hide-promo').html('v v v');
}

function showFullPromos() {
	$('.hidden').show();
	$('.show-hide-promo').html('^ ^ ^');
}



function updatePreviewText(element, promoId) {
    updatePreview(element, promoId, textDomSelector)
}

function updatePreviewTitle(element, promoId) {
    updatePreview(element, promoId, titleDomSelector)
}

function updatePreview(element, promoId, domSelector) {

    var newVal = $(element).val();
    var prevWin = prevWindows['prevWin' + promoId];
    //if(prevWin != undefined) {
        $(domSelector, prevWin.document).html(newVal);
    //}
}

function updatePreviewImg(element, promoId, domSelector) {
    var imgSrc = $(element).val();
    var prevWin = window.prevWindows['prevWin' + promoId];
    var imgPath = $(imgSrcDomSelector, prevWin.document).attr('src');
    var imgPathArr = imgPath.split('/');
    imgPathArr.pop();
    imgPath = imgPathArr.join('/') + '/';
    //if(prevWin != undefined) {
        $(imgSrcDomSelector, prevWin.document).attr('src', imgPath + imgSrc);
    //}
}

function openPreviewWin(promoId) {
    //var prevWindows['prevWin' + promoId] = false;  
    if (prevWindows['prevWin' + promoId] && !prevWindows['prevWin' + promoId].closed) {
      prevWindows['prevWin' + promoId].focus();  
    }  
    else {  
      prevWindows['prevWin' + promoId] = window.open('?section=321&promo_id=' + promoId, 'preview-' + promoId, 'winPop, height=700, width=1050'); 
    }  
    return false;
}

function updatePreviewWin(promoId) {
    var prevWin = prevWindows['prevWin' + promoId];
    
    var newTextFieldVal = $("#textField").val();
    $(textDomSelector, prevWin.document).html(newTextFieldVal);
    
    var newTitlFieldeVal = $("#titleField").val();
    $(titleDomSelector, prevWin.document).html(newTitlFieldeVal);

    var imgSrc = $("#img_name").val();
    var imgPath = $(imgSrcDomSelector, prevWin.document).attr('src');
    var imgPathArr = imgPath.split('/');
    imgPathArr.pop();
    imgPath = imgPathArr.join('/') + '/';
    $(imgSrcDomSelector, prevWin.document).attr('src', imgPath + imgSrc);

    return false;
}
