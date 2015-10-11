<?php
class MangaPage
{
	private $_page = '';
	private $_pages = Array("MangaFox","Batoto");
	private $_urls = Array("MangaFox"=>"http://mangafox.me", "Batoto"=>"http://bato.to");
	private $_genres = Array();
	private $_included = Array();
	private $_excluded = Array();
	private $_pageNo = 1;
	private $_completed = TRUE;
	
	function __construct($page = FALSE, $included = FALSE, $exluded = FALSE, $completed = TRUE) 
	{
		if($page) $this->setPage($page);
		if($included) $this->setIncluded($included);
		if($exluded) $this->setExluded($exluded);
		if($minChapters) $this->setMaxChapters($minChapters);
		if($maxChapters) $this->setMinChapters($maxChapters);
		if($pageNo) $this->setPageNo($pageNo);
		$this->setCompleted($completed);
	}
	
	public function setPage($page)
	{
		if(isset($page) && $this->isSupportedPage($page))
		{
			$this->_page = $page;
			$this->makeGenres();
			return TRUE;
		}else return FALSE;
	}
	
	public function getPage() {return $this->_page;}
	
	public function getUrl() {return (isset($this->getUrls()[$this->getPage()]))?$this->getUrls()[$this->getPage()]:FALSE; }
	
	public function getPages() {return $this->_pages;}
	
	public function getUrls() {return $this->_urls;}
	
	public function setGenres($genres) { $this->_genres = (is_array($genres))?$genres:Array(); }
	
	private function makeGenres()
	{
		if($this->getPage() == "Baka-Updates") $this->setGenres(array("Action"=>"Action","Adult"=>"Adult","Adventure"=>"Adventure","Comedy"=>"Comedy","Doujinshi"=>"Doujinshi",
					"Drama"=>"Drama","Ecchi"=>"Ecchi","Fantasy"=>"Fantasy","Gender Bender"=>"Gender+Bender","Harem"=>"Harem",
					"Hentai"=>"Hentai","Historical"=>"Historical","Horror"=>"Horror","Josei"=>"Josei","Lolicon"=>"Lolicon","Martial Arts"=>"Martial+Arts",
					"Mature"=>"Mature","Mecha"=>"Mecha","Mystery"=>"Mystery","Psychological"=>"Psychological","Romance"=>"Romance","School Life"=>"School+Life",
					"Sci-fi"=>"Sci-fi","Seinen"=>"Seinen","Shotacon"=>"Shotacon","Shoujo"=>"Shoujo","Shoujo Ai"=>"Shoujo+Ai","Shounen"=>"Shounen","Shounen Ai"=>"Shounen+Ai",
					"Slice of Life"=>"Slice+of+Life","Smut"=>"Smut","Sports"=>"Sports","Supernatural"=>"Supernatural","Tragedy"=>"Tragedy","Yaoi"=>"Yaoi","Yuri"=>"Yuri"));
		elseif($this->getPage() == "MangaFox") $this->setGenres(array("Action"=>"Action", "Adult"=>"Adult", "Adventure"=>"Adventure", "Comedy"=>"Comedy", "Doujinshi"=>"Doujinshi", "Drama"=>"Drama", 
				"Ecchi"=>"Ecchi", "Fantasy"=>"Fantasy", "Gender Bender"=>"Gender+Bender", "Harem"=>"Harem", "Historical"=>"Historical", "Horror"=>"Horror", "Josei"=>"Josei", 
				"Martial Arts"=>"Martial+Arts", "Mature"=>"Mature", "Mecha"=>"Mecha", "Mystery"=>"Mystery", "One Shot"=>"One+Shot", "Psychological"=>"Psychological", 
				"Romance"=>"Romance", "School Life"=>"School+Life", "Sci-fi"=>"Sci-fi", "Seinen"=>"Seinen", "Shoujo"=>"Shoujo", "Shoujo Ai"=>"Shoujo+Ai", "Shounen"=>"Shounen", 
				"Shounen Ai"=>"Shounen+Ai", "Slice of Life"=>"Slice+of+Life", "Smut"=>"Smut", "Sports"=>"Sports", "Supernatural"=>"Supernatural", "Tragedy"=>"Tragedy", 
				"Webtoons"=>"Webtoons", "Yaoi"=>"Yaoi", "Yuri"=>"Yuri"));
		elseif($this->getPage() == "Batoto") $this->setGenres(array("4-Koma"=>"40", "Action"=>"1", "Adventure"=>"2", "Award Winning"=>"39", "Comedy"=>"3",
					"Cooking"=>"41", "Doujinshi"=>"9", "Drama"=>"10", "Ecchi"=>"12", "Fantasy"=>"13", 
					"Gender Bender"=>"15", "Harem"=>"17", "Historical"=>"20", "Horror"=>"22", "Josei"=>"34", 
					"Martial Arts"=>"27", "Mecha"=>"30", "Medical"=>"42", "Music"=>"37", "Mystery"=>"4", 
					"Oneshot"=>"38", "Psychological"=>"5", "Romance"=>"6", "School Life"=>"7", "Sci-fi"=>"8", 
					"Seinen"=>"32", "Shoujo"=>"35", "Shoujo Ai"=>"16", "Shounen"=>"33", "Shounen Ai"=>"19", 
					"Slice of Life"=>"21", "Smut"=>"23", "Sports"=>"25", "Supernatural"=>"26", "Tragedy"=>"28", 
					"Webtoon"=>"36", "Yaoi"=>"29", "Yuri"=>"31", "[no chapters]"=>"44"));
		else return Array();
	}
	
	public function getGenres() {return $this->_genres;}
	
	public function setIncluded($included)  { $this->_included = (is_array($included))?$included:Array(); }
	 
	public function getIncluded() {return $this->_included;}

	public function setExluded($exluded)  { $this->_exluded = (is_array($exluded))?$exluded:Array(); }
	
	public function getExluded() {return $this->_excluded;}
	
	public function setPageNo($pageNo)  { $this->_pageNo = (is_numeric($pageNo))?$pageNo:1; }
	
	public function nextPage()  { $this->_pageNo = $this->getPageNo() + 1; }
	public function previousPage()  { $this->_pageNo = ($this->getPageNo() > 1)?$this->getPageNo()-1:1; }

	public function getPageNo() {return $this->_pageNo;}
	
	public function setCompleted($completed)  { $this->_completed = (is_bool($completed))?$completed:TRUE; }
	
	public function getCompleted()
	{
		if($this->getPage() == "MangaFox")
		{
			return ($this->_completed)?1:0;
		}
		elseif($this->getPage() == "Batoto")
		{
			return ($this->_completed)?'c':'i';

		}else return FALSE;
	}
	
	public function getGenresForUrl() 
	{
		$genres = '';
		$included = $this->getIncluded();
		$excluded = $this->getExluded();
		if(!empty($included) || !empty($excluded))
		{
			
			if($this->getPage() == "MangaFox")
			{
				foreach($this->getGenres() as $genre)
				{
					if(is_array($included) && in_array($genre, $included)) $genres .= "genres%5B$genre%5D=1&";
					elseif(is_array($excluded) && in_array($genre, $excluded)) $genres .= "genres%5B$genre%5D=2&";
					else $genres .= "genres%5B$genre%5D=0&";
				}
			}
			elseif($this->getPage() == "Batoto")
			{
				$genres = "genres=;";
				if(is_array($included))foreach($included as $include) $genres .= "i".$include.";";
				if(is_array($excluded))foreach($excluded as $exlude) $genres .= "e".$exlude.";";
				$genres = rtrim($genres, ";") . "&genre_cond=and&";
			}
		}
		return $genres;
	}
	
	public function getSearchUrl()
	{
		if($this->getPage() == "MangaFox")
		{
			return $this->getUrl()."/search.php?".$this->getGenresForUrl()."is_completed=".$this->getCompleted()."&advopts=1&sort=rating&order=za&page=".$this->getPageNo();
		}
		elseif($this->getPage() == "Batoto")
		{
			return $this->getUrl()."/search?".$this->getGenresForUrl()."completed=".$this->getCompleted()."&order_cond=rating&order=desc&p=".$this->getPageNo();
		}else return FALSE;
	}
	
	public function getQuery()
	{
		if($this->getPage() == "MangaFox")
		{
			return "//a[@class='series_preview manga_close']";
		}
		elseif($this->getPage() == "Batoto")
		{
			return "//a[starts-with(@href,'http://bato.to/comic/_/')]";
		}else return FALSE;
	}
	
	
	private function isSupportedPage($page){return (in_array($page,$this->getPages())); }
}
?>