<?php
namespace Database\Seeders;

use App\Models\{Application,Booking,Caregiver,Customer,Promotion,Review,Service,ServiceArea};
use App\Services\PricingEngine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name'=>'Perawat Home Visit','slug'=>'perawat-home-visit','category'=>'Keperawatan','description'=>'Perawatan dasar, pemantauan kondisi, dan bantuan aktivitas harian di rumah.','icon'=>'heart','price'=>85000,'unit'=>'hour','min_units'=>2,'duration_minutes'=>60],
            ['name'=>'Fisioterapi Rumah','slug'=>'fisioterapi-rumah','category'=>'Terapi','description'=>'Sesi latihan mobilitas dan rehabilitasi sesuai asesmen tenaga kesehatan.','icon'=>'activity','price'=>220000,'unit'=>'visit','min_units'=>1,'duration_minutes'=>60],
            ['name'=>'Akupunktur','slug'=>'akupunktur','category'=>'Terapi','description'=>'Sesi akupunktur oleh tenaga kompeten sesuai hasil skrining.','icon'=>'sparkles','price'=>195000,'unit'=>'visit','min_units'=>1,'duration_minutes'=>60],
            ['name'=>'Perawatan Lansia','slug'=>'perawatan-lansia','category'=>'Pendampingan','description'=>'Pendampingan lansia dan pemantauan kebutuhan rutin selama kunjungan.','icon'=>'users','price'=>75000,'unit'=>'hour','min_units'=>4,'duration_minutes'=>60],
            ['name'=>'Perawatan Luka','slug'=>'perawatan-luka','category'=>'Keperawatan','description'=>'Tindakan perawatan luka sederhana setelah penilaian tenaga medis.','icon'=>'shield','price'=>185000,'unit'=>'visit','min_units'=>1,'duration_minutes'=>45],
            ['name'=>'Pendamping Harian','slug'=>'pendamping-harian','category'=>'Pendampingan','description'=>'Dukungan aktivitas non-medis dan pendampingan keluarga selama satu hari.','icon'=>'sun','price'=>495000,'unit'=>'day','min_units'=>1,'duration_minutes'=>480],
        ];
        foreach ($services as $data) Service::create($data);
        foreach ([
            ['name'=>'Nadia Putri, S.Kep.','role'=>'Perawat','speciality'=>'Perawatan lansia & home visit','experience_years'=>6,'city'=>'Bandung','rating'=>4.9,'initials'=>'NP','accent'=>'mint'],
            ['name'=>'Rizky Pratama, S.Ft.','role'=>'Fisioterapis','speciality'=>'Rehabilitasi & terapi gerak','experience_years'=>5,'city'=>'Cimahi','rating'=>4.9,'initials'=>'RP','accent'=>'peach'],
            ['name'=>'Aulia Rahma, A.Md.Kep.','role'=>'Perawat','speciality'=>'Perawatan luka & pasien rumah','experience_years'=>7,'city'=>'Bandung','rating'=>5.0,'initials'=>'AR','accent'=>'lilac'],
            ['name'=>'Daniel Wijaya, S.Kep.','role'=>'Perawat','speciality'=>'Perawatan pasca-rawat inap','experience_years'=>4,'city'=>'Lembang','rating'=>4.8,'initials'=>'DW','accent'=>'sky'],
        ] as $person) Caregiver::create($person);
        foreach ([['name'=>'Rina Puspita','phone'=>'081234567801','email'=>'rina@example.test','address'=>'Jl. Setiabudi, Bandung'], ['name'=>'Agus Haryanto','phone'=>'081234567802','email'=>'agus@example.test','address'=>'Jl. Gegerkalong, Bandung'], ['name'=>'Salsa Maharani','phone'=>'081234567803','email'=>'salsa@example.test','address'=>'Jl. Cihampelas, Bandung']] as $customer) Customer::create($customer);
        Promotion::create(['code'=>'CARE10','type'=>'percent','value'=>10,'max_discount'=>50000,'active'=>true]);
        Promotion::create(['code'=>'HEMAT25','type'=>'fixed','value'=>25000,'max_discount'=>25000,'active'=>true]);
        foreach ([['name'=>'Bandung Utara','city'=>'Bandung','coverage_km'=>25], ['name'=>'Cimahi & Sekitar','city'=>'Cimahi','coverage_km'=>22], ['name'=>'Lembang','city'=>'Bandung Barat','coverage_km'=>35]] as $area) ServiceArea::create($area);
        Application::create(['name'=>'Dian Lestari','email'=>'dian@example.test','phone'=>'081234567900','profession'=>'Perawat','experience_years'=>3,'motivation'=>'Ingin bergabung dalam tim layanan home care.']);
        Review::create(['name'=>'Ibu Rina','service_id'=>1,'rating'=>5,'comment'=>'Tim ramah, komunikatif, dan datang sesuai jadwal.','published'=>true]);
        Review::create(['name'=>'Bapak Agus','service_id'=>2,'rating'=>5,'comment'=>'Penjelasan terapi mudah dipahami keluarga.','published'=>true]);
        $engine=app(PricingEngine::class);
        foreach ([
            ['customer_id'=>1,'service_id'=>1,'caregiver_id'=>1,'date'=>'+2 days','time'=>'09:00','units'=>4,'distance'=>8,'status'=>'confirmed'],
            ['customer_id'=>2,'service_id'=>2,'caregiver_id'=>2,'date'=>'+3 days','time'=>'14:00','units'=>1,'distance'=>12,'status'=>'assigned'],
            ['customer_id'=>3,'service_id'=>3,'caregiver_id'=>null,'date'=>'+5 days','time'=>'10:00','units'=>1,'distance'=>6,'status'=>'pending'],
        ] as $i=>$b) {
            $date=now()->modify($b['date'])->toDateString();
            $quote=$engine->calculate(Service::findOrFail($b['service_id']),['units'=>$b['units'],'distance_km'=>$b['distance'],'scheduled_date'=>$date,'scheduled_time'=>$b['time'],'urgent'=>false,'addons'=>[]],null);
            Booking::create(['code'=>'HC-'.now()->format('ymd').'-'.str_pad((string)($i+1),3,'0',STR_PAD_LEFT),'service_id'=>$b['service_id'],'customer_id'=>$b['customer_id'],'caregiver_id'=>$b['caregiver_id'],'scheduled_date'=>$date,'scheduled_time'=>$b['time'],'units'=>$b['units'],'distance_km'=>$b['distance'],'urgent'=>false,'addons'=>[],'address'=>Customer::find($b['customer_id'])->address,'status'=>$b['status'],'price_breakdown'=>$quote,'total'=>$quote['total']]);
        }
    }
}
