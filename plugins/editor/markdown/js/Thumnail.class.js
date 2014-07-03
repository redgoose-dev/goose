var Thumnail = function(parent, options) {
	
	var
		self = this
		,windowName = 'thumnailWindow'
		,maxImageSize = 640
	;

	this.settings = $.extend({}, this.defaults, options);
	this.$window = null;
	this.data = {
		queue : null
		,coords : null
	};

	// window template
	var template = function()
	{
		var str = '<div id="' + windowName + '">\n';
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

	// get ratio
	var getRatio = function(w, h)
	{
		var result = parseInt(w) / parseInt(h);
		result = Math.round(result * 1000) / 1000;
		return result;
	}

	// install
	var createWindow = function(obj)
	{
		self.$window = template();
		log(obj);
		self.$window.find('figure').append(
			$('<img src="' + parent.settings.fileDir + obj.location + '" />')
		);
		$('body').append(self.$window);
	}

	var removeWindow = function()
	{
		
	}


	// create window
	this.open = function(item)
	{
		if (!self.$window)
		{
			createWindow({
				location : item.location
			});
		}
		
	}

	// close window
	this.close = function()
	{
		
	}

	// get data
	this.getData = function()
	{
		
	}

}


// default variables
Thumnail.prototype.defaults = {
	type : 'crop'
	,ratio : 1
	,size : [100,100]
	,quality : 0.7
}