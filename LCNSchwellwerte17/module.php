<?
	class LCNGetThresholds extends IPSModule
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
      $this->RegisterPropertyInteger("Intervall", 0);
      $this->RegisterTimer("SendSECommand", 0, 'LCNGetThresholds_Update($_IPS[\'TARGET\']);');
      
      $this->RegisterVariableInteger("Reg1Thres1", "Register 1 Schwellwert 1");
      $this->RegisterVariableInteger("Reg1Thres2", "Register 1 Schwellwert 2");
      $this->RegisterVariableInteger("Reg1Thres3", "Register 1 Schwellwert 3");
      $this->RegisterVariableInteger("Reg1Thres4", "Register 1 Schwellwert 4");
      $this->RegisterVariableInteger("Reg2Thres1", "Register 2 Schwellwert 1");
      $this->RegisterVariableInteger("Reg2Thres2", "Register 2 Schwellwert 2");
      $this->RegisterVariableInteger("Reg2Thres3", "Register 2 Schwellwert 3");
      $this->RegisterVariableInteger("Reg2Thres4", "Register 2 Schwellwert 4");
      $this->RegisterVariableInteger("Reg3Thres1", "Register 3 Schwellwert 1");
      $this->RegisterVariableInteger("Reg3Thres2", "Register 3 Schwellwert 2");
      $this->RegisterVariableInteger("Reg3Thres3", "Register 3 Schwellwert 3");
      $this->RegisterVariableInteger("Reg3Thres4", "Register 3 Schwellwert 4");
      $this->RegisterVariableInteger("Reg4Thres1", "Register 4 Schwellwert 1");
      $this->RegisterVariableInteger("Reg4Thres2", "Register 4 Schwellwert 2");
      $this->RegisterVariableInteger("Reg4Thres3", "Register 4 Schwellwert 3");
      $this->RegisterVariableInteger("Reg4Thres4", "Register 4 Schwellwert 4");
      
   	}
		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
			
			//Connect to available splitter or create a new one
			$this->ConnectParent("{6179ED6A-FC31-413C-BB8E-1204150CF376}");
			
			//Apply filter
			//$this->SetReceiveDataFilter($this->ReadPropertyString("ReceiveFilter"));
      //IPS_LogMessage("IOTest", "Inititalisiere Filter M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")));
			$this->SetReceiveDataFilter(".*=M".sprintf("%06d",$this->ReadPropertyInteger("ModulID"))."\.T[0-9]{7}.*");
	
  		$this->Update();
	 		$this->SetTimerInterval("SendSECommand", $this->ReadPropertyInteger("Intervall") * 1000);

		}

    public function Update()
    {
  		if (IPS_GetKernelRunlevel() !== 10103)
	   	{
		  	$this->SendDebug("FUNCTION -Update-", "Kernel is not ready! Kernel Runlevel = ".IPS_GetKernelRunlevel(), 0);
			  return false;
  		}
      @$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".SE1\n"))));
      @$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".SE2\n"))));
      @$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".SE3\n"))));
      @$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".SE4\n"))));
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
      if (preg_match('/=(?<modul>M'.sprintf("%06d",$this->ReadPropertyInteger("ModulID")).')\.T(?<reg>[1-4])(?<nr>[0-9]{1})(?<wert>[0-9]{5}))/',$line,$treffer)){
        $this->SetValueInteger("Reg".$treffer['reg']."Thres".$treffer['nr'], intval($treffer['wert']));
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
