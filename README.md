# Zetabase
PHP PDO Database wrapper

//Initialise the connection details
$connectionDetails = new \StdClass() ;
$connectionDetails->host = '' ;
$connectionDetails->dbname = '' ;
$connectionDetails->user = '' ;
$connectionDetails->pass = '' ;

$zetabase = new Zetabase($connectionDetails) ;

//Usage is simple
$zetabase->query('SELECT id, name FROM db1.user WHERE name = :name') ;
$zetabase->bind('name', 'John Smith') ;
$zetabase->execute() ;

//Fetch single result as object
$obj = $zetabase->single() ;

//Fetch array of objects
$objects = $zetabase->resultSet() ;
foreach($objects AS $object)
{
  print_r($object) ;
}
