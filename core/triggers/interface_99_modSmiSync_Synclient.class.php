<?php
/* Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis.houssin@capnetworks.com>
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
 *  \file       htdocs/core/triggers/interface_99_modSmiSync_Synclient.class.php
 *  \ingroup    core
 *  \brief      Fichier de demo de personalisation des actions du workflow
 *  \remarks
 *              
 */
require_once DOL_DOCUMENT_ROOT."/smisync/class/db_smi.class.php";

/**
 *  Class of triggers for smisync module
 */
class InterfaceSynclient
{
    var $db;
    
    /**
     *   Constructor
     *
     *   @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
    
        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "demo";
        $this->description = "Triggers of this module are empty functions. They have no effect. They are provided for tutorial purpose only.";
        $this->version = 'dolibarr';            // 'development', 'experimental', 'dolibarr' or version
        $this->picto = 'technic';
    }
    
    
    /**
     *   Return name of trigger file
     *
     *   @return     string      Name of trigger file
     */
    function getName()
    {
        return $this->name;
    }
    
    /**
     *   Return description of trigger file
     *
     *   @return     string      Description of trigger file
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   Return version of trigger file
     *
     *   @return     string      Version of trigger file
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'development') return $langs->trans("Development");
        elseif ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }
    
    /**
     *      Function called when a Dolibarrr business event is done.
     *      All functions "run_trigger" are triggered if file is inside directory htdocs/core/triggers
     *
     *      @param	string		$action		Event action code
     *      @param  Object		$object     Object
     *      @param  User		$user       Object user
     *      @param  Translate	$langs      Object langs
     *      @param  conf		$conf       Object conf
     *      @return int         			<0 if KO, 0 if no triggered ran, >0 if OK
     */
	function run_trigger($action,$object,$user,$langs,$conf)
    {
        // Put here code you want to execute when a Dolibarr business events occurs.
        // Data and type of action are stored into $object and $action
    
        // Users
        if ($action == 'USER_LOGIN')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_UPDATE_SESSION')
        {
            // Warning: To increase performances, this action is triggered only if 
            // constant MAIN_ACTIVATE_UPDATESESSIONTRIGGER is set to 1.
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_CREATE_FROM_CONTACT')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_NEW_PASSWORD')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_ENABLEDISABLE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_LOGOUT')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_SETINGROUP')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'USER_REMOVEFROMGROUP')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

		// Groups
		elseif ($action == 'GROUP_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'GROUP_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'GROUP_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
		
        // Companies
        elseif ($action == 'COMPANY_CREATE')
        {

            try {
                //connection bdd smi
                $dbSmi = db_smi::getInstance()->getSmi();
                
                // recherche du code client le plus grand
                $clicode = $dbSmi->query('SELECT cli_code FROM smi_cli ORDER BY cli_code DESC LIMIT 0, 1');
                $clicod = $clicode->fetch(PDO::FETCH_BOTH);
                $cli_code =  $clicod['cli_code'];
                    
                // on tri les clients dans les tiers
                if($object->client != 0)
                {
                    // INFOS CLIENTS
                        
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
        
                    if($object->civilite == 'MME')
                        $cli_civilite = 'MME';
                    else if($object->civilite == 'MLE')
                        $cli_civilite = 'MELLE';  // modifier cette valeur en MME si l'on ne veux pas insulter les madames
                    else
                        $cli_civilite = 'M.';
        
                    $cli_prenom = addslashes($object->nom); // mis dans le champ prenom pour raison de formatage de text dans le champ nom
                    $cli_nom = ' ';
                    $cli_adr1 = addslashes($object->address);
                    $cli_adr2 = addslashes($object->zip).' '.addslashes($object->town); // code pour la ville (2 ligne en dessous) un peu special je le met donc dans ce champ
                    $cli_dep = addslashes(substr($object->zip, 0, 2)); // recupere le num de departement dans le code postale
                    // a voir
                    $cli_ville = '0';
                    $cli_codepays = 'FR';
                    $cli_codeadev = 'EUR';
                    $cli_telf = addslashes($object->phone);
                    $cli_fax = addslashes($object->fax);
                    $cli_telp = addslashes($object->phone_mobile);
                    $cli_email = addslashes($object->email);
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
                    // echo $myquery.'<br>';
                    $dbSmi->query($myquery);
                    
                    //j'insert les id doli et smi dans la table des correspondances
                    $lastSmiId1 = $dbSmi->query("SELECT LAST_INSERT_ID() FROM smi_cli");
                    $lastSmiId0 = $lastSmiId1->fetch(PDO::FETCH_BOTH);
                    $lastSmiId =  $lastSmiId0[0];
                    
                    $this->db->query("INSERT INTO llx_idcli (idcli_doli, idcli_smi) VALUES (".$object->id.", $lastSmiId)");
                    
                    
                }
                    
            
            }
            catch (Exception $e)
            {
                die('Erreur : ' . $e->getMessage());
            }

            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'COMPANY_MODIFY')
        {
            try {                
                //connection bdd smi
                $dbSmi = db_smi::getInstance()->getSmi();                
                                
                // on tri les clients dans les tiers
                if($object->client != 0)
                {
                    // recherche de l'id du client smi correspondant
                    $cliId = $this->db->query('SELECT idcli_smi FROM llx_idcli WHERE idcli_doli = '.$object->id);
                    $cliSmi = $this->db->fetch_object($cliId);
                    $cliSmiId =  $cliSmi->idcli_smi;
                    
                    
                    // INFOS CLIENTS
                    $cli_datemod = date('Y-m-d'); // date du jour
                    
                    if($object->civilite == 'MME')
                        $cli_civilite = 'MME';
                    else if($object->civilite == 'MLE')
                        $cli_civilite = 'MELLE';  // modifier cette valeur en MME si l'on ne veux pas insulter les madames
                    else
                        $cli_civilite = 'M.';
        
                    $cli_prenom = addslashes($object->nom); // mis dans le champ prenom pour raison de formatage de text dans le champ nom
                    $cli_adr1 = addslashes($object->address);
                    $cli_adr2 = addslashes($object->zip).' '.addslashes($object->town); // code pour la ville (2 ligne en dessous) un peu special je le met donc dans ce champ
                    $cli_dep = addslashes(substr($object->zip, 0, 2)); // recupere le num de departement dans le code postale

                    $cli_telf = addslashes($object->phone);
                    $cli_fax = addslashes($object->fax);
                    $cli_telp = addslashes($object->phone_mobile);
                    $cli_email = addslashes($object->email);
                    
                    // si le client modifié n'est pas present dans la bdd smi on le créé
                    if(!is_numeric($cliSmiId))
                    {
                        // recherche du code client le plus grand
                        $clicode = $dbSmi->query('SELECT cli_code FROM smi_cli ORDER BY cli_code DESC LIMIT 0, 1');
                        $clicod = $clicode->fetch(PDO::FETCH_BOTH);
                        $cli_code =  $clicod['cli_code'];


                        $cli_cat = 'PAR'; // PAR pour 'particulier'
                        $cli_datecrea = date('Y-m-d'); // date du jour
                        $cli_codecrea = 'Administrateur';

                        $cli_prop = 'A06530'; // code du 'magasin' (possibilité de le recuperer quelque part dans la bdd)
                        $cli_codemod = 'Administrateur';
                        $cli_code++; // on incremente le code client
                        $cli_pass = '';
                        $cli_type = '2'; // 2 pour 'Deja client' - 1 pour demande d'intervention - 0 pour prospect
                        $cli_ste = '';
                        $cli_rcs = '';
                        $cli_ape = '';
                        $cli_tvai = '';
                    
                        $cli_nom = ' ';
                        
                        // a voir
                        $cli_ville = '0';
                        $cli_codepays = 'FR';
                        $cli_codeadev = 'EUR';
                        
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
                        $dbSmi->query($myquery);
                        
                        $lastSmiId1 = $dbSmi->query("SELECT LAST_INSERT_ID() FROM smi_cli");
                        $lastSmiId0 = $lastSmiId1->fetch(PDO::FETCH_BOTH);
                        $lastSmiId =  $lastSmiId0[0];
                        
                        $this->db->query("INSERT INTO llx_idcli (idcli_doli, idcli_smi) VALUES (".$object->id.", $lastSmiId)");
                    }
                    else
                    {
                        // je met a jour le client dolibarr dans la bdd de smi
                        $myquery = "UPDATE smi_cli SET cli_datemod='$cli_datemod', cli_civilite='$cli_civilite', cli_prenom='$cli_prenom', cli_adr1='$cli_adr1', cli_adr2='$cli_adr2', cli_dep='$cli_dep', cli_telf='$cli_telf', cli_fax='$cli_fax', cli_telp='$cli_telp', cli_email='$cli_email' WHERE cli_id = $cliSmiId";
                        $dbSmi->query($myquery);
                    }
                }
                
            
            }
            catch (Exception $e)
            {
                die('Erreur : ' . $e->getMessage());
            }
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'COMPANY_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Contacts
        elseif ($action == 'CONTACT_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTACT_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTACT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Products
        elseif ($action == 'PRODUCT_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PRODUCT_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PRODUCT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

		// Customer orders
        elseif ($action == 'ORDER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_CLONE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_BUILDDOC')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_SENTBYMAIL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_CLASSIFY_BILLED')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'LINEORDER_INSERT')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'LINEORDER_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

		// Supplier orders
        elseif ($action == 'ORDER_SUPPLIER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_SUPPLIER_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_SUPPLIER_SENTBYMAIL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'ORDER_SUPPLIER_BUILDDOC')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Proposals
        elseif ($action == 'PROPAL_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_CLONE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_BUILDDOC')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_SENTBYMAIL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_CLOSE_SIGNED')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_CLOSE_REFUSED')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROPAL_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'LINEPROPAL_INSERT')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'LINEPROPAL_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'LINEPROPAL_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Contracts
        elseif ($action == 'CONTRACT_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTRACT_ACTIVATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTRACT_CANCEL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTRACT_CLOSE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CONTRACT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Bills
        elseif ($action == 'BILL_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_CLONE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
		elseif ($action == 'BILL_UNVALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_BUILDDOC')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_SENTBYMAIL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_CANCEL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'BILL_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
		elseif ($action == 'LINEBILL_INSERT')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
		elseif ($action == 'LINEBILL_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Payments
        elseif ($action == 'PAYMENT_CUSTOMER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PAYMENT_SUPPLIER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PAYMENT_ADD_TO_BANK')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PAYMENT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

		// Interventions
		elseif ($action == 'FICHINTER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'FICHINTER_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'FICHINTER_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
	    elseif ($action == 'FICHINTER_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

        // Members
        elseif ($action == 'MEMBER_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_SUBSCRIPTION')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_NEW_PASSWORD')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_RESILIATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'MEMBER_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        
        // Categories
        elseif ($action == 'CATEGORY_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CATEGORY_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'CATEGORY_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        
        // Projects
        elseif ($action == 'PROJECT_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROJECT_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'PROJECT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        
        // Project tasks
        elseif ($action == 'TASK_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'TASK_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'TASK_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        
        // Task time spent
        elseif ($action == 'TASK_TIMESPENT_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'TASK_TIMESPENT_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'TASK_TIMESPENT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        
        // Shipping
        elseif ($action == 'SHIPPING_CREATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'SHIPPING_MODIFY')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'SHIPPING_VALIDATE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'SHIPPING_SENTBYMAIL')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'SHIPPING_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }
        elseif ($action == 'SHIPPING_BUILDDOC')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
        }

		return 0;
    }

}
?>
