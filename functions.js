$(function() {

  //compareDates() ;
  
  if(last_updated > 300) {
    requestUpdate();
  }
});




function requestUpdate() {
  $.getJSON( "index.php?mode=update", function( data ) {
    var items = [];
    $.each( data, function (key, val ) {
      var post_id = "#" + val.blogMd5;
      if($(post_id).attr("data-date") != val.postDateTime) {
        $(post_id).attr("data-date", val.postDateTime)
        $(post_id + " .post_title a").attr("href", val.postUrl);
        $(post_id + " .post_title a").text(val.postTitle);
        $(post_id + " .post_date i").text("- " + val.postRelativeDate);
        $(post_id + " .post_description").html(val.postDescription);		
      }
    });
  });
  $("#container .post").sort(sortDescending).appendTo("#container");
};




function sortDescending(a, b) {
  var d1  = $(a).attr("data-date");
  var d2  = $(b).attr("data-date");
  return d1 < d2 ? 1 : -1;
};


function compareDates() {
  $.getJSON( "index.php?mode=last-updated", function( data ) {
    var items = [];
    $.each( data, function (key, val ) {
      if($("#" + val.blogMd5).attr("data-date") != val.postDateTime) {
        //Need to replace div with new div here
        alert($("#" +val.blogMd5).attr("data-date"));				
      }
    });
  });
};