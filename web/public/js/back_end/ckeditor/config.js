/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
// CKEDITOR.stylesSet.add( 'kc_styles',
// [
//     // Block-level styles
// 	 	{
// 			name: 'Yellow Box',
// 			element: 'div',
// 			attributes: { 'class': 'new_yellow_box' }
// 		}
//     // Inline styles
// ]);

CKEDITOR.editorConfig = function( config ) {
	// %REMOVE_START%
	// The configuration options below are needed when running CKEditor from source files.
	config.plugins = 'dialogui,dialog,a11yhelp,basicstyles,blockquote,clipboard,panel,floatpanel,menu,contextmenu,resize,button,toolbar,elementspath,list,indent,enterkey,entities,popup,filebrowser,floatingspace,listblock,richcombo,format,htmlwriter,horizontalrule,wysiwygarea,image,fakeobjects,link,magicline,maximize,pastetext,pastefromword,removeformat,sourcearea,specialchar,stylescombo,tab,table,tabletools,undo';
	config.skin = 'moono';
	// %REMOVE_END%

	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.contentsCss = '/public/css/back_end/ckeditor_extra.css';
	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup', 'my_styles' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	//config.stylesSet = 'kc_styles';
	config.width = '100%';

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';
	config.filebrowserBrowseUrl = '/public/js/back_end/ckeditor/ckfinder/ckfinder.html',
	config.filebrowserImageBrowseUrl = '/public/js/back_end/ckeditor/ckfinder/ckfinder.html?type=Images';
	config.filebrowserFlashBrowseUrl = '/public/js/back_end/ckeditor/ckfinder/ckfinder.html?type=Flash';
	config.filebrowserUploadUrl = '/public/js/back_end/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
	config.filebrowserImageUploadUrl = '/public/js/back_end/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	config.filebrowserFlashUploadUrl = '/public/js/back_end/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'


    CKEDITOR.dtd.$removeEmpty['span'] = false;
    CKEDITOR.dtd.$removeEmpty['i'] = false;
};
