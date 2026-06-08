<?php
$filename = "medimitra_test_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=' . $filename);
echo "ID,Name,Status\n1,Test,Success\n2,Fix,Active";
exit;
