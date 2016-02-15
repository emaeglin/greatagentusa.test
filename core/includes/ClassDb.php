<?php

class DbModel
{
    private $DbHost     = false;
    private $DbUsername = false;
    private $DbPasswd   = false;
    private $DbDbname   = false;
    private $DbPort     = 3306;
    
    private $DbConnection = NULL;

    public $error       = "";
    
    private $query  = "";

    public function __construct($config) 
    {
        if (!isset($config) || !is_array($config)) {
            $this->error = "Wrong configuration\n";
            return false;
        }
        
        if (!isset($config['host']) || empty($config['host'])) {
            $this->error = "Empty host in configuration\n";
            return false;
        } else {
            $this->DbHost = $config['host'];
        }
        
        if (!isset($config['username']) || empty($config['username'])) {
            $this->error = "Empty username in configuration\n";
            return false;
        } else {
            $this->DbUsername = $config['username'];
        }
        
        if (!isset($config['passwd']) || empty($config['passwd'])) {
            $this->error = "Empty passwd in configuration\n";
            return false;
        } else {
            $this->DbPasswd = $config['passwd'];
        }
        
        if (!isset($config['dbname']) || empty($config['dbname'])) {
            $this->error = "Empty dbname in configuration\n";
            return false;
        } else {
            $this->DbDbname = $config['dbname'];
        }
        
        if (isset($config['port']) && !empty($config['port'])) {
            $this->DbPort  = $config['port'];
        }
        
        //connect
        $this->DbConnection = new mysqli($this->DbHost, $this->DbUsername, $this->DbPasswd, $this->DbDbname, $this->DbPort);
        
        if (mysqli_connect_errno()) {
            $this->error = sprintf("Connect failed: %s\n", mysqli_connect_error());
            return false;
        }
        
        return true;
    }
 
    public function PrepareQuery($query_pattern, $arguments = array())
    {
        if (!isset($query_pattern) || empty($query_pattern)) {
            $this->error = "Empty query pattern\n";
            return false;
        }
        
        $arguments = array_map(array($this->DbConnection, 'real_escape_string'), $arguments);
        
        $this->query = vsprintf($query_pattern, $arguments);
        
    }

    public function Query() 
    {
        if (!isset($this->query) || !is_string($this->query)) {
            $this->error = "No query given\n";
            return false;
        }
        
        if (is_null($this->DbConnection)) {
            $this->error = "No DB Connection\n";
            return false;
        }
        
        if ($result = $this->DbConnection->query($this->query)) {
            if (is_object($result)) {
                return $result->fetch_object();
            } else {
                return true;
            }
        } else {
            $this->error = sprintf("Error message: %s\n", $this->DbConnection->error);
            return false;
        }
    }
    
    public function LastInsertId()
    {
        if (is_null($this->DbConnection)) {
            $this->error = "No DB Connection\n";
            return false;
        }
        
        return $this->DbConnection->insert_id;
    }
}