<?php
namespace mod\api;
use core, mod;
if (!defined('__GOOSE__')) exit();


class api {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	public function index()
	{
		// method: GET, POST, PUT, PATCH

		// * action
		// `/goose/api/`: 문서
		// `/goose/api/nest/`: nest index
		// `/goose/api/nest/1/`: nest srl:1 article

		// * params
		// `field=srl,name`: field
		// `page=2`
		// `size=10`

		// * response
		// {
		//   code: 200,
		//   data: null,
		//   nav: {
		//     page, // 현재 페이지 번호
		//     count, // 전체 글 갯수
		//     lastPage, // 마지막 페이지(필요할까 고민됨)
		//     prev, // (srl) 이전글 번호
		//     next // (srl) 다음글 번호
		//   }
		// }

		if ($this->params['action'])
		{
			// TODO: 사용 가능한 모듈인지 검사
			// TODO: 오직 json으로만 출력
			core\Util::console($this->set['modules']);
			$asd = 'qwe';
		}
		else
		{
			// 문서나 설정
			$aa = '123';
		}

		core\Util::console('action:: ' . $this->params['action']);

		echo "api module";
	}

}