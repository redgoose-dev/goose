var FilesQueue = function(getParent, $el, options) {

	var self = this;
	var parent = getParent;
	var $preview = $el.children('figure.thumnail');
	var $index = $el.children('ul');

	this.index = new Object();
	this.count = 0;
	this.active = null;


	/**
	 * init
	 * 
	 * @author : redgoose
	 * @param : ...
	 * @return void
	 */
	var init = function()
	{
		
	}

	/**
	 * template
	 * 
	 * @author : redgoose
	 * @param {String} key
	 * @param {String} filename
	 * @return {DOM} : queue element
	 */
	var template = function(key, filename)
	{
		var item = '<li key="' + key + '">\n';
		item += '\t<div class="body">\n';
		item += '\t\t<span class="name">' + filename + '</span>\n';
		item += '\t\t<span class="size">0%</span>\n';
		item += '\t\t<span class="status">Ready</span>\n';
		item += '\t</div>\n';
		item += '\t<div class="progress">\n';
		item += '\t\t<p class="graph"><span></span></p>\n';
		item += '\t</div>\n';
		item += '\t<nav>\n';
		item += '\t\t<button type="button" rg-action="delete" class="sp-ico">삭제</button>\n';
		item += '\t</nav>\n';
		item += '</li>';

		return $(item);
	}

	/**
	 * queue event init
	 * 
	 * @author : redgoose
	 * @param {DOM} obj
	 * @return void
	 */
	var queueEventInit = function(obj)
	{
		// select queue
		obj.on('click', function(e){
			var filetype = getFileType($(this).attr('data-name'));
			$(this).parent().children().removeClass('on');
			$(this).addClass('on');
			$preview.html('<img src="' + $(this).attr('data-loc') + '" alt="" />');
			self.active = $(this);
		});

		// delete queue
		obj.find('nav > button').on('click', function(e){
			e.stopPropagation();
			if (confirm('파일을 삭제하시겠습니까?'))
			{
				self.removeQueue(obj.closest('li'));
			}
		});
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
	 * get file type
	 * 
	 * @param {} : ...
	 * @return void
	 */
	var getFileType = function(filename)
	{
		log(filename)
	}


	/**
	 * create queue
	 * 
	 * @author : redgoose
	 * @param {} : ...
	 * @return void
	 */
	this.createQueue = function(file)
	{
		var idx = self.count;
		var key = 'queue-' + idx;
		var $dom = template(key, file.name);

		self.index[key] = {
			name : file.name
			,size : file.size
			,type : file.type
			,status : 'ready'
			,element : $dom
		};

		// insert queue
		$el.children('ul').append($dom);

		// event init
		queueEventInit($dom);

		self.count++;
		return key;
	}

	/**
	 * remove queue
	 * 
	 * @author : redgoose
	 * @param {DOM} $queue
	 * @return void
	 */
	this.removeQueue = function($queue)
	{
		var action = parent.settings.removeAction;
		var srls = $queue.map(function(){
			return $(this).attr('data-type') + ':' + $(this).attr('data-srl');
		}).get().join(',');

		if (action)
		{
			var ajax = $.ajax({
				url : action
				,type : 'post'
				,dataType : 'json'
				,data : {
					data : srls
				}
			});

			ajax
				.fail(function(o){
					log('AJAX ERROR');
					if (o.error)
					{
						o.error(o.statusText);
					}
				})
				.done(function(o){
					if (o.status == 'success')
					{
						$queue.fadeOut(400, function(){
							$(this).remove();
						});
					}
				})
			;
		}
	}

	/**
	 * select all queue
	 * 
	 * @return void
	 */
	this.selectAllQueue = function()
	{
		$index.children('li').addClass('on');
	}

	/**
	 * enter infomation in the queue
	 * 
	 * @author : redgoose
	 * @param {DOM} $queue
	 * @param {Object} opt
	 * @return void
	 */
	this.inputInfomation = function($queue, opt)
	{
		$queue.find('div.body > span.size').text(bytesToSize(opt.size));
		$queue.find('div.body > span.status').text(opt.status);
		$queue.attr({
			'data-loc' : opt.dataLoc
			,'data-srl' : opt.dataSrl
			,'data-name' : opt.dataName
			,'data-type' : opt.dataType
		});
		if (opt.status == 'complete')
		{
			$queue.find('div.progress').delay(200).fadeOut(400);
		}
	}


	/**
	 * clear preview
	 * 프리뷰의 내용을 삭제한다.
	 * 
	 * @return void
	 */
	this.clearPreview = function()
	{
		$preview.html('');
	}

	// act
	init();

}