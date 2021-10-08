<?php

namespace db;

use PDO;
use Exception;

final class hpdo {

	/**
	* SINGLETON INITIALIZATION
	*/
	
	private static $DB_HOST = '';
	private static $DB_NAME = '';
	private static $DB_USER = '';
	private static $DB_PASS = '';

	protected static $_instance;
	protected static $_handler;
	protected static $_state;
	protected static $_message;

	private function __construct() {
		if(self::$DB_HOST == "" || self::$DB_NAME == "" || self::$DB_USER == ""){
			throw new Exception("Malformed DB information", 1);
		}
		try {
		self::$_handler = new PDO(
			'mysql:host=' . self::$DB_HOST . ';dbname=' . self::$DB_NAME,
			self::$DB_USER,
			self::$DB_PASS,
			[
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"
			]
		);
		} catch(Exception $e) {
			//\io::err("Internal server error", 500);
			echo 'Exception -> ';
			var_dump($e->getMessage());
		}
	}

	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new self;  
		}
		return self::$_instance;
	}

	public static function getHandler(){
		return self::$_handler;
	}

	public static function getHost(){
		return self::$DB_HOST;
	}

	public static function setHost($host){
		return self::$DB_HOST = $host;
	}

	public static function getName(){
		return self::$DB_NAME;
	}

	public static function setName($name){
		return self::$DB_NAME = $name;
	}

	public static function getUser(){
		return self::$DB_USER;
	}

	public static function setUser($user){
		return self::$DB_USER = $user;
	}

	public static function getPassword(){
		return self::$DB_PASS;
	}

	public static function setPassword($password){
		return self::$DB_PASS = $password;
	}

	public static function getDB(){
		return self::$_handler;
	}

	private function __clone() {}

	private function __wakeup() {}

	public function count($table){
		$db = self::getDB();
		$sth = $db->prepare("SELECT COUNT(*) FROM `$table`");
		$check = $sth->execute();
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][0] : null;
	}

	public function gfbi($dbn, $index, $indexvalue, $field){
		$db = self::getDB();
		$sth = $db->prepare("SELECT `{$field}` FROM `{$dbn}` WHERE `{$index}` = ?");
		$check = $sth->execute([$indexvalue]);
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][0] : null;
	}

	public function gsbi($dbn, $index, $indexvalue){
		$db = self::getDB();
		$sth = $db->prepare("SELECT * FROM `{$dbn}` WHERE `{$index}` = ?");
		$check = $sth->execute([$indexvalue]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query[0] : [];
	}

	public function dsbi($dbn, $index, $indexvalue){
		$db = self::getDB();
		$sth = $db->prepare("DELETE FROM `{$dbn}` WHERE `{$index}` = ?");
		$check = $sth->execute([$indexvalue]);
		return ($check == true) ? true : null;
	}

	public function gssbi($dbn, $index, $indexvalue, $addition = ""){
		$db = self::getDB();
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` = ? {$addition}";
		$sth = $db->prepare("SELECT * FROM `{$dbn}` WHERE `{$index}` = ? {$addition}");
		$check = $sth->execute([$indexvalue]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbli($dbn, $index, $indexvalue, $addition = ""){
		$db = self::getDB();
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` LIKE :likestr {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute([":likestr" => '%'.$indexvalue.'%']);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbliocod($dbn, $index, $indexvalue, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` LIKE :likestr {$addition} "
		."ORDER BY `{$colomn}` DESC LIMIT $count OFFSET $offset";
		$sth = $db->prepare($str);
		$check = $sth->execute([":likestr" => '%'.$indexvalue.'%']);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssoc($dbn, $offset, $count, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute();
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbiocod($dbn, $index, $indexvalue, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` = ? ORDER BY `{$colomn}` DESC LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute([$indexvalue]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbiocoa($dbn, $index, $indexvalue, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` = ? ORDER BY `{$colomn}` ASC LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute([$indexvalue]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbisocod($dbn, $index, $indexvalue, $index2, $indexvalue2, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` = ? AND `{$index2}` = ? ORDER BY `{$colomn}` DESC LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute([$indexvalue, $indexvalue2]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbisocoa($dbn, $index, $indexvalue, $index2, $indexvalue2, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` WHERE `{$index}` = ? AND `{$index2}` = ? ORDER BY `{$colomn}` ASC LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute([$indexvalue, $indexvalue2]);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssocod($dbn, $offset, $count, $colomn, $addition = ""){
		$db = self::getDB();
		$offset = (int) $offset;
		$count = (int) $count;
		$str = "SELECT * FROM `{$dbn}` ORDER BY `{$colomn}` DESC LIMIT $count OFFSET $offset {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute();
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbis($dbn, $indexes, $values, $addition = ""){
		$db = self::getDB();
		if(count($indexes) !== count($values)){
			return null;
		}
		$prepare = [];
		foreach ($indexes as $key => $value) {
			$prepare[] = "`{$value}` = ?";
		}
		$prepare = implode(" AND ", $prepare);
		$str = "SELECT * FROM `{$dbn}` WHERE {$prepare} {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute($values);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function gssbois($dbn, $indexes, $values, $compares, $addition = "", $condition = "AND"){
		$db = self::getDB();
		if(count($indexes) !== count($values)){
			return null;
		}
		$prepare = [];
		foreach ($indexes as $key => $value) {
			$prepare[] = "`{$value}` {$compares[$key]} ?";
		}
		$prepare = "(".implode(" {$condition} ", $prepare).")";
		$str = "SELECT * FROM `{$dbn}` WHERE {$prepare} {$addition}";
		$sth = $db->prepare($str);
		$check = $sth->execute($values);
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function all($dbn, $addition = ""){
		$db = self::getDB();
		$sth = $db->prepare("SELECT * FROM `{$dbn}` {$addition}");
		$check = $sth->execute();
		$query = $sth->fetchAll(PDO::FETCH_ASSOC);
		return (count($query) > 0) ? $query : [];
	}

	public function truncate($dbn){
		$db = self::getDB();
		$sth = $db->prepare("TRUNCATE TABLE `{$dbn}`");
		$check = $sth->execute();
		return ($check == true) ? true : null;
	}

	public function sf($dbn, $field, $fieldvalue){
		$db = self::getDB();
		$sth = $db->prepare("UPDATE `{$dbn}` SET `{$field}` = ?");
		$check = $sth->execute([$fieldvalue]);
		return ($check == true) ? true : null;
	}

	public function sfbi($dbn, $index, $indexvalue, $field, $fieldvalue){
		$db = self::getDB();
		$str = "UPDATE `{$dbn}` SET `{$field}` = ? WHERE `{$index}` = ?";
		$sth = $db->prepare($str);
		$check = $sth->execute([$fieldvalue, $indexvalue]);
		return ($check == true) ? true : null;
	}

	public function scbn($dbn, $colomn, $colomnvalue){
		$db = self::getDB();
		$sth = $db->prepare("UPDATE `{$dbn}` SET `{$colomn}` = ?");
		$check = $sth->execute([$colomnvalue]);
		return ($check == true) ? true : null;
	}

	public function summ($dbn, $index, $indexvalue, $field, $delta){
		$current = $this->gfbi($dbn, $index, $indexvalue, $field);
		$newSumm = $delta + $current;
		$check = $this->sfbi($dbn, $index, $indexvalue, $field, $newSumm);
		return ($check == true) ? true : null;
	}

	public function integral($dbn, $colomn, $addition = ""){
		$db = self::getDB();
		$sth = $db->prepare("SELECT SUM(`{$colomn}`) FROM `{$dbn}` {$addition}");
		$check = $sth->execute();
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][0] : null;
	}

	public function insert($dbn, $insertNames, $insertValues){
		$db = self::getDB();
		if(count($insertNames) !== count($insertValues)){
			return null;
		}
		foreach ($insertNames as $key => $value) {
			$insertNames[$key] = "`{$value}`";
		}
		$n = count($insertValues);
		$insertNamesPseudo = [];
		for ($i=0; $i < $n; $i++) { 
			$insertNamesPseudo[] = "?";
		}
		$insertNamesPseudo = implode(",", $insertNamesPseudo);
		$insertNames = implode(", ", $insertNames);
		$str = "INSERT INTO `{$dbn}` ({$insertNames}) VALUES ({$insertNamesPseudo})";
		$sth = $db->prepare($str);
		$check = $sth->execute($insertValues);
		return ($check == true) ? true : null;
	}
	public function maxcol($table, $colomn){
		$db = self::getDB();
		$querystr = "SELECT MAX(`{$colomn}`) as `max` FROM `{$table}` LIMIT 0,1";
		$sth = $db->prepare($querystr);
		$check = $sth->execute();
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][0] : null;
	}
	public function mincol($table, $colomn){
		$db = self::getDB();
		$querystr = "SELECT MIN(`{$colomn}`) as `min` FROM `{$table}` LIMIT 0,1";
		$sth = $db->prepare($querystr);
		$check = $sth->execute();
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][0] : null;
	}
	public function uptime(){
		$db = self::getDB();
		$querystr = "SHOW GLOBAL STATUS LIKE 'Uptime'";
		$sth = $db->prepare($querystr);
		$check = $sth->execute();
		$query = $sth->fetchAll();
		return (count($query) > 0) ? $query[0][1] : null;
	}

}

?>
