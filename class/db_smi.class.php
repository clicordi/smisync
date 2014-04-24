<?php

class db_smi
{
    private static $_instance = null;
    
    private $smi;

    private $doli;

    private $url; //url de la BDD SMI
    private $port; //port d'écoute de la BDD SMI
    private $name; //nom de la BDD SMI
    private $id; //identifiant de connexion à la BDD SMI
    private $pwd; //mot de passe de l'identifiant
    private $tpref; //prefixe des tables de la BDD SMI
    
    //constructeur lis et ce connecte à la bdd
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
            $port = '';
            if($this->port != 0)
                $port = 'port='.$this->port;

            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            // connexion a la bdd
            $this->smi = new PDO('mysql:host='. $this->url .';dbname='. $this->name.';'. $port, $this->id, $this->pwd, $pdo_options); 
            
        }
        catch (Exception $e)
        {
            //throw new Exception($e->getMessage());
            //die('Erreur : ' . $e->getMessage());
        }
    }

    //fonction qui met a jour les variables de la classe
    function setVar($newUrl, $newPort, $newName, $newId, $newPwd, $newTPref)
    {
        $this->url = $newUrl;
        if(empty($newPort))
            $this->port = 0;
        else
            $this->port = $newPort;
        $this->name = $newName;
        $this->id = $newId;
        $this->pwd = $newPwd;
        $this->tpref = $newTPref;
    }
    
    //fonction qui écris dans la table les identifiants de la bdd smi
    function write()
    {
        // vide la table
        $this->doli->query("DELETE FROM llx_dbsmi");
        //on ajoute une ligne pour les identifiants
        $this->doli->query("INSERT INTO llx_dbsmi (dbsmi_url, dbsmi_name, dbsmi_port, dbsmi_user, dbsmi_pwd, dbsmi_tpref) VALUES ('".$this->url."', '".$this->name."', ".$this->port.", '".$this->id."', MD5('".$this->pwd."'), '".$this->tpref"')");
    }
    
    //fonction qui lis dans la table les identifiants
    function read()
    {
        $dbSmiResult = $this->doli->query("SELECT dbsmi_url, dbsmi_name, dbsmi_port, dbsmi_user, dbsmi_pwd, dbsmi_tpref FROM llx_dbsmi");
        if($dbSmi = $this->doli->fetch_object($dbSmiResult))
        {
            $this->setVar($dbSmi->dbsmi_url, $dbSmi->dbsmi_port, $dbSmi->dbsmi_name, $dbSmi->dbsmi_user, $dbSmi->dbsmi_pwd, $dbSmi->dbsmi_tpref);
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
        if ($this->port == 0) {
            return '';
        }
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
    function getTpref()
    {
        return $this->tpref;
    }

}
?>
