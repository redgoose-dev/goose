var FilesQueue = function(getParent, $el, options) {

	var self = this;
	var parent = getParent;

	this.$index = $el.children('ul');
	this.index = new Object();
	this.count = 0;
	this.active = new Array();


	/**
	 * template
	 * 
	 * @param {String} key
	 * @param {String} filename
	 * @param {String} status
	 * @param {String} src
	 * @param {String} formData
	 * @param {String} type
	 * @return {object} queue element
	 */
	this.template = function(key, filename, status, src, formData, type)
	{
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
		if (status == 'ready')
		{
			item += '<div class="progress">';
			item += '<p class="graph"><span></span></p>';
			item += '</div>';
		}
		item += '<figure>';
		item += (src) ? '<img src="' + parent.settings.fileDir + src + '" alt="' + filename + '" />' : '';
		item += '</figure>';
		item += '<div class="body">';
		item += '<p class="title">';
		item += '<strong class="name">' + filename + '</strong>';
		item += (status == 'ready') ? '<span class="size">0%</span>' : '';
		item += '<span class="status">' + status + '</span>';
		item += '</p>';
		item += (formData) ? form(formData) : '';
		item += '</div>';
		item += '<nav>';
		item += (/^image/i.test(type)) ? '\t\t<button type="button" rg-action="thumnail" class="icn-thumnail" title="썸네일이미지"></button>\n' : '';
		item += '\t\t<button type="button" rg-action="delete" class="icn-close" title="삭제"></button>\n';
		item += '</nav>';
		item += '</div>';
		item += '</li>';

		return $(item);
	}

	/**
	 * queue event init
	 * 
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

		obj.find('[contenteditable]').on('click', function(e){
			e.stopPropagation();
		});

		// delete queue
		obj.find('nav > button').on('click', function(e){
			e.stopPropagation();
			switch($(this).attr('rg-action'))
			{
				case 'delete':
					if (confirm('파일을 삭제하시겠습니까?'))
					{
						self.removeQueue(obj.closest('li'));
					}
					break;
				case 'thumnail':
					var queue = parent.queue.index[$(this).closest('li').attr('key')];
					if (/^image/i.test(queue.filetype))
					{
						parent.thumnail.open(queue);
					}
					break;
			}
		});
	}

	/**
	 * create queue
	 * 
	 * @author : redgoose
	 * @param {Object} file
	 * @return {String}
	 */
	this.createQueue = function(file)
	{
		var idx = self.count;
		var key = 'queue-' + idx;
		var status = (file.status) ? file.status : 'ready';
		var form = parent.settings.queueForm;

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
			,status
			,(/^image/i.test(file.type)) ? file.loc : null
			,form
			,file.type
		);

		self.index[key] = {
			filename : file.name
			,filesize : file.size
			,filetype : file.type
			,status : status
			,element : $dom
			,location : file.loc
			,type : file.type2
			,srl : file.srl
		};

		// insert queue
		$el.children('ul').append($dom);

		// event init
		queueEventInit($dom);

		self.count++;
		return key;
	}

	/**
	 * select queue
	 * 
	 * @author : redgoose
	 * @param {} : ...
	 * @return void
	 */
	this.selectQueue = function(queue)
	{
		if (queue.element.hasClass('on'))
		{
			queue.element.removeClass('on');
		}
		else
		{
			if (!parent.key)
			{
				self.$index.children().removeClass('on');
			}
			queue.element.addClass('on');
		}
	}

	/**
	 * select all queue
	 * 
	 * @return void
	 */
	this.selectAllQueue = function()
	{
		if (self.$index.children('li').hasClass('on'))
		{
			self.$index.children('li').removeClass('on');
		}
		else
		{
			self.$index.children('li').addClass('on');
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
			var item = self.getIndexItem($(this).attr('key'));
			return item.type + ':' + item.srl;
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
						$queue.each(function(){
							delete self.index[$(this).attr('key')];
						});
						$queue.fadeOut(400, function(){
							$(this).remove();
						});
						parent.refreshAddQueue();
					}
				})
			;
		}
	}

	/**
	 * get index item
	 * 
	 * @param {String} key
	 * @return {Object}
	 */
	this.getIndexItem = function(key)
	{
		return self.index[key];
	}

	/**
	 * get items
	 * 
	 * @return {Array}
	 */
	this.getItems = function()
	{
		return self.$index.children('li.on').map(function(o){
			return self.index[$(this).attr('key')];
		});
	}

	/**
	 * get thumnail item
	 * 
	 * @return {Array}
	 */
	this.getThumnailItem = function()
	{
		return self.index[self.$index.children('li.thumnail').attr('key')];
	}

	/**
	 * remove thumnail class
	 */
	this.updateThumnailClass = function($item)
	{
		self.$index.children('li.thumnail').removeClass('thumnail');
		$item.addClass('thumnail');
	}

}

/*
// 참고 - uploadInterface에 있는 썸네일 이미지 만들기 메서드
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
		self.activeThumnailSize = self.thumnail.settings.size.map(function(n){
			return n;
		}).join('*');
	}
}
*/