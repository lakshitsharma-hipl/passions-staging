jQuery(document).ready(function($) {
    $("#showbidregform").click(function() {
        $("#bidregform").slideToggle();
    });

    $("#submitverification").click(function() {
        var verifyname = $('#auc_user_first_name').val();
        var last_name = $('#auc_user_last_name').val();
        var city = $('#auc_user_city').val();
        var state = $('#auc_user_state').val();
        var country = $('#auc_user_country').val();
        var zipcode = $('#auc_zipcode').val();
        
        var verifypnumber = $('#verifypnumber').val();
        var verifyaddress = $('#verifyaddress').val();

        var auctionid = $('#auctionid').val();
        var userid = $('#userid').val();
        
        $(".emptyerror").remove();
        var hasError = false;
        if (verifyname === "") {
            $('#auc_user_first_name').after('<div class="emptyerror">Please enter your first name</div>');
            hasError = true;
        }
        if (last_name === "") {
            $('#auc_user_last_name').after('<div class="emptyerror">Please enter your last name</div>');
            hasError = true;
        }
        if (city === "") {
            $('#auc_user_city').after('<div class="emptyerror">Please enter your city</div>');
            hasError = true;
        }
        if (state === "") {
            $('#auc_user_state').after('<div class="emptyerror">Please enter your state</div>');
            hasError = true;
        }
        if (country === "") {
            $('#auc_user_country').after('<div class="emptyerror">Please select your country</div>');
            hasError = true;
        }
        if (zipcode === "") {
            $('#auc_zipcode').after('<div class="emptyerror">Please enter your zipcode</div>');
            hasError = true;
        }
        if (verifypnumber === "") {
            $('#verifypnumber').after('<div class="emptyerror">Please enter your phone number</div>');
            hasError = true;
        }
        if (verifyaddress === "") {
            $('#verifyaddress').after('<div class="emptyerror">Please enter your address</div>');
            hasError = true;
        }

        if (hasError) {
            return;
        }
        var phonecode = $("#verifypnumber").intlTelInput("getSelectedCountryData").dialCode;
        jQuery.ajax({
          type: 'post',
          dataType: 'json',
          url: myAjax.ajaxurl,
          data: {
              'auctionid': auctionid,
              'userid': userid,
              'username': verifyname+' '+last_name,
              'first_name': verifyname,
              'last_name': last_name,
              'city': city,
              'state': state,
              'country': country,              
              'zipcode': zipcode,              
              'phone': verifypnumber,
              'phonecode': phonecode,
              'address': verifyaddress,
              'action': 'registerBidVarification'
          },
          success: function(response) {
              if(response.status == 'success'){
                jQuery('#afterresponse').html(response.message);
                jQuery('#afterresponse').show();
                jQuery('.verifybiddata').remove();
                jQuery('#afterresponse').addClass('success');
                setTimeout(function() {
                    location.reload();
                }, 3000);
              }else{
                jQuery('#afterresponse').html(response.message);
                jQuery('#afterresponse').show();
                jQuery('#afterresponse').addClass('error');
              }
          },
          error: function(xhr, status, error) {
              console.log(error);
          }
        });
        
    });

    $("#showmaxwraper").click(function() {
        $(".maxbidblock").slideToggle();
        $('.buyerpremiuminfo').slideToggle();
        var button = $("#placeaucbid");
        button.prop("disabled", !button.prop("disabled"));
    });
    $(".updatemaxbid-auction").click(function() {
        $(".maxbidblock").slideToggle();
        $('.buyerpremiuminfo').slideToggle();
        $('.mxdatablock').slideToggle();
        var button = $("#placeaucbid");
        button.prop("disabled", !button.prop("disabled"));
    });
});

function addMaxBidForAuction() {

    var auctionid = jQuery("#caucid").val();
    var userid = jQuery("#cuserid").val();
    var maxBidValue = jQuery("#maxbidamount").val();
    var minAmt = jQuery("#maxbidamount").data("minamt");
    var formattedminAmt = minAmt.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
    jQuery('#maxresponse').hide();
    if(minAmt > maxBidValue){
        jQuery('#maxresponse').html('Minimum bid amount required for Max bid is $'+formattedminAmt);
        jQuery('#maxresponse').show();
        jQuery('#maxresponse').addClass('error');
        return false;
    }else{
        jQuery('.bidloadimg').addClass('active');
        jQuery.ajax({
          type: 'post',
          dataType: 'json',
          url: myAjax.ajaxurl,
          data: {
              'auctionid': auctionid,
              'userid': userid,
              'bidamount': maxBidValue,
              'action': 'addMaxBidAmount'
          },
          success: function(response) {

              if(response.status == 'success'){
                jQuery('#maxresponse').html(response.message);
                jQuery('#maxresponse').show();
                jQuery('.maxfields').remove();
                jQuery('#showmaxwraper').remove();
                jQuery('#maxresponse').addClass('success');
                jQuery('.bidloadimg').removeClass('active');
                setTimeout(function() {
                    jQuery('#maxresponse').hide();
                    location.reload();
                }, 3000);
              }else{
                jQuery('#maxresponse').html(response.message);
                jQuery('#maxresponse').show();
                jQuery('#maxresponse').addClass('error');
                jQuery('.bidloadimg').removeClass('active');
              }
          },
          error: function(xhr, status, error) {
              console.log(error);
          }
        });
    }
}

function updateMaxBidForAuction(rowid, auctionid, userid) {
    var maxBidValue = jQuery("#maxbidamount").val();
    var minAmt = jQuery("#maxbidamount").data("minamt");
    var formattedminAmt = minAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    jQuery('#maxresponse').hide();
    if(minAmt > maxBidValue){
        jQuery('#maxresponse').html('Minimum bid amount required for Max bid is $'+formattedminAmt);
        jQuery('#maxresponse').show();
        jQuery('#maxresponse').addClass('error');
        return false;
    } else {
        jQuery('.bidloadimg').addClass('active');
        jQuery.ajax({
          type: 'post',
          dataType: 'json',
          url: myAjax.ajaxurl,
          data: {
            'rowid'    : rowid,
              'auctionid': auctionid,
              'userid': userid,
              'bidamount': maxBidValue,
              'action': 'updateMaxBidAmount'
          },
          success: function(response) {
              if(response.status == 'success'){
                jQuery('#maxresponse').html(response.message);
                jQuery('#maxresponse').show();
                jQuery('.maxfields').remove();
                jQuery('#showmaxwraper').remove();
                jQuery('#maxresponse').addClass('success');
                jQuery('.bidloadimg').removeClass('active');
                setTimeout(function() {
                    jQuery('#maxresponse').hide();
                    location.reload();
                }, 3000);
              }else{
                jQuery('#maxresponse').html(response.message);
                jQuery('#maxresponse').show();
                jQuery('#maxresponse').addClass('error');
                jQuery('.bidloadimg').removeClass('active');
              }
          },
          error: function(xhr, status, error) {
              console.log(error);
          }
        });
    }
}