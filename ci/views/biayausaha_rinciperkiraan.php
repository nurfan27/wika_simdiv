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
	<td colspan="5" NOWRAP class="garis_bwh2"><b>49&nbsp;PENGEMBANGAN SDM</b></td>		
	<td align=left class="garis_bwh2"><div align="right"></div></td>
	<td align=left class="garis_bwh2"><div align="right"></div></td>
</tr>

<?php
$pos_3dgt = 0;
$pos_4dgt = 0;
while ( ($item = @$datarows->fetchObject()) !== false ){ # start of while 01
$saldo_bln_lalu = 0;
$cek_3dgt = $item->kdper_3dgt;
$cek_4dgt = $item->kdper_4dgt;
?>	

<?php if ($cek_3dgt != $pos_3dgt): $pos_3dgt = $item->kdper_3dgt;?>
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2"><b><?php echo $item->kdper_3dgt;?>&nbsp;</b></td>		
		<td align=left class="garis_bwh2"><div align="right"></div></td>
		<td align=left class="garis_bwh2"><div align="right"></div></td>
	</tr>
<?php endif ?>
<?php if ($cek_4dgt != $pos_4dgt):  $pos_4dgt = $item->kdper_4dgt;?>
	<tr class="sums">
		<td colspan="5" NOWRAP class="garis_bwh2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $item->kdper_4dgt;?></b>&nbsp;</td>		
		<td align=left class="garis_bwh2"><div align="right"></div></td>
		<td align=left class="garis_bwh2"><div align="right"></div></td>
	</tr>
<?php endif ?>

	<!-- start sub header -->
	<?php 
	$ql02 = "
			SELECT
				SUM(CASE WHEN dk = 'D' THEN
			               rupiah
			           ELSE
			              rupiah * -1
			           END ) as rupiah
			FROM
				jurnal_v
			WHERE
				kdperkiraan = '".$item->kdper_5dgt."'
			AND tanggal >= '2017-01-01'
			AND tanggal < '2017-11-01'
			";

	$sql02 = $this->db->query($ql02)->row();
	$saldo_bln_lalu = $sql02->rupiah; 
	?>

	<tr>
		<td NOWRAP class="garis_bwh2">&nbsp;</td>
		<td colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo $item->kdper_5dgt;?>&nbsp;<?php echo $item->nmperkiraan;?></b></td>		
		<td class="garis_bwh2" align="right" nowrap="nowrap"><b>saldo Bulan Lalu</b></td>
		<td class="garis_bwh2" align="right" nowrap="nowrap"><?php echo $this->format->number($saldo_bln_lalu) ?></td>
	</tr>
	<!-- end sub header -->

	<!-- start transaksi -->
	<?php 
	$ql = "
		SELECT
			j.kdperkiraan AS coa,
			d.nmperkiraan,
			j.tanggal,
			j.nobukti,
			j.keterangan,
			j.rupiah
		FROM
			jurnal_v j
		JOIN dperkir d ON j.kdperkiraan = d.kdperkiraan
		WHERE
			j.kdperkiraan = '".$item->kdper_5dgt."'
		AND tanggal > '2017-10-01'
		AND tanggal < '2017-11-01'
		GROUP BY
		j.kdperkiraan,
			d.nmperkiraan,
			j.tanggal,
			j.nobukti,
			j.keterangan,
			j.rupiah
		ORDER BY
			coa ASC
		";

	$sql = $this->db->query($ql)->result();


	$sub_total = 0;
	foreach ($sql as $key) {
		$saldo_bln_lalu = $saldo_bln_lalu - $key->rupiah;
		$sub_total = $sub_total + $key->rupiah;
		?>
		<tr>
			<td colspan="2" >&nbsp;</td>		
			<td class="garis_bwh2" align="center" nowrap="nowrap"><?php echo $key->nobukti ?></td>
			<td class="garis_bwh2" align="center" nowrap="nowrap"><?php echo $this->format->tgl(substr($key->tanggal,0,11)) ?></td>
			<td class="garis_bwh2" align="left" nowrap="nowrap"><?php echo $key->keterangan ?></td>
			<td class="garis_bwh2" align="right" nowrap="nowrap"><?php echo $this->format->number($key->rupiah) ?></td>
			<td class="garis_bwh2" align="right" nowrap="nowrap"><?php echo $this->format->number($saldo_bln_lalu) ?></td>
		</tr>
	<?php }
	$grand_total = $grand_total + $sub_total; 
	?>
	<!-- end transaksi -->
	<tr>
		<td colspan="5" class="garis_bwh2" align="right" nowrap="nowrap">JUMLAH</td>
		<td class="garis_bwh2" align="right" nowrap="nowrap"><b><?php echo $this->format->number($sub_total) ?></b></td>
		<td class="garis_bwh2" align="right" nowrap="nowrap"></td>
	</tr>
<?php
 }# end of while 01
?>

<tr>
	<td colspan="7">&nbsp;</td>
</tr>
<tr>
	<td colspan="5" NOWRAP class="garis_bwh2"><div align="right"><strong>TOTAL</strong></div></td>
	<td align="right" nowrap="nowrap" class="garis_bwh2" style="font: 12px 'Arial';"><?php echo $this->format->number($grand_total) ?></strong></td>
	<td align="right" nowrap="nowrap" class="garis_bwh2" style="font: 12px 'Arial';">0</strong></td>
</tr>
</table>
</div>
</div>
</body>
<br>
<!-- end report_biaya_usaha_printable_rinci -->
</html>
