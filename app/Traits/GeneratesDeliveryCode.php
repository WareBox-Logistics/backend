<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesDeliveryCode
{
        public function generateConfirmationCode()
        {
            $this->update([
                'confirmation_code' => Str::random(12), // 12-character random code
                'code_generated_at' => now(),
                'code_expires_at' => $this->shipping_date->addDays(3),
                'code_used_at' => null
            ]);
            
            return $this->confirmation_code;
        }
    
        public function isCodeValid()
        {
            return $this->confirmation_code && 
                   !$this->code_used_at && 
                   now()->lt($this->code_expires_at);
        }
    
}