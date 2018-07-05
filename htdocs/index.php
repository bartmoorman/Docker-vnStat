<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, false, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>vnStat - Index</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
  </head>
  <body>
<?php
if ($vnstat->isAdmin()) {
  $homeLoc = dirname($_SERVER['PHP_SELF']);
  echo "    <nav class='navbar'>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-success id-nav' data-href='{$homeLoc}'>Home</button>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-info ml-auto mr-2 id-nav' data-href='interfaces.php'>Interfaces</button>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='users.php'>Users</button>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-info id-nav' data-href='events.php'>Events</button>" . PHP_EOL;
  echo "    </nav>" . PHP_EOL;
}
?>
    <canvas id='chart'></canvas>
    <nav class='navbar text-center'>
      <select class='btn btn-sm btn-outline-success ml-auto mr-2 id-interface_id' data-storage='interface_id'>
        <option value='0'>Interface</option>
<?php
foreach ($vnstat->getInterfaces() as $interface) {
  echo "        <option value='{$interface['interface_id']}'>{$interface['alias']} ({$interface['name']})</option>" . PHP_EOL;
}
?>
      </select>
      <select class='btn btn-sm btn-outline-success mr-auto id-granularity' data-storage='granularity'>
        <option value=''>Granularity</option>
<?php
foreach (array_keys($vnstat->granularities) as $granularity) {
  echo "        <option value='{$granularity}'>{$granularity}</option>" . PHP_EOL;
}
?>
      </select>
    </nav>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js' integrity='sha384-F13mJAeqdsVJS5kJv7MZ4PzYmJ+yXXZkt/gEnamJGTXZFzYgAcVtNg5wBDrRgLg9' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js' integrity='sha384-0saKbDOWtYAw5aP4czPUm6ByY5JojfQ9Co6wDgkuM7Zn+anp+4Rj92oGK8cbV91S' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        var timer;
        var config = {
          type: 'line',
          data: {
            datasets: [{
              label: 'Receive',
              backgroundColor: 'rgba(255, 0, 0, 0.3)',
              borderColor: 'rgb(255, 0, 0)',
              borderWidth: 2
            }, {
              label: 'Send',
              backgroundColor: 'rgba(0, 0, 255, 0.3)',
              borderColor: 'rgb(0, 0, 255)',
              borderWidth: 2
            }]
          },
          options: {
            legend: {position: 'bottom'},
            scales: {
              xAxes: [{display: true, type: 'time'}],
              yAxes: [{display: true, scaleLabel: {display: true}}]
            }
          }
        };
        var chart = new Chart($('#chart'), config);

        function getReadings() {
          $.get('src/action.php', {"func": "getReadings", "interface_id": $('select.id-interface_id').val(), "granularity": $('select.id-granularity').val()})
            .done(function(data) {
              if (data.success) {
                config.options.scales.yAxes[0].scaleLabel.labelString = data.data.unit;
                config.data.datasets[0].data = data.data.rx;
                config.data.datasets[1].data = data.data.tx;
                chart.update();
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`getReadings failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            })
            .always(function() {
              timer = setTimeout(getReadings, 30 * 1000);
            });
        };

        $.each(['interface_id', 'granularity'], function(key, value) {
          if (result = sessionStorage.getItem(value)) {
            $(`select.id-${value}`).val(result);
          }
        });

        if ($('select.id-interface_id').val() != 0 && $('select.id-granularity').val()) {
          getReadings();
        }

        $('select.id-interface_id, select.id-granularity').change(function() {
          clearTimeout(timer);
          sessionStorage.setItem($(this).data('storage'), $(this).val());
          if ($('select.id-interface_id').val() != 0 && $('select.id-granularity').val()) {
            getReadings();
          }
        });

        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });
      });
    </script>
  </body>
</html>
