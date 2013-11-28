<?php
/**
 * Central Metainformation controller
 * This controller provides create, delete, modify of metas and their values
 * on base of the following models
 *
 * 1) Application_Model_Meta
 * 2) Application_Model_Metavalue
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Metainformation_Controller
 * @version 1.0.0
 *
 * @uses Application_Model_Meta, Application_Model_Metavalue
 */
class MetaController extends Zend_Controller_Action {
	private $oMeta;
	private $oMetavalue;
	private $oAccount;
	private $oAccounts;
	private $oUser;
	private $oSettings;
	public function init() {
		$this->oAccount = new Zend_Session_Namespace ( 'ACCOUNT' );
		$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
		if (! isset ( $this->oAccount->userdata ['UID'] ) || (! isset ( $this->oAccount->userdata ['NAMESPACE'] ) && ( int ) $this->oAccount->userdata ['NAMESPACE'] < 1)) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		} else {
			$this->oMeta = new Application_Model_Meta ( $this->oAccount->userdata ['NAMESPACE'] );
			$this->oMetavalue = new Application_Model_Metavalue ();
			$this->oAccounts = new Application_Model_Account ( $this->oSettings );
		}
	}
	
	/**
	 * Represent all metas and route them to json output
	 *
	 * @return none
	 */
	public function indexAction() {
		// Meteas with their values (innerjoin array)
		$aMeta = $this->oMeta->getAll ( $this->oMetavalue );
		
		// JSON nodemame for metas values
		$sNodeName = 'properties';
		
		// All collumns from inner join rersult
		if (isset ( $aMeta ) && array_key_exists ( 0, $aMeta )) {
			if (is_array ( $aMeta [0] )) {
				$aKeys = array_keys ( $aMeta [0] );
			}
		} else {
			$aKeys = array ();
		}
		
		// Forbidden values (values which should placed in the root tree [not in
		// the new json node])
		$aBreaks = array ('idmeta', 'name' );
		
		// Allready iterated elements in given innerjoin result (int
		$aIBreaks = array ();
		
		// Iterate all items and move allowed value data (key => value) to
		// jsonnode
		$aPreJson = false;
		for($i = 0; $i < count ( $aMeta ); $i ++) {
			if (! in_array ( $i, $aIBreaks )) {
				
				$aIBreaks [count ( $aIBreaks )] = $i;
				
				// Count all elements in preJson Array
				$iElements = ($aPreJson) ? count ( $aPreJson ) : 0;
				
				// Get next empty position
				$iPosition = (isset ( $aPreJson [$iElements] [$sNodeName] )) ? count ( $aPreJson [$iElements] [$sNodeName] ) : 0;
				
				// Move all curent item allowed (key => values) to json node
				for($j = 0; $j < count ( $aKeys ); $j ++) {
					if (! in_array ( $aKeys [$j], $aBreaks )) {
						$aPreJson [$iElements] [$sNodeName] [$iPosition] [$aKeys [$j]] = $aMeta [$i] [$aKeys [$j]];
					} else {
						$aPreJson [$iElements] [$aKeys [$j]] = $aMeta [$i] [$aKeys [$j]];
					}
				}
				
				// Now try all upcoming elements which hasnt been used until now
				if (is_array ( $aMeta )) {
					foreach ( $aMeta as $iCurrent => $aParams ) {
						if ($aParams ['metas_idmeta'] == $aMeta [$i] ['idmeta']) {
							if (! in_array ( $iCurrent, $aIBreaks )) {
								
								$aIBreaks [count ( $aIBreaks )] = $iCurrent;
								$iPosition = count ( $aPreJson [$iElements] [$sNodeName] );
								
								foreach ( $aParams as $sKey => $sDefault ) {
									if (! in_array ( $sKey, $aBreaks )) {
										$aPreJson [$iElements] [$sNodeName] [$iPosition] [$sKey] = $sDefault;
									}
								}
							}
						}
					}
				}
			}
		}
		$aPreJson = json_encode ( $aPreJson );
		if (strlen ( $aPreJson ) > 0) {
			Preprocessor_Header::setContentType ( 'json' );
			$this->view->ausgabe = $aPreJson;
		} else {
			$this->view->ausgabe = array ('data' => 'false' );
		}
	}
	
	/**
	 * (Interface implementet methods override)
	 *
	 * @see var/www/dev/tagitall/library/Tagitall/TagitallDefaults::addAction()
	 *
	 */
	public function addAction() {
		if (isset ( $_POST ) && is_array ( $_POST ) && count ( $_POST ) == 1) {
			foreach ( $_POST as $sMetaname => $aNamesValues ) {
				$sMetaname = Preprocessor_String::legalizeString ( $sMetaname );
				if (isset ( $sMetaname )) {
					
					$iPrimaryMeta = ( int ) $this->oMeta->insertMeta ( array ('name' => $sMetaname, 'accounts_idaccount' => $this->oAccount->userdata ['UID'], 'namespaces_idnamespace' => $this->oAccount->userdata ['NAMESPACE'] ) );
					
					if (is_int ( $iPrimaryMeta ) && $iPrimaryMeta != 0) {
						if (! $this->oAccounts->setUsage ( $this->oAccount->userdata ['UID'], array ('maxmetas' => 1 ) )) {
							$this->view->Headline = 'The limit of your meta budget has been reached';
							$this->view->Message = '';
						} else {
							$insert = $this->oMetavalue->insertValue ( $aNamesValues, $iPrimaryMeta );
							if ($insert) {
								// Erfolgreich gespeichert
								$this->view->Headline = 'Meta item was successfull created';
								$this->view->Message = '';
							} else {
								$this->view->Headline = 'Error wrong data transmitted';
								$this->view->Message = 'Please try again or finish the job';
							}
						}
					} else {
						$this->view->Headline = 'Nothing done';
						$this->view->Message = 'It seems the metaitem already exists.';
					}
				} else {
					$this->view->Headline = 'Got no data!';
					$this->view->Message = '';
				}
			}
		} else {
			$this->view->Headline = 'Got no data!';
			$this->view->Message = '';
		}
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see DSBRG.NET/root/www/dev/tagitall/library/Tagitall/TagitallDefaults::getAction()
	 */
	public function getAction() {
		echo __METHOD__;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see DSBRG.NET/root/www/dev/tagitall/library/Tagitall/TagitallDefaults::deleteAction()
	 *
	 */
	public function deleteAction() {
		$this->view->Headline = 'Cannot delete metaitem';
		$this->view->Message = 'Meta is in in touch with a composite';
		$oTableCompMetas = new Application_Model_Table ();
		$oTableCompMetas->setTable ( 'comp_metas' );
		$oTableCompMetas->setPrimary ( 'idcomp_meta' );
		
		// Check if Meta is in use
		if ($_REQUEST ['container']) {
			foreach ( $_REQUEST ['container'] as $mKey => $mValue ) {
				if (( int ) $mValue > 0) {
					$oSelect = $oTableCompMetas->fetchAll ( $oTableCompMetas->select ()->from ( $oTableCompMetas->getTable () )->where ( 'metas_idmeta=' . $mValue ) );
					if ($oSelect->count () == 0) {
						$oRowToDelete = $this->oMeta->fetchRow ( $this->oMeta->select ()->where ( $this->oMeta->getPrimary () . '=' . $mValue ) );
						
						if ($this->oMeta->deleteMeta ( $mValue )) {
							
							$iGiveBudgetBack = $this->oAccount->userdata ['UID'];
							if (isset ( $oRowToDelete->accounts_idaccount ) && ( int ) $oRowToDelete->accounts_idaccount > 0) {
								$iIdCreator = ( int ) $oRowToDelete->accounts_idaccount;
								$iGiveBudgetBack = ($iIdCreator > 0) ? $iIdCreator : $iGiveBudgetBack;
							}
							
							$this->oAccounts->setUsage ( $iGiveBudgetBack, array ('maxmetas' => - 1 ) );
							$this->view->Headline = 'Metaitem was deleted';
							$this->view->Message = '';
						}
					}
				}
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see DSBRG.NET/root/www/dev/tagitall/library/Tagitall/TagitallDefaults::saveAction()
	 */
	public function saveAction() {
		if ($this->oMetavalue->updateValue ( $_REQUEST )) {
			$this->view->Headline = 'Metaitem was updated!';
			// $this->view->Message = 'Stored all changes';
		} else {
			$this->view->Headline = 'No changes made on metaitem!';
			$this->view->Message = '';
		}
	}
}
?>