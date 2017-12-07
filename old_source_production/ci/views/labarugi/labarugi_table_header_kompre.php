<table cellspacing="2" cellpadding="2" width="100%">
    <thead>
      <tr>
        <td colspan="11" align="left" style="border-right-style:none;">
        <div id="titles_left">
          <h1>PT. WIKAGEDUNG<span><?php echo $divisi;?></span></h1>
        </div><br><br>
         
            <div align="center" style="font-size:14px;">
              <b>LAPORAN LABA RUGI <?php echo $mytitle;?></b>
            </div>
            <div align="center">
               Bulan <?php echo $bulan;?> Tahun <?php echo $tahun;?>
            </div>
          <div align="left">Dicetak tanggal : : <?php echo date('d M Y H:i:s', strtotime('now'));?> Oleh : : <?php echo $admin;?></div></td>
      </tr>
      <tr>
        <th>NAMA PERKIRAAN</th>
        <th><?php echo $tahun_ini;?></th>
        <th><?php echo $tahun_lalu;?></th>
      </tr>
    </thead>
    <tbody>
