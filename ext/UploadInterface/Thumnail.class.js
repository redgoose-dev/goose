/**
 * Thumnail Class
 * create, modify thumnail image
 * 
 * @Param {UploadInterface} parent : UploadInterface class
 * @Param {Object} options : option parameter
 * @Return void
*/

var Thumnail = function(parent, options) {

	this.parent = parent;
	this.maxImageSize = 600;
	this.outputSize = null;
	this.ratio = null;
	this.windowName = 'thumnailWindow';
	this.settings = $.extend({}, this.defaults, options);
	this.queue = null;
	this.$window = null;

	this.setData(parent);
};


// default variables
Thumnail.prototype.defaults = {
	type : 'crop'
	,size : [200,200]
	,quality : 0.7
};

/**
 * set data
 *
 * @return {UploadInterface} parent
 */
Thumnail.prototype.setData = function(parent)
{
	// set thumnail data
	Thumnail.prototype.data = {
		srl : parent.settings.thumnail.srl
		,coords : (parent.settings.thumnail.coords) ? JSON.parse("[" + parent.settings.thumnail.coords + "]") : null
		,url : parent.settings.thumnail.url
		,location : null
	};
};

/**
 * window template
 *
 * @return {Object}
 */
Thumnail.prototype.template = function()
{
	var str = '<div id="' + this.windowName + '">\n';
	str += '<div class="bg"></div>\n';
	str += '<div class="wrap">\n';
	str += '<figure></figure>\n';
	str += '<dl>\n';
	str += '<dt>출력사이즈</dt>\n';
	str += '<dd>가로:<em data-text="width">0</em>px, 세로:<em data-text="height">0</em>px</dd>\n';
	str += '</dl>\n';
	str += '<nav>\n';
	str += '<button type="button" data-action="center" class="gs-button col-key">중간으로</button>\n';
	str += '<button type="button" data-action="close" class="gs-button">닫기</button>\n';
	str += '</nav>\n';
	str += '</div>\n';
	str += '</div>';
	return $(str);
};

/**
 * get image ratio
 *
 * @param {Object} $img
 * @param {String} type
 * @return {Number}
 */
Thumnail.prototype.getImageRatio = function($img, type)
{
	var self = this;
	var size = [];

	switch(type)
	{
		case 'resize':
			size = [$img.width(), $img.height()];
			break;
		case 'resizeWidth':
			size[0] = parseInt(self.settings.size[0]);
			size[1] = parseInt(($img.height() / $img.width()) * self.settings.size[0]);
			break;
		case 'resizeHeight':
			size[0] = parseInt(($img.width() / $img.height()) * self.settings.size[1]);
			size[1] = parseInt(self.settings.size[1]);
			break;
		default:
			size = self.settings.size;
			break;
	}
	return self.getRatio(size[0], size[1]);
};

/**
 * get output size
 *
 * @param {Object} $img
 * @param {String} type
 * @param {Array} size
 * @return {Array}
 */
Thumnail.prototype.getOutputSize = function($img, type, size)
{
	var w, h;
	switch(type)
	{
		case 'resize':
			if ($img.width() < $img.height())
			{
				w = parseInt(($img.width() / $img.height()) * size[1]);
				h = parseInt(size[1]);
			}
			else
			{
				w = parseInt(size[0]);
				h = parseInt(($img.height() / $img.width()) * size[0]);
			}
			break;
		case 'resizeWidth':
			w = parseInt(size[0]);
			h = parseInt(($img.height() / $img.width()) * size[0]);
			break;
		case 'resizeHeight':
			w = parseInt(($img.width() / $img.height()) * size[1]);
			h = parseInt(size[1]);
			break;
		default:
			w = parseInt(size[0]);
			h = parseInt(size[1]);
			break;
	}
	return [w, h];
};

/**
 * get ratio
 *
 * @param {Number} w
 * @param {Number} h
 * @return {Number}
 */
Thumnail.prototype.getRatio = function(w, h)
{
	var result = parseInt(w) / parseInt(h);
	result = Math.round(result * 1000) / 1000;
	return result;
};

/**
 * resize preview
 *
 * @param {Object} o
 * @param {Number} limit
 * @param {Number} size
 */
Thumnail.prototype.resizePreview = function(o, limit, size)
{
	if (o.width() > o.height())
	{
		o.width(o.width() * size);
		if (o.width() > limit)
		{
			o.width(limit);
		}
	}
	else
	{
		o.height(o.height() * size);
		if (o.height() > limit)
		{
			o.height(limit);
		}
	}
};

/**
 * get select coords
 *
 * @param {Object} $img
 * @param {String} type
 * @param {Array} coords
 * @return {Array}
 */
Thumnail.prototype.getSelectCoords = function($img, type, coords)
{
	if (coords)
	{
		return coords;
	}
	else
	{
		if (type == 'crop')
		{
			return ($img.width() < $img.height()) ? [0, 0, $img.width(), 0] : [0, 0, 0, $img.height()];
		}
		else
		{
			return [0, 0, $img.width(), $img.height()];
		}
	}
};

/**
 * onload preview image
 *
 * @param {Object} img
 * @return void
 */
Thumnail.prototype.onloadPreviewImage = function(img)
{
	var self = this;
	var $img = $(img);

	// 비율값 가져오기
	self.ratio = self.getImageRatio($img, self.settings.type);

	// 아웃풋 사이즈 가져오기
	self.outputSize = self.getOutputSize($img, self.settings.type, self.settings.size);

	// 윈도우 정보에 아웃풋 사이즈 입력
	self.$window.find('[data-text=width]').text(self.outputSize[0]);
	self.$window.find('[data-text=height]').text(self.outputSize[1]);

	// 프리뷰 이미지 적정크기로 줄이기
	self.resizePreview($img, self.maxImageSize, 0.7);

	// 좌표값 가져오기
	self.data.coords = self.getSelectCoords($img, self.settings.type, self.data.coords);

	// init Jcrop
	$img.Jcrop({
		bgOpacity : .4
		,setSelect : self.data.coords
		,aspectRatio : self.ratio
		,minSize : [50, 50]
		,onSelect : function(e) {
			self.data.coords = [
				parseInt(e.x)
				,parseInt(e.y) > 0 ? parseInt(e.y) : 0
				,parseInt(e.x2)
				,parseInt(e.y2)
				,parseInt(e.w)
				,parseInt(e.h)
			];
		}
	}, function(){
		var jcrop = this;
		var $btnCenter = self.$window.find('button[data-action=center]');
		var $btnClose = self.$window.find('button[data-action=close], div.bg');
		var $wrap = self.$window.find('div.wrap');

		$('body').addClass('thumnailWindowMode');
		$wrap.css({
			marginLeft : 0 - ($wrap.width() * 0.5) + 'px'
			/* ,marginTop : 0 - ($wrap.height() * 0.5) + 'px' */
		});

		$btnCenter.on('click', function(){
			var x, y;
			var src = jcrop.tellSelect();
			x = ($img.width() * 0.5) - (src.w * 0.5);
			y = ($img.height() * 0.5) - (src.h * 0.5);
			self.data.coords = [
				parseInt(x)
				,parseInt(y) > 0 ? parseInt(y) : 0
				,parseInt(x + src.w)
				,parseInt(y + src.h)
				,parseInt(src.w)
				,parseInt(src.h)
			];
			jcrop.animateTo(self.data.coords);
		});
		$btnClose.on('click', function(){
			self.close();
		});
	});
};

/**
 * open
 *
 * @param {Object} item
 * @return void
 */
Thumnail.prototype.open = function(item)
{
	var self = this;

	if (!self.$window)
	{
		self.queue = item;
		self.$window = self.template();

		if (item.srl !== self.data.srl)
		{
			self.data.coords = null;
		}

		self.data.srl = item.srl;
		self.data.location = self.parent.settings.fileDir + item.location;

		var $figure = self.$window.find('figure');
		var $img = $('<img src="' + self.data.location + '" />');
		$figure.append($img);
		$('body').append(self.$window);
		$img.get(0).onload = function(){
			self.onloadPreviewImage(this);
		};
	}
};

/**
 * close
 *
 * @return void
 */
Thumnail.prototype.close = function()
{
	var self = this;
	var thumnail_srl = self.parent.settings.thumnail.srl;
	var thumnail_coords = self.parent.settings.thumnail.coords;

	if (self.data.srl !== thumnail_srl || self.data.coords.toString() !== thumnail_coords)
	{
		self.data.image = self.getImageData(self.data.location);
		self.parent.queue.updateThumnailClass(self.queue.element);
	}

	$('body').removeClass('thumnailWindowMode');
	self.$window.remove();
	self.$window = null;
	self.queue = null;
};

/**
 * get image data
 * 이미지를 곧바로 축소를 하면 안티앨리어싱 문제 때문에 사선라인이 깨져보인다.
 * 그래서 결과물을 2배 사이즈로 키우고 그것을 1/2 사이즈로 줄이는 과정을 거치면 사선의 표면이 부드러워진다.
 *
 * @param {String} img_src
 * @return {String}
 */
Thumnail.prototype.getImageData = function(img_src)
{
	var self = this;
	var $img = self.$window.find('figure > img');

	var bigCanvas = document.createElement('canvas');
	var bigContext = bigCanvas.getContext('2d');
	var canvas = document.createElement('canvas');
	var context = canvas.getContext('2d');

	var coords = self.data.coords;
	var realSize = [$img.get(0).naturalWidth, $img.get(0).naturalHeight];
	var ratio = [realSize[0] / $img.width(), realSize[1] / $img.height()];

	// set big canvas size
	bigCanvas.width = self.outputSize[0] * 2;
	bigCanvas.height = self.outputSize[1] * 2;

	// draw big photo
	bigContext.drawImage(
		$img.get(0)
		,coords[0] * ratio[0] // x
		,coords[1] * ratio[1] // y
		,coords[4] * ratio[0] // x2
		,coords[5] * ratio[1] // y2
		,0 // dx
		,0 // dy
		,self.outputSize[0] * 2 // dw
		,self.outputSize[1] * 2 // dh
	);

	// set big canvas size
	canvas.width = self.outputSize[0];
	canvas.height = self.outputSize[1];

	// draw background
	context.fillStyle = '#ffffff';
	context.fillRect(0, 0, self.outputSize[0], self.outputSize[1]);

	// draw resize photo
	context.drawImage(bigCanvas, 0, 0, bigCanvas.width * 0.5, bigCanvas.height * 0.5);

	return canvas.toDataURL("image/jpeg", self.settings.quality);
};