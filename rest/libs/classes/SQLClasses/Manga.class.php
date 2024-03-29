<?php
class Manga extends SQLClass
{
	protected $_altTitles = array();
	private $statuses = array(
		0 => "ERROR!",
		1 => "Ongoing",
		2 => "Finished",
	);

	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="mal_id", $type="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="chapters", $type=false, $_value=0, $_size=5, $_empty=true);
		$this->addColumn($_name="status", $type=false, $_value=0, $_size=1, $_empty=true);
		$this->addColumn($_name="image", $type="url");
		$this->_table = "mangas";
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

	public function getAltTitles()
	{
		return $this->_altTitles;
	}

	public function getStatus($_source)
	{
		if($_source == "user" && $this->_userLink) return $this->_userLink->getStatus();
		else return $this->statuses[$this->getValue("status")];
	}

	private function addAltTitles()
	{
		$altTitles = $this->getAltTitles();
		if(!empty($altTitles))
		{
			foreach($this->_altTitles as $title)
			{
				$mAltTitle = new AltTitle(array("mangas_animes_id"=>$this->getValue("id"),"title"=>$title, "manga_or_anime"=>"manga"));
				$mAltTitle->COMMIT();
			}
		}
	}
}
?>
