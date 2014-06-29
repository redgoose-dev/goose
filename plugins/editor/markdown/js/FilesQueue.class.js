var FilesQueue = function($el, options) {

	var self = this;

	this.index = new Array();
	this.count = 0;

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
	 * @param {Number} idx
	 * @param {String} filename
	 * @return {DOM} : queue element
	 */
	var template = function(idx, filename)
	{
		var item = '<li idx="' + idx + '">\n';
		item += '\t<div class="body">\n';
		item += '\t\t<span class="name">' + filename + '</span>\n';
		item += '\t\t<span class="size">0%</span>\n';
		item += '\t\t<span class="status">Ready</span>\n';
		item += '\t</div>\n';
		item += '\t<div class="progress">\n';
		item += '\t\t<p class="graph"><span></span></p>\n';
		item += '\t</div>\n';
		item += '\t<nav>\n';
		item += '\t\t<button type="button" rg-action="useThumnail">썸네일 이미지</button>\n';
		item += '\t\t<button type="button" rg-action="delete">삭제</button>\n';
		item += '\t</nav>\n';
		item += '</li>';

		return $(item);
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
		var
			idx = self.count
			,$dom = template(idx, file.name)
		;
		self.index.push({
			name : file.name
			,size : file.size
			,type : file.type
			,status : 'ready'
			,element : $dom
		});
		// 엘리먼트 만들기
		$el.children('ul').append($dom);

		self.count++;
		return idx;
	}

	/**
	 * modify queue
	 * 
	 * @author : redgoose
	 * @param {Object} options
	 * @return void
	 */
	this.modifyQueue = function(options)
	{
		
	}

	/**
	 * remove queue
	 * 
	 * @author : redgoose
	 * @param {} : ...
	 * @return void
	 */
	this.removeQueue = function()
	{
		
	}

	// act
	init();
}