function getContent(el)
{
    var websiteId = parseInt(el.value);
    $.ajax({
        url : HOST_PATH + "admin/robot/getrobotfilecontent/websiteId/" + websiteId,
        dataType : "json",
        type : "post",
        success : function(data) { 
            if (data != null) {     
                $("#robotContent").show();
                $("#updateButton").show();
                $("#fileContent").html(data);
            } else {
                $("#robotContent").show();
                $("#updateButton").show();
                $("#fileContent").html();
            }
        }
    });
}