--TEST--
fetching cursor from a statement
--SKIPIF--
<?php
$target_dbs = array('oracledb' => true, 'timesten' => false);  // test runs on these DBs
require(dirname(__FILE__).'/skipif.inc');
?> 
--FILE--
<?php

require(dirname(__FILE__)."/connect.inc");

// Initialize

$stmtarray = array(
    "drop table cursors_old_tab",
    "create table cursors_old_tab (id number, value number)",
    "insert into cursors_old_tab (id, value) values (1,1)",
    "insert into cursors_old_tab (id, value) values (1,1)",
    "insert into cursors_old_tab (id, value) values (1,1)",
);

oci8_test_sql_execute($c, $stmtarray);

// Run Test

$sql = "select cursor(select * from cursors_old_tab) as curs from dual";
$stmt = ociparse($c, $sql);

ociexecute($stmt);

while ($result = ocifetchinto($stmt, $data, OCI_ASSOC)) {
	ociexecute($data["CURS"]);
	ocifetchinto($data["CURS"], $subdata, OCI_ASSOC);
	var_dump($subdata);
	var_dump(ocicancel($data["CURS"]));
	ocifetchinto($data["CURS"], $subdata, OCI_ASSOC);
	var_dump($subdata);
	var_dump(ocicancel($data["CURS"]));
}

// Cleanup

$stmtarray = array(
    "drop table cursors_old_tab"
);

oci8_test_sql_execute($c, $stmtarray);

echo "Done\n";

?>
--EXPECTF--
array(2) {
  [%u|b%"ID"]=>
  %unicode|string%(1) "1"
  [%u|b%"VALUE"]=>
  %unicode|string%(1) "1"
}
bool(true)

Warning: ocifetchinto():%sORA-01002: %s in %scursors_old.php on line %d
array(2) {
  [%u|b%"ID"]=>
  %unicode|string%(1) "1"
  [%u|b%"VALUE"]=>
  %unicode|string%(1) "1"
}
bool(true)
Done
