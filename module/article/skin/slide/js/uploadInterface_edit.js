
/***********************************\
 * UploadInterface.class.js
\***********************************/

/**
 * files queue element
 */
UploadInterface.prototype.$queue = $('<div class="filesQueue"><ul></ul></div>');

/**
 * controller button event
 * 하단 버튼이벤트
 *
 * @return void
 */
UploadInterface.prototype.controllerButton = function()
{
	var self = this;
	var dom = '<button type="button" data-action="selectAll" class="gs-button size-small">모두선택</button>';
	dom += '<button type="button" data-action="deleteSelect" class="gs-button size-small">선택삭제</button>';
	dom += '<button type="button" data-action="deleteAll" class="gs-button size-small">모두삭제</button>';

	this.$controller
		.append($(dom))
		.children('button').on('click', function(){
			switch($(this).attr('data-action'))
			{
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
			$(this).addClass('drag');
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
					$(this).removeClass('drag');
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
		,dragEnd: function() {}
	});
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
		queue.state = 'complete';
		queue.srl = data.srl;
		queue.location = data.loc;
		queue.type = 'session';
		queue.filename = data.name;
		queue.filetype = data.type;

		// edit queue
		queue.element.find('.name').text(queue.filename);
		queue.element.find('.size').text(self.bytesToSize(queue.filesize));
		queue.element.find('.state').text(queue.state);

		// hide progress
		if (queue.state == 'complete')
		{
			queue.element.find('div.progress').delay(200).fadeOut(400);
		}

		// append image
		if (/^image/i.test(queue.filetype))
		{
			queue.element.find('figure').html('<img src="' + self.settings.fileDir + '/' + data.loc + '" alt="" />');
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
			,form : data[n].form
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
		}
		else
		{
			alert('업로드 되어있는 파일이 없습니다.');
		}
	}
};

/**
 * Export slide JSON data
 *
 * @return {String} str
 */
UploadInterface.prototype.exportSlideJSON = function()
{
	var self = this;
	var data = [];
	self.queue.$index.children().each(function(){
		var $key = $(this).attr('key');
		var queue = self.queue.index[$key];
		var item = {
			filename : queue.filename
			,location : queue.location
			,type : 'modify'
			,form : []
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
};



/***********************************\
 * FilesQueue.class.js
 \***********************************/

FilesQueue.prototype.$preview = null;
FilesQueue.prototype.clearPreview = null;

/**
 * template
 *
 * @author : redgoose
 * @param {String} key
 * @param {String} filename
 * @param {String} state
 * @param {String} type
 * @param {String} src
 * @param {Object} formData
 * @return {Object} : queue element
 */
FilesQueue.prototype.template = function(key, filename, state, type, src, formData)
{
	var self = this;

	function form(data)
	{
		var str = '';
		str += '<div class="form">';
		for (var i=0; i<data.length; i++)
		{
			str += '<p>';
			str += '<strong>' + data[i].label + '</strong>';
			str += '<span contenteditable="true" spellcheck="false" name="' + data[i].name + '">' + data[i].value + '</span>';
			str += '</p>';
		}
		str += '</div>';
		return str;
	}

	var item = '<li key="' + key + '">';
	item += '<div>';
	if (state == 'ready')
	{
		item += '<div class="progress">';
		item += '<p class="graph"><span></span></p>';
		item += '</div>';
	}
	item += '<figure>';
	item += (src) ? '<img src="' + self.parent.settings.fileDir + '/' + src + '" alt="' + filename + '" />' : '';
	item += '</figure>';
	item += '<div class="body">';
	item += '<p class="title">';
	item += '<strong class="name">' + filename + '</strong>';
	item += (state == 'ready') ? '<span class="size">0%</span>' : '';
	item += '<span class="state">' + state + '</span>';
	item += '</p>';
	item += (formData) ? form(formData) : '';
	item += '</div>';
	item += '<nav>';
	item += (/^image/i.test(type)) ? '\t\t<button type="button" data-action="thumnail" class="icn-thumnail" title="썸네일이미지"></button>\n' : '';
	item += '\t\t<button type="button" data-action="delete" class="icn-close" title="삭제"></button>\n';
	item += '</nav>';
	item += '</div>';
	item += '</li>';

	return $(item);
};

/**
 * queue event init
 *
 * @param {Object} obj
 * @return void
 */
FilesQueue.prototype.queueEventInit = function(obj)
{
	var self = this;

	// select queue
	obj.on('click', function(){
		var item = self.getIndexItem($(this).attr('key'));
		self.selectQueue(item);
	});

	obj.find('[contenteditable]').on('click', function(e){
		e.stopPropagation();
	});

	// delete queue
	obj.find('nav > button').on('click', function(e){
		e.stopPropagation();
		var $queue = $(this).closest('li');
		switch($(this).attr('data-action'))
		{
			case 'delete':
				if (confirm('파일을 삭제하시겠습니까?'))
				{
					self.removeQueue($queue);
				}
				break;
			case 'thumnail':
				var queue = self.parent.queue.index[$queue.attr('key')];
				if (/^image/i.test(queue.filetype))
				{
					self.parent.thumnail.open(queue);
				}
				break;
		}
	});
};

/**
 * create queue
 *
 * @param {Object} file
 * @return {String}
 */
FilesQueue.prototype.createQueue = function(file)
{
	var self = this;
	var idx = self.count;
	var key = 'queue-' + idx;
	var state = (file.state) ? file.state : 'ready';
	var form = self.parent.settings.queueForm;

	if (form)
	{
		if (file.form)
		{
			for (var n=0; n<file.form.length; n++)
			{
				if (form[n])
				{
					form[n].value = file.form[n].value;
				}
			}
		}
		else
		{
			for (var n=0; n<form.length; n++)
			{
				form[n].value = '';
			}
		}
	}
	var $dom = self.template(
		key
		,file.name
		,state
		,file.type
		,(/^image/i.test(file.type)) ? file.loc : null
		,form
	);

	self.index[key] = {
		filename : file.name
		,filesize : file.size
		,filetype : file.type
		,state : state
		,element : $dom
		,location : file.loc
		,type : file.type2
		,srl : file.srl
	};

	// insert queue
	this.$el.children('ul').append($dom);

	// event init
	self.queueEventInit($dom);

	self.count++;
	return key;
};

/**
 * select queue
 *
 * @author : redgoose
 * @param {Object} queue
 * @return void
 */
FilesQueue.prototype.selectQueue = function(queue)
{
	var self = this;

	if (queue.element.hasClass('on'))
	{
		queue.element.removeClass('on');
		self.clearPreview();
	}
	else
	{
		if (!self.parent.key)
		{
			self.$index.children().removeClass('on');
		}
		queue.element.addClass('on');
	}
};