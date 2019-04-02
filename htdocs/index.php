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
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' integrity='sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css' integrity='sha384-ae362vOLHy2F1EfJtpMbNW0i9pNM1TP2l5O4VGYYiLJKsaejqVWibbP6BSf0UU5i' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.1.0/css/all.css' integrity='sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt' crossorigin='anonymous'>
  </head>
  <body>
<?php
include_once('header.php');
?>
    <canvas id='chart'></canvas>
    <nav class='navbar text-center'>
      <select class='btn btn-sm btn-outline-success ml-auto mr-2 id-interface_id' data-storage='interface_id'>
        <option value='0'>-- Interface --</option>
<?php
foreach ($vnstat->getInterfaces() as $interface) {
  echo "        <option value='{$interface['interface_id']}'>{$interface['alias']} ({$interface['name']})</option>" . PHP_EOL;
}
?>
      </select>
      <select class='btn btn-sm btn-outline-success mr-auto id-granularity' data-storage='granularity'>
        <option value=''>-- Granularity --</option>
<?php
foreach (array_keys($vnstat->granularities) as $granularity) {
  echo "        <option value='{$granularity}'>{$granularity}</option>" . PHP_EOL;
}
?>
      </select>
    </nav>
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
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
              backgroundColor: 'rgba(30, 144, 255, 0.3)',
              borderColor: 'rgb(30, 144, 255)',
              borderWidth: 2
            }, {
              label: 'Send',
              backgroundColor: 'rgba(255, 0, 0, 0.3)',
              borderColor: 'rgb(255, 0, 0)',
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
              if (jqxhr.status == 403) {
                location.reload();
              } else {
                console.log(`getReadings failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              }
            })
            .always(function() {
              timer = setTimeout(getReadings, 5 * 1000);
            });
        };

        $.each(['interface_id', 'granularity'], function(key, value) {
          if (result = localStorage.getItem(value)) {
            if ($(`select.id-${value} option[value="${result}"]`).length) {
              $(`select.id-${value}`).val(result);
            }
          }
        });

        if ($('select.id-interface_id').val() != 0 && $('select.id-granularity').val()) {
          getReadings();
        }

        $('select.id-interface_id, select.id-granularity').change(function() {
          clearTimeout(timer);
          localStorage.setItem($(this).data('storage'), $(this).val());
          if ($('select.id-interface_id').val() != 0 && $('select.id-granularity').val()) {
            getReadings();
          } else {
            delete config.options.scales.yAxes[0].scaleLabel.labelString;
            delete config.data.datasets[0].data;
            delete config.data.datasets[1].data;
            chart.update();
          }
        });

        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });
      });
    </script>
  </body>
</html>
