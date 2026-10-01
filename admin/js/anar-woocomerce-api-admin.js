function awca_move_to_step(stepNumber) {
  let steps = document.querySelectorAll(".step");
  let stepsTitle = document.querySelectorAll(".step-title");
  steps.forEach(function (step, index) {
    if (index + 1 <= stepNumber) {
      step.classList.add("active");
    } else {
      step.classList.remove("active");
    }
  });
  stepsTitle.forEach(function (step, index) {
    if (index + 1 == stepNumber) {
      step.classList.add("activeTitle");
    } else {
      step.classList.remove("activeTitle");
    }
  });
  let stepContents = document.querySelectorAll(".stepContent");
  stepContents.forEach(function (content, index) {
    if (index + 1 === stepNumber) {
      content.classList.add("active");
    } else {
      content.classList.remove("active");
    }
  });
  let persent = stepNumber == 2 ? 85 : 86;
  let progressWidth = ((stepNumber - 1) / (steps.length - 1)) * persent + "%";

  document.querySelector(".stepper-progress").style.width = progressWidth;
}

function awca_show_toast(message, type = "error") {
  Toastify({
    text: message,
    duration: 5000,
    newWindow: true,
    close: true,
    style: {
      background:
        type === "error" ? "#ff6666" : type === "success" ? "#66ff66" : "#999", // Custom background colors for different types
    },
    gravity: "bottom", // `top` or `bottom`
    position: "left", // `left`, `center` or `right`
    stopOnFocus: true, // Prevents dismissing of toast on hover
  }).showToast();
}

function clear_select(target, type = "varient") {
  const selects = document.getElementsByClassName("varient_selcet");
  const categorySelects = document.getElementsByClassName("category_select");
  const cat_icons = document.getElementsByClassName("clear-cat-icon");
  const icons = document.getElementsByClassName("clear-icon");

  if (type != "varient") {
    categorySelects[target].selectedIndex = 0;
    cat_icons[target].style.display = "none";
  } else {
    console.log(target);
    selects[target].selectedIndex = 0;
    icons[target].style.display = "none";
  }
}
function changeVarient(e, target) {
  const icons = document.getElementsByClassName("clear-icon");

  if (e.value != "0") {
    icons[target].style.display = "block";
  } else {
    icons[target].style.display = "none";
  }
}

function changeCategorySelect(e, target) {
  const icons = document.getElementsByClassName("clear-cat-icon");

  if (e.value != "0") {
    icons[target].style.display = "block";
  } else {
    icons[target].style.display = "none";
  }
}

function awca_complete_desc(desc, title = 'توضیحات کامل محصول') {
  jQuery('#fullDesctitle').html(title);
  jQuery('#fullDescContent').html(desc);
  jQuery('#fullDescModal').show();
}

(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */



  jQuery(document).ready(function ($) {

    jQuery('#fullDescModal .close-btn').click(function () {
      jQuery('#fullDescModal').hide();
    });

    // Close modal when clicking outside the modal content
    jQuery('#fullDescModal').click(function (event) {
      if (jQuery(event.target).is(jQuery(this))) {
        jQuery(this).hide();
      }
    });
    jQuery("#plugin_activation_form").on("submit", function (e) {
      e.preventDefault();

      var form = jQuery(this);

      jQuery.ajax({
        url: form.attr("action"),
        type: "POST",
        dataType: "json",
        data: form.serialize(),
        beforeSend: function () {
          jQuery(".spinner-loading").show();
        },
        success: function (response) {
          if (response.success) {
            awca_show_toast(response.message, "success");
            window.location.reload();
          } else {
            awca_show_toast(response.message, "error");
            if (response.activation_status) {
              awca_move_to_step(2);
            }
          }

        },
        error: function (xhr, status, err) {
          jQuery(".spinner-loading").hide();

          awca_show_toast(xhr.responseText)

        },
        complete: function () {
          jQuery(".spinner-loading").hide();
        },
      });
    });


    jQuery("#plugin_category_creation_form").on("submit", function (e) {
      e.preventDefault();

      var form = jQuery(this);

      jQuery.ajax({
        url: form.attr("action"),
        type: "POST",
        dataType: "json",
        data: form.serialize(),
        beforeSend: function () {
          jQuery(".spinner-loading").show();
          jQuery(".configuration_save_button").attr("disabled", "disabled");
        },
        success: function (response) {
          if (response.success) {
            awca_show_toast(response.message, "success");
            awca_move_to_step(4)
          }
        },
        error: function (xhr, status, err) {
          awca_show_toast(xhr.responseText)
          jQuery(".spinner-loading").hide();
          jQuery(".configuration_save_button").removeAttr("disabled");

        },
        complete: function () {
          jQuery(".spinner-loading").hide();
          jQuery(".configuration_save_button").removeAttr("disabled");

        },
      });
    });


    jQuery("#plugin_product_creation_form").on("submit", function (e) {
      e.preventDefault();

      var form = jQuery(this);

      jQuery.ajax({
        url: form.attr("action"),
        type: "POST",
        dataType: "json",
        data: form.serialize(),
        beforeSend: function () {
          jQuery(".spinner-loading").show();
          jQuery(".configuration_save_button").attr("disabled", "disabled");
        },
        success: function (response) {
          if (response.success) {
            awca_show_toast('محصولات شما با موفقیت افزوده شد در حال هدایت به صفحه محصولات ....', "success");
            window.location.href = response.woo_url;

          } else {
            awca_show_toast(response.message);
          }
        },
        error: function (xhr, status, err) {
          awca_show_toast(xhr.responseText)
          jQuery(".spinner-loading").hide();
          jQuery(".configuration_save_button").removeAttr("disabled");

        },
        complete: function () {
          jQuery(".spinner-loading").hide();
          jQuery(".configuration_save_button").removeAttr("disabled");

        },
      });
    });


  });

})(jQuery);
