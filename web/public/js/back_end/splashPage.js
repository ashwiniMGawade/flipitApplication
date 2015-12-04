var CKcontent = false ;
$(document).ready(function(){
    $('.numeric').keyup(function () {
        this.value = this.value.replace(/[^1-9\.]/g,'');
    });
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
});