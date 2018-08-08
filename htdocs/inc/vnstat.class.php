<?php
class vnStat {
  private $dbFile = '/config/vnstat.db';
  private $vnStatDbFile = '/var/lib/vnstat/vnstat.db';
  private $dbConn, $vnStatDbConn;
  public $pageLimit = 20;
  public $granularities = ['fiveminute' => 72, 'hour' => 24, 'day' => 30, 'month' => 12, 'year' => 0];
  public $formatBytes = ['base' => 1024, 'units' => ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'], 'decimals' => 2];

  public function __construct($requireConfigured = true, $requireValidSession = true, $requireAdmin = true, $requireIndex = false) {
    session_start([
      'save_path' => '/config/sessions',
      'name' => '_sess_vnstat',
      'gc_maxlifetime' => 60 * 60 * 24 * 7,
      'cookie_lifetime' => 60 * 60 * 24 * 7,
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'use_strict_mode' => true
    ]);

    if (is_writable($this->dbFile)) {
      $this->connectDb($this->dbConn, $this->dbFile);
    } elseif (is_writable(dirname($this->dbFile))) {
      $this->connectDb($this->dbConn, $this->dbFile);
      $this->initDb();
    }

    if ($this->isConfigured()) {
      if ($this->isValidSession()) {
        if (($requireAdmin && !$this->isAdmin()) || $requireIndex) {
          header('Location: index.php');
          exit;
        }
      } elseif ($requireValidSession) {
        header('Location: login.php');
        exit;
      }
    } elseif ($requireConfigured) {
      header('Location: setup.php');
      exit;
    }

    if (is_readable($this->vnStatDbFile)) {
       $this->connectDb($this->vnStatDbConn, $this->vnStatDbFile);
    }
  }

  private function connectDb(&$conn, $file) {
    if ($conn = new SQLite3($file)) {
      $conn->busyTimeout(500);
      $conn->exec('PRAGMA journal_mode = WAL');
      return true;
    }
    return false;
  }

  private function initDb() {
    $query = <<<EOQ
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `username` TEXT NOT NULL UNIQUE,
  `password` TEXT NOT NULL,
  `first_name` TEXT NOT NULL,
  `last_name` TEXT,
  `role` TEXT NOT NULL,
  `disabled` INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `date` INTEGER DEFAULT (STRFTIME('%s', 'now')),
  `user_id` INTEGER,
  `action` TEXT,
  `message` BLOB,
  `remote_addr` INTEGER
);
EOQ;
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function isConfigured() {
    if ($this->getObjectCount('users')) {
      return true;
    }
    return false;
  }

  public function isValidSession() {
    if (array_key_exists('authenticated', $_SESSION) && $this->isValidUser('user_id', $_SESSION['user_id'])) {
      return true;
    }
    return false;
  }

  public function isAdmin() {
    $user_id = $_SESSION['user_id'];
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` = '{$user_id}'
AND `role` = 'admin';
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
    }
    return false;
  }

  public function isValidCredentials($username, $password) {
    $username = $this->dbConn->escapeString($username);
    $query = <<<EOQ
SELECT `password`
FROM `users`
WHERE `username` = '{$username}'
EOQ;
    if (password_verify($password, $this->dbConn->querySingle($query))) {
      return true;
    }
    return false;
  }

  public function isValidUser($type, $value) {
    $type = $this->dbConn->escapeString($type);
    $value = $this->dbConn->escapeString($value);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `{$type}` = '{$value}'
AND NOT `disabled`;
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
    }
    return false;
  }

  public function authenticateSession($username, $password) {
    if ($this->isValidCredentials($username, $password)) {
      $username = $this->dbConn->escapeString($username);
      $query = <<<EOQ
SELECT `user_id`
FROM `users`
WHERE `username` = '{$username}';
EOQ;
      if ($user_id = $this->dbConn->querySingle($query)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = $user_id;
        return true;
      }
    }
    return false;
  }

  public function deauthenticateSession() {
    if (session_unset() && session_destroy()) {
      return true;
    }
    return false;
  }

  public function createUser($username, $password, $first_name, $last_name = null, $role) {
    $username = $this->dbConn->escapeString($username);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `username` = '{$username}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $password = password_hash($password, PASSWORD_DEFAULT);
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $role = $this->dbConn->escapeString($role);
      $query = <<<EOQ
INSERT
INTO `users` (`username`, `password`, `first_name`, `last_name`, `role`)
VALUES ('{$username}', '{$password}', '{$first_name}', '{$last_name}', '{$role}');
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function createInterface($name, $alias = null) {
    $name = $this->vnStatDbConn->escapeString($name);
    $alias = $this->vnStatDbConn->escapeString($alias);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `interface`
WHERE `name` = '{$name}';
EOQ;
    if (!$this->vnStatDbConn->querySingle($query)) {
      $query = <<<EOQ
INSERT
INTO `interface` (`name`, `alias`, `active`, `created`, `updated`, `rxcounter`, `txcounter`, `rxtotal`, `txtotal`)
VALUES ('{$name}', '{$alias}', 1, DATETIME('now', 'localtime'), DATETIME('now', 'localtime'), 0, 0, 0, 0);
EOQ;
      if ($this->vnStatDbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function updateUser($user_id, $username, $password = null, $first_name, $last_name = null, $role) {
    $user_id = $this->dbConn->escapeString($user_id);
    $username = $this->dbConn->escapeString($username);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` != '{$user_id}'
AND `username` = '{$username}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $passwordQuery = null;
      if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $passwordQuery = <<<EOQ
  `password` = '{$password}',
EOQ;
      }
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $role = $this->dbConn->escapeString($role);
      $query = <<<EOQ
UPDATE `users`
SET
  `username` = '{$username}',
{$passwordQuery}
  `first_name` = '{$first_name}',
  `last_name` = '{$last_name}',
  `role` = '{$role}'
WHERE `user_id` = '{$user_id}';
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function updateInterface($interface_id, $alias = null) {
    $interface_id = $this->vnStatDbConn->escapeString($interface_id);
    $alias = $this->vnStatDbConn->escapeString($alias);
    $query = <<<EOQ
UPDATE `interface`
SET `alias` = '{$alias}'
WHERE `id` = '{$interface_id}';
EOQ;
    if ($this->vnStatDbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function modifyUser($action, $user_id) {
    $user_id = $this->dbConn->escapeString($user_id);
    switch ($action) {
      case 'enable':
        $query = <<<EOQ
UPDATE `users`
SET `disabled` = '0'
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
      case 'disable':
        $query = <<<EOQ
UPDATE `users`
SET `disabled` = '1'
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
      case 'delete':
        $query = <<<EOQ
DELETE
FROM `users`
WHERE `user_id` = '{$user_id}';
DELETE
FROM `events`
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
    }
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function modifyInterface($action, $interface_id) {
    $interface_id = $this->vnStatDbConn->escapeString($interface_id);
    switch ($action) {
      case 'enable':
        $query = <<<EOQ
UPDATE `interface`
SET `active` = '1'
WHERE `id` = '{$interface_id}';
EOQ;
        break;
      case 'disable':
        $query = <<<EOQ
UPDATE `interface`
SET `active` = '0'
WHERE `id` = '{$interface_id}';
EOQ;
        break;
      case 'delete':
        $query = <<<EOQ
DELETE
FROM `interface`
WHERE `id` = '{$interface_id}';
EOQ;
        break;
    }
    if ($this->vnStatDbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function getUsers() {
    $query = <<<EOQ
SELECT `user_id`, `username`, `first_name`, `last_name`, `role`, `disabled`
FROM `users`
ORDER BY `last_name`, `first_name`
EOQ;
    if ($users = $this->dbConn->query($query)) {
      $output = [];
      while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $user;
      }
      return $output;
    }
    return false;
  }

  public function getUserDetails($user_id) {
    $user_id = $this->dbConn->escapeString($user_id);
    $query = <<<EOQ
SELECT `user_id`, `username`, `first_name`, `last_name`, `role`, `disabled`
FROM `users`
WHERE `user_id` = '{$user_id}';
EOQ;
    if ($user = $this->dbConn->querySingle($query, true)) {
      return $user;
    }
    return false;
  }

  public function getObjectCount($type) {
    $type = $this->dbConn->escapeString($type);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `{$type}`;
EOQ;
    if ($count = $this->dbConn->querySingle($query)) {
      return $count;
    }
    return false;
  }

  public function putEvent($action, $message = []) {
    $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
    $action = $this->dbConn->escapeString($action);
    $message = $this->dbConn->escapeString(json_encode($message));
    $remote_addr = ip2long(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    $query = <<<EOQ
INSERT
INTO `events` (`user_id`, `action`, `message`, `remote_addr`)
VALUES ('{$user_id}', '{$action}', '{$message}', '{$remote_addr}');
EOQ;
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function getEvents($page = 1) {
    $start = ($page - 1) * $this->pageLimit;
    $query = <<<EOQ
SELECT `event_id`, STRFTIME('%s', `date`, 'unixepoch') AS `date`, `user_id`, `first_name`, `last_name`, `action`, `message`, `remote_addr`, `disabled`
FROM `events`
LEFT JOIN `users` USING (`user_id`)
ORDER BY `date` DESC
LIMIT {$start}, {$this->pageLimit};
EOQ;
    if ($events = $this->dbConn->query($query)) {
      $output = [];
      while ($event = $events->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $event;
      }
      return $output;
    }
    return false;
  }

  public function formatBytes($bytes, $return_array = false) {
    if ($bytes >= $this->formatBytes['base']) {
      $multiple = floor(log($bytes) / log($this->formatBytes['base']));
      $converted = round($bytes / pow($this->formatBytes['base'], $multiple), $this->formatBytes['decimals']);
      if ($return_array) {
        return ['size' => $converted, 'unit' => $this->formatBytes['units'][$multiple], 'multiple' => $multiple];
      } else {
        return "{$converted} {$this->formatBytes['units'][$multiple]}";
      }
    }
    if ($return_array) {
      return ['size' => $bytes, 'unit' => $this->formatBytes['units'][0], 'multiple' => 0];
    } else {
      return "{$bytes} {$this->formatBytes['units'][0]}";
    }
  }

  public function reduceBytes($bytes, $multiple, $with_unit = false) {
    if ($bytes > 0 && $multiple > 0) {
      $converted = round($bytes / pow($this->formatBytes['base'], $multiple), $this->formatBytes['decimals']);
      if ($with_unit) {
        return "{$converted} {$this->formatBytes['units'][$multiple]}";
      } else {
        return $converted;
      }
    }
    if ($with_unit) {
      return "{$bytes} {$this->formatBytes['units'][$multiple]}";
    } else {
      return $bytes;
    }
  }

  public function getInterfaces() {
    $query = <<<EOQ
SELECT `id` AS `interface_id`, `name`, IFNULL(`alias`, `name`) AS `alias`, `active`, `created`, `updated`, `rxcounter`, `txcounter`, `rxtotal`, `txtotal`
FROM `interface`
ORDER BY `name`;
EOQ;
    if ($interfaces = $this->vnStatDbConn->query($query)) {
      $output = [];
      while ($interface = $interfaces->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $interface;
      }
      return $output;
    }
    return false;
  }

  public function getInterfaceDetails($interface_id) {
    $interface_id = $this->vnStatDbConn->escapeString($interface_id);
    $query = <<<EOQ
SELECT `id` AS `interface_id`, `name`, IFNULL(`alias`, `name`) AS `alias`, `active`, `created`, `updated`, `rxcounter`, `txcounter`, `rxtotal`, `txtotal`
FROM `interface`
WHERE `id` = '{$interface_id}'
EOQ;
    if ($interface = $this->vnStatDbConn->querySingle($query, true)) {
      return $interface;
    }
    return false;
  }

  public function getReadings($interface_id, $granularity) {
    $interface_id = $this->vnStatDbConn->escapeString($interface_id);
    $granularity = $this->vnStatDbConn->escapeString($granularity);
    $query = <<<EOQ
SELECT `date`, `rx`, `tx`
FROM `{$granularity}`
WHERE `interface` = '{$interface_id}'
ORDER BY `date` DESC
LIMIT {$this->granularities[$granularity]};
EOQ;
    if ($readings = $this->vnStatDbConn->query($query)) {
      $max = 0;
      while ($reading = $readings->fetchArray(SQLITE3_ASSOC)) {
        $max = max($max, $reading['rx'], $reading['tx']);
      }
      $max = $this->formatBytes($max, true);
      $output = ['rx' => [], 'tx' => [], 'unit' => $max['unit']];
      while ($reading = $readings->fetchArray(SQLITE3_ASSOC)) {
        $output['rx'][] = ['x' => $reading['date'], 'y' => $this->reduceBytes($reading['rx'], $max['multiple'])];
        $output['tx'][] = ['x' => $reading['date'], 'y' => $this->reduceBytes($reading['tx'], $max['multiple']) * -1];
      }
      return $output;
    }
    return false;
  }
}
?>
