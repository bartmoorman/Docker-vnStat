<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(false, true, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>vnStat - Setup</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.0.0/darkly/bootstrap.min.css' integrity='sha384-p8bH4RlA/kdF4wbAoep+/6VeOQI2IAWa9zLjTIQbQLv2FuCjX/W/FkdYdeKISDvK' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
  </head>
  <body>
    <div class='modal d-block'>
      <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'>vnStat Setup</h5>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Username <sup class='text-danger'>*</sup></label>
                  <input class='form-control' id='username' type='text' name='username' pattern='[A-Za-z0-9]+' autofocus required>
                </div>
                <div class='form-group col'>
                  <label>Password <sup class='text-danger'>*</sup></label>
                  <input class='form-control' id='password' type='password' name='password' minlength='6' required>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>First Name <sup class='text-danger'>*</sup></label>
                  <input class='form-control' id='first_name' type='text' name='first_name' required>
                </div>
                <div class='form-group col'>
                  <label>Last Name</label>
                  <input class='form-control' id='last_name' type='text' name='last_name'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Role <sup class='text-danger'>*</sup></label>
                  <input class='form-control' id='role' type='text' name='role' value='admin' readonly required>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='submit' class='btn btn-info'>Setup</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('form').submit(function(e) {
          e.preventDefault();
          $.post('src/action.php', {"func": "createUser", "username": $('#username').val(), "password": $('#password').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "role": $('#role').val()})
            .done(function(data) {
              if (data.success) {
                location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`createUser failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });
      });
    </script>
  </body>
</html>
