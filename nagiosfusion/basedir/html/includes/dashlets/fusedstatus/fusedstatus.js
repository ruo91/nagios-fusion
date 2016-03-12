

//bind clicks to overlay popups for fused status summary
function fss_reveal(id) {

	//hide all other open overlays
	$('.fss_hidden').hide();
	
	$('#fss_'+id).show(); 


}

function fss_close(id) {

	$('#fss_'+id).hide(); 

}