<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       dev/skeletons/skeleton_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *					Put here some comments
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
if (! $res && file_exists("../../../main.inc.php")) $res=@include '../../../main.inc.php';
//if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
//if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
//if (! $res && file_exists("../../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
dol_include_once('/smisync/class/skeleton_class.class.php');

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$myparam	= GETPOST('myparam','alpha');

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($action == 'add')
{
	$object=new Skeleton_Class($db);
	$object->prop1=$_POST["field1"];
	$object->prop2=$_POST["field2"];
	$result=$object->create($user);
	if ($result > 0)
	{
		// Creation OK
	}
	{
		// Creation KO
		$mesg=$object->error;
	}
}





/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/
$page_name = 'Détail fiche';

llxHeader('', $page_name, '');





// Put here content of your page

// et on affiche

$linkback = '<a href="skeleton_page.php">Retour à la liste</a>';
print_fiche_titre($langs->trans($page_name), $linkback);


if(isset($_REQUEST['int_code']) && !empty($_REQUEST['int_code']))
{
    
    $int_code = htmlspecialchars(addslashes($_REQUEST['int_code']));
    
    try {
        //identifiants bdd dolibarr
        $idDoli = array(
        "URL"			    => "localhost", 
        "port"			    => "8888", 
        "nomBDD"		=> "dolibarr", 
        "identifiant"	=> "root", 
        "mdp"			    => "root" 
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
            "URL"               => $url, 
            "port"              => $port, 
            "nomBDD"      => $nom, 
            "identifiant"   => $id, 
            "mdp"             => $mdp 
        ); 
                

        $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
        // connections aux bdd
        $bddSmi = new PDO('mysql:host='. $idSmi['URL'] .';dbname='. $idSmi['nomBDD'].';port='. $idSmi['port'], $idSmi['identifiant'], $idSmi['mdp'], $pdo_options); 
        $bddDoli = new PDO('mysql:host='. $idDoli['URL'] .';dbname='. $idDoli['nomBDD'].';port='. $idDoli['port'], $idDoli['identifiant'], $idDoli['mdp'], $pdo_options); 


        //on charge les statuts
        // recupere toute les infos des statuts
        $infosStatutsbdd = $bddSmi->query("SELECT statut_code, statut_desc, statut_img FROM smi_statut");
        // on met ca dans un tableau
        $infosStatuts = array();
        while($infosStatutbdd = $infosStatutsbdd->fetch(PDO::FETCH_BOTH))
        {
            $infosStatuts[$infosStatutbdd['statut_code']]['label'] = $infosStatutbdd['statut_desc'];
            $infosStatuts[$infosStatutbdd['statut_code']]['icon'] = $infosStatutbdd['statut_img'];
        }
    
    
    
        //clauses wheres par tables
        $tWhere['smi_int'] = "WHERE int_code = '". $int_code ."'";
        //$tWhere['smi_pdt'] = "pdt_code = (SELECT pdti_codepdt FROM smi_pdti WHERE pdti_codeint = '". $int_code ."')";
        $tWhere['smi_pdt'] = "INNER JOIN smi_pdti WHERE pdt_code = pdti_codepdt AND pdti_codeint = '". $int_code ."'";
        $tWhere['smi_tec'] = "WHERE tec_code = (SELECT int_codetec FROM smi_int WHERE int_code = '". $int_code ."')";
        $tWhere['smi_cli'] = "WHERE cli_code = (SELECT int_codecli FROM smi_int WHERE int_code = '". $int_code ."')";
    
    
        $detailsCols = $bddDoli->query("SELECT cfgdetail_column, cfgdetail_label, cfgdetail_table, cfgdetail_display FROM llx_cfgdetail ORDER BY cfgdetail_table");
    
        //on construit nos requetes
        $querys = array();
        $labels= array();
        $i = 0;
        $lbl = 0;

        if($detailsCol = $detailsCols->fetch(PDO::FETCH_BOTH))
        {
            $prevTable = $detailsCol['cfgdetail_table'];
            $querys[$i] = $detailsCol['cfgdetail_column'] . ', ';
            $labels[$i][$lbl] = $detailsCol['cfgdetail_label'];
            $lbl++;
            while($detailsCol = $detailsCols->fetch(PDO::FETCH_BOTH))
            {
                if($prevTable == $detailsCol['cfgdetail_table'])
                {
                    $querys[$i] .= $detailsCol['cfgdetail_column'] . ', ';
                    $labels[$i][$lbl] = $detailsCol['cfgdetail_label'] ;
                    $lbl++;
                }
                else
                {
                    $querys[$i] = 'SELECT '. substr($querys[$i], 0, -2)  .' FROM '. $prevTable .' '. $tWhere[$prevTable];
                    $i++;
                    $lbl = 0;
                    $querys[$i] = $detailsCol['cfgdetail_column'] . ', ';
                    $labels[$i][$lbl] = $detailsCol['cfgdetail_label'];
                    $lbl++;
                    $prevTable = $detailsCol['cfgdetail_table'];
                }
                
            }
            $querys[$i] = 'SELECT '. substr($querys[$i], 0, -2)  .' FROM '. $prevTable .' '. $tWhere[$prevTable];

        }
        //print '<pre>';
        //print print_r($querys);
        //print print_r($labels);
        //print '</pre>';
    
        $tab = '<br /><table class=border>';
        
        $iLbl = 0;
        $iParite = 0;
        // on parcour les requetes
        foreach($querys as $query)
        {
            $infos = $bddSmi->query($query);
            //On parcour les resultats description requetes
            while($info = $infos->fetch(PDO::FETCH_BOTH))
            {
                $iCol = 0;
                //on ecris les colonnes
                foreach($labels[$iLbl] as $label)
                {
                    if($iParite%2)
                        $tab .= '<tr class="pair">';
                    else
                        $tab .= '<tr class="impair">';
                    $iParite++;
                    $tab .= '<td><b>'. $label .'</b></td>';
                    $tab .= '<td>'. $info[$iCol] .'</td>';
                    $tab .= '</tr>';
                    $iCol++;
                }
            }
            $iLbl++;
        }
        $tab .= '</table>';
        
        print $tab;
    
        /*
        N° de l'intervention
- Date de demande
- Fin prévu
- Catégorie
- Urgence
- Taches
- Code saisie manuelle
- Code donneur d'ordre
- Code ordre client
- Responsable dossier
- Contrat
- Statut
- Matériel
- Travail à effectuer
- Garantie
- Référence Facture
- Acompte
- Technicien
- Temps travaillé
- Travail effectué
- Client prévenu le
- Règlement
- Dossier clôt le
- Forfaits et pièces facturées
        
        
        */
        
        
        
    }
    catch (Exception $e)
    {
        die('Erreur : ' . $e->getMessage());
    }
}
else
{
    print 'pas de code intervention';
}











?>

<?php

// End of page
llxFooter();
$db->close();
?>