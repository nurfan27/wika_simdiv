	<?php if($h_d!='ST' && $h_d!='T') { ?>
    <tr>
    	<td><?php echo $nmperkiraan; ?></td>
    	<td align="right"><?php if($h_d!='H') { echo '0'; } ?></td>
        <td align="right"><?php echo $konsol_divisi_bln_ini; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $konsol_divisi_sd_bln_ini; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $divisi_bln_ini; ?></td>
        <td align="right"><?php echo $divisi_sd_bln_ini; ?></td>
        <td align="right"><?php echo $proyek_bln_ini; ?></td>
        <td align="right"><?php echo $proyek_sd_bln_ini; ?></td>
    </tr>
    <?php } ?>