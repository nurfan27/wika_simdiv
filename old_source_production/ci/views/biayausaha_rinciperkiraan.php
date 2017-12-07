<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		
	
		<link title=win2k-1 media=all href="<?php echo $BASE_SIMDIV;?>images/calendar2-blue.css" type=text/css rel=stylesheet>
		<!--script src="assets/scripts/adm.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2-en.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2-start.js" type="text/javascript"></script-->


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
						BULAN <?php echo strtoupper($month); ?> TAHUN <?php echo $year; ?> </h2>
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
@$kodegroup2 = array_unique($rincian['kodegroup2']);
$i=0;
$total_mutasi=0;
$total_bius=0;
$kdperkiraaan_total="";
$kode_total="";
$novi_kode="";
$total_bawah = 0;
if (is_array($kodegroup2))
{
foreach($kodegroup2 as $group2)
{
	

	$nama_singkatan_ = @$rincian['novi'][$i];
	//print_r($saldo[$group2]);
	@$total_2 = array_sum($saldo[$group2]);
	@$rincitotal_2 = array_sum($rincian_total[$group2]);
	$total_bawah += $rincitotal_2;
	@$nama___ = $this->mdl_report_biayausaha->get_namaperkiraan($group2);
?>	
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2"<strong><b><?php echo @$group2;?>&nbsp;<?php echo $nama___; ?></b></strong></td>		
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$rincitotal_2);?></strong></div></td>
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$total_2+$rincitotal_2);?></strong></div></td>
	</tr>
<?php 
  $kodegroup = array_unique($rincian['kodegroup'][$group2]);
  foreach($kodegroup as $group1)
  {
	@$kdperkiraan = array_unique($rincian[$group1]['kdperkiraans']);
	@$nama__ = $this->mdl_report_biayausaha->get_namaperkiraan($group1);
	@$total_4 = array_sum($saldo[$group1]);
	@$rincitotal_4 = array_sum($rincian_total[$group1]);
	?>
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2"<strong><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo @$group1;?>&nbsp;<?php echo $nama__; ?></b></strong></td>		
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$rincitotal_4);?></strong></div></td>
		<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$total_4+$rincitotal_4);?></strong></div></td>
	</tr>
<?php
		$subsub_mutasi=0;
		if (is_array($kdperkiraan))
		{
			foreach($kdperkiraan as $kdper)
			{
				
				$subtotal_mutasi = 0;
				if(isset($saldo['debet'][$kdper]))
				{
					$nilai = $saldo['debet'][$kdper];
				}
				else {
					$nilai = 0;
				}
				$nmperkiraan_ = @$rincian['nmperkiraan'][@$kdper];
				if($nmperkiraan_ == '')
				$nmperkiraan_ = $this->mdl_report_biayausaha->get_namaperkiraan(@$kdper);
			
?>
				<tr>
					<td NOWRAP class="garis_bwh2"><strong></strong></td>
					<td colspan="4"  class="garis_bwh2"><strong><?php echo $kdper;?> &nbsp; <?php echo $nmperkiraan_;?></strong></td>
					<td align=left class="garis_bwh2"><div align="right"><strong>Saldo Bulan Lalu :</strong></div></td>
					<td align=left class="garis_bwh2"><div align="right"><strong><?php echo $this->format->number(@$nilai);?></strong></div></td>
				</tr>
		
		
<?php 
				@$novi_kode = @$rincian['nmperkiraan'][@$kdper];
				$mutasi=0;
				$total=$nilai;
				$total_=0;
				$total_ = $total;
				if (@$rincian[@$group1.'-'.@$kdper])
				{
					foreach($rincian[$group1.'-'.$kdper] as $rs)
					{
						//print_r($rs);
						$kdperkiraaan_total = $kdper;
						$mutasi = $rs['rupiah'];
						$total += $mutasi;
						$subtotal_mutasi+=$mutasi;
						$total_ = $total;
						//$total_mutasi+=$subtotal_mutasi;
?>
						<tr>
							<td colspan="2" NOWRAP class="garis_bwh2">&nbsp;</td>
							<td class="garis_bwh2" align=center><?php echo $rs['nobukti']; ?></td>
							<td class="garis_bwh2" align="center" nowrap="nowrap"><?php echo $this->format->tgl(@$rs['tanggal']); ?></td>
							<td class="garis_bwh2" align="left" nowrap="nowrap"><?php echo @$rs['keterangan']; ?></td>
							<td class="garis_bwh2" align="right" nowrap="nowrap"><?php echo $this->format->number(@$mutasi) ;?></td>
							<td class="garis_bwh2" align="right" nowrap="nowrap"><?php echo $this->format->number(@$total);?></td>
						</tr>
				
<?php
			 			$i++;
					}		
				}
				$total_bius+=$total_;
?>
				<tr class="subsums">
					<td colspan="5" align="right" NOWRAP class="garis_bwh2"><div align="right">JUMLAH-<?php echo @$nmperkiraan_; ?></div></td>
					<td align="right" class="garis_bwh2"><strong><?php echo $this->format->number(@$subtotal_mutasi);?></strong></td>
					<td class="garis_bwh2" align="right">&nbsp;</td>
				</tr>
<?php
				$subsub_mutasi+=$subtotal_mutasi;
			}
		}
	}
?>
		<!--<tr class="sums">
			<td colspan="5" align="right" NOWRAP class="garis_bwh2"><div align="right"><b>JUMLAH-PPA-<?=@$nama_singkatan_ ?></b></div></td>
			<td align="right" class="garis_bwh2"><strong><?=$this->format->number(@$subsub_mutasi) ?></strong></td>
			<td class="garis_bwh2" align="right">&nbsp;</td>
		</tr>-->
<?php
	$total_mutasi+=$subsub_mutasi;
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
