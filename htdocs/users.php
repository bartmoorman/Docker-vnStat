<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, true, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>vnStat - Users</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' integrity='sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css' integrity='sha384-ae362vOLHy2F1EfJtpMbNW0i9pNM1TP2l5O4VGYYiLJKsaejqVWibbP6BSf0UU5i' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.1.0/css/all.css' integrity='sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt' crossorigin='anonymous'>
  </head>
  <body>
    <nav class='navbar'>
      <button class='btn btn-sm btn-outline-success id-nav' data-href='<?php echo dirname($_SERVER['PHP_SELF']) ?>'>Home</button>
      <button class='btn btn-sm btn-outline-info ml-auto mr-2 id-nav' data-href='interfaces.php'>Interfaces</button>
      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='users.php'>Users</button>
      <button class='btn btn-sm btn-outline-info id-nav' data-href='events.php'>Events</button>
    </nav>
    <div class='container'>
      <table class='table table-striped table-hover table-sm'>
        <thead>
          <tr>
            <th><button type='button' class='btn btn-sm btn-outline-success id-add'>Add</button></th>
            <th>User ID</th>
            <th>Username</th>
            <th>User Name</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($vnstat->getUsers() as $user) {
  $user_name = !empty($user['last_name']) ? sprintf('%2$s, %1$s', $user['first_name'], $user['last_name']) : $user['first_name'];
  $tableClass = $user['disabled'] ? 'text-warning' : 'table-default';
  echo "          <tr class='{$tableClass}'>" . PHP_EOL;
  echo "            <td><button type='button' class='btn btn-sm btn-outline-info id-details' data-user_id='{$user['user_id']}'>Details</button></td>" . PHP_EOL;
  echo "            <td>{$user['user_id']}</td>" . PHP_EOL;
  echo "            <td>{$user['username']}</td>" . PHP_EOL;
  echo "            <td>{$user_name}</td>" . PHP_EOL;
  echo "            <td>{$user['role']}</td>" . PHP_EOL;
  echo "          </tr>" . PHP_EOL;
}
?>
        </tbody>
      </table>
    </div>
    <div class='modal fade id-modal'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'></h5>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Username <sup class='text-danger'>*</sup></label>
                  <input class='form-control' id='username' type='text' name='username' pattern='[A-za-z0-9]+' required>
                </div>
                <div class='form-group col'>
                  <label>Password <sup class='text-danger id-required'>*</sup></label>
                  <input class='form-control id-password' id='password' type='password' name='password' minlength='6' required>
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
                  <select class='form-control' id='role' name='role' required>
                    <option value='user'>user</option>
                    <option value='admin'>admin</option>
                  </select>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-outline-warning id-modify id-volatile'></button>
              <button type='button' class='btn btn-outline-danger mr-auto id-modify' data-action='delete'>Delete</button>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
              <button type='submit' class='btn id-submit'></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('button.id-add').click(function() {
          $('h5.modal-title').text('Add User');
          $('form').removeData('user_id').data('func', 'createUser').trigger('reset');
          $('sup.id-required').removeClass('d-none');
          $('input.id-password').prop('required', true);
          $('button.id-modify').addClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-info').addClass('btn-success').text('Add');
          $('div.id-modal').modal('toggle');
        });
        $('button.id-details').click(function() {
          $('h5.modal-title').text('User Details');
          $('form').removeData('user_id').data('func', 'updateUser').trigger('reset');
          $('sup.id-required').addClass('d-none');
          $('input.id-password').prop('required', false);
          $('button.id-modify').removeClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-success').addClass('btn-info').text('Save');
          $.get('src/action.php', {"func": "getUserDetails", "user_id": $(this).data('user_id')})
            .done(function(data) {
              if (data.success) {
                user = data.data;
                $('form').data('user_id', user.user_id);
                $('#username').val(user.username);
                $('#first_name').val(user.first_name);
                $('#last_name').val(user.last_name);
                $('#role').val(user.role);
                $('button.id-modify.id-volatile').data('action', user.disabled ? 'enable' : 'disable').text(user.disabled ? 'Enable' : 'Disable');
                $('button.id-modify').data('user_id', user.user_id);
                $('div.id-modal').modal('toggle');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`getUserDetails failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });
        $('button.id-modify').click(function() {
          if (confirm(`Want to ${$(this).data('action').toUpperCase()} user ${$(this).data('user_id')}?`)) {
            $.get('src/action.php', {"func": "modifyUser", "action": $(this).data('action'), "user_id": $(this).data('user_id')})
              .done(function(data) {
                if (data.success) {
                  location.reload();
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`removeUser failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
          }
        });
        $('form').submit(function(e) {
          e.preventDefault();
          $.post('src/action.php', {"func": $(this).data('func'), "user_id": $(this).data('user_id'), "username": $('#username').val(), "password": $('#password').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "role": $('#role').val()})
            .done(function(data) {
              if (data.success) {
                location.reload();
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`${$(this).data('func')} failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });
        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });
      });
    </script>
  </body>
</html>
