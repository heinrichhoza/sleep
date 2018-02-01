<?php
// this is what's called periodically by the client-side ticker.
// step 1: log the fact that this was called now.
// step 2: check any tocks for inactivity and unset any inactive tocks.  (inactivity threshold == 2 seconds)

include("sess_id.php");
session_start();

// step 1
$tock = $_POST['tock'];
if (isset($tock) && is_numeric($tock))
{
  $_SESSION['tock' . $tock] = date("Y-m-d H:i:s.u");
}

// step 2
$max = $_SESSION['max_id'];
for ($i = 0; $i < $max; $i++)
{
  if (isset($_SESSION['tock' . $i]))
  {
    echo "tock" . $i . ":" . $_SESSION['tock' . $i] . "\n";
    echo "now:" . strtotime("now") . "\n";
    echo "then:" . (isset($_SESSION['tock' . $i]) ? strtotime($_SESSION['tock' . $i]) : "unset") . "\n";
    echo "diff:" . (isset($_SESSION['tock' . $i]) ? strtotime("now") - strtotime($_SESSION['tock' . $i]) : "nan") . "\n";
  }
  
  if (isset($_SESSION['tock' . $i]) && strtotime("now") - strtotime($_SESSION['tock' . $i]) > 2)
  {
    unset($_SESSION['tock' . $i]);
    echo "unsetting...\n";
    // just in case i just unset the current turn
    // (warning: for loop abuse.  ish.)
    for (; !isset($_SESSION['tock' . $_SESSION['turn']]); $_SESSION['turn'] = ($_SESSION['turn'] + 1) % $max)
      ;
  }
  
  if (isset($_SESSION['tock' . $i]))
    echo "\n";
}
print_r($_SESSION);
echo "\nthis request put in at " . date("Y-m-d H:i:s") . "\nyour id:" . $tock;
?>