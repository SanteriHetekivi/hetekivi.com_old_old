<?php
class MangaSearch
{
	private $_mangaPage = FALSE;
	private $_minChapters = 0;
	private $_maxChapters = PHP_INT_MAX;
	private $_exludedManga = Array();
	private $_searchResult = Array();


	function __construct($mangaPage, $included=array(), $exluded=array(), $username=FALSE, $completed=TRUE, $minChapters = FALSE, $maxChapters = FALSE, $_excluded_statuses=FALSE)
	{
		if(isset($mangaPage))
		{
			$this->setMangaPage(new MangaPage($mangaPage, $included, $exluded, $completed));
			if($minChapters) $this->setMinChapters($minChapters);
			if($maxChapters) $this->setMaxChapters($maxChapters);
			if($username) $this->makeExludedManga($username, $_excluded_statuses);
		}else return FALSE;
	}

	public function setMangaPage($mangaPage){ $this->_mangaPage = (is_object($mangaPage))?$mangaPage:FALSE;}
	public function getMangaPage(){ return $this->_mangaPage;}

	public function setMinChapters($minChapters)  { $this->_minChapters = (is_numeric($minChapters))?$minChapters:0; }
	public function getMinChapters() {return $this->_minChapters;}

	public function setMaxChapters($maxChapters)  { $this->_maxChapters = (is_numeric($maxChapters))?$maxChapters:PHP_INT_MAX; }
	public function getMaxChapters() {return $this->_maxChapters;}

	public function makeExludedManga($username, $_excluded_statuses=false)
	{
		$exludedManga = SQL_getMangaTitles(getUserID(),$username,true, $_excluded_statuses);
		if(is_array($exludedManga))
		{
			$this->_exludedManga = array_filter(array_unique(array_map('clean', $exludedManga)));
		}else return FALSE;
	}
	public function getExludedManga() {return $this->_exludedManga;}

	public function SEARCH()
	{
		$mangaPage = $this->getMangaPage();
		if(!is_object($mangaPage)) return FALSE;
		$result = Array();
		$id = 0;
		$samePage = FALSE;
		$first = "";
		$query = $mangaPage->getQuery();
		$exludedManga = $this->getExludedManga();
		do
		{
			libxml_use_internal_errors(true);
			$resultPage = new DOMDocument();
			$resultPage->loadHTMLFile($mangaPage->getSearchUrl());
			$xpath = new DOMXPath($resultPage);
			$linkClasses = $xpath->query($query);
			//die($mangaPage->getSearchUrl());
			if(is_object($linkClasses))
			{
				if(is_object($linkClasses->item(0)) && $first != $linkClasses->item(0)->nodeValue)
				{
					$first = $linkClasses->item(0)->nodeValue;
					foreach($linkClasses as $linkClass)
					{
						$new = TRUE;
						$title = $linkClass->nodeValue;
						$searchTitle = clean($title);
						if(!in_array($searchTitle,$exludedManga))
						{
							$result[$id]["title"] = $title;
							$result[$id]["url"]= $linkClass->getAttribute('href');
							$result[$id]["searchTitle"]= urlencode($searchTitle);
							++$id;
						}
					}
				}else $samePage = TRUE;
			}
			$mangaPage->nextPage();
		}while(is_object($linkClasses) && $samePage === FALSE);
		$this->setSearchResult($result);
		return TRUE;
	}



	public function getSearchResult() {return $this->_searchResult;}
	public function setSearchResult($searchResult) { $this->_searchResult = (is_array($searchResult))?$searchResult:Array(); }


}
?>
