jQuery(document).ready(function ($) {

  $('.quantity-control').on('click', '.quantity-increase', function() {
    $('.cart-update-response').remove();
      var $quantityInput = $(this).siblings('.quantity-input');
      var currentQuantity = parseInt($quantityInput.val());
      var new_quantity = currentQuantity + 1;
      var productId = $quantityInput.data('product');
      var itemId = $quantityInput.data('item-id');
      
      // Show loader
      $('.loader-single-product').show();
      
      // AJAX request
      $.ajax({
          url: myAjax.ajaxurl,
          type: 'POST',
          data: {
              action: 'auc_product_quantity_update',
              productId: productId,
              itemId: itemId,
              quantity: new_quantity,
              type: 'increase'
          },
          success: function(response) {
            if (response.status === 'success') {              
              $quantityInput.val(response.new_quantity);
              $('#auc_product_crt_table').after('<div class="cart-update-response success-message">' + response.message + '</div>');
              location.reload();
            } else {
              $('#auc_product_crt_table').after('<div class="cart-update-response error-message">' + response.message + '</div>');
            }            
            $('.loader-single-product').hide();                         
            
          }
      });
  });

  // Function to handle the click event on the quantity decrease button
  $('.quantity-control').on('click', '.quantity-decrease', function() {
      $('.cart-update-response').remove();
      var $quantityInput = $(this).siblings('.quantity-input');
      var productId = $quantityInput.data('product');
      var itemId = $quantityInput.data('item-id');
      var currentQuantity = parseInt($quantityInput.val());
      var new_quantity = currentQuantity - 1;
      $('.loader-single-product').show();      
      // AJAX request
      $.ajax({
          url: myAjax.ajaxurl,
          type: 'POST',
          data: {
              action: 'auc_product_quantity_update',
              productId: productId,
              itemId: itemId,
              quantity: new_quantity,
              type: 'decrease'
          },
          success: function(response) {
            if (response.status === 'success') {              
              $quantityInput.val(response.new_quantity);
              $('#auc_product_crt_table').after('<div class="cart-update-response success-message">' + response.message + '</div>');
              location.reload();
            } else {
              $('#auc_product_crt_table').after('<div class="cart-update-response error-message">' + response.message + '</div>');
            }            
            $('.loader-single-product').hide();                            
          }
      });
  });
    if($('body').hasClass('passion-product-body')) {
      $('#quantity').on('input', function() {
        let value = parseInt($(this).val());
        var max = parseInt($(this).attr('max'));
        console.log("Yo", max);
        if (isNaN(value) || value < 1) {
            $(this).val(1);
        } else if (value > max) {
            $(this).val(max);
        }
      });
      $('.decrease').on('click', function() {
        let quantityInput = $('#quantity');
        let currentValue = parseInt(quantityInput.val());
        
        if (currentValue > 1) {
            quantityInput.val(currentValue - 1);
        }
    });

    $('.increase').on('click', function() {
        let quantityInput = $('#quantity');
        let maxVal = parseInt(quantityInput.attr('max'));
        let currentValue = parseInt(quantityInput.val());
        if (currentValue < maxVal) {
            quantityInput.val(currentValue + 1);
        }
    });
    
    // Cart Page increase decrease


    $('input[name="productcategory"], input[name="productevent"]').change(function(){
      $('.product-loader').show();
      updateproductValues();
    });

    $('.addto_cart_passion').on('click', function(e) {
        e.preventDefault();
        $(this).css('pointer-events', 'none');
        $('.woo-message').remove();
        jQuery('.woo_single_product_cont .woo-product-messages').removeClass('error-message, success-message');
        $('.loader-single-product').show();
        var product_id = $(this).data('product');
        var quantity = $('#quantity').val();
        var userid =  $(this).data('user');
        jQuery.ajax({
          type : "post",
          dataType : "json",
          url : myAjax.ajaxurl,
          data : {
              "product_id" : product_id,
              "quantity" : quantity,
              "userid" : userid,
              "action" : "pass_product_add_to_cart"
          },
          success: function(response) {
            if(response.status == 'success') {
              jQuery('.woo_single_product_cont .woo-product-messages').html('<div class="woo-message">'+response.message+'</div><span class="cart-btn-single"><a href="'+response.url+'">View Cart</a></span>');
              jQuery('.woo_single_product_cont .woo-product-messages').addClass('success-message');
              
              jQuery('.header-carticon .cart-counter').html(response.cart_quantity);
              // jQuery('#add_cart_passion').after('<a href="'+response.url+'" class="btn btn-green response-btn">View Cart</a><div class="response-message ">  <span class="success-message">'+response.message+'</span></div>');
            } else {
              // jQuery('#add_cart_passion').after('<span class="response-message error-message">'+response.message+'</span>');
              jQuery('.woo_single_product_cont .woo-product-messages').html('<div class="woo-message">'+response.message+'</div><span class="cart-btn-single"><a href="'+response.url+'">View Cart</a></span>');
              jQuery('.woo_single_product_cont .woo-product-messages').addClass('error-message');
            }  
          },
          complete: function() {
            // Hide the loader image after AJAX request completes            
            $('.addto_cart_passion').css('pointer-events', 'unset');
            $('.loader-single-product').hide();
          }
      });
    });
  
    function updateproductValues() {
      var checkedCategory = [];
      var checkedEvent = [];
      var checkedYear = [];
      var checkedMin = [];
      var checkedMax = [];
      var type = jQuery('#checked_product_type').val();
      var year = $('#productyear').val();
      var minprice = $('#productminprice').val();
      var maxprice = $('#productmaxprice').val();
      var search = $('#productsearch').val();
      
      $('input[name="productcategory"]:checked').each(function(){
          checkedCategory.push($(this).val());
      });
  
      $('input[name="productevent"]:checked').each(function(){
          checkedEvent.push($(this).val());
      });
  
      if ($('#productyear').length) {
        checkedYear.push($('#productyear').val());
      }
  
      if ($('#productminprice').length) {
        checkedMin.push($('#productminprice').val());
      }
  
      if ($('#productmaxprice').length) {
        checkedMax.push($('#productmaxprice').val());
      }
  
      $('#checked_productcategory').val(checkedCategory);
      $('#checked_productevent').val(checkedEvent);
      $('#checked_productyear').val(checkedYear);
      $('#checked_productminprice').val(checkedMin);
      $('#checked_productmaxprice').val(checkedMax);
  
      load_all_product(1, type, checkedCategory, checkedEvent, year, minprice, maxprice, search);
    }
  
    var queryValue = getQueryParameter('sq');
    if (queryValue) {
        $('#productsearch').val(queryValue);
        setTimeout(function() {
            $("#productsearch").change();
            $("#productsearch").keyup();
        }, 100);
    }
  
  
    $('#productyear, #productminprice, #productmaxprice, #productsearch').on('change keyup', function () {
      $('.product-loader').show(); 
      updateproductValues();
    });
  
    $('.product-list-grid .nav-link').on('click', function () {
      $('.product-loader').show();   
      var type = $(this).attr('aria-controls');
      var category = $('#checked_productcategory').val();
      var event = $('#checked_productevent').val();
      var year = $('#checked_productyear').val();
      var minprice = $('#checked_productminprice').val();
      var maxprice = $('#checked_productmaxprice').val();
      var search = $('#productsearch').val();
      $('#checked_product_type').val(type);
      load_all_product(1, type, category, event, year, minprice, maxprice, search);
    });
    $('.product-loader').show();
    var category = jQuery('#checked_productcategory').val();
    load_all_product(1, 'all', category, '', '', '', '', '');
  }
  
});

function loadmoreproducts(lisec){
  jQuery('.product-loader').show();
  var type = jQuery('#checked_product_type').val();
  var category = jQuery('#checked_productcategory').val();
  var event = jQuery('#checked_productevent').val();
  var year = jQuery('#checked_productyear').val();
  var minprice = jQuery('#checked_productminprice').val();
  var maxprice = jQuery('#checked_productmaxprice').val();
  var search = jQuery('#productsearch').val();
  var page = jQuery(lisec).attr('p');
  load_all_product(page, type, category, event, year, minprice, maxprice, search);
}

var ajaxurl = myAjax.ajaxurl;
function load_all_product(page, type, category, event, year, minprice, maxprice, search){
  var data = { 
    page: page,
    type: type,
    category: category,
    event: event,
    year: year,
    minprice: minprice,
    maxprice: maxprice,
    search: search,
    action: "pagination_load_products"
  };
  jQuery.post(ajaxurl, data, function(response) {
    jQuery('.product-loader').hide();
    jQuery(".product-posts").html('').append(response);
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
  $('.product-list .clearbtn, .filter-apply-clear .clearall').on('click', function() {
    var type = jQuery('#checked_product_type').val();
    $('input[name="productcategory"]').prop('checked', false); 
    $('input[name="productevent"]').prop('checked', false);
    $('#productyear').val('');
    $('#productminprice').val('');
    $('#productmaxprice').val('');
    $('#productsearch').val('');
    load_all_product(1, type, '', '', '', '', '', '');
  });
  /* product layout */
  $('.layout-view button').on('click', function() {
    $('.layout-view button').removeClass('active');
    $(this).addClass('active');
    if ($(this).hasClass('grid-view-btn')) {
      $('.product-list-grid').removeClass('list').addClass('grid');
    } else if ($(this).hasClass('list-view-btn')) {
      $('.product-list-grid').removeClass('grid').addClass('list');
    }
  });
});

jQuery(document).ready(function($){
  $('.filter-apply-clear .clearall, .filter-apply-clear .applyfilter').on('click', function(){
    $('.filtersidebar').removeClass('show');
  });
  function getImageDataUrl(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        var reader = new FileReader();
        reader.onloadend = function() {
            callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}
  
  $('a.delete-cart-item').on('click', function(e) {    
    e.preventDefault();
    $('.cart-message').hide();
    $('a.delete-cart-item').css('pointer-events', 'none');
    $('.cart-message').removeClass('success-message error-message');
    $('.loader-img-cart-item').css('display', 'inline-block');
    var item_id = $(this).data('item-id');
    jQuery.ajax({
      type : "post",
      dataType : "json",
      url : myAjax.ajaxurl,
      data : {
          "item_id" : item_id,
          "action" : "pass_product_delete_from_cart"
      },
      success: function(response) {
        if(response.status == 'success') {
          $('.cart-message').addClass('success-message');
          $('.cart-message').show();            
          $('.cart-message').html(response.message);
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else {          
          jQuery('.cart-message').html(response.message);
          $('.cart-message').addClass('error-message');
          $('.cart-message').show();
        }
      },
      complete: function() {
        // Hide the loader image after AJAX request completes
        $('a.delete-cart-item').css('pointer-events', 'unset');
        $('.loader-img-cart-item').hide();
      }
    });
  });



  
    // Function to generate and download PDF
    function downloadInvoiceAsPDF() {
      // Define the content before and after the order details
      var logo = $('.hfe-site-logo-img').attr('src');
      var invoice_id = $('.order-details').data('invoice');
      var first_name = $('.order-details').data('user');
      getImageDataUrl(logo, function(dataUrl) {
          var contentBefore = [
              {
                  columns: [
                      // Left side: Website logo
                      { image: dataUrl, fit: [100, 100], alignment: 'left' },
                      // Right side: Invoice text
                      { text: 'Invoice', alignment: 'right', width: '*' }
                  ],
                  margin: [0, 0, 0, 10] // Adjust margins as needed
              },       
              // Additional details div row
            {
              columns: [
                  // Left side: Bill To
                  { text: 'Bill To: '+first_name, alignment: 'left' },
                  // Right side: Payment Method
                  { text: 'Payment Method: Stripe', alignment: 'right' }
              ],
              margin: [0, 0, 0, 10] // Adjust margins as needed
          },
          {
            columns: [
                // Left side: Bill To
                { text: '', alignment: 'left' },
                // Right side: Payment Method
                { text: 'Invoice No.: '+invoice_id, alignment: 'right' }
            ],
            margin: [0, 0, 0, 10] 
        }   
          ];
          // Define padding for table cells
          var styles = {
            td: {
                padding: 5 // Adjust padding as needed
            }
          };
                    // var contentAfter = 'Additional Content After Order Details';
  
        var tableData = [];
        $('.order-detail-table tbody tr').each(function() {
            var rowData = [];
            var columnIndex = 0;
            $(this).find('td, th').each(function() {
                var colspan = parseInt($(this).attr('colspan')) || 1;
                var cellText = $(this).text().trim();
                for (var i = 0; i < colspan; i++) {
                    if (i === 0) {
                        rowData[columnIndex++] = cellText; // Insert cell text only for the first column of the span
                    } else {
                        rowData[columnIndex++] = ""; // For subsequent columns of the span, insert empty string
                    }
                }
            });
            tableData.push(rowData);
        });

        console.log('Table Data:', tableData);

        // Extract the data from tfoot
        var tfootData = [];
        $('.order-detail-table tfoot tr').each(function() {
            var rowData = [];
            var columnIndex = 0;
            $(this).find('td, th').each(function() {
                var colspan = parseInt($(this).attr('colspan')) || 1;
                var cellText = $(this).text().trim();
                for (var i = 0; i < colspan; i++) {
                    if (i === 0) {
                        rowData[columnIndex++] = cellText; // Insert cell text only for the first column of the span
                    } else {
                        rowData[columnIndex++] = ""; // For subsequent columns of the span, insert empty string
                    }
                }
            });
            tfootData.push(rowData);
        });

          // Concatenate tbody and tfoot data
          var tableContent = tableData.concat(tfootData);
  
          // Define the order details content dynamically
          var orderDetailsContent = [
              { text: 'Order details', style: 'header' },
              {
                  table: {
                      headerRows: 1,
                      widths: ['*', '*', '*'],
                      body: [
                          ['Item', 'Qty', 'Total']
                      ].concat(tableContent) // Add the table data
                  }
              }
          ];
  
          // Define the document definition
          var docDefinition = {
              content: [
                  contentBefore,
                  orderDetailsContent
                  // contentAfter
              ],
              styles: {
                header: {
                    fontSize: 18,
                    bold: true,
                    margin: [0, 0, 0, 10]
                },
                tableStyle: {
                    td: {
                        padding: 5 // Padding for table cells
                    }
                }
            }
          };
  
          // Generate PDF
          pdfMake.createPdf(docDefinition).download('invoice.pdf');
      });
  }
  
  // Add click event listener to the button using jQuery
  $('#downloadInvoiceBtn').on('click', downloadInvoiceAsPDF);
  
    
  
  
  

});



function getQueryParameter(name) {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}