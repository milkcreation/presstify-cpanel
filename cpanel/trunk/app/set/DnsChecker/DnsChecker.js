jQuery( document ).ready( function($){
	// Bouton de soumission
	var $submitButton = $( '#tiFyDnsCheckerForm-submit' );
	var submitOriginalLabel = $submitButton.html();
	
	// Serveurs DNS
	var nserver = $( ".tiFyDnsCheckServer" ).length;
	
	// Barre de progression
	var progressBar = { success: 0, danger: 0, warning : 0 };
	var progressBarPercentPart = 100/nserver;
	
	// Traitement du formulaire
	var proceed = 0;
		
	$( '#tiFyDnsCheckerForm' ).on( 'submit', function( e ){
		e.preventDefault();
		
		// Bypass
		if( proceed )
			return;

		// Remise à zéro 
		$( '.tiFyDnsCheckServer-result, .tiFyDnsCheckServer-ttl' ).empty();
		$( '.tiFyDnsCheckServer' ).removeClass( 'success warning' );
		$( '.tiFyDnsCheckerProgressBar' ).empty().css('width', '0%' );		
		proceed = 1;
		
		// Bouton de soumission
		$submitButton.html( $submitButton.data( 'loading-text' ) ).addClass( 'disabled' );
		
		var data = 'action=tify_dns_check';
		data += '&'+ $(this).serialize();
		
		$( '.tiFyDnsCheckServer' ).each( function( u, row ){
			data += '&node='+ $(this).data('node');
			
			$.ajax({
				url:		tify_ajaxurl,
				data:		data,
				success:	function( resp )
				{				
					$( '.tiFyDnsCheckServer-result', $(row) ).html( resp.data.result );				
					$( '.tiFyDnsCheckServer-ttl', $(row) ).html( resp.data.ttl );
					$(row).addClass( resp.data.status );
					
					progressBar[resp.data.status] += progressBarPercentPart;
					$( '.tiFyDnsCheckerProgressBar-'+ resp.data.status ).css( 'width', progressBar[resp.data.status] +'%' ).html( Math.round( progressBar[resp.data.status] ) + '%' ); 
				},
				complete:	function( resp )
				{					
					if( proceed++ !== nserver )
						return false;
					proceed = 0;
					progressBar = { success: 0, danger: 0, warning : 0 };
					$submitButton.html( submitOriginalLabel ).removeClass( 'disabled' );
				}
			});
		});		
	});
});
