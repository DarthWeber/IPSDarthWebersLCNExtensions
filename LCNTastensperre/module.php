<?
	class LCNGetKeyLocks extends IPSModule
	{

  	public function Destroy() 
  	{
  		//Never delete this line!
  		parent::Destroy();
  	}
		public function Create() {
			//Never delete this line!
			parent::Create();
			
   		$this->RegisterPropertyInteger("ModulID", 22);
      $this->RegisterPropertyInteger("Intervall", 60);
      $this->RegisterTimer("SendTXCommand", 0, 'LCNGetKeyLocks_Update($_IPS[\'TARGET\']);');
      
      $this->RegisterVariableInteger("TastentabelleA", "Tastentabelle A");
      $this->RegisterVariableInteger("TastentabelleB", "Tastentabelle B");
      $this->RegisterVariableInteger("TastentabelleC", "Tastentabelle C");
      $this->RegisterVariableInteger("TastentabelleD", "Tastentabelle D");
      
   	}
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			//Connect to available splitter or create a new one
			$this->ConnectParent("{6179ED6A-FC31-413C-BB8E-1204150CF376}");
			
			//Apply filter
			//$this->SetReceiveDataFilter($this->ReadPropertyString("ReceiveFilter"));
      IPS_LogMessage("IOTest", "Inititalisiere Filter M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")));
			$this->SetReceiveDataFilter(".*=M".sprintf("%06d",$this->ReadPropertyInteger("ModulID"))."\.TX[0-9]{12}.*");
	
  		$this->Update();
	 		$this->SetTimerInterval("SendTXCommand", $this->ReadPropertyInteger("Intervall") * 1000);

		}

    public function Update()
    {
  		if (IPS_GetKernelRunlevel() !== 10103)
	   	{
		  	$this->SendDebug("FUNCTION -Update-", "Kernel is not ready! Kernel Runlevel = ".IPS_GetKernelRunlevel(), 0);
			  return false;
  		}
    IPS_LogMessage("IOTest", "Sende TX Command an ".($this->InstanceID)." mit >M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".TX");
    $this->SendDataToParent(json_encode(Array("DataID" => "{B87AC955-F258-468B-92FE-F4E0866A9E18}", "Buffer" => utf8_encode(">M000085.PIN001\n"))));
		}
		
		public function ReceiveData($JSONString)
		{
			//$data = json_decode($JSONString);
			$data = json_decode($JSONString);

			//Parse and write values to our buffer
			//$this->SetBuffer("Test", utf8_decode($data->Buffer));

			//Print buffer
			//IPS_LogMessage("IOTest", $this->GetBuffer("Test"));
      foreach(preg_split("/((\r?\n)|(\r\n?))/", utf8_decode($data->Buffer)) as $line){
      if (preg_match('/=(?<modul>M'.sprintf("%06d",$this->ReadPropertyInteger("ModulID")).')\.TX(?<A>[0-9]{3})(?<B>[0-9]{3})(?<C>[0-9]{3})(?<D>[0-9]{3})/',$line,$treffer)){
        $this->SetValueInteger("TastentabelleA", intval($treffer['A']));
        $this->SetValueInteger("TastentabelleB", intval($treffer['B']));
        $this->SetValueInteger("TastentabelleC", intval($treffer['C']));
        $this->SetValueInteger("TastentabelleD", intval($treffer['D']));
        }
      }
		}
    private function SetValueInteger($Ident, $value)
    {
        $id = $this->GetIDForIdent($Ident);
        if (GetValueInteger($id) <> $value)
        {
            SetValueInteger($id, $value);
            return true;
        }
        return false;
    }
	
  	private function SetValueFloat($Ident, $value)
      {
          $id = $this->GetIDForIdent($Ident);
          if (GetValueFloat($id) <> $value)
          {
              SetValueFloat($id, $value);
              return true;
          }
          return false;
      }
  	
  	private function SetValueString($Ident, $value)
      {
          $id = $this->GetIDForIdent($Ident);
          if (GetValueString($id) <> $value)
          {
              SetValueString($id, $value);
              return true;
          }
          return false;
      }

	}
?>
