const fs=require('fs'),vm=require('vm'),assert=require('node:assert/strict');
const pages=['index.html','san-pham.html','chi-tiet-san-pham.html','chinh-sach.html','cam-on.html'];
const ctx={window:{},document:{addEventListener(){}}};vm.createContext(ctx);
vm.runInContext(fs.readFileSync('assets/js/data.js','utf8'),ctx);
vm.runInContext(fs.readFileSync('assets/js/main.js','utf8'),ctx);
const data=ctx.window.FCC_DATA;
assert.equal(data.combos.length,1);assert.equal(data.combos[0].salePrice,1237500);assert.deepEqual(Array.from(data.combos[0].productIds),[6,7]);
for(const [id,price] of Object.entries({6:593100,7:682500,8:506800,9:379776,10:379776}))assert.equal(data.products.find(p=>p.id===+id).salePrice,price);
assert.equal(ctx.formatVND(null),'Liên hệ');
const valid=new Set([...data.products.map(p=>'prod_'+p.id),...data.combos.map(c=>'combo_'+c.id)]);
for(const file of pages){
 const h=fs.readFileSync(file,'utf8');
 const ids=[...h.matchAll(/\bid="([^"]+)"/g)].map(m=>m[1]);assert.equal(ids.length,new Set(ids).size,`Duplicate ID in ${file}`);
 for(const m of h.matchAll(/data-product-select="([^"]+)"/g))assert(valid.has(m[1]),`Unknown CTA ${m[1]} in ${file}`);
 for(const m of h.matchAll(/<label[^>]*\bfor="([^"]+)"/g))assert(ids.includes(m[1]),`Broken label ${file}: ${m[1]}`);
 for(const m of h.matchAll(/(?:src|href)="([^"#]+)"/g)){const path=m[1].split(/[?#]/)[0];if(!/^(https?:|tel:|mailto:)/.test(path))assert(fs.existsSync(path),`Missing ${path}`);}
 for(const m of h.matchAll(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/g))JSON.parse(m[1]);
 const markup=h.replace(/<!--[\s\S]*?-->/g,'').replace(/<(script|style)\b[^>]*>[\s\S]*?<\/\1>/g,'');
 const stack=[],voidTags=new Set('area base br col embed hr img input link meta param source track wbr path circle rect line polyline'.split(' '));
 for(const m of markup.matchAll(/<(\/?)([a-z][a-z0-9-]*)\b[^>]*>/gi)){
   const tag=m[2].toLowerCase();if(voidTags.has(tag)||m[0].endsWith('/>'))continue;
   if(m[1])assert.equal(stack.pop(),tag,`Unbalanced HTML in ${file}: ${m[0]}`);else stack.push(tag);
 }
 assert.equal(stack.length,0,`Unclosed HTML in ${file}`);
 assert(!/1900252563|1900\.2525\.63|contact@fractional-cc\.vn|combo_CB-|42%|ĐẶT HÀNG THÀNH CÔNG/.test(h),`Old content in ${file}`);
}
let delivered,toast;
ctx.renderOrderHandoff=(form,order)=>{delivered=order};ctx.showToast=m=>{toast=m};
function form(phone='0985561862',agree=true,honeypot=''){
 const values={customer_name:{value:'Khách kiểm tra',focus(){}},customer_phone:{value:phone,focus(){}},selected_item:{value:'combo_C-CX4',options:[{text:'Combo C–CX4 - 1.237.500đ'}],selectedIndex:0},customer_address:{value:'Địa chỉ kiểm tra'},customer_note:{value:'<img src=x onerror=alert(1)>'},agree_terms:{checked:agree,focus(){}},website_url:{value:honeypot}};
 return {querySelector(selector){return values[selector.match(/name="([^"]+)"/)[1]]}};
}
ctx.handleOrderSubmit({preventDefault(){},target:form()});assert.equal(delivered.selectedItemCode,'combo_C-CX4');assert.equal(delivered.customerPhone,'0985561862');
delivered=undefined;ctx.handleOrderSubmit({preventDefault(){},target:form('123')});assert.equal(delivered,undefined);assert(toast.includes('định dạng'));
delivered=undefined;ctx.handleOrderSubmit({preventDefault(){},target:form('0985561862',false)});assert.equal(delivered,undefined);
delivered=undefined;ctx.handleOrderSubmit({preventDefault(){},target:form('0985561862',true,'spam')});assert.equal(delivered,undefined);
// Exercise the real message renderer with DOM elements: user input must stay text, not HTML.
class Element{constructor(tag){this.tag=tag;this.children=[];}setAttribute(){}append(...els){this.children.push(...els)}appendChild(el){this.children.push(el)}querySelector(){return null}addEventListener(){}scrollIntoView(){}}
const uiCtx={window:{},document:{addEventListener(){},createElement:tag=>new Element(tag)}};
vm.createContext(uiCtx);vm.runInContext(fs.readFileSync('assets/js/main.js','utf8'),uiCtx);
const container=new Element('form');uiCtx.renderOrderHandoff(container,{customerName:'<script>bad</script>',customerPhone:'0985561862',selectedItemName:'Combo C–CX4',customerAddress:'',customerNote:'<img src=x>'});
const box=container.children[0];assert(box.children[2].value.includes('<script>bad</script>'));assert.equal(box.children[3].children[1].href,'https://zalo.me/0985561862');assert(box.children[3].children[2].href.startsWith('mailto:jpbuidangco.ltd@gmail.com?'));assert(box.children[1].textContent.includes('chưa được gửi'));
console.log('PASS: prices, combo selection, all CTA codes, local assets, labels, structured data, form validation and manual order handoff.');
