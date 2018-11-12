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
      $this->RegisterPropertyInteger("Intervall", 0);
      $this->RegisterTimer("SendTXCommand", 0, 'LCNGetKeyLocks_Update($_IPS[\'TARGET\']);');
      
      $this->RegisterVariableInteger("TastentabelleA", "Tastentabelle A");
      $this->RegisterVariableInteger("TastentabelleB", "Tastentabelle B");
      $this->RegisterVariableInteger("TastentabelleC", "Tastentabelle C");
      $this->RegisterVariableInteger("TastentabelleD", "Tastentabelle D");
      
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
	 		$this->SetTimerInterval("SendTXCommand", $this->ReadPropertyInteger("Intervall") * 1000);
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
            'Target'   => $this->ReadPropertyInteger('ModulID'),
            'Function' => $Function,
            'Data'     => $Data
        ];
        $this->SendDebug('Send', $SendData, 0);
        $this->SendDebug('Send', json_encode($SendData), 0);
        $Result = $this->SendDataToParent(json_encode($SendData));
        $this->SendDebug('Result', $Result, 0);
        $this->SendDebug('Result', json_decode($Result), 0);
        //SetValueInteger($this->GetIDForIdent("TastentabelleA"), intval($treffer['A']));
    }

    public function Update()
    {
  		if (IPS_GetKernelRunlevel() !== 10103)
	   	{
		  	$this->SendDebug("FUNCTION -Update-", "Kernel is not ready! Kernel Runlevel = ".IPS_GetKernelRunlevel(), 0);
			  return false;
  		}
      $this->SendDebug('STX',"Sende TX Kommando...", 0);
      //@$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => utf8_encode(">M".sprintf("%06d",$this->ReadPropertyInteger("ModulID")).".STX\n"))));
		}
		
    public function ReceiveData($JSONString)
    {
        $this->SendDebug('Receive', $JSONString, 0);
        $this->SendDebug('Receive', $Data->Data, 0);
        if (preg_match('/(?<A>[0-9]{3})(?<B>[0-9]{3})(?<C>[0-9]{3})(?<D>[0-9]{3})/',$Data->Data,$treffer)){
          SetValueInteger($this->GetIDForIdent("TastentabelleA"), intval($treffer['A']));
          SetValueInteger($this->GetIDForIdent("TastentabelleB"), intval($treffer['B']));
          SetValueInteger($this->GetIDForIdent("TastentabelleC"), intval($treffer['C']));
          SetValueInteger($this->GetIDForIdent("TastentabelleD"), intval($treffer['D']));
        }
    }
	}
?>
