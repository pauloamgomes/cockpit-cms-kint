<?php

/**
 * @file
 * Cockpit module bootstrap implementation.
 */

// Kint library.
require __DIR__ . '/vendor/autoload.php';

$this->module('kint')->extend([
  'dump' => function() {
    $_SESSION['kint_dump'][] = func_get_args();
  },
  'console' => function() {
    $_SESSION['kint_console'][] = func_get_args();
  },
]);

if (!COCKPIT_API_REQUEST) {
  $app->on('shutdown', function() {
    // Kint output.
    if (!empty($_SESSION['kint_dump'])) {
      $args = $_SESSION['kint_dump'];
      d($args);
      unset($_SESSION['kint_dump']);
    }
    // Kint console output.
    if (!empty($_SESSION['kint_console'])) {
      $args = $_SESSION['kint_console'];
      j($args);
      unset($_SESSION['kint_console']);
    }
  });
}
else {
  $app->on('after', function() {
    if (!empty($_SESSION['kint_dump'])) {
      header('X-Cockpit-Kint-Dump: ' . json_encode($_SESSION['kint_dump']));
      unset($_SESSION['kint_dump']);
    }
    if (!empty($_SESSION['kint_console'])) {
      header('X-Cockpit-Kint-Console: ' . json_encode($_SESSION['kint_console']));
      unset($_SESSION['kint_console']);
    }
  });
}

