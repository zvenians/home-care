import test from 'node:test';
import assert from 'node:assert/strict';
import vm from 'node:vm';
import fs from 'node:fs';
const src=fs.readFileSync(new URL('../preview/assets/app.js',import.meta.url),'utf8');
const css=fs.readFileSync(new URL('../preview/assets/app.css',import.meta.url),'utf8').toLowerCase();
const app={innerHTML:''};
const local=new Map();
const fake={
 window:{HOMECARE_CONFIG:{api:false,demo:true,assets:'assets/'},scrollTo(){},addEventListener(){}},
 document:{getElementById:(id)=>id==='app'?app:null,addEventListener(){},querySelector(){return null;},body:{append(){},classList:{add(){},remove(){}}},createElement(){return {innerHTML:'',className:'',querySelector(){return {onclick:null}},remove(){},setAttribute(){}}}},
 localStorage:{getItem:k=>local.get(k)||null,setItem:(k,v)=>local.set(k,v)},
 structuredClone,Date,Intl,Math,Number,String,Array,Object,Error,Set,JSON,console,setTimeout,clearTimeout,Blob,URL:{createObjectURL(){return 'blob:mock'},revokeObjectURL(){}},
};
const ctx=vm.createContext(fake);
vm.runInContext(src,ctx,{filename:'app.js'});
function evalJS(script){return vm.runInContext(script,ctx)}
test('home renders and service browsing route works',()=>{
 assert.match(app.innerHTML,/Perawatan terbaik/);
 evalJS("openPage('services')");assert.match(app.innerHTML,/Layanan kami/);
 evalJS("openPage('booking')");assert.match(app.innerHTML,/Rincian biaya/);
});
test('booking persists, viewer receipt has detailed prices',()=>{
 const b=evalJS(`makeBookingLocal({service_id:1,units:2,distance_km:7,scheduled_date:'2027-12-01',scheduled_time:'09:00',name:'Demo Customer',phone:'081298761234',email:'demo@example.com',address:'Jl. Demo No. 15 Bandung',patient_note:'',addons:[],urgent:false},calculateQuote(getService(1),{units:2,distance_km:7,scheduled_date:'2027-12-01',scheduled_time:'09:00'}))`);
 assert.equal(b.total,184000);
 assert.ok(evalJS('viewerBookings().length')>0);
 const receipt=evalJS(`receiptContent(bookingRich(state.data.bookings.at(0)))`);
 assert.match(receipt,/BUKAN BUKTI PEMBAYARAN/);
 assert.match(receipt,/Total estimasi/);
 assert.match(receipt,/Perjalanan/);
});
test('valid PDF is generated without network dependence',async()=>{
 const blob=evalJS('buildReceiptPdf(state.data.bookings.at(0))');
 const str=await blob.text();
 assert.ok(str.startsWith('%PDF-1.4'));
 assert.match(str,/xref/);
 assert.match(str,/STRUK PEMESANAN/);
 assert.match(str,/184.000/);
});
test('admin and customer menus are distinct',()=>{
 evalJS("state.role='admin'; state.view='dashboard'; render()");
 assert.match(app.innerHTML,/Kelola Layanan/);
 assert.match(app.innerHTML,/Pemesanan terbaru/);
 evalJS("state.role='viewer'; state.view='orders'; render()");
 assert.match(app.innerHTML,/Riwayat/);
});
test('caregiver demo role has an operational dashboard and assigned schedule',()=>{
 evalJS("state.role='caregiver'; state.view='caregiver-dashboard'; render()");
 assert.match(app.innerHTML,/Dashboard petugas/);
 assert.match(app.innerHTML,/Jadwal saya/);
 assert.match(app.innerHTML,/HC-DEMO-001/);
 assert.doesNotMatch(app.innerHTML,/Kelola Layanan/);
});
test('booking is presented as a five-step wizard with draft progress',()=>{
 evalJS("state.role='viewer'; state.view='booking'; state.bookingStep=1; render()");
 assert.match(app.innerHTML,/Langkah 1 dari 5/);
 assert.match(app.innerHTML,/Pilih layanan/);
 assert.match(app.innerHTML,/data-booking-next/);
 evalJS('state.bookingStep=5; render()');
 assert.match(app.innerHTML,/Tinjau dan konfirmasi/);
 assert.match(app.innerHTML,/Konfirmasi pesanan/);
});
test('admin local preview CRUD service create update delete',async()=>{
 fake.confirm=()=>true;
 evalJS("state.role='admin'; state.view='services'; render()");
 const form=(id,price)=>({dataset:{resource:'services',id:id?String(id):''},elements:{namedItem(key){const values={name:'Layanan Uji Demo',category:'Terapi',description:'Deskripsi yang cukup untuk contoh.',price:String(price),unit:'visit',min_units:'1',duration_minutes:'60',icon:'heart',active:true};return {value:String(values[key]??''),checked:key==='active'};}}});
 const count=evalJS('state.data.services.length');
 fake.fakeForm=form('',123000);
 await evalJS('submitManagement(fakeForm)');
 assert.equal(evalJS('state.data.services.length'),count+1);
 const id=evalJS('state.data.services[0].id');
 fake.fakeForm=form(id,155000);
 await evalJS('submitManagement(fakeForm)');
 assert.equal(evalJS(`state.data.services.find(s=>s.id===${id}).price`),155000);
 await evalJS(`removeItem('services',${id})`);
 assert.equal(evalJS('state.data.services.length'),count);
});
test('brand palette is cranberry red without legacy green tokens',()=>{
 assert.match(css,/#9f1239/);
 assert.match(css,/#be123c/);
 assert.match(css,/#fff1f2/);
 for(const legacy of ['#0b635b','#0d5145','#137e6a','#137e6d','#0f745f']){
  assert.doesNotMatch(css,new RegExp(legacy));
 }
});
test('premium identity uses the official seal, editorial type, local photography, and real reveal motion',()=>{
 evalJS("state.role='viewer'; state.view='home'; render()");
 const brand=evalJS('brand()');
 assert.match(brand,/logo-unai\.png/);
 assert.doesNotMatch(brand,/brand-mark/);
 assert.match(css,/newsreader/);
 assert.match(css,/geist/);
 assert.match(src,/photos\/home-care-hero\.webp/);
 assert.match(src,/intersectionobserver/i);
 assert.match(app.innerHTML,/fetchpriority="high"/);
 assert.doesNotMatch(app.innerHTML,/<img(?![^>]*\bwidth=)/);
});
