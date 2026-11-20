export const ADDONS = {
  care_kit: { name: 'Care kit sekali pakai', price: 40000 },
  report: { name: 'Laporan perawatan digital', price: 15000 },
  companion: { name: 'Pendamping keluarga', price: 90000 }
};
export const POLICY = { freeKm: 5, extraKm: 4500, nightPercent: 20, weekendPercent: 10, urgent: 75000, platform: 5000 };
export const money = (amount) => 'Rp' + Number(amount || 0).toLocaleString('id-ID');
export const unitLabel = (unit) => ({ hour:'jam', visit:'kunjungan', day:'hari' }[unit] || unit);
export function calculateQuote(service, options={}, promos=[]) {
  if (!service) throw new Error('Pilih layanan terlebih dahulu.');
  const units = Number(options.units ?? service.min_units ?? 1);
  const distance = Number(options.distance_km ?? 0);
  if (!Number.isInteger(units) || units < Number(service.min_units ?? 1) || units > 30) throw new Error(`Minimal ${service.min_units} ${unitLabel(service.unit)} dan maksimal 30 unit.`);
  if (!Number.isFinite(distance) || distance < 0 || distance > 200) throw new Error('Jarak harus antara 0 dan 200 km.');
  const date = options.scheduled_date || '2026-12-01';
  const time = options.scheduled_time || '09:00';
  const d = new Date(`${date}T${time}:00+07:00`);
  if (Number.isNaN(d.getTime())) throw new Error('Tanggal atau waktu tidak valid.');
  const hour = Number(time.split(':')[0]);
  const base = Number(service.price) * units;
  const travel = Math.ceil(Math.max(0,distance-POLICY.freeKm)) * POLICY.extraKm;
  const night = (hour >= 20 || hour < 6) ? Math.round(base*POLICY.nightPercent/100) : 0;
  // The date supplied is an Indonesian calendar date. UTC parsing avoids local timezone dependency.
  const day = new Date(`${date}T12:00:00Z`).getUTCDay();
  const weekend = (day === 0 || day === 6) ? Math.round(base*POLICY.weekendPercent/100) : 0;
  const urgent = options.urgent ? POLICY.urgent : 0;
  const addons = [...new Set(options.addons || [])].filter(code => ADDONS[code]).slice(0,3).map(code=>({code, ...ADDONS[code], amount: ADDONS[code].price}));
  const addon_total = addons.reduce((n,item)=>n+item.amount,0);
  let promo_code = null, discount = 0;
  const code=String(options.promo_code || '').trim().toUpperCase();
  if (code) {
    const promo=promos.find(p=>String(p.code).toUpperCase()===code && p.active && (!p.expires_at || String(p.expires_at).slice(0,10)>=date));
    if (!promo) throw new Error('Kode promo tidak ditemukan atau sudah tidak aktif.');
    discount = promo.type === 'percent' ? Math.round(base * Number(promo.value)/100) : Number(promo.value);
    if (Number(promo.max_discount)>0) discount = Math.min(discount,Number(promo.max_discount));
    discount=Math.min(base,discount);
    promo_code=code;
  }
  return {service_name:service.name, unit:service.unit, units, base, travel, night, weekend, urgent, addons, addon_total, discount, platform:POLICY.platform, total:Math.max(0,base+travel+night+weekend+urgent+addon_total+POLICY.platform-discount),distance_km:distance, promo_code, pricing_note:'Tarif simulasi untuk demo. Jarak diisi manual; belum memakai peta atau GPS. Pembayaran online belum aktif.'};
}
export function breakdownRows(q={}) {
  return [
    [`Layanan (${q.units ?? 1} ${unitLabel(q.unit)})`,q.base || 0],
    [`Perjalanan (jarak ${q.distance_km ?? 0} km; 5 km pertama gratis)`,q.travel || 0],
    ['Tambahan malam (20%)',q.night || 0],
    ['Tambahan akhir pekan (10%)',q.weekend || 0],
    ['Prioritas mendesak',q.urgent || 0],
    ...(q.addons || []).map(a=>[a.name,a.amount]),
    ['Biaya platform',q.platform || 0],
    ['Diskon'+(q.promo_code ? ` (${q.promo_code})` : ''),-(q.discount || 0)]
  ];
}
