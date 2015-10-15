<?php
  function SQL_getGiftTypes()
  {
    return SQL_SELECT(
      $_columns       = "title",
      $_table         = "gifts_types",
      $_joinTable     = FALSE,
      $_joinTableId   = FALSE,
      $_where         = FALSE,
      $_order         = FALSE,
      $_offset        = FALSE,
      $_limit         = FALSE,
      $_object        = FALSE,
      $_onlyFirstRow  = FALSE
    );
  }
  function SQL_getGiftsUserDoesNotHaveTitles($userid)
	{
		$gifts = ($userid)?SQL_SELECT_COLUMN($_table="gifts", $_column="title", $_where="id NOT IN (SELECT gifts_id FROM users_links WHERE gifts_id > 0 AND users_id = '".$userid."') "):Array();
		return $gifts;
	}

	function SQL_getGiftPositions($userid, $new = false)
	{
		$positions = array();
		$giftPositions = SQL_SELECT_COLUMN($_table="users_links", $_column="gift_position", $where="users_id='".$userid."' AND gifts_id>0");
		if(is_array($giftPositions) && !empty($giftPositions))
		{
			foreach($giftPositions as $position)$positions[$position]= $position;
			if($new) $positions[max($positions) + 1] = max($positions) + 1;
		}else $positions[1] = 1;
		asort($positions);
		return $positions;
	}

  function SQL_loadMyanimelistXMLToDataspace()
	{
		$file = simplexml_load_file(TMP_XML_FILE_PATH);
		$user = (string)$file->myinfo->user_name;
		$alreadyIn = SQL_getMangaTitles(false,$user);
    $ob = (isset($file->manga))?"manga":((isset($file->anime))?"anime":false);
		if(isset($user) && $ob)
		{
			foreach($file->$ob as $series)
			{
				$data = array();
				$data["title"] = (string)$series->series_title;
				if(isset($data["title"]) && !empty($data["title"]))
				{
          $malid= "series_".$ob."db_id";
					$data["mal_id"] = (string)$series->$malid;
					$data["id"] = SQL_GET_ID($ob."s", "mal_id = '".$data["mal_id"]."'");
					$data["status"] = (string)$series->series_status;
					if($ob=="manga")$data["chapters"] = (string)$series->series_chapters;
          elseif($ob=="anime")$data["episodes"] = (string)$series->series_episodes;
					$data["image"] = (string)$series->series_image;

					$synonyms = explode("; ", $series->series_synonyms);
					$altTitles = array();
					foreach($synonyms as $synonym)$altTitles[] = clean($synonym);
					$altTitles = array_filter($altTitles);

					$userLink["manga_anime_user"] = (string)$user;
					$userLink["manga_anime_status"] = (int)$series->my_status;
					$userLink["manga_anime_score"] = (int)$series->my_score;
          if($ob=="manga")$userLink["manga_anime_parts"] = (int)$series->my_read_chapters;
          elseif($ob=="anime")$userLink["manga_anime_parts"] = (int)$series->my_watched_episodes;
					$userLink["manga_anime_last_updated"] = toMysqlDateTime((int)$series->my_last_updated);
          if($ob=="manga") $mObject = new Manga($data);
          elseif($ob=="anime") $mObject = new Anime($data);

					if(!empty($altTitles)) $mObject->setAltTitles($altTitles);
					foreach($userLink as $column => $value) $mObject->setLinkedValue($column,$value);
					$mObject->COMMIT();
				}
			}
		}else return FALSE;
		return TRUE;
	}

	function SQL_getMangaTitles($_userID = FALSE, $_manga_anime_user = FALSE, $_alsoAltTitles = FALSE, $_allowed_status = FALSE)
	{
		$where = "mangas_id > 0 ";
		if($_userID) $where .= "users_id='".$_userID."' ";
		if($_manga_anime_user) $where .= "manga_anime_user='".$_manga_anime_user."' ";
		if($_allowed_status && is_array($_allowed_status)) $where .= "AND manga_anime_status IN ('".implode("','", $_allowed_status)."') ";
		$titles = SQL_SELECT_COLUMN("mangas", "title", "id IN (SELECT mangas_id FROM users_links WHERE ".$where." ) ");
		if(is_array($titles))
		{
			$ids = SQL_SELECT_COLUMN("users_links", "mangas_id", $where);
			if($_alsoAltTitles) $titles = $titles + SQL_SELECT_COLUMN("mangas_animes_altTitles", "title", "mangas_animes_id IN (" . implode(",",$ids) . ") AND manga_or_anime='manga'");
			return array_filter(array_unique($titles));
		}else return array();
	}
 ?>
