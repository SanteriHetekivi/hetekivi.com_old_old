$(document).ready(function(){
  $("#manga_or_anime").bootstrapSwitch();
  $('#manga_or_anime').on('switchChange.bootstrapSwitch', function(event, state)
    {
      if(state) loadTable("manga");
      else loadTable("anime");
  });
});

var loadTable = function loadTable(manga_or_anime)
{
  var url = 'rest/index.php/mal?manga_or_anime='+manga_or_anime;
  $('#mal_table').bootstrapTable('refresh', {url: url});
}
