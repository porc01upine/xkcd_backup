<?php
require_once 'functions.php';
// This script should send XKCD updates to all registered emails.
// You need to implement this functionality.
ini_set("SMTP", "localhost");
ini_set("smtp_port", "1025");

require_once 'functions.php';
sendXKCDUpdatesToSubscribers();
