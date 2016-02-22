var CKcontent = false ;
$(document).ready(function(){
   // setup ckeditor and its configurtion
	CKEDITOR.replace( 'splashPageContent',
        {
            customConfig : 'config.js' ,
            toolbar :  'BasicToolbar'  ,
            width : "605" ,
            height : "250"

        });
	// setup ckeditor and its configurtion
	CKEDITOR.replace( 'splashPagePopularShops',
        {
            customConfig : 'config.js' ,
            toolbar :  'BasicToolbar'  ,
            width : "605" ,
            height : "250"

        });
    CKEDITOR.replace( 'splashPageFooter',
        {
            customConfig : 'config.js' ,
            toolbar :  'BasicToolbar'  ,
            width : "605" ,
            height : "250"

        });
    CKEDITOR.replace( 'splashPageStatistics',
        {
            customConfig : 'config.js' ,
            toolbar :  'BasicToolbar'  ,
            width : "605" ,
            height : "250"

        });
});