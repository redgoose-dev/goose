### method
- GET
- POST (예정)
- PUT (예정)
- PATCH (예정)


### action example
- `/goose/api/`: 문서
- `/goose/api/nest/`: nest index
- `/goose/api/nest/1/`: nest srl:1 article


### token

api를 요청하기 위하여 token값을 필수로 넣어줘야한다. token값은 [API 페이지] 에서 확인할 수 있다.

#### `url`에서 토큰값을 넣어서 요청하는 방법
```
?token={TOKEN}
```

#### `headers`에 넣어서 요청하는 방법
- key: `token`
- value: {TOKEN}


### get single or multiple data
`/goose/api/{MODULE}/{SRL}/` 과 `srl`값을 추가해주면 데이터 하나만 뽑아온다.  
없으면 배열로 여러 데이터를 가져온다.


### params

#### 공통 파라메터

- `field=srl,name`
- `sort=desc`
- `order=srl`
- `page=2`
- `size=10`
- `json=json`
- `token=qwe123`
- `q=keyword`

#### 모듈 컴포넌트

각 모듈별로 요구하는 파라메터가 달라진다. 자세한 내용은 해당하는 모듈 도움말 페이지에서 API 섹션을 참고


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
