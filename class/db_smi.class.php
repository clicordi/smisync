<?php

class db_smi
{
    private static $_instance = null;
    
    private $smi;

    private $url;
    private $port;
    private $name;
    private $id;
    private $pwd;
    
    //constructeur lis et ce connecte a la bdd
    function __construct()
    {
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
    
    //fonction qui écris dans le fichier config les identifiants de la bdd smi
    function write()
    {
        // on ouvre le fichier
        $file = fopen(DOL_DOCUMENT_ROOT.'/smisync/admin/bddsmi.ini', 'r+');
        if($file == '')
        {
            $file = fopen('../../smisync/admin/bddsmi.ini', 'r+');
            if($file == '')
            {
                $file = fopen('../../../smisync/admin/bddsmi.ini', 'r+');
                if($file == '')
                {
                    $file = fopen('../../../../smisync/admin/bddsmi.ini', 'r+');
                }
            }
        }
        //echo $file;
        // remet le curseur en debut de fichier
        fseek($file, 0);
        
        // on réécris dans le fichier
        fputs($file, $this->url);
        fputs($file, "\n");
        fputs($file, $this->port);
        fputs($file, "\n");
        fputs($file, $this->name);
        fputs($file, "\n");
        fputs($file, $this->id);
        fputs($file, "\n");
        fputs($file, $this->pwd);
        fputs($file, "\n");
        
        fclose($file);
    }
    
    //fonction qui lis dans le fichier config les identifiants
    function read()
    {
        // on ouvre le fichier
        $file = fopen(DOL_DOCUMENT_ROOT.'/smisync/admin/bddsmi.ini', 'r');
        if($file == '')
        {
            $file = fopen('../../smisync/admin/bddsmi.ini', 'r');
            if($file == '')
            {
                $file = fopen('../../../smisync/admin/bddsmi.ini', 'r');
                if($file == '')
                {
                    $file = fopen('../../../../smisync/admin/bddsmi.ini', 'r');
                }
            }
        }
        //echo $file;

        // remet le curseur en debut de fichier
        fseek($file, 0);

        // lecture des variables et retire le retour a la ligne a la fin
        $this->url = substr(fgets($file), 0, -1);
        $this->port = substr(fgets($file), 0, -1);
        $this->name = substr(fgets($file), 0, -1);
        $this->id = substr(fgets($file), 0, -1);
        $this->pwd = substr(fgets($file), 0, -1);
        
        fclose($file);
    }
    
    // renvoie l'instance en cours ou la crée
    public static function getInstance()
    {
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
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