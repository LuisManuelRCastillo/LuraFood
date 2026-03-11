<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffAuthController extends Controller
{
    public function showPin()
    {
        return view('Staff.pin');
    }

    public function verifyPin(Request $request)
    {
        $pin = $request->input('pin');
        $correct = config('app.staff_pin', env('STAFF_PIN', '1234'));

        if ($pin === (string) $correct) {
            session(['staff_authenticated' => true]);
            return redirect()->route('pedidos.pendientes');
        }

        return back()->withErrors(['pin' => 'PIN incorrecto. Intenta de nuevo.']);
    }

    public function logout()
    {
        session()->forget('staff_authenticated');
        return redirect()->route('staff.pin');
    }
}
