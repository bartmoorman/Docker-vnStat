<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, true, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $vnstat->appName ?> - Events</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<?php require_once('include.css'); ?>
  </head>
  <body>
<?php require_once('header.php'); ?>
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
<?php require_once('include.js'); ?>
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
