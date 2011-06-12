	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "fullscreen,inlinepopups,media,paste,preview,save,searchreplace,table,xhtmlxtras",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,styleselect,formatselect,|,blockquote,link,unlink,anchor,|,sub,sup,|,image,media,|,cite",
		theme_advanced_buttons2 : "tablecontrols,|,bullist,numlist,outdent,indent",
		theme_advanced_buttons3 : "save,|,pastetext,pasteword,|,search,replace,|,undo,redo,|,visualaid,code,preview,fullscreen,visualchars",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],
		
		// Example content CSS (should be your site CSS)
		content_css : "../../css/contenido_teorico.css",

		// Drop lists for link/image/media/template dialogs
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js"
	});