<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, true, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>vnStat - Interfaces</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' integrity='sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css' integrity='sha384-ae362vOLHy2F1EfJtpMbNW0i9pNM1TP2l5O4VGYYiLJKsaejqVWibbP6BSf0UU5i' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.1.0/css/all.css' integrity='sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt' crossorigin='anonymous'>
  </head>
  <body>
<?php
include_once('header.php');
?>
    <div class='container'>
      <table class='table table-striped table-hover table-sm'>
        <thead>
          <tr>
            <th><button type='button' class='btn btn-sm btn-outline-success id-add'>Add</button></th>
            <th>Interface ID</th>
            <th>Interface Alias (Name)</th>
            <th>Created</th>
            <th>Updated</th>
            <th>RX Counter</th>
            <th>TX Counter</th>
            <th>RX Total</th>
            <th>TX Total</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($vnstat->getInterfaces() as $interface) {
  $tableClass = $interface['active'] ? 'table-default' : 'text-warning';
  echo "          <tr class='{$tableClass}'>" . PHP_EOL;
  echo "            <td><button type='button' class='btn btn-sm btn-outline-info id-details' data-interface_id='{$interface['interface_id']}'>Details</button></td>" . PHP_EOL;
  echo "            <td>{$interface['interface_id']}</td>" . PHP_EOL;
  echo "            <td>{$interface['alias']} ({$interface['name']})</td>" . PHP_EOL;
  echo "            <td>{$interface['created']}</td>" . PHP_EOL;
  echo "            <td>{$interface['updated']}</td>" . PHP_EOL;
  echo "            <td>{$vnstat->formatBytes($interface['rxcounter'])}</td>" . PHP_EOL;
  echo "            <td>{$vnstat->formatBytes($interface['txcounter'])}</td>" . PHP_EOL;
  echo "            <td>{$vnstat->formatBytes($interface['rxtotal'])}</td>" . PHP_EOL;
  echo "            <td>{$vnstat->formatBytes($interface['txtotal'])}</td>" . PHP_EOL;
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
                  <label>Interface Name <sup class='text-danger id-required' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control id-name' id='name' type='text' name='name' required>
                </div>
                <div class='form-group col'>
                  <label>Interface Alias</label>
                  <input class='form-control' id='alias' type='text' name='alias'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>RX Counter</label>
                  <input class='form-control' id='rxcounter' type='number' name='rxcounter' disabled>
                </div>
                <div class='form-group col'>
                  <label>TX Counter</label>
                  <input class='form-control' id='txcounter' type='number' name='txcounter' disabled>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>RX Total</label>
                  <input class='form-control' id='rxtotal' type='number' name='rxtotal' disabled>
                </div>
                <div class='form-group col'>
                  <label>TX Total</label>
                  <input class='form-control' id='txtotal' type='number' name='txtotal' disabled>
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
        $('[data-toggle="tooltip"]').tooltip();

        $('button.id-add').click(function() {
          $('h5.modal-title').text('Add Interface');
          $('form').removeData('interface_id').data('func', 'createInterface').trigger('reset');
          $('sup.id-required').removeClass('d-none');
          $('input.id-name').prop('required', true).prop('disabled', false);
          $('button.id-modify').addClass('d-none').removeData('interface_id');
          $('button.id-submit').removeClass('btn-info').addClass('btn-success').text('Add');
          $('div.id-modal').modal('toggle');
        });

        $('button.id-details').click(function() {
          $('h5.modal-title').text('Interface Details');
          $('form').removeData('interface_id').data('func', 'updateInterface').trigger('reset');
          $('sup.id-required').addClass('d-none');
          $('input.id-name').prop('required', false).prop('disabled', true);
          $('button.id-modify').removeClass('d-none').removeData('interface_id');
          $('button.id-submit').removeClass('btn-success').addClass('btn-info').text('Save');
          $.get('src/action.php', {"func": "getInterfaceDetails", "interface_id": $(this).data('interface_id')})
            .done(function(data) {
              if (data.success) {
                interface = data.data;
                $('form').data('interface_id', interface.interface_id);
                $('#name').val(interface.name);
                $('#alias').val(interface.alias);
                $('#rxcounter').val(interface.rxcounter);
                $('#txcounter').val(interface.txcounter);
                $('#rxtotal').val(interface.rxtotal);
                $('#txtotal').val(interface.txtotal);
                $('button.id-modify.id-volatile').data('action', interface.active ? 'disable' : 'enable').text(interface.active ? 'Disable' : 'Enable');
                $('button.id-modify').data('interface_id', interface.interface_id);
                $('div.id-modal').modal('toggle');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`getInterfaceDetails failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });

       $('button.id-modify').click(function() {
          if (confirm(`Want to ${$(this).data('action').toUpperCase()} interface ${$(this).data('interface_id')}?`)) {
            $.get('src/action.php', {"func": "modifyInterface", "action": $(this).data('action'), "interface_id": $(this).data('interface_id')})
              .done(function(data) {
                if (data.success) {
                  location.reload();
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`modifyInterface failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
          }
        });

        $('form').submit(function(e) {
          e.preventDefault();
          $.post('src/action.php', {"func": $(this).data('func'), "interface_id": $(this).data('interface_id'), "name": $('#name').val(), "alias": $('#alias').val()})
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
