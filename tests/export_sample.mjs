import vm from 'node:vm';import fs from 'node:fs';
const app={innerHTML:''};const local=new Map();
const sandbox={window:{HOMECARE_CONFIG:{api:false},scrollTo(){},addEventListener(){}},document:{getElementById:id=>app,addEventListener(){},querySelector(){return null}},localStorage:{getItem:k=>local.get(k)||null,setItem:(k,v)=>local.set(k,v)},structuredClone,Date,Intl,Math,Number,String,Array,Object,Set,Error,JSON,console,setTimeout,clearTimeout,Blob};
vm.createContext(sandbox);vm.runInContext(fs.readFileSync(new URL('../preview/assets/app.js',import.meta.url),'utf8'),sandbox);
const blob=vm.runInContext('buildReceiptPdf(state.data.bookings[0])',sandbox);
fs.writeFileSync(new URL('../../Struk-Contoh-HomeCare.pdf',import.meta.url),Buffer.from(await blob.arrayBuffer()));
console.log('Created example PDF');
