/*
	http://scripterkr.tistory.com/ 참고
	
	1. 키값을 input에 입력
	2. 추가하려면 엔터를 추르거나 옆에 'add'버튼을 누르면 태그추가 메서드 실행
	3. 폼 아래에 태그목록에서 태그가 만들어지고 태그배열에서 새로운 태그 키워드 추가.(추가하기전에 단어검사와 중복검사)
	4. 태그목록에서 x버튼을 누르면 해당되는 태그 삭제
	5. submit 이벤트가 발생하면 태그모록에서의 태그들을 모아서 문자변수로 합치고 json값으로 변형시켜 <input type="hidden" name="json" /> 항목에다 삽입
*/

var TagManager = function($el)
{
	var self = this;

	// events
	var events = function()
	{
		$el.keyup(function(e){
			if (e.keyCode == 13)
			{
				log($(this).val())
				log('add tag');
				return false;
			}
		});
	}

	// action
	events();
}