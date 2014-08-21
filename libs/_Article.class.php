<?php
if(!defined("GOOSE")){exit();}

class Article {

	// classes
	public $goose;
	public $util;
	public $spawn;
	public $route;
	public $paginate;

	// variables
	public $action;
	public $srl;
	public $nav;

	// data
	public $data = array();


	function __construct()
	{
		global $goose, $route;

		$this->goose = $goose;
		$this->util = $goose->util;
		$this->spawn = $goose->spawn;
		$this->route = $route;

		self::setAction();
		self::initSrl();
	}


	/**
	 * Set action
	 * 
	 * @param object $route
	 */
	private function setAction()
	{
		$rt = ($this->route) ? $this->route->getParameters() : null;
		$this->action = ($rt) ? $rt['action'] : null;
	}

	/**
	 * init srl
	 * srl값들이 담아있는 배열을 초기화한다.
	 */
	private function initSrl()
	{
		$this->srl = $this->util->checkArray(
			$this->srl
			,array('nest', 'category', 'article', 'group')
		);
		$params = $this->route->getParameters();

		switch($this->action)
		{
			case 'index':
				$this->srl['nest'] = (isset($params['param0'])) ? $params['param0'] : null;
				$this->srl['category'] = (isset($params['param1'])) ? $params['param1'] : null;
				break;
			case 'view':
				$this->srl['article'] = (isset($params['param0'])) ? $params['param0'] : null;
				break;
			case 'create':
				$this->srl['nest'] = (isset($params['param0'])) ? $params['param0'] : null;
				$this->srl['category'] = (isset($params['param1'])) ? $params['param1'] : null;
				break;
			case 'modify':
				$this->srl['article'] = (isset($params['param0'])) ? $params['param0'] : null;
				break;
			case 'delete':
				$this->srl['article'] = (isset($params['param0'])) ? $params['param0'] : null;
				break;
		}
	}

	/**
	 * set srl
	 * srl배열에서 값을 세트한다.
	 */
	public function setSrl($name, $srl)
	{
		if ($srl)
		{
			$this->srl[$name] = $srl;
		}
	}

	/**
	 * get nest
	 * 
	 * @return array
	 */
	private function getNest($srl)
	{
		if (!$srl)
		{
			return array();
		}
		$nest = $this->spawn->getItem(array(
			'table' => 'nests',
			'where' => 'srl='.$srl
		));
		if ($nest)
		{
			try {
				$nest['json'] = json_decode($nest['json']);
			} catch(Exception $e) {}
		}
		else
		{
			$this->util->back('없는 둥지번호입니다.');
			$this->goose->out();
		}
		return $nest;
	}

	/**
	 * get categories
	 * 
	 * @return array
	 */
	private function getCategories()
	{
		if (!$this->srl['nest'])
		{
			return null;
		}
		$category = $this->spawn->getItems(array(
			'table' => 'categories',
			'where' => 'nest_srl='.$this->srl['nest'],
			'order' => 'turn',
			'sort' => 'asc'
		));
		foreach ($category as $k=>$v)
		{
			$cnt = $this->spawn->getCount(array(
				'table' => 'articles',
				'where' => 'category_srl='.$v['srl'].' and nest_srl='.$this->srl['nest']
			));
			$category[$k]['count'] = $cnt;
		}
		return (count($category)) ? $category : array();
	}

	/**
	 * get category
	 * 
	 * @return array
	 */
	private function getCategory($srl)
	{
		return $this->spawn->getItem(array(
			'table' => 'categories',
			'where' => 'srl='.$srl
		));
	}

	/**
	 * get article
	 * 
	 */
	private function getCount()
	{
		if ($this->srl['nest'])
		{
			$where = 'nest_srl='.$this->srl['nest'];
			$where .= ($this->srl['category']) ? ' and category_srl='.$this->srl['category'] : '';
		}
		return $this->spawn->getCount(array(
			'table' => 'articles'
			,'where' => $where
		));
	}

	/**
	 * get articles
	 * 
	 * @return array
	 */
	private function getArticles()
	{
		if ($this->srl['nest'])
		{
			$where = 'nest_srl='.$this->srl['nest'];
			$where .= ($this->srl['category']) ? ' and category_srl='.$this->srl['category'] : '';
		}

		require_once(PWD.'/libs/Paginate.class.php');
		$paginateParameter = array(
			'keyword' => (isset($_GET['keyword'])) ? $_GET['keyword'] : ''
		);
		$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
		$this->paginate = new Paginate($this->data['count'], $_GET['page'], $paginateParameter, $this->data['nest']['listCount'], 5);

		$articles = $this->spawn->getItems(array(
			'field' => '*',
			'table' => 'articles',
			'where' => (isset($where)) ? $where : null,
			'order' => 'srl',
			'sort' => 'desc',
			'limit' => array($this->paginate->offset, $this->paginate->size)
		));

		foreach ($articles as $k=>$v)
		{
			$categoryName = ($v['category_srl']) ? $this->spawn->getItem(array(
				'table' => 'categories',
				'where' => 'srl='.$v['category_srl']
			)) : '';
			$articles[$k]['categoryName'] = (isset($categoryName['name'])) ? $categoryName['name'] : null;
		}

		return $articles;
	}

	/**
	 * get article
	 * 
	 * @return array
	 */
	private function getArticle()
	{
		if ($this->srl['article'])
		{
			return $this->spawn->getItem(array(
				'table' => 'articles',
				'where' => 'srl='.$this->srl['article']
			));
		}
		else
		{
			return array();
		}
	}

	/**
	 * page
	 * 페이지 성격에 따른 데이터를 준비한다.
	 * 
	 */
	public function pageReady()
	{
		switch($this->action)
		{
			case 'index':
				$this->data['nest'] = $this->getNest($this->srl['nest']);
				$this->data['count'] = $this->getCount();
				$this->data['documents'] = ((int)$this->data['count'] > 0) ? $this->getArticles() : null;
				$this->data['categories'] = $this->getCategories($this->srl['category']);
				break;
			case 'view':
				if (!$this->srl['article'])
				{
					$this->util->alert('srl값이 없습니다.');
					$this->goose->out();
				}
				$this->data['document'] = $this->getArticle();
				$this->data['nest'] = $this->getNest($this->data['document']['nest_srl']);
				$this->data['category'] = $this->getCategory($this->data['document']['category_srl']);
				$_GET = $this->util->checkArray($_GET, array('page', 'm'));
				break;
			case 'create':
				if (!$this->srl['nest'])
				{
					$this->util->back('nest값이 없습니다.');
					$this->goose->out();
				}
				$_GET = $this->util->checkArray($_GET, array('page'));
				$this->data['nest'] = $this->getNest($this->srl['nest']);

				if ($this->data['nest']['useCategory'])
				{
					$this->data['categories'] = $this->getCategories();;
				}

				break;
			case 'modify':
				
				break;
			case 'delete':
				
				break;
		}

		$this->skin = ($this->data['nest']['json']->articleSkin) ? $this->data['nest']['json']->articleSkin : 'basic';
	}

}
?>