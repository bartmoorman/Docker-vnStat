<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(false, true, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $vnstat->appName ?> - Setup</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<?php require_once('include.css'); ?>
  </head>
  <body>
    <div class='modal fade'>
      <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'>vnStat Setup</h5>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Username <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='username' type='text' name='username' pattern='[A-Za-z0-9]+' autofocus required>
                </div>
                <div class='form-group col'>
                  <label>Password <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='password' type='password' name='password' minlength='6' required>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>First Name <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='first_name' type='text' name='first_name' required>
                </div>
                <div class='form-group col'>
                  <label>Last Name</label>
                  <input class='form-control' id='last_name' type='text' name='last_name'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Role <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
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
<?php require_once('include.js'); ?>
    <script>
      $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $('div.modal').modal({backdrop: false, keyboard: false});

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
