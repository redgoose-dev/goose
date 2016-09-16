## Introduce
첨부파일을 관리하는 모듈입니다.  
`Article`모듈에서 관리하는 글을 올릴때 같이 첨부하는 파일들을 정보가 이 모듈의 테이블에 저장됩니다.



## URL guide

#### 모들 첨부파일 목록
`/mod/File/index/`



## setting.json

#### name
모듈의 id값

#### title
출력되는 제목값

#### description
모듈의 설명

#### permission
접근권한 번호 (숫자가 높을수록 권한이 높습니다.)

#### adminPermission
모듈 관리자 권한 번호 (숫자가 높을수록 권한이 높습니다.)

#### install
인스톨이 필요한 모듈인지에 대한 유무를 정합니다.

#### skin
다른형태로 목록이나 폼 페이지가 출력되는 스킨값

#### pagePerCount
한페이지에 출력되는 갯수

#### upPath_upload
업로드 부모 디렉토리 경로

#### upPath_original
원본파일이 저장되는 디렉토리 경로

#### upPath_make
썸네일 이미지로 제작되는 파일이 저장되는 경로

#### limitFileSize
첨부파일 사이즈 제한 용량

#### allowFileType
허용되는 파일 확장자



## Database field
다음은 nest 모듈을 설치할때 사용되는 db 필드들입니다.

| Field         | Type       | Comment
| : ----------: | :--------: | :----------------------------
| srl           | int        | 고유번호
| article_srl   | bigint     | article 모듈 데이터의 고유번호
| name          | varchar    | 파일이름
| loc           | varchar    | 저장된 위치
| type          | varchar    | 파일의 종류
| size          | bigint     | 파일의 용량
| regdate       | varchar    | 등록날짜
| ready         | int        | 등록전 대기상태



## Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.

```
$file = new mod\File\File();
$file = core\Module::load('File');
```

#### $mod->actUploadFiles()
복수의 파일을 업로드한다. 데이터페이스에 있는 정보도 추가한다.
```
$data = $file->actUploadFiles(
	$_FILES, // 파일목록
	'{goose}/data/upload/original', // 업로드 디렉토리
	23, // $article_srl 마지막 article번호.
	0 // 업로드 대기상태(ready값)
);
```

#### $mod->actRemoveFile()
파일삭제, 데이터베이스에 있는 정보도 삭제합니다.
```
$data = $file->actUploadFiles([ 1, 2, 3 ]);
```