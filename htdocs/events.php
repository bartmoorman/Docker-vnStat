<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, true, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>vnStat - Events</title>
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
            <th>Date</th>
            <th>User Name</th>
            <th>Action</th>
            <th>Message</th>
            <th>Remote Addr</th>
          </tr>
        </thead>
        <tbody>
<?php
$currentPage = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
foreach ($vnstat->getEvents($currentPage) as $event) {
  $date = date('m/d/Y, h:i A', $event['date']);
  $user_name = !empty($event['last_name']) ? sprintf('%2$s, %1$s', $event['first_name'], $event['last_name']) : $event['first_name'];
  $remote_addr = !empty($event['remote_addr']) ? long2ip($event['remote_addr']) : null;
  echo "          <tr>" . PHP_EOL;
  echo "            <td>{$date}</td>" . PHP_EOL;
  echo "            <td>{$user_name}</td>" . PHP_EOL;
  echo "            <td>{$event['action']}</td>" . PHP_EOL;
  echo "            <td>{$event['message']}</td>" . PHP_EOL;
  echo "            <td>{$remote_addr}</td>" . PHP_EOL;
  echo "          </tr>" . PHP_EOL;
}
?>
        </tbody>
      </table>
    </div>
    <nav>
      <ul class='pagination justify-content-center'>
<?php
$pages = ceil($vnstat->getObjectCount('events') / $vnstat->pageLimit);
$group = ceil($currentPage / 5);
$previousPage = $currentPage - 1;
$nextPage = $currentPage + 1;
if ($previousPage <= 0) {
  echo "        <li class='page-item disabled'><a class='page-link'>Previous</a></li>" . PHP_EOL;
} else {
  echo "        <li class='page-item'><a class='page-link id-page' data-page='{$previousPage}'>Previous</a></li>" . PHP_EOL;
}
for ($i=1; $i<=$pages; $i++) {
  if ($currentPage == $i) {
    echo "        <li class='page-item disabled'><a class='page-link bg-secondary id-page' data-page='{$i}'>{$i}</a></li>" . PHP_EOL;
  } elseif (ceil($i / 5) == $group) {
    echo "        <li class='page-item'><a class='page-link id-page' data-page='{$i}'>{$i}</a></li>" . PHP_EOL;
  }
}
if ($nextPage > $pages) {
  echo "        <li class='page-item disabled'><a class='page-link'>Next</a></li>" . PHP_EOL;
} else {
  echo "        <li class='page-item'><a class='page-link id-page' data-page='{$nextPage}'>Next</a></li>" . PHP_EOL;
}
?>
      </ul>
    </nav>
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/URI.min.js' integrity='sha384-p+MfR+v7kwvUVHmsjMiBK3x45fpY3zmJ5X2FICvDqhVP5YJHjfbFDc9f5U1Eba88' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/jquery.URI.min.js' integrity='sha384-zdBrwYVf1Tu1JfO1GKzBAmCOduwha4jbqoCt2886bKrIFyAslJauxsn9JUKj6col' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });
        $('a.id-page').click(function() {
          location.href=URI().removeQuery('page').addQuery('page', $(this).data('page'));
        });
      });
    </script>
  </body>
</html>
