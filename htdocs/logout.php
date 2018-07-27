<?php
require_once('inc/vnstat.class.php');
$vnstat = new vnStat(true, true, false, false);

if ($vnstat->deauthenticateSession()) {
  header('Location: login.php');
} else {
  header('Location: index.php');
}
?>
