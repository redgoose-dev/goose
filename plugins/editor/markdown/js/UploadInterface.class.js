var UploadInterface = function(el, options) {

	var self = this;
	var $el = $(el);

	self.el = el;
	self.settings = $.extend({}, this.defaults, options);
	self.queue = new FilesQueue(this, this.settings.$queue, {});
	self.key = false;

	var thumnail = new Thumnail(self, {
		type : self.settings.thumnailType
		,size : self.settings.thumnailSize.split('*')
		,quality : 0.8
	});

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
	}

	/**
	 * get cursor position
	 * 
	 * @param {DOM} $el
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

	/**
	 * Events init
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
						self.insertContent();
						break;

					// use thumnail
					case 'useThumnail':
						self.createThumnail();
						break;

					// select all items
					case 'selectAll':
						self.selectAllQueue();
						break;

					// delete item
					case 'deleteSelect':
						self.deleteQueue();
						break;

					// delete all item
					case 'deleteAll':
						self.deleteAllQueue();
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

		// keyboard event
		$(window).on('keydown', function(e){
			if (e.which == 91)
			{
				self.key = true;
			}
		});
		$(window).on('keyup', function(e){
			self.key = false;
		});

		// drop files event
		if (self.settings.$drop)
		{
			self.settings.$drop.on('dragover', false);
			self.settings.$drop.on('dragenter', function(e){
				e.preventDefault();
				e.stopPropagation();
				$(this).addClass('drag')
			});
			self.settings.$drop.on('dragleave', function(e){
				e.preventDefault();
				e.stopPropagation();
				$(this).removeClass('drag')
			});
			self.settings.$drop.on('drop', function(e){
				if (e.originalEvent.dataTransfer)
				{
					if (e.originalEvent.dataTransfer.files.length)
					{
						e.preventDefault();
						e.stopPropagation();
						$(this).removeClass('drag')
						self.upload(e.originalEvent.dataTransfer.files);
					}
				}
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
	 * reset file input
	 */
	var resetInput = function()
	{
		$el.replaceWith( $el = $el.clone( true ) );
	}

	/**
	 * get file type
	 * 
	 * @param {String} filename
	 * @return {String}
	 */
	var getFileType = function(filename)
	{
		var type = (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename)[0] : undefined;

		if (/(\.jpg|\.jpeg|\.bmp|\.gif|\.png)$/i.test(filename))
		{
			return 'image/' + type;
		}
		else if (/mp4|mov/gi.test(filename))
		{
			return 'video/' + type;
		}
		else if (/m4a|mp3/gi.test(filename))
		{
			return 'audio/' + type;
		}
		else if (/txt/gi.test(filename))
		{
			return 'text/' + type;
		}
		else if (/pdf/gi.test(filename))
		{
			return 'application/' + type;
		}
		else
		{
			return null;
		}
	}

	/**
	 * get object key
	 * 
	 * @param {Object} obj
	 * @return {Array}
	 */
	var getObjectKey = function(obj)
	{
		var arr = new Array();
		for (var key in obj)
		{
			arr.push(key);
		}
		return arr;
	}


	/**
	 * file upload method
	 * 
	 * @param {File} getFiles
	 */
	this.upload = function(getFiles)
	{
		var files = (getFiles) ? getFiles : $el.get(0).files;
		var count = Object.keys(self.queue.index).length + files.length;

		if (count > self.settings.limit)
		{
			alert('파일은 총 ' + self.settings.limit + '개까지 업로드할 수 있습니다.');
		}
		else
		{
			for (var n = 0; n < files.length; n++)
			{
				addQueueItem(files[n]);
			}
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
		queue.srl = data.sess_srl;
		queue.location = data.loc;
		queue.type = 'session';

		// edit queue
		queue.element.find('div.body > span.size').text(bytesToSize(queue.filesize));
		queue.element.find('div.body > span.status').text(queue.status);
		if (queue.status == 'complete')
		{
			queue.element.find('div.progress').delay(200).fadeOut(400);
		}

		// reset file input
		resetInput();

		// addQueue 갱신
		self.refreshAddQueue();
		
	}

	/**
	 * upload error
	 */
	this.uploadError = function(message, queue)
	{
		log(message);
	}

	/**
	 * push queue
	 * 
	 * @param {Array} data
	 * @return void
	 */
	this.pushQueue = function(data)
	{
		for (var n = 0; n < data.length; n++)
		{
			var key = self.queue.createQueue({
				name : data[n].filename
				,size : null
				,type : getFileType(data[n].filename)
				,loc : data[n].location
				,srl : data[n].srl
				,type2 : data[n].type
				,status : data[n].status
			});
			if (self.settings.form.thumnail_srl.value == data[n].srl)
			{
				self.queue.index[key].element.addClass('thumnail');
			}
		}
		if (data.length)
		{
			if (data[0].type == 'session')
			{
				self.refreshAddQueue();
			}
		}
	}

	/**
	 * insert content
	 */
	this.insertContent = function()
	{
		var keyword = '';
		var items = self.queue.getItems();

		for (var i=0; i<items.length; i++)
		{
			keyword += '<img src="' + self.settings.fileDir + items[i].location + '" alt="" />\n';
		}

		if (keyword)
		{
			var
				$content = self.settings.content
				,position = getCursorPosition($content)
				,content = $content.val()
				,newContent = content.substr(0, position) + keyword + content.substr(position)
			;
			$content.val(newContent);
		}
	}

	/**
	 * create thumnail
	 */
	this.createThumnail = function()
	{
		var item = self.queue.getItems()[0];
		if (!item)
		{
			item = self.queue.getThumnailItem();
		}
		if (!item)
		{
			item = self.queue.index[Object.keys(self.queue.index)[0]];
		}
		if (item)
		{
			if (/^image/i.test(item.filetype))
			{
				thumnail.open(item);
			}
		}
	}

	/**
	 * select all queue
	 */
	this.selectAllQueue = function()
	{
		self.queue.selectAllQueue();
	}

	/**
	 * delete select queue
	 */
	this.deleteQueue = function()
	{
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
	}

	/**
	 * delete all queue
	 */
	this.deleteAllQueue = function()
	{
		if (confirm('모두 삭제하시겠습니까?'))
		{
			var $lis = self.settings.$queue.find('>ul>li');
			if ($lis.length)
			{
				self.queue.removeQueue($lis);
				self.queue.clearPreview();
			}
			else
			{
				alert('업로드 되어있는 파일이 없습니다.');
			}
		}
	}

	/**
	 * refresh add queue
	 */
	this.refreshAddQueue = function()
	{
		var value = $.map(self.queue.index, function(obj, key){
			if (obj.type !== 'modify')
			{
				return obj.srl;
			}
		}).join(',');
		self.settings.form.addQueue.value = value;
	}

	// act
	events();
}


/**
 * Default variables
 */
UploadInterface.prototype.defaults = {
	uploadAction : null
	,removeAction : null
};
