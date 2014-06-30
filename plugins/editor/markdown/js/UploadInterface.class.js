var UploadInterface = function(el, options) {

	var self = this;
	var $el = $(el);

	self.el = el;
	self.settings = $.extend({}, this.defaults, options);
	self.queue = new FilesQueue(this, this.settings.$queue, {});

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
			self.settings.$controller.children('button').on('click', function(e){
				switch($(this).attr('rg-action'))
				{
					// insert content
					case 'insertContents':
						log('본문삽입');
						break;

					// use thumnail
					case 'useThumnail':
						log('썸네일 설정');
						break;

					// select all items
					case 'selectAll':
						self.queue.selectAllQueue();
						break;

					// delete item
					case 'deleteSelect':
						if (confirm('선택한 파일을 삭제하시겠습니까?'))
						{
							var $lis = self.settings.$queue.find('>ul>li.on');
							if ($lis.length)
							{
								self.queue.removeQueue($lis);
							}
							else
							{
								alert('선택한 파일이 없습니다.');
							}
						}
						break;

					// delete all item
					case 'deleteAll':
						if (confirm('모두 삭제하시겠습니까?'))
						{
							var $lis = self.settings.$queue.find('>ul>li');
							if ($lis.length)
							{
								self.queue.removeQueue($lis);
							}
							else
							{
								alert('업로드 되어있는 파일이 없습니다.');
							}
						}
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
		var key = self.queue.createQueue(file);
		var fileUpload = new FileUpload(
			self
			,self.settings.uploadAction
			,self.queue.index[key]
			,file
		);
	}

	/**
	 * byte to size convert
	 * 
	 * @param {Number} bytes
	 * @return {String}
	 */
	var bytesToSize = function(bytes)
	{
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		if (bytes == 0) return '0 Byte';
		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
		return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
	};


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

	/**
	 * upload progress
	 * 
	 * @param {Number} loaded
	 * @param {Number} total
	 * @param {Object} queue
	 * @return void
	 */
	this.uploadProgress = function(loaded, total, queue)
	{
		var percent = parseInt(loaded / total * 100);
		queue.status = 'loading';
		queue.element.find('div.body > span.size').text(percent + '%');
		queue.element.find('div.body > span.status').text('Loading');
		queue.element.find('div.progress span').width(percent + '%');
	}

	/**
	 * upload complete
	 * 
	 * @param {String} response
	 * @param {Object} queue
	 * @return void
	 */
	this.uploadComplete = function(response, queue)
	{
		var data = JSON.parse(response);
		queue.status = 'complete';
		queue.element.find('div.body > span.size').text(bytesToSize(queue.size));
		queue.element.find('div.body > span.status').text('Complete');
		queue.element.find('div.progress').delay(200).fadeOut(400);
		queue.element.attr({
			'data-loc' : data.filelink
			,'data-srl' : data.sess_srl
			,'data-name' : data.filename
		});
	}

	/**
	 * upload error
	 * 
	 * @return void
	 */
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
	,uploadAction : null
	,removeAction : null
};
