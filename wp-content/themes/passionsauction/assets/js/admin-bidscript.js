function updateVerificationStatus(userid, auctionid, actiontype) {
	jQuery('.custombidtbl').addClass('loading');
	jQuery.ajax({
      	type: 'post',
      	dataType: 'json',
      	url: myAjax.ajaxurl,
      	data: {
          	'auctionid': auctionid,
          	'userid': userid,
          	'actiontype': actiontype,
          	'action': 'updateUserVarification'
      	},
      	success: function(response) {
          	if(response.status == 'success'){
            	jQuery('#afterresponse').html(response.message);
            	jQuery('#afterresponse').show();
            	jQuery('.verifybiddata').remove();
            	jQuery('#afterresponse').addClass('success');
            	jQuery('.custombidtbl').removeClass('loading');
	            setTimeout(function() {
	                location.reload();
	            }, 3000);
          	}else{
            	jQuery('#afterresponse').html(response.message);
            	jQuery('#afterresponse').show();
            	jQuery('#afterresponse').addClass('error');
            	jQuery('.custombidtbl').removeClass('loading');
          	}
      	},
      	error: function(xhr, status, error) {
          	console.log(error);
      	}
    });
}