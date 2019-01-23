<?php
if ($vnstat instanceof vnStat) {
  $homeLoc = dirname($_SERVER['PHP_SELF']);
  echo "    <nav class='navbar'>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-success mr-auto id-nav' data-href='{$homeLoc}'>Home</button>" . PHP_EOL;
  if ($vnstat->isAdmin()) {
    echo "      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='interfaces.php'>Interfaces</button>" . PHP_EOL;
    echo "      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='users.php'>Users</button>" . PHP_EOL;
    echo "      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='apps.php'>Apps</button>" . PHP_EOL;
    echo "      <button class='btn btn-sm btn-outline-info mr-2 id-nav' data-href='events.php'>Events</button>" . PHP_EOL;
  }
  echo "      <button class='btn btn-sm btn-outline-danger id-nav' data-href='logout.php' title='Log out'><span class='fa fa-power-off'></span></button>" . PHP_EOL;
  echo "    </nav>" . PHP_EOL;
}
?>
