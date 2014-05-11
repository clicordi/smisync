<?php
/* <one line to give the program's name and a brief idea of what it does.>
* Copyright (C) <year> <name of author>
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
* \file admin/mymodule.php
* \ingroup mymodule
* \brief This file is an example module setup page
* Put some comments here
*/
// Dolibarr environment
$res = @include("../../main.inc.php"); // From htdocs directory
if (! $res) {
    $res = @include("../../../main.inc.php"); // From "custom" directory
}


// Libraries
require_once DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php";
//require_once '../lib/mymodule.lib.php';
//require_once "../class/myclass.class.php";
require_once "../class/db_smi.class.php";
// Translations
$langs->load("smisync@smisync");

// Access control
if (! $user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'alpha');

/*
* Actions
*/


try {
    //connection bdd smi
    $dbSmiInfo = db_smi::getInstance($db);
    $dbSmi = $dbSmiInfo->getSmi();


    // Synchroniser les tables de civilités
    // On recupere toute la table de smi (seulement les civilitée supprimable)
    //$civSmiAlls = $dbSmi->query("SELECT civ_code, civ_desc FROM ".$dbSmiInfo->getTpref()."_civ WHERE civ_delok = 1");
    $civSmiAlls = $dbSmi->query("SELECT civ_code, civ_desc FROM ".$dbSmiInfo->getTpref()."_civ");
    $civSmi = array();
    $iCivMod = 0;
    $iCivAdd = 0;
    $iCivOk = 0;
    while($civSmiAll = $civSmiAlls->fetch(PDO::FETCH_ASSOC))
    {
        $civSmi[$civSmiAll['civ_code']] = $civSmiAll['civ_desc'];
    }

    // On parcourt Toutes les civilitées dans dolibarr
    $sql = 'SELECT code, civilite FROM llx_c_civilite';
    $civsDoli = $db->query($sql);
    while($civDoli = $db->fetch_object($civsDoli))
    {
        // si pas presente dans smi
        if(!isset($civSmi[$civDoli->code]))
        {
            //on l'ajoute
            $sql = "INSERT INTO ".$dbSmiInfo->getTpref()."_civ (civ_code, civ_desc, civ_delok) VALUES ('".$civDoli->code."','".utf8_decode($civDoli->civilite)."', 1)";
            $dbSmi->query($sql);
            $iCivAdd++;
        }
        // si le libellé n'est pas egale
        else if($civSmi[$civDoli->code] != utf8_decode($civDoli->civilite))
        {
            // on l'update
            // $sql = "UPDATE ".$dbSmiInfo->getTpref()."_civ SET civ_desc = '".$civDoli->civilite."' WHERE civ_code = '".$civDoli->code."' AND civ_delok = 1";
            $sql = "UPDATE ".$dbSmiInfo->getTpref()."_civ SET civ_desc = '".utf8_decode($civDoli->civilite)."' WHERE civ_code = '".$civDoli->code."'";
            $dbSmi->query($sql);
            $iCivMod++;
        }
        else
        {
            $iCivOk++;
        }
    }



    //recupere tout les id dans la table des correspondances des id smi/doli
    $sql = 'SELECT idcli_doli, idcli_smi FROM llx_idcli';
    $idssync = $db->query($sql);
    // on met ca dans un tableau
    $idcli = array();
    while($idsync = $db->fetch_object($idssync))
    {
        $idcli[$idsync->idcli_doli] = $idsync->idcli_smi;
    }
    //echo '<pre>';
    //echo print_r($idcli);
    //echo '</pre>';
    
    
    // recherche du code client le plus grand
    $clicodelast = $dbSmi->query("SELECT cli_code FROM ".$dbSmiInfo->getTpref()."_cli ORDER BY cli_code DESC LIMIT 0, 1");
    $clicodlast = $clicodelast->fetch(PDO::FETCH_BOTH);
    $cli_code =  $clicodlast['cli_code'];
    //si pas de code clients presents
    if(!preg_match("/C12[0-9]{7}/", $cli_code))
        $cli_code = 'C120000000';
    
    //je recupere les infos des client de smi
    $usersSmi = $dbSmi->query("SELECT cli_id, cli_cat, cli_datecrea, cli_codecrea, cli_datemod, cli_prop, cli_codedo, cli_codemod, cli_code, cli_pass, cli_type, cli_ste, cli_rcs, cli_ape, cli_tvai, cli_civilite, cli_prenom, cli_nom, cli_adr1, cli_adr2, cli_dep, cli_ville, cli_codepays, cli_codeadev, cli_telf, cli_fax, cli_telp, cli_email, cli_mess, cli_notaa, cli_notat, cli_ccpta, cli_ccptasp, cli_cpta, cli_prev, cli_modfact FROM ".$dbSmiInfo->getTpref()."_cli");
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
    // $usersDoli = $db->query('SELECT soc.rowid, nom, soc.address, soc.zip, soc.town, soc.phone, soc.fax, soc.email, client, civilite, phone_mobile FROM llx_societe AS soc INNER JOIN llx_socpeople WHERE soc.rowid =  fk_soc');
    $usersDoli = $db->query('SELECT rowid, nom, address, zip, town, phone, fax, email, client FROM llx_societe');
    
    $cptModif = 0;
    $cptAjout = 0;
    $cptCliOk = 0;
    // on boucle pour chaque clients
    while($userDoli = $db->fetch_object($usersDoli))
    {
    
        // on tri les clients dans les tiers
    	if($userDoli->client != 0)
    	{
            //si le client a un contact (pour plus d'info)
            $contactUserDoli = $db->query('SELECT civilite, phone_mobile FROM llx_socpeople WHERE fk_soc = '.$userDoli->rowid);
            $contactUser = $db->fetch_object($contactUserDoli);
            
            
            //on verifie si notre client est present dans la table des correspondances
            // et on test si notre client est dans la bdd smi
            if(isset($idcli[$userDoli->rowid]) && isset($cliSmi[$idcli[$userDoli->rowid]]))
            {
                $idSmi = $idcli[$userDoli->rowid];
                //echo 'present id correspondance';

                //on test les valeurs de notre client smi avec celles de doli
                $err = 0;

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
                if($cliSmi[$idSmi]['cli_codemod'] != 'Administrateur')
                {
                    $cliSmi[$idSmi]['cli_codemod'] = 'Administrateur';
                    $err = 1;
                }
                if(!preg_match("/C12[0-9]{7}/", $cliSmi[$idSmi]['cli_code']))
                {
                    $cli_code++;
                    $cliSmi[$idSmi]['cli_code'] = $cli_code;
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_type'] != '2')
                {
                    $cliSmi[$idSmi]['cli_type'] = '2';
                    $err = 1;
                }
                if(isset($contactUser->civilite) && $cliSmi[$idSmi]['cli_civilite'] != $contactUser->civilite)
                {
                    $cliSmi[$idSmi]['cli_civilite'] = $contactUser->civilite;
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_prenom'] != utf8_decode($userDoli->nom))
                {
                    $cliSmi[$idSmi]['cli_prenom'] = utf8_decode($userDoli->nom);
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_nom'] != ' ')
                {
                    $cliSmi[$idSmi]['cli_nom'] = ' ';
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_adr1'] != utf8_decode(substr($userDoli->address, 0, 50)))
                {
                    $cliSmi[$idSmi]['cli_adr1'] = utf8_decode(substr($userDoli->address, 0, 50));
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_adr2'] != utf8_decode($userDoli->zip).' '.utf8_decode($userDoli->town))
                {
                    $cliSmi[$idSmi]['cli_adr2'] = utf8_decode($userDoli->zip).' '.utf8_decode($userDoli->town);
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_dep'] != substr($userDoli->zip, 0, 2))
                {
                    $cliSmi[$idSmi]['cli_dep'] = substr($userDoli->zip, 0, 2);
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
                if($cliSmi[$idSmi]['cli_telf'] != $userDoli->phone)
                {
                    $cliSmi[$idSmi]['cli_telf'] = $userDoli->phone;
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_fax'] != $userDoli->fax)
                {
                    $cliSmi[$idSmi]['cli_fax'] = $userDoli->fax;
                    $err = 1;
                }
                if(isset($contactUser->phone_mobile) && $cliSmi[$idSmi]['cli_telp'] != $contactUser->phone_mobile)
                {
                    $cliSmi[$idSmi]['cli_telp'] = $contactUser->phone_mobile;
                    $err = 1;
                }
                if($cliSmi[$idSmi]['cli_email'] != $userDoli->email)
                {
                    $cliSmi[$idSmi]['cli_email'] = $userDoli->email;
                    $err = 1;
                }
                
                
                if($err != 0)
                {// une erreur dans les données
                    $myquery = "UPDATE ".$dbSmiInfo->getTpref()."_cli SET";
                    foreach($cliSmi[$idSmi] as $key => $val) 
                    {
                        $myquery .= ' '. $key .'=\''. addslashes($val) .'\',';
                    }
                    $myquery = substr($myquery, 0, -1);
                    $myquery .= ' WHERE cli_id = '. $idSmi;
                    //echo '<br>'.$myquery.'<br>Client update';
                    $dbSmi->query($myquery);
                    $cptModif++;
                }
                else
                {//pas d'erreur dans les données
                    //echo 'Client sans Probleme';
                    $cptCliOk++;
                }

            }
            else
            {
                
                //echo 'client pas present dans smi';
                
                $cli_cat = 'PAR'; // PAR pour 'particulier'
                $cli_datecrea = date('Y-m-d'); // date du jour
                $cli_codecrea = 'Administrateur';
                $cli_datemod = date('Y-m-d'); // date du jour
                $cli_prop = 'A06530'; // code du 'magasin' (possibilité de le recuperer quelque part dans la bdd)
                $cli_codemod = 'Administrateur';
                $cli_code++; // on incremente le code client
                $cli_pass = '';
                $cli_type = '2'; // 2 pour 'Deja client' - 1 pour demande d'intervention - 0 pour prospect
                $cli_ste = '';
                $cli_rcs = '';
                $cli_ape = '';
                $cli_tvai = '';
                $cli_civilite = $contactUser->civilite;
                $cli_prenom = addslashes(utf8_decode($userDoli->nom)); // mis dans le champ prenom pour raison de formatage de text dans le champ nom
                $cli_nom = ' ';
                $cli_adr1 = addslashes(utf8_decode(substr($userDoli->address, 0, 50)));
                $cli_adr2 = addslashes(utf8_decode($userDoli->zip)).' '.addslashes(utf8_decode($userDoli->town)); // code pour la ville (2 ligne en dessous) un peu special je le met donc dans ce champ
                $cli_dep = addslashes(substr($userDoli->zip, 0, 2)); // recupere le num de departement dans le code postale
                // a voir
                $cli_ville = '0';
                $cli_codepays = 'FR';
                $cli_codeadev = 'EUR';
                $cli_telf = addslashes($userDoli->phone);
                $cli_fax = addslashes($userDoli->fax);
                $cli_telp = addslashes($contactUser->phone_mobile);
                $cli_email = addslashes($userDoli->email);
                $cli_mess = '';
                $cli_notaa = '';
                $cli_notat = '';
                $cli_ccpta = '';
                $cli_ccptasp = '';
                $cli_cpta = '0';
                $cli_prev = '4'; // 4 pour Ne pas prevenir par mail et autres moyens de comunication
                $cli_modfact = '0';
                
                // j'insert le client dolibarr dans la bdd de smi
                $myquery = "INSERT INTO ".$dbSmiInfo->getTpref()."_cli (cli_cat, cli_datecrea, cli_codecrea, cli_datemod, cli_prop, cli_codemod, cli_code, cli_pass, cli_type, cli_ste, cli_rcs, cli_ape, cli_tvai, cli_civilite, cli_prenom, cli_nom, cli_adr1, cli_adr2, cli_dep, cli_ville, cli_codepays, cli_codeadev, cli_telf, cli_fax, cli_telp, cli_email, cli_mess, cli_notaa, cli_notat, cli_ccpta, cli_ccptasp, cli_cpta, cli_prev, cli_modfact) VALUES ('$cli_cat', '$cli_datecrea', '$cli_codecrea', '$cli_datemod', '$cli_prop', '$cli_codemod', '$cli_code', '$cli_pass', '$cli_type', '$cli_ste', '$cli_rcs', '$cli_ape', '$cli_tvai', '$cli_civilite', '$cli_prenom', '$cli_nom', '$cli_adr1', '$cli_adr2', '$cli_dep', '$cli_ville', '$cli_codepays', '$cli_codeadev', '$cli_telf', '$cli_fax', '$cli_telp', '$cli_email', '$cli_mess', '$cli_notaa', '$cli_notat', '$cli_ccpta', '$cli_ccptasp', '$cli_cpta', '$cli_prev', '$cli_modfact')";
                $dbSmi->query($myquery);
                $cptAjout++;

                $lastSmiId1 = $dbSmi->query("SELECT LAST_INSERT_ID() FROM ".$dbSmiInfo->getTpref()."_cli");
                $lastSmiId0 = $lastSmiId1->fetch(PDO::FETCH_BOTH);
                $lastSmiId =  $lastSmiId0[0];

                if(!isset($idcli[$userDoli->rowid]))
                {
                    //insert des id dans la table des correspondances
                    $myquery = "INSERT INTO llx_idcli (idcli_doli, idcli_smi) VALUES (".$userDoli->rowid.", $lastSmiId)";
                    $db->query($myquery);
                }
                else
                {
                    //met a jour l'id smi dans la table des correspondances
                    $myquery = "UPDATE llx_idcli SET idcli_smi = $lastSmiId WHERE idcli_doli =  ".$userDoli->rowid." ";
                    $db->query($myquery);
                }

            }
        
    	}
    
    }

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}


/*
* View
*/
$page_name = "SmiSync_repair";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/smisync/admin/SmiSync_setuppage.php">Retour à la page de configuration de SmiSync</a>';
print_fiche_titre($langs->trans($page_name), $linkback);



?>
<div class="titre">Nombre de civilitée(s) modifé(s) : <?php echo $iCivMod; ?></div>
<div class="titre">Nombre de civilitée(s) ajouté(s) : <?php echo $iCivAdd; ?></div>
<div class="titre">Nombre de civilitée(s) sans problème(s) : <?php echo $iCivOk; ?></div>
<br />
<div class="titre">Nombre de client(s) modifé(s) : <?php echo $cptModif; ?></div>
<div class="titre">Nombre de client(s) ajouté(s) : <?php echo $cptAjout; ?></div>
<div class="titre">Nombre de client(s) sans problème(s) : <?php echo $cptCliOk; ?></div>

<?php

llxFooter();

$db->close();