<?php
	session_start();
	if(!isset($_SESSION['is_login'])){
		header("Location: ../index.php");
	}
	include "../function/koneksi.php";
include "p_header.php";
include "p_menu.php";

 if(isset($_POST['cari']))
	{
		//$produk = 'value="'.$_POST['produk'].'"';
		$periode = 'value="'.$_POST['periode'].'"';
		$idkategori=$_POST['txtKategori'];
		$idproduk=$_POST['txtProduk'];
		
		$kategoriyangdicari='-';
		$produkyangdicari='-';
	}
else
	{
		//$produk = '';
		$periode = '';
		$idproduk='';
		$idkategori='';
	} 


?>

 
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
             Pelanggan Lama Dan Baru
          </h1>
          <ol class="breadcrumb">
            <li class="active"><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
			<li class="active"><a href="customer.php">Pelanggan Lama Dan Baru</li></a>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
			<div class="row">
			<div class="col-md-12">
			<form id="Pencarian" method="POST" action="customer.php">
				<div class="box box-default">
					<div class="box-header with-border">
					  <h3 class="box-title">Kriteria Pencarian</h3>
					</div><!-- /.box-header -->
					<div style="display: block;" class="box-body">
						<div class="col-md-12">
						  <div class="form-group">
							  <label class="col-sm-8" >Kategori Produk</label>
							  <div class="col-sm-8">
							  <?php /* <input class="form-control" name="produk" id="produk" placeholder="Contoh : Barcelona, Milan 2 Rossi 4, ..., dst" type="text" <?php echo $produk;?>>
							   */
							   ?>
									<select class="form-control select2" data-placeholder="Kategori Produk"  name="txtKategori" id="txtKategori" >
									<option value="-">Semua</option>
									<?php
									
									$sqlkategoriparent ='
									SELECT id_parent FROM ps_category WHERE id_parent <> 0 AND id_parent <> 1 and active= 1 GROUP BY id_parent';
									
									$qparent=@mysql_query($sqlkategoriparent);
									
									while($dparent = mysql_fetch_array($qparent)){
										$sqlkategori='
										SELECT a.`id_category`, `name`, `description`, sa.`position` AS `position`, `active` , sa.position POSITION FROM `ps_category` a 
										LEFT JOIN `ps_category_lang` b ON (b.`id_category` = a.`id_category` AND b.`id_lang` = 2 AND b.`id_shop` = 1) 
										LEFT JOIN `ps_category_shop` sa ON (a.`id_category` = sa.`id_category` AND sa.id_shop = 1) 
										WHERE 1 AND `id_parent` = '.$dparent['id_parent'].' and active= 1 ORDER BY sa.`position` ASC';
										$qkategori=@mysql_query($sqlkategori);
											while($dkategori = mysql_fetch_array($qkategori)){
												if ($dkategori['id_category']==$idkategori)
													{
														$dipilih=' selected="selected" ';
														$kategoriyangdicari=$dkategori['id_category'].' - '.$dkategori['name'];
													}
													else
													{
														$dipilih='';
													}
												echo '<option value="'.$dkategori['id_category'].'" '.$dipilih.'> id '.$dkategori['id_category'].' - '.$dkategori['name'].'</option>';
											}
									}
									?>
											
									</select>
							   </div>
						  </div>
						  <div class="form-group">
							  <label class="col-sm-8" >Produk</label>
							  <div class="col-sm-8">
							        <select class="form-control select2" data-placeholder="Produk"  name="txtProduk" id="txtProduk" >
									<option value="-">Semua</option>
									<?php
									$sqlproduk='
									SELECT a.`id_product`, b.`name` AS `name`, `reference`, a.`price` AS `price`, sa.`active` AS `active` , shop.`name` AS `shopname`, a.`id_shop_default`, image_shop.`id_image` AS `id_image`, cl.`name` AS `name_category`, sa.`price`, 0 AS `price_final`, a.`is_virtual`, pd.`nb_downloadable`, sav.`quantity` AS `sav_quantity`, sa.`active`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger` 
									FROM `ps_product` a 
									LEFT JOIN `ps_product_lang` b ON (b.`id_product` = a.`id_product` AND b.`id_lang` = 2 AND b.`id_shop` = 1) 
									LEFT JOIN `ps_stock_available` sav ON (sav.`id_product` = a.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop = 1 AND sav.id_shop_group = 0 ) 
									JOIN `ps_product_shop` sa ON (a.`id_product` = sa.`id_product` AND sa.id_shop = a.id_shop_default) 
									LEFT JOIN `ps_category_lang` cl ON (sa.`id_category_default` = cl.`id_category` AND b.`id_lang` = cl.`id_lang` AND cl.id_shop = a.id_shop_default) 
									LEFT JOIN `ps_shop` shop ON (shop.id_shop = a.id_shop_default) 
									LEFT JOIN `ps_image_shop` image_shop ON (image_shop.`id_product` = a.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = a.id_shop_default) 
									LEFT JOIN `ps_image` i ON (i.`id_image` = image_shop.`id_image`) 
									LEFT JOIN `ps_product_download` pd ON (pd.`id_product` = a.`id_product`) 
									WHERE a.active= 1  ORDER BY a.`id_product` ASC
									';
									
									$qproduk = @mysql_query($sqlproduk);
									while($dproduk = mysql_fetch_array($qproduk)){
										if ($dproduk['id_product']==$idproduk)
										{
											$dipilih=' selected="selected" ';
											$produkyangdicari=$dproduk['name'];
										}
										else
										{
											$dipilih='';
											
										}
										echo '<option value="'.$dproduk['id_product'].'" '.$dipilih.'>'.$dproduk['name'].'</option>';
										}
									?>
									</select>
							   </div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-8" >Periode Transaksi</label>
								<div class="col-sm-8">
									<div class="input-group">
									  <div class="input-group-addon">
										<i class="fa fa-clock-o"></i>
									  </div> 
									  <input type="text" class="form-control pull-right" name="periode" <?php echo $periode;?> id="reservationtime" required />  
									</div><!-- /.input group -->
								</div>
						  </div><!-- /.form group -->
						</div>
					</div><!-- /.box-body -->
					<div style="display: block;" class="box-footer">
						 <button class="btn btn-primary" name="cari">Cari</button> 
					</div>
				  </div>
			</form>
			</div>
			</div>
			<div class="row">
            <div class="col-md-12">
              
                <?php
				if(isset($_POST['cari'])){
					//$produk = $_POST['produk'];
					$periode = $_POST['periode'];
					$idproduk=$_POST['txtProduk'];
					$idkategori=$_POST['txtKategori'];
		
					$_SESSION['periode'] = $_POST['periode'];
					$_SESSION['kategori']=$_POST['txtKategori'];
					$_SESSION['produk']=$_POST['txtProduk'];
		
					//$_SESSION['katakunci'] = $_POST['produk'];
					$experiode = explode(" - ",$periode);
					$periode1 = $experiode[0];
					$periode2 = $experiode[1];
					
					$kriteriakategori='';
					if (0!=strlen($idkategori) && "-"!=$idkategori)
						{
						$kriteriakategori=' and IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2)="'.$idkategori.'" ';
						}
						else
						{
						$kriteriakategori='';	
						}
			
					$kriteriaproduk='';
					if (0!=strlen($idproduk) && "-"!=$idproduk)
						{
						$kriteriaproduk=' and pod.product_id="'.$idproduk.'" ';
						}
						else
						{
						$kriteriaproduk='';	
						}
			
					
					
					$sqlold=' select count(*) as jmlold from
						(
						SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
AND so.id_order < po.id_order LIMIT 1) > 0, "Old", "New") AS new, 
pgl.name AS group_customer, po.invoice_date AS transfer_date, po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) AS id_category
FROM ps_orders po
RIGHT JOIN ps_order_detail pod ON po.id_order=pod.`id_order`
LEFT JOIN ps_customer pc ON po.`id_customer`=pc.`id_customer`
LEFT JOIN ps_group_lang pgl ON pc.id_default_group=pgl.id_group
WHERE 
po.`invoice_date` BETWEEN "'.$periode1.'" AND "'.$periode2.'" '.$kriteriakategori.' '.$kriteriaproduk.' 
group by po.`id_customer`, pc.`lastname`, pc.`firstname` , 
pgl.name, po.invoice_date , po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2)
) tabel where new="Old"
						';
						
						$qold=@mysql_query($sqlold);
						while($dold = mysql_fetch_array($qold)){
							$jmlold=$dold['jmlold'];
						}
						
						$sqlnew=' select count(*) as jmlnew from
						(
						SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
AND so.id_order < po.id_order LIMIT 1) > 0, "Old", "New") AS new, 
pgl.name AS group_customer, po.invoice_date AS transfer_date, po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) AS id_category
FROM ps_orders po
RIGHT JOIN ps_order_detail pod ON po.id_order=pod.`id_order`
LEFT JOIN ps_customer pc ON po.`id_customer`=pc.`id_customer`
LEFT JOIN ps_group_lang pgl ON pc.id_default_group=pgl.id_group
WHERE 
po.`invoice_date` BETWEEN "'.$periode1.'" AND "'.$periode2.'" '.$kriteriakategori.' '.$kriteriaproduk.' 
group by po.`id_customer`, pc.`lastname`, pc.`firstname` , 
pgl.name, po.invoice_date , po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2)
) tabel where new="New";
						';
						$qnew=@mysql_query($sqlnew);
						while($dnew = mysql_fetch_array($qnew)){
							$jmlnew=$dnew['jmlnew'];
						}

				?>
				<div class="box">
				<div class="box-header with-border">
					  <h3 class="box-title">Data Pelanggan | Periode : <?php echo $periode1." s/d ".$periode2;?> | Kategori : <?php echo $kategoriyangdicari; ?> | Produk : <?php echo $produkyangdicari;?></h3>
					  <a href='export.php?data=customer' class="btn btn-success pull-right">Excel</a> | Jumlah Old : <strong><?php echo $jmlold;?></strong> & Jumlah New : <strong><?php echo $jmlnew;?></strong>
				</div><!-- /.box-header -->
				<div class="box-body" style="overflow-x:scroll;">
                  <table id="DataPelanggan" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Sumber</th>
                        <th>Group Pelanggan </th>
                        <th>Pelanggan Lama/Baru</th>
                        <th>Tanggal Transfer</th>
                        <th>ID Transaksi</th>
                        <th>Nama Produk</th>
                        <th>Jumlah Produk Dibeli</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
		/* $sql="
						SELECT SQL_CALC_FOUND_ROWS
             a.id_customer,a.`id_order`, `reference`, `total_paid_tax_incl`, `payment`, a.`date_add` AS `date_add`, 
		a.id_currency,
		a.id_order AS id_pdf,
		c.`lastname` AS customer,
		c.`firstname` AS `sumber`,
		(SELECT NAME FROM ps_group_lang WHERE id_group=c.id_default_group AND id_lang=c.id_lang) AS group_customer,
		IFNULL((SELECT CONCAT(pe.firstname,'', pe.lastname) AS pegawai FROM ps_employee pe 
		WHERE CONCAT(SUBSTR(pe.firstname,1,1),'. ',pe.lastname) LIKE (
		SELECT SUBSTR(message,23,50)
		FROM 
		ps_message m 
		WHERE id_order=a.id_order ORDER BY DATE_ADD DESC LIMIT 1)),'') AS nama_cs,
		osl.`name` AS `osname`,
		IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) AS NEW,
		country_lang.name AS cname,
		SUM((SELECT SUM(product_quantity) FROM ps_order_detail WHERE id_order=a.id_order)) AS jumlah_beli,
		SUM((SELECT SUM(pod.product_quantity) FROM ps_order_detail pod WHERE pod.id_order=a.id_order AND NOT(pod.product_name LIKE '%ongkos kirim%') AND NOT(total_price_tax_incl='0') AND NOT(total_price_tax_excl='0'))) AS dikurangi_ongkirmanual_n_bonus ,
		
		COUNT(*) AS jmlorder,
		FORMAT(SUM((SELECT SUM(ppod.total_price_tax_incl) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=a.id_order AND NOT(ppod.product_name LIKE '%ongkos kirim%') AND NOT(ppod.total_price_tax_incl='0') AND NOT(ppod.total_price_tax_excl='0'))),2)AS totaluang
		
		FROM `ps_orders` a 
		LEFT JOIN `ps_customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `ps_address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `ps_country` country ON address.id_country = country.id_country
		INNER JOIN `ps_country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = 2)
		LEFT JOIN `ps_order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `ps_order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = 2) 
		WHERE a.date_add BETWEEN '".$periode1."' AND '".$periode2."'
		AND a.valid=1
		GROUP BY a.`id_customer`
		ORDER BY a.`id_order` DESC
						"; */
						$sql='
						SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
AND so.id_order < po.id_order LIMIT 1) > 0, "Old", "New") AS new, 
pgl.name AS group_customer, po.invoice_date AS transfer_date, po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) AS id_category
FROM ps_orders po
RIGHT JOIN ps_order_detail pod ON po.id_order=pod.`id_order`
LEFT JOIN ps_customer pc ON po.`id_customer`=pc.`id_customer`
LEFT JOIN ps_group_lang pgl ON pc.id_default_group=pgl.id_group
WHERE 
po.`invoice_date` BETWEEN "'.$periode1.'" AND "'.$periode2.'" '.$kriteriakategori.' '.$kriteriaproduk.' 
group by po.`id_customer`, pc.`lastname`, pc.`firstname` , 
pgl.name, po.invoice_date , po.id_order,
pod.`product_name`,pod.product_quantity,
pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) 
;
						';	
						$q2 = mysql_query($sql);
						$i=1;
						while($d2 = mysql_fetch_array($q2)){
						?>
						<tr>
							<td align="center"><?php echo $i;?></td>
							<td><?php echo $d2['id_customer'];?></td>
							<td><?php echo $d2['customer'];?></td>
							<td><?php echo $d2['sumber'];?></td>
							<td><?php echo $d2['group_customer'];?></td>
							<td><?php echo $d2['new'];?></td>
							<td><?php echo $d2['transfer_date'];?></td>
							<td><?php echo $d2['id_order'];?></td>
							<td><?php echo $d2['product_name'];?></td>
							<td><?php echo $d2['product_quantity'];?></td>
							<?php 
							$i++;
							?>
						 </tr>
						<?php							
						}
					?>
                      
					</tbody>
                  </table>
				
                </div><!-- /.box-body -->
				</div><!-- /.box -->
				
				<?php
				}else{
				?>
				<div class="box">
				<div class="box-header with-border">
				</div><!-- /.box-header -->
				<div class="box-body"><br />
				</div><!-- /.box -->
				</div><!-- /.box -->
				<?php
				}
				?>
				
              
            </div><!-- /.col -->
          </div><!-- /.row -->
		  </form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<?php
include "p_footer.php"
?>
