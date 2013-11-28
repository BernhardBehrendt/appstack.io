<?php

require_once 'abstract.proxy.php';
class cats extends Proxy {
	public function __construct() {
		parent::__construct ( __CLASS__ );
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /all/
	 */
	public function all() {
		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$oCategories = new Application_Model_Cat ( $this->req ['request'] ['ns'] );
				if (is_object ( $oCategories )) {
					$aTree = $oCategories->getTree ( true );
					$aTree = $this->renderTree ( $aTree );
					$this->addEntry ( 'response', array ('categories' => $aTree ) );
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /sub/$idSub
	 */
	public function sub($idSub) {
		$oGroups = new Application_Model_Groups ( $this->oSettings );
		$aAccessible = $oGroups->accountGroups ();
		if (isset ( $aAccessible ['MEMBER'] ) && isset ( $aAccessible ['OWNER'] )) {
			$aAccessible = array_merge ( $aAccessible ['MEMBER'], $aAccessible ['OWNER'] );
		} elseif (isset ( $aAccessible ['MEMBER'] )) {
			$aAccessible = $aAccessible ['MEMBER'];
		} elseif (isset ( $aAccessible ['OWNER'] )) {
			$aAccessible = $aAccessible ['OWNER'];
		}

		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$oCategories = new Application_Model_Cat ( $this->req ['request'] ['ns'] );

				if (is_object ( $oCategories )) {
					if (( int ) $idSub > 0) {
						$bOffline = false;
						if ($this->req ['auth_type'] == 'oauth') {
							$bOffline = true;
						}
						$aTree = $oCategories->getSubTree ( $idSub, $this->usr, $bOffline );
						foreach ( $aTree as $iKey => $aCategory ) {
							$aTree [$iKey] = $this->removeKeys ( $aTree [$iKey], array ('depth' ) );
						}
						$bLock = false;
						$iNext = 0;
						foreach ( $aTree as $iKey => $aCat ) {

							if ($aCat ['ident'] == $idSub) {
								unset ( $aTree [$iKey] );
								continue;
							}

							if ($aCat ['left'] + 1 == $aCat ['right'] && ! $bLock || $iNext == $aCat ['right'] && $bLock) {
								$bLock = false;
								continue;
							} else {
								if (! $bLock) {
									$bLock = true;
									$iNext = $aCat ['right'] + 1;
								} else {
									unset ( $aTree [$iKey] );
								}
							}
						}

						$this->addEntry ( 'response', array ('categories' => $aTree ) );
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'CATEGORY_IDENT_NOT_INTEGER' ) );
					}
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /parent/$idChild
	 */
	public function par($idChild) {
		$oGroups = new Application_Model_Groups ( $this->oSettings );
		$aAccessible = $oGroups->accountGroups ();

		if (isset ( $aAccessible ['MEMBER'] ) && isset ( $aAccessible ['OWNER'] )) {
			$aAccessible = array_merge ( $aAccessible ['MEMBER'], $aAccessible ['OWNER'] );
		} elseif (isset ( $aAccessible ['MEMBER'] )) {
			$aAccessible = $aAccessible ['MEMBER'];
		} elseif (isset ( $aAccessible ['OWNER'] )) {
			$aAccessible = $aAccessible ['OWNER'];
		}

		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$oCategories = new Application_Model_Cat ( $this->req ['request'] ['ns'] );

				if (is_object ( $oCategories )) {
					if (( int ) $idChild > 0) {
						$bOffline = false;
						if ($this->req ['auth_type'] == 'oauth') {
							$bOffline = true;
						}
						$aTreeAll = $this->renderTree ( $oCategories->getTree ( true ) );
						$this->addEntry ( 'response', array ('categories' => $aTreeAll ) );
						// LOOK FOR iDPARENT
						// HOLD ALL TIMES LEVEL BEFORE
						// IF FOUND RETURN LEVEL BEFORE

						$this->getUpperLevel ( $aTreeAll, $idChild );

						$this->addEntry ( 'response', array ('categories' => $this->getUpperLevel ( $aTreeAll, $idChild ) ) );
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'CATEGORY_IDENT_NOT_INTEGER' ) );
					}
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}
	private function getUpperLevel($aTreeAll, $iIdChild) {
	}

	/**
	 * Internal required but not accessible from outside
	 */
	private function renderTree(&$arrs, $depth_key = 'depth') {
		$nested = array ();
		$depths = array ();

		foreach ( $arrs as $key => $arr ) {
			if ($arr [$depth_key] == 0) {
				$nested [$key] = $arr;
				$depths [$arr [$depth_key] + 1] = $key;
			} else {
				$parent = &$nested;
				for($i = 1; $i <= ($arr [$depth_key]); $i ++) {
					if (isset ( $depths [$i] )) {
						unset ( $parent [$depths [$i]] ['depth'], $parent [$depths [$i]] ['pub'], $parent [$depths [$i]] ['lft'], $parent [$depths [$i]] ['rgt'] );
						$parent = &$parent [$depths [$i]];
					}
				}

				unset ( $arr ['pub'], $arr ['lft'], $arr ['rgt'] );
				$parent [$key] = $arr;
				$depths [$arr [$depth_key] + 1] = $key;
				unset ( $parent [$key] ['depth'] );
			}
		}

		return $nested;
	}
}
?>