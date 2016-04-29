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
             Pelanggan Terbaik
          </h1>
          <ol class="breadcrumb">
            <li class="active"><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
			<li class="active"><a href="bestcustomer.php">Pelanggan Terbaik</li></a>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
			<div class="row">
			<div class="col-md-12">
			<form id="Pencarian" method="POST" action="bestcustomer.php">
				<div class="box box-default">
					<div class="box-header with-border">
					  <h3 class="box-title">Kriteria Pencarian</h3>
					</div><!-- /.box-header -->
					<div style="display: block;" class="box-body">
						<div class="col-md-12">
							<div class="form-group">
							  <label class="col-sm-8" >Group Pelanggan</label>
							  <div class="col-sm-8">
							  <input class="form-control" name="group" id="group" placeholder="Contoh : Customer, Dropship Gold, ..., dst" type="text" <?php echo $grouppelanggan;?>>
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
						
						
						<?php
						if(isset($_POST['cari'])){
							
							$periode = $_POST['periode'];
							$_SESSION['periode'] = $_POST['periode'];
							$_SESSION['katakunci'] = $_POST['group'];
							$experiode = explode(" - ",$periode);
							$periode1 = $experiode[0];
							$periode2 = $experiode[1];
							
							$grouppelanggan =$_POST['group'];
							$kuerilike='';
							if (0!=strlen($grouppelanggan))
								{
									$i=0;
									$explodegrouppelanggan = explode(",", $grouppelanggan);
									foreach ($explodegrouppelanggan as $egp)
									{
										if (0==$i)
										{
										$kuerilike=' where (gl.name LIKE "%'.$egp.'%" ';
										}
										else
										{
										$kuerilike=' or gl.name LIKE "%'.$egp.'%" '	;	
										}
									}
									$kuerilike=$kuerilike.')';
								}
							
						}else{}
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
              <div class="box">
                <?php
				if(isset($_POST['cari'])){
					
					//echo $k1."<br />".$k2a."<br />".$k2b;
				?>
				<div class="box-header with-border">
					  <h3 class="box-title">Tabel Pelanggan Terbaik</h3>
					  <a href='export.php?data=bestcustomer' class="btn btn-success pull-right">Excel</a>
				</div><!-- /.box-header -->
				<div class="callout callout-info">
                    <h4>Informasi : </h4>
                    <p><strong>Total Produk Terjual</strong> : Jumlah Total Produk yang ada dibelanjaan/Order dikurangi Bonus (Produk harga Rp 0,-) dan Ongkos Kirim Manual</p>
                </div>
				<div class="box-body" style="overflow-x:scroll;">
				
                  <table id="bestcustomer" class="table table-striped table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>ID Customer</th>
                        <th>Nama Pelanggan</th>
                        <th>Sumber</th>
                        <th>Group Customer</th>
                        <th>Total Uang Yang Dibelanjakan</th>
                        <th>Jumlah Order Valid</th>
                        <th>Total Produk Yang Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
					<?php
						$sql='
								SELECT SQL_CALC_FOUND_ROWS c.`id_customer`, c.`lastname`, c.`firstname`, c.`email`, gl.name AS customer_group,
									FORMAT(IFNULL((
										SELECT ROUND(SUM(IFNULL(op.`amount`, 0) / cu.conversion_rate), 2)
										FROM `ps_orders` o
										LEFT JOIN `ps_order_payment` op ON o.reference = op.order_reference
										LEFT JOIN `ps_currency` cu ON o.id_currency = cu.id_currency
										WHERE o.id_customer = c.id_customer
										AND o.invoice_date BETWEEN "'.$periode1.'"
										AND "'.$periode2.'"
									), 0),2) AS totalMoneySpent,
									IFNULL((
										SELECT COUNT(*)
										FROM `ps_orders` o
										WHERE o.id_customer = c.id_customer
										AND o.invoice_date BETWEEN "'.$periode1.'"
										AND "'.$periode2.'"
									), 0) AS totalValidOrders,
									CONVERT((SUM(IFNULL((SELECT SUM(pod.product_quantity) FROM ps_orders o INNER JOIN ps_order_detail pod ON o.id_order=pod.id_order AND NOT(pod.product_name LIKE "%ongkos kirim%") AND NOT(total_price_tax_incl="0") AND NOT(total_price_tax_excl="0") WHERE o.id_customer=c.id_customer AND o.invoice_date BETWEEN "'.$periode1.'" AND "'.$periode2.'"),0))),UNSIGNED INTEGER) AS dikurangi_ongkirmanual_n_bonus 
								FROM `ps_customer` c
								LEFT JOIN `ps_guest` g ON c.`id_customer` = g.`id_customer`
								LEFT JOIN `ps_connections` co ON g.`id_guest` = co.`id_guest`
								LEFT JOIN `ps_group_lang` gl ON gl.`id_group` = c.`id_default_group`
								 '.$kuerilike.' 
								GROUP BY c.`id_customer`, c.`lastname`, c.`firstname`, c.`email`
								order by totalMoneySpent desc
								';
						
						$q2 = mysql_query($sql);
						$i=1;
						while($d2 = mysql_fetch_array($q2)){
						?>
						<tr>
							<td align="center"><?php echo $i;?></td>
							<td align="center"><?php echo $d2['id_customer'];?></td>
							<td><?php echo $d2['lastname'];?></td>
							<td><?php echo $d2['firstname'];?></td>
							<td><?php echo $d2['customer_group'];?></td>
							<td><?php echo $d2['totalMoneySpent'];?></td>
							<td><?php echo $d2['totalValidOrders'];?></td>
							<td><?php echo $d2['dikurangi_ongkirmanual_n_bonus'];?></td>
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
				
				<?php
				}else{
				?>
				<div class="box-body">
					<br />
				<?php
				}
				?>
				
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
		  </form>
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<?php
include "p_footer.php"
?>
