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
      
      $this->ConnectParent("{9BDFC391-DEFF-4B71-A76B-604DBA80F207}");
   	}

		public function ApplyChanges()
		{
			parent::ApplyChanges();
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }
      $Filter = '.*"Message":2,"Segment":' .
              "0" .
              ',"Target":' .
              $this->ReadPropertyInteger('ModulID') .
              ',"Function":"TX".*';
      $this->SendDebug('FILTER', $Filter, 0);
      $this->SetReceiveDataFilter($Filter);
	 		$this->SetTimerInterval("SendSECommand", $this->ReadPropertyInteger("Intervall") * 1000);
		}

    protected function KernelReady()
    {
        $this->ApplyChanges();
    }

   public function SendTest(string $Function, string $Data)
    {
        $SendData = [
            'DataID'   => '{C5755489-1880-4968-9894-F8028FE1020A}',
            'Address'  => 0, // 0 => M, 1 => G
            'Segment'  => 0,
            'Target'   => $this->ReadPropertyInteger('ModulID'),
            'Function' => $Function,
            'Data'     => $Data
        ];
        $this->SendDebug('Send', json_encode($SendData), 0);
        $Result = $this->SendDataToParent(json_encode($SendData));
        $this->SendDebug('Result', json_decode($Result), 0);
    }

    public function Update()
    {
  		if (IPS_GetKernelRunlevel() !== 10103)
	   	{
		  	$this->SendDebug("FUNCTION -Update-", "Kernel is not ready! Kernel Runlevel = ".IPS_GetKernelRunlevel(), 0);
			  return false;
  		}
      $this->SendDebug('SE',"Sende SE Kommandos...", 0);
      $this->SendTest("SE1","");
      $this->SendTest("SE2","");
      $this->SendTest("SE3","");
      $this->SendTest("SE4","");
		}
		
    public function ReceiveData($JSONString)
    {
        $this->SendDebug('Receive', $JSONString, 0);
        if (preg_match('/(?<reg>[1-4])(?<nr>[0-9]{1})(?<wert>[0-9]{5})/',json_decode($JSONString)->Data,$treffer)){
          SetValueInteger($this->GetIDForIdent("Reg".$treffer['reg']."Thres".$treffer['nr']), intval($treffer['wert']));
        }
    }
	}
?>
