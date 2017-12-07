    <?php if($h_d!='ST' && $h_d!='T') { ?>
    <tr>
    	<td><?php echo $nmperkiraan; ?></td>
    	<td align="right"><?php if($h_d!='H') { echo '0'; } ?></td>
        <td align="right"><?php echo $proyek_sd_thn_lalu; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $proyek_sd_bln_lalu; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $proyek_bln_ini; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $proyek_sd_bln_ini; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
        <td align="right"><?php echo $proyek_sd_thn_ini; ?></td>
        <td align="right"><?php if($h_d!='H') { echo '0,00'; } ?></td>
    </tr>
    <?php } ?>