### method
- GET
- POST
- PUT
- PATCH


### action
- `/goose/api/`: 문서
- `/goose/api/nest/`: nest index
- `/goose/api/nest/1/`: nest srl:1 article


### params
- `field=srl,name`
- `sort=desc`
- `order=srl`
- `page=2`
- `size=10`
- `json=json`
- `token=qwe123`
- `q=keyword`


### response
```
{
	code: 200,
	data: null,
	nav: { // 네비게이션 정보 예정
		page, // 현재 페이지 번호
		count, // 전체 글 갯수
		lastPage, // 마지막 페이지(필요할까 고민됨)
		prev, // (srl) 이전글 번호
		next // (srl) 다음글 번호
	}
}
```


### TODO

- [ ] help 문서 작업하기
- [ ] GET 쪽만을 목표로 작업
- [ ] 토큰 변경기능
