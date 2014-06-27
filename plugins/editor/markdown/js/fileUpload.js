function log(o){console.log(o);}

var FilesUpload = function(el, options)
{
	var
		self = this
		,$el = $(el)
	;

	self.el = el;
	self.settings = $.extend({}, this.defaults, options);

	/**
	 * Initializes
	 */
	var init = function()
	{
		buttonEvent();
	}

	/**
	 * Buttons Event
	 */
	var buttonEvent = function()
	{
		if (!self.settings.autoUpload && self.settings.uploadButton)
		{
			self.settings.uploadButton.on('click', function(){
				upload();
			});
		}

		if (self.settings.queueController)
		{
			self.settings.queueController.children('button').on('click', function(){
				switch($(this).attr('rg-action'))
				{
					// insert content
					case 'insertContents':
						log('본문삽입');
						break;

					// select all items
					case 'selectAll':
						log('전체선택');
						break;

					// delete item
					case 'deleteSelect':
						log('선택삭제');
						break;

					// delete all item
					case 'deleteAll':
						log('모두삭제');
						break;
				}
			});
		}
	}

	/**
	 * create dummy form
	 * 
	 * @return Boolean : 더미폼 중복id가 존재하면 만들지않고 false를 리턴한다.
	 */
	var createDummyForm = function()
	{
		if ($('form#dummyForm').length)
		{
			return false;
		}

		var form = $('<form/>');
		form
			.attr({
				'id' : 'dummyForm'
				,'method' : 'post'
				,'enctype' : 'multipart/form-data'
				,'action' : self.settings.action
			})
			.ajaxForm({
				beforeSubmit : uploadReady
				,success : uploadSuccess
			})
			.hide()
		;

		$('body').append(form);
		self.$form = form;

		return true;
	}

	/**
	 * create dummy form
	 */
	var removeDummyForm = function()
	{
		self.$form.remove();
		self.$form = null;
	}

	/**
	 * file upload
	 * 
	 * 외부 더미폼을 만들고, jquery.form 플러그인을 이용하여 파일을 업로드한다.
	 * 
	 * @return Boolean : 
	*/
	var upload = function()
	{
		if (!createDummyForm())
		{
			error('중복되는 dummyForm이 존재합니다.');
			return false;
		}

		self.$form.submit();
	}

	/**
	 * file upload ready callback
	 */
	var uploadReady = function(formData, jqForm, options){
		// input[type=file] 데이터를 다른폼에 옮기는 방법을 찾기
		log($el.val());
		log(self.$form);
		log('upload ready');
	}

	/**
	 * file upload success callback
	 */
	var uploadSuccess = function(response, status, xhr, $form){
		//log(response);

		removeDummyForm();
	}

	/**
	 * error
	 * 
	 * @param String msg : 메세지
	 */
	var error = function(msg)
	{
		alert(msg);
	}

	// act
	init();
}


/**
 * Default variables
 */
FilesUpload.prototype.defaults = {
	foo : 'bar'
	,action : null
};
