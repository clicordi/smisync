<?php

class db_smi
{
    public $smi;
    
    protected $url;
    protected $port;
    protected $nom;
    protected $id;
    protected $pwd;
    
    //constructeur lis et ce connecte a la bdd
    function __construct()
    {
        $this->read();
        $this->connect();
    }
    
    //fonction de connection a la dbb smi
    function connect()
    {
        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        // connexion a la bdd
        $this->smi = new PDO('mysql:host='. $this->url .';dbname='. $this->nom.';port='. $this->port, $this->id, $this->mdp, $pdo_options); 
    }

    //fonction qui met a jour les variables de la classe
    function setBd($newUrl, $newPort, $newNom, $newId, $newPwd)
    {
        $this->url = $newUrl;
        $this->port = $newPort;
        $this->nom = $newNom;
        $this->id = $newId;
        $this->mdp = $newPwd;
    }
    
    //fonction qui écris dans le fichier config les identifiants de la bdd smi
    function write()
    {
        // on ouvre le fichier
        if(! $file = fopen('../smisync/admin/bddsmi.ini', 'r+'))
            if(! $file = fopen('../../smisync/admin/bddsmi.ini', 'r+'))
                if(! $file = fopen('../../smisync/admin/bddsmi.ini', 'r+'))
                    $file = fopen('../../../../smisync/admin/bddsmi.ini', 'r+');

        // remet le curseur en debut de fichier
        fseek($file, 0);
        
        // on réécris dans le fichier
        fputs($file, $this->url);
        fputs($file, "\n");
        fputs($file, $this->port);
        fputs($file, "\n");
        fputs($file, $this->nom);
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
        //if(! $file = fopen('../smisync/admin/bddsmi.ini', 'r'))
            if(! $file = fopen('../../smisync/admin/bddsmi.ini', 'r'))
                if(! $file = fopen('../../smisync/admin/bddsmi.ini', 'r'))
                    $file = fopen('../../../../smisync/admin/bddsmi.ini', 'r');

        // remet le curseur en debut de fichier
        fseek($file, 0);

        // lecture des variables et retire le retour a la ligne a la fin
        $this->url = substr(fgets($file), 0, -1);
        $this->port = substr(fgets($file), 0, -1);
        $this->nom = substr(fgets($file), 0, -1);
        $this->id = substr(fgets($file), 0, -1);
        $this->mdp = substr(fgets($file), 0, -1);
        
        fclose($file);
    }
    

    
    
    
}
?>