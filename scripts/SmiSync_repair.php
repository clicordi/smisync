<?php

// Faire plein de putins de commentaires !!!!!
try {
    //identifiants pour la bdd de dolibarr
    $idDoli = array(
        "URL"			=> "localhost", 
        "port"			=> "8888", 
        "nomBDD"		=> "dolibarr", 
        "identifiant"	=> "root", 
        "mdp"			=> "root" 
    );
    
    // on ouvre le fichier
    $file = fopen('/var/www/doli/htdocs/smisync/admin/bddsmi.ini', 'r+');

    // remet le curseur en debut de fichier
    fseek($file, 0);

    // lecture des variables
    $url = fgets($file);
    $port = fgets($file);
    $nom = fgets($file);
    $id = fgets($file);
    $mdp = fgets($file);
    
    //retire le retour a la ligne a la fin
    $url = substr($url, 0, -1);
    $port = substr($port, 0, -1);
    $nom = substr($nom, 0, -1);
    $id = substr($id, 0, -1);
    $mdp =substr($mdp, 0, -1);

    fclose($file);
    
    //identifiants pour la bdd de smi
    $idSmi = array(
        "URL"			=> $url, 
        "port"			=> $port, 
        "nomBDD"		=> $nom, 
        "identifiant"	=> $id, 
        "mdp"			=> $mdp 
    );

    $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    // connections aux bdd
    $bddDoli = new PDO('mysql:host='. $idDoli['URL'] .';dbname='. $idDoli['nomBDD'].';port='. $idDoli['port'], $idDoli['identifiant'], $idDoli['mdp'], $pdo_options); 
    $bddSmi = new PDO('mysql:host='. $idSmi['URL'] .';dbname='. $idSmi['nomBDD'].';port='. $idSmi['port'], $idSmi['identifiant'], $idSmi['mdp'], $pdo_options); 
    
    
    //recupere tout les id dans la table des correspondances des id smi/doli
    $idssync = $bddDoli->query('SELECT idcli_doli, idcli_smi FROM llx_idcli');
    // on met ca dans un tableau
    $idcli = array();
    while($idsync = $idssync->fetch(PDO::FETCH_BOTH))
    {
        $idcli[$idsync['idcli_doli']] = $idsync['idcli_smi'];
    }
    
    //echo '<pre>';
    //echo print_r($idcli);
    //echo '</pre>';
    
    
    // recherche du code client le plus grand
    $clicodelast = $bddSmi->query('SELECT cli_code FROM smi_cli ORDER BY cli_code DESC LIMIT 0, 1');
    $clicodlast = $clicodelast->fetch(PDO::FETCH_BOTH);
    $last_cli_code =  $clicodlast['cli_code'];
    
    //je recupere les infos des client de smi
    //$users = $bddDoli->query('SELECT nom, address, zip, town, phone, fax, email, client FROM llx_societe');
    $usersSmi = $bddSmi->query('SELECT cli_id, cli_cat, cli_datecrea, cli_codecrea, cli_datemod, cli_prop, cli_codedo, cli_codemod, cli_code, cli_pass, cli_type, cli_ste, cli_rcs, cli_ape, cli_tvai, cli_civilite, cli_prenom, cli_nom, cli_adr1, cli_adr2, cli_dep, cli_ville, cli_codepays, cli_codeadev, cli_telf, cli_fax, cli_telp, cli_email, cli_mess, cli_notaa, cli_notat, cli_ccpta, cli_ccptasp, cli_cpta, cli_prev, cli_modfact FROM smi_cli');
    $cliSmi = array();
    $i = 0;
    while($userSmi = $usersSmi->fetch(PDO::FETCH_ASSOC))
    {
        foreach($userSmi as $key => $val)
        {
            $cliSmi[$userSmi['cli_id']][$key] = $val;
        }
        
    }
    //echo '<pre>';
    //echo print_r($cliSmi);
    //echo '</pre>';
    
    //je recupere les infos des tiers de dolibarr
    //$users = $bddDoli->query('SELECT nom, address, zip, town, phone, fax, email, client FROM llx_societe');
    $usersDoli = $bddDoli->query('SELECT soc.rowid, nom, soc.address, soc.zip, soc.town, soc.phone, soc.fax, soc.email, client, civilite, phone_mobile FROM llx_societe AS soc INNER JOIN llx_socpeople WHERE soc.rowid =  fk_soc');
    
    $cptModif = 0;
    $cptAjout = 0;
    // on boucle pour chaque clients
    while($userDoli = $usersDoli->fetch(PDO::FETCH_BOTH)) 
    {
      
        // on tri les clients dans les tiers
    	if($userDoli['client'] != 0)
    	{
            //on verifie si notre client est present dans la table des correspondances
            if(isset($idcli[$userDoli['rowid']]))
            {
                $idSmi = $idcli[$userDoli['rowid']];
                echo 'present id correspondance';
                $err = 0;
                
                 if(isset($cliSmi[$idSmi]))
                {
                    echo 'ligne bdd smi';
                    if($cliSmi[$idSmi]['cli_cat'] != 'PAR')
                    {
                        $cliSmi[$idSmi]['cli_cat'] = 'PAR';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_codecrea'] != 'Administrateur')
                    {
                        $cliSmi[$idSmi]['cli_codecrea'] = 'Administrateur';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_prop'] != 'A06530')
                    {
                        $cliSmi[$idSmi]['cli_prop'] = 'A06530';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_codemod'] != 'Administrateur')
                    {
                        $cliSmi[$idSmi]['cli_codemod'] = 'Administrateur';
                        $err = 1;
                    }
                    /*if($cliSmi[$idSmi]['cli_code'] != 'C12')
                    {
                        $cliSmi[$idSmi]['cli_code'] = 'C12';
                        $err = 1;
                    }*/
                    if($cliSmi[$idSmi]['cli_pass'] != '')
                    {
                        $cliSmi[$idSmi]['cli_pass'] = '';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_type'] != '2')
                    {
                        $cliSmi[$idSmi]['cli_type'] = '2';
                        $err = 1;
                    }
                
                    if($userDoli['civilite'] == 'MME' && $cliSmi[$idSmi]['cli_civilite'] != 'MME')
                    {
                        echo 'civ0 '.$cliSmi[$idSmi]['cli_civilite'] .' / '.$userDoli['civilite'];
                        $cliSmi[$idSmi]['cli_civilite'] = 'MME';
                        $err = 1;
                        
                    }
                    else if($userDoli['civilite'] == 'MLE' && $cliSmi[$idSmi]['cli_civilite'] != 'MELLE') 
                    {
                        echo 'civ1 '.$cliSmi[$idSmi]['cli_civilite'] .' / '.$userDoli['civilite'];
                        $cliSmi[$idSmi]['cli_civilite'] = 'MELLE';
                        $cli_civilite = 'MELLE';  // modifier cette valeur en MME si l'on ne veux pas insulter les madames
                        $err = 1;
                        
                    }
                    else if($cliSmi[$idSmi]['cli_civilite'] != 'M.')
                    {
                        echo 'civ2 '.$cliSmi[$idSmi]['cli_civilite'] .' / '.$userDoli['civilite'];
                        $cliSmi[$idSmi]['cli_civilite'] = 'M.';
                        $err = 1;
                        
                    }

                    if($cliSmi[$idSmi]['cli_prenom'] != $userDoli['nom'])
                    {
                        echo 'nom '.$cliSmi[$idSmi]['cli_prenom'] .' / '.$userDoli['nom'];
                        $cliSmi[$idSmi]['cli_prenom'] = $userDoli['nom'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_nom'] != ' ')
                    {
                        $cliSmi[$idSmi]['cli_nom'] = ' ';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_adr1'] != $userDoli['address'])
                    {
                        $cliSmi[$idSmi]['cli_adr1'] = $userDoli['address'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_adr2'] != $userDoli['zip'].' '.$userDoli['town'])
                    {
                        $cliSmi[$idSmi]['cli_adr2'] = $userDoli['zip'].' '.$userDoli['town'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_dep'] != substr($userDoli['zip'], 0, 2))
                    {
                        $cliSmi[$idSmi]['cli_dep'] = substr($userDoli['zip'], 0, 2);
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_ville'] != '0')
                    {
                        $cliSmi[$idSmi]['cli_ville'] = '0';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_codepays'] != 'FR')
                    {
                        $cliSmi[$idSmi]['cli_codepays'] = 'FR';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_codeadev'] != 'EUR')
                    {
                        $cliSmi[$idSmi]['cli_codeadev'] = 'EUR';
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_telf'] != $userDoli['phone'])
                    {
                        $cliSmi[$idSmi]['cli_telf'] = $userDoli['phone'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_fax'] != $userDoli['fax'])
                    {
                        $cliSmi[$idSmi]['cli_fax'] = $userDoli['fax'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_telp'] != $userDoli['phone_mobile'])
                    {
                        $cliSmi[$idSmi]['cli_telp'] = $userDoli['phone_mobile'];
                        $err = 1;
                    }
                    if($cliSmi[$idSmi]['cli_email'] != $userDoli['email'])
                    {
                        $cliSmi[$idSmi]['cli_email'] = $userDoli['email'];
                        $err = 1;
                    }
                    
                    
                    if($err == 1)
                    {
                        $myquery = 'UPDATE smi_cli SET';
                        foreach($cliSmi[$idSmi] as $key => $val)
                        {
                            $myquery .= ' '. $key .'=\''. $val .'\',';
                        }
                        $myquery = substr($myquery, 0, -1);
                        $myquery .= ' WHERE cli_id = '. $idSmi;
                        //$myquery = "UPDATE smi_cli SET cli_datemod='$cli_datemod', cli_civilite='$cli_civilite', cli_prenom='$cli_prenom', cli_adr1='$cli_adr1', cli_adr2='$cli_adr2', cli_dep='$cli_dep', cli_telf='$cli_telf', cli_fax='$cli_fax', cli_telp='$cli_telp', cli_email='$cli_email' WHERE cli_id = $cliSmiId";
                        //$myquery = "UPDATE smi_cli SET cli_cat='$cli_datemod', cli_civilite='$cli_civilite', cli_prenom='$cli_prenom', cli_adr1='$cli_adr1', cli_adr2='$cli_adr2', cli_dep='$cli_dep', cli_telf='$cli_telf', cli_fax='$cli_fax', cli_telp='$cli_telp', cli_email='$cli_email' WHERE cli_id = $cliSmiId";
                        echo '<br>'.$myquery.'<br>';
                    }
                    

                }
                else
                {
                     echo 'pas ligne bdd smi';
                     //Client present dans la table des correspondance mais pas dans smi
                     //donc faire un INSERT
                }
                /*
                //$cli_datecrea = date('Y-m-d'); // date du jour
                //$cli_datemod = date('Y-m-d'); // date du jour
                $cli_ste = '';
                $cli_rcs = '';
                $cli_ape = '';
                $cli_tvai = '';

                $cli_mess = '';
                $cli_notaa = '';
                $cli_notat = '';
                $cli_ccpta = '';
                $cli_ccptasp = '';
                $cli_cpta = '0';
                $cli_prev = '4'; // 4 pour Ne pas prevenir par mail et autres moyens de comunication
                $cli_modfact = '0';
                
                // j'insert le client dolibarr dans la bdd de smi
                $myquery = "INSERT INTO smi_cli (cli_cat, cli_datecrea, cli_codecrea, cli_datemod, cli_prop, cli_codemod, cli_code, cli_pass, cli_type, cli_ste, cli_rcs, cli_ape, cli_tvai, cli_civilite, cli_prenom, cli_nom, cli_adr1, cli_adr2, cli_dep, cli_ville, cli_codepays, cli_codeadev, cli_telf, cli_fax, cli_telp, cli_email, cli_mess, cli_notaa, cli_notat, cli_ccpta, cli_ccptasp, cli_cpta, cli_prev, cli_modfact) VALUES ('$cli_cat', '$cli_datecrea', '$cli_codecrea', '$cli_datemod', '$cli_prop', '$cli_codemod', '$cli_code', '$cli_pass', '$cli_type', '$cli_ste', '$cli_rcs', '$cli_ape', '$cli_tvai', '$cli_civilite', '$cli_prenom', '$cli_nom', '$cli_adr1', '$cli_adr2', '$cli_dep', '$cli_ville', '$cli_codepays', '$cli_codeadev', '$cli_telf', '$cli_fax', '$cli_telp', '$cli_email', '$cli_mess', '$cli_notaa', '$cli_notat', '$cli_ccpta', '$cli_ccptasp', '$cli_cpta', '$cli_prev', '$cli_modfact')";
                echo $myquery.'<br>';
                $bddSmi->query($myquery);
                */
                $cptModif = 0;
                //UPDATE
            }
            else
            {
                 echo 'pas present id correspondance';
               //INSERT dans la table des correspondances et dans la table client smi
                
            }
            
        
    	}
    
    }
    

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}


?>
<p>Je fais rien :D</p>
<a href="../admin/SmiSync_setuppage.php">Retour</a>


