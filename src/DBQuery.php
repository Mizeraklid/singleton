<?php
namespace DB;

use DB\Interfaces\DBConnectionInterface;
use DB\Interfaces\DBQueryInterface;
use PDO;
use PDOException;

require __DIR__ . '\Interfaces\DBQueryInterface.php';

class DBQuery implements DBQueryInterface
{
    /**
     * @var DB
     */
    private $DB;
    private $lastQueryTime = null;
    private $startTime;

    /**
     * Create new instance DBQuery.
     *
     * @param DBConnectionInterface $DBConnection
     */
    public function __construct(DBConnectionInterface $DBConnection)
    {
        //parent::__construct($DBConnection);
        $this->setDBConnection($DBConnection);
    }

    /**
     * Returns the DBConnection instance.
     *
     * @return DBConnectionInterface
     */
    public function getDBConnection()
    {
        return $this->DB;
    }

    /**
     * Change DBConnection.
     *
     * @param DBConnectionInterface $DBConnection
     *
     * @return void
     */
    public function setDBConnection(DBConnectionInterface $DBConnection)
    {
        $this->DB = $DBConnection;
    }

    /**
     * Executes the SQL statement and returns query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed if successful, returns a PDOStatement on error false
     */
    public function query($query, $params = null)
    {
        try {
            $this->setStartTime();
            $sth = $this->getDBConnection()->getPdoInstance()->prepare($query);
            $sth->execute($params);
            $this->endTime();

            return $sth;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Executes the SQL statement and returns all rows of a result set as an associative array
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryAll($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return $sth->fetchAll();
    }

    /**
     * Executes the SQL statement returns the first row of the query result
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryRow($query, array $params = null)
    {
        $sth = $this->query($query, $params);
        $result = $sth->fetchAll();

        return count($result) > 1 ? $result[0] : [];
    }

    /**
     * Executes the SQL statement and returns the first column of the query result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return array
     */
    public function queryColumn($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return $sth->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Executes the SQL statement and returns the first field of the first row of the result.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return mixed  column value
     */
    public function queryScalar($query, array $params = null)
    {
        $sth = $this->query($query, $params);

        return $sth->fetchColumn();
    }

    /**
     * Executes the SQL statement.
     * This method is meant only for executing non-query SQL statement.
     * No result set will be returned.
     *
     * @param string $query sql query
     * @param array $params input parameters (name=>value) for the SQL execution
     *
     * @return integer number of rows affected by the execution.
     */
    public function execute($query, array $params = null)
    {
        $sth = $this->query($query, $params);
        $count = $sth->rowCount();

        return $count;
    }

    private function setStartTime()
    {
        $this->startTime = microtime();
    }

    private function endTime()
    {
        $this->lastQueryTime = microtime() - $this->startTime;
    }

    /**
     * Returns the last query execution time in seconds
     *
     * @return float query time in seconds
     */
    public function getLastQueryTime()
    {
        return $this->lastQueryTime;
    }
}