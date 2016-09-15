<?php namespace Creios\MVCFW\MVC\Model;

use Creios\MVCFW\MVC\Model;
use PDO;
use PDOException;

class BDB
{
    /** @var string */
    public $tableName;
    /** @var string */
    public $whereClause;
    /** @var string */
    public $columns = "";
    /** @var string */
    public $joins = "";
    /** @var array */
    public $bindings = [];
    /** @var string */
    public $query = "";
    /** @var string */
    public $updateValues = "";

    protected $oDb;
    /** @param string $tableName */
    public function __construct($tableName)
    {
        $this->oDb = $GLOBALS['oDb'];
        $this->tableName = $tableName;
    }

    public function select($columns, $where = [])
    {
        $this->createColumnsAndJoins($columns);
        foreach ($where as $column => $value) {
            $this->addWhereClause($column, $value);
        }
        $this->createSelectQuery();
        try {
            $stmt = $this->oDb->prepare($this->query);
            echo "<label>Erstelltes SQL-Statement: </label>" . $this->query;
            echo "<br><br>";
            $stmt->execute($this->bindings);
            $response = $this->createResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            $response = new bDBResponse();
            $response->setStatus("ERROR");
            $response->setMessage("Select Failed: " . $e->getMessage());
            $response->setResults(null);
        }
        return $response;
    }

    public function addWhereClause($where = "", $value = "", $compare = "=")
    {
        $where = explode('.', $where);
        if (count($where) > 1) {
            $this->whereClause .= " AND " . $where[0] . "." . $where[1] . " " . $compare . " :" . $where[1] . "";
            $this->bindings[":" . $where[1]] = $value;

        } else {
            $this->whereClause .= " AND " . $where[0] . " " . $compare . " :" . $where[0] . "";
            $this->bindings[":" . $where[0]] = $value;
        }
    }

    public function createSelectQuery($columns = "", $joins = "", $where = "")
    {
        if ($columns == "") {
            $columns = $this->columns;
        }
        if ($joins == "") {
            $joins = $this->joins;
        }
        if ($where == "") {
            $where = $this->whereClause;
        }
        $this->query = "SELECT " . $columns . " FROM " . $this->tableName . $joins . " WHERE 1=1 " . $where;
    }

    public function createColumnsAndJoins($columns)
    {
        $columns = explode(',', $columns);
        foreach ($columns as $id => $column) {
            if (($id + 1) != count($columns)) {
                $this->columns .= $column . ",";
            } else {
                $this->columns .= $column;
            }
            $this->addJoin($column);
        }
    }

    public function addJoin($column)
    {
        $joinColumn = explode('.', $column);
        if (count($joinColumn) == 2) {
            $this->columns = $column . "," . $this->columns;
            $this->joins .= " JOIN " . $joinColumn[0] . " ON " . $joinColumn[0] . "." . $joinColumn[0] . "Id = " . $this->tableName . "." . $this->tableName . "Id";
        }
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function delete($where = [])
    {
        foreach ($where as $column => $value) {
            $this->addWhereClause($column, $value);
        }
        $this->createDeleteQuery();
        $stmt = $this->oDb->prepare($this->query);
        echo "<label>Erstelltes SQL-Statement: </label>" . $this->query;
        echo "<br><br>";
        $stmt->execute($this->bindings);
    }

    public function createDeleteQuery($where = "")
    {
        if ($where == "") {
            $where = $this->whereClause;
        }
        $this->query = "DELETE * FROM " . $this->tableName . " WHERE " . $where;
    }

    public function createResponse($rows)
    {
        $response = new bDBResponse();
        if (count($rows) <= 0) {
            $response->setStatus("NO DATA");
        } elseif (count($rows) == 1) {
            $response->setStatus("SUCCESS");
            $response->setResults($rows);
        } else {
            $response->setStatus("SUCCESS");
            $response->setResults($rows);
        }
        return $response;
    }

    public function insertData($columnsAndValues)
    {
        $count = 0;
        foreach ($columnsAndValues as $column => $value) {
            if (!is_null($value)) {
                $this->columns .= $column;
                $this->columns .= ",";
                $this->updateValues .= ":" . $column;
                $this->updateValues .= ",";
                $count++;
                $this->bindings[":" . $column] = $value;
            }
        }
        $this->columns = substr($this->columns, 0, -1);
        $this->updateValues = substr($this->updateValues, 0, -1);
        $this->createInsertQuery();

        echo "<label>Erstelltes SQL-Statement: </label>" . $this->query;
        echo "<br><br>";

        try {
            $stmt = $this->oDb->prepare($this->query);
            $stmt->execute($this->bindings);
            $response = new bDBResponse();
            $response->setStatus("SUCCESS");
            $response->setInsertedId($this->oDb->lastInsertId());

        } catch (PDOException $e) {
            $response = new bDBResponse();
            $response->setStatus("ERROR");
            $response->setMessage("Insert Failed: " . $e->getMessage());
            $response->setResults(null);
        }
        return $response;
    }

    public function createInsertQuery($escapedValues = null)
    {
        $this->query = "INSERT INTO " . $this->tableName . " (" . $this->columns . ") VALUES (" . $this->updateValues . ")";
        if (!is_null($escapedValues)) {
            $this->query = "INSERT INTO " . $this->tableName . " VALUES (" . $escapedValues . ")";
        }
    }
}