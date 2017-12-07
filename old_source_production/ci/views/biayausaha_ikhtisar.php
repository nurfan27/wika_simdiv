<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		
	
		<link title=win2k-1 media=all href="<?=$BASE_SIMDIV?>images/calendar2-blue.css" type=text/css rel=stylesheet>
		<!--script src="assets/scripts/adm.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2-en.js" type="text/javascript"></script>
		<script src="assets/scripts/calendar2-start.js" type="text/javascript"></script-->


		<link media=all href="<?=$BASE_SIMDIV?>images/assets/styles/popups.css" type=text/css rel=stylesheet>
	</head>
<!-- start report_biaya_usaha_printable_group.html -->
<style type="text/css">
<!--
.style1 {
	color: #FF0000;
	font-weight: bold;
}
-->

	table.dash {
   border: 1px dashed #cccccc;
   border-collapse: collapse;
}
.garis_bwh2 {
   border: 0px dashed #cccccc;
}
tbody tr:nth-child(even) {
	   background-color: #f2f2f2;
	}
</style>
<body>
<title> BIAYA USAHA- IKHTISAR </title> 
<div style="page-break-after:always; margin: 10px 0;">

<div id="popreps">
<table cellspacing="2" cellpadding="2" width="100%">
<thead>
	<tr>
		<td colspan="7" align="left" style="border-right-style:none;"><div id="titles_left">
			<h1>PT. WIKAGEDUNG<span><?=$divisi ?></span></h1>
		</div>
			<br/><br/><br /><br />
			&nbsp;
				<div align="center">
					<h2>IKHTISAR BIAYA USAHA<br />
						BULAN <?php echo $month; ?> TAHUN <?php echo $year; ?> </h2>
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
@$kode = array_unique($ikhtisar['kodes']);

//(ksort($kode));
$i=0;
$j=0;
$k=0;
$total_mutasi=0;
$total_bius=0;
$kdperkiraaan_total="";
$kode_total="";
$total_bulanini=0;
$total_sdbulanini=0;
$total_rab=0;
$total_persen1=0;
$total_persen2=0;
foreach ($kode as $item)
{
	$k++;
?>
	<tr class="{tr_class}">
		<td style="{style}" NOWRAP colspan="2"><?php echo $item; ?> &nbsp;<?php echo $ikhtisar['novi'][$item]; ?></td>
		<td style="{style}" align=left><div align="right">&nbsp;</div></td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
	</tr>
<? 
/*$kodegroup = array_unique($ikhtisar[$item]['kodegroup']);
  foreach($kodegroup as $group1)
  {
  */	
?>
	<!--<tr class="{tr_class}">
		<td style="{style}" NOWRAP colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$group1 ?></td>
		<td style="{style}" align=left><div align="right">&nbsp;</div></td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
		<td style="{style}" align="right" nowrap="nowrap">&nbsp;</td>
	</tr>-->
<? 
	//$kdperkiraan = array_unique($ikhtisar[$item]['kdperkiraan'][$group1]);
	@$kdperkiraan = array_unique($ikhtisar[$item]['kdperkiraans']);
	//print_r($kodegroup);
	
	$sub_total=0;
	$sub_total_now=0;
	$sub_total_rab=0;
	$sub_persen1=0;
	$sub_persen2=0;
	foreach($kdperkiraan as $kdper)
	{
		$j++;
		
		$subtotal_mutasi = 0;
		if(isset($ikhtisar2[$item][$kdper]['rupiah']))
		{
			$sub_total+=$ikhtisar2[$item][$kdper]['rupiah'];
			$rupiah = $ikhtisar2[$item][$kdper]['rupiah'];
		}
		else {
			$sub_total+= 0;
			$rupiah = 0;
		}
		
		if(isset($ikhtisar[$item]['rupiah'][$kdper]))
		{
			$sub_total_now+=$ikhtisar[$item]['rupiah'][$kdper];
			$rupiahnow = $ikhtisar[$item]['rupiah'][$kdper];
		}
		else {
			$sub_total_now+= 0;
			$rupiahnow = 0;
		}
		
		if(isset($ikhtisar2[$item][$kdper]['rab']))
		{
			$rab=$ikhtisar2[$item][$kdper]['rab'];
			$sub_total_rab+=$ikhtisar2[$item][$kdper]['rab'];
		}
		else {
			$rab= 0;
			$sub_total_rab+=0;
		}
		
		if($rab > 0)
		{
			$persentase = ($rupiah/$rab)*100;
			$persentase_now = ($rupiahnow/$rab)*100;
			$sub_persen1+=$persentase;
			$sub_persen2+=$persentase_now;
		}
		else {
			$persentase = 0;
			$persentase_now = 0;
		}
		$nmperkiraan_ = @$ikhtisar['nmperkiraan'][@$kdper];
		if($nmperkiraan_ == '')
		$nmperkiraan_ = $this->mdl_report_biayausaha->get_namaperkiraan(@$kdper);
		
?>
	<tr class="{tr_class}">
		<td width="8%" NOWRAP style="{style}"></td>
		<td width="30%" style="{style}"><?php echo $kdper;?> &nbsp; <?php echo $nmperkiraan_; ?></td>
		<td style="{style}" align="right"><?php echo $this->format->number($rab)?></td>
		<td style="{style}" align=left><div align="right"><?php echo $this->format->number(@$rupiah);?></div></td>
		<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->double(@$persentase); ?></td>
		<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->number(@$rupiahnow); ?></td>
		<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->double(@$persentase_now); ?></td>
	</tr>
		
<? 
	
	 $i++;
	}
   //}
	 @$sub_persen1=(@$sub_total/@$sub_total_rab)*100;
	 @$sub_persen2=(@$sub_total_now/@$sub_total_rab)*100;
?>
		<tr class="sums">
			<td style="{style}" NOWRAP></td>
			<td style="{style}"></td>
			<td style="{style}" align="right"><?php echo $this->format->number(@$sub_total_rab); ?></td>
			<td style="{style}" align=left><div align="right"><?php echo $this->format->number(@$sub_total); ?></div></td>
			<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->double(@$sub_persen1); ?></td>
			<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->number(@$sub_total_now); ?></td>
			<td style="{style}" align="right" nowrap="nowrap"><?php echo $this->format->double(@$sub_persen2); ?></td>
		</tr>
<? 
	$j=0;	
	$total_bulanini+=$sub_total;
	$total_sdbulanini+=$sub_total_now;
	$total_rab+=$sub_total_rab;
}
	@$total_persen1=(@$total_bulanini/@$total_rab)*100;
	@$total_persen2=(@$total_sdbulanini/@$total_rab)*100;
?>
<tr>
	<td colspan="7">&nbsp;</td>
</tr>
<tr>
	<td colspan="2" NOWRAP><div align="left" class="style1" style="font-size:12px">TOTAL BIAYA USAHA </div></td>
	<td align="right"><span class="style1" style="font-size:12px"><?php echo $this->format->number(@$total_rab); ?></span></td>
	<td align=left><div align="right" class="style1" style="font-size:12px"><?php echo $this->format->number(@$total_bulanini); ?></div></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px"><?php echo $this->format->double(@$total_persen1); ?></span></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px"><?php echo $this->format->number(@$total_sdbulanini); ?></span></td>
	<td align="right" nowrap="nowrap"><span class="style1" style="font-size:12px"><?php echo $this->format->double(@$total_persen2); ?></span></td>
	</tr>
</table>
</div>
</div>
</body>
<br>

<!-- end report_biaya_usaha_printable_rinci -->
</html>
<!-- end report_biaya_usaha_printable_group.html -->
