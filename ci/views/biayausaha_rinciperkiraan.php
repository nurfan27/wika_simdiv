<?php 

 ?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		
	
		<link title=win2k-1 media=all href="<?php echo $BASE_SIMDIV;?>images/calendar2-blue.css" type=text/css rel=stylesheet>
		<link media=all href="<?php echo $BASE_SIMDIV;?>images/assets/styles/popups.css" type=text/css rel=stylesheet>
	</head>
<!-- start report_biaya_usaha_printable_rinci-->
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>
<body>
<title> BIAYA USAHA - RINCIAN </title> 
<div style="page-break-after:always; margin: 10px 0;">
<div id="popreps">
<table cellspacing="2" cellpadding="2" width="100%" border="1">
<thead>
		<tr>
		<td colspan="8" align="left" style="border-right-style:none;">
        
        <div id="titles_left">
			<h1>PT. WIKAGEDUNG<br><span style="font-size:18px"><?php echo $divisi; ?></span></h1>
		</div>
			&nbsp;
	  <div align="center">
			<h2>RINCIAN BIAYA USAHA<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BULAN <?php echo strtoupper($month); ?> TAHUN <?php echo $year; ?> </h2>
		</div>
	<div align="left">Dicetak tanggal : : <?php echo $timestamp; ?> Oleh : : <?php echo $pembuat; ?></div></td>
	</tr>
	  <th bgcolor="#666666"><span class="style1">Kode</span></th>	
	  <th bgcolor="#666666"><span class="style1">Nama Perkiraan  </span></th>
	  <th bgcolor="#666666"><span class="style1">Nomor-Bukti</span></th>
	  <th bgcolor="#666666"><span class="style1">Tanggal</span></th>
	  <th bgcolor="#666666"><span class="style1">Uraian</span></th>
	  <th bgcolor="#666666"><span class="style1">Mutasi</span></th>
	  <th bgcolor="#666666"><span class="style1">Saldo</span></th>
</thead>                                                                                                   

<?php 
//@$kodegroup2 = array_unique($rincian['kodegroup2']);
$i=0;
$total_mutasi=0;
$total_bius=0;
$kdperkiraaan_total="";
$kode_total="";
$novi_kode="";
$total_bawah = 0;

?>

<tr class="sums">
	<td colspan="5" NOWRAP class="garis_bwh2"<strong><b>49&nbsp;PENGEMBANGAN SDM</b></strong></td>		
	<td align=left class="garis_bwh2"><div align="right"></div></td>
	<td align=left class="garis_bwh2"><div align="right"></div></td>
</tr>

<?php 
while ( ($item = @$groupcoa_3dgt->fetchObject()) !== false ){
?>	
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2"<strong><b><?php echo $item->coa;?>&nbsp;<?php echo $nama___; ?></b></strong></td>		
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$rincitotal_2);?></strong></div></td>
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$total_2+$rincitotal_2);?></strong></div></td>
	</tr>
	<?php $groupcoa_4dgt = $this->mdl_report_biayausaha->get_dperkir_4($item->coa); 
	while ( ($item2 = @$groupcoa_4dgt->fetchObject()) !== false ){?>
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2">&nbsp;&nbsp;<strong><b><?php echo $item2->coa;?>&nbsp;<?php echo $nama___; ?></b></strong></td>		
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$rincitotal_2);?></strong></div></td>
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$total_2+$rincitotal_2);?></strong></div></td>
	</tr>
		<?php 
		$saldo_tahunlalu = $this->mdl_report_biayausaha->saldo_tahunlalu(strtolower($div),$month2,$month5,$uker_,'t');
		while ( ($item_isi = $saldo_tahunlalu->fetchObject()) !== false ){ //var_dump($item_isi);
		?>

		
		<?php
		if (substr($item_isi->kdperkiraan, 0,4) == $item2->coa) {
				$nmperkiraan_ = $this->mdl_report_biayausaha->get_namaperkiraan_dperkir($item_isi->kdperkiraan);
				if(isset($item_isi->debit))
				{
					$nilai = $item_isi->total;
				}
				else {
					$nilai = 0;
				}?>
				<tr>
					<td colspan="5"  class="garis_bwh2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $item_isi->kdperkiraan;?> &nbsp; <?php echo $nmperkiraan_;?></strong></td>
					<td align=left class="garis_bwh2"><div align="right"><strong>Saldo Bulan Lalu :</strong></div></td>
					<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$nilai);?></strong></div></td>
				</tr>
<?php 
			}
		}
	}
} 
?>

<tr>
	<td colspan="7">&nbsp;</td>
</tr>
<tr>
	<td colspan="5" NOWRAP class="garis_bwh2"><div align="right"><strong>TOTAL</strong></div></td>
	<td align="right" nowrap="nowrap" class="garis_bwh2" style="font: 12px 'Arial';"><strong><?php echo $this->format->number(@$total_bawah); ?></strong></td>
	<td align="right" nowrap="nowrap" class="garis_bwh2" style="font: 12px 'Arial';"><strong><?php echo $this->format->number(@$total_bius); ?></strong></td>
</tr>
</table>
</div>
</div>
</body>
<br>

<!-- end report_biaya_usaha_printable_rinci -->
</html>
