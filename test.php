<?php
$c = stream_context_create(['http'=>['ignore_errors'=>true]]);
echo file_get_contents('http://localhost/Next%20Gen%20Online%20Medicine%20Delivery%20System/api/auth/login', false, $c);
