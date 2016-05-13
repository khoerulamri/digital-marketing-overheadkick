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
             Penjualan
          </h1>
          <ol class="breadcrumb">
            <li class="active"><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
			<li class="active"><a href="sales.php">Penjualan</li></a>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
			<div class="row">
			<div class="col-md-12">
			<form id="Pencarian" method="POST" action="sales.php">
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
							
							$date=$periode1;
							$end_date=$periode2;
							
							$ts1 = date_create($date);
							$ts2 = date_create($end_date);

							$seconds_diff = date_diff($ts1,$ts2);
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
					
				?>
				<div class="box">
				<div class="box-header with-border">
				</div><!-- /.box-header -->
				<div class="box-body" style="overflow-x:scroll;">
					<table class="tree table table-striped table-bordered table-hover">
						<tr>
							<th rowspan="2" width="5%" class="text-center">No</th>
							<th rowspan="2" class="text-center">Nama Kategori</th>
							<th rowspan="2" class="text-center">Kelompok Kategori</th>
							<th colspan="<?php echo $seconds_diff->format("%d")+1;?>" class="text-center">Tgl</th>
						</tr>
						<tr>
				<?php 
				
				//loop range tanggal
				while (strtotime($date) <= strtotime($end_date)) {
					echo "<th class='text-center'>".date("d",strtotime($date))."</th>";
					$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
					}
					
				?>
						</tr>
				<?php
				$sql="
						SELECT pc.id_category,pcl.`name` AS category_name,
						(SELECT NAME FROM ps_category_lang WHERE id_category=pc.id_parent AND id_lang='2') AS group_parent
						FROM ps_category pc 
						LEFT JOIN ps_category_lang pcl ON pc.`id_category`=pcl.`id_category`
						WHERE pc.`active`='1' AND pc.id_category>36 
						ORDER BY is_root_category DESC, id_parent,group_parent,  pcl.`name`;
					";
				$q2 = mysql_query($sql);
						$i=1;
						$nomor=1;
						while($d2 = mysql_fetch_array($q2)){
							echo '<tr class="treegrid-'.$i.'">
										<td  class="text-center">'.$nomor.'</td>
										<td>'.$d2['category_name'].'</td>
										<td>'.$d2['group_parent'].'</td>';
									

							$date=$periode1;
							$end_date=$periode2;
							//hitung jumlahnya setua tanggal
							while (strtotime($date) <= strtotime($end_date)) {
								$tgldari=date("Y-m-d",strtotime($date)).' 00:00:00';
								$tglsampai=date("Y-m-d",strtotime($date)).' 23:59:59';;
								$sqltgl=" SELECT IFNULL(SUM(product_quantity),0) AS qty FROM 
									(
									SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
									IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
									AND so.id_order < po.id_order LIMIT 1) > 0, 'Old', 'New') AS NEW, 
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
									WHERE po.`invoice_date` BETWEEN '".$tgldari."' AND '".$tglsampai."' 
									GROUP BY po.`id_customer`, pc.`lastname`, pc.`firstname` , 
									pgl.name, po.invoice_date , po.id_order,
									pod.`product_name`,pod.product_quantity,
									pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) 
									) tabel WHERE tabel.id_category='".$d2['id_category']."' ;
									";
								$qsqltgl2 = mysql_query($sqltgl);
								while($dsqltgl = mysql_fetch_array($qsqltgl2)){
										echo '<td width="3%"  class="text-center">'.$dsqltgl['qty'].'</td>';
								}
								$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
															
								
								}
								
							echo '
								  </tr>';
							
							//detail sub produknya
							$sqldetail="SELECT pp.id_product,pp.`id_category_default`,ppl.name AS product_name FROM 
								ps_product pp, ps_product_lang ppl 
								WHERE ppl.`id_lang`='2' AND pp.`id_product`=ppl.`id_product` AND pp.`id_category_default`='".$d2['id_category']."'
								ORDER BY ppl.name  ;";
								
							$j=$i;
							$i++;
							$qsqldetail = mysql_query($sqldetail);
							while($dsqldetail = mysql_fetch_array($qsqldetail)){
								echo '<tr class="treegrid-'.$i.' treegrid-parent-'.$j.' success">
									<td  class="text-center"></td>
									<td>'.$dsqldetail['product_name'].'</td>
									<td>'.$d2['category_name'].'</td>';
									
								//jmldetailsub			
								$date=$periode1;
								$end_date=$periode2;
								//hitung jumlahnya setua tanggal
								while (strtotime($date) <= strtotime($end_date)) {
									$tgldari=date("Y-m-d",strtotime($date)).' 00:00:00';
									$tglsampai=date("Y-m-d",strtotime($date)).' 23:59:59';;
									$sqltgl=" SELECT *, IFNULL(SUM(product_quantity),0) AS qty FROM 
										(
										SELECT po.`id_customer`, pc.`lastname` AS customer, pc.`firstname` AS sumber, 
										IF((SELECT so.id_order FROM `ps_orders` so WHERE so.id_customer = po.id_customer 
										AND so.id_order < po.id_order LIMIT 1) > 0, 'Old', 'New') AS NEW, 
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
										WHERE po.`invoice_date` BETWEEN '".$tgldari."' AND '".$tglsampai."' 
										GROUP BY po.`id_customer`, pc.`lastname`, pc.`firstname` , 
										pgl.name, po.invoice_date , po.id_order,
										pod.`product_name`,pod.product_quantity,
										pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2) 
										) tabel WHERE tabel.product_id='".$dsqldetail['id_product']."' GROUP BY product_id
										";
									$qsqltgl2 = mysql_query($sqltgl);
									$nsqltgl2 = mysql_num_rows($qsqltgl2);
									if (0==$nsqltgl2)
									{
										echo '<td width="3%"  class="text-center">0</td>';	
									}
									else{
										while($dsqltgl = mysql_fetch_array($qsqltgl2)){
											echo '<td width="3%"  class="text-center">'.$dsqltgl['qty'].'</td>';
										}
									}
									
									$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));	
									
								}
								echo '</tr>';
								$i++;
							}
							$nomor++;
						}
				?>
						
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
