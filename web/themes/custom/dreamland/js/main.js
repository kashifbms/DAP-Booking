window.addEventListener("load", (event) => {
  jQuery(document).ready(function ($) {
 
    let ticketBooking = {};
    // **************first time called ajax when page reload show the current date tickets**********
    function updateDate() {
      var currentDate = new Date();
      var formattedDate = currentDate.toISOString().split("T")[0];
      $('input[name="checkDate[date]"]').val(formattedDate);
      let validTo = jQuery('input[name="field_valid__from_value"]');
      let validFrom = jQuery('input[name="field_valid__to_value"]');
      jQuery(validFrom).val(jQuery('input[name="checkDate[date]"]').val());
      jQuery(validTo).val(jQuery('input[name="checkDate[date]"]').val());
      // Select the element by its name attribute
      let selectElement = $('select[name="field_enable_days_target_id"]');
      let selectedValue = selectElement.val();
      let date = new Date(jQuery('input[name="checkDate[date]"]').val());
      let dayOfWeek = date.getDay();
      selectElement.val(dayOfWeek + 1);
      selectElement.change();
      $("#views-exposed-form-ticket-booking-page-1")
        .find('input[type="submit"]')
        .click();
    }
    // funciton called to first time reoload page*************
    updateDate();

    // **********when date value change the change event then called  drupal view ajax*******************
    $(document).on("change", 'input[name="checkDate[date]"]', function (e) {
      let validTo = jQuery('input[name="field_valid__from_value"]');
      let validFrom = jQuery('input[name="field_valid__to_value"]');
      jQuery(validFrom).val(jQuery(this).val());
      jQuery(validTo).val(jQuery(this).val());
      // Select the element by its name attribute
      let selectElement = $('select[name="field_enable_days_target_id"]');
      let selectedValue = selectElement.val();
      let date = new Date(jQuery(this).val());
      let dayOfWeek = date.getDay();
      selectElement.val(dayOfWeek + 1);
      selectElement.change();
      $("#views-exposed-form-ticket-booking-page-1")
        .find('input[type="submit"]')
        .click();
      // set total amount empty and reset block value 0
      $("#price-field").text("0");
      ticketBooking = {};
    });

    // ****************ajax called function****************************
    function ajaxCall(pid, updateQuantity) {
      jQuery.ajax({
        url: `/add/product/${pid}`,
        type: "POST",
        data: {
          updateQuantity: updateQuantity,
        },
        success: function (response) {
          console.log(response);
          console.log("Data saved successfully!");
          $("#price-field").text(
            ` : ${
              response.price != undefined
                ? parseFloat(response.price).toFixed(2)
                : "0"
            }`
          );
          $("#currency_code").text(
            `${
              response.currency_code != undefined
                ? response.currency_code
                : "AED"
            }`
          );
          if (response.productQuantity != undefined) {
            console.log(
              "another",
              $(`[pid="${pid}"]`).find(".ticketqty").text()
            );
            $(document).find(`[pid="${pid}"]`).find(".ticketqty").text(2);
            $(`[pid="${pid}"]`)
              .find(".ticketqty")
              .text(parseInt(response.productQuantity));
          }
        },
        error: function (error) {
          console.log("Error: " + error);
        },
      });
    }

    // **********************add ticket****************************

    $(document).on("click", ".ticketadd", function () {
      let index = $(this).prev().data("index");
      let getVal = parseInt($(this).prev().text());
      let pid = parseInt($(this).attr("productid"));
      let totalTickets = parseInt($(this).closest('.ticketcounter').next().text());
 //     $(this)
    //    .prev()
    //    .text(parseInt(getVal) + 1);
      let totalPrice = 0;
      let priceText = $(this)
        .closest(".field-content")
        .find(".product-count")
        .text();
    //  let price = parseFloat(priceText.replace(/[^\d.]/g, ""));
      //console.log(price);
      //ticketBooking[pid] = {
      //  pid: pid,
        //quantity: parseInt(getVal) + 1,
        //price: price,
      //};
          if(getVal<totalTickets){
          $(this)
          .prev()
          .text(parseInt(getVal) + 1);
          
          let price = parseFloat(priceText.replace(/[^\d.]/g, ""));
          console.log(price);
          ticketBooking[pid] = {
            pid: pid,
            quantity: getVal+1,
            price: price,
          };
          
        }else{
         console.log("no more tickets available");
         $(`[tickets-error=${pid}]`).css("display", "block");
        }
      console.log(ticketBooking);
      for (let key in ticketBooking) {
        if (ticketBooking.hasOwnProperty(key)) {
          let item = ticketBooking[key];
          totalPrice += item.quantity * item.price;
        }
      }
      $("#price-field").text(
        `${totalPrice != 0 ? parseFloat(totalPrice).toFixed(2) : "0"}`
      );
      $("#currency_code").text(`${"AED"}`);
    });

    // **************subtracted ticket***************************
    $(document).on("click", ".ticketsub", function () {
      var getVal = $(this).next().text();
      console.log(getVal);
      if (parseInt(getVal) <= 0) {
        return false;
      }
      let pid = parseInt($(this).attr("productid"));
       $(`[tickets-error=${pid}]`).css("display", "none");
      let index = $(this).next().data("index");
      $(this)
        .next()
        .text(parseInt(getVal) - 1);
      // ajaxCall(pid, "reduce");
      let priceText = $(this)
        .closest(".field-content")
        .find(".product-count")
        .text();
      let price = parseFloat(priceText.replace(/[^\d.]/g, ""));
      console.log(price);
      ticketBooking[pid] = {
        pid: pid,
        quantity: parseInt(getVal) - 1,
        price: price,
      };
      let totalPrice = 0;
      for (let key in ticketBooking) {
        if (ticketBooking.hasOwnProperty(key)) {
          let item = ticketBooking[key];
          totalPrice += item.quantity * item.price;
        }
      }
      $("#price-field").text(
        `${totalPrice != 0 ? parseFloat(totalPrice).toFixed(2) : "0"}`
      );
      $("#currency_code").text(`${"AED"}`);
    });

    // **************checkout button on ticket booking page************************
    $(".checkout-btn").on("click", function () {
      let checkoutValue = $("#price-field").text();
      let checkoutValue1 = parseFloat(checkoutValue);
      console.log(checkoutValue1);
      if (checkoutValue1 === 0) {
        if (Object.keys(ticketBooking).length === 0) {
        }
        $(".invalid-feedback").css("display", "block");
      } else {
        if (Object.keys(ticketBooking).length === 0) {
          console.log("error");
        }

        // var formattedDate = currentDate.toISOString().split("T")[0];
        let date = $('input[name="checkDate[date]"]').val();
        console.log("sadjfldsjfkldsjfljdsflj", date);
        if (date != "") {
          jQuery.ajax({
            url: `/add/productMultiple`,
            type: "POST",
            data: {
              Product: ticketBooking,
              Date: date,
            },
            success: function (response) {
              window.location.href = "/checkout/";
            },
            error: function (error) {
              console.log("Error: " + error);
            },
          });
        } else {
          $(".invalid-feedback").text("Date should not be empty");
          $(".invalid-feedback").css("display", "block");
        }
      }
    });


    // *****************************ajax called to get cart product quantity and & total price **********************************
    function ajaxCalled() {
      jQuery.ajax({
        url: `/getCart`,
        type: "POST",
        data: {
          Date: jQuery(this).val(),
        },
        success: function (response) {
          console.log(response);
          if (response.length != 0) {
            response.forEach((element) => {
              console.log("respose");
              $(`[pid="${element.pid}"]`)
                .find(".ticketqty")
                .text(
                  ` : ${
                    response.price != undefined
                      ? parseFloat(element.quantity).toFixed(2)
                      : "0"
                  }`
                );
              $("#price-field").text(
                ` : ${
                  element.TotalPrice != undefined
                    ? parseFloat(element.TotalPrice).toFixed(2)
                    : "0"
                }`
              );
              $("#currency_code").text(
                `${
                  element.TotalCurrency != undefined
                    ? element.TotalCurrency
                    : "AED"
                }`
              );
            });
          } else {
            console.log("yes");
            $("#price-field").text("0");
          }
        },
        error: function (error) {
          console.log("Error: " + error);
        },
      });
    }
  });
});
