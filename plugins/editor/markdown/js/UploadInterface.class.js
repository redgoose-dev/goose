var UploadInterface = function(el, options) {
	var
		self = this
		,$el = $(el)
	;

	self.el = el;
	self.settings = $.extend({}, this.defaults, options);
	self.queue = new FilesQueue(self.settings.$queue, {});

	/**
	 * Initializes
	 */
	var init = function()
	{
		events();
	}

	/**
	 * Events init
	 * 
	 * @return void
	 */
	var events = function()
	{
		// controller
		if (self.settings.$controller)
		{
			self.settings.$controller.children('button').on('click', function(){
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

		// file input change
		if (self.settings.auto)
		{
			$el.on('change', function(){
				self.upload();
			});
		}
	}

	/**
	 * add queue item
	 * 
	 * @author : redgoose
	 * @param {File} file : 파일하나
	 * @return void
	 */
	var addQueueItem = function(file)
	{
		var
			idx = self.queue.createQueue(file)
			,fileUpload = new FileUpload(self, self.settings.action, self.queue.index[idx], file)
		;
	}

	/**
	 * file upload method
	 * 
	 * @return Boolean
	*/
	this.upload = function()
	{
		// limit item check

		var files = $el.get(0).files;
		for (var n = 0; n < files.length; n++)
		{
			addQueueItem(files[n]);
		}
	}

	this.uploadProgress = function(loaded, total, queue)
	{
		var percent = parseInt(loaded / total * 100);
		queue.status = 'loading';
		queue.element.find('div.body > span.size').text(percent + '%');
		queue.element.find('div.body > span.status').text('Loading');
		queue.element.find('div.progress span').width(percent + '%');
	}

	this.uploadComplete = function(response, queue)
	{
		queue.status = 'complete';
		queue.element.find('div.body > span.size').text(queue.size + 'kb');
		queue.element.find('div.body > span.status').text('Complete');
		queue.element.find('div.progress').delay(200).fadeOut(400);
	}

	this.uploadError = function(message, queue)
	{
		log(message);
	}

	// act
	init();
}


/**
 * Default variables
 */
UploadInterface.prototype.defaults = {
	foo : 'bar'
	,action : null
};
