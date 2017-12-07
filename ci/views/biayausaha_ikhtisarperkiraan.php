<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	
		<link title=win2k-1 media=all href="<?php echo $BASE_SIMDIV;?>images/calendar2-blue.css" type=text/css rel=stylesheet>
		<link media=all href="<?php echo $BASE_SIMDIV;?>images/assets/styles/popups.css" type=text/css rel=stylesheet>
	</head>
<!-- start report_biaya_usaha_printable_group.html -->
<style type="text/css">
<!--
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
<body>
<title> BIAYA USAHA - IKHTISAR </title> 
<div style="page-break-after:always; margin: 10px 0;">

<div id="popreps">
<table cellspacing="2" cellpadding="2" width="100%">
<thead>
	<tr>
		<td colspan="7" align="left" style="border-right-style:none;"><div id="titles_left">
			<h1>PT. WIKAGEDUNG<span><?php echo $divisi; ?></span></h1>
		</div>
			<br/><br/><br /><br />
			&nbsp;
				<div align="center">
					<h2>IKHTISAR BIAYA USAHA<br />
						BULAN <?php echo strtoupper($month); ?> TAHUN <?php echo $year; ?> </h2>
				</div>
			<div align="left">Dicetak tanggal : : <?php echo $timestamp; ?> Oleh : : <?php echo $pembuat; ?></div></td>
	</tr>
	<tr>
	  	<th colspan="2" rowspan="2">U&nbsp;R&nbsp;A&nbsp;I&nbsp;A&nbsp;N</th>
	  	<th width="12%" rowspan="2">R&nbsp;A&nbsp;B</th>
	  	<th height="21" colspan="2">REALISASI BULAN INI</th>
	  	<th colspan="2">REALISASI S/D BULAN INI</th>
	  </tr>
	<tr>
	    <th width="14%" height="23">Rupiah</th>
		<th width="5%">%</th>
		<th width="14%">Rupiah</th>
		<th width="5%">%</th>
	</tr>

<?php
$total_now = 0;
$total_sd = 0;
while ( ($item = @$datarows->fetchObject()) !== false ){ # start of while 01

	$ql02 = "SELECT DISTINCT substr(j.kdperkiraan, 1,3) AS coa3,  
				SUM(
						CASE
							WHEN dk = 'D' THEN rupiah
							ELSE (rupiah * -1) 
						END 
					) as jml3_thn
				FROM jurnal_v j WHERE 
				j.kdperkiraan like '".$item->coa3."%'
				AND tanggal >= '".$awal_tahun."'
				AND tanggal < '".$bulan_depan."'
				GROUP BY coa3
			";

	$sql02 = $this->db->query($ql02)->row();
	$total_now = $total_now+$item->jml3;
	$total_sd = $total_sd+$sql02->jml3_thn;
	switch ($item->coa3) {
		case '491':
			$name = 'Biaya Pemasaran';
			break;
		case '492':
			$name = 'Biaya Fasilitas Kantor';
			break;
		case '493':
			$name = 'Biaya Informatika';
			break;
		case '494':
			$name = 'Biaya Personalia';
			break;
		case '495':
			$name = 'Biaya Keuangan';
			break;
		case '496':
			$name = 'Biaya Pengembangan';
			break;
		case '497':
			$name = 'Biaya Pengadaan';
			break;
		default:
			$name = 'Tidak Dikenal';
			break;
	}
?>	
<tr class="sums">
	<td style="{style}" NOWRAP colspan="2"><b><?php echo $item->coa3; ?>&nbsp;&nbsp;<?php echo $name ?></b></td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right">0</div></td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php echo $this->format->number($item->jml3); ?></b></div></td>
	<td style="{style}" align="right" nowrap="nowrap">0</td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php  echo $this->format->number($sql02->jml3_thn);?></b></div></td>
	<td style="{style}" align="right" nowrap="nowrap">0</td>
</tr>

<?php 
		$ql03 = "SELECT DISTINCT substr(j.kdperkiraan, 1,4) AS coa4,  
				SUM(
						CASE
							WHEN dk = 'D' THEN rupiah
							ELSE (rupiah * -1) 
						END 
					) as jml4
				FROM jurnal_v j WHERE 
				j.kdperkiraan like '".$item->coa3."%'
				AND tanggal >= '".$bulan_ini."'
				AND tanggal < '".$bulan_depan."'
				GROUP BY coa4
				ORDER BY coa4 ASC
			";

	$sql04 = $this->db->query($ql03)->result();


	foreach ($sql04 as $key) { 
		$ql_sd4 = "SELECT DISTINCT substr(j.kdperkiraan, 1,4) AS coa4,  
				SUM(
						CASE
							WHEN dk = 'D' THEN rupiah
							ELSE (rupiah * -1) 
						END 
					) as jml4_thn
				FROM jurnal_v j WHERE 
				j.kdperkiraan like '".$key->coa4."%'
				AND tanggal >= '".$awal_tahun."'
				AND tanggal < '".$bulan_depan."'
				GROUP BY coa4
			";

		$sql_sd04 = $this->db->query($ql_sd4)->row();
		?>		

	
		<tr class="sums">
			<td style="{style}" NOWRAP colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $key->coa4;?></b>&nbsp;</td>
			<td style="{style}" align="right" nowrap="nowrap"><div align="right">0</div></td><!-- RAB -->
			<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php echo  $this->format->number($key->jml4)?></b></div></td><!-- rupiah -->
			<td style="{style}" align="right" nowrap="nowrap">0</td><!-- % -->
			<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php echo  $this->format->number($sql_sd04->jml4_thn) ?></b></div></td><!-- rupiah -->
			<td style="{style}" align="right" nowrap="nowrap">0</td><!-- % -->
		</tr>
	
		<?php 
			$ql05 = "SELECT
					substr(j.kdperkiraan, 1, 5) AS coa5,
					d.nmperkiraan,
					SUM(CASE
								WHEN dk = 'D' THEN rupiah
								ELSE (rupiah * -1) 
							END 
						) as jml5,
					(SELECT
							SUM(CASE WHEN dk = 'D' THEN
									 rupiah
							 ELSE
									rupiah * -1
							 END ) as rupiah
						FROM
							jurnal_v
						WHERE
							kdperkiraan = j.kdperkiraan
					AND tanggal >= '".$awal_tahun."'
					AND tanggal < '".$bulan_depan."'
					) AS jum5_thn 
					FROM
						jurnal_v j
					JOIN dperkir d ON j.kdperkiraan = d.kdperkiraan
					WHERE
						j.kdperkiraan LIKE '".$key->coa4."%'
					AND tanggal >= '".$bulan_ini."'
					AND tanggal < '".$bulan_depan."'
					GROUP BY
					j.kdperkiraan,
					d.nmperkiraan
					ORDER BY
						coa5 ASC
				";

			$sql05 = $this->db->query($ql05)->result();

			foreach ($sql05 as $isi) { ?>
				 <tr class="{tr_class}">
					<td width="8%" NOWRAP style="{style}"></td>
					<td width="30%" style="{style}"><?php echo $isi->coa5;?></b>&nbsp;<?php echo $isi->nmperkiraan;?></td>
					<td style="{style}" align="right" nowrap="nowrap"><div align="right">0</div></td><!-- RAB -->
					<td style="{style}" align="right" nowrap="nowrap"><div align="right"><?php echo $this->format->number($isi->jml5); ?></div></td><!-- rupiah -->
					<td style="{style}" align="right" nowrap="nowrap">0</td><!-- % -->
					<td style="{style}" align="right" nowrap="nowrap"><div align="right"><?php echo $this->format->number($isi->jum5_thn); ?></div></td><!-- rupiah -->
					<td style="{style}" align="right" nowrap="nowrap">0</td><!-- % -->
				</tr>
	<?php } ?>
<?php 		
	}?>

<?php
}# end of while 01
?>
<!-- <tr>
	<td colspan="2" NOWRAP><div align="left" class="style1" style="font-size:12px">TOTAL BIAYA USAHA </div></td>
	<td align="right"><span class="style1" style="font-size:12px">0</span></td>
	<td align=left><div align="right" class="style1" style="font-size:12px"><b><?php echo $this->format->number($total_now); ?></b></div></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px">0</span></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px"><b><?php echo $this->format->number($total_sd); ?></b></span></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px">0</span></td>
</tr> -->
<?php 
$total_sd_srg = $total_now + $total_sd;
 ?>
<tr class="sums">
	<td style="{style}" NOWRAP colspan="2"><b>TOTAL BIAYA USAHA&nbsp;&nbsp;</b></td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right">0</div></td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php echo $this->format->number($total_now); ?></b></div></td>
	<td style="{style}" align="right" nowrap="nowrap">0</td>
	<td style="{style}" align="right" nowrap="nowrap"><div align="right"><b><?php  echo $this->format->number($total_sd);?><b></div></td>
	<td style="{style}" align="right" nowrap="nowrap">0</td>
</tr>

</table>
</div>
</div>
</body>
<br>
<!-- end report_biaya_usaha_printable_rinci -->
</html>
<!-- end report_biaya_usaha_printable_group.html -->
