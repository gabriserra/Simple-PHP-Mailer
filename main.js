// -----------------------------------------
// MAIN.JS
// It contains js code to permit form submit
// ------------------------------------------

$(document).ready(function() {
    $('#summernote').summernote({
      placeholder: 'Insert here your content. Use the shortcode {company_name} to insert the company name.',
      callbacks: {
        onChange: function(contents, $editable) {
          fillup_preview(contents);
        }
      }
    });
  });

function fillup_preview(raw_html) {
  $('.email-preview').html(raw_html);
  $('.sender').html($("input[name='name']").val() + ' <' + $( "input[name='email']" ).val() + '>');
  $('.mailsub').html($("input[name='subject']").val());
}

// Submit form with CSV file
function submit_form() {
  var $form = $('#form-mail');

  if(!$form[0].checkValidity()) {
    alert("Some fields are empty or bad compiled.");
    return;
  }

  data = new FormData(document.getElementById('form-mail'));

  ajax_req_file(
      'sendmail.php',
      data,     
      on_succ, 
      on_err
  );
}

function on_succ(reply) {
  if(!reply.error) {
    $('.alert-success-text').html(reply.message);
    $('.alert-success').show();
  } else {
    $('.alert-danger-text').html(reply.message);
    $('.alert-danger').show();
  }
}

function on_err(reply) {
  $('.alert-danger-text').html(reply.message);
  $('.alert-danger').show();
}

// -------------------------------------
// UTILITY
// -------------------------------------

function ajax_req_file(dest, info, succ, err) {
  $.ajax({
      type: "POST",
      url: dest,
      data: info,
      dataType: "json",
      processData: false,
      contentType: false,
      success: succ,
      error: err
  });
}