<?
	class LCNGetKeyLocks extends IPSModule
	{

  	public function Destroy() 
  	{
  		//Never delete this line!
  		parent::Destroy();
  		$this->SetTimerInterval("Timer_1", 0);
  	}
		public function Create() {
			//Never delete this line!
			parent::Create();
			
			//$this->RegisterPropertyString("ReceiveFilter", ".*Hallo.*");
   		$this->RegisterPropertyInteger("ModulID", 22);
   		$this->RegisterPropertyInteger("Timer_1", 60);
		}
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			//Connect to available splitter or create a new one
			$this->ConnectParent("{6179ED6A-FC31-413C-BB8E-1204150CF376}");
			
			//Apply filter
			//$this->SetReceiveDataFilter($this->ReadPropertyString("ReceiveFilter"));
      IPS_LogMessage("IOTest", "Inititalisiere Filter M000".$this->ReadPropertyInteger("ModulID"));
			$this->SetReceiveDataFilter(".*=M000022\.TX[0-9]{12}.*");
			
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
      if (preg_match('/=(?<modul>M[0-9]{6})\.TX(?<A>[0-9]{3})(?<B>[0-9]{3})(?<C>[0-9]{3})(?<D>[0-9]{3})/',$line,$treffer)){
  			IPS_LogMessage("IOTest", $treffer['modul']." ".$treffer['A']." ".$treffer['B']." ".$treffer['C']." ".$treffer['D']);
        }
      }
		}
		
	
	}

?>
