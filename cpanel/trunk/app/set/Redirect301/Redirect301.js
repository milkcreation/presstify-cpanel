jQuery( document ).ready( function( $ ){
	/* = ARGUMENTS = */
	/** == Upload == **/
	var files;
	var rules, total, current = 0, root_url;

	/* = TELECHARGEMENT DU FICHIER D'IMPORT = */
	/** == Déclenchement == **/		
	$( document ).on( 'change', '#uploadfile-trigger', function(e){
		e.stopPropagation();
    	e.preventDefault();
   				
    	files = e.target.files;
    	
	    var data = new FormData();
	    $.each( files, function( key, value ){
	        data.append(key, value);
	    });
   
	    $.ajax({
	        url			: tify_ajaxurl +'?action=uploadfile_handle',
	        type		: 'POST',
	        data		: data,
	        cache		: false,
	        dataType	: 'json',
	        processData	: false,
	        contentType	: false, 
	        success		: function( resp, textStatus, jqXHR ){
	        	rules = resp;
	        	total = rules.length;
	        	$( "#rules #total > .label" ).text( total );
	        	var html = ""
	        	$.each( rules, function( u, v ){ html += "\nRedirect 301 "+ v[0] +" "+ v[1] });	        	      	        	
	        	$( '#rules > pre > code' ).html( html );
	        }
	    });
	});
	
	$( document ).on( 'click', '#test-trigger', function(e){
		e.stopPropagation();
    	e.preventDefault();
    	current 	= 0;
    	root_url	= $( '#root_url' ).val();
    	$( '#test-results').empty();
    	check_redirect_url( rules[current][0], rules[current][1], root_url );	
	});
	
	check_redirect_url = function( ori, dest, root ){
		if( current >= total )
			return false;
		$.ajax({
	        url			: tify_ajaxurl +'?action=check_redirect_url',
	        type		: 'POST',
	        data		: { ori_url: ori, dest_url : dest, root_url : root },
	        dataType	: 'json',
	        success		: function( resp, textStatus, jqXHR ){
	        	current++;
	        	var res = ( resp.success ) ? '<b style="color:green;">!</b>' : '<b style="color:red;">X</b>';
	        	
	        	$( '#test-results').append( '<li>'+ori+' -->'+ dest +' : '+ res +'</li>');
	        	check_redirect_url( rules[current][0], rules[current][1], root_url );
	        }
	    });
	}; 
});