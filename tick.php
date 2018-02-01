<?php
// this script is a server ticker that's called by a client-side button click.
// step 1: if it's this person's turn, make it the next person's turn.
// step 2: wait until it's this person's turn before returning anything.

include("sess_id.php");
session_start();

$tick = $_POST['tick'];
if (isset($tick) && is_numeric($tick))
{
  if (!isset($_SESSION['turn']))
    $_SESSION['turn'] = 0;
  
  // i think this whole if block would work w/o curly braces.
  if ($_SESSION['turn'] == $tick)
  {
    do
    {
      $_SESSION['turn'] = ($_SESSION['turn'] + 1) % $_SESSION['max_id'];
    }
    while(!isset($_SESSION['tock' . $_SESSION['turn']]));
  }
  
  while($_SESSION['turn'] != $tick)
  {
    session_write_close();
    usleep(10000);
    include("sess_id.php"); // just in case
    session_start();
  }
  
  echo $_SESSION['turn'];
}
?>