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
 ?>
