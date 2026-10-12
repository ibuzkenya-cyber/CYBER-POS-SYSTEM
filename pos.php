<?php require 'includes/header.php';
$products=$pdo->query("SELECT id,name,sku,selling_price,stock_qty FROM products WHERE active=1 ORDER BY name")->fetchAll();
$services=$pdo->query("SELECT id,name,price FROM services WHERE active=1 ORDER BY name")->fetchAll();
$customers=$pdo->query("SELECT id,name,phone FROM customers ORDER BY name")->fetchAll();
?><h1>Point of Sale</h1><div class="pos-layout"><div><input class="search" id="search" placeholder="Search products/services..." oninput="filterItems()"><div class="items" id="items">
<?php foreach($products as $p):?><div class="item searchable" data-name="<?=e(strtolower($p['name'].' '.$p['sku']))?>" onclick='addItem(<?=json_encode(["type"=>"product","id"=>(int)$p["id"],"name"=>$p["name"],"price"=>(float)$p["selling_price"],"stock"=>(float)$p["stock_qty"]])?>)'><b><?=e($p['name'])?></b><br>KSh <?=money($p['selling_price'])?><br><small>Stock: <?=$p['stock_qty']?></small></div><?php endforeach;?>
<?php foreach($services as $s):?><div class="item searchable" data-name="<?=e(strtolower($s['name']))?>" onclick='addItem(<?=json_encode(["type"=>"service","id"=>(int)$s["id"],"name"=>$s["name"],"price"=>(float)$s["price"],"stock"=>999999])?>)'><b><?=e($s['name'])?></b><br>KSh <?=money($s['price'])?></div><?php endforeach;?></div></div>
<div class="panel" style="margin-top:0"><h2>Cart</h2><div id="cart"></div><div class="total">Total: KSh <span id="total">0.00</span></div>
<select id="customer"><option value="">Walk-in Customer</option><?php foreach($customers as $c):?><option value="<?=$c['id']?>"><?=e($c['name'])?> - <?=e($c['phone'])?></option><?php endforeach;?></select><br><br>
<input id="discount" type="number" step=".01" placeholder="Discount" value="0" oninput="render()"><br><br>
<select id="payment"><option value="cash">Cash</option><option value="mpesa">M-Pesa</option><option value="bank">Bank</option><option value="credit">Credit</option><option value="partial">Partial</option></select><br><br>
<input id="paid" type="number" step=".01" placeholder="Amount paid" value="0"><br><br><button onclick="checkout()">Complete Sale & Print Receipt</button></div></div>
<script>
let cart=[];
function addItem(x){let a=cart.find(i=>i.type==x.type&&i.id==x.id);if(a)a.qty++;else cart.push({...x,qty:1});render();}
function render(){let h='';let sub=0;cart.forEach((x,i)=>{let t=x.qty*x.price;sub+=t;h+=`<div class="cart-row"><span>${esc(x.name)}<br><small>${x.price.toFixed(2)}</small></span><input type="number" min="1" value="${x.qty}" onchange="cart[${i}].qty=this.value;render()"><span>${t.toFixed(2)}</span><button onclick="cart.splice(${i},1);render()">×</button></div>`});document.getElementById('cart').innerHTML=h;let d=parseFloat(document.getElementById('discount').value||0);document.getElementById('total').textContent=Math.max(0,sub-d).toFixed(2);}
function filterItems(){let q=document.getElementById('search').value.toLowerCase();document.querySelectorAll('.searchable').forEach(x=>x.style.display=x.dataset.name.includes(q)?'block':'none');}
function esc(s){return s.replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));}
async function checkout(){if(!cart.length)return alert('Cart is empty');let total=parseFloat(document.getElementById('total').textContent),paid=parseFloat(document.getElementById('paid').value||0);let r=await fetch('api/sale.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({cart,customer_id:document.getElementById('customer').value||null,discount:parseFloat(document.getElementById('discount').value||0),payment_method:document.getElementById('payment').value,amount_paid:paid})});let j=await r.json();if(j.ok){window.open('receipt.php?id='+encodeURIComponent(j.receipt),'_blank');cart=[];document.getElementById('paid').value=0;render();}else alert(j.error);}
render();
</script><?php require 'includes/footer.php';?>