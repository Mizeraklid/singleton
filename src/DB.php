<?php
require __DIR__ . '\Interfaces\DBConnectionInterface.php';

class DB implements DBConnectionInterface
{
    /**
     * @var PDO
     */
    protected $PDO = null;
    protected static $_instance = [];

    private $dsn;
    private $username;
    private $password;

    private function __construct($key)
    {
    }

    final private function __clone()
    {
    }

    function __wakeup()
    {
    }

    function __toString()
    {
        return $this->dsn . $this->username;
    }

    function __destruct()
    {
        $this->close();
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
        if (!key_exists($dsn . $username, self::$_instance)){
            self::$_instance[$dsn . $username] = null;
        }
        if (is_null(self::$_instance[$dsn . $username])) {
            self::$_instance[$dsn . $username] = new self($dsn . $username);
        }
        $tempInstance = self::$_instance[$dsn . $username];
        if ($tempInstance->PDO === null) {
            $tempInstance->setPDOParams($dsn, $username, $password);
            $tempInstance->PDO = new PDO($dsn, $username, $password);
        }
        return $tempInstance;
    }

    protected function setPDOParams($dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Completes the current session connection, and creates a new.
     *
     * @return void
     */
    public function reconnect()
    {
        $this->close();
        self::connect($this->dsn, $this->username, $this->password);
    }

    /**
     * Returns the PDO instance.
     *
     * @return PDO the PDO instance, null if the connection is not established yet
     */
    public function getPdoInstance()
    {
        return $this->PDO;
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
        return $this->PDO->lastInsertId($sequenceName);
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     *
     * @return void
     */
    public function close()
    {
        if ($this->PDO !== null) {
            $this->PDO = null;
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
        return $this->PDO->setAttribute($attribute, $value);
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
        return $this->PDO->getAttribute($attribute);
    }
}