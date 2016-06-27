<?php

namespace DbDiff\Connexion;

use DbDiff\Connexion;

class Mysql extends Connexion {

    protected function connectDb($host, $login, $password, $dbname) {
        try{
            $this->db = new \PDO('mysql:host=' . $host . ';dbname=' . $dbname, $login, $password);
        }
        catch (\Exception $e) {
            die('Error : ' . $e->getMessage());
        }
    }

    /**
     * Get database's tables
     *
     * @return array
     */
    protected function getDbTables() {
        $return = array();
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
     * @param  string $tablename
     * @return array
     */
    protected function getTableSchema($tablename) {
        $return = array();

        // get columns
        $result_columns = $this->query('SHOW FULL COLUMNS FROM `' . $tablename . '`', \PDO::FETCH_ASSOC);

        if (!$result_columns) {
            return false;
        }
        foreach ($result_columns as $row) {
            $return[$row['Field']]['column'] = $row;
        }

        // get indexes
        $result_columns = $this->query('SHOW INDEX FROM `' . $tablename . '`', \PDO::FETCH_ASSOC);
        if (!$result_columns) {
            return false;
        }
        foreach ($result_columns as $row) {
            $return[$row['Column_name']]['index'][$row['Key_name']] = $row;
        }

        return $return;
    }

    /**
     *
     *
     * @param  string $query
     * @param  mixed $opts
     * @return PDOStatement
     * @throws RuntimeException
     */
    protected function query($query, $opts = null) {
        if ($opts) {
            $result = $this->db->query($query, $opts);
        } else {
            $result = $this->db->query($query);
        }

        if($result === false) {
            $error = $this->db->errorInfo();
            throw new \RuntimeException('Error : ' . $error[2]);
        }
        return $result;
    }
}