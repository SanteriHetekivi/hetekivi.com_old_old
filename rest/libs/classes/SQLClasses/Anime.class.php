<?php
class Anime extends SQLClass
{
	
	private $statuses = array(
		0 => "ERROR!",
		1 => "",
		2 => "",
		3 => "",
		4 => "",
		5 => "",
		6 => "",
		7 => "",
		8 => "",
		9 => "",
		10 => ""
	);
	
	protected $_altTitles = array();
	
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="mal_id", $type="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="episodes", $type=false, $_value=0, $_size=5, $_empty=true);
		$this->addColumn($_name="status", $type=false, $_value=0, $_size=1, $_empty=true);
		$this->addColumn($_name="image", $type="url");
		$this->_table = "animes";
		return TRUE;
	}
	
	protected function Before_COMMIT()
	{
		$this->addAltTitles();
	}
	
	public function setAltTitles($altTitles)
	{
		if(is_array($altTitles)) $this->_altTitles=$altTitles;
	}
	
	public function getStatus($_source)
	{
		if($_source == "user" && $this->_userLink) return $this->_userLink->getStatus();
		else return $this->statuses[$this->getValue("status")];
	}
	
	public function getAltTitles()
	{
		return $this->_altTitles;
	}
	
	private function addAltTitles()
	{
		$altTitles = $this->getAltTitles();
		if(!empty($altTitles))
		{
			foreach($this->_altTitles as $title)
			{
				$mAltTitle = new AltTitle(array("mangas_animes_id"=>$this->getValue("id"),"title"=>$title, "manga_or_anime"=>"anime"));
				$mAltTitle->COMMIT();
			}
		}
	}
}
?>