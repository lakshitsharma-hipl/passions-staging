jQuery(document).ready(function ($) {
  $('input[name="auctioncategory"], input[name="auctionevent"]').change(function(){
    $('.auction-loader').show();
    updateAuctionValues();
  });

  function updateAuctionValues() {
    var checkedCategory = [];
    var checkedEvent = [];
    var checkedYear = [];
    var checkedMin = [];
    var checkedMax = [];
    var type = jQuery('#checked_auction_type').val();
    var year = $('#auctionyear').val();
    var minprice = $('#auctionminprice').val();
    var maxprice = $('#auctionmaxprice').val();
    var search = $('#auctionsearch').val();
    
    $('input[name="auctioncategory"]:checked').each(function(){
        checkedCategory.push($(this).val());
    });

    $('input[name="auctionevent"]:checked').each(function(){
        checkedEvent.push($(this).val());
    });

    if ($('#auctionyear').length) {
      checkedYear.push($('#auctionyear').val());
    }

    if ($('#auctionminprice').length) {
      checkedMin.push($('#auctionminprice').val());
    }

    if ($('#auctionmaxprice').length) {
      checkedMax.push($('#auctionmaxprice').val());
    }

    $('#checked_auctioncategory').val(checkedCategory);
    $('#checked_auctionevent').val(checkedEvent);
    $('#checked_auctionyear').val(checkedYear);
    $('#checked_auctionminprice').val(checkedMin);
    $('#checked_auctionmaxprice').val(checkedMax);

    load_all_auction(1, type, checkedCategory, checkedEvent, year, minprice, maxprice, search);
  }

  var queryValue = getQueryParameter('sq');
  if (queryValue) {
      $('#auctionsearch').val(queryValue);
      setTimeout(function() {
          $("#auctionsearch").change();
          $("#auctionsearch").keyup();
      }, 100);
  }


  $('#auctionyear, #auctionminprice, #auctionmaxprice, #auctionsearch').on('change keyup', function () {
    $('.auction-loader').show(); 
    updateAuctionValues();
  });

  $('.auction-list-grid .nav-link').on('click', function () {
    $('.auction-loader').show();   
    var type = $(this).attr('aria-controls');
    var category = $('#checked_auctioncategory').val();
    var event = $('#checked_auctionevent').val();
    var year = $('#checked_auctionyear').val();
    var minprice = $('#checked_auctionminprice').val();
    var maxprice = $('#checked_auctionmaxprice').val();
    var search = $('#auctionsearch').val();
    $('#checked_auction_type').val(type);
    load_all_auction(1, type, category, event, year, minprice, maxprice, search);
  });
  $('.auction-loader').show();
  var category = jQuery('#checked_auctioncategory').val();
  load_all_auction(1, 'all', category, '', '', '', '', '');
});

function loadmoreauctions(lisec){

  var checkedCategory = [];
  var checkedEvent = [];
  jQuery('input[name="auctioncategory"]:checked').each(function(){
      checkedCategory.push(jQuery(this).val());
  });

  jQuery('input[name="auctionevent"]:checked').each(function(){
      checkedEvent.push(jQuery(this).val());
  });

  jQuery('.auction-loader').show();
  var type = jQuery('#checked_auction_type').val();
  
  var category = jQuery('#checked_auctioncategory').val();

  var event = jQuery('#checked_auctionevent').val();

  var year = jQuery('#checked_auctionyear').val();
  var minprice = jQuery('#checked_auctionminprice').val();
  var maxprice = jQuery('#checked_auctionmaxprice').val();
  var search = jQuery('#auctionsearch').val();
  var page = jQuery(lisec).attr('p');
  
  load_all_auction(page, type, checkedCategory, checkedEvent, year, minprice, maxprice, search);
}

var ajaxurl = myAjax.ajaxurl;
function load_all_auction(page, type, category, event, year, minprice, maxprice, search){
  var data = { 
    page: page,
    type: type,
    category: category,
    event: event,
    year: year,
    minprice: minprice,
    maxprice: maxprice,
    search: search,
    action: "pagination_load_auctions"
  };
  jQuery.post(ajaxurl, data, function(response) {
    jQuery('.auction-loader').hide();
    jQuery(".auctions-posts").html('').append(response);
    var noposts = jQuery('div').hasClass('noposts');
    if(noposts) {
      jQuery('.paginationpro').hide();
    }else {
      jQuery('.paginationpro').show();
    }
  });
}


jQuery(document).ready(function($){
  /* reset filter */
  $('.auction-list .clearbtn, .filter-apply-clear .clearall').on('click', function() {
    var type = jQuery('#checked_auction_type').val();
    $('input[name="auctioncategory"]').prop('checked', false); 
    $('input[name="auctionevent"]').prop('checked', false);
    $('#auctionyear').val('');
    $('#auctionminprice').val('');
    $('#auctionmaxprice').val('');
    $('#auctionsearch').val('');
    load_all_auction(1, type, '', '', '', '', '', '');
  });
  /* auction layout */
  $('.layout-view button').on('click', function() {
    $('.layout-view button').removeClass('active');
    $(this).addClass('active');
    if ($(this).hasClass('grid-view-btn')) {
      $('.auction-list-grid').removeClass('list').addClass('grid');
    } else if ($(this).hasClass('list-view-btn')) {
      $('.auction-list-grid').removeClass('grid').addClass('list');
    }
  });
});

jQuery(document).ready(function($){
  $('.filter-apply-clear .clearall, .filter-apply-clear .applyfilter').on('click', function(){
    $('.filtersidebar').removeClass('show');
  });
});

function addWatchlist(postid, userid, thispost){
  jQuery.ajax({
        type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {
            "postid" : postid,
            "userid" : userid,
            "action" : "auction_watchlist"
        },
        success: function(response) {
          if(response.status == 'success') {
            jQuery('.watchlistgrp').html('<a href="'+response.url+'" class="btn btn-border addedwatchlist watchlist-btn">View Watch List</a>');
          } else {
            if(response.redirect != ''){
              window.location.href = response.redirect;
              return false;
            }
            jQuery('.watchlist-btn').text('Add to Watch List');
          }

        }
    });
}

function deleteWatchlist(postid, userid, thispost){
  var checkstr =  confirm('are you sure you want to delete this?');
  if(checkstr == true){
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {
          "postid" : postid,
          "userid" : userid,
          "action" : "auction_delete_watchlist"
      },
      success: function(response) {
        if(response.status == 'success') {
          location.reload();
        } else {
          
        }
      }
    });
  }
}

function getQueryParameter(name) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}