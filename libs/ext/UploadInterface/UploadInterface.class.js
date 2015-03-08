var UploadInterface = function(el, options) {

	var self = this;
	var $el = $(el);

	this.el = el;
	this.settings = $.extend({}, this.defaults, options);
	this.key = false;
	this.queue = null;
	this.$drop = null;
	this.$queue = $('<div class="filesQueue"><figure class="thumnail"></figure><ul></ul></div>');
	this.$controller = $('<nav id="queueController"></nav>');
	this.readyItem = new Array();
	this.thumnail = new Thumnail(self, {
		type : self.settings.thumnail.type
		,size : self.settings.thumnail.size.split('*')
		,quality : 0.8
		,data : {
			srl : self.settings.thumnail.srl
			,coords : self.settings.thumnail.coords
			,url : self.settings.thumnail.url
		}
	});

	/**
	 * controller button event
	 * 하단 버튼이벤트
	 *
	 * @return void
	 */
	var controllerButton = function()
	{
		var dom = '';
		dom += '<button type="button" rg-action="insertContents" class="ui-button btn-small btn-highlight">본문삽입</button>';
		dom += '<button type="button" rg-action="selectAll" class="ui-button btn-small">모두선택</button>';
		dom += '<button type="button" rg-action="deleteSelect" class="ui-button btn-small">선택삭제</button>';
		dom += '<button type="button" rg-action="deleteAll" class="ui-button btn-small">모두삭제</button>';

		self.$controller
			.append($(dom))
			.children('button').on('click', function(e){
				switch($(this).attr('rg-action'))
				{
					// insert content
					case 'insertContents':
						self.insertContent();
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
			})
		;
	}

	/**
	 * Events init
	 */
	var events = function()
	{
		// file input change
		if (self.settings.auto)
		{
			$el.on('change', function(){
				self.upload();
			});
		}

		// keyboard event
		$(window).on('keydown', function(e){
			if (e.which == 91 || e.which == 17)
			{
				self.key = true;
			}
		});
		$(window).on('keyup', function(e){
			self.key = false;
		});

		// drop files event
		if (self.$drop)
		{
			self.$drop.on('dragover', false);
			self.$drop.on('dragenter', function(e){
				e.preventDefault();
				e.stopPropagation();
				$(this).addClass('drag')
			});
			self.$drop.on('dragleave', function(e){
				e.preventDefault();
				e.stopPropagation();
				$(this).removeClass('drag')
			});
			self.$drop.on('drop', function(e){
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
	 * File upload
	 *
	 * @Param {Object} item
	 */
	var fileUpload = function(item)
	{
		var up = new FileUpload(
			self
			,self.settings.uploadAction
			,self.queue.index[item.key]
			,item.file
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
	this.getFileType = function(filename)
	{
		var type = (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename)[0] : undefined;

		if (/(\.jpg|\.jpeg|\.bmp|\.gif|\.png)$/i.test(filename))
		{
			return 'image/' + type;
		}
		else if (/(\.mp4|\.mov)/gi.test(filename))
		{
			return 'video/' + type;
		}
		else if (/(\.m4a|\.mp3)/gi.test(filename))
		{
			return 'audio/' + type;
		}
		else if (/(\.txt)/i.test(filename))
		{
			return 'text/' + type;
		}
		else if (/(\.pdf|\.zip|\.psd)/i.test(filename))
		{
			return 'application/' + type;
		}
		else
		{
			return null;
		}
	}

	/**
	 * file upload method
	 *
	 * @param {File} getFiles
	 */
	this.upload = function(getFiles)
	{
		if (self.$queue)
		{
			var files = (getFiles) ? getFiles : $el.get(0).files;
			var count = Object.keys(self.queue.index).length + files.length;

			if (self.settings.limit && (count > self.settings.limit))
			{
				alert('파일은 총 ' + self.settings.limit + '개까지 업로드할 수 있습니다.');
			}
			else
			{
				for (var n = 0; n < files.length; n++)
				{
					self.readyItem.push({
						key : self.queue.createQueue(files[n])
						,file : files[n]
					});
				}
				if (self.readyItem.length)
				{
					fileUpload(self.readyItem[0]);
				}
			}
		}
		else
		{
			alert('not install queue manager');
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
		queue.element.find('span.size').text(percent + '%');
		queue.element.find('span.status').text('Loading');
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
		queue.element.find('span.size').text(bytesToSize(queue.filesize));
		queue.element.find('span.status').text(queue.status);

		// hide progress
		if (queue.status == 'complete')
		{
			queue.element.find('div.progress').delay(200).fadeOut(400);
		}

		// reset file input
		resetInput();

		// refresh addQueue
		self.refreshAddQueue();

		// upload next file
		self.readyItem.splice(0, 1);
		if (self.readyItem.length)
		{
			fileUpload(self.readyItem[0]);
		}
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
				,type : self.getFileType(data[n].filename)
				,loc : data[n].location
				,srl : data[n].srl
				,type2 : data[n].type
				,status : data[n].status
			});
			if (self.thumnail.data.srl == data[n].srl)
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
	 * 
	 * @param {DOM} $queue
	 * @return void
	 */
	this.insertContent = function($queue)
	{
		var keyword = '';
		var urls = new Array();
		var items = ($queue) ? new Array(self.queue.index[$queue.attr('key')]) : self.queue.getItems();

		for (var i=0; i<items.length; i++)
		{
			keyword += '<img src="' + self.settings.fileDir + items[i].location + '" alt="" />\n';
			urls.push(self.settings.fileDir + items[i].location);
		}

		if (urls.length)
		{
			if (self.settings.insertFunc)
			{
				self.settings.insertFunc(urls);
			}
			else if (self.settings.$insertTarget)
			{
				var $content = self.settings.$insertTarget;
				var position = getCursorPosition($content);
				var content = $content.val();
				var newContent = content.substr(0, position) + keyword + content.substr(position);
				$content.val(newContent);
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
			var $lis = self.$queue.find('>ul>li.on');
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
			var $lis = self.$queue.find('> ul >li');
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

	/**
	 * thumnail image exist check
	 *
	 * @return {Boolean} : 첨부파일중에 이미지가 있으면 true를 반환
	 */
	this.thumnailImageCheck = function()
	{
		var $items = self.queue.$index.children();

		if ($items.filter('.thumnail').length)
		{
			return false;
		}
		else
		{
			var existImage = false;
			$items.each(function(){
				var index = self.queue.getIndexItem($(this).attr('key'));
				if (/^image/i.test(index.filetype))
				{
					existImage = true;
					return false;
				}
			});

			if (existImage && !self.settings.form.thumnail_image.value)
			{
				alert('썸네일 이미지를 만들지 않았습니다.');
				self.queue.$index.find('[rg-action=thumnail]').eq(0).focus();
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	// action
	if (self.settings.$manager)
	{
		// files queue
		self.queue = new FilesQueue(self, self.$queue, {});
		// set $drop
		self.$drop = self.$queue.children('ul');
		// append elements
		self.settings.$manager
			.append(self.$queue)
			.append(self.$controller)
		;
		// init controller buttons event
		controllerButton();
		// init events
		events();
		// set ready
		self.ready = true;
	}

}


/**
 * Default variables
 */
UploadInterface.prototype.defaults = {
	uploadAction : null
	,removeAction : null
};
