<?php
	namespace IBuildWebApps ;

    class Zetabase
    {

        //Default user
        private $host = '';
        private $user = '';
        private $pass = '';
        private $dbname = '';

        private $dbh;
        private $error;
        private $stmt;

        private $query;
        private $bind_params = array();

        public function query($query)
        {
            $this->query = $query;
            unset($this->bind_params);
            $this->stmt = $this->dbh->prepare($query);
        }

        public function bind($param, $value, $type = null)
        {
            if (is_null($type))
            {
                switch (true)
                {
                    case is_int($value):
                        $type = \PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = \PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = \PDO::PARAM_NULL;
                        break;
                    default:
                        $type = \PDO::PARAM_STR;
                }
            }
            $this->bind_params[] = array('param' => $param, 'value' => $value, 'type' => $type);
            $this->stmt->bindValue($param, $value, $type);
        }

        public function execute()
        {
            try
            {
                $this->stmt->execute();
                $this->error = 'Statement executed.';
                return true;
            } catch (\PDOException $e)
            {
                $this->error = $e->getMessage();
                return false;
            }
        }

        public function resultset()
        {
            $this->execute();
            return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        public function single()
        {
            $this->execute();
            return $this->stmt->fetch(\PDO::FETCH_OBJ);
        }

        public function rowCount()
        {
            return $this->stmt->rowCount();
        }

        public function Count()
        {
            $this->rowCount();
        }

        public function lastInsertId()
        {
            return $this->dbh->lastInsertId();
        }

        public function beginTransaction()
        {
            return $this->dbh->beginTransaction();
        }

        public function endTransaction()
        {
            return $this->dbh->commit();
        }

        public function cancelTransaction()
        {
            return $this->dbh->rollBack();
        }

        public function debug()
        {
            $debug        = new StdClass();
            $debug->query = $this->query;
            foreach ($this->bind_params AS $param)
            {
                $debug->query = preg_replace("/\:{$param['param']}/", "'{$param['value']}'", $debug->query);
            }
            $debug->result = $this->error;
            return $debug;
        }

        public function debugDumpParams()
        {
            return $this->stmt->debugDumpParams();
        }

        public function __construct($connection_details = null)
        {
            //First, set up logging
            //$this->configureMonologging();

            //Use the provided connection details
            if (!empty($connection_details))
            {
                if (!empty($connection_details->host))
                    $this->host = $connection_details->host;
                if (!empty($connection_details->dbname))
                    $this->dbname = $connection_details->dbname;
                if (!empty($connection_details->user))
                    $this->user = $connection_details->user;
                if (!empty($connection_details->pass))
                    $this->pass = $connection_details->pass;
            }

            $dsn = "mysql:host={$this->host};dbname={$this->dbname}";

            $options = array(
                \PDO::ATTR_PERSISTENT         => true,
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                \PDO::ATTR_EMULATE_PREPARES   => true
            );

            try
            {
                $this->dbh = new \PDO($dsn, $this->user, $this->pass, $options);
            } catch (\Exception $e)
            {
                $this->error = $e->getMessage();
                throw $e;
            }
        }

        public function __destruct()
        {
            $this->dbh = null;
        }
    }
