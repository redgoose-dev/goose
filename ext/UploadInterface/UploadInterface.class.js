var UploadInterface = function(el, options) {

	var self = this;
	this.$el = $(el);

	this.el = el;
	this.settings = $.extend({}, this.defaults, options);
	this.key = false;
	this.queue = null;
	this.$drop = null;
	this.readyItem = [];

	/**
	 * get cursor position
	 *
	 * @param {Object} $el
	 * @return {Number}
	 */
	this.getCursorPosition = function($el)
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
	};

	/**
	 * byte to size convert
	 *
	 * @param {Number} bytes
	 * @return {String}
	 */
	this.bytesToSize = function(bytes)
	{
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		if (bytes == 0) return '0Byte';
		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
		return Math.round(bytes / Math.pow(1024, i), 2) + '' + sizes[i];
	};

	/**
	 * reset file input
	 */
	this.resetInput = function()
	{
		self.$el.replaceWith( self.$el = self.$el.clone( true ) );
	};


	// ACTION
	if (self.settings.$manager)
	{
		// init thumnail class
		self.thumnail = self.initThumnail();
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
		self.controllerButton();
		// init events
		self.events();
		// set ready
		self.ready = true;
	}

};


/**
 * Default variables
 */
UploadInterface.prototype.defaults = {
	uploadAction : null
	,removeAction : null
	,filesizeLimit : 5000000
};

/**
 * files queue element
 */
UploadInterface.prototype.$queue = $('<div class="filesQueue"><figure class="thumnail"></figure><ul></ul></div>');
UploadInterface.prototype.$controller = $('<nav id="queueController"></nav>');

/**
 * controller button event
 * 하단 버튼이벤트
 *
 * @return void
 */
UploadInterface.prototype.controllerButton = function()
{
	var self = this;
	var dom = '';
	dom += '<button type="button" data-action="insertContents" class="gs-button size-small col-key">본문삽입</button>';
	dom += '<button type="button" data-action="selectAll" class="gs-button size-small">모두선택</button>';
	dom += '<button type="button" data-action="deleteSelect" class="gs-button size-small">선택삭제</button>';
	dom += '<button type="button" data-action="deleteAll" class="gs-button size-small">모두삭제</button>';

	this.$controller
		.append($(dom))
		.children('button').on('click', function(){
			switch($(this).attr('data-action'))
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
};

/**
 * Events init
 */
UploadInterface.prototype.events = function()
{
	var self = this;

	// file input change
	if (self.settings.auto)
	{
		self.$el.on('change', function(){
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
	$(window).on('keyup', function(){
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
			$(this).removeClass('drag');
		});
		self.$drop.on('drop', function(e){
			var evt_dataTransfer = e.originalEvent.dataTransfer;
			if (evt_dataTransfer)
			{
				if (evt_dataTransfer.files.length)
				{
					e.preventDefault();
					e.stopPropagation();
					$(this).removeClass('drag');
					self.upload(evt_dataTransfer.files);
				}
			}
		});
	}
};

/**
 * File upload
 *
 * @Param {Object} item
 */
UploadInterface.prototype.fileUpload = function(item)
{
	var up = new FileUpload(
		this
		,this.settings.uploadAction
		,this.queue.index[item.key]
		,item.file
		,userData.originalPath
	);
};

/**
 * file upload method
 *
 * @param {File} getFiles
 */
UploadInterface.prototype.upload = function(getFiles)
{
	var self = this;

	if (self.$queue)
	{
		var files = (getFiles) ? getFiles : self.$el.get(0).files;
		var count = Object.keys(self.queue.index).length + files.length;
		var errorMsg = null;

		if (self.settings.limit && (count > self.settings.limit))
		{
			errorMsg = '파일은 총 ' + self.settings.limit + '개까지 업로드할 수 있습니다.';
		}
		else
		{
			for (var n = 0; n < files.length; n++)
			{
				if (files[n].size < this.settings.filesizeLimit)
				{
					self.readyItem.push({
						key : self.queue.createQueue(files[n])
						,file : files[n]
					});
				}
				else
				{
					errorMsg = '허용용량을 초과하는 파일이 있습니다.\n허용용량은 ' + self.bytesToSize(this.settings.filesizeLimit) + '까지입니다.';
				}
			}
			if (self.readyItem.length)
			{
				self.fileUpload(self.readyItem[0]);
			}
		}
	}
	else
	{
		errorMsg = 'not install queue manager';
	}

	if (errorMsg)
	{
		alert(errorMsg);
		return false;
	}
};

/**
 * upload progress
 *
 * @param {Number} loaded
 * @param {Number} total
 * @param {Object} queue
 * @return void
 */
UploadInterface.prototype.uploadProgress = function(loaded, total, queue)
{
	var percent = parseInt(loaded / total * 100);
	queue.state = 'loading';
	queue.element.find('span.size').text(percent + '%');
	queue.element.find('span.state').text('Loading');
	queue.element.find('div.progress span').width(percent + '%');
};

/**
 * upload complete
 *
 * @param {String} response
 * @param {Object} queue
 * @return void
 */
UploadInterface.prototype.uploadComplete = function(response, queue)
{
	var self = this;

	try {
		var data = JSON.parse(response)[0];

		if (data.state == 'error')
		{
			log(data);
			if (data.message)
			{
				alert(data.message);
			}
			queue.element.remove();
			return false;
		}

		queue.state = 'complete';
		queue.srl = data.srl;
		queue.location = data.loc;
		queue.type = 'session';
		queue.filename = data.name;
		queue.filetype = data.type;

		// edit queue
		queue.element.find('span.name').text(queue.filename);
		queue.element.find('span.size').text(self.bytesToSize(queue.filesize));
		queue.element.find('span.state').text(queue.state);

		// hide progress
		if (queue.state == 'complete')
		{
			queue.element.find('div.progress').delay(200).fadeOut(400);
		}

		// reset file input
		self.resetInput();

		// refresh addQueue
		self.refreshAddQueue();

		// upload next file
		self.readyItem.splice(0, 1);
		if (self.readyItem.length)
		{
			self.fileUpload(self.readyItem[0]);
		}
	} catch(e) {
		// error upload
		log(response);
		alert('ERROR UPLOAD');
		queue.element.remove();
	}
};

/**
 * push queue
 *
 * @param {Array} data
 * @return void
 */
UploadInterface.prototype.pushQueue = function(data)
{
	var self = this;

	for (var n = 0; n < data.length; n++)
	{
		var key = self.queue.createQueue({
			name : data[n].filename
			,size : data[n].filesize
			,type : data[n].filetype
			,loc : data[n].location
			,srl : data[n].srl
			,type2 : data[n].type
			,state : data[n].state
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
};

/**
 * insert content
 *
 * @param {Object} $queue
 * @return void
 */
UploadInterface.prototype.insertContent = function($queue)
{
	var self = this;

	var items = ($queue) ? new Array(self.queue.index[$queue.attr('key')]) : self.queue.getItems();
	if (self.settings.insertFunc)
	{
		var params = [];
		for (var i=0; i<items.length; i++)
		{
			params.push({
				url : self.settings.fileDir + '/' + items[i].location
				,type : items[i].filetype
				,name : items[i].filename
			});
		}
		self.settings.insertFunc(params);
	}
	else if (self.settings.$insertTarget)
	{
		var $content = self.settings.$insertTarget;
		var position = self.getCursorPosition($content);
		var content = $content.val();
		var keyword = '';
		for (var i=0; i<items.length; i++)
		{
			if (/^image/.test(items[i].filetype))
			{
				keyword += '<img src="' + self.settings.fileDir + '/' + items[i].location + '" alt="" />\n';
			}
			else
			{
				keyword += '<a href="' + self.settings.fileDir + '/' + items[i].location + '">' + items[i].filename + '</a>\n';
			}
		}
		$content.val(content.substr(0, position) + keyword + content.substr(position));
	}
};

/**
 * upload error
 */
UploadInterface.prototype.uploadError = function(message, queue)
{
	log(message);
};

/**
 * select all queue
 */
UploadInterface.prototype.selectAllQueue = function()
{
	this.queue.selectAllQueue();
};

/**
 * delete select queue
 */
UploadInterface.prototype.deleteQueue = function()
{
	var self = this;

	if (confirm('선택한 파일을 삭제하시겠습니까?'))
	{
		var $lis = self.$queue.find('> ul > li.on');
		if ($lis.length)
		{
			self.queue.removeQueue($lis);
		}
		else
		{
			alert('선택한 파일이 없습니다.');
		}
	}
};

/**
 * delete all queue
 */
UploadInterface.prototype.deleteAllQueue = function()
{
	var self = this;

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
};

/**
 * refresh add queue
 */
UploadInterface.prototype.refreshAddQueue = function()
{
	var self = this;

	var value = $.map(self.queue.index, function(obj, key){
		if (obj.type !== 'edit')
		{
			return obj.srl;
		}
	}).join(',');
	self.settings.form.addQueue.value = value;
};

/**
 * thumnail image exist check
 *
 * @return {Boolean} 첨부파일중에 이미지가 있으면 true를 반환
 */
UploadInterface.prototype.thumnailImageCheck = function()
{
	var self = this;
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
			self.queue.$index.find('[data-action=thumnail]').eq(0).focus();
			return true;
		}
		else
		{
			return false;
		}
	}
};

/**
 * init thumnail class
 *
 * @return {Thumnail}
 */
UploadInterface.prototype.initThumnail = function()
{
	var self = this;

	return new Thumnail(self, {
		type : self.settings.thumnail.type
		,size : self.settings.thumnail.size.split('*')
		,quality : 0.8
		,data : {
			srl : self.settings.thumnail.srl
			,coords : self.settings.thumnail.coords
			,url : self.settings.thumnail.url
		}
	});
};
