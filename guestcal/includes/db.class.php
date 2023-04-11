<?php
// error_reporting(E_ALL);
// ini_set('display_errors', true);
// Class for database connection
class DatabaseConnection {

    public    $error = false;
    public    $version = false;
    private    $link = false;
    private $config = array ();

    /**
     * Wrapper for connect ()
     * @param    $db        Array with vars from config
     * @return            result from connect ()
     */
    // public function databaseConnection ($mysql) {
    public function __construct ($mysql) {
        $this->config = $mysql;
        return $this->connect ();
    }

    /**
     * Tries to connect to db and checks installation.
     * @return            true if no error, else false
     */
    public function connect () {
        // Reset $error
        $this->error = false;
        // Try to connect to db
        $this->link = @mysqli_connect($this->config['host'] . ':' . $this->config['port'], $this->config['user'], $this->config['pass']);
        if (! $this->link)
        {
            echo "Connection failed, Config:<br><pre>\n";
            var_dump($this->config);
            die("</pre>");
        }
//        if (!$this->link)
//            $this->error = 1; // Connection failed.
        else {
            if (function_exists ('mysql_set_charset'))
                mysqli_set_charset($this->link,'utf8');
            // Try to select db
            if (mysqli_select_db($this->link,$this->config['name'])) {
                // Look for prefs table
                $version = $this->select ("SELECT `value` FROM `prefs` WHERE `name`='dbVersion'");
                if (!$version) {
                    // Check for v1.x installation with this year's table
                    $tables = $this->select ("SHOW TABLES");
                    if ($tables) {
                        foreach ($tables as $table) {
                            if (in_array ($this->config['pfix'] . date ('Y'), $table)) {
                                $this->error = 5; // v1.x installation found
                                break;
                            }
                        }
                    }
                    if (!$this->error)
                        $this->error = 3; // Table does not exist
                }
                // Check for db version
                else {
                    $this->version = $version[0]['value'];
                    if ($this->version != VERSION)
                        $this->error = 4; // DB-Version and Files-Version differ
                }
            }
            else
                $this->error = 2; // Database does not exist
        }
        if ($this->error)
            return false;
        else
            return true;
    }

    /**
     * Validates an id
     * @param    $table    table to query
     * @param    $id        id to check
     * @return            true if exists, else false
     */
    function idExists ($table, $id) {
        if (is_numeric ($id)) {
            $query = "SELECT `id`
                FROM `" . $table . "`
                WHERE `id`='" . $id . "'
                LIMIT 1";
            $query = $this->queryAddTablePrefixes ($query);
            $result = mysqli_query($this->link, $query);
            if (mysqli_num_rows($result)) {
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Submits a query to db.
     * @param    $query    String
     * @param    $return    String, what should be returned: affected_rows | insert_id
     * @return            int affected rows, error: false
     */
    public function query ($query, $return = 'affected_rows') {
        $query = $this->queryAddTablePrefixes ($query);
        $result = mysqli_query($this->link,$query);
        if (!$result) {
            if (DEBUG)
                trigger_error (mysqli_error($this->link));
            return false;
        }
        else {
            switch ($return) {
                case 'insert_id':
                    return mysqli_insert_id($this->link);
                    break;
                default:
                    return mysqli_affected_rows($this->link);
            }
        }
    }

    /**
     * Submits a SELECT-query to db and returns rows as multidimensional array.
     * @param    $query    String
     * @return            Array, error: false
     */
    public function select ($query) {
        $query = $this -> queryAddTablePrefixes ($query);
        $result = mysqli_query($this -> link,$query);
        if (!$result) {
            if (DEBUG)
                trigger_error (mysqli_error($this->link));
            return false;
        }
        else {
            $num = mysqli_num_rows($result);
            if ($num) {
                $array = array ();
                while ($row = mysqli_fetch_assoc($result))
                    $array[] = $row;
                return $array;
            }
            else
                return false;
        }
    }

    /**
     * Creates the given db and selects it if successfull.
     * @return        true if successful, else MySQL-error
     */
    public function createDB () {
        $query = "CREATE DATABASE `" . $this->config['name'] . "`";
        $result = mysqli_query($this->link,$query);
        if (!$result)
            return mysqli_error($this->link);
        else {
            mysqli_select_db($this->link,$this->config['name']);
            return true;
        }
    }

    /**
     * Updates database to current GuestCal version.
     * @return        true if successful, else error message
     */
    public function update ($reconnect = false) {
        $errors = array ();
        if ($this->error == 4) { // update if file and db versions differ
            $result = $this->importDump ('sql/update_' . $this->version . '.mysql');
            if ($result === true) {
                $this->close();
                $this->connect();
                return $this->update();
            }
            else {
                $errors[] = 'Database could not be updated to version ' . VERSION;
                $errors = array_merge ($errors, $result);
                return $errors;
            }
        }
        else
            return true;
    }

    /**
     * Imports SQL dump from a file
     * @param    $filename    Path to file
     * @return                true if successful, else array with errors
     */
    public function importDump ($filename) {
        if (!file_exists ($filename))
            return array ('File "' . $filename . '" does not exist.');
        // Read file
        $content = file_get_contents ($filename);
        // Split into statements
        $lines = explode (";\n", $content);
        // Execute statements
        $errors = array ();
        foreach ($lines as $line) {
            if (!empty ($line)) {
                $query = $this -> queryAddTablePrefixes ($line);
                $result = mysqli_query($this->link,$query);
                if (!$result) {
                    $errors[] = 'MySQL-Error: "' . mysqli_error($this->link) . '"';
                }
            }
        }
        if (count ($errors))
            return $errors;
        else
            return true;
    }

    /**
     * Imports tables from 1.x installation.
     * @param    $prefix        table prefix of version to be imported
     * @param    $objectId    id of the object, in which the data will be imported
     * @return                number of tables imported
     */
    public function importFromV1 ($prefix = false, $objectId = 1) {
        $countImported = 0;
        if (!$prefix)
            $prefix = $this->config['pfix'];
        $newClasses = array (
            '1' => 2,
            '2' => 3
        );
        $tables = $this->select ("SHOW TABLES");
        if ($tables) {
            foreach ($tables as $table) {
                $year = array_pop ($table);
                $year = str_replace ($prefix, '', $year);
                if (is_numeric ($year) && $year > 1970 && $year < 2038) {
                    $array = $this->select ("SELECT `tag`, `belegt`, `infotext` FROM `" . $prefix . $year . "`");
                    $belegt = 0;
                    $infotext = '';
                    $from = false;
                    $to = false;
                    foreach ($array as $row) {
                        $month = substr($row['tag'], 0, 2);
                        $day = substr($row['tag'], 2, 2);
                        if (@checkdate ($month, $day, $year)) {
                            $now = $year . '-' . $month . '-' . $day;
                            if ($row['belegt'] != $belegt or $row['infotext'] != $infotext) {
                                if ($belegt != 0 || $infotext != '') {
                                    $id = $this->query ("INSERT INTO `entries` SET `object_id`=" . $objectId . ", `class_id`=" . $newClasses[$belegt] . ", `kind`='static'", 'insert_id');
                                    $this->query ("INSERT INTO `entries_static` SET `entry_id`=" . $id . ", `from`='" . $from . "', `to`='" . $to . "'");
                                    $this->query ("INSERT INTO `entries_per_language` SET `entry_id`=" . $id . ", `language_id`=1, `desc`='" . $infotext . "'");
                                }
                                $belegt = $row['belegt'];
                                $infotext = $row['infotext'];
                                $from = $now;
                            }
                        }
                        $to = $now;
                    }
                    $countImported ++;
                }
            }
        }
        return $countImported;
    }

    /**
     * Closes connection.
     */
    public function close () {
        if ($this->link)
            return mysqli_close($this->link);
    }

    /**
     * Adds prefix to table names in query.
     * @param    $query    String
     * @return            String
     */
    private function queryAddTablePrefixes ($query) {
        $naked_tables = array (
            '`classes`',
            '`classes_per_language`',
            '`entries`',
            '`entries_per_language`',
            '`entries_static`',
            '`entry_returning`',
            '`languages`',
            '`objects`',
            '`objects_per_language`',
            '`prefs`',
            '`users`'
        );
        $prefixed_tables = array ();
        foreach ($naked_tables as $table)
            $prefixed_tables[] = '`' . $this->config['pfix'] . substr ($table, 1);
        $query = str_replace ($naked_tables, $prefixed_tables, $query);
        return $query;
    }

    function mysql_real_escape_mixed ($var) {
        if (is_array ($var)) {
            foreach ($var as $name => $value) {
                if (is_array ($value)) {
                    $var[$name] = $this->mysql_real_escape_mixed ($value);
                }
                else {
                    $var[$name] = mysqli_real_escape_string($this->link,$value);
                }
            }
        }
        else {
            $var = mysqli_real_escape_string($this->link,$var);
        }
        return $var;
    }

    private function stripslashes_a ($array) {
        if (count ($array) > 0) {
            foreach ($array as $name => $value) {
                if (is_array ($value)) {
                    $array[$name] = stripslashes_a ($value);
                }
                else {
                    $array[$name] = stripslashes ($value);
                }
            }
        }
        return $array;
    }
}
