<?php
if (!defined('__GOOSE__')) exit();

class Paginate {
	var $total, $page, $size, $scale;
	var $start_page, $page_max, $offset, $block, $tails;

	/**
	 * Paginate
	 *
	 * @Param {Number} $total : total items
	 * @Param {Number} $page : page number
	 * @Param {Array} $arr : url parameter
	 * @Param {Number} $size : count of list
	 * @Param {Number} $scale : count of page
	 * @Param {Number} $start_page : start page number
	 * @Return void
	 */
	public function Paginate($total="0", $page="1", $arr="", $size="10", $scale="10", $start_page="1")
	{
		$page = ($page) ? $page : 1;
		$total = ($total) ? $total : 0;

		$this->total = $total;
		$this->page = $page;
		$this->size = $size;
		$this->scale = $scale;
		$this->start_page = $start_page;
		$this->page_max = ceil($total / $size); // 총 페이지개수
		$this->offset = ($page - 1) * $size; // 해당 페이지에서 시작하는 목록번호
		$this->block = floor(($page-1) / $scale); // 페이지를 10개씩보여준다면 1~10페이지까지는 0블럭..
		$this->no = $this->total - $this->offset; // 목록에서 번호나열할때 필요함

		$tails = '';
		if (is_array($arr)) {
			while (list($key,$val)=each($arr))
			{
				$tails .= ($val) ? "$key=$val&" : "";
			}
		}
		$this->tails = substr($tails, 0, -1);
	}

	/**
	 * Create Navigation
	 * make navigation element
	 *
	 * @Return void
	 */
	public function createNavigation()
	{
		$op = null;
		if($this->total > $this->size) {
			$op = "<div class='gs-paginate'>\n";
			if($this->block > 0) {
				$prev_block = ($this->block - 1) * $this->scale + 1;
				$str = ($prev_block == 1) ? "" : "page=$prev_block";
				$amp = ($this->tails && $str) ? "&" : "";
				$str = ($str || $this->tails) ? "?".$this->tails.$amp.$str : "";
				$op.="<a href=\"./$str\">Prev</a>\n";
			} else {
				$op.="";
			}
			$this->start_page = $this->block * $this->scale + 1;

			for($i = 1; $i <= $this->scale && $this->start_page <= $this->page_max; $i++, $this->start_page++) {
				if($this->start_page == $this->page) {
					$op.="<strong>$this->start_page</strong>\n";
				} else {
					$str = ($this->start_page == 1) ? "" : "page=$this->start_page";
					$amp = ($this->tails && $str) ? "&" : "";
					$str = ($str || $this->tails) ? "?".$this->tails.$amp.$str : "";
					$op.="<a href=\"./$str\">$this->start_page</a>\n";
				}
			}
			if($this->page_max > ($this->block + 1) * $this->scale) {
				$next_block = ($this->block + 1) * $this->scale + 1;
				$op.="<a href=\"./?{$this->tails}page=$next_block\">Next</a>\n";
			} else {
				$op.="";
			}
			$op .= "</div>\n";
		}
		return $op;
	}
}
?>