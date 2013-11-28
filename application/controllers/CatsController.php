<?php
/**
 * Central categories controller
 * This controller execute call defined actions on Base of the Application_Model_Cat model
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Categories_Controller
 * @version 0.0.2b
 *
 * @uses Application_Model_Cat
 *
 */
class CatsController extends Zend_Controller_Action {
	private $oCat;
	private $oAccount;
	private $oAccounts;
	private $oSettings;
	private $oComp;
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
			if (! $this->oCat->getRoot ( ( int ) $this->oAccount->userdata ['NAMESPACE'] )) {
				$this->oCat->createRoot ( $this->oAccount->userdata ['NAMESPACE'] );
			}
		}
	}
	public function indexAction() {
		Preprocessor_Header::setContentType ( 'json' );

		$aTree = $this->oCat->getTree ();
		if (is_array ( $aTree )) {

			$aPreTree = array ();
			// $oComp = new Application_Model_Comp();

			$aTree = $this->oComp->extendTree ( $this->oAccount->userdata ['UID'], $aTree );

			// First Step
			for($i = 0; $i < count ( $aTree ); $i ++) {
				$aPreTree [$aTree [$i] ['depth']] [] = $aTree [$i];
			}
			// Second Step; $i++
			for($i = 0; $i < count ( $aPreTree ); $i ++) {
				if ($i == 0) {
					continue;
				}
				if ($i == 1) {
					foreach ( $aPreTree [$i] as $Key => $aValue ) {
						$aPreTree [$i] [$Key] ['child_of'] = 0;
					}
				}
				if ($i > 1) {
					// Third Step
					foreach ( $aPreTree [$i] as $KeyA => $aValueA ) {
						// Fourth step
						foreach ( $aPreTree [$i - 1] as $KeyB => $aValueB ) {
							if ($aValueA ['lft'] > $aValueB ['lft'] && $aValueA ['rgt'] < $aValueB ['rgt']) {
								$aPreTree [$i] [$KeyA] ['child_of'] = $aValueB ['ident'];
								break;
							}
						}
					}
				}
			}
			$aPreJson [0] = array ('idcategory' => $aPreTree [0] [0] ['ident'], 'name' => $aPreTree [0] [0] ['name'], 'child_of' => ((isset ( $aPreTree [0] [0] ['child_of'] )) ? $aPreTree [0] [0] ['child_of'] : '_rootlevel'), 'comps' => $aPreTree [0] [0] ['comps'] );

			// unset because _rootlevel is the root category of each following
			// category
			unset ( $aPreTree [0] );

			array_merge ( $aTree );

			// And now set roots subcategores as its properies
			$aPreJson [0] ['properties'] = $aPreTree;

			$this->view->tree = $aPreJson;
		}
	}

	/**
	 * (Interface implementet methods override)
	 *
	 * @see var/www/dev/tagitall/library/Tagitall/TagitallDefaults::addAction()
	 *
	 */
	public function addAction() {
		$sHeadline = '...';

		if (isset ( $_POST ['name'] ) && isset ( $_POST ['mode'] ) && isset ( $_POST ['direction'] ) && $_POST ['direction'] != 0) {

			$sHeadline = 'Category was added successfull';
			$sName = Preprocessor_String::filterBadChars ( $_POST ['name'] );
			$sAlias = Preprocessor_String::legalizeString ( $sName );
			if ($this->oAccounts->setUsage ( $this->oAccount->userdata ['UID'], array ('maxcats' => 1 ) )) {
				if ($_POST ['mode'] == 'first') {
					$this->oCat->insertAsFirstChildOf ( $_POST ['direction'], $sName, $sAlias, $this->oAccount->userdata ['UID'] );
				}
				if ($_POST ['mode'] == 'last') {
					$this->oCat->insertAsLastChildOf ( $_POST ['direction'], $sName, $sAlias, $this->oAccount->userdata ['UID'] );
				}

				if ($_POST ['mode'] == 'before') {
					$this->oCat->insertAsPrevSiblingOf ( $_POST ['direction'], $sName, $sAlias, $this->oAccount->userdata ['UID'] );
				}
				if ($_POST ['mode'] == 'behind') {
					$this->oCat->insertAsNextSiblingOf ( $_POST ['direction'], $sName, $sAlias, $this->oAccount->userdata ['UID'] );
				}
			} else {
				$sHeadline = 'The limit of your category budget has been reached';
			}
		} else {
			if ($_POST ['direction'] == 0) {
				$sHeadline = 'Cant store';
			} else {
				$sHeadline = 'Got no data';
			}
		}
		$this->view->Headline = $sHeadline;

		// Required Data to Add Cat
		// New cat name
		// New cat position also - erstes Element von Kategorie...
		// - letztes Element von Kategorie...
		// - Vorg√§nger von Element...
		// - Nachfolger von Element...
		// in JS iCurCatIn is The holder fr the current catagory bt if the value
	// is 0 user is in root cat and i have to use iInCat
	}
	public function deleteAction() {
		if (count ( $this->oComp->getByCategory ( $_POST ['category'] ) ) == 0) {

			$oRowToDelete = $this->oCat->fetchRow ( $this->oCat->select ()->where ( $this->oCat->getPrimary () . '=' . (( int ) $_POST ['category']) ) );
			$iDelete = $this->oCat->deleteNode ( $_POST ['category'] );

			if ($iDelete !== 0 && $iDelete !== false) {

				$iGiveBudgetBack = $this->oAccount->userdata ['UID'];

				if (isset ( $oRowToDelete->accounts_idaccount ) && ( int ) $oRowToDelete->accounts_idaccount > 0) {
					$iGiveBudgetBack = ( int ) $oRowToDelete->accounts_idaccount;
				}

				$this->oAccounts->setUsage ( $iGiveBudgetBack, array ('maxcats' => - 1 ) );

				$this->view->Headline = 'Category was deleted.';
			} else {
				if ($iDelete === 0) {
					$this->view->Headline = 'Cant delete because category has subcategories';
				} else {
					$this->view->Headline = 'Category not found';
				}
			}
		} else {
			$this->view->Headline = 'Cant delete because category has composites';
		}
	}
	public function saveAction() {
		echo __METHOD__;
	}
	public function renameAction() {
		$sNewName = Preprocessor_String::filterBadChars ( $_POST ['name'] );
		$sAlias = Preprocessor_String::legalizeString ( $sNewName );

		if ($this->oCat->renameCategory ( $_POST ['direction'], $sNewName, $sAlias )) {
			$this->view->Headline = 'Renamed';
		} else {
			$this->view->headline = 'Error rename';
		}
	}
	public function infoAction() {
		$iStatus = Preprocessor_String::filterBadChars ( $_POST ['public'] );

		// Protect foreign data
		$iStatus = ($iStatus == 0) ? 0 : 1;

		if ($this->oCat->statusCategory ( $_POST ['direction'], $iStatus )) {
			$this->view->Headline = 'Changed category status';
			$this->view->Message = ($iStatus == 1) ? 'This category is now accessible with ists composites for everyone.' : 'This category is now private';
		} else {
			$this->view->Headline = 'Unspecified error';
		}
	}

	// ///////
	public function testAction() {
		exit ();
		// $root = $this->oCat->createRoot($this->oSession->id_user); //creating
		// root node.
		$iInCat = 2;

		for($i = 0; $i < 50; $i ++) {

			$iInCat = $this->oCat->insertAsNextSiblingOf ( $iInCat, $i );

			$iInsertIn = $iInCat;

			for($j = 0; $j < 50; $j ++) {
				$iInsertIn = $this->oCat->insertAsFirstChildOf ( $iInCat, $i . ' - ' . $j );
			}
		}
	}

	// ///////
}
?>