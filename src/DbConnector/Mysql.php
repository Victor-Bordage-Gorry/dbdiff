<?php

namespace DbDiff\DbConnector;

use DbDiff\DbConnector;

class Mysql extends DbConnector
{
    const DB_TYPE = 'Mysql';

    /**
     * establish a connection with a database
     *
     * @param  string $host     database's host to connect
     * @param  string $login    database's login to connect
     * @param  string $password database's password to connect
     * @param  string $dbname   database's name to connect
     */
    protected function connectDb($host, $login, $password, $dbname)
    {
        try {
            $this->db = new \PDO('mysql:host=' . $host . ';dbname=' . $dbname, $login, $password);
        } catch (\Exception $e) {
            die('Error : ' . $e->getMessage());
        }
    }

    /**
     * Get database's tables
     *
     * @return array
     */
    protected function getDbTables()
    {
        $return = [];
        $result = $this->query('SHOW TABLES');
        if (!$result) {
            return false;
        }
        foreach ($result as $row) {
            $return[] = $row[0];
        }
        return $return;
    }

    /**
     * Get columns and indexes of a table
     *
     * @param  string $tablename    name of the table
     * @return array
     */
    protected function getTableSchema($tablename)
    {
        $return = [];

        // get columns
        $resultColumns = $this->query('SHOW FULL COLUMNS FROM `' . $tablename . '`', \PDO::FETCH_ASSOC);

        if (!$resultColumns) {
            return false;
        }
        foreach ($resultColumns as $row) {
            $return[$row['Field']]['column'] = $row;
        }

        // get indexes
        $resultColumns = $this->query('SHOW INDEX FROM `' . $tablename . '`', \PDO::FETCH_ASSOC);
        if (!$resultColumns) {
            return false;
        }
        foreach ($resultColumns as $row) {
            $return[$row['Column_name']]['index'][$row['Key_name']] = $row;
        }

        return $return;
    }

    /**
     *
     *
     * @param  string $query query to run
     * @param  mixed $opts  optionnal
     * @return PDOStatement
     * @throws RuntimeException
     */
    protected function query($query, $opts = null)
    {
        if ($opts) {
            $result = $this->db->query($query, $opts);
        } else {
            $result = $this->db->query($query);
        }

        if ($result === false) {
            $error = $this->db->errorInfo();
            throw new \RuntimeException('Error : ' . $error[2]);
        }
        return $result;
    }
}
