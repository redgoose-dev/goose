jQuery(function($){
	var form = document.writeForm;
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : form
		,$manager : $('#queuesManager')
		,uploadAction : userData.root + '/files/upload/'
		,removeAction : userData.root + '/files/remove/'
		,fileDir : userData.url + userData.originalPath
		,auto : false
		,limit : 5
		,thumnail : userData.thumnail
		,$insertTarget : $('#content')
		,insertFunc : null // function(value){}
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
	var $mkEditorButtons = $mkEditor.find('a[role-control]');
	$mkEditorButtons.on('click', function(){

		var mode = $(this).attr('role-control');
		var $target = $mkEditor.find('[role-target=' + mode + ']');

		if (!$(this).hasClass('active'))
		{
			$mkEditorButtons.removeClass('active');
			$(this).addClass('active');
			$mkEditor.find('[role-target]').removeClass('show');
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
	function getPreviewData(complete)
	{
		var req = $.ajax({
			url : userData.root + '/article/preview'
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
	}

});