var mangaSearch = function ()
{
  var mangaPage = new String();
  var fields = new Object();

  this.getMangaPage = function ()
  {
    return mangaPage;
  }
  this.getCurrentMangaPage = function ()
  {
    mangaPage = $('select[name="mangaPage"]').val();
  }
  this.getInput = function ()
  {
    var inputs = $("form").serializeArray();
    $.each(inputs, function(i, input)
    {
      if(isEmpty(fields[input.name])) fields[name] = {value : input.value, options:""};
      else fields[input.name]["value"] = input.value;
    });
  }
  this.getFields = function ()
  {
    this.getCurrentMangaPage();

    $.getJSON("rest/index.php/mangaSearchFields", {mangaPage:mangaPage},
      function(result)
      {
        if(!isEmpty(result))
        {
          $.each(result, function(name, field)
          {
            if(isEmpty(fields[name])) fields[name] = {value : "", options:field};
            else fields[name].options = field;
          });
          search.setupFields();
          if(isEmpty(mangaPage)) search.getFields();
        }
      }
    );

  }
  this.setupFields = function ()
  {
    if(!isEmpty(fields))
    {
      this.getInput();
      $.each(fields, function(name, field)
      {
        var query = 'select[name="'+name+'"]';
        if(!isEmpty(field.options))
        {
          var options = "";
          $.each(field.options, function(text, value){
            var selected = (field.value == value)?"selected":"";
            options += "<option "+selected+" value='" + value + "'>" + text + "</option>";
          });
          $(query).html(options);
        }
        if(!isEmpty(field.value)) $(query).selectpicker('val', field.value);
        $(query).selectpicker('refresh');
      });
    }
    else this.getFields();
  }
  this.getParameters = function()
  {
    return $("#mangaSearchForm").serialize();
  }
  this.SEARCH = function ()
  {
    loadTable(this.getParameters());
  }
}

var search = new mangaSearch();

$(document).ready(function(){
  $('.selectpicker').selectpicker();
  if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
    $('.selectpicker').selectpicker('mobile');
  }
  $('input[name="completed"]').bootstrapSwitch();
  $("#startMangaSearch").click(function(){search.SEARCH();});
  $('select[name="mangaPage"]').change(function(){search.getFields();});
  search.setupFields();
});

var loadTable = function (parameters)
{
  if (parameters === undefined) parameters = "";
  var url = 'rest/index.php/mangaSearch?'+parameters;
  $('#mangaSearch_table').bootstrapTable('refresh', {url: url});
}

function operateFormatter(value, row, index)
{
  return [
      '<button id="getMangaImage" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span>Get Image</button></br></br>',
      '<button id="googleManga" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Google</button></br></br>',
      '<button id="MALManga" type="button" class="btn btn-danger"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span>MAL</button>'
  ].join('');
}
window.operateEvents =
{
    'click #getMangaImage': function (e, value, row, index)
    {
      $.get("rest/index.php/mangaSearchImage",{url:row.url, mangaPage:search.getMangaPage()}, function(data, status)
      {
        $('.img-rounded', 'tr[data-index="'+index+'"]').attr("src",data);
      });
    },
    'click #googleManga': function (e, value, row, index)
    {
      window.open('https://www.google.fi/search?q='+row.searchTitle+'+manga', '_blank');
    },
    'click #MALManga': function (e, value, row, index)
    {
      window.open('https://www.google.fi/search?q='+row.searchTitle+'+myanimelist+manga', '_blank');
    }
};
