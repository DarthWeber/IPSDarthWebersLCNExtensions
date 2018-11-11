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
			//$this->RequireParent("{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}");
   		$this->RegisterPropertyInteger("ModulID", 22);
      $this->RegisterPropertyInteger("Intervall", 0);
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
			$this->ConnectParent("{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}");
			
			//Apply filter
			//$this->SetReceiveDataFilter($this->ReadPropertyString("ReceiveFilter"));
      //IPS_LogMessage("IOTest", "Inititalisiere Filter M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")));
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
      @$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".TX\n"))));
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
