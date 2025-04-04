<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
class DeliveryConfirmationController extends Controller
{
    public function generateCode(Delivery $delivery)
    {
        if (!$delivery->isClientDelivery()) {
            throw new \Exception('QR codes only available for client deliveries');
        }

        $code = $delivery->generateConfirmationCode();
        
        return response()->json([
            'qr_code' => $code,
            'expires_at' => $delivery->code_expires_at->toDateTimeString()
        ]);
    }

    public function confirmByCode(Request $request)
{
    $validated = $request->validate(['confirmation_code' => 'required|string|size:12']);

    $delivery = Delivery::where('confirmation_code', $validated['confirmation_code'])
        ->whereNull('code_used_at')
        ->where('code_expires_at', '>', now())
        ->firstOrFail();

    $enumExists = DB::selectOne("
        SELECT 1 FROM pg_enum 
        JOIN pg_type ON pg_enum.enumtypid = pg_type.oid 
        WHERE pg_type.typname = 'delivery_status' 
        AND enumlabel = 'Delivered'
    ");

    if (!$enumExists) {
        throw new \Exception('Delivered status not available in database');
    }

    DB::statement("
        UPDATE delivery 
        SET status = 'Delivered',
            code_used_at = NOW(),
            completed_date = NOW(),
            updated_at = NOW()
        WHERE id = ?
    ", [$delivery->id]);

    return response()->json([
        'message' => 'Delivery confirmed',
        'status' => 'Delivered'
    ]);
}
}
