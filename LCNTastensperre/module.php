<?php

include_once(__DIR__ . "/../libs/DebugHelper.php");

/*
 * @addtogroup network
 * @{
 *
 * @package       LCNTest
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       0.1
 */

/**
 * LCNTestDevice Klasse implementiert einen Sniffer für den Datenaustausch mit dem LCN Gateway.
 * Erweitert IPSModule.
 *
 * @package       LCNTest
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       0.1
 * @example <b>Ohne</b>
 */
class LCNTestDevice extends ipsmodule
{

    use DebugHelper; // Erweitert die SendDebug Methode von IPS um Arrays, Objekte und bool.
    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger('Modul', 0);
        $this->RegisterPropertyInteger('Segment', 0);
        $this->ConnectParent("{9BDFC391-DEFF-4B71-A76B-604DBA80F207}");
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        $this->RegisterMessage(0, IPS_KERNELSTARTED);

        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }
        $Filter = '.*"Message":2,"Segment":' .
                $this->ReadPropertyInteger('Segment') .
                ',"Target":' .
                $this->ReadPropertyInteger('Modul') .
                ',"Function":"TX".*';

        $this->SendDebug('FILTER', $Filter, 0);
        $this->SetReceiveDataFilter($Filter);
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IPS_KERNELSTARTED: // Nach dem IPS-Start
                $this->KernelReady(); // Sagt alles.
                break;
        }
    }

    /**
     * Wird ausgeführt wenn der Kernel hochgefahren wurde.
     * @access protected
     */
    protected function KernelReady()
    {
        $this->ApplyChanges();
    }

   public function SendTest(string $Function, string $Data)
    {
        $SendData = [
            'DataID'   => '{C5755489-1880-4968-9894-F8028FE1020A}',
            'Address'  => 0, // 0 => M, 1 => G
            'Segment'  => $this->ReadPropertyInteger('Segment'),
            'Target'   => $this->ReadPropertyInteger('Modul'),
            'Function' => $Function,
            'Data'     => $Data
        ];
        $this->SendDebug('Send', $SendData, 0);
        $this->SendDebug('Send', json_encode($SendData), 0);
        $Result = $this->SendDataToParent(json_encode($SendData));
        $this->SendDebug('Result', $Result, 0);
        $this->SendDebug('Result', json_decode($Result), 0);
    }

    /**
     * Empfängt Daten vom Parent.
     *
     * @access public
     * @param string $JSONString Das empfangene JSON-kodierte Objekt vom Parent.
     */
    public function ReceiveData($JSONString)
    {
        $this->SendDebug('Receive', $JSONString, 0);
        $Data = json_decode($JSONString);
        $this->SendDebug('Receive', $Data, 0);
    }

}
