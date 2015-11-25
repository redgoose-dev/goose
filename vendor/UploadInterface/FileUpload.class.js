/**
 * File upload class
 *
 * @author : redgoose
 * @param {UploadInterface} parent
 * @param {String} action 파일처리 백엔드 url
 * @param {Object} queue
 * @param {File} file
 * @param {String} uploadPath 파일업로드 경로
 * @return void
 */
var FileUpload = function(parent, action, queue, file, uploadPath)
{
	var xhr = new XMLHttpRequest();

	if (typeof FormData === 'function' || typeof FormData === 'object')
	{
		var formData = new FormData();
		formData.append('file', file)
		formData.append('upload_loc', uploadPath);
		formData.append('table', 'file_tmp');
		formData.append('sess_id', (userData.sess_id) ? userData.sess_id : '');

		xhr.open('post', action, true);
		xhr.upload.addEventListener('progress', function(e){
			if (e.lengthComputable)
			{
				parent.uploadProgress(e.loaded, e.total, queue);
			}
		}, false);
		xhr.addEventListener('load', function(e){
			uploadSuccess(e.target, queue);
		});
		xhr.send(formData);
	}
	else
	{
		alert('not support browser');
	}

	/**
	 * upload success callback
	 *
	 * @Param {XMLHttpRequestProgressEvent} e
	 * @Param {Object} queue
	 * @return void
	 */
	var uploadSuccess = function(e, queue)
	{
		if (e.readyState == 4)
		{
			switch (e.status)
			{
				case 200:
					if (e.responseText !== 'Invalid file type.') {
						parent.uploadComplete(e.responseText, queue);
					}
					else
					{
						parent.uploadError(e.responseText, queue);
					}
					break;
				case 404:
					parent.uploadError('404 - File not found', queue);
					break;
				case 403:
					parent.uploadError('403 - Forbidden file type', queue);
					break;
				default:
					parent.uploadError('Unknown Error', queue);
					break;
			}
		}
	}
};