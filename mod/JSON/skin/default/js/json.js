jQuery.fn.getSource = function(editor)
{
	var self = this;
	var $toggleButton = this.find('button[data-action=toggle_form]');
	var $form = this.find('.form');
	var $formButtons = {
		close : $form.find('button[data-action=getSourceClose]'),
		import : $form.find('button[data-action=getSourceImport], button[data-action=getSourceReplace]')
	};
	var $textarea = $form.find('textarea');


	// open getSource form
	var open = function()
	{
		$toggleButton.addClass('col-key');
		$form.addClass('on');
	};

	// close getSource form
	var close = function()
	{
		$toggleButton.removeClass('col-key');
		$form.removeClass('on');
		$textarea.val('{}');
	};

	var stringToJSON = function(str)
	{
		var result;
		try {
			result = JSON.parse(decodeURIComponent(str.replace(/\+/g, '%20')));
		}
		catch(e) {
			result = null;
		}
		return result;
	};


	$toggleButton.on('click', function(){
		if ($(this).hasClass('col-key'))
		{
			close();
		}
		else
		{
			open();
		}
	});

	$formButtons.close.on('click', function(){
		close();
	});

	$formButtons.import.on('click', function(){
		var json = stringToJSON($textarea.val());
		var action = $(this).attr('data-action');
		if (json)
		{
			switch(action)
			{
				case 'getSourceImport':
					editor.import(json);
					break;
				case 'getSourceReplace':
					editor.replace(json);
					break;
			}
			close();
		}
		else
		{
			alert('Fail convert string to json');
			$textarea.focus();
		}
	});
};

jQuery(function($){
	var $jsonEditor = $('#JSONEditor');
	var $form = $('#regsterForm');
	var jsonData = $form.get(0).json.value;
	var jsonEditor = new JSONEditor($jsonEditor);

	// import json
	var json;
	try {
		json = JSON.parse(decodeURIComponent(jsonData.replace(/\+/g, '%20')));
	}
	catch(e) {
		json = {};
	}
	jsonEditor.replace(json);

	// submit form
	$form.on('submit', function(){
		var str = encodeURIComponent(jsonEditor.export(false));
		$(this).find('input[name=json]').val(str);
	});

	// value validate
	$form.validate({
		ignore: '[contenteditable]',
		rules : {
			name : {required : true, minlength : 3}
		}
	});

	// set get source
	$('#getSource').getSource(jsonEditor);
});