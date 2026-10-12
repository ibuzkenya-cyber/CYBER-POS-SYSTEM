<?php
require_once __DIR__.'/../config/config.php';header('Content-Type: application/json');
if(!logged_in()){http_response_code(401);echo json_encode(['ok'=>false,'error'=>'Not logged in']);exit;}
$data=json_decode(file_get_contents('php://input'),true);
try{
$pdo->beginTransaction();
$cart=$data['cart']??[];$discount=(float)($data['discount']??0);$paid=(float)($data['amount_paid']??0);$method=$data['payment_method']??'cash';
if(!$cart)throw new Exception('Cart is empty');
$sub=0;$hasP=false;$hasS=false;
foreach($cart as $i=>$x){$qty=(float)$x['qty'];if($qty<=0)throw new Exception('Invalid quantity');$price=(float)$x['price'];$sub+=$qty*$price;if($x['type']==='product')$hasP=true;else $hasS=true;}
$total=max(0,$sub-$discount);$balance=max(0,$total-$paid);
$receipt='R'.date('YmdHis').rand(10,99);
$type=$hasP&&$hasS?'mixed':($hasS?'service':'product');
$s=$pdo->prepare("INSERT INTO sales(receipt_no,customer_id,user_id,subtotal,discount,total,amount_paid,balance,payment_method,sale_type) VALUES(?,?,?,?,?,?,?,?,?,?)");
$s->execute([$receipt,$data['customer_id']?:null,$_SESSION['user']['id'],$sub,$discount,$total,$paid,$balance,$method,$type]);$saleId=$pdo->lastInsertId();
foreach($cart as $x){
$qty=(float)$x['qty'];$price=(float)$x['price'];$buy=0;
if($x['type']==='product'){
$q=$pdo->prepare("SELECT * FROM products WHERE id=? FOR UPDATE");$q->execute([(int)$x['id']]);$p=$q->fetch();if(!$p)throw new Exception('Product not found');if($p['stock_qty']<$qty)throw new Exception('Insufficient stock for '.$p['name']);$buy=$p['buying_price'];
$pdo->prepare("UPDATE products SET stock_qty=stock_qty-? WHERE id=?")->execute([$qty,$x['id']]);
$pdo->prepare("INSERT INTO stock_movements(product_id,movement_type,qty,unit_cost,reference) VALUES(?,?,?,?,?)")->execute([$x['id'],'sale',$qty,$buy,$receipt]);
}
$pdo->prepare("INSERT INTO sale_items(sale_id,item_type,item_id,item_name,qty,unit_price,buying_price,line_total) VALUES(?,?,?,?,?,?,?,?)")->execute([$saleId,$x['type'],(int)$x['id'],$x['name'],$qty,$price,$buy,$qty*$price]);
}
if($paid>0 && in_array($method,['cash','mpesa','bank']))$pdo->prepare("INSERT INTO payments(sale_id,payment_method,amount) VALUES(?,?,?)")->execute([$saleId,$method,$paid]);
$pdo->commit();echo json_encode(['ok'=>true,'receipt'=>$receipt]);
}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);}
