<?php
	$MALClasses = array("MangaSearch","MangaPage");
	$SQLClasses = array("Column","SQL","User","UserLink","AltTitle","Manga","Anime","Gift","GiftType");
	$SESSIONClasses = array("Rest","Auth");
	if(!empty($MALClasses))foreach($MALClasses as $class)require(LIBS_PATH."classes/MAL/".$class.".class.php");
	if(!empty($SQLClasses))foreach($SQLClasses as $class)require(LIBS_PATH."classes/SQL/".$class.".class.php");
	if(!empty($SESSIONClasses))foreach($SESSIONClasses as $class)require(LIBS_PATH."classes/Session/".$class.".class.php");

	$ALLClasses = $MALClasses+$SQLClasses+$SESSIONClasses;
	function isClass($name)
	{
		return (in_array($name,$GLOBALS['ALLClasses']));
	}

	function classAndTable($_name, $_classOrTable)
	{
		if($_name && $_classOrTable)
		{
			$_name = strtolower($_name);
			$_classOrTable = strtolower($_classOrTable);
			if($_classOrTable = "class")
			{
				if($_name == "gift") $return = "gifts";
				elseif($_name == "gifttype") $return = "gifts_types";
				elseif($_name == "manga") $return = "mangas";
				elseif($_name == "anime") $return = "animes";
				elseif($_name == "user") $return = "users";
				elseif($_name == "alttitle") $return = "mangas_animes_altTitles";
				elseif($_name == "userlink") $return = "users_links";
				else $return = FALSE;
			}
			elseif($_classOrTable = "table")
			{
				if($_name == "gifts") $return = "Gift";
				elseif($_name == "gifts_types") $return = "GiftType";
				elseif($_name == "mangas") $return = "Manga";
				elseif($_name == "animes") $return = "Anime";
				elseif($_name == "users") $return = "User";
				elseif($_name == "mangas_animes_altitles") $return = "AltTitle";
				elseif($_name == "users_links") $return = "UserLSink";
				else $return = FALSE;
			}
		}else return FALSE;

		return $return;
	}
	function hasUserLink($_object)
	{
		if($_object)
		{
			$hasUserLink = array(
				"gift",
				"manga",
				"anime"
			);
			$_object = strtolower($_object);
			return in_array($_object, $hasUserLink);
		}else return FALSE;
	}

	function getObjectByName($name, $id = FALSE)
	{
		$name = strtolower($name);
		if($name == "gift") return new Gift($id);
		elseif($name == "gifttype") return new GiftType($id);
		elseif($name == "manga") return new Manga($id);
		elseif($name == "anime") return new Anime($id);
		elseif($name == "user") return new User($id);
		elseif($name == "alttitle") return new AltTitle($id);
		elseif($name == "userlink") return new UserLink($id);
		else return FALSE;
	}

?>
