<?php
class UserLink extends SQLClass
{
	private $manga_statuses = array(
		0 => "ERROR!",
		1 => "Reading",
		2 => "Completed",
		3 => "On Hold",
		4 => "Dropped",
		6 => "Plan to Read"
	);
	
	private $anime_statuses = array(
		0 => "ERROR!",
		1 => "Watching",
		2 => "Completed",
		3 => "On Hold",
		4 => "Dropped",
		6 => "Plan to Watch"
	);
	
	private $oldGiftPosition = FALSE;
	
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="users_id", $type="id");
		$this->addColumn($_name="mangas_id", $type="id_empty");
		$this->addColumn($_name="animes_id", $type="id_empty");
		$this->addColumn($_name="gifts_id", $type="id_empty");
		$this->addColumn($_name="manga_anime_status", $type=false, $_value=0, $_size=1, $_empty=true);
		$this->addColumn($_name="manga_anime_parts", $type=false, $_value=0, $_size=5, $_empty=true);
		$this->addColumn($_name="manga_anime_score", $type=false, $_value=0, $_size=2, $_empty=true);
		$this->addColumn($_name="manga_anime_user", $type=false, $_value="", $_size=64, $_empty=true);
		$this->addColumn($_name="manga_anime_last_updated", $type="datetime");
		$this->addColumn($_name="gift_position", $type=false, $_value=0, $_size=5, $_empty=true);
		$this->addColumn($_name="gift_show", $type="boolean");
		$this->addColumn($_name="gift_mother_id", $type="id_empty");
		$this->_table = "users_links";
	}
	
	public function getStatus()
	{
		$status = $this->getValue("manga_anime_status");
		if($status)
		{
			if($this->getValue("mangas_id")) $status = $this->manga_statuses[$status];
			elseif($this->getValue("animes_id")) $status = $this->anime_statuses[$status];
		}else $status = "ERROR!";
		return $status;
	}
	
	protected function AfterCONSTRUCT()
	{
		$this->oldGiftPosition = $this->getValue("gift_position");
		return true;
	}
	
	protected function AfterCOMMIT()
	{
		if($this->getValue("gifts_id"))
		{
			SQL_SEND_QUERY("UPDATE ".$this->getTable()." SET gift_position = gift_position + 1 WHERE gift_position >= '".$this->getValue("gift_position").
				"' AND gifts_id <> '".$this->getValue("gifts_id")."' AND users_id = '".$this->getValue("users_id")."'");
			$oldPosition = $this->oldGiftPosition;
			if($oldPosition)
			{
				SQL_SEND_QUERY("UPDATE ".$this->getTable()." SET gift_position = gift_position - 1 WHERE gift_position > '".$oldPosition.
				"' AND gifts_id <> '".$this->getValue("gifts_id")."' AND users_id = '".$this->getValue("users_id")."'");
				//debug($oldPosition);
			}
		}
	}
	
	protected function AfterREMOVE()
	{
		if($this->getValue("gifts_id"))
		{
			return SQL_SEND_QUERY($_query="UPDATE ".$this->getTable()." SET gift_position = gift_position - 1 WHERE gift_position > '".$this->getValue("gift_position").
			"' AND users_id = '".$this->getValue("users_id")."'");
		}
		
	}
}
?>