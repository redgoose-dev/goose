<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - intro
 *
 */

class Intro {

	public $name, $param, $set, $layout;
	public $path, $pwd_container;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';
	}

	/**
	 * index method
	 */
	public function index()
	{
		// create layout module
		$this->layout = Module::load('layout');

		// set repo
		$repo = Array('article');

		// load modules
		$article = Module::load('article');
		$nest = Module::load('nest');
		$category = Module::load('category');

		// get articles
		$data = $article->getItems(array( 'limit' => array(0, $this->set['pagePerCount']) ));
		$repo['article'] = ($data['state'] == 'success') ? $data['data'] : array();

		foreach($repo['article'] as $k=>$v)
		{
			$data = $nest->getItem(array(
				'field' => 'name,json',
				'where' => 'srl='.$v['nest_srl']
			));
			$repo['article'][$k]['nest'] = ($data['state'] == 'success') ? $data['data'] : null;

			if ($repo['article'][$k]['nest']['json']['useCategory'] && $v['category_srl'])
			{
				$data = $category->getItem(array(
					'field' => 'name',
					'where' => 'srl='.(int)$v['category_srl']
				));
				$repo['article'][$k]['categoryName'] = ($data['state'] == 'success') ? $data['data']['name'] : null;
			}
		}

		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'view_index.html';

		// require layout
		require_once($this->layout->getUrl());
	}

}