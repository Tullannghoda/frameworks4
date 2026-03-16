<?php

namespace App\Http\Controllers;

use App\Models\RegProvince;
use App\Models\RegRegency;
use App\Models\RegDistrict;
use App\Models\RegVillage;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $provinceList = RegProvince::orderBy('name')->get();

        return view('wilayah.index', [
            'provinces' => $provinceList
        ]);
    }

    public function fetchRegency(Request $request)
    {
        $regencies = RegRegency::where('province_id', $request->province_id)
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'message' => 'Regency loaded',
            'result' => $regencies
        ]);
    }

    public function fetchDistrict(Request $request)
    {
        $districts = RegDistrict::where('regency_id', $request->regency_id)
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'message' => 'District loaded',
            'result' => $districts
        ]);
    }

    public function fetchVillage(Request $request)
    {
        $villages = RegVillage::where('district_id', $request->district_id)
                        ->orderBy('name')
                        ->get();

        return response()->json([
            'message' => 'Village loaded',
            'result' => $villages
        ]);
    }
}