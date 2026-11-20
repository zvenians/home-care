<?php
namespace App\Http\Controllers\Api;

use App\Models\{Application,Caregiver,Customer,Promotion,Review,Service,ServiceArea};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManagementController extends Controller
{
    private const MODELS=['services'=>Service::class,'caregivers'=>Caregiver::class,'customers'=>Customer::class,'applications'=>Application::class,'promotions'=>Promotion::class,'service_areas'=>ServiceArea::class,'reviews'=>Review::class];

    private function definition(string $resource): string
    {
        abort_unless(array_key_exists($resource,self::MODELS),404);
        return self::MODELS[$resource];
    }
    private function rules(string $resource,?int $id=null): array
    {
        return match($resource) {
            'services'=>['name'=>'required|string|max:120','slug'=>['sometimes','string','max:120',Rule::unique('services','slug')->ignore($id)],'category'=>'required|string|max:80','description'=>'required|string|max:600','icon'=>'sometimes|string|max:30','price'=>'required|integer|min:0|max:20000000','unit'=>'required|in:hour,visit,day','min_units'=>'required|integer|min:1|max:24','duration_minutes'=>'required|integer|min:15|max:1440','active'=>'required|boolean'],
            'caregivers'=>['name'=>'required|string|max:120','role'=>'required|string|max:80','speciality'=>'required|string|max:160','experience_years'=>'required|integer|min:0|max:60','city'=>'required|string|max:100','rating'=>'required|numeric|between:0,5','available'=>'required|boolean','initials'=>'nullable|string|max:4','accent'=>'sometimes|string|in:mint,peach,lilac,sky','bio'=>'nullable|string|max:1000'],
            'customers'=>['name'=>'required|string|max:120','phone'=>'required|string|max:25','email'=>'nullable|email|max:180','address'=>'nullable|string|max:500'],
            'applications'=>['name'=>'required|string|max:120','email'=>'required|email|max:180','phone'=>'required|string|max:25','profession'=>'required|string|max:100','experience_years'=>'required|integer|min:0|max:60','motivation'=>'nullable|string|max:1000','status'=>'required|in:new,reviewed,interview,accepted,rejected'],
            'promotions'=>['code'=>['required','alpha_dash','max:30',Rule::unique('promotions','code')->ignore($id)],'type'=>'required|in:percent,fixed','value'=>'required|integer|min:1|max:1000000','max_discount'=>'required|integer|min:0|max:1000000','active'=>'required|boolean','expires_at'=>'nullable|date'],
            'service_areas'=>['name'=>'required|string|max:100','city'=>'required|string|max:100','coverage_km'=>'required|integer|min:1|max:200','active'=>'required|boolean'],
            'reviews'=>['name'=>'required|string|max:120','service_id'=>'nullable|exists:services,id','rating'=>'required|integer|min:1|max:5','comment'=>'required|string|max:1000','published'=>'required|boolean'],
            default=>abort(404)
        };
    }
    public function store(Request $request,string $resource)
    {
        $model=$this->definition($resource);
        $data=$request->validate($this->rules($resource));
        if ($resource==='services') $data['slug']=$data['slug']??Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        if ($resource==='promotions') $data['code']=strtoupper($data['code']);
        return response()->json(['item'=>$model::create($data),'message'=>'Data berhasil ditambahkan.'],201);
    }
    public function update(Request $request,string $resource,int $id)
    {
        $model=$this->definition($resource);
        $data=$request->validate($this->rules($resource,$id));
        if ($resource==='promotions') $data['code']=strtoupper($data['code']);
        $item=$model::findOrFail($id);$item->update($data);
        return response()->json(['item'=>$item->fresh(),'message'=>'Perubahan tersimpan.']);
    }
    public function destroy(string $resource,int $id)
    {
        $model=$this->definition($resource);
        $item=$model::findOrFail($id);
        try { $item->delete(); }
        catch (\Illuminate\Database\QueryException $e) { return response()->json(['message'=>'Data sudah dipakai pada pesanan. Nonaktifkan data jika tidak ingin menampilkannya.'],409); }
        return response()->json(['message'=>'Data berhasil dihapus.']);
    }
}
