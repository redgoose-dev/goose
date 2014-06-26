function log(o){console.log(o);}

var FilesUpload = function(el, options)
{
	var
		that = this
		,$el = $(el)
	;

	that.el = el;
	that.settings = $.extend({}, this.defaults, options);

	/**
	 * Initializes
	 */
	var init = function()
	{
		buttonEvent('upload');
	}

	/**
	 * Buttons Event
	 */
	var buttonEvent = function(role)
	{
		switch(role)
		{
			case 'upload':
				$el.find('[role=' + role + ']').on('click', function(){
					upload();
				});
				break;
			case 'aa':
				break;
		}
	}

	/**
	 * create dummy form
	 * 
	 * @return Boolean : 더미폼 중복id가 존재하면 만들지않고 false를 리턴한다.
	 */
	var createDummyForm = function()
	{
		if (!$('form#dummyForm').length)
		{
			var form = $('<form/>');
			form
				.attr({
					'id' : 'dummyForm'
					,'method' : 'post'
					,'enctype' : 'multipart/form-data'
					,'action' : that.settings.action
				})
				.hide()
			;
			$('body').append(form);
			that.$form = form;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * create dummy form
	 */
	var removeDummyForm = function()
	{
		that.$form.remove();
	}

	/**
	 * file upload
	 * 
	 * 외부 더미폼을 만들고 
	 * jquery.form 플러그인을 이용하여 파일을 업로드한다.
	*/
	var upload = function()
	{
		if (createDummyForm())
		{
			log('input 엘리먼트를 넣을 준비완료');
		}
		else
		{
			error('중복되는 dummyForm이 존재합니다.');
			return false;
		}
	}

	/**
	 * error
	 */
	var error = function(msg)
	{
		alert(msg);
	}

	init();
}


/**
 * Default variables
*/
FilesUpload.prototype.defaults = {
	foo : 'bar'
	,action : '/sdgksdgsgd/dfgsdg.php'
};
