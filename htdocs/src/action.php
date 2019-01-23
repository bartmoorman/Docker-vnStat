<?php
require_once('../inc/vnstat.class.php');
$vnstat = new vnStat(false, false, false, false);

$output = $logFields = ['success' => null, 'message' => null];
$log = [];
$putEvent = true;

switch ($_REQUEST['func']) {
  case 'authenticateSession':
    if (!empty($_REQUEST['username']) && !empty($_REQUEST['password'])) {
      $output['success'] = $vnstat->authenticateSession($_REQUEST['username'], $_REQUEST['password']);
      $log['username'] = $_REQUEST['username'];
    } else {
      header('HTTP/1.1 400 Bad Request');
      $output['success'] = false;
      $output['message'] = 'Missing arguments';
    }
    break;
  case 'createUser':
    if (!$vnstat->isConfigured() || ($vnstat->isValidSession() && $vnstat->isAdmin())) {
      if (!empty($_REQUEST['username']) && !empty($_REQUEST['password']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $output['success'] = $vnstat->createUser($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['first_name'], $last_name, $_REQUEST['role']);
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'createInterface':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['name'])) {
        $alias = !empty($_REQUEST['alias']) ? $_REQUEST['alias'] : null;
        $output['success'] = $vnstat->createInterface($_REQUEST['name'], $alias);
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No name supplied';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'createApp':
    if ($dashboard->isValidSession() && $dashboard->isAdmin()) {
      if (!empty($_REQUEST['name'])) {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $dashboard->createApp($_REQUEST['name'], $token, $begin, $end);
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No name supplied';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'updateUser':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['username']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : null;
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $output['success'] = $vnstat->updateUser($_REQUEST['user_id'], $_REQUEST['username'], $password, $_REQUEST['first_name'], $last_name, $_REQUEST['role']);
        $log['user_id'] = $_REQUEST['user_id'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'updateInterface':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['interface_id'])) {
        $alias = !empty($_REQUEST['alias']) ? $_REQUEST['alias'] : null;
        $output['success'] = $vnstat->updateInterface($_REQUEST['interface_id'], $alias);
        $log['interface_id'] = $_REQUEST['interface_id'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No interface id supplied';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'updateApp':
    if ($dashboard->isValidSession() && $dashboard->isAdmin()) {
      if (!empty($_REQUEST['app_id']) && !empty($_REQUEST['name']) && !empty($_REQUEST['token'])) {
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $dashboard->updateApp($_REQUEST['app_id'], $_REQUEST['name'], $_REQUEST['token'], $begin, $end);
        $log['app_id'] = $_REQUEST['app_id'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'modifyObject':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['action']) && !empty($_REQUEST['type']) && !empty($_REQUEST['value'])) {
        $output['success'] = $vnstat->modifyObject($_REQUEST['action'], $_REQUEST['type'], $_REQUEST['value']);
        $log['action'] = $_REQUEST['action'];
        $log['type'] = $_REQUEST['type'];
        $log['value'] = $_REQUEST['value'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'modifyInterface':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['action']) && !empty($_REQUEST['interface_id'])) {
        $output['success'] = $vnstat->modifyInterface($_REQUEST['action'], $_REQUEST['interface_id']);
        $log['action'] = $_REQUEST['action'];
        $log['interface_id'] = $_REQUEST['interface_id'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getObjectDetails':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['type']) && !empty($_REQUEST['value'])) {
        if ($output['data'] = $vnstat->getObjectDetails($_REQUEST['type'], $_REQUEST['value'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['type'] = $_REQUEST['type'];
          $log['value'] = $_REQUEST['value'];
        }
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getInterfaceDetails':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['interface_id'])) {
        if ($output['data'] = $vnstat->getInterfaceDetails($_REQUEST['interface_id'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['interface_id'] = $_REQUEST['interface_id'];
        }
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No interface id supplied';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getReadings':
    if ($vnstat->isValidSession()) {
      if (!empty($_REQUEST['interface_id']) && !empty($_REQUEST['granularity'])) {
        if ($output['data'] = $vnstat->getReadings($_REQUEST['interface_id'], $_REQUEST['granularity'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['interface_id'] = $_REQUEST['interface_id'];
        }
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 403 Forbidden');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
}

if ($putEvent) {
  $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
  $vnstat->putEvent($user_id, $_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));
}

header('Content-Type: application/json');
echo json_encode($output);
?>
