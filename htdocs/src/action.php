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
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'No name supplied';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'No interface id supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'modifyUser':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['action']) && !empty($_REQUEST['user_id'])) {
        $output['success'] = $vnstat->modifyUser($_REQUEST['action'], $_REQUEST['user_id']);
        $log['action'] = $_REQUEST['action'];
        $log['user_id'] = $_REQUEST['user_id'];
      } else {
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getUserDetails':
    if ($vnstat->isValidSession() && $vnstat->isAdmin()) {
      if (!empty($_REQUEST['user_id'])) {
        if ($output['data'] = $vnstat->getUserDetails($_REQUEST['user_id'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['user_id'] = $_REQUEST['user_id'];
        }
      } else {
        $output['success'] = false;
        $output['message'] = 'No user id supplied';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'No interface id supplied';
      }
    } else {
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
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
}

if ($putEvent) {
  $vnstat->putEvent($_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));
}

header('Content-Type: application/json');
echo json_encode($output);
?>
