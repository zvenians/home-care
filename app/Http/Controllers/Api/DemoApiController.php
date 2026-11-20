<?php
namespace App\Http\Controllers\Api;

use App\Models\{Application,Booking,Caregiver,Customer,Promotion,Review,Service,ServiceArea};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DemoApiController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(config('homecare.demo_mode'),403);
        $admin=$request->session()->get('demo_role')==='admin';
        $mine = $request->session()->get('demo_customer_id');
        return response()->json([
            'role'=>$admin?'admin':'viewer',
            'services'=>$admin?Service::orderBy('id')->get():Service::where('active',true)->orderBy('id')->get(),
            'caregivers'=>$admin?Caregiver::orderBy('id')->get():Caregiver::where('available',true)->orderBy('id')->get(),
            'bookings'=>Booking::with(['service','caregiver','customer'])->when(!$admin,fn($q)=>$q->where('customer_id',$mine??0))->latest()->get(),
            'customers'=>$admin?Customer::latest()->get():[],
            'applications'=>$admin?Application::latest()->get():[],
            'promotions'=>$admin?Promotion::orderBy('id')->get():Promotion::where('active',true)->get(['id','code','type','value','max_discount','expires_at']),
            'service_areas'=>$admin?ServiceArea::orderBy('id')->get():ServiceArea::where('active',true)->orderBy('id')->get(),
            'reviews'=>$admin?Review::latest()->get():Review::where('published',true)->latest()->get(),
        ]);
    }

    public function apply(Request $request)
    {
        abort_unless(config('homecare.demo_mode'),403);
        $data=$request->validate(['name'=>'required|string|min:2|max:120','email'=>'required|email|max:180','phone'=>'required|string|max:25','profession'=>'required|string|max:100','experience_years'=>'required|integer|min:0|max:60','motivation'=>'nullable|string|max:1000']);
        return response()->json(['item'=>Application::create($data),'message'=>'Lamaran demo berhasil dikirim.'],201);
    }

    public function review(Request $request)
    {
        abort_unless(config('homecare.demo_mode'),403);
        $data=$request->validate(['name'=>'required|string|min:2|max:120','service_id'=>'nullable|exists:services,id','rating'=>'required|integer|min:1|max:5','comment'=>'required|string|min:10|max:1000']);
        return response()->json(['item'=>Review::create([...$data,'published'=>false]),'message'=>'Ulasan tersimpan dan menunggu moderasi.'],201);
    }
}
