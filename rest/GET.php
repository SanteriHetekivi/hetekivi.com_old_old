<?php
  function runFunction($function, $variables)
  {
    $data = array();
    switch ($function) {
      case 'giftList':
        $data=giftList($variables);
        break;
      case 'login':
        $data=checkLogin();
        break;
      case 'logout':
        if(logout()) BACK();
        else ERROR("Logout failed!");
        break;
      case 'gift_positions':
        $new = (isset($variables["new"]) && $variables["new"] && $variables["new"] != "false")?true:false;
        $data = SQL_getGiftPositions(getUserID(), $new);
        break;
      case 'gift_types':
        $data = SQL_SELECT_COLUMN($_table="gifts_types", $_column="title");
        break;
      case 'objectColumns':
        if(isset($variables["object"]))
        {
          $link = new UserLink();
          $object = getObjectByName($variables["object"]);
          if(is_object($object))
          {
            $data = array_merge($object->getColumnNames(), $link->getColumnNames());
          }
        }
        break;
      case 'getOldGifts':
        $data = getOldGifts();
        break;
      case 'gift':
        $data = gift($variables);
        break;
      case 'mal':
        $data = mal($variables);
        break;
      case 'mangaSearchFields':
        $mangaPage = (isset($variables["mangaPage"]))?$variables["mangaPage"]:false;
        $data = mangaSearchFields($mangaPage);
        break;
      case 'mangaSearch':
        $data = mangaSearch($variables);
        break;
      case 'mangaSearchImage':
        $data = mangaSearchImage($variables);
        break;
      case 'error':
        $error = getSessionData("error");
        setSessionData("error", false);
        die($error);
        break;
      case 'message':
        $message = getSessionData("message");
        setSessionData("message", false);
        die($message);
        break;
      default:
        return false;
        break;
    }
    //if($function=="mangaSearch") die(json_encode(array(array("title"=>json_encode($variables)))));

    echo json_encode($data, JSON_PRETTY_PRINT);
    return true;
  }

  function giftList($variables)
  {
    $order = (isset($variables["order"]))?$variables["order"]:"desc";
    $limit = (isset($variables["limit"]) && $variables["limit"] != "ALL")?$variables["limit"]:false;
    $offset = (isset($variables["offset"]))?$variables["offset"]:false;
    $userId=getUserID();
    if($userId)
    {
      $gifts = SQL_SELECT(
        $_columns = FALSE,
        $_table="gifts",
        $_joinTable = "users_links",
        $_joinTableId = "gifts_id",
        $_where = "users_links.users_id='".$userId."'",
        $_order = "users_links.gift_position ".$order,
        $_offset = $offset,
        $_limit = $limit,
        $_object = FALSE,
        $_onlyFirstRow = FALSE);
      if(is_array($gifts))
      {

        $giftTypes = SQL_getGiftTypes();
        $return = array();
        foreach ($gifts as $key => $gift)
        {
          foreach ($gift as $column => $value) {
            if($column == "gifts_types_id")
            {
              $row["type"] = (isset($giftTypes[$value]))?$giftTypes[$value]:"";
            }
            elseif ($column == "price")
            {
              $row["eur"]= euroString($value);
            }
            /*elseif ($column == "title")
            {
              $value=json_encode($variables, JSON_PRETTY_PRINT);
            }*/
            $row[$column] = $value;
          }
          $return["rows"][] = $row;
        }
        $return["total"] = SQL_COUNT_ROWS(
          $_table="users_links",
          $_where="gifts_id>0 AND users_id='".$userId."'"
        );
      }else ERROR("Getting gifts failed!");
    }else ERROR("Getting gifts failed!");
    return $return;
  }

  function giftTypes()
  {
    return SQL_SELECT(
      $_columns = "title",
      $_table="gifts_types",
      $_joinTable = FALSE,
      $_joinTableId = FALSE,
      $_where = FALSE,
      $_order = FALSE,
      $_offset = FALSE,
      $_limit = FALSE,
      $_object = FALSE,
      $_onlyFirstRow = FALSE);
  }

  function getOldGifts()
  {
    $userId=getUserID();
    if($userId)
    {
        return SQL_SELECT(
        $_columns = "title",
        $_table="gifts",
        $_joinTable = FALSE,
        $_joinTableId = FALSE,
        $_where = "id NOT IN (SELECT gifts_id FROM users_links WHERE gifts_id > 0 AND users_id = '".$userId."') ",
        $_order = "title ASC",
        $_offset = FALSE,
        $_limit = FALSE,
        $_object = FALSE,
        $_onlyFirstRow = FALSE);
      }
      return false;
  }

  function gift($variables)
  {
    if(isset($variables["id"]))
    {
      $gift = SQL_SELECT_ID($_table="gifts", $_id=$variables["id"]);
      if(is_array($gift) && isset($gift["id"]))
      {
        $gift["gifts_id"]=$gift["id"];
        unset($gift["id"]);
        return $gift;
      }
    }
    return FALSE;
  }

  function mal($variables)
  {

    $ob = (isset($variables["manga_or_anime"]) && $variables["manga_or_anime"] == "anime")?$variables["manga_or_anime"]:"manga";
    $sort = (isset($variables["sort"]))?$variables["sort"]:"manga_anime_last_updated";
    if($sort=="mal_status") $sort = "status";
    elseif($sort=="user_status") $sort = "manga_anime_status";
    elseif($sort=="completed_parts") $sort = "manga_anime_parts";
    $order = (isset($variables["order"]))?$variables["order"]:"desc";
    $limit = (isset($variables["limit"]) && $variables["limit"] != "ALL")?$variables["limit"]:false;
    $offset = (isset($variables["offset"]))?$variables["offset"]:false;
    $search = (isset($variables["search"]))?" AND title LIKE '%".$variables["search"]."%'":"";
    $table = $ob."s";
    $join_id = $ob."s_id";
    $userId=getUserID();
    if($userId)
    {
      $where = "users_id='".$userId."' AND ".$join_id.">'0'".$search;
      $objects = SQL_SELECT(
        $_columns = FALSE,
        $_table=$table,
        $_joinTable ="users_links",
        $_joinTableId = $join_id,
        $_where = $where,
        $_order = $sort." ".$order,
        $_offset = $offset,
        $_limit = $limit,
        $_object = FALSE,
        $_onlyFirstRow = FALSE);
      if(is_array($objects))
      {
        $return = array();
        foreach ($objects as $id => $object)
        {
          $object["mal_status"] = malStatus($object["status"], $mal_or_user="mal", $manga_or_anime=$ob);
          $object["user_status"] = malStatus($object["manga_anime_status"], $mal_or_user="user", $manga_or_anime=$ob);
          $parts = (isset($object["chapters"]))?$object["chapters"]:((isset($object["episodes"]))?$object["episodes"]:0);
          $object["completed_parts"] = $object["manga_anime_parts"]."/".$parts;
          //$object["completed_parts"] = json_encode($variables);
          $object["url"] = "https://www.google.fi/search?q=".urlencode($object["title"]);
          $return["rows"][] = $object;
        }
        if(!isset($return["rows"])) $return["rows"] = array();
        $return["total"] = SQL_COUNT_ROWS(
          $_table=$table,
          $_where=$where,
          $_joinTable = "users_links",
          $_joinTableId= $join_id
        );
      }else ERROR("Getting mal failed!");
    }else ERROR("Getting mal failed!");
    return $return;
  }

  function mangaSearchFields($mangaPage = false)
  {
    $data = array();
    $data["mangaPage"] = array(
      "MangaFox"=>"MangaFox",
      "Batoto"=>"Batoto",
      "Baka-Updates"=>"Baka-Updates"
    );
    if($mangaPage)
    {
      $genres["Baka-Updates"] = array("Action"=>"Action","Adult"=>"Adult","Adventure"=>"Adventure","Comedy"=>"Comedy","Doujinshi"=>"Doujinshi",
					"Drama"=>"Drama","Ecchi"=>"Ecchi","Fantasy"=>"Fantasy","Gender Bender"=>"Gender+Bender","Harem"=>"Harem",
					"Hentai"=>"Hentai","Historical"=>"Historical","Horror"=>"Horror","Josei"=>"Josei","Lolicon"=>"Lolicon","Martial Arts"=>"Martial+Arts",
					"Mature"=>"Mature","Mecha"=>"Mecha","Mystery"=>"Mystery","Psychological"=>"Psychological","Romance"=>"Romance","School Life"=>"School+Life",
					"Sci-fi"=>"Sci-fi","Seinen"=>"Seinen","Shotacon"=>"Shotacon","Shoujo"=>"Shoujo","Shoujo Ai"=>"Shoujo+Ai","Shounen"=>"Shounen","Shounen Ai"=>"Shounen+Ai",
					"Slice of Life"=>"Slice+of+Life","Smut"=>"Smut","Sports"=>"Sports","Supernatural"=>"Supernatural","Tragedy"=>"Tragedy","Yaoi"=>"Yaoi","Yuri"=>"Yuri");
			$genres["MangaFox"] = array("Action"=>"Action", "Adult"=>"Adult", "Adventure"=>"Adventure", "Comedy"=>"Comedy", "Doujinshi"=>"Doujinshi", "Drama"=>"Drama",
				"Ecchi"=>"Ecchi", "Fantasy"=>"Fantasy", "Gender Bender"=>"Gender+Bender", "Harem"=>"Harem", "Historical"=>"Historical", "Horror"=>"Horror", "Josei"=>"Josei",
				"Martial Arts"=>"Martial+Arts", "Mature"=>"Mature", "Mecha"=>"Mecha", "Mystery"=>"Mystery", "One Shot"=>"One+Shot", "Psychological"=>"Psychological",
				"Romance"=>"Romance", "School Life"=>"School+Life", "Sci-fi"=>"Sci-fi", "Seinen"=>"Seinen", "Shoujo"=>"Shoujo", "Shoujo Ai"=>"Shoujo+Ai", "Shounen"=>"Shounen",
				"Shounen Ai"=>"Shounen+Ai", "Slice of Life"=>"Slice+of+Life", "Smut"=>"Smut", "Sports"=>"Sports", "Supernatural"=>"Supernatural", "Tragedy"=>"Tragedy",
				"Webtoons"=>"Webtoons", "Yaoi"=>"Yaoi", "Yuri"=>"Yuri");
			$genres["Batoto"] = array("4-Koma"=>"40", "Action"=>"1", "Adventure"=>"2", "Award Winning"=>"39", "Comedy"=>"3",
					"Cooking"=>"41", "Doujinshi"=>"9", "Drama"=>"10", "Ecchi"=>"12", "Fantasy"=>"13",
					"Gender Bender"=>"15", "Harem"=>"17", "Historical"=>"20", "Horror"=>"22", "Josei"=>"34",
					"Martial Arts"=>"27", "Mecha"=>"30", "Medical"=>"42", "Music"=>"37", "Mystery"=>"4",
					"Oneshot"=>"38", "Psychological"=>"5", "Romance"=>"6", "School Life"=>"7", "Sci-fi"=>"8",
					"Seinen"=>"32", "Shoujo"=>"35", "Shoujo Ai"=>"16", "Shounen"=>"33", "Shounen Ai"=>"19",
					"Slice of Life"=>"21", "Smut"=>"23", "Sports"=>"25", "Supernatural"=>"26", "Tragedy"=>"28",
					"Webtoon"=>"36", "Yaoi"=>"29", "Yuri"=>"31", "[no chapters]"=>"44");
      if(isset($genres[$mangaPage]))
      {
        $data["included[]"] = $genres[$mangaPage];
        $data["excluded[]"] = $genres[$mangaPage];
      }
      $data["excluded_statuses[]"] = array_flip(malStatus("kaikki", $mal_or_user="user", $manga_or_anime="manga"));
    }
    return $data;
  }

  function mangaSearch($variables)
  {
    $data = array();
    if(isset($variables) && !empty($variables) && isset($variables["mangaPage"]))
    {
      $included = (isset($variables["included"]))?$variables["included"]:array();
      $excluded = (isset($variables["excluded"]))?$variables["excluded"]:array();
      $completed = (isset($variables["completed"]))?TRUE:FALSE;
      $minChapters = (isset($variables["minChapters"]))?$variables["minChapters"]:FALSE;
      $maxChapters = (isset($variables["maxChapters"]))?$variables["maxChapters"]:FALSE;
      $excluded_statuses = (isset($variables["excluded_statuses"]))?$variables["excluded_statuses"]:FALSE;
      $mangaPage = $variables["mangaPage"];
      $mMangaSearch = new MangaSearch($mangaPage, $included, $excluded, getUserID(), $completed, $minChapters, $maxChapters, $excluded_statuses);
  		$mMangaSearch->SEARCH();
  		$result = $mMangaSearch->getSearchResult();
  		foreach($result as $id => $manga)
  		{
        $row = array();
        foreach ($manga as $key => $value) {
          $row[$key]=$value;
        }
        $row["image"] = "https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/600px-No_image_available.svg.png";
        //getMangaImage($row["url"], $mangaPage);
        $data[] = $row;
  		}
      //$data["total"] = count($data["rows"]);
      setSessionData("MangaSearch", $data);
    }
    else
    {
      $data = getSessionData("MangaSearch");

    }
    return $data;
  }
  function mangaSearchImage($variables)
  {
    if(isset($variables["url"]) && isset($variables["mangaPage"]))
    {
      die(getMangaImage($variables["url"], $variables["mangaPage"]));
    }
  }
?>
