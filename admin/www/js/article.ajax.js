function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#article-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("article-detail");
}

function updateArticle(articleId) {
	resetTabs();

    // load article update
	$('#article-detail').load('?section=366',
	{ articleId : articleId },
	function(data){
		$('#article-container div.tab').hide();
		$('#article-detail').show();
		$('input#articleId').val(articleId);

		wrapperRestore();

		$('#article-container').show();
        
        $(".idTabs").idTabs("article-detail");
		$('.idTabs').show();
	});
    
}

function insertArticle() {
	resetTabs();

    // load article update
	$('#article-detail').load('?section=367',
	{  },
	function(data){
		$('#article-container div.tab').hide();
		$('#article-detail').show();

		wrapperRestore();

		$('#article-container').show();
		$('.idTabs').show();
		
		loadOddTips();
	});
}

function deleteArticle(articleId) {
	resetTabs();
	$('.idTabs').hide();

	$('#article-detail').load('?section=368',
	{ articleId: articleId },
	function(data){
		$('#article-container div.tab').hide();
		$('#article-detail').show();
		$("#list").load(
			'?section=365 #list',
			{  },
			function(data){
				wrapperRestore();
				hideTabs(section);
			});

		$('#article-container').show();
	});
}

// called onchange on bet alias field
function setArticleBetId(alias) {
    $.post('?section=369', {'alias':alias}, function(data) {
        clearBet();
        
        var result = jQuery.parseJSON(data);
        
		//console.log('result');
		//console.log(result);
		
        // 1) ID sazky + info o sazce
        $('#articleBetId').val(result.bet.betId);
        $('#betSpanInfo').remove();
        
		if (typeof result.bet.name !== 'undefined') {
			$('#articleBetAliasInserted').after('<span id="betSpanInfo" style="margin-left:7px;">(' + result.bet.name + ', ' + result.bet.sportName
					+ ', ' + result.bet.regionName + ', ' + result.bet.eventName + ')</span>');
		}
        
        // 2) naplnit tipy (multiselect)
        var betTipsCnt = result['aux'].length;
        if (betTipsCnt === 0) {
            $('#betAuxSpanInfo').remove();
            $('#articleBetAux').after('<span id="betAuxSpanInfo" style="margin-left:7px;">(Žádné sázky, zkontrolujte vložený alias.)</span>');
        }
		
        var options = '';
        for(var i=0; i<betTipsCnt; i++) {
			var betNote = (result['aux'][i].betNote == '') ? ' '+result['aux'][i].betNote + ', ' : ', ';
			options += '<option value="' + result['aux'][i].betId + '">' + result['aux'][i].typeName + betNote + result['aux'][i].name + '</option>';
        }
        $('#articleBetAux').html(options);
    });
}

function clearBet(){
    $('#articleBetId').val('');
    $('#betSpanInfo').remove();
    $('#betAuxSpanInfo').remove();
    $('#articleBetAux').html('');
	$('.articleOddsDiv').remove();
}

function previewArticle(webHost, lang, articleId, publisherKey) {
	window.open('http://' + webHost + lang + '/clanek/'+articleId+'?k='+publisherKey,'_blank');
}

function loadOddTips(selectedOddsFromDb) {
	
	$('#articleBetAux :not(:selected)').each(function(i, notselected) {
		$('#radioDiv'+$(notselected).val()).remove();
	});
	
	
	var selectedBets = [];
	$('#articleBetAux :selected').each(function(i, selected) {
		selectedBets[i] = [];
		selectedBets[i]['optName'] = $(selected).text(); 
		selectedBets[i]['optValue'] = $(selected).val();
	});
	
	if(selectedBets.length > 0) {
		$('#articleBetAux').after('<div id="oddsHtml"></div>');
		for (var i = 0; i < selectedBets.length; i++) {

			if (typeof $('#radioDiv' + selectedBets[i]['optValue']).html() !== 'undefined') {
				continue; // already added
			}

			// create radios
			$.get('/?section=370',
				{ 'betId':selectedBets[i]['optValue'],
				'betOptionName':selectedBets[i]['optName'] }, function(data) {
				
				data = $.parseJSON(data);
				
				if (data.odds.length > 0) {
					
					var oddsOptions = '<div class="left articleOddsDiv" id="radioDiv'+data.betId+'">'
									+ '<p>'+data.betOptionName+':</p>';
					
					for (var j=0; j<data.odds.length; j++) {
						//console.log('data.odds[j]:');
						//console.log(data.odds[j]);
						
						var radioId = data.odds[j]['sazka_id'] + '-' + data.odds[j]['sloupec_id'];
						var radioValue = data.odds[j]['sloupec_id'];
						var radioName = 'odds-' + data.odds[j]['sazka_id'];
						var radioLabel = data.odds[j]['nazev'] + ' - kurz ' + data.odds[j]['kurz'];
						
						var checked = '';
						var sazkaId = data.odds[j]['sazka_id'];
						if(typeof selectedOddsFromDb !== 'undefined'
						   && typeof selectedOddsFromDb[sazkaId] !== 'undefined'
							&& selectedOddsFromDb[sazkaId] === data.odds[j]['sloupec_id']){
							checked = ' checked="checked" ';
						}
						
						oddsOptions += '<div class="articleOddsRadios">\n\
										<input type="radio" id="'+radioId+'"\n\
										 name="'+radioName+'" value="'+radioValue+'"'+checked+'/>\n\
										<label for="'+radioId+'">'+radioLabel+'</label></div>';
					}
					
					oddsOptions += '</div>';
					
					$("#articleBetAux").after(oddsOptions);
				}
			});
		}
	}
}