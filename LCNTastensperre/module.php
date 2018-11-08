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
			
			//$this->RegisterPropertyString("ReceiveFilter", ".*Hallo.*");
   		$this->RegisterPropertyInteger("ModulID", 22);
      $this->RegisterPropertyInteger("Intervall", 60);
      $this->RegisterTimer("SendTXCommand", 0, 'LCNGetKeyLocks_Update($_IPS[\'TARGET\']);');
      
      $this->RegisterVariableInteger("TastentabelleA", "Tastentabelle A");
      
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
    IPS_LogMessage("IOTest", "Sende TX Command");
    $this->SetValueInteger("Tastentabelle A", 99);
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
  			IPS_LogMessage("IOTest", $treffer['modul']." ".$treffer['A']." ".$treffer['B']." ".$treffer['C']." ".$treffer['D']);
        $this->SetValueInteger("Tastentabelle A", intval($treffer['A']));
        }
      }
		}
	}
?>
