$(document).ready(function() {
    $.get(http, function (data) {
      $('#'+divId).html(data);
      console.log('Load of '+ link +' was performed with ajax.');
    });
});