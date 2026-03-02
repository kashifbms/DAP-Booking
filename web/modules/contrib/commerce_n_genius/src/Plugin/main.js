// window.addEventListener("load", (event) => {
//     jQuery(document).ready(function ($)
//     {

// var inputField = document.getElementById("edit-number--2");
// console.log(inputField);
// if(inputField){
//     // console.log("dfdshfsdf");
// }

// if (inputField) {

//   inputField.setAttribute("type", "range");
//   inputField.setAttribute("min", "0");
//   inputField.setAttribute("max", "6000");
// }
//     }

//     );
// });

let lastScroll = 0;

function sticky_header() {
  let header_height = jQuery("#header.site-header").innerHeight();
  let scroll = jQuery(window).scrollTop();
  if (scroll > header_height && scroll > lastScroll) {
    jQuery("#header.site-header").addClass("hide-header");
    jQuery("body").addClass("scrolled-down");
  } else if (scroll < lastScroll) {
    jQuery("#header.site-header").removeClass("hide-header");
    jQuery("body").removeClass("scrolled-down");
  }
  lastScroll = scroll;
}

jQuery(() => {
  sticky_header();
});

window.onload = () => {
  sticky_header();
};

window.onscroll = () => {
  sticky_header();
};

window.onresize = (event) => {
  sticky_header();
};

window.addEventListener("load", (event) => {
  sticky_header();
});

/*for mobile menu*/
jQuery(document).ready(function () {
  jQuery(".site-header .mobile_menu").click(function () {
    jQuery("html").toggleClass("mobilemenuactive");
  });
  jQuery(".logo_navigation ul.menu li>a").click(function () {
    jQuery("html").removeClass("mobilemenuactive");
  });
  jQuery(".logo_navigation .close_menu").click(function () {
    jQuery("html").removeClass("mobilemenuactive");
  });

  // for filter mobile
  jQuery(".product_list_title .mb-filter_button").click(function () {
    jQuery("html").toggleClass("openFilteractive");
  });
  jQuery(".sidebar_title .close_filter").click(function () {
    jQuery("html").removeClass("openFilteractive");
  });

  jQuery(".owl-carousel").owlCarousel({
    loop: true,
    dots: true,
    autoPlay: true,
    navigation: false,
    nav: false,
    responsive: {
      0: {
        items: 2,
        margin: 13,
      },
      576: {
        items: 3,
        margin: 13,
      },
      992: {
        items: 3,
        margin: 25,
      },
    },
  });
  //
});

jQuery(document).ready(function () {
  // Trigger click event on the first accordion header
  jQuery(".filter-accordion-header:first").trigger("click");
});

jQuery(document).on("click", ".filter-accordion-header", function () {
  jQuery(this).toggleClass("active");
  jQuery(this).next(".filter-accordion-content").slideToggle();

  // Toggle between "+" and "-"
  var span = jQuery(this).find("span");
  var currentText = span.text();
  span.text(currentText === "+" ? "-" : "+");

  // Collapse other accordion sections
  jQuery(".filter-accordion-content").not(jQuery(this).next()).slideUp();
  jQuery(".filter-accordion-header").not(jQuery(this)).removeClass("active");
  jQuery(".filter-accordion-header").not(jQuery(this)).find("span").text("+");
});

window.addEventListener("load", (event) => {
  // Ensure the DOM is fully loaded before executing any code
  jQuery(document).ready(function ($) {
    // Iterate over each element with the class product_price
    jQuery(".product_price").each(function () {
      // Fetch the current value of the product price div
      var currentValue = jQuery(this).text().trim(); // Trim any leading/trailing spaces

      // Convert the value to a float and then format it to remove decimal places
      var formattedValue = parseFloat(currentValue).toFixed(0);

      // Modify the current value (for example, add '$' symbol)
      var newValue = "Price - $" + formattedValue;

      // Update the product price div with the new value
      jQuery(this).text(newValue);
    });
  });
});

window.addEventListener("load", (event) => {
  // Ensure the DOM is fully loaded before executing any code
  jQuery(document).ready(function ($) {
    // Iterate over each element with the class total_price_number
    $(".total_price_number").each(function () {
      // Fetch the current value of the element
      var currentValue = $(this).text().trim(); // Trim any leading/trailing spaces

      // Remove non-numeric characters from the value
      var numericValue = parseFloat(currentValue.replace(/[^\d.]/g, ""));

      // Check if numericValue is a valid number
      if (!isNaN(numericValue)) {
        // Format the value to remove decimal places and add '$' symbol
        var formattedValue = "$" + numericValue.toFixed(0);

        // Update the element with the new value
        $(this).text(formattedValue);
      } else {
        // Log an error if the value couldn't be parsed
        // console.error("Failed to parse value as a number:", currentValue);
      }
    });
  });
});

// window.addEventListener("load", (event) => {
//   // Ensure the DOM is fully loaded before executing any code
//   jQuery(document).ready(function ($) {
//     // Iterate over each element with the class total_price_number
//     $(".home_page_price").each(function () {
//       // Fetch the current value of the element
//       var currentValue = $(this).text().trim(); // Trim any leading/trailing spaces

//       // Remove non-numeric characters from the value
//       var numericValue = parseFloat(currentValue.replace(/[^\d.]/g, ""));

//       // Check if numericValue is a valid number
//       if (!isNaN(numericValue)) {
//         // Format the value to remove decimal places and add '$' symbol
//         var formattedValue = "Price: $" + numericValue.toFixed(0);

//         // Update the element with the new value
//         $(this).text(formattedValue);
//       } else {
//         // Log an error if the value couldn't be parsed
//         // console.error("Failed to parse value as a number:", currentValue);
//       }
//     });
//   });
// });

window.addEventListener("load", (event) => {
  // Ensure the DOM is fully loaded before executing any code
  jQuery(document).ready(function ($) {
    // Iterate over each element with the class total_price_number
    $(".order-total-line-value").each(function () {
      // Fetch the current value of the element
      var currentValue = $(this).text().trim(); // Trim any leading/trailing spaces

      // Remove non-numeric characters from the value
      var numericValue = parseFloat(currentValue.replace(/[^\d.]/g, ""));

      // Check if numericValue is a valid number
      if (!isNaN(numericValue)) {
        // Format the value to remove decimal places and add '$' symbol
        var formattedValue = "$" + numericValue.toFixed(0);

        // Update the element with the new value
        $(this).text(formattedValue);
      } else {
        // Log an error if the value couldn't be parsed
        // console.error("Failed to parse value as a number:", currentValue);
      }
    });
  });
});
window.addEventListener("load", (event) => {
  (function ($) {
    $(document).ready(function () {
      // Assuming your checkbox has id="edit-billing-information-profile-is-default-value"
      $("#edit-billing-information-profile-is-default-value").change(
        function () {
          if ($(this).is(":checked")) {
            if ($(this).is(":checked")) {
              // Copy billing country to delivery country
              var billingCountry = $(".form-select").val();
              $("form-select").val(billingCountry);
            } else {
              // Clear delivery country
              $("form-select").val("");
            }
            // Copy billing fields to delivery fields
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-given-name"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-given-name"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-family-name"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-family-name"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-organization"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-organization"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-address-line1"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-address-line1"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-address-line2"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-address-line2"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-locality"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-locality"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-administrative-area"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-administrative-area"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-postal-code"
            ).val(
              $(
                "#edit-billing-information-profile-address-0-address-postal-code"
              ).val()
            );
            $(
              "#edit-billing-information-profile-field-phone-number-delivery-info-0-value"
            ).val(
              $(
                "#edit-billing-information-profile-field-phone-number-0-value"
              ).val()
            );
          } else {
            // Clear delivery fields
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-given-name"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-family-name"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-organization"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-address-line1"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-address-line2"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-locality"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-administrative-area"
            ).val("");
            $(
              "#edit-billing-information-profile-field-delivery-information-0-address-postal-code"
            ).val("");
            $(
              "#edit-billing-information-profile-field-phone-number-delivery-info-0-value"
            ).val("");
            // Repeat the above line for each field you want to clear
          }
        }
      );
    });
  })(jQuery);
});

window.addEventListener("load", (event) => {
  (function ($) {
    $(document).ready(function () {
      // Remove label and description
      $(
        ".js-form-item-billing-information-profile-is-default-value .option"
      ).remove();
      $(
        ".js-form-item-billing-information-profile-is-default-value .description"
      ).remove();

      // Add custom text
      $(".js-form-item-billing-information-profile-is-default-value").append(
        '<div class="custom-description">My delivery information same as billing information</div>'
      );

      // Assuming your checkbox has class ".form-checkbox"
      $(
        ".js-form-item-billing-information-profile-is-default-value .form-checkbox"
      ).change(function () {
        if ($(this).is(":checked")) {
          // Copy billing country to delivery country
          var billingCountry = $(
            ".billing-information-country.form-select"
          ).val();
          $(".delivery-information-country.form-select").val(billingCountry);
        } else {
          // Clear delivery country
          $(".delivery-information-country.form-select").val("");
        }
      });
    });
  })(jQuery);
});

// add _target blank in image to open anether tab
window.addEventListener("load", (event) => {
  // Ensure the DOM is fully loaded before executing any code
  jQuery(document).ready(function ($) {
    // Iterate over each element with the class total_price_number
    $(".total_price_number").each(function () {
      // Fetch the current value of the element
      var currentValue = $(this).text().trim(); // Trim any leading/trailing spaces

      // Remove non-numeric characters from the value
      var numericValue = parseFloat(currentValue.replace(/[^\d.]/g, ""));

      // Check if numericValue is a valid number
      if (!isNaN(numericValue)) {
        // Format the value to remove decimal places and add '$' symbol
        var formattedValue = "$" + numericValue.toFixed(0);

        // Update the element with the new value
        $(this).text(formattedValue);
      } else {
        // Log an error if the value couldn't be parsed
        // console.error("Failed to parse value as a number:", currentValue);
      }
    });

    //
    function checkCheckboxStatus() {
      jQuery(".facets-checkbox").each(function () {
        if (jQuery(this).is(":checked")) {
          jQuery(this)
            .closest(".filter-accordion")
            .find(".filter-accordion-header")
            .addClass("active");
          jQuery(this)
            .closest(".filter-accordion")
            .find(".filter-accordion-content")
            .css("display", "block");
        } else {
          jQuery(this)
            .closest(".filter-accordion")
            .find(".filter-accordion-header")
            .removeClass("active");
          jQuery(this)
            .closest(".filter-accordion")
            .find(".filter-accordion-content")
            .css("display", "none");
        }
      });
    }
    checkCheckboxStatus();
  });
});

// Function to handle window resize event
function handleResize() {
  const elements = document.querySelectorAll(".home-page-view.views-row");
  const viewportHeight = window.innerHeight;
  console.log("Viewport height:", viewportHeight);
  elements.forEach((element) => {
    element.style.height = viewportHeight + "px";
    element.style.width = "auto";
  });
}

// Add event listener for window resize event
window.addEventListener("resize", handleResize);
function handleDynamicElements(event) {
  const target = event.target;
  if (
    target &&
    typeof target.matches === "function" &&
    target.matches(".home-page-view.views-row")
  ) {
    handleResize();
  }
}
document.addEventListener("DOMNodeInserted", handleDynamicElements);
handleResize();

// // Wait for the document to be ready
// jQuery(document).ready(function($) {
//   // Find all images within the slideshow
//   $('.cycle-slideshow img').each(function() {
//       // Update the onclick event handler to open the image in a new tab/window
//       $(this).attr('onclick', "window.open('" + $(this).attr('src') + "', '_blank')");
//   });
// });

// // Wait for the document to be ready
// jQuery(document).ready(function($) {
//   // Find all images within the slideshow but not within .cycle-pager.external
//   $('.cycle-slideshow img:not(.cycle-pager.external img)').each(function() {
//       // Update the onclick event handler to open the image in a new tab/window
//       $(this).attr('onclick', "window.open('" + $(this).attr('src') + "', '_blank')");
//   });
// });

// window.addEventListener("load", (event) => {
//   jQuery(document).ready(function() {
//     // Function to update filter counts.
//     // Iterate over each checkbox label
//     jQuery('.option').each(function(index) {
//       // Get the count text based on the index (you can replace this with your desired count logic)
//       // var countText = '(' + (index + 1) + ')';
//       // Append the count text after the label
//       jQuery(this).append(' count ' );
//     });
//   });
// })(jQuery);

// (function($) {
//   $(document).ready(function() {
//       // Assuming your exposed filter has a class of 'exposed-filter'
//       $('.exposed-filter').each(function() {
//           var categories = {};
//           // Traverse through each option in the exposed filter
//           $(this).find('option').each(function() {
//               var category = $(this).data('category'); // Assuming data-category attribute holds category information
//               if (category) {
//                   if (!categories[category]) {
//                       categories[category] = 0;
//                   }
//                   categories[category]++;
//               }
//           });

//           // Update UI with category labels and counts
//           $.each(categories, function(category, count) {
//               // Assuming you have a container with class 'category-list' to append the category labels and counts
//               $('.category-list').append('<li>' + category + ' (' + count + ')</li>');
//           });
//       });
//   });
// })(jQuery);

// jQuery(document).on('click','.filter-accordion-header',function(){
//   jQuery(this).toggleClass('active');
//   jQuery(this).next('.filter-accordion-content').slideToggle();

//   // Toggle between "+" and "-"
//   var span = jQuery(this).find('span');
//   var currentText = span.text();
//   span.text(currentText === '+' ? '-' : '+');

//   // Collapse other accordion sections
//   jQuery('.filter-accordion-content').not(jQuery(this).next()).slideUp();
//   jQuery('.filter-accordion-header').not(jQuery(this)).removeClass('active');
//   jQuery('.filter-accordion-header').not(jQuery(this)).find('span').text('+');
// });
