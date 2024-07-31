function placeBid(auctionid, userid){
  jQuery('#placeaucbid').addClass('disabled');
  var bidtype = jQuery('input[name="bid"]:checked').val();
  var bidamount = jQuery('#bid_value').val();
	jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {
          "auctionid" : auctionid,
          "userid" : userid,
          "bidtype" : bidtype,
          "bidamount" : bidamount,
          "action" : "placeAuctionBid"
      },
      success: function(response) {
        jQuery('#infoblc').removeClass();
        var messageis = response.message;
        jQuery('#infoblc').html(messageis);
        jQuery('#infoblc').show();
        jQuery('#infoblc').addClass(response.status);
        if(response.status == 'success'){
          setTimeout(function(){
              jQuery('#infoblc').hide();
              jQuery('#infoblc').html('');
              jQuery('#infoblc').removeClass();
              jQuery('#placebid').modal('hide');


              if(response.scredirect){
                setTimeout(function() {
                  var auction_id = response.auction_id;
                  var userid = response.userid;
                  var lastbidid = response.bidid;
                  jQuery.ajax({
                      type: 'post',
                      dataType: 'json',
                      url: myAjax.ajaxurl,
                      data: {
                          'auction_id': auction_id,
                          'userid': userid,
                          'lastbidid': lastbidid,
                          'action': 'send_email_previous_bidders'
                      },
                      success: function(email_result) {
                          console.log(email_result);
                          window.location.href = response.scredirect;
                      },
                      error: function(xhr, status, error) {
                          console.log(error);
                          window.location.href = response.scredirect;
                      }
                    });
                  }, 1000);
                  
                  return false;
              }
            }, 1000);

        }else{
          if(response.popup == 'close'){
            setTimeout(function(){
              jQuery('#placebid').modal('hide');
              return false;
            }, 1000);
          }
          if(response.redirect){
            setTimeout(function(){
              window.location.href = response.redirect;
              return false;
            }, 3000);
          }
        }
        jQuery('#placeaucbid').removeClass('disabled');
      }
  });
}
jQuery(document).ready(function(){
  //Pusher.logToConsole = true;
  var pusher = new Pusher('e4bbf991aaac16fd100c', {
    cluster: 'ap2'
  });
  var channel = pusher.subscribe('auction');
  channel.bind('newbid', function(data) {
  //alert(JSON.stringify(data));
      jQuery.ajax({
        type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {
            "bidid" : data.bidid,
            "auctionid" : data.auctionid,
            "userid" : data.userid,
            "location" : data.location,
            "bidamount" : data.bidamount,
            "datetime" : data.datetime,
            "inctime" : data.inctime,
            "action" : "updateLiveAuctionData"
        },
        success: function(response) {
           if(jQuery("body").hasClass("postid-"+data.auctionid)){
              if(response.price){
                jQuery('#letestbidprc').val(data.bidamount);
                jQuery('#livebid').trigger('click');
                jQuery('.livebidprice').html(response.price);
                jQuery('.bidcount').html(response.bidcount);
                jQuery('.historytable').html(response.bidhistory);
                jQuery('.scrolltable').html(response.bidhistory);
                if(response.daynum || response.hournum || response.minnum){
                    jQuery('#daynum').val(response.daynum);
                    jQuery('#hournum').val(response.hournum);
                    jQuery('#minutesnum').val(response.minnum);
                    updateCountdown();
                }
              }
           }
           if(jQuery("body").hasClass("archive")){
              jQuery('#auction-past-'+data.auctionid+' .livebidprice, #auction-upcoming-'+data.auctionid+' .livebidprice, #auction-new-'+data.auctionid+' .livebidprice, #auction-all-'+data.auctionid+' .livebidprice').html(response.price);
              jQuery('#auction-past-'+data.auctionid+' .bidcount, #auction-upcoming-'+data.auctionid+' .bidcount, #auction-new-'+data.auctionid+' .bidcount, #auction-all-'+data.auctionid+' .bidcount').html(response.bidcount);
              if(response.daynum || response.hournum || response.minnum){
                jQuery('#auction-past-'+data.auctionid+' .duration, #auction-upcoming-'+data.auctionid+' .duration, #auction-new-'+data.auctionid+' .duration, #auction-all-'+data.auctionid+' .duration').html(response.daynum+' days '+response.hournum+' hours '+response.minnum+' minutes');
              }
           }
        }
    });
  });
});

/* load more bid history */
jQuery(function($) {
  
    function loadBids(tabId, currentPage, auctionid) {
      var $container = $('#pagination-container-' + tabId + '-' + auctionid);
      var $table = $('#auctionbid' + tabId + '-' + auctionid);
      var itemsPerPage = 20;
      
      $.ajax({
        url: myAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'load_bids_pagination',
            page: currentPage,
            itemsPerPage: itemsPerPage,
            auctionid: auctionid
        },
        success: function(response) {
            $table.find('tbody').html(response);
            updatePaginationLinks(tabId, currentPage, auctionid);
        },
        error: function(xhr, status, error) {
            console.error(status + ": " + error);
        }
      });
    }

    function updatePaginationLinks(tabId, currentPage, auctionid) {
      var $container = $('#pagination-container-' + tabId + '-' + auctionid);
      var totalBids = parseInt($container.attr('total-bids'));
      var totalPages = Math.ceil(totalBids / 20);

      var paginationHtml = '<ul class="pagination">';
      paginationHtml += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="prev"><svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 1L1 7L7 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></li>';
      for (var i = 1; i <= totalPages; i++) {
          paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
      }
      paginationHtml += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="next"><svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L7 7L1 13" stroke="#151515" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></a></li>';
      paginationHtml += '</ul>';

      $container.html(paginationHtml);

      $container.off('click', '.page-link');
      $container.on('click', '.page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page === 'prev' && currentPage > 1) {
            loadBids(tabId, currentPage - 1, auctionid);
        } else if (page === 'next' && currentPage < totalPages) {
            loadBids(tabId, currentPage + 1, auctionid);
        } else if (typeof page === 'number') {
            loadBids(tabId, page, auctionid);
        }
      });
    }

    $('[id^="pagination-container-"]').each(function() {
      var $container = $(this);
      var tabId = $container.attr('id').split('-')[2];
      var auctionid = $container.attr('auc-id');
      
      loadBids(tabId, 1, auctionid);
    });
});