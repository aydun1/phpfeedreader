var opts = {
  left: "20em",
  lines: 13,
  length: 5,
  width: 3,
  radius: 5,
};
var interval = setInterval( increment, 1000 );



function increment(){
last_updated += 1;
$(function() {
  console.log(last_updated);
  if(last_updated > 300) {
    last_updated=0;
    console.log("Updating");
    requestUpdate();
  }
});
}



function requestUpdate() {
  $("#heading").spin(opts);
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
  $("#updated").html("Updated seconds ago");
  $("#container .post").sort(sortDescending).appendTo("#container");
  $("#heading").spin(false);
  });
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
