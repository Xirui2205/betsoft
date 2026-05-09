/* To set up field character counter for a field where id="titleField", insert the 
 * following block in view.
 * The output will be put in element where id="titleFieldCounter"
 * The max length varaibl in js has to be called "titleFieldMaxLength"
 * 
 * Note the "titleField" part if the variable name and the element ids.
 * 
 * <script type="text/javascript">
 *   var maxLengths = new Array();
 *   maxLengths['titleFieldMaxLength']	= max_number_of_characters;
 * 
 *   countFields = new Array();
 *   countFields[0] = 'titleField';
 * </script>
 * 
 * <textarea name="text" class="smallTa" id="textField"><?=(!empty($fieldData['text']) ? $fieldData['text'] : '')?></textarea>
 * <div>
 *   <?=I18n::tr('Characters Left:')?>
 *   <span id="titleFieldCounter" class="char-counter"></span>
 * </div>
 **/



function charCount(element) {
	var curLength	= $(element).val().length;
	var elementId	= $(element).attr('id');
	var counter		= $('#' + elementId + 'Counter');
	var charsLeft	= maxLengths[elementId + 'MaxLength'] - curLength;
	
	$(counter).text(charsLeft);
	if(charsLeft < 0) {
		$(counter).addClass('blocked');
	}
	else if(charsLeft > 0) {
		$(counter).addClass('allowed');
	}
	else {
		$(counter).removeClass('blocked');
		$(counter).removeClass('allowed');
	}
}

$(document).ready(function() {
	if(typeof(countFields) != 'undefined') {
		$.each(countFields, function(index, value) {
			element = $('#' + value);
			charCount(element);

			$(element).change(function() {
				charCount(this);
			});
			
			$(element).keyup(function() {
				charCount(this);
			});
		});
	}
	
});


