<?php

namespace App\Intents;

use Alexa\Request\IntentRequest;
use App\Intents\Slots\Slot;
use App\User;

class Intent
{
    /* Properties */

    public $slots = [];
    public $user;
    public $requiredSlots = [];
    public $optionalSlots = [];
    public $confirmationStatus;

    /* Methods */

    public function __construct(User $user, IntentRequest $request)
    {
        $this->user = $user;
        $this->confirmationStatus = 'NONE';

        /* Parse request data for slot values */

        $this->parseDataForSlots($request);

        /* Assert required slot values are set */

        $this->assertRequiredSlotValuesAreSet();
    }

    protected function parseDataForSlots(IntentRequest $request)
    {
        foreach ($request->slots as $slotName => $slotValue) {
            $slotValue = ucwords($slotValue);
            error_log('Slot ' . $slotName . ': ' . $slotValue);
            $this->slots[$slotName] = new Slot($slotName, $slotValue, 'NONE');
        }
    }

    protected function assertRequiredSlotValuesAreSet()
    {
        /* Optional slots */

        foreach ($this->optionalSlots as $slotName) {
            if (!isset($this->slotValues[$slotName])) {
                $this->slotValues[$slotName] = '';
            }
        }

        /* Required slots */

        foreach ($this->requiredSlots as $slotName) {
            if (!isset($this->slotValues[$slotName]) || empty($this->slotValues[$slotName])) {
                /* Redirect the Alexa Dialog */

                $this->delegateDialog();
            }
        }
    }

    public function process()
    {

    }

    public function reply(string $answer)
    {

    }

    public function delegateDialog()
    {
        $intentName = get_class($this);
        error_log('delegating dialog for intent ' . $intentName);

        $json = json_encode([
            "version" => "1.0",
            "sessionAttributes" => [],
            "response" => [
                "outputSpeech" => [
                    "type" => "PlainText",
                    "text" => "Standard-Antwort bei Umleitung"
                ],
                "shouldEndSession" => false,
                "directives" => [
                    [
                        'type' => 'Dialog.Delegate'
                    ]
                ]
            ]
        ], JSON_FORCE_OBJECT);
        error_log($json);

        echo $json;
        exit;
    }
}