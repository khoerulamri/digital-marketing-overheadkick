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
		$grouppelanggan = 'value="'.$_POST['group'].'"';
		$periode = 'value="'.$_POST['periode'].'"';
	}
else
	{
		$grouppelanggan = '';
		$periode = '';
	}									

?>

 
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
             Wilayah Sebaran Penjualan
          </h1>
          <ol class="breadcrumb">
            <li class="active"><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
			<li class="active"><a href="salesarea.php">Wilayah Sebaran Penjualan</li></a>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
			<div class="row">
			<div class="col-md-12">
			<form id="Pencarian" method="POST" action="salesarea.php">
				<div class="box box-default">
					<div class="box-header with-border">
					  <h3 class="box-title">Kriteria Pencarian</h3>
					</div><!-- /.box-header -->
					<div style="display: block;" class="box-body">
						<div class="col-md-12">
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
						
						
						<?php
						if(isset($_POST['cari'])){
							
							$periode = $_POST['periode'];
							$_SESSION['periode'] = $_POST['periode'];
							$experiode = explode(" - ",$periode);
							$periode1 = $experiode[0];
							$periode2 = $experiode[1];
						}
						?>
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
					
					//echo $k1."<br />".$k2a."<br />".$k2b;
				?>
				<div class="box">
				<div class="box-header with-border">
					  <h3 class="box-title">Data Area Penjualan Berdasarkan Alamat <strong>Penagihan</strong></h3> | Periode : <?php echo $periode1." s/d ".$periode2;?> <a href='export.php?data=sainvoice' class="btn btn-success pull-right">Excel</a>
				</div><!-- /.box-header -->
				<div class="callout callout-info">
                    <h4>Informasi : </h4>
                    <p><strong>Total Produk Terjual</strong> : Jumlah Total Produk yang ada dibelanjaan/Order dikurangi Bonus (Produk harga Rp 0,-) dan Ongkos Kirim Manual</p>
					<p><strong>Total Uang Yang Dibelanjakan</strong> : Total Uang masuk dikurangi ongkos kirim</p>
                </div>
				<div class="box-body" style="overflow-x:scroll;">
                  <table id="TotalAreaPenjualanBasedPenagihan" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Kecamatan</th>
                        <th>Kota/Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Negara</th>
                        <th>Qty Order Valid</th>
						<th>Persentase Order Valid</th>
                        <th>Total Produk Terjual</th>
                        <th>Total Uang Yang Dibelanjakan</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
						$sql="
						
						SELECT SUBSTRING(pa.city, 1, LOCATE('-',pa.city)-1) AS kecamatan,
						SUBSTRING(pa.city, LOCATE('-',pa.city) + 1) AS kabupaten,pa.id_country, pa.id_state,
						ps.name AS state_name,
						pcl.`name` AS country_name,
						CONVERT(COUNT(*)/2,UNSIGNED INTEGER) AS validorder,
						ROUND((((COUNT(*)/2)/
							(SELECT COUNT(*)
							FROM `ps_orders` po
							LEFT JOIN ps_address pa ON pa.id_address=po.id_address_invoice
							WHERE po.current_state<>'6'
							AND po.current_state<>'7'
							AND po.current_state<>'8' and po.invoice_date between '".$periode1."' and '".$periode2."')
						)*100),4) AS persentase,

						CONVERT(SUM((SELECT SUM(ppod.product_quantity) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order ))/2,UNSIGNED INTEGER) AS tpmix,
						CONVERT(SUM((SELECT SUM(ppod.product_quantity) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order AND NOT(ppod.product_name LIKE '%ongkos kirim%') AND NOT(ppod.total_price_tax_incl='0') AND NOT(ppod.total_price_tax_excl='0')))/2,UNSIGNED INTEGER) AS tp,
						FORMAT(SUM((SELECT SUM(ppod.total_price_tax_incl) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order AND NOT(ppod.product_name LIKE '%ongkos kirim%') AND NOT(ppod.total_price_tax_incl='0') AND NOT(ppod.total_price_tax_excl='0')))/2,2)AS totaluang
						FROM `ps_orders` po
						LEFT JOIN ps_address pa ON pa.id_address=po.id_address_invoice
						LEFT JOIN ps_state ps ON pa.`id_state`=ps.`id_state` 
						LEFT JOIN `ps_country_lang` pcl ON pa.`id_country`=pcl.`id_country`
						WHERE po.current_state<>'6'
						AND po.current_state<>'7'
						AND po.current_state<>'8' and po.invoice_date between '".$periode1."' and '".$periode2."'
						GROUP BY kecamatan,kabupaten,pa.`id_state`, pa.`id_country`
						ORDER BY validorder DESC, pa.`id_country`,pa.`id_state`,kabupaten,kecamatan
								";
						
						$q2 = mysql_query($sql);
						$i=1;
						while($d2 = mysql_fetch_array($q2)){
						?>
						<tr>
							<td align="center"><?php echo $i;?></td>
							<td><?php echo $d2['kecamatan'];?></td>
							<td><?php echo $d2['kabupaten'];?></td>
							<td><?php echo $d2['state_name'];?></td>
							<td><?php echo $d2['country_name'];?></td>
							<td><?php echo $d2['validorder'];?></td>
							<td><?php echo $d2['persentase'];?> % </td>
							<td><?php echo $d2['tp'];?></td>
							<td><?php echo $d2['totaluang'];?></td>
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
				
				<div class="box">
				<div class="box-header with-border">
					  <h3 class="box-title">Data Area Penjualan Berdasarkan Alamat <strong>Pengiriman</strong></h3> | Periode : <?php echo $periode1." s/d ".$periode2;?> <a href='export.php?data=sakirim' class="btn btn-success pull-right">Excel</a>
				</div><!-- /.box-header -->
				<div class="callout callout-info">
                    <h4>Informasi : </h4>
                    <p><strong>Total Produk Terjual</strong> : Jumlah Total Produk yang ada dibelanjaan/Order dikurangi Bonus (Produk harga Rp 0,-) dan Ongkos Kirim Manual</p>
					<p><strong>Total Uang Yang Dibelanjakan</strong> : Total Uang masuk dikurangi ongkos kirim</p>
                </div>
				<div class="box-body" style="overflow-x:scroll;">
                  <table id="TotalAreaPenjualanBasedPengiriman" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Kecamatan</th>
                        <th>Kota/Kabupaten</th>
                        <th>Provinsi</th>
                        <th>Negara</th>
                        <th>Qty Valid Order</th>
                        <th>Persentase Valid Order</th>
                        <th>Total Produk Terjual</th>
                        <th>Total Uang Yang Dibelanjakan</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
						$sql="
						
						SELECT SUBSTRING(pa.city, 1, LOCATE('-',pa.city)-1) AS kecamatan,
						SUBSTRING(pa.city, LOCATE('-',pa.city) + 1) AS kabupaten,pa.id_country, pa.id_state,
						ps.name AS state_name,
						pcl.`name` AS country_name,
						CONVERT(COUNT(*)/2,UNSIGNED INTEGER) AS validorder,
						ROUND((((COUNT(*)/2)/
							(SELECT COUNT(*)
							FROM `ps_orders` po
							LEFT JOIN ps_address pa ON pa.id_address=po.id_address_delivery
							WHERE po.current_state<>'6'
							AND po.current_state<>'7'
							AND po.current_state<>'8' and po.invoice_date between '".$periode1."' and '".$periode2."')
						)*100),4) AS persentase,

						CONVERT(SUM((SELECT SUM(ppod.product_quantity) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order ))/2,UNSIGNED INTEGER) AS tpmix,
						CONVERT(SUM((SELECT SUM(ppod.product_quantity) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order AND NOT(ppod.product_name LIKE '%ongkos kirim%') AND NOT(ppod.total_price_tax_incl='0') AND NOT(ppod.total_price_tax_excl='0')))/2,UNSIGNED INTEGER) AS tp,
						FORMAT(SUM((SELECT SUM(ppod.total_price_tax_incl) AS jumlah FROM ps_order_detail ppod WHERE ppod.id_order=po.id_order AND NOT(ppod.product_name LIKE '%ongkos kirim%') AND NOT(ppod.total_price_tax_incl='0') AND NOT(ppod.total_price_tax_excl='0')))/2,2)AS totaluang
						FROM `ps_orders` po
						LEFT JOIN ps_address pa ON pa.id_address=po.id_address_delivery
						LEFT JOIN ps_state ps ON pa.`id_state`=ps.`id_state` 
						LEFT JOIN `ps_country_lang` pcl ON pa.`id_country`=pcl.`id_country`
						WHERE po.current_state<>'6'
						AND po.current_state<>'7'
						AND po.current_state<>'8' and po.invoice_date between '".$periode1."' and '".$periode2."'
						GROUP BY kecamatan,kabupaten,pa.`id_state`, pa.`id_country`
						ORDER BY validorder DESC, pa.`id_country`,pa.`id_state`,kabupaten,kecamatan
						";
						
						
						$q2 = mysql_query($sql);
						$i=1;
						while($d2 = mysql_fetch_array($q2)){
						?>
						<tr>
							<td align="center"><?php echo $i;?></td>
							<td><?php echo $d2['kecamatan'];?></td>
							<td><?php echo $d2['kabupaten'];?></td>
							<td><?php echo $d2['state_name'];?></td>
							<td><?php echo $d2['country_name'];?></td>
							<td><?php echo $d2['validorder'];?></td>
							<td><?php echo $d2['persentase'];?> % </td>
							<td><?php echo $d2['tp'];?></td>
							<td><?php echo $d2['totaluang'];?></td>
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
