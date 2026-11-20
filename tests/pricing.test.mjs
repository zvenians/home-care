import test from 'node:test';
import assert from 'node:assert/strict';
import {calculateQuote,breakdownRows} from '../resources/js/pricing.js';
const service={name:'Perawat Home Visit',price:85000,unit:'hour',min_units:2};
test('tarif dasar, jarak, admin tetap konsisten',()=>{
 const q=calculateQuote(service,{units:2,distance_km:7,scheduled_date:'2026-12-01',scheduled_time:'09:00'});
 assert.equal(q.base,170000);assert.equal(q.travel,9000);assert.equal(q.total,184000);
 assert.equal(breakdownRows(q).reduce((n,row)=>n+row[1],0),q.total);
});
test('malam dan weekend berlaku berdasarkan tanggal Indonesia',()=>{
 const q=calculateQuote(service,{units:2,distance_km:0,scheduled_date:'2026-12-05',scheduled_time:'21:00'});
 assert.equal(q.night,34000);assert.equal(q.weekend,17000);assert.equal(q.total,226000);
});
test('promo memiliki batas maksimum, add-on dihitung',()=>{
 const q=calculateQuote(service,{units:2,distance_km:0,scheduled_date:'2026-12-01',scheduled_time:'09:00',addons:['report'],promo_code:'CARE10'},[{code:'CARE10',type:'percent',value:10,max_discount:50000,active:true}]);
 assert.equal(q.discount,17000);assert.equal(q.total,173000);
});
test('invalid units and promo rejected',()=>{
 assert.throws(()=>calculateQuote(service,{units:1}),/Minimal/);
 assert.throws(()=>calculateQuote(service,{promo_code:'INVALID'}),/promo/);
});
