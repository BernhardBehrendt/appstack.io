<?php
/**
 * Central categories model
 * This model provides an abstraction of a nested set tree with base tree functionalities
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Categories_Model
 * @version 0.0.2b
 *
 */
class Application_Model_Cat extends Application_Model_Table{
	//table name
	protected $_name     = "categories";

	//primary key
	protected $_primary  = "idcategory";

	protected $_foreign = 'namespaces_idnamespace';

	/**
	 * left column in
	 * @var unknown_type
	 */
	protected $_left     = "lft";

	/**
	 * right column in table
	 * @var unknown_type
	 */
	protected $_right    = "rgt";

	/**
	 * used when retrieving tree. if not set primary key will be used
	 * @var string
	 */
	protected $_toString = "name";

	protected $_sCreated = 'created';

	protected $_sModified = 'lastmodified';

	/**
	 * Additional data to be inserted.
	 *
	 * @var Array
	 */
	private  $_insertData = array();

	/**
	 * specifiy the owner of the tree currently working in
	 * @var int
	 *
	 */
	private $iNamespace;

	/**
	 * constructor
	 */
	public function __construct($iNamespace)
	{
		if(!$iNamespace){
			throw new Exception("No owner was set!");
		}else {
			$this->iNamespace = $iNamespace;
		}
		parent::__construct();
		if (!$this->_toString)
		{
			$this->_toString = $this->_primary[1];
		}
	}

	/**
	 * Additional data to be inserted
	 *
	 * @param array
	 * @access public
	 */
	public function setInsertData(array $data)
	{
		$this->_insertData = $data;
	}

	/**
	 * Retrieve whole tree
	 *
	 * @access public
	 * @return array
	 */
	public function getTree($bPublished=false)
	{
		//TODO Check why $_primary is going to be an array
		if(!is_array($this->_primary)){
			$sDepthColumn = $this->_primary;
		}else{
			$sDepthColumn = $this->_primary[1];
		}

		$sQuery = "SELECT COUNT( parent.{$sDepthColumn}) - 1 as depth, node.{$sDepthColumn} AS ident, node.{$this->_toString}, node.{$this->_sCreated} AS created, node.{$this->_sModified} AS modified, node.public AS pub, node.{$this->_left}, node.{$this->_right}
                   FROM  {$this->_name} AS node, {$this->_name} AS parent
                   WHERE (node.{$this->_left}
                   BETWEEN parent.{$this->_left}
                   AND parent.{$this->_right})
                   AND node.{$this->_foreign}={$this->iNamespace}
                   AND parent.{$this->_foreign}={$this->iNamespace}".
                   (($bPublished)?" AND node.public=1 ":' ')
                   ."GROUP BY node.{$sDepthColumn}
                   ORDER BY  {$this->_left}";
				   
				
		$ret = $this->_db->query($sQuery);

		return $ret->fetchAll();

	}

	/**
	 * New Function for a Subtree for given ID not testet yet
	 *
	 * NOTE
	 * Dont make use of this method as long deprecatet is set to this method
	 * There is a missing implementation for specify the owner of tree and shity result can be the call
	 *
	 * @deprecated
	 * @param unknown_type $id
	 */
	public function getSubTree($id, $iIdUser, $bPrivate=false)
	{

		$ret = $this->_db->query(	"SELECT node.{$this->_primary} AS ident, node.created AS created, node.rgt AS 'right', node.lft AS 'left', node.lastmodified AS modified, node.{$this->_toString},
									(
									COUNT(parent.{$this->_toString}) - (sub_tree.depth + 1)
									) AS depth
									FROM
									`{$this->_name}` AS node,
									`{$this->_name}` AS parent,
									`{$this->_name}` AS sub_parent,
									(
									SELECT node.{$this->_primary}, (COUNT(parent.{$this->_toString}) - 1) AS depth
									FROM `{$this->_name}` AS node, `{$this->_name}` AS parent
									WHERE node.{$this->_left} BETWEEN parent.{$this->_left} AND parent.{$this->_right}
									AND node.{$this->_primary} = $id
									GROUP BY node.{$this->_primary}
									ORDER BY node.{$this->_left}
									) AS sub_tree
									WHERE node.{$this->_left} BETWEEN parent.{$this->_left} AND parent.{$this->_right}
									AND node.{$this->_left} BETWEEN sub_parent.{$this->_left} AND sub_parent.{$this->_right}
									AND node.namespaces_idnamespace=".$this->iNamespace."
									".((!$bPrivate)?'AND node.public=1':' ')."
									AND sub_parent.{$this->_primary} = sub_tree.{$this->_primary}
									GROUP BY node.{$this->_primary}
									ORDER BY node.{$this->_left}
									");
		
		return $ret->fetchAll();
	}

	/**
	 * Insert node as first child
	 *
	 * @param int
	 * @access public
	 * @return int
	 */
	public function insertAsFirstChildOf($id, $sNodeName, $sAlias, $iAccountID)
	{
		// HERE I WORK
		$row = $this->retrieveData($id);

		$right = (int) $row->{$this->_right};
		$left  = (int) $row->{$this->_left};

		$this->_db->query("UPDATE {$this->_name} SET {$this->_right} = {$this->_right} + 2 WHERE ({$this->_right} > {$left}) AND {$this->_foreign} = {$this->iNamespace}");
		$this->_db->query("UPDATE {$this->_name} SET {$this->_left} = {$this->_left} + 2 WHERE ({$this->_left} > {$left}) AND {$this->_foreign} = {$this->iNamespace}");

		$data = array(
		$this->_left => $left + 1,
		$this->_right => $left + 2,
		$this->_toString => $sNodeName,
		'alias' => $sAlias,
		$this->_foreign => $this->iNamespace,
		'accounts_idaccount'=>$iAccountID
		);
		$this->_insertData = array_merge($this->_insertData, $data);

		$this->updateCategory($id);

		return $this->insert($this->_insertData);
	}

	/**
	 * Insert node as last child
	 *
	 * @param int
	 * @access public
	 * @return int
	 */
	public function insertAsLastChildOf($id, $sNodeName, $sAlias, $iAccountID)
	{
		$row = $this->retrieveData($id);

		$right = (int) $row->{$this->_right};
		$left  = (int) $row->{$this->_left};

		$this->_db->query("UPDATE {$this->_name} SET {$this->_right} = {$this->_right} + 2 WHERE {$this->_right} >= {$right} AND {$this->_foreign} = {$this->iNamespace}");
		$this->_db->query("UPDATE {$this->_name} SET {$this->_left} = {$this->_left} + 2 WHERE {$this->_left} > {$right} AND {$this->_foreign} = {$this->iNamespace}");


		$data = array(
		$this->_left => $right,
		$this->_right => $right + 1,
		$this->_toString => $sNodeName,
		'alias' => $sAlias,
		$this->_foreign => $this->iNamespace,
		'accounts_idaccount'=>$iAccountID
		);
		$this->_insertData = array_merge($this->_insertData, $data);

		$this->updateCategory($id);

		return $this->insert($this->_insertData);

	}

	/**
	 * Insert node as next sibling of given node
	 *
	 * @param int
	 * @access public
	 * @return int
	 * @throws Exception
	 */
	public function insertAsNextSiblingOf($id, $sNodename, $sAlias, $iAccountID)
	{
		$row = $this->retrieveData($id);
		$right = (int) $row->{$this->_right};
		$left  = (int) $row->{$this->_left};

		if ($left === 1) {
			throw new Exception("Root node can't have siblings");
		}

		$this->_db->query("UPDATE {$this->_name} SET {$this->_right} = {$this->_right} + 2 WHERE ({$this->_right} > {$right}) AND {$this->_foreign} = {$this->iNamespace}");
		$this->_db->query("UPDATE {$this->_name} SET {$this->_left} = {$this->_left} + 2 WHERE ({$this->_left} > {$right}) AND {$this->_foreign} = {$this->iNamespace}");


		$data = array(
		$this->_left => $right+1,
		$this->_right => $right + 2,
		$this->_toString => $sNodename,
		'alias' => $sAlias,
		$this->_foreign => $this->iNamespace,
		'accounts_idaccount'=>$iAccountID
		);

		$this->_insertData = array_merge($this->_insertData, $data);

		$this->updateCategory($id);

		return $this->insert($this->_insertData);

	}

	/**
	 * Insert node as prev sibling of given node
	 *
	 * @param int
	 * @access public
	 * @return int
	 * @throws Exception
	 */
	public function insertAsPrevSiblingOf($id, $sNodeName, $sAlias, $iAccountID)
	{
		$row = $this->retrieveData($id);
		$right = (int) $row->{$this->_right};
		$left  = (int) $row->{$this->_left};

		if ($left === 1) {
			throw new Exception("Root node can't have siblings");
		}


		$this->_db->query("UPDATE {$this->_name} SET {$this->_right} = {$this->_right} + 2 WHERE ({$this->_right} > {$left}) AND {$this->_foreign} = {$this->iNamespace}");
		$this->_db->query("UPDATE {$this->_name} SET {$this->_left} = {$this->_left} + 2 WHERE ({$this->_left} >= {$left}) AND {$this->_foreign} = {$this->iNamespace}");


		$data = array(
		$this->_left => $left,
		$this->_right => $left + 1,
		$this->_toString => $sNodeName,
		'alias' => $sAlias,
		$this->_foreign => $this->iNamespace,
		'accounts_idaccount'=>$iAccountID
		);

		$this->_insertData = array_merge($this->_insertData, $data);

		$this->updateCategory($id);

		return $this->insert($this->_insertData);
	}

	/**
	 * Delete node with it's child(s) and return affected rows
	 *
	 * @param int
	 * @access public
	 * @return int
	 */
	public function deleteNode($id)
	{
		$row = $this->retrieveData($id);

		if(is_object($row)){
			$right = (int) $row->{$this->_right};
			$left  = (int) $row->{$this->_left};
			if($right-1==$left){
				$width = $right - $left + 1;
				$res = $this->_db->query("DELETE FROM {$this->_name} WHERE ({$this->_left} BETWEEN {$left} AND {$right}) AND {$this->_foreign} = {$this->iNamespace}");

				$this->_db->query("UPDATE {$this->_name} SET {$this->_right} = {$this->_right} - {$width} WHERE ({$this->_right} > {$right}) AND {$this->_foreign} = {$this->iNamespace}");
				$this->_db->query("UPDATE {$this->_name} SET {$this->_left} = {$this->_left} - {$width} WHERE ({$this->_left} > {$right}) AND {$this->_foreign} = {$this->iNamespace}");

				return $res->rowCount();
			}else{
				return 0;
			}
		}else{
			return false;
		}
	}

	/**
	 * Deliver currrent root id
	 */
	public function getRoot(){

		$select = $this->select()->where($this->_left .' = 1 AND '.$this->_foreign.'='.$this->iNamespace);

		$oRow = $this->fetchRow($select);
		if(!is_object($oRow)){
			return false;
		}

		return  $oRow->{$this->_primary[1]};
	}

	/**
	 * Insert root node
	 *
	 * @access public
	 * @return int
	 */
	public function createRoot()
	{
		$biRoot = $this->getRoot($this->iNamespace);

		if(!$biRoot){
			$data = array(
			$this->_left => 1,
			$this->_right => 2,
			$this->_toString => '/',
			$this->_foreign => $this->iNamespace
			);
			$this->_insertData = array_merge($this->_insertData, $data);
			return $this->insert($this->_insertData);
		}else{
			return $biRoot;
		}
	}

	public function isInSpace($iIdCat){
		return (count($this->retrieveData($iIdCat))==1)?true:false;
	}
	/**
	 * Insert node
	 *
	 * @param int
	 * @access private
	 * @return Zend_Db_Row
	 */
	private function retrieveData($id)
	{
		$select = $this->select()->where($this->_primary[1] .' = '. $id . ' AND '.$this->_foreign .' = '. $this->iNamespace);
		return $this->fetchRow($select);
	}

	private function updateCategory($iIdCategory){
		if(is_array($this->_primary)){
			$sPrimary = $this->_primary[1];
		}else{
			$sPrimary = $this->_primary;
		}
		$this->_db->query("UPDATE {$this->_name} SET {$this->_sModified} = CURRENT_TIMESTAMP WHERE {$sPrimary} = {$iIdCategory} AND {$this->_foreign} = {$this->iNamespace}");

	}

	public function renameCategory($iIdCategory, $sNewName, $sAlias){
		if(is_array($this->_primary)){
			$sPrimary = $this->_primary[1];
		}else{
			$sPrimary = $this->_primary;
		}

		if(strlen($sNewName)>0){

			$this->_db->query("UPDATE {$this->_name} SET {$this->_toString} = '{$sNewName}', alias ='{$sAlias}' WHERE {$sPrimary} = {$iIdCategory} AND {$this->_foreign} = {$this->iNamespace}");
			$this->updateCategory($iIdCategory);

			return true;
		}
		return false;
	}
	public function statusCategory($iIdCategory, $iStatus){
		if(is_array($this->_primary)){
			$sPrimary = $this->_primary[1];
		}else{
			$sPrimary = $this->_primary;
		}

		if(strlen($iStatus)==1){

			$this->_db->query("UPDATE {$this->_name} SET public = '{$iStatus}' WHERE {$sPrimary} = {$iIdCategory} AND {$this->_foreign} = {$this->iNamespace}");
			$this->updateCategory($iIdCategory);

			return true;
		}
		return false;
	}
}
?>