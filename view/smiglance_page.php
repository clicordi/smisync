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
//dol_include_once('/smisync/class/skeleton_class.class.php');
require_once "../class/db_smi.class.php";

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
$page_name = 'Suivi d\'intervention';

llxHeader('', $page_name, '');


// Put here content of your page


try {
    //connection bdd smi
    $bdd = new db_smi();


    //on charge les statuts
    // recupere toute les infos des statuts
    $infosStatutsbdd = $bdd->smi->query("SELECT statut_code, statut_desc, statut_img FROM smi_statut");
    // on met ca dans un tableau
    $infosStatuts = array();
    while($infosStatutbdd = $infosStatutsbdd->fetch(PDO::FETCH_BOTH))
    {
        $infosStatuts[$infosStatutbdd['statut_code']]['label'] = $infosStatutbdd['statut_desc'];
        $infosStatuts[$infosStatutbdd['statut_code']]['icon'] = $infosStatutbdd['statut_img'];
    }

    if($user->admin)
    {
        //on recupere les infos des interventions
        $userInfos = $bdd->smi->query("SELECT int_code, int_codecli, int_codestatut, int_datefinp, int_mat, int_pbm, cli_prenom, cli_nom FROM smi_int INNER JOIN smi_cli WHERE int_codecli = cli_code ORDER BY int_datedde");
    }
    else
    {
        // probleme d'accents.............. T.T ........
        // recupere l'idsmi qui correspond au user (dans dolibarr)
        $cliId = $db->query("SELECT idcli_smi FROM llx_idcli WHERE idcli_doli = (SELECT fk_societe FROM llx_user WHERE rowid = ".$user->id.")");
        $cliSmi = $db->fetch_object($cliId);
        $cliSmiId =  $cliSmi->idcli_smi;

        //on recupere les infos grace a cet idsmi (dans smi)
        $userInfos = $bdd->smi->query("SELECT int_code, int_codestatut, int_datefinp, int_mat, int_pbm FROM smi_int WHERE int_codecli = (SELECT cli_code FROM smi_cli WHERE cli_id = $cliSmiId) ORDER BY int_datedde");
    }

    // variables a remplir pour l'affichage plus bas
    $tabNonClot = '';
    $tabClot = '';
    $iNonClot = 0;
    $iClot = 0;
    // on remplit
    while($userInfo = $userInfos->fetch(PDO::FETCH_BOTH))
    {
        if($userInfo['int_codestatut'] == "CLOT")
        {//les interventions terminees
            if($iClot%2)
                $tabClot .= '<tr class="pair">';
            else
                $tabClot .= '<tr class="impair">';
            
            $tabClot .= '<td width="22px"><img src="'.str_replace('..', '/smi', $infosStatuts['CLOT']['icon']).'" border="0" title="" alt="cloturé" /></td>';
            $tabClot .= '<td>'.$userInfo['int_code'].'</td>';
            $tabClot .= '<td>'.$userInfo['int_datefinp'].'</td>';
            $tabClot .= '<td>'.$userInfo['int_mat'].'</td>';
            $tabClot .= '<td>'.$userInfo['int_pbm'].'</td>';
            if($user->admin)
            {
                $tabClot .= '<td>'.$userInfo['int_codecli'].'</td>';
                $tabClot .= '<td>'.$userInfo['cli_prenom'].' '.$userInfo['cli_nom'].'</td>';
            }
            $tabClot .= '<td>
                                    <form method="post" action="smiglance_detail_page.php">
                                        <input name="int_code" type="hidden" value="'.$userInfo['int_code'].'" />
                                        <input class="button" type="submit" value="Détail" />
                                    </form>
                                </td>';
            $tabClot .= '</tr>';

            $iClot++;
        }
        else
        {//les non terminees
            if($iNonClot%2)
                $tabNonClot .= '<tr class="pair">';
            else
                $tabNonClot .= '<tr class="impair">';
            
            $tabNonClot .= '<td width="22px"><img src="'.str_replace('..', '/smi', $infosStatuts[$userInfo['int_codestatut']]['icon']).'" border="0" title="" alt="icon statut" /></td>';
            $tabNonClot .= '<td>'.$infosStatuts[$userInfo['int_codestatut']]['label'].'</td>';
            $tabNonClot .= '<td>'.$userInfo['int_code'].'</td>';
            $tabNonClot .= '<td>'.$userInfo['int_datefinp'].'</td>';
            $tabNonClot .= '<td>'.$userInfo['int_mat'].'</td>';
            $tabNonClot .= '<td>'.$userInfo['int_pbm'].'</td>';
            if($user->admin)
            {
                $tabNonClot .= '<td>'.$userInfo['int_codecli'].'</td>';
                $tabNonClot .= '<td>'.$userInfo['cli_prenom'].' '.$userInfo['cli_nom'].'</td>';
            }
            $tabNonClot .= '<td>
                                        <form method="post" action="smiglance_detail_page.php">
                                            <input name="int_code" type="hidden" value="'.$userInfo['int_code'].'" />
                                            <input class="button" type="submit" value="Détail" />
                                        </form>
                                    </td>';
            $tabNonClot .= '</tr>';
            
            $iNonClot++;
        }
        
    }
    
    //si les tableaux sont vides
    if($iNonClot == 0) 
    {
        $tabNonClot .= '<tr class="impair">';
        $tabNonClot .= '<td colspan="5"><b>Aucune intervention.</b></td>';
        $tabNonClot .= '</tr>';
    }
    if($iClot == 0) 
    {
        $tabClot .= '<tr class="impair">';
        $tabClot .= '<td colspan="6"><b>Aucune intervention.</b></td>';
        $tabClot .= '</tr>';
    }

}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}


// et on affiche

print_fiche_titre($langs->trans($page_name), '');

?>

<br/>
<table id="otherboxes" class="notopnoleftnoright" border="0" width="100%" style="margin-bottom: 2px;">
    <tbody>
        <tr>
            <td class="nobordernopadding" valign="middle">
                <div class="titre">
                    Intervention(s) non terminée(s)
                </div>
            </td>
        </tr>
    </tbody>
</table>
<table class="noborder">
    <tr class="liste_titre">
        <th colspan="2">Statut</th>
        <th>Code d''intervention</th>
        <th>Date de fin</th>
        <th>Materiel</th>
        <th>Probleme</th>
        <?php
        if($user->admin)
        {
        ?>
        <th>Code client</th>
        <th>Nom client</th>
        <?php
        }
        ?>
        <th></th>
    </tr>
    <?php print $tabNonClot; ?>
</table>

<br/>
<table id="otherboxes" class="notopnoleftnoright" border="0" width="100%" style="margin-bottom: 2px;">
    <tbody>
        <tr>
            <td class="nobordernopadding" valign="middle">
                <div class="titre">
                    Intervention(s) terminée(s)
                </div>
            </td>
        </tr>
    </tbody>
</table>
<table class="noborder">
    <tr class="liste_titre">
        <th width="22px"></th>
        <th>Code d''intervention</th>
        <th>Date de fin</th>
        <th>Materiel</th>
        <th>Probleme</th>
        <?php
        if($user->admin)
        {
        ?>
        <th>Code client</th>
        <th>Nom client</th>
        <?php
        }
        ?>
        <th></th>
    </tr>
    <?php print $tabClot; ?>
</table>

<?php

// End of page
llxFooter();
$db->close();
?>