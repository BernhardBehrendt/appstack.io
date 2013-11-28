<?php
class CompController extends Zend_Controller_Action {
	private $oCat;
	private $oAccount;
	private $oAccounts;
	private $oComp;
	private $oSettings;
	public function init() {
		
		/* Initialize action controller here */
		$this->oAccount = new Zend_Session_Namespace ( 'ACCOUNT' );
		$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
		if (! isset ( $this->oAccount->userdata ['UID'] ) || (! isset ( $this->oAccount->userdata ['NAMESPACE'] ) && ( int ) $this->oAccount->userdata ['NAMESPACE'] < 1)) {
			header ( 'Location:' . $this->oSettings->website->site->basehref );
			exit ();
		} else {
			$this->oComp = new Application_Model_Comp ( $this->oAccount->userdata ['NAMESPACE'] );
			$this->oCat = new Application_Model_Cat ( $this->oAccount->userdata ['NAMESPACE'] );
			$this->oAccounts = new Application_Model_Account ( $this->oSettings );
		}
	}
	public function indexAction() {
		Preprocessor_Header::setContentType ( 'json' );
		// action body
		$oTableComposites = new Application_Model_Table ();
		$oTableComposites->setTable ( 'composites' );
		$oTableComposites->setPrimary ( 'idcomposite' );
		
		$aMatchCat = $oTableComposites->getRow ( 'namespaces_idnamespace=' . $this->oAccount->userdata ['NAMESPACE'] . ' AND categories_idcategory=' . $_GET ['parent'] . ' AND (pub=1 OR accounts_idaccount=' . $this->oAccount->userdata ['UID'] . ')' );
		foreach ( $aMatchCat as $aMatchCatKey => $aMatchCatValue ) {
			$aMatchCat [$aMatchCatKey] ['source'] = substr ( urldecode ( $aMatchCatValue ['source'] ), 0, 40 );
		}
		echo json_encode ( $aMatchCat );
	}
	
	/**
	 * (Interface implementet methods override)
	 *
	 * @see var/www/dev/tagitall/library/Tagitall/TagitallDefaults::addAction()
	 *
	 */
	public function addAction() {
		$iParent = (isset ( $_POST ['parent'] )) ? Preprocessor_String::filterBadChars ( $_POST ['parent'] ) : false;
		$sName = (isset ( $_POST ['name'] )) ? Preprocessor_String::filterBadChars ( $_POST ['name'] ) : false;
		
		$sSource = (isset ( $_POST ['source'] )) ? $_POST ['source'] : 'false';
		
		$aResponse = $this->oComp->addComp ( $this->oAccount->userdata ['UID'], $sName, $iParent, $sSource );
		
		if (! $aResponse ['error']) {
			$this->view->Headline = $aResponse ['message'];
			$this->view->updateCategory = $aResponse ['insertin'];
		} else {
			$this->view->Headline = $aResponse ['message'];
		}
	}
	public function duplicateAction() {
		if (isset ( $_POST ['comp'] ) && ( int ) $_POST ['comp'] > 0) {
			$iIdComposite = ( int ) $_POST ['comp'];
			$aResponse = $this->oComp->duplicate ( $this->oAccount->userdata ['UID'], $iIdComposite );
			$this->view->Headline = $aResponse ['message'];
			if (! $aResponse ['error']) {
				$this->view->updateCategory = $aResponse ['category'];
			}
		} else {
			$this->view->Headline = 'Absolute unknown error';
		}
	}
	public function renameAction() {
		$sNewName = Preprocessor_String::filterBadChars ( $_POST ['name'] );
		
		if ($this->oComp->renameComp ( $_POST ['composite'], $sNewName )) {
			$this->view->Headline = 'Renamed';
			$this->view->sNewName = $sNewName;
			$this->view->iCompositeId = $_POST ['composite'];
		} else {
			$this->view->headline = 'Error rename';
		}
	}
	public function removeAction() {
		$iIdComposite = (isset ( $_POST ['comp'] )) ? Preprocessor_String::filterBadChars ( $_POST ['comp'] ) : false;
		$aResponse = $this->oComp->remove ( $this->oAccount->userdata ['UID'], $iIdComposite );
		
		if (! $aResponse ['error']) {
			
			$this->view->Headline = $aResponse ['message'];
			$this->view->updateCategory = $aResponse ['category'];
			$this->view->deleteComp = $aResponse ['composite'];
		} else {
			$this->view->Headline = $aResponse ['message'];
		}
	}
	public function deleteAction() {
		$oTableCompMetas = new Application_Model_Table ();
		$oTableCompMetas->setTable ( 'comp_metas' );
		$oTableCompMetas->setPrimary ( 'idcomp_meta' );
		
		$oTableCMNV = new Application_Model_Table ();
		$oTableCMNV->setTable ( 'comp_meta_values' );
		$oTableCMNV->setPrimary ( 'idcomp_meta_value' );
		
		$aComposite = $this->oComp->getById ( Preprocessor_String::filterBadChars ( $_POST ['composite'] ) );
		
		if ($aComposite ['namespaces_idnamespace'] == $this->oAccount->userdata ['NAMESPACE']) {
			$iIdCompMeta = false;
			
			foreach ( $_POST as $key => $val ) {
				if (is_array ( $val ) && count ( $val ) == 1) {
					foreach ( $val as $sValName => $sKeyVal ) {
						echo $sValName;
						if (stripos ( $sValName, 'metas_idmeta_' ) !== false) {
							$iIdCompMeta = $sKeyVal;
						}
					}
				}
			}
			
			if ($iIdCompMeta) {
				$oSelect = $oTableCompMetas->select ( $oTableCompMetas->getPrimary () )->where ( 'metas_idmeta=' . $iIdCompMeta . ' AND composites_idcomposite=' . Preprocessor_String::filterBadChars ( $_POST ['composite'] ) );
				$oresult = $oTableCompMetas->fetchRow ( $oSelect );
				if (isset ( $oresult->idcomp_meta )) {
					$oTableCMNV->delete ( 'comp_metas_idcomp_meta=' . $oresult->idcomp_meta );
					$oTableCompMetas->delete ( $oTableCompMetas->getPrimary () . '=' . $oresult->idcomp_meta );
					$this->view->sMessage = 'Metaitem was untouched';
					$this->view->MetaDelete = $iIdCompMeta;
				} else {
					$this->view->sMessage = 'Metaitem was not appended';
				}
			} else {
				$this->view->sMessage = 'Meta wasnt found';
			}
		} else {
			$this->view->sMessage = 'Meta delete error.';
		}
	}
	public function insertAction() {
		// CHECK IF OSER IS OWNER OF ELEMENTS
		$iComposite = (isset ( $_POST ['comp'] )) ? Preprocessor_String::filterBadChars ( $_POST ['comp'] ) : false;
		$iDestination = (isset ( $_POST ['destination'] )) ? Preprocessor_String::filterBadChars ( $_POST ['destination'] ) : false;
		
		if ($this->oCat->isInSpace ( $iDestination )) {
			$aComposite = $this->oComp->getById ( $iComposite );
			if (isset ( $aComposite ['namespaces_idnamespace'] ) && $aComposite ['namespaces_idnamespace'] == $this->oAccount->userdata ['NAMESPACE']) {
				if ($aComposite ['categories_idcategory'] != $iDestination) {
					$this->oComp->insertComp ( $iComposite, $iDestination );
					$this->view->Headline = 'Composite moved';
					$this->view->updateComposite = $iComposite;
					$this->view->updateCategory = $iDestination;
					$this->view->fromCategory = $aComposite ['categories_idcategory'];
				} else {
					$this->view->Headline = 'Composite already placed here';
				}
			} else {
				$this->view->Headline = 'Composite doesn\\\’ t exist';
			}
		} else {
			$this->view->Headline = 'Cant\\\’ t find target';
		}
	}
	public function appendAction() {
		$aResponse = $this->oComp->touch ( $this->oAccount->userdata ['UID'], $_GET ['composite'], $_GET ['meta'] );
		$this->view->bError = $aResponse ['error'];
		if (! $aResponse ['error']) {
			$this->view->iComposite = $aResponse ['composite'];
		} else {
			$this->view->sErrorMessage = $aResponse ['message'];
		}
	}
	public function infoAction() {
		if (isset ( $_POST ['public'] ) && isset ( $_POST ['composite'] )) {
			$iStatus = Preprocessor_String::filterBadChars ( $_POST ['public'] );
			$iComposite = Preprocessor_String::filterBadChars ( $_POST ['composite'] );
			
			// Protect foreign data
			$iStatus = ($iStatus == 0) ? 0 : 1;
			
			if ($this->oComp->statusComp ( $this->oAccount->userdata ['UID'], $iComposite, $iStatus )) {
				$this->view->Headline = 'Changed composite status';
				$this->view->Message = ($iStatus == 1) ? 'This composite is now accessible for everyone.' : 'This composite is now private';
			} else {
				$this->view->Headline = 'Composite is not your own!';
			}
		} else {
			$this->view->Headline = 'Invalid call';
		}
	}
	public function relocateAction() {
		if (isset ( $_POST ['source'] ) && isset ( $_POST ['composite'] )) {
			
			$sSource = $_POST ['source'];
			$iComposite = Preprocessor_String::filterBadChars ( $_POST ['composite'] );
			if ($this->oComp->relocateComp ( $this->oAccount->userdata ['UID'], $_POST ['composite'], $sSource )) {
				$this->view->Headline = 'Relocated';
			} else {
				$this->view->headline = 'Error relocate';
			}
		} else {
			$this->view->headline = 'Invallid call';
		}
	}
	public function saveAction() {
		// Is owner
		// update trimmed
		if (isset ( $_POST ['composite'] )) {
			$aComp = $aComp = $this->oComp->getById ( $_POST ['composite'] );
			
			if ($aComp ['namespaces_idnamespace'] == $this->oAccount->userdata ['NAMESPACE']) {
				
				$aStoreData = array ();
				
				$oTableCompMetas = new Application_Model_Table ();
				$oTableCompMetas->setTable ( 'comp_metas' );
				$oTableCompMetas->setPrimary ( 'idcomp_meta' );
				
				$oTableCMNV = new Application_Model_Table ();
				$oTableCMNV->setTable ( 'comp_meta_values' );
				$oTableCMNV->setPrimary ( 'idcomp_meta_value' );
				
				// Iterate for primary of meta
				if (isset ( $_POST ['container'] ) && is_array ( $_POST ['container'] )) {
					
					foreach ( $_POST ['container'] as $mKey => $mValue ) {
						if (stripos ( $mKey, 'metas_idmeta_' ) !== false && ! isset ( $iFkMeta )) {
							$iFkMeta = $mValue;
						}
						
						if (stripos ( $mKey, 'valname_' ) !== false) {
							$aStoreData [count ( $aStoreData )] = array ('idcomp_meta_value' => str_replace ( 'valname_', '', $mKey ), 'valdef' => $mValue );
						}
					}
				}
				
				if (isset ( $iFkMeta )) {
					$sSelect = $oTableCompMetas->select ( $oTableCompMetas->getPrimary () )->where ( 'composites_idcomposite=' . $aComp ['idcomposite'] . ' AND metas_idmeta=' . $iFkMeta );
					$aRow = $oTableCompMetas->fetchRow ( $sSelect );
					
					if (isset ( $aRow->idcomp_meta )) {
						for($i = 0; $i < count ( $aStoreData ); $i ++) {
							$sNewVal = ($aStoreData [$i] ['valdef'] != 'null' || (strlen ( $aStoreData [$i] ['valdef'] ) != 0 && $aStoreData [$i] ['valdef'] != 'null')) ? trim ( $aStoreData [$i] ['valdef'] ) : NULL;
							$oTableCMNV->update ( array ('valdef' => $sNewVal ), 'comp_metas_idcomp_meta=' . $aRow->idcomp_meta . ' AND idcomp_meta_value=' . $aStoreData [$i] ['idcomp_meta_value'] );
							unset ( $sNewVal );
						}
						$this->view->Headline = 'Composites meta was updated';
					}
				} else {
					$this->view->Headline = 'Metaitem does not exist';
				}
			} else {
				$this->view->Headline = 'Metaitem does not exist';
			}
		}
	}
	public function loadAction() {
		if (isset ( $_GET ['composite'] )) {
			$aComp = $this->oComp->getById ( Preprocessor_String::filterBadChars ( $_GET ['composite'] ) );
			if ($aComp ['namespaces_idnamespace'] == $this->oAccount->userdata ['NAMESPACE']) {
				/*
				 * SELECT a.*, b.* FROM comp_metas AS a INNER JOIN
				 * comp_meta_values AS b ON
				 * a.idcomp_meta=b.comp_metas_idcomp_meta WHERE
				 * composites_idcomposite=POSTVAL;
				 */
				$oTableCompMetas = new Application_Model_Table ();
				$oTableCompMetas->setTable ( 'comp_metas' );
				$oTableCompMetas->setPrimary ( 'idcomp_meta' );
				
				$oSelect = $oTableCompMetas->select ()->setIntegrityCheck ( false )->from ( $oTableCompMetas->getTable () )->join ( 'comp_meta_values', $oTableCompMetas->getPrimary () . '=comp_metas_idcomp_meta', '*' )->join ( 'metas', 'metas_idmeta=idmeta', 'name' )->where ( 'composites_idcomposite=' . (Preprocessor_String::filterBadChars ( $_GET ['composite'] )) );
				
				$oResult = $oTableCompMetas->fetchAll ( $oSelect );
				
				$aPreJson = array ();
				foreach ( $oResult as $oRow ) {
					$iIdMeta = $oRow->metas_idmeta;
					$iMetaValId = $oRow->idcomp_meta_value;
					
					if (! isset ( $aPreJson [$iIdMeta] ['name'] )) {
						$aPreJson [$iIdMeta] ['name'] = $oRow->name;
					}
					$aPreJson [$iIdMeta] ['values'] [$iMetaValId] = array ($oRow->valname => (strlen ( $oRow->valdef ) > 0) ? $oRow->valdef : '' );
				}
				
				Preprocessor_Header::setContentType ( 'json' );
				echo json_encode ( $aPreJson );
			}
		}
	}
}
?>