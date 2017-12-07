<table cellspacing="2" cellpadding="2" width="100%">
    <thead>
      <tr>
        <td colspan="11" align="left" style="border-right-style:none;">
        <div id="titles_left">
          <h1>PT. WIKAGEDUNG<span><?php echo $divisi;?></span><span>No Bukti : <?php if($nobukti=='PUSAT') { echo 'Pusat'; } elseif($nobukti=='PROYEK') { echo 'Proyek'; } elseif($nobukti=='ALL') { echo 'Pusat & Proyek'; } ?></span></h1>
        </div><br><br>
         
            <div align="center" style="font-size:14px;">
              <b>LAPORAN LABA RUGI <?php echo $mytitle;?></b>
            </div>
            <div align="center">
               Bulan <?php echo $namabulan;?> Tahun <?php echo $tahun;?>
            </div>
          <div align="left">Dicetak tanggal : : <?php echo date('d M Y H:i:s', strtotime('now'));?> Oleh : : <?php echo $admin;?></div></td>
      </tr>
      <tr>
        <th rowspan="3">NAMA PERKIRAAN</th>
        <th rowspan="3">RKAP</th>
        <th colspan="10"><?php echo $mytitle;?></th>
      </tr>
      <tr>
        <th colspan="2">S/D TAHUN LALU</th>
        <th colspan="2">S/D BULAN LALU</th>
        <th colspan="2">BULAN INI</th>
        <th colspan="2">S/D BULAN INI</th>
        <th colspan="2">S/D TAHUN INI</th>
      </tr>
      <tr>
        <th>RUPIAH</th>
        <th>%-TASE</th>
        <th>RUPIAH</th>
        <th>%-TASE</th>
        <th>RUPIAH</th>
        <th>%-TASE</th>
        <th>RUPIAH</th>
        <th>%-TASE</th>
        <th>RUPIAH</th>
        <th>%-TASE</th>
      </tr>
    </thead>
    <tbody>
