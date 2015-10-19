<?php
class AltTitle extends SQLClass
{
	protected function initializeValues($_pass_to_intiliazing=FALSE)
	{
		$this->addColumn($_name="id");
		$this->addColumn($_name="mangas_animes_id",$type="id");
		$this->addColumn($_name="title");
		$this->addColumn($_name="manga_or_anime", $type=false, $_value="", $_size=5, $_empty=false);
		$this->_table = "mangas_animes_altTitles";
	}
	
	public function setMangaOrAnime($_manga_or_anime)
	{
		if($_manga_or_anime==="manga")
		{
			$old = $this->getVal("mangas_id");
			
			$name = "mangas";
			
		}
		elseif($_manga_or_anime==="anime")
		{
			$name = "animes";
		}else return FALSE;
		
		
	}
}
?>