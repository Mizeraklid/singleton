<?php
require __DIR__  . '\Interfaces\DBConnectionInterface.php';

class DB implements DBConnectionInterface
{
    /**
     * @var PDO
     */
    private static $PDO = null;
    private static $_instance = null;

    private static $dsn;
    private static $username;
    private static $password;

    final private function __construct()
    {
    }

    /**
     * Creates new instance representing a connection to a database
     * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
     *
     * @param string $username The user name for the DSN string.
     * @param string $password The password for the DSN string.
     * @see http://www.php.net/manual/en/function.PDO-construct.php
     * @throws  PDOException if the attempt to connect to the requested database fails.
     *
     * @return $this DB
     */

    public static function connect($dsn, $username = '', $password = '')
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        if (self::$PDO === null) {
            self::setPDOParams($dsn, $username, $password);
            self::$PDO = new PDO($dsn, $username, $password);
        }

        return self::$_instance;
    }

    private static function setPDOParams($dsn, $username, $password){
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->close();
        self::connect(self::$dsn, self::$username, self::$password);
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        return self::$PDO;
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @param string $sequenceName name of the sequence object (required by some DBMS)
     *
     * @return string the row ID of the last row inserted, or the last value retrieved from the sequence object
     * @see http://www.php.net/manual/en/function.PDO-lastInsertId.php
     */
    public function getLastInsertID($sequenceName = null)
    {
        return self::$PDO->lastInsertId($sequenceName);
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        if (self::$PDO !== null) {
            self::$PDO = null;
        }
    }

    /**
     * Sets an attribute on the database handle.
     * Some of the available generic attributes are listed below;
     * some drivers may make use of additional driver specific attributes.
     *
     * @param int $attribute
     * @param mixed $value
     *
     * @return bool
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function setAttribute($attribute, $value)
    {
        return self::$PDO->setAttribute($attribute, $value);
    }

    /**
     * Returns the value of a database connection attribute.
     *
     * @param int $attribute
     *
     * @return mixed
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public function getAttribute($attribute)
    {
        return self::$PDO->getAttribute($attribute);
    }
}