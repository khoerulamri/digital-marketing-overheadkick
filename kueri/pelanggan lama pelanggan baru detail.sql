SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
AND so.id_order < po.id_order LIMIT 1) > 0, "Old", "New") AS NEW, 
pgl.name AS group_customer, po.invoice_date AS transfer_date, po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, 
IFNULL((SELECT NAME FROM ps_product_lang  WHERE id_product=pod.product_id),2) AS product_real_name,
IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) AS id_category,
IFNULL((SELECT DISTINCT pcl.`name` AS category_name 
FROM ps_product p
LEFT JOIN ps_category_lang pcl ON pcl.`id_category`=p.`id_category_default` 
WHERE p.id_product=pod.product_id AND pcl.id_lang='2' GROUP BY pcl.`name`),2) AS category_name
FROM ps_orders po
RIGHT JOIN ps_order_detail pod ON po.id_order=pod.`id_order`
LEFT JOIN ps_customer pc ON po.`id_customer`=pc.`id_customer`
LEFT JOIN ps_group_lang pgl ON pc.id_default_group=pgl.id_group
WHERE 
po.`invoice_date` BETWEEN "2016-03-01 00:00:00" AND " 2016-05-12 23:59:59" 
GROUP BY po.`id_customer`, pc.`lastname`, pc.`firstname` , 
pgl.name, po.invoice_date , po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) 