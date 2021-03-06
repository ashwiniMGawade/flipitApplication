<?php 

namespace Helper;

class PdoHelper
{
    protected $_pdo;

    /**
     * Connect to a database, using PDO dsn, username and password
     * @param  string $dsn      PDO dsn string
     * @param  string $username 
     * @param  string $password 
     * @return Fixture           $this
     */
    public function connect($dsn, $username = null, $password = null)
    {
        $this->_pdo = new \PDO($dsn, $username, $password);
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    /**
     * Getter / Setter of PDO
     * @param  PDO $pdo 
     * @return PDO|Fixture      
     */
    public function pdo(\PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->_pdo = $pdo;
            return $this;
        }
        return $this->_pdo;
    }

    /**
     * Return an array with all the table names
     * @return array 
     */
    public function listTables()
    {
        return $this->pdo()->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Truncate a specific table
     * @param  string $table 
     * @return Fixture        $this
     */
    public function truncate($table)
    {
        $this->pdo()->exec("TRUNCATE TABLE `{$table}`");

        return $this;
    }

    /**
     * Truncate all tables
     * @return Fixture $this
     */
    public function truncateAll()
    {
        foreach ($this->list_tables() as $table) {
            $this->truncate($table);
        }

        return $this;
    }

    /**
     * Truncate all tables and load the data from the file
     * @param  string $file sql inserts file
     * @return Fixture       $this
     */
    public function replace($sql)
    {
        $this->truncate_all();

        $this->load($sql);

        return $this;
    }

    /**
     * Dump the contents of the database as insert statements
     * 
     * @return string
     */
    public function dump()
    {
        $pdo = $this->pdo();

        $sql = '';

        foreach ($this->list_tables() as $table) {
            $query = $pdo->query("SELECT * FROM `{$table}`");

            while ($row = $query->fetch(\PDO::FETCH_NUM)) {
                $values = array();

                foreach ($row as $column) {
                    $values[] = is_null($column) ? 'null' : $pdo->quote($column);
                }

                $sql .= "INSERT INTO `{$table}` VALUES (".join(',', $values). ");\n";
            }
        }

        return $sql;
    }

    /**
     * Execute Some PHP Files
     * @param  array $files 
     * @return Fixture        $this
     */
    public function executeImportFiles(array $files)
    {
        foreach ($files as $file) {
            include $file;
        }
        return $this;
    }

    /**
     * Load the contents of a sql script file, containing inserts and flush tables
     * @param  string $file 
     * @return Fixture       $this
     */
    public function load($sql)
    {
        $this->pdo()->exec($sql);
        $this->pdo()->exec('FLUSH TABLES');

        return $this;
    }

    public function insertInToDb($table, array $arr)
    {
        if (!is_array($arr) || !count($arr)) {
            throw new \Exception("pdoInsert needs an array to insert", 1);
        }

        foreach ($arr as $rec) {
            if (!is_array($rec)) {
                $rec = $arr;
            }
            $bind = ':'.implode(',:', array_keys($rec));
            $sql  = 'insert into '.$table.'('.implode(',', array_keys($rec)).') values ('.$bind.')';
            $stmt = $this->pdo()->prepare($sql);

            $stmt->execute(array_combine(explode(',', $bind), array_values($rec)));
            if ($rec == $arr) {
                break;
            }
        }
    }

    public function restart($databaseName)
    {
        $this->dropDatabase($databaseName);
        $this->createDatabase($databaseName);

        return $this;
    }

    public function dropDatabase($databaseName)
    {
        $this->pdo()->exec("DROP DATABASE IF EXISTS `${databaseName}`");

        return $this;
    }

    public function createDatabase($databaseName)
    {
        $this->pdo()->exec("CREATE DATABASE IF NOT EXISTS `${databaseName}`");

        return $this;
    }
}
