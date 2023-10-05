<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.edit');
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            $setting = Setting::firstOrCreate(['key' => $key]);
            $setting->value = $value;
            $setting->save();
        }

        return redirect()->route('settings.index');
    }

    public function getDiscount()
    {
        $discount = (int) (Setting::where('key', 'min_discount_amount')->first())->value ?? 0;
        $discountPercent = (float) (Setting::where('key', 'discount_percentage')->first())->value ?? 0;

        return response([
            'discount' => $discount,
            'discount_percent' => $discountPercent,
        ], 200);
    }
}
