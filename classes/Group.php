<?php
class Group {
	public static function create($name, $permissions = array()){
		//Creates a permissions group
		if(strlen($name) > 0){
			$insert = DB::getInstance()->table("groups")->insert(array(
				'name' => $name,
				'permissions' => json_encode($permissions)
			));
			if($insert){
				return true;	
			}
		}
		return false;
	}
	
	public static function delete($id){
		//Deletes a permissions group
		return DB::getInstance()->table("groups")->where('id', $id)->delete();
	}
	
	public static function setPermissions($id, $permissions = array()){
		//Removes all existing permissions and sets the permissions to ones speicified in the parameters
		return DB::getInstance()->table("groups")->where('id', $id)->update(array("permissions"=>json_encode($permissions)));
	}
	
	public static function clearPermissions($id){
		//clears all permissions from a particular group
		return self::setPermissions($id, array());		
	}
	
	public function getPermissions($id){
		//returns a certain groups permissions as an array
		$Data = DB::getInstance()->table('groups')->where('id', $id)->get(1);
		if($Data){
			return json_decode($Data[0]->permissions, true);
		}
		return false;
	}
	
	public static function removePermissions($id, $permissions = array()){
		//Removes permissions stated in the parameter while leaving ones that are not stated in the parameter
		//$remove = Group::removePermissions(2, array(
		//	'admin'
		//));
		$current = self::getPermissions($id);
		foreach($permissions as $permission){
			if(isset($current[$permission])){
				unset($current[$permission]);
			}
		}
		return self::setPermissions($id,$current); 
	}
	
	public static function addPermissions($id, $permissions = array()){
		//Adds permissions listed in the parameter to the existing list of permissions
		//$add = Group::addPermissions(2, array(
		//	'admin'
		//));
		$current = self::getPermissions($id);
		foreach($permissions as $permission => $value){
			if(!isset($current[$permission])){
				$current[$permission] = $value;
			}
		}
		return self::setPermissions($id,$current); 
	}
}
?>