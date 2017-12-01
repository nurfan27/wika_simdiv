<?php
class Mdl_report_biayausaha extends Mdl_core {
	
	private $_DB;
	
	function __construct() {
		parent::__construct();		
		$this->_DB = parent::$API_DB;
	}

	public function get_dperkir_3(){
		$sql = "SELECT DISTINCT substr(kdperkiraan,1,3) AS coa  FROM dperkir WHERE substr(dperkir.kdperkiraan, 1 ,2) = '49' ORDER BY coa  ASC";

		return $this->_DB->query($sql);
	}

	public function get_dperkir_4($key){
		$sql = "SELECT DISTINCT substr(dperkir.kdperkiraan, 1 ,4) as coa  FROM dperkir WHERE substr(dperkir.kdperkiraan,1,3) like '".$key."%' ORDER BY coa  ASC;";

		return $this->_DB->query($sql);
	}

	public function saldo_tahunlalu($div='',$month1='',$month2='',$uker_='',$sort='')
	{
		/*$sql = "
			SELECT a.kdperkiraan ,(CASE WHEN a.dk='D' THEN SUM(a.rupiah) END) AS debet ,
			(CASE WHEN a.dk='K' THEN sum(a.rupiah) END) AS kredit 
			FROM jurnal_{$div} a WHERE a.kdperkiraan LIKE '7%' AND 
			DATE(a.tanggal) <= '{$month1}' AND DATE(a.tanggal) >= '{$month2}' AND isdel='f' 
			AND isapp='t' GROUP BY a.kdperkiraan,a.dk ORDER BY a.kdperkiraan,a.dk;
		";*/
		$tambah='';
		if($sort=='t')
		{
			$tambah2 = " kdperkiraan ASC";
			$header = " kdperkiraan ";
			$group = " kdperkiraan ";
		}
		else {
			$tambah2 = "kduker ASC, kdperkiraan ASC";
			$header = "kduker,  kdperkiraan";
			$group = "kduker,  kdperkiraan";
		}
		if($uker_)
		{
			$tambah = " AND b.kduker = '{$uker_}' ";
		}
		$sql = "
		SELECT {$header}, SUM(debet) AS debet, SUM(kredit) AS kredit, SUM(debet) - SUM(kredit) as total FROM
		(
			SELECT 
				substr(A .nobukti, 1, 2) as kduker, a.jid,
				a.kdperkiraan, (CASE WHEN a.dk='D' THEN a.rupiah ELSE 0 END) AS debet , 
				(CASE WHEN a.dk='K' THEN a.rupiah ELSE 0 END) AS kredit 
			FROM jurnal_{$div} a 
			LEFT JOIN anggar As b ON b.kdperkiraan = a.kdperkiraan and b.kduker = substr(a.nobukti, 1 ,2)
			WHERE a.kdperkiraan LIKE '49%' AND (a.tanggal BETWEEN '{$month1}' AND '{$month2}' ) AND isdel='f' AND isapp='t' {$tambah}
			GROUP BY b.kduker,a.kdperkiraan,a.nobukti,a.dk, a.rupiah, a.jid
			ORDER BY b.kduker ASC ,a.kdperkiraan ASC,a.dk
		)
		tabel
		GROUP BY {$group}
		ORDER BY {$tambah2}
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}

	public function data_transaksi_coa($bln_ini,$bln_depan){
		$sql = "
			SELECT
				substr(j.kdperkiraan, 1, 3) AS kdper_3dgt,
				substr(j.kdperkiraan, 1, 4) AS kdper_4dgt,
				substr(j.kdperkiraan, 1, 5) AS kdper_5dgt,
				d.nmperkiraan
			FROM
				jurnal_v j
			JOIN dperkir d ON j.kdperkiraan = d.kdperkiraan
			WHERE
				j.kdperkiraan LIKE '49%'
			AND tanggal >= '".$bln_ini."'
			AND tanggal < '".$bln_depan."'
			GROUP BY
			j.kdperkiraan,
				d.nmperkiraan
			ORDER BY
				kdper_5dgt ASC
		";
		return $this->_DB->query($sql);
	}

	public function data_transaksi_detail($key,$bln_ini,$bln_depan){
		$sql = "
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
				j.kdperkiraan = '".$key."'
			AND tanggal >= '".$bln_ini."'
			AND tanggal < '".$bln_depan."'
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

		return $this->_DB->query($sql);
	}

	public function data_ikhtisar_coa3(){
		$sql = "
				SELECT DISTINCT substr(j.kdperkiraan, 1,3) AS coa3,  
				SUM(
						CASE
							WHEN dk = 'D' THEN rupiah
							ELSE (rupiah * -1) 
						END 
					) as jml3
				FROM jurnal_v j WHERE 
				j.kdperkiraan like '49%'
				AND tanggal >= '2017-09-01'
				AND tanggal < '2017-10-01'
				GROUP BY coa3
			";

		return $this->_DB->query($sql);
	}

	public function rincian_realisasi($div='',$month1='',$month2='')
	{
		$sql = "
			SELECT t.kdwilayah as kduker, t.namasingkatan, t.kdperkiraan, t.nobukti, t.keterangan, t.nmperkiraan, sum(d) as debet
			FROM (
						SELECT substr(a.nobukti, 1, 2) as kdwilayah, d.namasingkatan,  a.kdperkiraan, a.nobukti, a.keterangan, e.nmperkiraan,
									(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) ELSE SUM(a.rupiah*-1) END) AS d
						FROM jurnal_{$div} a
						-- RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
						LEFT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
						LEFT JOIN z_groupname_bius d ON d.kddivisi = a.kddivisi AND d.kduker = substr(a.nobukti,1,2)
						LEFT JOIN dperkir e ON e.kdperkiraan = a.kdperkiraan
						WHERE a.kdperkiraan LIKE '49%' AND DATE(a.tanggal) <= '{$month2}' AND DATE(a.tanggal) >= '{$month1}'
						GROUP BY a.nobukti, d.namasingkatan,a.kdperkiraan, a.keterangan, e.nmperkiraan, a.dk
						ORDER BY a.nobukti, a.kdperkiraan
			) t
			GROUP BY t.kdwilayah, t.namasingkatan, t.kdperkiraan, t.nobukti, t.keterangan, t.nmperkiraan
			ORDER BY t.kdwilayah
		";
		return $this->_DB->query($sql);
	}
	public function rincian_bius($div='',$year='',$month='')
	{
		
	}
	public function rincian_biayausaha($div='',$year='',$month='',$uker_='')
	{
		/*$sql = "
				  SELECT a.kdperkiraan,b.nmperkiraan, a.nobukti, to_char(date(a.tanggal),'DD-MM-YYYY') as tanggal,
       									a.keterangan, a.rupiah, a.dk
                  FROM jurnal_{$div} a,dperkir b
                  WHERE a.kdperkiraan=b.kdperkiraan AND a.kdperkiraan LIKE '7%'
                    AND isdel='f' AND isapp='t'
                  			AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')
                  ORDER BY a.kdperkiraan,a.nobukti
		";*/
		
		/*$sql = "
				SELECT
						c.kduker,d.namasingkatan,a.kdperkiraan,b.nmperkiraan,
						a.nobukti,to_char(date(a.tanggal),'DD-MM-YYYY') AS tanggal,
						a.keterangan,a.rupiah,a.dk, substr(a.nobukti, 1 ,2) as kode
				FROM
					jurnal_{$div} AS a
				INNER JOIN dperkir AS b ON b.kdperkiraan = a.kdperkiraan
				INNER JOIN anggar AS c ON c.kdperkiraan = a.kdperkiraan and c.kduker = substr(a.nobukti, 1 ,2)
				INNER JOIN z_groupname_bius As d ON c.kduker = d.kduker and c.kddivisi = d.kddivisi
				WHERE
					a.kdperkiraan LIKE '7%' AND isdel='f' AND isapp='t'
					AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')
				GROUP BY 
					c.kduker, d.namasingkatan, a.kdperkiraan, b.nmperkiraan, a.nobukti, tanggal, a.keterangan,a.rupiah,a.dk
				ORDER BY
					c.kduker ASC, a.kdperkiraan ASC,a.nobukti ASC
		";*/
		$tambah='';
		if($uker_)
		{
			$tambah = " AND c.kduker = '{$uker_}' ";
		}
		$sql = "
				SELECT
						substr(A .nobukti, 1, 2)AS kduker,d.namasingkatan,a.kdperkiraan,b.nmperkiraan,a.jid,
						a.nobukti,to_char(date(a.tanggal),'DD-MM-YYYY') AS tanggal,
						a.keterangan,case when a.dk = 'D' then a.rupiah else a.rupiah *-1 end as rupiah, a.dk, substr(a.nobukti, 1 ,2) as kode
				FROM
					jurnal_{$div} AS a
				LEFT JOIN dperkir AS b ON b.kdperkiraan = a.kdperkiraan
				LEFT JOIN anggar AS c ON c.kdperkiraan = a.kdperkiraan and c.kduker = substr(a.nobukti, 1 ,2)
				LEFT JOIN z_groupname_bius As d ON substr(A .nobukti, 1, 2) = d.kduker AND A .kddivisi = d.kddivisi
				WHERE
					a.kdperkiraan LIKE '49%' AND isdel='f' AND isapp='t'
					AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')
					{$tambah}
				GROUP BY 
					c.kduker, d.namasingkatan, a.kdperkiraan, b.nmperkiraan, a.jid, a.nobukti, tanggal, a.keterangan,a.rupiah,a.dk
				ORDER BY
					c.kduker ASC, a.kdperkiraan ASC,a.nobukti ASC
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function ikhtisar_sdnow($div='',$month1='',$month2='',$uker_='')
	{
		/*$sql = "
		SELECT t.kdwilayah as kduker, t.namasingkatan, t.kdperkiraan, t.nmperkiraan, sum(d) as debet
		FROM (
					SELECT c.kdwilayah, d.namasingkatan,  a.kdperkiraan, e.nmperkiraan,
								(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) END) AS d
					FROM jurnal_{$div} a
					-- RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN z_groupname_bius d ON d.kddivisi = a.kddivisi AND d.kduker = substr(a.nobukti,1,2)
					LEFT JOIN dperkir e ON e.kdperkiraan = a.kdperkiraan
					WHERE a.kdperkiraan LIKE '7%' AND DATE(a.tanggal) <= '{$month1}' AND DATE(a.tanggal) >= '{$month2}'
					GROUP BY c.kdwilayah, d.namasingkatan,a.kdperkiraan, e.nmperkiraan, a.dk
					ORDER BY c.kdwilayah,a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.namasingkatan, t.kdperkiraan, t.nmperkiraan
		ORDER BY t.kdwilayah
		";*/
		/*$sql = "
		SELECT t.kdwilayah as kduker, t.namasingkatan, t.kdperkiraan, t.nmperkiraan, sum(d) as debet
		FROM (
					SELECT substr(a.nobukti, 1, 2) as kdwilayah, d.namasingkatan,  a.kdperkiraan, e.nmperkiraan,
								(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) END) AS d
					FROM jurnal_{$div} a
					-- RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					-- LEFT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN anggar AS b ON b.kdperkiraan = A .kdperkiraan		AND b.kduker = substr(A .nobukti, 1, 2)
					LEFT JOIN z_groupname_bius d ON d.kddivisi = a.kddivisi AND d.kduker = substr(a.nobukti,1,2)
					LEFT JOIN dperkir e ON e.kdperkiraan = a.kdperkiraan
					WHERE a.kdperkiraan LIKE '7%' -- AND DATE(a.tanggal) <= '{$month1}' AND DATE(a.tanggal) >= '{$month2}'
					AND (a.tanggal BETWEEN '{$month1}' AND '{$month2}' )
					AND a.isdel='f' AND a.isapp='t'
					GROUP BY a.nobukti, d.namasingkatan,a.kdperkiraan, e.nmperkiraan, a.dk
					ORDER BY a.nobukti, a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.namasingkatan, t.kdperkiraan, t.nmperkiraan
		ORDER BY t.kdwilayah
		";*/
		$tambah='';
		if($uker_)
		{
			$tambah = " AND d.kduker = '{$uker_}' ";
		}
		$sql = "
		SELECT t.kdwilayah as kduker, t.namasingkatan, t.kdperkiraan, t.nmperkiraan, sum(d) as debet
		FROM (
					SELECT substr(a.nobukti, 1, 2) as kdwilayah, d.namasingkatan,  a.kdperkiraan, e.nmperkiraan,a.jid,
								(CASE WHEN a.dk = 'D' THEN a.rupiah ELSE a.rupiah*-1 END) AS d
					FROM jurnal_{$div} a
					-- RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					-- LEFT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN anggar AS b ON b.kdperkiraan = A .kdperkiraan		AND b.kduker = substr(A .nobukti, 1, 2)
					LEFT JOIN z_groupname_bius d ON d.kddivisi = a.kddivisi AND d.kduker = substr(a.nobukti,1,2)
					LEFT JOIN dperkir e ON e.kdperkiraan = a.kdperkiraan
					WHERE a.kdperkiraan LIKE '49%' 
					-- AND DATE(a.tanggal) <= '{$month1}' AND DATE(a.tanggal) >= '{$month2}'
					AND (a.tanggal BETWEEN '{$month1}' AND '{$month2}' )
					AND a.isdel='f' AND a.isapp='t' {$tambah}
					GROUP BY a.nobukti, d.namasingkatan,a.kdperkiraan, e.nmperkiraan, a.dk, a.rupiah, a.jid
					ORDER BY a.nobukti, a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.namasingkatan, t.kdperkiraan, t.nmperkiraan
		ORDER BY t.kdwilayah
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function ikhtisar_now($div='',$year='',$month='',$uker_='')
	{
		/*$sql = "
		SELECT t.kdwilayah as kduker,t.kdperkiraan, sum(d) as debet, sum(rab) as rab
		FROM (
					SELECT c.kdwilayah, a.kdperkiraan,
								(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) END) AS d, d.rupiah as rab
					FROM jurnal_{$div} a
					RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN anggar d ON d.kdperkiraan = a.kdperkiraan
					WHERE a.kdperkiraan LIKE '7%' AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')
					GROUP BY c.kdwilayah, a.kdperkiraan, a.dk, rab
					ORDER BY c.kdwilayah,a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.kdperkiraan
		ORDER BY t.kdwilayah
		";*/
		//echo $month."<br>";
		if(substr($month,1,1)=='0')
		{
			$month1 = substr($month,2,1);
		}
		else {
			$month1 = $month;
		}
		$tambah='';
		if($uker_)
		{
			$tambah = " AND d.kduker = '{$uker_}' ";
		}
		
		/*$sql = "
		SELECT t.kdwilayah as kduker,t.kdperkiraan, sum(d) as debet, rab as rab
		FROM (
					SELECT c.kdwilayah, a.kdperkiraan,
								(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) END) AS d, d.rupiah as rab
					FROM jurnal_{$div}  a
					RIGHT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN anggar d ON d.kdperkiraan = a.kdperkiraan AND d.kddivisi = a.kddivisi 
					AND d.tahun = '{$year}' AND d.bulan={$month} AND d.kduker = substr(a.nobukti,1,2)
					WHERE a.kdperkiraan LIKE '7%' AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')					
					GROUP BY c.kdwilayah, a.kdperkiraan, a.dk, rab
					ORDER BY c.kdwilayah,a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.kdperkiraan, t.rab
		ORDER BY t.kdwilayah
		";*/
		
		$sql = "
		SELECT t.kdwilayah as kduker,t.kdperkiraan, sum(d) as debet, rab as rab
		FROM (
					SELECT substr(a.nobukti, 1, 2) as kdwilayah, a.kdperkiraan,a.jid,
								(CASE WHEN a.dk = 'D' THEN SUM (a.rupiah) ELSE SUM (a.rupiah*-1) END) AS d, d.rupiah as rab
					FROM jurnal_{$div}  a
					LEFT JOIN dwilayah c ON c.kdwilayah = substr(a.nobukti,1,2) AND c.kddivisi = a.kddivisi
					LEFT JOIN anggar d ON d.kdperkiraan = a.kdperkiraan AND d.kddivisi = a.kddivisi 
					AND d.tahun = '{$year}' AND d.bulan='{$month}' AND d.kduker = substr(a.nobukti,1,2)
					WHERE a.kdperkiraan LIKE '49%' AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')	
					AND a.isdel='f' AND a.isapp='t'	{$tambah}		
					GROUP BY a.nobukti, a.kdperkiraan, a.dk, rab,a.jid
					ORDER BY a.nobukti, a.kdperkiraan
		) t
		GROUP BY t.kdwilayah, t.kdperkiraan, t.rab
		ORDER BY t.kdwilayah
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function ikhtisar_per($div='',$month1='',$month2='',$uker_='')
	{
		$tambah='';
		if($uker_)
		{
			$tambah = " AND b.kduker = '{$uker_}' ";
		}
		$sql="
			SELECT  t.kdperkiraan, t.nmperkiraan, sum(d) as debet
			FROM (
						SELECT a.kdperkiraan, e.nmperkiraan,a.jid,
									(CASE WHEN a.dk = 'D' THEN a.rupiah ELSE a.rupiah*-1 END) AS d
						FROM jurnal_{$div} a
						LEFT JOIN anggar AS b ON b.kdperkiraan = A .kdperkiraan	AND b.kduker = substr(A .nobukti, 1, 2)
						LEFT JOIN dperkir e ON e.kdperkiraan = a.kdperkiraan
						WHERE a.kdperkiraan LIKE '49%'
						AND (a.tanggal BETWEEN '{$month1}' AND '{$month2}' )
						AND a.isdel='f' AND a.isapp='t' {$tambah}
						GROUP BY a.kdperkiraan, e.nmperkiraan, a.dk, a.rupiah, a.jid
						ORDER BY a.kdperkiraan
			) t
			GROUP BY t.kdperkiraan, t.nmperkiraan
			ORDER BY t.kdperkiraan
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function ikhtisar_pernow($div='',$year='',$month='',$uker_='')
	{
		$tambah='';
		if($uker_)
		{
			$tambah = " AND d.kduker = '{$uker_}' ";
		}
		$sql="
			SELECT t.kdperkiraan, sum(d) as debet, sum(rab) as rab
			FROM (
						SELECT a.kdperkiraan,a.jid,
									(CASE WHEN a.dk = 'D' THEN a.rupiah ELSE a.rupiah*-1 END) AS d, d.rupiah as rab
						FROM jurnal_{$div}  a
						LEFT JOIN anggar d ON d.kdperkiraan = a.kdperkiraan AND d.kddivisi = a.kddivisi 
						AND d.tahun = '{$year}' AND d.bulan='12' AND d.kduker = substr(a.nobukti,1,2)
						WHERE a.kdperkiraan LIKE '49%' AND (DATE_PART ('YEAR',a.tanggal) = '{$year}' AND DATE_PART ('MONTH',a.tanggal) = '{$month}')		
						AND a.isdel='f' AND a.isapp='t'	{$tambah}			
						GROUP BY a.kdperkiraan,a.rupiah, a.dk, rab,a.jid
						ORDER BY a.kdperkiraan
			) t
			GROUP BY  t.kdperkiraan
			ORDER BY  t.kdperkiraan
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function anggaran($div='',$year='',$uker_='')
	{
		$tambah='';
		if($uker_)
		{
			$tambah = " AND kduker = '{$uker_}' ";
		}
		$sql="
			SELECT kddivisi,kduker,kdperkiraan,rupiah from anggar 
			where tahun = '{$year}' and bulan = '12' and kddivisi = '{$div}' {$tambah}
			order by kdperkiraan
		";
		//echo $sql;
		return $this->_DB->query($sql);
	}
	public function get_namaperkiraan($kd)
	{
		$sql = "SELECT gname AS nmperkiraan FROM dperkir_group bawah WHERE bawah LIKE '$kd%' ";//dperkir where kdperkiraan='{$kd}'";
		//echo $sql;
		$query = $this->_DB->query($sql)->fetchObject();
		return $query->nmperkiraan;
	}

	public function get_namaperkiraan_dperkir($kd)
	{
		$sql = "SELECT nmperkiraan FROM dperkir WHERE kdperkiraan  = '$kd' ";//dperkir where kdperkiraan='{$kd}'";
		//echo $sql;
		$query = $this->_DB->query($sql)->fetchObject();
		return $query->nmperkiraan;
	}
	public function get_kode_namaperkiraan($kd)
	{
		$sql = "SELECT kdperkiraan, nmperkiraan FROM dperkir WHERE kdperkiraan='{$kd}'";
		//echo $sql;
		$query = $this->_DB->query($sql)->fetchObject();
		return array($query->kdperkiraan,$query->nmperkiraan);
	}
	public function custom_query ($query='')
	{
		$query = $this->_DB->query($query)->fetchObject();
		return $query;
	}
	
	public function format($nilai)
	{
		if ($nilai < 0)
		{
		  return "(".number_format(abs($nilai)).")";
		}
		else {
			return number_format($nilai);
		}
	}
}
