var Thumnail = function(parent, options) {
	
	var
		self = this
		,windowName = 'thumnailWindow'
		,maxImageSize = 640
		,outputSize = null
	;

	this.settings = $.extend({}, this.defaults, options);
	this.queue = null;
	this.$window = null;
	this.data = {
		srl : null
		,location : null
		,coords : null
	};

	var init = function()
	{
		self.settings.ratio = getRatio(self.settings.size[0], self.settings.size[1]);
	}

	/**
	 * window template
	 * 
	 * @return {DOM}
	 */
	var template = function()
	{
		var str = '<div id="' + windowName + '">\n';
		str += '<div class="bg"></div>\n';
		str += '<div class="wrap">\n';
		str += '<figure></figure>\n';
		str += '<div>\n';
		str += '<p class="title">출력사이즈</p>\n';
		str += '<p class="text">가로:<em ar-text="width">0</em>px, 세로:<em ar-text="height">0</em>px</p>\n';
		str += '</div>\n';
		str += '<nav>\n';
		str += '<button type="button" rg-action="center" class="ui-button btn-highlight">중간으로</button>\n';
		str += '<button type="button" rg-action="close" class="ui-button">닫기</button>\n';
		str += '</nav>\n';
		str += '</div>\n';
		str += '</div>';
		return $(str);
	}

	/**
	 * get ratio
	 * 
	 * @param {Number} w
	 * @param {Number} h
	 * @return {Number}
	 */
	var getRatio = function(w, h)
	{
		var result = parseInt(w) / parseInt(h);
		result = Math.round(result * 1000) / 1000;
		return result;
	}



	/**
	 * open
	 * 
	 * @param {Object} item
	 * @return void
	 */
	this.open = function(item)
	{
		if (!self.$window)
		{
			self.queue = item;
			self.$window = template();

			var $figure = self.$window.find('figure');
			var $img = $('<img src="' + parent.settings.fileDir + item.location + '" />')

			$figure.append($img);
			$('body').append(self.$window);

			$img.get(0).onload = onloadPreviewImage;
		}
	}

	/**
	 * onload preview image
	 * 
	 * @param {} e
	 * @return void
	 */
	var onloadPreviewImage = function(e)
	{
		var $btnCenter = self.$window.find('button[rg-action=center]');
		var $btnClose = self.$window.find('button[rg-action=close], div.bg');


		$btnCenter.on('click', function(e){
			log($(this));
		});
		$btnClose.on('click', function(e){
			self.close();
		});

		self.getData(self.queue.srl, parent.settings.fileDir + self.queue.location, null);
		log(self.data);
	}

	/**
	 * close
	 * 
	 * @param {}
	 * @return void
	 */
	this.close = function()
	{
		self.$window.remove();
		self.$window = null;
	}

	/**
	 * get data
	 * 
	 * @param {}
	 * @return void
	 */
	this.getData = function(srl, location, coords)
	{
		self.data.srl = srl;
		self.data.location = location;
		self.data.coords = coords;
	}


	// init
	init();
}


/*
output post
["thumnail_srl"]=> string(0) ""
["thumnail_image"]=> string(0) ""
["thumnail_coords"]=> string(21) "45,39,570,564,525,525"
*/


// default variables
Thumnail.prototype.defaults = {
	type : 'crop'
	,ratio : 1
	,size : [200,200]
	,quality : 0.7
}