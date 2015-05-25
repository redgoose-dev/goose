jQuery(function($){

	var form = document.writeForm;
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : form
		,$manager : $('#queuesManager')
		,uploadAction : userData.root + 'file/upload/'
		,removeAction : userData.root + 'file/remove/'
		,fileDir : userData.url
		,auto : false
		,limit : 30
		,thumnail : userData.thumnail
		,queueForm : [
			{ label : 'Subject', name : 'subject', value : '' }
		]
	});

	if (!uploadInterface.ready)
	{
		return false;
	}

	// push data
	var attachFiles = userData.pushData;
	attachFiles = (attachFiles) ? JSON.parse(attachFiles) : null;
	var contentData = $(form).find('input[name=content]').val();
	try {
		contentData = (contentData) ? JSON.parse(decodeURIComponent(contentData)) : null;
	} catch(e) {
		contentData = null;
	}
	if (attachFiles)
	{
		if (contentData)
		{
			// srl값이 일치하지 않아 srl값 매칭
			for (var n=0; n<contentData.length; n++)
			{
				for (var nn=0; nn<attachFiles.length; nn++)
				{
					if (contentData[n].location == attachFiles[nn].location)
					{
						contentData[n].srl = attachFiles[nn].srl;
						contentData[n].filesize = attachFiles[nn].filesize;
						contentData[n].filetype = attachFiles[nn].filetype;
						contentData[n].state = attachFiles[nn].state;
						break;
					}
				}
			}
			uploadInterface.pushQueue(contentData);
		}
		else
		{
			uploadInterface.pushQueue(attachFiles);
		}
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

		// set thumnail data
		var thumnailData = uploadInterface.thumnail.data;
		var json = {
			thumnail : {
				srl : thumnailData.srl
				,coords : (thumnailData.coords) ? thumnailData.coords.toString() : ''
				,url : thumnailData.url
			}
		};
		form.thumnail_image.value = (thumnailData.image) ? thumnailData.image : '';

		// push slide data to content
		form.content.value = uploadInterface.exportSlideJSON();

		json = encodeURIComponent(JSON.stringify(json));
		form.json.value = json;
	});
});