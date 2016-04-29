<?php 
session_start();
if(!isset($_SESSION['is_login']))
{
	header("Location: ../index.php");
}
else
{
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2013 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2013 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.9, 2013-06-02
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set('memory_limit', '1024M');
set_time_limit ( 0 );
date_default_timezone_set('Asia/Jakarta');

if (PHP_SAPI == 'cli')
	die('This should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../assets/plugins/PHPExcel/PHPExcel.php';
include "../function/koneksi.php";

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$data=$_GET['data'];
			
			

switch ($data)
	  { 
	  case 'sainvoice':
			$periode=$_SESSION['periode'];
			$experiode = explode(" - ",$periode);
			$periode1 = $experiode[0];
			$periode2 = $experiode[1];
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
							AND po.current_state<>'8' and po.date_add between '".$periode1."' and '".$periode2."')
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
						AND po.current_state<>'8' and po.date_add between '".$periode1."' and '".$periode2."'
						GROUP BY kecamatan,kabupaten,pa.`id_state`, pa.`id_country`
						ORDER BY validorder DESC, pa.`id_country`,pa.`id_state`,kabupaten,kecamatan
								";
								
			$hasil = @mysql_query($sql);
			$nomor=1;
			$namafile='AreaSalesBaseInvoiceAddress';
			while ($baca = @mysql_fetch_array($hasil)) 
					{
						$nomor++;
						$kolomA='A'.$nomor;
						$kolomB='B'.$nomor;
						$kolomC='C'.$nomor;
						$kolomD='D'.$nomor;
						$kolomE='E'.$nomor;
						$kolomF='F'.$nomor;
						$kolomG='G'.$nomor;
						$kolomH='H'.$nomor;
						$kolomI='I'.$nomor;


					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($kolomA, ($nomor-1))
								->setCellValue($kolomB, $baca['kecamatan'])
								->setCellValue($kolomC, $baca['kabupaten'])
								->setCellValue($kolomD, $baca['state_name'])
								->setCellValue($kolomE, $baca['country_name'])
								->setCellValue($kolomF, $baca['validorder'])
								->setCellValue($kolomG, $baca['persentase'])
								->setCellValue($kolomH, $baca['tp'])
								->setCellValue($kolomI, $baca['totaluang']);
					}
					// Set document properties
					$objPHPExcel->getProperties()->setCreator("Digital Marketing")
												 ->setLastModifiedBy("Digital Marketing")
												 ->setTitle("Digital Marketing")
												 ->setSubject("Digital Marketing")
												 ->setDescription("Digital Marketing")
												 ->setKeywords("Digital Marketing")
												 ->setCategory("Digital Marketing");


					// Add some data
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Nomor')
								->setCellValue('B1', 'Kecamatan')
								->setCellValue('C1', 'Kota/Kabupaten')
								->setCellValue('D1', 'Provinsi')
								->setCellValue('E1', 'Negara')
								->setCellValue('F1', 'Qty Order Valid')
								->setCellValue('G1', 'Persentase Order Valid')
								->setCellValue('H1', 'Total Produk Terjual')
								->setCellValue('I1', 'Total Uang Yang Dibelanjakan');

					
		   break;
	  case 'sakirim':
			$periode=$_SESSION['periode'];
			$experiode = explode(" - ",$periode);
			$periode1 = $experiode[0];
			$periode2 = $experiode[1];
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
							AND po.current_state<>'8' and po.date_add between '".$periode1."' and '".$periode2."')
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
						AND po.current_state<>'8' and po.date_add between '".$periode1."' and '".$periode2."'
						GROUP BY kecamatan,kabupaten,pa.`id_state`, pa.`id_country`
						ORDER BY validorder DESC, pa.`id_country`,pa.`id_state`,kabupaten,kecamatan
						";
								
			$hasil = @mysql_query($sql);
			$nomor=1;
			$namafile='AreaSalesBaseDeliveryAddress';
			while ($baca = @mysql_fetch_array($hasil)) 
					{
						$nomor++;
						$kolomA='A'.$nomor;
						$kolomB='B'.$nomor;
						$kolomC='C'.$nomor;
						$kolomD='D'.$nomor;
						$kolomE='E'.$nomor;
						$kolomF='F'.$nomor;
						$kolomG='G'.$nomor;
						$kolomH='H'.$nomor;
						$kolomI='I'.$nomor;


					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($kolomA, ($nomor-1))
								->setCellValue($kolomB, $baca['kecamatan'])
								->setCellValue($kolomC, $baca['kabupaten'])
								->setCellValue($kolomD, $baca['state_name'])
								->setCellValue($kolomE, $baca['country_name'])
								->setCellValue($kolomF, $baca['validorder'])
								->setCellValue($kolomG, $baca['persentase'])
								->setCellValue($kolomH, $baca['tp'])
								->setCellValue($kolomI, $baca['totaluang']);
					}
					// Set document properties
					$objPHPExcel->getProperties()->setCreator("Digital Marketing")
												 ->setLastModifiedBy("Digital Marketing")
												 ->setTitle("Digital Marketing")
												 ->setSubject("Digital Marketing")
												 ->setDescription("Digital Marketing")
												 ->setKeywords("Digital Marketing")
												 ->setCategory("Digital Marketing");


					// Add some data
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Nomor')
								->setCellValue('B1', 'Kecamatan')
								->setCellValue('C1', 'Kota/Kabupaten')
								->setCellValue('D1', 'Provinsi')
								->setCellValue('E1', 'Negara')
								->setCellValue('F1', 'Qty Order Valid')
								->setCellValue('G1', 'Persentase Order Valid')
								->setCellValue('H1', 'Total Produk Terjual')
								->setCellValue('I1', 'Total Uang Yang Dibelanjakan');

			
		   break; 
	  case 'bestcustomer':
			
			$katakunci=$_SESSION['katakunci'];
			$periode=$_SESSION['periode'];
			$experiode = explode(" - ",$periode);
			$periode1 = $experiode[0];
			$periode2 = $experiode[1];
			
			$grouppelanggan =$katakunci;
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
						
						
			$hasil = @mysql_query($sql);
			$nomor=1;
			$namafile='BestCustomer';
			while ($baca = @mysql_fetch_array($hasil)) 
					{
						$nomor++;
						$kolomA='A'.$nomor;
						$kolomB='B'.$nomor;
						$kolomC='C'.$nomor;
						$kolomD='D'.$nomor;
						$kolomE='E'.$nomor;
						$kolomF='F'.$nomor;
						$kolomG='G'.$nomor;
						$kolomH='H'.$nomor;


					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($kolomA, ($nomor-1))
								->setCellValue($kolomB, $baca['id_customer'])
								->setCellValue($kolomC, $baca['lastname'])
								->setCellValue($kolomD, $baca['firstname'])
								->setCellValue($kolomE, $baca['customer_group'])
								->setCellValue($kolomF, $baca['totalMoneySpent'])
								->setCellValue($kolomG, $baca['totalValidOrders'])
								->setCellValue($kolomH, $baca['dikurangi_ongkirmanual_n_bonus']);
					}
					// Set document properties
					$objPHPExcel->getProperties()->setCreator("Digital Marketing")
												 ->setLastModifiedBy("Digital Marketing")
												 ->setTitle("Digital Marketing")
												 ->setSubject("Digital Marketing")
												 ->setDescription("Digital Marketing")
												 ->setKeywords("Digital Marketing")
												 ->setCategory("Digital Marketing");


					// Add some data
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Nomor')
								->setCellValue('B1', 'ID Customer')
								->setCellValue('C1', 'Nama Pelanggan')
								->setCellValue('D1', 'Sumber')
								->setCellValue('E1', 'Group Customer')
								->setCellValue('F1', 'Total Uang Yang Dibelanjakan')
								->setCellValue('G1', 'Jumlah Order Valid')
								->setCellValue('H1', 'Total Produk Yang Terjual');

			
		   break; 
	 case 'customer':
			//$katakunci=$_SESSION['katakunci'];
			$periode=$_SESSION['periode'];
			$idproduk=$_SESSION['produk'];
			$idkategori=$_SESSION['kategori'];
		
			$experiode = explode(" - ",$periode);
			$periode1 = $experiode[0];
			$periode2 = $experiode[1];
			
			//$produk =$katakunci;
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
			pod.product_id, IFNULL((SELECT id_category_default FROM ps_product WHERE id_product=pod.product_id),2)';					
			
			$hasil = @mysql_query($sql);
			$nomor=1;
			$namafile='Customer';
			while ($baca = @mysql_fetch_array($hasil)) 
					{
						$nomor++;
						$kolomA='A'.$nomor;
						$kolomB='B'.$nomor;
						$kolomC='C'.$nomor;
						$kolomD='D'.$nomor;
						$kolomE='E'.$nomor;
						$kolomF='F'.$nomor;
						$kolomG='G'.$nomor;
						$kolomH='H'.$nomor;
						$kolomI='I'.$nomor;
						$kolomJ='J'.$nomor;

					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($kolomA, ($nomor-1))
								->setCellValue($kolomB, $baca['id_customer'])
								->setCellValue($kolomC, $baca['customer'])
								->setCellValue($kolomD, $baca['sumber'])
								->setCellValue($kolomE, $baca['group_customer'])
								->setCellValue($kolomF, $baca['new'])
								->setCellValue($kolomG, $baca['transfer_date'])
								->setCellValue($kolomH, $baca['id_order'])
								->setCellValue($kolomI, $baca['product_name'])
								->setCellValue($kolomJ, $baca['product_quantity']);
					}
					// Set document properties
					$objPHPExcel->getProperties()->setCreator("Digital Marketing")
												 ->setLastModifiedBy("Digital Marketing")
												 ->setTitle("Digital Marketing")
												 ->setSubject("Digital Marketing")
												 ->setDescription("Digital Marketing")
												 ->setKeywords("Digital Marketing")
												 ->setCategory("Digital Marketing");


					// Add some data
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A1', 'Nomor')
								->setCellValue('B1', 'ID Customer')
								->setCellValue('C1', 'Nama Pelanggan')
								->setCellValue('D1', 'Sumber')
								->setCellValue('E1', 'Group Customer')
								->setCellValue('F1', 'Pelanggan Baru')
								->setCellValue('G1', 'Tanggal Transfer')
								->setCellValue('H1', 'ID Order')
								->setCellValue('I1', 'Nama Produk')
								->setCellValue('J1', 'Jumlah Produk Yang Dibeli');
		   break; 
	 
	 
	  default: echo "<script>window.location.href='home.php' </script>";
	  ;
	  }


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($namafile);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$namafile.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
}
?>
