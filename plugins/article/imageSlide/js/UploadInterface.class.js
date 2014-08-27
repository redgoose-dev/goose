var UploadInterface = function(el, options) {

	var self = this;
	var $el = $(el);

	this.el = el;
	this.settings = $.extend({}, this.defaults, options);
	this.key = false;
	this.queue = null;
	this.$queue = null;
	this.$drop = null;
	this.$controller = null;
	this.json = new Object();
	this.readyItem = new Array();
	this.thumnail = new Thumnail(self, {
		type : self.settings.thumnailType
		,size : self.settings.thumnailSize.split('*')
		,quality : 0.8
	});

	/**
	 * init
	 */
	var init = function()
	{
		if (self.settings.$queue)
		{
			self.$queue = $('<div class="filesQueue"><ul></ul></div>');
			self.queue = new FilesQueue(self, self.$queue, {});
			self.$drop = self.$queue.children('ul');
			self.$controller = $('<nav id="queueController"></nav>');
			self.settings.$queue
				.append(self.$queue)
				.append(self.$controller)
			;

			createControllter(self.$controller);
			events();
		}
	}

	/**
	 * create controller
	 * 
	 * @param {DOM} $nav
	 */
	var createControllter = function($nav)
	{
		var $dom = '';
		$dom += '<button type="button" rg-action="useThumnail" class="ui-button btn-small btn-highlight">썸네일설정</button>';
		$dom += '<button type="button" rg-action="selectAll" class="ui-button btn-small">모두선택</button>';
		$dom += '<button type="button" rg-action="deleteSelect" class="ui-button btn-small">선택삭제</button>';
		$dom += '<button type="button" rg-action="deleteAll" class="ui-button btn-small">모두삭제</button>';
		$nav.append($($dom));
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
	 * Events init
	 */
	var events = function()
	{
		// controller
		if (self.$controller)
		{
			self.$controller.children('button').on('click', function(e){
				switch($(this).attr('rg-action'))
				{
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

		// Drag event
		self.queue.$index.dragsort({
			dragSelector : 'li'
			,dragSelectorExclude : 'span[contenteditable], button'
			,dragBetween: true
			,placeHolderTemplate: '<li class="placeHolder"><div></div></li>'
			,dragEnd: function() {
				
			}
		})
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
	var getFileType = function(filename)
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
				fileUpload(self.readyItem[0]);
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
		queue.element.find('span.size').text(bytesToSize(queue.filesize));
		queue.element.find('span.status').text(queue.status);

		// append image
		if (/^image/i.test(queue.filetype))
		{
			queue.element.find('figure').html('<img src="' + self.settings.fileDir + data.loc + '" alt="" />');
		}

		// hide progress
		queue.element.find('div.progress').delay(200).fadeOut(400);

		// reset file input
		resetInput();

		// addQueue 갱신
		self.refreshAddQueue();

		// 다음파일 업로드하기
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
	 * @param {Array} data : attach files data
	 * @param {Array} conData : contents data
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
				,form : data[n].form
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
	 * create thumnail
	 */
	this.createThumnail = function()
	{
		var item = null;

		// 선택된 큐중에 이미지인 큐가 나오면 item변수로 넣기
		var items = self.queue.getItems(); // 선택된 큐
		for (var i=0; i<items.length; i++)
		{
			if (/^image/i.test(items[i].filetype))
			{
				item = items[i];
				break;
			}
		}

		// 썸네일로 지정되어있는 큐라면 item변수에 넣기
		if (!item)
		{
			item = self.queue.getThumnailItem();
		}

		// 조건없이 순서대로 찾아서 이미지인 큐가 나오면 item변수로 넣기
		if (!item)
		{
			for (var key in self.queue.index)
			{
				if (/^image/i.test(self.queue.index[key].filetype))
				{
					item = self.queue.index[key];
					break;
				}
			}
		}
		if (item)
		{
			self.thumnail.open(item);
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
			var $lis = self.$queue.find('>ul>li');
			if ($lis.length)
			{
				self.queue.removeQueue($lis);
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
		if (!$items.filter('.thumnail').length)
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
				self.$controller.children('[rg-action=useThumnail]').focus();
				return true;
			}
		}
		return false;
	}

	/**
	 * Export JSON
	 * 
	 * @return {String} str
	 */
	this.exportJSON = function()
	{
		var data = new Array();
		self.queue.$index.children().each(function(){
			var queue = self.queue.index[$(this).attr('key')];
			var item = {
				filename : queue.filename
				,location : queue.location
				,status : 'uploaded'
				,type : 'modify'
				,form : new Array()
			};
			$(this).find('div.form > p').each(function(){
				var item2 = {
					key : $(this).children('strong').text()
					,value : $(this).children('span').text()
				};
				item.form.push(item2);
			});
			data.push(item);
		});
		return encodeURIComponent(JSON.stringify(data));
	}


	// act
	init();
}


/**
 * Default variables
 */
UploadInterface.prototype.defaults = {
	uploadAction : null
	,removeAction : null
};
