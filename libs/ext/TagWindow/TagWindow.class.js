/*
 * Tag window class
 * 객체 초기화는 init() 함수가 실행되며, 태그 등록을 위한 초기화는 form()메서드를 사용하고, 검색을 위한 초기화는 search()메서드가 사용된다.
 * 
 * @Param {Object} options
 */
var TagWindow = function(options) {

	var self = this;
	var $window = null;
	var settings = $.extend({}, this.defaults, options);

	/**
	 * init
	 */
	var init = function()
	{
		$window = windowTemplate();
		events($window);
		$('body').append($window);
		// $window 엘리먼트 속 이벤트들 만들기
	}

	/**
	 * Tag window template
	 */
	var windowTemplate = function()
	{
		var str = '<div class="tagWindow">';
		str += '<div class="hd"><a href="#" role-action="clear">Clear All</a></div>';
		str += '<div class="form">';
		str += '<input type="text" placeholder="Search/Add" name="tag" role-action="keyword" />';
		str += '<button type="button" role-action="add">Add</button>';
		str += '</div>';
		str += '<ul class="index"></ul>';
		str += '</div>';
		return $(str);
	}

	/*
	 * Events
	 * 윈도우 속에서의 이벤트들
	 */
	var events = function(w)
	{
		log(w);
		log('events');
		w.find('[role-action=clear]').on('click', function(){
			self.clearAll();
		});
		w.find('[role-action=keyword]').on('keydown', function(o){
			self.searchTag(this.value);
		});
	}

	/*
	 * Form
	 * 폼 만들기 초기화
	 * 
	 * @Param {Array} $input : 인풋 폼
	 */
	this.form = function($input)
	{
		log('form init');
	}

	/*
	 * Search
	 * 태그검색 초기화
	 */
	this.search = function()
	{
		log('search init');
	}

	/*
	 * Clear all
	 * 모든태그 삭제
	 */
	this.clearAll = function()
	{
		
	}

	/*
	 * Search keyword
	 * 
	 * @Param {String} keyword
	 * 
	 */
	this.searchTag = function(keyword)
	{
		
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
	data : 'tag.txt'
}
