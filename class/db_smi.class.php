<?php

class db_smi
{
    private static $_instance = null;
    
    private $smi;

    private $doli;

    private $url;
    private $port;
    private $name;
    private $id;
    private $pwd;
    
    //constructeur lis et ce connecte a la bdd
    function __construct($dbDoli)
    {
        $this->doli = $dbDoli;
        $this->read();
        $this->connect();
    }
    
    //fonction de connection a la dbb smi
    function connect()
    {
        try {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            // connexion a la bdd
            $this->smi = new PDO('mysql:host='. $this->url .';dbname='. $this->name.';port='. $this->port, $this->id, $this->pwd, $pdo_options); 
            
        }
        catch (Exception $e)
        {
            //throw new Exception($e->getMessage());
            //die('Erreur : ' . $e->getMessage());
        }
    }

    //fonction qui met a jour les variables de la classe
    function setVar($newUrl, $newPort, $newName, $newId, $newPwd)
    {
        $this->url = $newUrl;
        $this->port = $newPort;
        $this->name = $newName;
        $this->id = $newId;
        $this->pwd = $newPwd;
    }
    
    //fonction qui écris dans la table les identifiants de la bdd smi
    function write()
    {
        //$this->doli->query("TRUNCATE TABLE llx_dbsmi");
        $this->doli->query("INSERT INTO llx_dbsmi (dbsmi_url, dbsmi_name, dbsmi_port, dbsmi_user, dbsmi_pwd) VALUES ('".$this->url."', '".$this->name."', ".$this->port.", '".$this->id."', '".$this->pwd."')");
    }
    
    //fonction qui lis dans la table les identifiants
    function read()
    {
        $dbSmiResult = $this->doli->query("SELECT dbsmi_url, dbsmi_name, dbsmi_port, dbsmi_user, dbsmi_pwd FROM llx_dbsmi");
        if($dbSmi = $this->doli->fetch_object($dbSmiResult))
        {
            $this->setVar($dbSmi->dbsmi_url, $dbSmi->dbsmi_port, $dbSmi->dbsmi_name, $dbSmi->dbsmi_user, $dbSmi->dbsmi_pwd);
        }
    }
    
    // renvoie l'instance en cours ou la crée
    public static function getInstance($dbDoli)
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self($dbDoli);
        }
        return self::$_instance;
    }
    
    function getSmi()
    {
        return $this->smi;
    }

    function getUrl()
    {
        return $this->url;
    }

    function getPort()
    {
        return $this->port;
    }

    function getName()
    {
        return $this->name;
    }

    function getId()
    {
        return $this->id;
    }

    function getPwd()
    {
        return $this->pwd;
    }

}
?>