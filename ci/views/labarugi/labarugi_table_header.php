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
        <th rowspan="3">NAMA PERKIRAAN</th>
        <th rowspan="3">RKAP</th>
        <th colspan="4">KONSOLIDASI <?php echo $mytitle;?></th>
        <th colspan="2">KANTOR <?php echo $mytitle;?></th>
        <th colspan="2"><?php echo $mytitle_proyek;?></th>
      </tr>
      <tr>
        <th colspan="2">BULAN INI</th>
        <th colspan="2">S/D BULAN INI</th>
        <th rowspan="2">BULAN INI</th>
        <th rowspan="2">S/D BULAN INI</th>
        <th rowspan="2">BULAN INI</th>
        <th rowspan="2">S/D BULAN INI</th>
      </tr>
      <tr>
        <th>RUPIAH </th>
        <th>%-TASE</th>
        <th>RUPIAH</th>
        <th>%-TASE</th>
      </tr>
    </thead>
    <tbody>
