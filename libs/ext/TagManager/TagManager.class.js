/**
 * Tag Manager
 * 
 * @Param {Object} $el : 태그 input폼
 * @Param {Object} $tags : 태그목록
 */
var TagManager = function($el, $tags)
{
	var self = this;

	this.$input = $el;
	this.tags = new Array();
	this.alltags = new Array();
	this.$alltagsIndex = null;

	/**
	 * events
	 */
	var events = function()
	{
		$el.on('keypress', function(e){
			if (e.keyCode == 13)
			{
				self.add($(this).val());
				return false;
			}
		});
	}

	/**
	 * tag template
	 * 
	 * @Param {String} keyword
	 * @Return {Object}
	 */
	var tagTemplate = function(keyword)
	{
		var str = '<p>';
		str += '<span>' + keyword + '</span>';
		str += '<button type="button" role-action="removeTag" title="remove tag">remove</button>';
		str += '</p>';
		return $(str);
	}

	/**
	 * add tag
	 * 
	 * @Param {String} keyword
	 */
	this.add = function(keyword)
	{
		keyword = keyword.replace(/[^a-zA-Z0-9가-힣]|/g, '');

		if (keyword.replace(/[^a-zA-Z0-9가-힣]|/g, ''))
		{
			if (self.tags.indexOf(keyword) < 0)
			{
				self.tags.push(keyword);
				var $tag = tagTemplate(keyword);
				$tag.children('button').on('click', function(){
					self.remove($tag);
				});
				$tags.append($tag);
				$el.val('');
			}
			else
			{
				alert('중복된 키워드가 있습니다.');
				$el.focus();
			}
		}
	}

	/**
	 * remove tag
	 * 
	 * @Param {Object} $tag
	 */
	this.remove = function($tag)
	{
		$tag.each(function(){
			var text = $(this).children('span').text();
			var position = self.tags.indexOf(text);
			self.tags.splice(position, 1);
			$tag.remove();
			self.$alltagsIndex.eq(self.alltags.indexOf(text)).removeClass('on');
			
		});
	}

	/**
	 * import tag
	 * 
	 * @Param {Array} keywords
	 */
	this.import = function(keywords)
	{
		for (var i=0; i<keywords.length; i++)
		{
			self.add(keywords[i]);
		}
	}

	/**
	 * export tag
	 * 
	 * @Return {Array}
	 */
	this.export = function()
	{
		return self.tags;
	}

	
	this.allTagsInit = function($wrap)
	{
		self.$alltagsIndex = $wrap.find('ul > li');
		self.alltags = self.$alltagsIndex.map(function(){
			return $(this).children('span').text();
		}).get();

		// sync tags
		for (var i=0; i< self.tags.length; i++)
		{
			if (self.alltags.indexOf(self.tags[i]) > 0)
			{
				self.$alltagsIndex.eq(self.alltags.indexOf(self.tags[i])).addClass('on');
			}
		}

		// button event
		$wrap.children('button').on('click', function(){
			$wrap.toggleClass('on');
			$(this).toggleClass('btn-highlight');
		});

		// tag event
		self.$alltagsIndex.on('click', function(){
			if (!$(this).hasClass('on'))
			{
				self.add($(this).children('span').text());
				$(this).addClass('on');
			}
		});
	}

	// action
	events();
}
