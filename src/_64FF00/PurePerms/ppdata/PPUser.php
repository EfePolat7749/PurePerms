<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

use pocketmine\IPlayer;

class PPUser implements PPDataInterface
{	
	public function __construct(PurePerms $plugin, IPlayer $player)
	{
		$this->player = $player;
		$this->plugin = $plugin;
	}
	
	public function getData()
	{
		return $this->plugin->getProvider()->getUserData($this, true);
	}
	
	public function getGroup($levelName = null)
	{
		if($levelName == null)
		{
			$groupName = $this->getNode("group");
		}
		else
		{
			$groupName = $this->getWorldData($levelName)["group"];
		}
		
		if(isset($groupName))
		{
			return $this->plugin->getGroup($groupName);
		}
	}
	
	public function getGroupPermissions($levelName = null)
	{
		return $this->getGroup($levelName)->getPermissions($levelName);
	}
	
	public function getNode($node)
	{
		if(!isset($this->getData()[$node]))
		{
			return null;
		}
		
		return $this->getData()[$node];
	}
	
	public function getPermissions($levelName = null)
	{
		$permissions = array_merge($this->getGroupPermissions($levelName), $this->getUserPermissions($levelName));
		
		return $permissions;
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getUserPermissions($levelName = null)
	{
		if($levelName == null)
		{
			return $this->getNode("permissions");
		}
		
		return $this->getWorldData($levelName)["permissions"];
	}
	
	public function getWorldData($levelName)
	{
		if($levelName == null) return null;
			
		if(!isset($this->getData()["worlds"][$levelName]))
		{
			$tempUserData = $this->getData();
			
			$tempUserData["worlds"][$levelName] = array(
				"group" => $this->plugin->getDefaultGroup()->getName(),
				"permissions" => array(
				)
			);
				
			$this->setData($tempUserData);
		}
			
		return $this->getData()["worlds"][$levelName];
	}
	
	public function removeNode($node)
	{
		$tempUserData = $this->getData();
		
		if(isset($tempUserData[$node]))
		{				
			unset($tempUserData[$node]);
			
			$this->setData($tempUserData);
		}
	}
	
	public function setData(array $data)
	{
		$this->plugin->getProvider()->setUserData($this, $data);
	}
	
	public function setGroup(PPGroup $group, $levelName)
	{
		if($levelName == null)
		{
			$this->setNode("group", $group->getName());
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			$worldData["group"] = $group->getName();
			
			$this->setWorldData($levelName, $worldData);
		}
	}
	
	public function setNode($node, $value)
	{
		$tempUserData = $this->getData();
					
		$tempUserData[$node] = $value;
			
		$this->setData($tempUserData);
	}
	
	public function setWorldData($levelName, array $worldData)
	{
		if(isset($this->getData()["worlds"][$levelName]))
		{
			$tempUserData = $this->getData();
			
			$tempUserData["worlds"][$levelName] = $worldData;
				
			$this->setData($tempUserData);
		}
	}
}