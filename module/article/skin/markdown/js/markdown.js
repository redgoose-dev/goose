jQuery(function($){

	var form = document.writeForm;
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : form
		,$manager : $('#queuesManager')
		,uploadAction : userData.root + 'file/upload/'
		,removeAction : userData.root + 'file/remove/'
		,fileDir : userData.url
		,auto : false
		,limit : 10
		,thumnail : userData.thumnail
		,$insertTarget : $('#content')
		,insertFunc : function(params){
			var str = '';
			for (var i=0; i<params.length; i++)
			{
				if (/^image/.test(params[i].type))
				{
					str += '![](' + params[i].url + ')\n';
				}
				else
				{
					str += '[' + params[i].name + '](' + params[i].url + ')\n';
				}
			}
			var $content = $(form.content);
			var position = getCursorPosition($content);
			var content = $content.val();
			var newContent = content.substr(0, position) + str + content.substr(position);
			$content.val(newContent);
		}
	});

	if (!uploadInterface.ready)
	{
		return false;
	}

	var attachFiles = userData.pushData;
	var attachFilesData = (attachFiles) ? JSON.parse(attachFiles) : null;
	if (attachFilesData)
	{
		uploadInterface.pushQueue(attachFilesData);
	}

	// upload button click event
	$('#fileUploadButton').on('click', function(){
		uploadInterface.upload();
	});

	// onsubmit event
	$(form).on('submit', function(){
		// check thumnail image
		if (uploadInterface.thumnailImageCheck())
		{
			return false;
		}

		// set json data
		var coords = uploadInterface.thumnail.data.coords;
		var json = {
			thumnail : {
				srl : uploadInterface.thumnail.data.srl
				,coords : (coords) ? coords.toString() : ''
				,url : uploadInterface.thumnail.data.url
			}
		};

		// set thumnail image
		if (uploadInterface.thumnail.data.image)
		{
			form.thumnail_image.value = uploadInterface.thumnail.data.image;
		}

		// json object to hidden string
		json = encodeURIComponent(JSON.stringify(json));
		form.json.value = json;
	});

	// toggle edit/preview
	var $mkEditor = $('div.mk-editor');
	var $mkEditorButtons = $mkEditor.find('a[data-control]');
	$mkEditorButtons.on('click', function(){

		var mode = $(this).attr('data-control');
		var $target = $mkEditor.find('[data-target=' + mode + ']');

		if (!$(this).hasClass('active'))
		{
			$mkEditorButtons.removeClass('active');
			$(this).addClass('active');
			$mkEditor.find('[data-target]').removeClass('show');
			$target.addClass('show');

			// load preview data
			if (mode == 'preview' && form.content.value)
			{
				var result = getPreviewData(function(result){
					$target.html(result);
				});
			}
		}

		return false;
	});

	/**
	 * get preview data
	 *
	 * @Param {Function} complete
	 * @Return void
	 */
	var getPreviewData = function(complete)
	{
		var req = $.ajax({
			url : userData.root + 'script/run/markdown_preview/'
			,type : 'post'
			,data : {
				title : '...'
				,nest_srl : form.nest_srl.value
				,content : form.content.value
			}
		});
		req.done(function(str){
			complete(str);
		});
	};


	/**
	 * get cursor position
	 *
	 * @param {object} $el
	 * @return {Number}
	 */
	var getCursorPosition = function($el)
	{
		var el = $el.get(0);
		var pos = 0;
		if ('selectionStart' in el)
		{
			pos = el.selectionStart;
		}
		else if ('selection' in document)
		{
			el.focus();
			var Sel = document.selection.createRange();
			var SelLength = document.selection.createRange().text.length;
			Sel.moveStart('character', -el.value.length);
			pos = Sel.text.length - SelLength;
		}
		return pos;
	}
});