<?php

namespace core;


class BaseObject {
	public function __construct($getter)
	{
		foreach ($getter as $k=>$v)
		{
			$this->$k = $v;
		}
	}
}