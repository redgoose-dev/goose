<?php
use core\Spawn, core\Goose, core\Util, core\Module, core\Paginate;
if(!defined("__GOOSE__")){exit();}


class API {

	public $goose, $ajax;

	public function __construct()
	{
		global $goose;

		$this->goose = $goose;
		$this->ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['REQUEST_METHOD'] == 'GET');
	}

	/**
	 * Search key in array
	 *
	 * @param array $get
	 * @param string $key
	 * @return bool
	 */
	private function searchKeyInArray($get, $key)
	{
		return in_array($key, $get);
	}

	/**
	 * Update hit
	 *
	 * @param int $hit
	 * @param int $article_srl
	 * @return bool
	 */
	private function updateHit($hit, $article_srl)
	{
		$hit += 1;
		$result = core\Spawn::update([
			'table' => core\Spawn::getTableName('article'),
			'where' => 'srl=' . $article_srl,
			'data' => [
				'hit=' . $hit
			],
		]);

		// writing cookie
		setCookieKey('redgoose-hit-'.(int)$article_srl, 7);

		return ($result == 'success');
	}

	/**
	 * thumbnail size to class name
	 *
	 * @param array $size
	 * @return string
	 */
	private function thumbnailSizeToClassName($size)
	{
		$largeSize = explode(',', __THUMBNAIL_LARGE_SIZE__);
		$sizeName = (in_array($size['width'], $largeSize) ? ' wx2' : '') . (in_array($size['height'], $largeSize) ? ' hx2' : '');
		return trim($sizeName);
	}

	/**
	 * Index
	 *
	 * @param object $options
	 * @return object
	 */
	public function index($options=null)
	{
		// set result
		$result = (object)[
			'nest' => null,
			'category' => null,
			'articles' => null,
			'pageNavigation' => null,
			'nextpage' => null,
		];

		// set print types
		$print = isset($options->print_data) ? explode(',', $options->print_data) : ['nest','category','article','nav_paginate','nav_more'];

		// get nest
		if ($options->nest_id)
		{
			$result->nest = Spawn::item([
				'table' => Spawn::getTableName('nest'),
				'where' => 'id=\''.$options->nest_id.'\'',
				'jsonField' => ['json'],
			]);

			if (!isset($result->nest['srl']))
			{
				return (object)[
					'state' => 'error',
					'message' => 'not found nest data',
				];
			}

			// get categories
			if (!!$result->nest['json']['useCategory'] && $this->searchKeyInArray($print, 'category'))
			{
				$result->category = Spawn::items([
					'table' => Spawn::getTableName('category'),
					'where' => 'nest_srl='.(int)$result->nest['srl'],
					'field' => 'srl,name',
					'order' => 'turn',
					'sort' => 'asc',
				]);
			}

			// get article count
			$cnt_all = core\Spawn::count([
				'table' => core\Spawn::getTableName('article'),
				'where' => 'app_srl='.$options->app_srl.' and nest_srl='.(int)$result->nest['srl'],
			]);

			// correction categories
			if (count($result->category))
			{
				$check_active = false;
				$index = [
					[ 'srl' => 0, 'name' => 'All', 'count' => $cnt_all, 'active' => false ]
				];

				foreach($result->category as $k=>$v)
				{
					$cnt = ($cnt_all > 0) ? Spawn::count([
						'table' => core\Spawn::getTableName('article'),
						'where' => 'category_srl='.(int)$v['srl']
					]) : 0;
					if ($options->category_srl === (int)$v['srl'])
					{
						$check_active = true;
						$result->category_name = $v['name'];
					}
					$index[] = [
						'srl' => (int)$v['srl'],
						'name' => $v['name'],
						'count' => $cnt,
						'active' => !!($options->category_srl === (int)$v['srl'])
					];
				}

				if (!$check_active)
				{
					$index[0]['active'] = true;
				}

				$result->category = $index;
			}
		}

		// get articles
		// init paginate
		$options->page = (isset($options->page) && $options->page > 1) ? $options->page : 1;
		$params = ['keyword' => ($options->keyword) ? $options->keyword : ''];

		$nest_srl = ($options->nest_id) ? ((isset($result->nest['srl'])) ? $result->nest['srl'] : -1) : null;
		$where = 'app_srl='.$options->app_srl;
		$where .= ($nest_srl) ? ' and nest_srl='.$nest_srl : '';
		$where .= ($options->category_srl) ? ' and category_srl='.(int)$options->category_srl : '';
		$where .= ($options->keyword) ? ' and (title LIKE "%'.$options->keyword.'%" or content LIKE "%'.$options->keyword.'%")' : '';

		// get total article count
		$total = Spawn::count([
			'table' => Spawn::getTableName('article'),
			'where' => $where,
		]);

		// set paginate instance
		$paginate = new Paginate($total, $options->page, $params, $options->size, $options->pageSize);

		// set limit
		$limit = $paginate->offset.','.$paginate->size;

		// get articles
		if ($this->searchKeyInArray($print, 'article'))
		{
			$result->articles = Spawn::items([
				'table' => Spawn::getTableName('article'),
				'field' => $options->field ? $options->field : '*',
				'where' => $where,
				'limit' => $limit,
				'sort' => 'desc',
				'order' => 'srl',
				'jsonField' => ['json'],
			]);

			// adjustment articles
			foreach ($result->articles as $k=>$v)
			{
				if (isset($v['regdate']))
				{
					$result->articles[$k]['regdate_original'] = $v['regdate'];
					$result->articles[$k]['regdate'] = Util::convertDate($v['regdate']);
				}
				if (isset($v['modate']))
				{
					$result->articles[$k]['modate_original'] = $v['modate'];
					$result->articles[$k]['modate'] = Util::convertDate($v['modate']);
				}

				if (isset($v['json']['thumbnail']['size']))
				{
					$result->articles[$k]['size_className'] = $this->thumbnailSizeToClassName($v['json']['thumbnail']['size']);
				}
			}
		}

		// set paginate
		if ($this->searchKeyInArray($print, 'nav_paginate'))
		{
			$result->pageNavigation = $paginate->createNavigationToObject();
		}

		// set more articles
		// 다음페이지에 글이 존재하는지 검사하고 있으면 다음 페이지 번호를 저장한다.
		if ($this->searchKeyInArray($print, 'nav_more'))
		{
			$nextPaginate = new Paginate($total, $options->page+1, $params, $options->size, $options->pageSize);
			$limit = $nextPaginate->offset.','.$nextPaginate->size;
			$nextArticles = Spawn::items([
				'table' => Spawn::getTableName('article'),
				'field' => 'srl',
				'where' => $where,
				'limit' => $limit,
				'sort' => 'desc',
				'order' => 'srl',
			]);
			$result->nextpage = (count($nextArticles)) ? $options->page + 1 : null;
		}

		$result->currentPage = $options->page;
		$result->nest = ($this->searchKeyInArray($print, 'nest')) ? $result->nest : null;
		$result->articles = isset($result->articles) ? $result->articles : null;
		$result->state = 'success';

		return $result;
	}

	/**
	 * Article
	 *
	 * @param object $options
	 * @return object
	 */
	public function article($options)
	{
		// check article_srl
		if (!$options->article_srl)
		{
			return (object)[
				'state' => 'error',
				'message' => 'not found article_srl'
			];
		}

		// get article data
		$article = Spawn::item([
			'table' => Spawn::getTableName('article'),
			'field' => $options->field ? $options->field : null,
			'where' => 'srl='.$options->article_srl,
			'jsonField' => ['json'],
		]);

		// check article data
		if ($article)
		{
			$article = (object)$article;
		}
		else
		{
			return (object)[
				'state' => 'error',
				'message' => 'not found article data'
			];
		}

		// convert date
		$article->regdate = Util::convertDate($article->regdate);
		$article->modate = Util::convertDate($article->modate);

		// set content type
		switch($article->json['mode'])
		{
			case 'markdown':
				// load parsedown
				require_once(__GOOSE_PWD__.'vendor/parsedown/Parsedown.php');

				// get instance parseDown
				$parseDown = new Parsedown();

				// convert markdown
				$article->content = $parseDown->text($article->content);
				break;

			case 'text':
				$article->content = nl2br(htmlspecialchars($article->content));
				break;
		}

		// make where query
		$print_data = explode(',', $options->print_data);
		$str = ($this->searchKeyInArray($print_data, 'nest')) ? 'nest_srl='.(int)$article->nest_srl : '';
		$str .= ($this->searchKeyInArray($print_data, 'category') && $article->category_srl) ? ' and category_srl='.(int)$article->category_srl : '';
		$str .= ($str) ? ' and ' : ' app_srl='.$options->app_srl . ' and ';

		// set prev,next item
		$prevItem = Spawn::item([
			'table' => Spawn::getTableName('article'),
			'field' => 'srl',
			'where' => $str.'srl<'.(int)$article->srl,
			'order' => 'srl',
			'sort' => 'desc',
			'limit' => 1,
			'debug' => false,
		]);
		$nextItem = Spawn::item([
			'table' => Spawn::getTableName('article'),
			'field' => 'srl',
			'where' => $str.'srl>'.(int)$article->srl,
			'order' => 'srl',
			'limit' => 1,
			'debug' => false,
		]);

		// get nest data
		$nest = Spawn::item([
			'table' => Spawn::getTableName('nest'),
			'field' => 'srl,name,id,json',
			'where' => 'srl='.(int)$article->nest_srl,
			'jsonField' => ['json']
		]);

		// get category
		$category = !!$article->category_srl ? Spawn::item([
			'table' => Spawn::getTablename('category'),
			'field' => 'name',
			'where' => 'srl='.(int)$article->category_srl,
		]) : null;

		return (object)[
			'state' => 'success',
			'article' => $article,
			'nest' => isset($nest) ? (object)$nest : null,
			'category' => isset($category) ? (object)$category : null,
			'anotherArticle' => (object)[
				'prev' => (isset($prevItem['srl'])) ? [ 'srl' => (int)$prevItem['srl'] ] : null,
				'next' => (isset($nextItem['srl'])) ? [ 'srl' => (int)$nextItem['srl'] ] : null,
			],
			'checkUpdateHit' => ($options->updateHit) ? ($this->updateHit((int)$article->hit, (int)$article->srl)) : null,
		];
	}

	/**
	 * Up like
	 *
	 * @param object $options : [article_srl, header_key]
	 * @return object
	 */
	public function upLike($options)
	{
		// check `article_srl` value
		if (!$options->article_srl)
		{
			return (object)[
				'state' => 'error',
				'message' => 'not found article_srl'
			];
		}

		// get article
		$article = Spawn::item([
			'table' => Spawn::getTableName('article'),
			'field' => 'srl,json',
			'where' => 'srl=' . (int)$options->article_srl,
			'jsonField' => ['json']
		]);

		// convert type
		$article = $article ? (object)$article : null;

		// check article json
		if (!isset($article->json))
		{
			return (object)[
				'state' => 'error',
				'message' => 'not found article data'
			];
		}

		// change like count
		$like = (isset($article->json['like'])) ? ((int)$article->json['like']) : 0;
		$article->json['like'] = $like + 1;
		$json = Util::arrayToJson($article->json, true);

		// update article
		$result = Spawn::update([
			'table' => Spawn::getTableName('article'),
			'data' => [ 'json=\''.$json.'\'' ],
			'where' => 'srl=' . (int)$options->article_srl,
		]);

		// save cookie
		setCookieKey('redgoose-like-'.(int)$options->article_srl, 7);

		// return
		return ($result == 'success') ? (object)[
			'state' => 'success',
			'data' => [
				'srl' => $options->article_srl,
				'like' => (int)$article->json['like']
			],
			'message' => 'update complete',
		] : (object)[
			'state' => 'error',
			'message' => 'fail update complete',
		];
	}

}