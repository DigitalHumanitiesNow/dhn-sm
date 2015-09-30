jQuery(document).ready(function( $ ) {
	console.log('loading jQuery ready function');

	
});


jQuery("#test").click(function( $ ) {
	alert( "Handler for .click() called." );
});

jQuery(function ($) { $('#test').click(); });