<?php
// unset all data associate with the sleep session.  (not called by anything in sleep.php - this is for just in case)

include("sess_id.php");
session_start();
session_unset();
session_destroy();
?>
dead.