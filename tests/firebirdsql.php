<?php
require __DIR__ . '/../vendor/autoload.php';
define('BASEPATH', __DIR__ . '/../vendor/codeigniter/framework/system/'); // endslash!

/* LI-V3.0.4.33054 Firebird 3.0 (superserver) */

$ci3db =& anovsiradj\CI3DataBase::init();
$ci3db->set_db_config_file(__DIR__ . '/cfg.php');

$dsn = 'ibase://sysdba:masterkey@localhost/employee?pconnect=true';
$ci3db->set_db_config('firebirdsql-dsn', $dsn);

try {
	// throw new Exception('${ibase_errmsg}', 1);

	/**
	* cannot using $db0 and $db1
	* @see https://bugs.php.net/bug.php?id=72175
	*/
	$db =& $ci3db->db('firebirdsql');
	// $db =& $ci3db->db('firebirdsql-dsn');

} catch (\Exception $e) {
	echo 'can\'t firebirdsql.', PHP_EOL, $e->getMessage(), PHP_EOL;
	die();
}

$db->trans_start();
if ($db->query('SELECT 1 FROM rdb$relations WHERE rdb$relation_name = UPPER(\'ci3db_tests\')')->num_rows() < 1) {
	$db->query('
		CREATE TABLE ci3db_tests(
			k INTEGER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
			v BLOB SUB_TYPE TEXT
		)
	');
}
$db->trans_complete();

$db->insert(strtoupper('ci3db_tests'), [strtoupper('v') => 'lorem ipsum']);
$db->insert(strtoupper('ci3db_tests'), [strtoupper('v') => 'anovsiradj']);
$db->insert(strtoupper('ci3db_tests'), [strtoupper('v') => 'foo bar']);

$q = $db->from(strtoupper('ci3db_tests'))->get();
dump(
	$q->num_rows(),
	$q->first_row(),
	$q->last_row()
);

$db->simple_query('DROP TABLE ci3db_tests');

$db->close();
