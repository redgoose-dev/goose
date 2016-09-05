### Introduce
첨부파일을 관리하는 모듈입니다.  
article모듈에서 관리하는 글을 올릴때 같이 첨부하는 파일들을 정보가 이 모듈의 테이블에 저장됩니다.


### Address guide
* `{goose}/file/index/`  
모들 첨부파일 목록


### setting.json

* __name__  
모듈의 id값

* __title__  
출력되는 제목값

* __description__  
모듈의 설명

* __permission__  
접근권한 번호 (숫자가 높을수록 권한이 높습니다.)

* __adminPermission__  
모듈 관리자 권한 번호 (숫자가 높을수록 권한이 높습니다.)

* __install__  
인스톨이 필요한 모듈인지에 대한 유무를 정합니다.

* __skin__  
다른형태로 목록이나 폼 페이지가 출력되는 스킨값

* __pagePerCount__  
한페이지에 출력되는 갯수

* **upPath_upload**  
업로드 부모 디렉토리 경로

* **upPath_original**  
원본파일이 저장되는 디렉토리 경로

* **upPath_make**  
썸네일 이미지로 제작되는 파일이 저장되는 경로

* **limitFileSize**  
첨부파일 사이즈 제한 용량

* **allowFileType**  
허용되는 파일 확장자


### Database field
다음은 nest 모듈을 설치할때 사용되는 db 필드들입니다.

#### {table_prefix}_file
글이 등록되면 저장되는 데이터 테이블입니다.

| Field         | Type       | Comment
| : ----------: | :--------: | :----------------------------
| srl           | int        | 고유번호
| article_srl   | bigint     | article 모듈 데이터의 고유번호
| name          | varchar    | 파일이름
| loc           | varchar    | 저장된 위치
| type          | varchar    | 파일의 종류
| size          | bigint     | 파일의 용량
| regdate       | varchar    | 등록날짜

#### {table_prefix}_file_tmp
글이 등록되기전에 임시로 저장되는 데이터 테이블입니다.

| Field         | Type       | Comment
| : ----------: | :--------: | :----------------------------
| srl           | int        | 고유번호
| name          | varchar    | 파일이름
| loc           | varchar    | 저장된 위치
| type          | varchar    | 파일의 종류
| size          | bigint     | 파일의 용량
| regdate       | varchar    | 등록날짜


### Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.

##### $mod->getCount()  
조건에 맞는 데이터 갯수를 가져옵니다.
```
$count = $mod->getCount(array(
	'where' => 'app_srl='.(int)$v['srl'])
));
```

##### $mod->getItems()  
조건에 맞는 데이터들의 모음을 가져옵니다.
```
$data = $mod->getItems(array(
	'where' => 'app_srl=1'
));
```

##### $mod->getItem()  
조건에 맞는 데이터 한개만 가져옵니다.
```
$data = $mod->getItem(array(
	'where' => 'srl=1'
));
```

##### $mod->actUploadFiles()  
복수의 파일을 업로드한다. 데이터페이스에 있는 정보도 추가한다.
```
$data = $mod->actUploadFiles(
	$_FILES['name'], // 파일목록
	'{goose}/data/upload/original', // 업로드 디렉토리
	'file', // 업로드 테이블 (file|file_tmp)
	23 // $article_srl 마지막 article번호. 테이블이 file_tmp라면 필요없음
);
```

##### $mod->actRemoveFile()  
파일삭제, 데이터베이스에 있는 정보도 삭제합니다.
```
$data = $mod->actUploadFiles(
	array(1,2,3), // srl 필드
	'file' // 테이블 (file|file_tmp)
);
```

##### $mod->actDBFiletmpToFile()
file_tmp에 있는 db데이터를 file로 옮깁니다.
```
$result = $mod->actDBFiletmpToFile(
	array(1,2,3), // 옮기려는 file_tmp 테이블의 srl 번호들
	23 // article_srl
);
```
