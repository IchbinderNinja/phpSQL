<?php namespace Creios\MVCFW\MVC\Model;

use Creios\MVCFW\MVC\Model;

class bDBResponse extends Model
{
    /** @var string */
    public $status = "";
    /** @var string */
    public $message = "";
    /** @var array */
    public $results = [];
    /** @var int */
    public $countResults = 0;
    /** @var int */
    public $insertedId = 0;

    public function _construct($dataSet = null)
    {
        if (isset ($dataSet)) {
            $this->setAll($dataSet);
        }
    }

    public function setAll($dataSet)
    {
        if (isset ($dataSet['status'])) {
            $this->status = $dataSet['status'];
        }
        if (isset ($dataSet['message'])) {
            $this->message = $dataSet['message'];
        }
        if (isset ($dataSet['results'])) {
            $this->results = $dataSet['results'];
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        if (count($this->results) == 1) {
            return $this->results[0];
        }
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults($results)
    {
        $this->results = $results;
        $this->countResults = count($results);
    }

    /**
     * @return int
     */
    public function getCountResults()
    {
        return $this->countResults;
    }

    /**
     * @param int $countResults
     */
    public function setCountResults($countResults)
    {
        $this->countResults = $countResults;
    }

    /**
     * @return int
     */
    public function getInsertedId()
    {
        return $this->insertedId;
    }

    /**
     * @param int $insertedId
     */
    public function setInsertedId($insertedId)
    {
        $this->insertedId = $insertedId;
    }
}