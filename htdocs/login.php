<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, false, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $vnstat->appName ?> - Login</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<?php require_once('include.css'); ?>
  </head>
  <body>
    <div class='modal fade'>
      <div class='modal-dialog modal-sm modal-dialog-centered'>
        <div class='modal-content'>
          <form>
            <div class='modal-header py-2'>
              <h3 class='modal-title w-100 text-center'><?php echo $vnstat->appName ?></h3>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <input class='form-control form-control-lg' id='username' type='text' name='username' placeholder='Username' autofocus required>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group mb-0 col'>
                  <input class='form-control form-control-lg' id='password' type='password' name='password' placeholder='Password' required>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='submit' class='btn btn-lg btn-info btn-block id-login'>Log in</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php require_once('include.js'); ?>
    <script src='//code.jquery.com/ui/1.12.1/jquery-ui.min.js' integrity='sha384-Dziy8F2VlJQLMShA6FHWNul/veM9bCkRUaLqr199K94ntO5QUrLJBEbYegdSkkqX' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('div.modal').modal({backdrop: false, keyboard: false});

        $('form').submit(function(e) {
          e.preventDefault();
          $('button.id-login').prop('disabled', true);
          $.post('src/action.php', {"func": "authenticateSession", "username": $('#username').val(), "password": $('#password').val()})
            .done(function(data) {
              if (data.success) {
                location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
              } else {
                $('div.modal').effect('shake');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`authenticateSession failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            })
            .always(function() {
              $('button.id-login').prop('disabled', false);
            });
        });
      });
    </script>
  </body>
</html>
