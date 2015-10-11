function bootcamp_table_imageFromUrl(value, row, index)
{
  return "<img src='"+value+"' class='img-rounded' width='304' height='236'>";
}
function bootcamp_table_urlToTitle(value, row, index)
{
  return "<a href='"+row.url+"'>"+value+"</a>";
}
