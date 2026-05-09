<?php
/*
 * Lister
 * class for handling sorting and paging of list
 * 2010 Petr Stastny
 */

//require_once('QuerySorterLimiter.class.php');
//require_once('Database.class.php');
//require_once('Printable.class.php');
//require_once('ApplicationDependent.class.php');

class Lister {

	const PARAM_COUNTSUFFIX = '_pg_c';
	const PARAM_FROMSUFFIX = '_pg_f';
	const PARAM_ORDERBYSUFFIX = '_pg_b';
	const PARAM_ORDERDIRSUFFIX = '_pg_d';

	const DIR_ASC = 1; //ASC only
	const DIR_DESC = 2; //DESC only
	const DIR_ASC_DESC = 3; //ASC default
	const DIR_DESC_ASC = 4; //DESC default

	const ITER_BREAK = 0;
	const ITER_SKIP = 1;
	const ITER_DO = -1;

	protected $id; // ID of this lister (one ID share same set of parameter names)
	protected $from; // zero based
	protected $count; // count per page
	protected $totalCount; // total count of listed items
	protected $columns; // description for columns for sorting
	protected $dirKeywords = array( array('asc', 'ASC'), array('desc', 'DESC') ); // sort direction
	protected $activeColumnIndex; // column for sorting as index into $columns
	protected $defaultColumnIndex; // default column index for sorting
	protected $activeDirIndex; // actual direction as index into $dirKeywords
	protected $defaultDirIndex; // default direction index
	protected $paramCount = self::PARAM_COUNTSUFFIX; // parameter names
	protected $paramFrom = self::PARAM_FROMSUFFIX;
	protected $paramOrderBy = self::PARAM_ORDERBYSUFFIX;
	protected $paramOrderDir = self::PARAM_ORDERDIRSUFFIX;
	protected $post = false;
	protected $template = null; // template file
	protected $maxPages; // max count of pages shown in navigation; for $maxPages <= 0 all pages will be shown

	protected $iterationCounter;

	/**
	 * @param $columnDescs array ( array(publicName, dbName, Lister::DIR_*), ... )
	 * @param $defaultColumnIndex index to $columnDescs, default column to sort by
	 * @param $dirKeywords optional array ( array(publicWordASC, dbWordASC), array(publicWordDESC, dbWordDESC) )
	 * @param $post TRUE if lister should be posted using form
	 */
	public function __construct($id, $count, $totalCount, $from = null, $maxPages = 0, array $columnDescs = null, $defaultColumnIndex = 0, $dirKeywords = null, $defaultDirIndex = 0, $post = false, $template = null) {
		$this->id = $id;
		$this->count = $count;
		$this->from = $from;
		$this->totalCount = $totalCount;
		$this->columns = $columnDescs;
		if (isset($dirKeywords))
			$this->dirKeywords = $dirKeywords;
		$this->defaultColumnIndex = $defaultColumnIndex;
		$this->activeColumnIndex = $defaultColumnIndex;
		$this->defaultDirIndex = $defaultDirIndex;
		$this->paramOrderBy = $id . self::PARAM_ORDERBYSUFFIX;
		$this->paramOrderDir = $id . self::PARAM_ORDERDIRSUFFIX;
		$this->paramCount = $id . self::PARAM_COUNTSUFFIX;
		$this->paramFrom = $id . self::PARAM_FROMSUFFIX;
		$this->post = $post;
		$this->template = $template;
		$this->maxPages = (isset($maxPages) ? $maxPages : 0);

		$this->initIteration();
		$this->checkLimits();
	}

	public function checkLimits() {
		if (isset($this->totalCount)) {
			if ($this->totalCount < 0)
				$this->totalCount = 0;
			if (isset($this->count)) {
				if ($this->count < 0)
					$this->count = $this->totalCount;
			}
			if (isset($this->from)) {
				if ($this->from > $this->totalCount - 1)
					$this->from = $this->totalCount - 1;
				if ($this->from < 0)
					$this->from = 0;
			}
		}
	}

	public function setTotalCount($count) {
		$this->totalCount = $count;
		$this->checkLimits();
	}

	public function isListerParam($name) {
		switch ($name) {
		case $this->paramCount:
		case $this->paramFrom:
		case $this->paramOrderBy:
		case $this->paramOrderDir:
			return true;
		default:
			return false;
		}
	}

	public function removeListerParams(array $params) {
		$removed = array();
		foreach ($params as $name => $value) {
			if (!$this->isListerParam($name))
				$removed[$name] = $value;
		}
	}

	public function updateFromParams(array &$params) {
		/* get sorting params */
		if (isset($this->columns) && !empty($params[$this->paramOrderBy])) {
			/* find column to be sorted by (or let default unchanged) */
			$param = $params[$this->paramOrderBy];
			for ($i = 0; $i < sizeof($this->columns); ++$i) {
				$col = $this->columns[$i];
				if (0 == strcasecmp($param, $col[0])) {
					$this->activeColumnIndex = $i;
					break;
				}
			}
			/* set default direction */
			switch ($this->columns[$this->activeColumnIndex][2]) {
			case self::DIR_DESC:
			case self::DIR_DESC_ASC:
				$this->activeDirIndex = 1;
				break;
			case self::DIR_ASC_DESC:
			case self::DIR_ASC:
			default:
				$this->activeDirIndex = 0;
				break;
			}
			/* try to find direction for columns that can have both directions */
			switch ($this->columns[$this->activeColumnIndex][2]) {
			case self::DIR_ASC_DESC:
			case self::DIR_DESC_ASC:
				if (!empty($params[$this->paramOrderDir])) {
					$param = $params[$this->paramOrderDir];
					for ($i = 0; $i < sizeof($this->dirKeywords); ++$i) {
						$dir = $this->dirKeywords[$i];
						if (0 == strcasecmp($param, $dir[0])) {
							$this->activeDirIndex = $i;
							break;
						}
					}
				}
				break;
			default:
				break;
			}
		}
		/* get limits */
		if (isset($params[$this->paramCount])) {
			$param = $params[$this->paramCount];
			if (is_numeric($param)) {
				$this->count = round($param, 0);
				if (isset($params[$this->paramFrom])) {
					$param = $params[$this->paramFrom];
					if (is_numeric($param))
						$this->from = round($param, 0);
				}
			}
		}
		$this->checkLimits();
	}

	/**
	 * @param $publicName string public name of column, if empty then active column/dir URL part will be returned
	 */
	public function getUrlParams($publicName = null, $count = null, $from = null) {
		$urlName = null;
		$urlDir = null;
		if (isset($this->columns)) {
			if (empty($publicName)) {
				if (isset($this->activeColumnIndex)) {
					$urlName = $this->columns[$this->activeColumnIndex][0];
					if (isset($this->activeDirIndex))
						$urlDir = $this->dirKeywords[$this->activeDirIndex][0];
				}
			}
			else {
				$found = false;
				for ($i = 0; $i < sizeof($this->columns); ++$i) {
					$col = $this->columns[$i];
					if (0 == strcasecmp($publicName, $col[0])) {
						$found = true;
						break;
					}
				}
				if ($found) {
					$urlName = $col[0];
					if ($this->activeColumnIndex == $i) {
						/* generate URL for switching direction */
						switch ($col[2]) {
						case self::DIR_ASC_DESC:
							if ($this->activeDirIndex < sizeof($this->dirKeywords) - 1)
								$urlDir = $this->dirKeywords[$this->activeDirIndex + 1][0];
							else
								$urlDir = $this->dirKeywords[0][0];
							break;
						case self::DIR_DESC_ASC:
							if ($this->activeDirIndex > 0)
								$urlDir = $this->dirKeywords[$this->activeDirIndex - 1][0];
							else
								$urlDir = $this->dirKeywords[sizeof($this->dirKeywords) - 1][0];
							break;
						default:
							break;
						}
					}
					else {
						/* generate URL for default direction */
						switch ($col[2]) {
						case self::DIR_ASC_DESC:
							$dir = $this->dirKeywords[0][0];
							break;
						case self::DIR_DESC_ASC:
							$dir = $this->dirKeywords[1][0];
							break;
						default:
							break;
						}
					}
				}
			}
		}
		$ret = array();
		if (!empty($urlName)) {
			$ret[$this->paramOrderBy] = $urlName;
			if (!empty($urlDir))
				$ret[$this->paramOrderDir] = $urlDir;
		}
		if (!isset($count))
			$count = $this->count;
		if (isset($count)) {
			$ret[$this->paramCount] = $count;
			if (!isset($from))
				$from = $this->from;
			if (isset($from))
				$ret[$this->paramFrom] = $from;
		}
		return $ret;
	}

	/**
	 * Creates list of URLs for page browsing, uses members like from, count, totalCount
	 * @param $maxPages max count of pages that can be in returned (first and last are not counted in)
	 * @returns array( 'index' => activeIndex,
	 *              'urls' => array(i => array(urlParams1), (i+1) => array(urlParams2), ...),
	 *              'prev' => array(previousUrlParams),
	 *              'next' => array(nextUrlParams),
	 *              'first' => array(firstUrlParams),
	 *              'last' => array(lastUrlParams),
	 *              'count' => realCount,
	 *              'pagesCount' => pagesCount,
	 *          );
	 *          null is returned when totalCount is not known or zero, so no page browsing is possible;
	 *          prev, next, first, last or all urls each can be empty;
	 *          returned index pointing to urls array and keys in urls are one based (index value must be present within urls keys)
	 *          realCount is real count of records on page (useful for last page)
	 *          pagesCount is total number of pages of listed items
	 */
	public function getPagesUrls($maxPages = null) {
		if (empty($this->totalCount))
			return null;
		if (!isset($maxPages))
			$maxPages = $this->maxPages;
		$this->checkLimits();
		/* no count per page given (or zero) => all on one page */
		if (empty($this->count))
			return array(
				'index' => 0,
				'urls' => array($this->getUrlParams(null, null, null)),
				'prev' => null,
				'next' => null,
				'first' => null,
				'last' => null,
				'count' => $this->totalCount,
				'totalCount' => $this->totalCount,
				'pagesCount' => 0
			);
		$from = (isset($this->from) ? $this->from : 0);
		$pagesCount = floor(($this->totalCount - $from) / $this->count);
		if ($this->totalCount % $this->count > 0)
			++$pagesCount;
		$pagesCount += floor($from / $this->count);
		$page = floor($from / $this->count);
		if ($page >= $pagesCount)
			$page = $pagesCount - 1;
		if ( ($offset = $from % $this->count) > 0 ) {
			++$pagesCount;
			++$page;
			$pageOffset = 1;
		}
		else
			$pageOffset = 0;
		if ($maxPages <= 0)
			$maxPages = $pagesCount;
		$l = $page - floor($maxPages / 2);
		if ($l < 0)
			$l = 0;
		$r = $l + $maxPages - 1;
		if ($r >= $pagesCount) {
			$r = $pagesCount - 1;
			$l = $r - $maxPages + 1;
			if ($l < 0)
				$l = 0;
		}
		$list = array();
		for ($i = $l; $i <= $r; ++$i) {
			if ($i == 0)
				$list[$i + 1] = $this->getUrlParams(null, $this->count, 0);
			else
				$list[$i + 1] = $this->getUrlParams(null, $this->count, ($i - $pageOffset) * $this->count + $offset);
		}
		return array(
			'index' => $page + 1,
			'urls' => $list,
			'first' => ($l > 0 ? $this->getUrlParams(null, $this->count, 0) : null),
			'prev' => ($page > 0 ? $list[$page] : null),
			'next' => ($page < $pagesCount - 1 ? $list[$page + 2] : null),
			'last' => ($r < $pagesCount - 1 ? $this->getUrlParams(null, $this->count, ($pagesCount - 1 - $pageOffset) * $this->count + $offset) : null),
			'count' => ( ($page == $pagesCount - 1) ? ($this->totalCount - ($page - $pageOffset) * $this->count - $offset) : $this->count),
			'totalCount' => $this->totalCount,
			'pagesCount' => $pagesCount,
		);
	}

	public function getId() {
		return $this->id;
	}

	public function getActiveColumnDBName() {
		return $this->columns[$this->activeColumnIndex][1];
	}

	public function getActiveDirDBName() {
		return $this->dirKeywords[$this->activeDirIndex][1];
	}

	public function getParamNameOrderBy() {
		return $this->paramOrderBy;
	}

	public function getParamNameOrderDir() {
		return $this->paramOrderDir;
	}

	public function getParamNameCount() {
		return $this->paramCount;
	}
	
	public function getParamNameFrom() {
		return $this->paramFrom;
	}

	public function getOrderBy() {
		if (!empty($this->columns))
			return $this->columns[$this->activeColumnIndex][1];
		else
			return null;
	}

	public function getOrderDirection() {
		if (!empty($this->dirKeywords))
			return $this->dirKeywords[$this->activeDirIndex][1];
		else
			return null;
	}

	public function getCount() {
		return $this->count;
	}

	public function getFrom() {
		return $this->from;
	}

	public function setFrom($from) {
		$this->from = $from;
	}

	public function getMaxPages() {
		return $this->maxPages;
	}

	public function setMaxPages($maxPages) {
		$this->maxPages = $maxPages;
	}

	public function isPost() {
		return $this->post;
	}

	public function setPost($post) {
		$this->post = $post;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * WARNING: PEAR::DB specific
	 * @param $sort if false then no query modifier for sorting will be produced, if true then modofier is produced only if necessary
	 * @param $limit if false then no query modifier for sorting will be produced, if true then modofier is produced only if necessary
	 */
	public function getSqlWhere(Database &$db, $sort = true, $limit = true) {
	/*
		$query = '';
		if ($sort) {
			$orderBy = $this->getOrderBy();
			if (isset($orderBy))
				$orderDir = $this->getOrderDirection();
			else
				$orderDir = null;
			$query .= $db->getSortQuery($orderBy, $orderDir);
		}
		if ($limit) {
			$count = $this->getCount();
			if (isset($count))
				$from = $this->getFrom();
			else
				$from = null;
			$query .= ' ' . $db->getLimitQuery($count, $from);
		}
		return $query;
	*/
	}

	/**
	 * @param $url Url Wrapper for URL of page where lister should direct links.
	 * @param $counter For making unique HTML DOM IDs. (When more listers reside on one page.)
	 * @param $templateParams Additional params that should be passed to template.
	 * @param $template File path of PHTML template. (When empty, default will be used.)
	 */
	public function getOutput(Url $url = null, $counter = null, array $templateParams = null, $template = null) {
		if (empty($template)) {
			if (empty($this->template))
				$template = ($this->post ? 'Template/ListerPost.phtml' : 'Template/Lister.phtml');
			else
				$template = $this->template;
		}
		if (!isset($url))
			$url = new Url();
		if (!isset($templateParams))
			$templateParams = array();
		return Utils::processTemplate($template, array( 'lister' => &$this, 'url' => &$url , 'counter' => $counter, 'params' => $templateParams), true);
	}

	/**
	 * Call before iterating paged items (required only before later runs when iterating more than once)
	 */
	public function initIteration() {
		$this->iterationCounter = -1;
	}

	/**
	 * Call to determine action for iterating paged items
	 * @returns Lister::ITER_BREAK = item count per page was listed so can break iteration; Lister::ITER_SKIP = iteration didn't reached first item to be listed yet; Lister::ITER_DO = current item should be displayed;
	 */
	public function getIterationStatus() {
		++$this->iterationCounter;
		if ($this->iterationCounter >= $this->getFrom() + $this->getCount())
			return self::ITER_BREAK;
		else if ($this->iterationCounter < $this->getFrom())
			return self::ITER_SKIP;
		else
			return self::ITER_DO;
	}

}

?>
