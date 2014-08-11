var TagWindow = function(input, options) {
	
	var self = this;
	var $input = $(input);
	var $wrap = null;
	var $window = null;
	var settings = $.extend({}, this.defaults, options);

	/**
	 * init
	 */
	var init = function()
	{
		createWrap($el);
		$wrap = null; // 엘리먼트가 만들어졌을테니 대입하기
		$window = null; // 엘리먼트가 만들어졌을테니 대입하기 
		events();
	}

	/**
	 * Create Wrap
	 * div로 input을 감싼다.
	 */
	var createWrap = function(el)
	{
		// 특정 div로 input을 감싼다.
		log(el);
	}

	/*
	 * Events
	*/
	var events = function()
	{
		log('events');
	}

	/**
	 * Action
	 */
	init();
}


/**
 * Default variables
 */
TagWindow.prototype.defaults = {
	
}