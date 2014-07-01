var FilesQueue = function(getParent, $el, options) {

	var self = this;
	var parent = getParent;
	var $preview = $el.children('figure.thumnail');
	var $index = $el.children('ul');

	this.index = new Object();
	this.count = 0;
	this.active = new Array();


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
			var item = self.getIndexItem($(this).attr('key'));
			self.selectQueue(item);
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
			filename : file.name
			,filesize : file.size
			,filetype : file.type
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

	// select queue
	this.selectQueue = function(queue)
	{
		if (queue.element.hasClass('on'))
		{
			queue.element.removeClass('on');
			self.clearPreview();
		}
		else
		{
			$index.children().removeClass('on');
			queue.element.addClass('on');

			log(queue.filetype);
			if (queue.filetype == 'image/png' ||
				queue.filetype == 'image/jpg' ||
				queue.filetype == 'image/png')
			{
				$preview.html('<img src="' + queue.location + '" alt="" />');
			}
		}
	}

	/**
	 * select all queue
	 * 
	 * @return void
	 */
	this.selectAllQueue = function()
	{
		if ($index.children('li').hasClass('on'))
		{
			$index.children('li').removeClass('on');
		}
		else
		{
			$index.children('li').addClass('on');
		}
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
	 * clear preview
	 * 프리뷰의 내용을 삭제한다.
	 * 
	 * @return void
	 */
	this.clearPreview = function()
	{
		$preview.html('');
	}

	// get index item
	this.getIndexItem = function(key)
	{
		return self.index[key];
	}

	// act
	init();

}