<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerOutputControl extends Object {

	public function getComposerOutputControlID() {return $this->cmpOutputControlID;}
	public function getComposerOutputControlDisplayOrderID() {return $this->cmpOutputControlDisplayOrder;}
	public function getComposerFormLayoutSetControlID() {return $this->cmpFormLayoutSetControlID;}
	public function getComposerFormLayoutSetID() {return $this->cmpFormLayoutSetID;}

	public static function add(ComposerFormLayoutSetControl $control, $arHandle) {

		$set = $control->getComposerFormLayoutSetObject();
		$composer = $set->getComposerObject();

		$db = Loader::db();
		$displayOrder = $db->GetOne('select count(cmpOutputControlID) from ComposerOutputControls where cmpID = ? and arHandle = ?', array($composer->getComposerID(), $arHandle));
		if (!$displayOrder) {
			$displayOrder = 0;
		}

		$db->Execute('insert into ComposerOutputControls (arHandle, cmpID, cmpFormLayoutSetControlID, cmpOutputControlDisplayOrder) values (?, ?, ?, ?)', array(
			$arHandle, $composer->getComposerID(), $control->getComposerFormLayoutSetControlID(), $displayOrder
		));
		$cmpOutputControlID = $db->Insert_ID();
		return ComposerOutputControl::getByID($cmpOutputControlID);
	}

	public static function getList(Composer $composer, $arHandle) {
		$db = Loader::db();
		$cmpOutputControlIDs = $db->GetCol('select cmpOutputControlID from ComposerOutputControls where cmpID = ? and arHandle = ? order by cmpOutputControlDisplayOrder asc', array(
			$composer->getComposerID(), $arHandle
		));
		$list = array();
		foreach($cmpOutputControlIDs as $cmpOutputControlID) {
			$cm = ComposerOutputControl::getByID($cmpOutputControlID);
			if (is_object($cm)) {
				$list[] = $cm;
			}
		}
		return $list;
	}

	public static function getByID($cmpOutputControlID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from ComposerOutputControls where cmpOutputControlID = ?', array($cmpOutputControlID));
		if (is_array($r) && $r['cmpOutputControlID']) {
			$cm = new ComposerOutputControl;
			$cm->setPropertiesFromArray($r);
			return $cm;
		}
	}

	public function updateComposerOutputControlArea($arHandle) {
		$db = Loader::db();
		$db->Execute('update ComposerOutputControls set arHandle = ? where cmpOutputControlID = ?', array(
			$arHandle, $this->cmpOutputControlID
		));
		$this->arHandle = $arHandle;
	}

	public function updateComposerOutputControlDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update ComposerOutputControls set cmpOutputControlDisplayOrder = ? where cmpOutputControlID = ?', array(
			$displayOrder, $this->cmpOutputControlID
		));
		$this->displayOrder = $displayOrder;
	}

	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from ComposerOutputControls where cmpOutputControlID = ?', array($this->cmpOutputControlID));
	}

	public function getComposerControlOutputLabel() {
		$control = ComposerFormLayoutSetControl::getByID($this->cmpFormLayoutSetControlID);
		return $control->getComposerControlLabel();
	}

	/*
	public function rescanFormLayoutSetDisplayOrder() {
		$sets = ComposerFormLayoutSet::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}

	*/
	
}