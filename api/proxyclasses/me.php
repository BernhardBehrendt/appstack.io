<?php

require_once 'abstract.proxy.php';
class me extends Proxy {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/privileges/
	 */
	public function privileges() {
		if ($this->req ['auth_type'] == 'oauth') {
			if ($this->isReadable ( true )) {
				$oGroups = new Application_Model_Groups ( $this->oSettings );
				$this->addEntry ( 'response', array ('groups' => $oGroups->accountGroups () ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/accounts/
	 */
	public function accounts() {
		if ($this->req ['auth_type'] == 'oauth') {
			if ($this->isReadable ( true )) {
				$aSubAccounts = $this->getMySubAccounts ( true )->toArray ();
				$aSubAccountsOut = array ();
				$aNonRelevant = array ('fk_account', 'sid', 'password', 'address', 'city', 'company', 'phone', 'regdate', 'lastlogin', 'activation', 'countries_idcountry' );
				foreach ( $aSubAccounts as $iKey => $aUser ) {
					array_push ( $aSubAccountsOut, $this->removeKeys ( $aUser, $aNonRelevant ) );
					$aSubAccountsOut [count ( $aSubAccountsOut ) - 1] ['idaccount'] = ( int ) $aSubAccountsOut [count ( $aSubAccountsOut ) - 1] ['idaccount'];
					$aSubAccountsOut [count ( $aSubAccountsOut ) - 1] ['activated'] = ($aSubAccountsOut [count ( $aSubAccountsOut ) - 1] ['activated'] == 1) ? true : false;
				}
				$this->addEntry ( 'response', array ('accounts' => $aSubAccountsOut ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/budget/
	 */
	public function budget() {
		if ($this->req ['auth_type'] == 'oauth') {
			if ($this->isReadable ( true )) {
				$aBudget = $this->getMyBudget ();
				$aBudgetOut = array ();
				foreach ( $aBudget as $key => $budget ) {
					$key = strtolower ( str_replace ( 'HAS', '', $key ) );
					$aBudgetOut [$key] = $budget;
				}
				$this->addEntry ( 'response', array ('budget' => $aBudgetOut ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}

		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/groups/
	 */
	public function groups() {
		if ($this->req ['auth_type'] == 'oauth') {
			if ($this->isReadable ( true )) {
				$oGroups = new Application_Model_Groups ( $this->oSettings );
				$aGroups = $oGroups->getMyGroups ( true )->toArray ();
				$aGroupsOut = array ();
				$aNonRelevant = array ('accounts_idaccount', 'recursiv', 'catmode' );

				foreach ( $aGroups as $key => $value ) {
					array_push ( $aGroupsOut, $this->removeKeys ( $value, $aNonRelevant ) );
					$aGroupsOut [count ( $aGroupsOut ) - 1] ['idgroup'] = ( int ) $aGroupsOut [count ( $aGroupsOut ) - 1] ['idgroup'];
				}

				$this->addEntry ( 'response', array ('groups' => $aGroupsOut ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}

		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/group/spaces/$idgroup
	 */
	public function spaces($idgroup) {
		if ((( int ) $idgroup > 0 || (is_string ( $idgroup ) && strlen ( $idgroup ) > 3)) && $this->req ['auth_type'] == 'oauth') {

			if ($this->isReadable ( true )) {
				$oGroups = new Application_Model_Groups ( $this->oSettings );

				if (is_string ( $idgroup ) && ( int ) $idgroup == 0) {
					$oSelect = $oGroups->select ()->where ( 'accounts_idaccount=' . $this->usr . ' AND name="' . $idgroup . '"' );

					$oGroupRow = $oGroups->fetchRow ( $oSelect );

					if (is_object ( $oGroupRow )) {
						$idgroup = $oGroupRow->idgroup;
					} else {
						$idgroup = 0;
					}
				}

				$oSpaces = new Application_Model_Namespaces ( $this->oSettings );

				$bHasGroup = false;
				$aGroups = $oGroups->getMyGroups ( true )->toArray ();

				foreach ( $aGroups as $iRow => $aRow ) {
					if ($aRow ['idgroup'] == $idgroup) {
						$bHasGroup = true;
						break;
					}
				}

				if ($bHasGroup) {
					$aSpaces = $oGroups->getGroupSpaces ( $idgroup )->toArray ();
					$aSpacesOut = array ();
					$aNonRelevant = array ('groups_idgroup' );

					foreach ( $aSpaces as $key => $value ) {

						$oSpace = $oSpaces->fetchRow ( $oSpaces->select ()->where ( $oSpaces->getPrimary () . '=' . $value ['namespaces_idnamespace'] ) );

						$value ['idnamespace'] = ( int ) $value ['namespaces_idnamespace'];
						$value ['name'] = $oSpace->name;
						$value ['created'] = $oSpace->created;
						unset ( $value ['namespaces_idnamespace'] );

						array_push ( $aSpacesOut, $this->removeKeys ( $value, $aNonRelevant ) );
					}

					$this->addEntry ( 'response', array ('namespaces' => $aSpacesOut ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'GROUP_NOT_FOUND' ) );
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /my/group/members/$idgroup
	 */
	public function members($idgroup) {
		if ((( int ) $idgroup > 0 || (is_string ( $idgroup ) && strlen ( $idgroup ) > 3)) && $this->req ['auth_type'] == 'oauth') {

			if ($this->isReadable ( true )) {
				$oGroups = new Application_Model_Groups ( $this->oSettings );

				if (is_string ( $idgroup ) && ( int ) $idgroup == 0) {
					$oSelect = $oGroups->select ()->where ( 'accounts_idaccount=' . $this->usr . ' AND name="' . $idgroup . '"' );

					$oGroupRow = $oGroups->fetchRow ( $oSelect );

					if (is_object ( $oGroupRow )) {
						$idgroup = $oGroupRow->idgroup;
					} else {
						$idgroup = 0;
					}
				}

				$bHasGroup = false;
				$aGroups = $oGroups->getMyGroups ( true )->toArray ();

				foreach ( $aGroups as $iRow => $aRow ) {
					if ($aRow ['idgroup'] == $idgroup) {
						$bHasGroup = true;
						break;
					}
				}

				if ($bHasGroup) {

					$aMembers = $oGroups->getGroupMembers ( $idgroup )->toArray ();
					$aMembersOut = array ();
					$aNonRelevant = array ('groups_idgroup' );

					foreach ( $aMembers as $key => $value ) {

						$oSelect = $this->select ()->where ( $this->getPrimary () . '=' . $value ['accounts_idaccount'] );
						$oAccount = $this->fetchRow ( $oSelect );

						$value ['idaccount'] = ( int ) $value ['accounts_idaccount'];
						$value ['name'] = $oAccount->username;
						$value ['mail'] = $oAccount->mail;
						$value ['activatet'] = ($oAccount->activated == 1) ? true : false;

						unset ( $value ['accounts_idaccount'] );

						array_push ( $aMembersOut, $this->removeKeys ( $value, $aNonRelevant ) );
					}
					$this->addEntry ( 'response', array ('members' => $aMembersOut ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'GROUP_NOT_FOUND' ) );
				}
			}
		} else {
			return $this->oError->throwError ( array ('message' => 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}
}
?>
