/*
	http://scripterkr.tistory.com/ 참고
	
	1. 키값을 input에 입력
	2. 추가하려면 엔터를 추르거나 옆에 'add'버튼을 누르면 태그추가 메서드 실행
	3. 폼 아래에 태그목록에서 태그가 만들어지고 태그배열에서 새로운 태그 키워드 추가.(추가하기전에 단어검사와 중복검사)
	4. 태그목록에서 x버튼을 누르면 해당되는 태그 삭제
	5. submit 이벤트가 발생하면 태그모록에서의 태그들을 모아서 문자변수로 합치고 json값으로 변형시켜 <input type="hidden" name="json" /> 항목에다 삽입
*/

var TagManager = function($el, $tags)
{
	var self = this;

	this.$input = $el;
	this.tags = new Array();

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

	
	this.allTags = function($wrap)
	{
		log('all tags');
	}

	// action
	events();
}
