<?php
class Mdl_report_labarugi extends Mdl_core {
	
	private $_DB;
	
	function __construct() {
		parent::__construct();		
		$this->_DB = parent::$API_DB;
	}

	public function get_group_report($hd='D')
	{

		$sql = "
				SELECT parent 
				FROM mapping_neraca
				WHERE visibility='t' AND group_type='laba_rugi' AND h_d='D' --AND h_d='$hd'
				GROUP BY parent
				ORDER BY parent ASC;
			";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function get_subtotal_report($group_name='')
	{
		$sql = "
				SELECT parent, flag_rumus
				FROM mapping_neraca
				WHERE visibility='t' AND group_type='laba_rugi' AND parent <> '' AND h_d='D'
				GROUP BY parent, flag_rumus
				ORDER BY parent ASC;
			";
		return $this->db->query($sql);
	}
	
	//$command_attr->kddivisi, $command_attr->periode_year, $command_attr->periode_month, $command_attr->admin, $command_attr->title, $command_attr->kdwilayah)
	//public function get_total_bygroup($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='',$kdkira_similar='')
	
	// public function get_total_bygroup_proyek($kddivisi, $tahun='', $bulan='', $periode_awal='', $periode_akhir='', $kdpsk='', $nobukti = '', $similar='')
	// {
	// 	//ini_set('error_reporting', E_ALL);
	// 	//echo $tahun;
	// 	//var_dump($similar);exit;
	// 	if($similar==''){
	// 		$similar = "3|4|5|6|7|9";
	// 	}
	// 	if((int)$bulan<10){
	// 		$bulan = '0'.$bulan;
	// 	}
	// 	if($bulan > 1)
	// 	{
	// 		$bulan_lalu = $bulan - 1;	
	// 	}
	// 	else
	// 	{
	// 		$bulan_lalu = 1;
	// 	}
	// 	//var_dump($bulan);exit;

	// 	$kddivisi 		= str_replace("'","",$kddivisi);    	
 //    	$tahun 			= str_replace("'","",$tahun);    	 	
 //    	$kdspk 			= str_replace("'","",$kdspk);
 //    	$nobukti 		= str_replace("'","",$nobukti);
		
	// 	// print_r($similar);
	// 	// echo '<br>';
	// 	// print_r($nobukti);
	// 	// die;

	// 	if($kdspk==''){
	// 		$sql_kdspk = '';
	// 	}else{
	// 		$sql_kdspk = "AND j.kdspk = '$kdspk'";
	// 	}
		
	// 	if($nobukti==''){
	// 		$nobukti = '01';
	// 	}
		 
 //    	$periode_awal 	= str_replace("'","",($tahun.'-'.$bulan.'-01'));
 //    	$periode_akhir	= date("Y-m-t", strtotime($periode_awal));
 //    	$periode_akhir_bl = date("Y-m-t", strtotime('-1 day' . $periode_awal));
 //    	$kodejurnal 	= '01';

 //    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
	// 	else $sql_wil = '';


	// 	$sql = 
	// 	"SELECT
	// 		SUM( 
	// 			CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 't' 
	// 					THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
	// 				WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 'f' 
	// 					THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
	// 		  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
	// 		SUM( 
	// 			CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 't' 
	// 			       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
	// 				WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 'f'
	// 			      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah 
	// 			ELSE 0 END )AS proyek_rupiah_bln_ini,
	// 		SUM( 
	// 			CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 't' 
	// 					THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
	// 				WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti NOT LIKE '($nobukti)%' AND dp.default_debet = 'f' 
	// 					THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
	// 		  	ELSE 0 END )AS proyek_rupiah_sd_bln_lalu
	// 	FROM
	// 		dperkir dp
	// 	LEFT JOIN 
 //    	(
 //     		SELECT jj.* FROM jurnal_v jj 
	// 			LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
 //     		WHERE true 
 //    	) j  ON (
	// 			j.kdperkiraan = dp.kdperkiraan AND 
	// 			j.tanggal::date <= '$periode_akhir' AND 
	// 			isdel = 'f' -- gar is dead
	// 		)
	// 		LEFT JOIN mapping_neraca m ON m.group_name = group_coa
	// 		WHERE
	// 				dp.kdperkiraan SIMILAR TO '($similar)%' AND 
	// 				m.visibility='t' --AND
	// 				--m.h_d = 'ST' AND 
	// 				$sql_kdspk
	// 				--m.parent ='I000' 
	// 		GROUP BY
	// 				--dp.kdperkiraan,
	// 				--group_name, 
	// 				--parent,  
	// 				flag_rumus
	// 		";
	// 		// print '<pre>'.$sql."</pre>";exit;
	// 		$query = $this->db->query($sql);
	// 		$result = $query->result_array();
	// 		//var_dump($similar);exit;
	// 		return $result;
	// }
	
	public function get_total_bygroup(
							$kddivisi, 
							$tahun='', $bulan='',
							$kdwilayah='',
							$similar='')
	{
		//ini_set('error_reporting', E_ALL);
		//echo $tahun;
		//var_dump($similar);exit;
		if($similar==''){
			$similar = "3|4|5|6|7|9";
		}
		if((int)$bulan<10){
			$bulan = '0'.$bulan;
		}
		//var_dump($bulan);exit;

		$kddivisi 		= str_replace("'","",$kddivisi);    	
    	$tahun 			= str_replace("'","",$tahun);    	 	
    	$kdwilayah 		= str_replace("'","",$kdwilayah);

    	$periode_awal 	= str_replace("'","",($tahun.'-'.$bulan.'-01'));
    	$periode_akhir	= date("Y-m-t", strtotime($periode_awal));
    	$kodejurnal 	= '01';

    	//print_r($div,$tahun,$periode_awal,$periode_akhir,$kdwilayah,$kdkira_similar);exit;

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';


		$sql = 
		"SELECT
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			-------
			-------
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
			  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah 
				ELSE 0 END )AS proyek_rupiah_bln_ini, 
			------
			------
			( 
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
				ELSE 0 END ) 
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
			  	ELSE 0 END )
				) AS konsol_rp_divisi_sd_bln_ini,
			
			(
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah
				ELSE 0 END )
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) *-1* j.rupiah 
				ELSE 0 END )
			) AS konsol_rp_divisi_bln_ini,
			'0.00' AS persentase ,
			'-' as flag_rumus
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_v jj 
				LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan AND 
				j.tanggal::date <= '$periode_akhir' AND 
				isdel = 'f' -- gar is dead
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
			WHERE
					dp.kdperkiraan SIMILAR TO '($similar)%' AND 
					m.visibility='t' --AND
					--m.h_d = 'ST' AND 
					--m.parent ='I000' 
			GROUP BY
					--dp.kdperkiraan,
					--group_name, 
					--parent,  
					flag_rumus
			";
			//print '<pre>'.$sql."</pre>";exit;
			$query = $this->db->query($sql);
			$result = $query->result_array();
			//var_dump($similar);exit;
			return $result;
	}

	public function get_total_bygroup_kompre($div='',$tahun, $periode_awal='',$periode_akhir='',$kdwilayah='',$kdkira_similar='')
	{
		//ini_set('error_reporting', E_ALL);
		//echo $tahun;

		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	 	
    	$kdwilayah = str_replace("'","",$kdwilayah);
    	//$periode_awal 	= str_replace("'","",($tahun.'-'.$bulan.'-01'));
    	$periode_akhir	= date("Y-m-t", strtotime($periode_awal));
    	$kodejurnal = '01';

    	if ($this->S['curr_divisi'] == 'V') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		$div_spk = strtoupper($div);
		$last_year = ($tahun-1);
		
		$last_year = ($tahun-1);
			$sql = "SELECT parent,
						SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$tahun-01-01') AND date('$periode_akhir') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							WHEN date_trunc('month', j.tanggal) BETWEEN date('$tahun-01-01') AND date('$periode_akhir') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							ELSE 0 END) AS konsol_rp_divisi_bln_ini,
						SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$last_year-01-01') AND date('$last_year-12-31') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							WHEN date_trunc('month', j.tanggal) BETWEEN date('$last_year-01-01') AND date('$last_year-12-31') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							ELSE 0 END) AS konsol_rp_divisi_sd_bln_ini
					FROM dperkir dp
					LEFT JOIN jurnal_$div j ON (
						j.kdperkiraan=dp.kdperkiraan
						AND date_trunc('month', j.tanggal) <= date('$periode_akhir')
						AND isdel='f' 
						AND j.kddivisi IN ('$div_spk')
						
						)
					LEFT JOIN mapping_neraca m ON m.group_name = dp.group_coa
					WHERE dp.kdperkiraan SIMILAR TO '($kdkira_similar)%' AND group_coa <> '0' AND group_coa IS NOT NULL
					GROUP BY parent
					ORDER BY parent ASC; ";
			//echo '<pre>'.$sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}

	public function get_report_format()
	{
		$sql = "
				SELECT a.parent, a.group_name, a.description, trim(a.h_d) as h_d, a.total_group, a.priority, a.l_r,a.sum_type, a.flag_rumus
                FROM mapping_neraca a
                WHERE a.visibility='t' AND group_type='laba_rugi'
				GROUP BY a.parent, a.group_name, a.description, a.h_d, a.total_group,  a.priority, a.l_r,a.sum_type, a.flag_rumus
				ORDER BY  a.group_name, a.parent ASC;
				";

			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}

	public function get_report_proyek_format()
	{
		$sql = "
				SELECT a.parent, a.group_name, a.description, trim(a.h_d) as h_d, a.total_group, a.priority, a.l_r,a.sum_type, a.flag_rumus
                FROM mapping_neraca a
                WHERE a.visibility='t' AND group_type='laba_rugi' AND parent!='D000' 
				GROUP BY a.parent, a.group_name, a.description, a.h_d, a.total_group,  a.priority, a.l_r,a.sum_type, a.flag_rumus
				ORDER BY  a.group_name, a.parent ASC;
				";

			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}

	// public function get_report_format()
	// {
	// 	$sql = "
	// 			SELECT a.parent, a.group_name, a.description, trim(a.h_d) as h_d, a.total_group, a.priority, a.l_r,a.sum_type, a.flag_rumus
 //                FROM mapping_neraca a
 //                WHERE a.visibility='t' AND group_type='laba_rugi' AND a.h_d != 'ST'
	// 			GROUP BY a.parent, a.group_name, a.description, a.h_d, a.total_group,  a.priority, a.l_r,a.sum_type, a.flag_rumus
	// 			ORDER BY  a.group_name, a.parent ASC;
	// 			";

	// 		$query = $this->db->query($sql);
	// 		$result = $query->result_array();

	// 		return $result;
	// }

	public function urutan_report_labarugi()
	{
		$sql = "SELECT a.description,a.parent, trim(a.h_d) as h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type, a.flag_rumus
                FROM mapping_neraca a
                WHERE a.visibility='t' AND group_type='laba_rugi'
                ORDER BY a.urutan";
		//echo $sql.";";
		return $this->_DB->query($sql);
	}

	public function get_rows_labarugi($div='',$tahun,$bulan='',$periode_awal='',$periode_akhir='',$kdwilayah='',$group_parent='',$is_subtotal=false)
	{
		$post = $this->input->post('data');
		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$post['periode']['year']);  
    	$bulan = str_replace("'","",$bulan);    	 
		if((int)$bulan < 10){
			$bulan ='0'.$bulan;
		}
    	$kdwilayah = str_replace("'","",$kdwilayah);
    	$kodejurnal = '01';

    	$periode_akhir = date("Y-m-t", strtotime($periode_awal));

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		}else{
			$getsubtot = 'group_name, ';
			$group_subtot = 'group_name, ';
			$where_parent = "
							 m.parent <> '' AND 
							 m.parent IS NOT NULL ";
		}
		
		$add_where = '';
		if($group_parent ==''){
			$add_where .= '';//"m.parent = '$group_parent' ";
		}else{
			$add_where .= '';
		}

		if ($kdwilayah != '')
		{
			$sql_wil .= " AND kodewilayah='$kdwilayah'";
		}
		
		$sql = 
		"
SELECT $getsubtot parent,
SUM(
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
					 THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f' 
					 THEN get_dk(j.dk)*- 1 * j.rupiah
	ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
SUM(
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
					 THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f'  
					 THEN get_dk(j.dk)*- 1 * j.rupiah
	ELSE 0 END )AS divisi_rupiah_bln_ini, 
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
	         THEN get_dk(j.dk) * j.rupiah 
		   WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
	         THEN get_dk(j.dk)*- 1 * j.rupiah 
	ELSE 0 END )AS proyek_rupiah_bln_ini,
----
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )
	+
SUM(
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
					 THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f' 
					 THEN get_dk(j.dk)*- 1 * j.rupiah
	ELSE 0 END )
	AS konsol_rp_divisi_sd_bln_ini
, 
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 't' 
	         THEN get_dk(j.dk) * j.rupiah 
		   WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '01%' AND dp.default_debet = 'f' 
	         THEN get_dk(j.dk)*- 1 * j.rupiah 
	ELSE 0 END )
	+
SUM(
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 't' 
					 THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti LIKE '01%' AND dp.default_debet = 'f'  
					 THEN get_dk(j.dk)*- 1 * j.rupiah
	ELSE 0 END )
	AS konsol_rp_divisi_bln_ini 
----
FROM dperkir dp 
LEFT JOIN ( SELECT jj.* FROM jurnal_V jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
WHERE true ) j ON ( j.kdperkiraan = dp.kdperkiraan AND j.tanggal::date <= '$periode_akhir' AND isdel = 'f' AND isapp = 't' ) 
LEFT JOIN mapping_neraca m ON m.group_name = dp.group_coa
WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND 
					m.visibility='t' AND
					--m.h_d = 'ST' AND 
					$add_where
					$where_parent 
			GROUP BY
					m.parent, $group_subtot flag_rumus
			ORDER BY $group_subtot parent;
		";
		//print "<pre>".$sql."</pre>";exit;
			//echo 'get_rows_labarugi: '.$sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();
			
			return $result;	
	}
	
	public function get_rows_labarugi_proyek($div='',$tahun,$bulan='',$periode_awal='',$periode_akhir='',$kdspk='',$nobukti, $group_parent='',$is_subtotal=false)
	{
		$post = $this->input->post('data');
		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$post['periode']['year']);  
    	$bulan = str_replace("'","",$bulan);    	 
		if((int)$bulan < 10){
			$bulan ='0'.$bulan;
		}

		if($bulan > 1)
		{
			$bulan_lalu = $bulan - 1;	
		}
		else
		{
			$bulan_lalu = 1;
		}
		$tahun_lalu = $tahun - 1;

    	$kdspk = str_replace("'","",$kdspk);
    	$nobukti = str_replace("'","",$nobukti);
    	$kodejurnal = '01';
		
		if($kdspk==''){
			$sql_kdspk = '';
		}else{
			$sql_kdspk = "j.kdspk = '$kdspk'";
		}

		print_r($periode_akhir);
		die;

		if($nobukti=='PUSAT')
		{
			$sql_nobukti='01';
			$sql_not 	='';
		}
		elseif($nobukti=='PROYEK')
		{
			$sql_nobukti='01';
			$sql_not 	='NOT';
		}
		elseif($nobukti=='ALL')
		{
			$sql_sql_nobukti='';
			$sql_not 	='';
		}

    	$periode_akhir = date("Y-m-t", strtotime($periode_awal));
    	$periode_akhir_bl = date("Y-m-t", strtotime('-1 day' . $periode_awal));

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		}else{
			$getsubtot = 'group_name, ';
			$group_subtot = 'group_name, ';
			$where_parent = "
							 m.parent <> '' AND 
							 m.parent IS NOT NULL ";
		}
		
		$add_where = '';
		if($group_parent ==''){
			$add_where .= '';//"m.parent = '$group_parent' ";
		}else{
			$add_where .= '';
		}

		$sql = 
		"
SELECT $getsubtot parent,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$tahun_lalu-12-31') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$tahun_lalu-12-31') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  	ELSE 0 END )AS proyek_rupiah_sd_thn_lalu,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  	ELSE 0 END )AS proyek_rupiah_sd_bln_lalu, 
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
	         THEN get_dk(j.dk) * j.rupiah 
		   WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
	         THEN get_dk(j.dk)*- 1 * j.rupiah 
	ELSE 0 END )AS proyek_rupiah_bln_ini,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )AS proyek_rupiah_sd_bln_ini,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )AS proyek_rupiah_sd_thn_ini
FROM dperkir dp 
LEFT JOIN ( SELECT jj.* FROM jurnal_V jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
WHERE true ) j ON ( j.kdperkiraan = dp.kdperkiraan AND j.tanggal::date <= '$periode_akhir' AND isdel = 'f' AND isapp = 't' ) 
LEFT JOIN mapping_neraca m ON m.group_name = dp.group_coa
WHERE
dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND 
m.visibility='t' AND
--m.h_d = 'ST' AND 
$sql_kdspk AND
$add_where
$where_parent 
GROUP BY
m.parent, $group_subtot flag_rumus
ORDER BY $group_subtot parent;
		";
		print "<pre>".$sql."</pre>";exit;
		// 	echo 'get_rows_labarugi: '.$sql; exit();
			$query = $this->db->query($sql);
			$result = $query->result_array();
			
			return $result;	
	}

	public function get_rows_labarugi_proyek_st($div='',$tahun,$bulan='',$periode_awal='',$periode_akhir='',$kdspk='',$nobukti, $group_parent='',$is_subtotal=false)
	{
		$post 	= $this->input->post('data');
		$div 	= strtolower($div);
		$div 	= str_replace("'","",$div);    	
    	$tahun 	= str_replace("'","",$post['periode']['year']);  
    	$bulan 	= str_replace("'","",$bulan);    	 
		if((int)$bulan < 10){
			$bulan ='0'.$bulan;
		}

		if($bulan > 1)
		{
			$bulan_lalu = $bulan - 1;	
		}
		else
		{
			$bulan_lalu = 1;
		}
		$tahun_lalu = $tahun - 1;

    	$kdspk = str_replace("'","",$kdspk);
    	$nobukti = str_replace("'","",$nobukti);
    	$kodejurnal = '01';
		
		if($kdspk==''){
			$sql_kdspk = '';
		}else{
			$sql_kdspk = "j.kdspk = '$kdspk'";
		}

		if($nobukti=='PUSAT')
		{
			$sql_nobukti='01';
			$sql_not 	='';
		}
		elseif($nobukti=='PROYEK')
		{
			$sql_nobukti='01';
			$sql_not 	='NOT';
		}
		elseif($nobukti=='ALL')
		{
			$sql_sql_nobukti='';
			$sql_not 	='';
		}

    	$periode_akhir = date("Y-m-t", strtotime($periode_awal));
    	$periode_akhir_bl = date("Y-m-t", strtotime('-1 day' . $periode_awal));

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		// if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		// }else{
		// 	$getsubtot = 'group_name, ';
		// 	$group_subtot = 'group_name, ';
		// 	$where_parent = "
		// 					 m.parent <> '' AND 
		// 					 m.parent IS NOT NULL ";
		// }
		
		$add_where = '';

		$sql = 
		"
SELECT $getsubtot parent,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$tahun_lalu-12-31') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$tahun_lalu-12-31') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  	ELSE 0 END )AS proyek_rupiah_sd_thn_lalu,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir_bl') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  	ELSE 0 END )AS proyek_rupiah_sd_bln_lalu, 
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
	         THEN get_dk(j.dk) * j.rupiah 
		   WHEN (j.tanggal::date BETWEEN '$tahun-$bulan-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
	         THEN get_dk(j.dk)*- 1 * j.rupiah 
	ELSE 0 END )AS proyek_rupiah_bln_ini,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )AS proyek_rupiah_sd_bln_ini,
SUM( 
	CASE WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 't' 
			     THEN get_dk(j.dk) * j.rupiah
			 WHEN (j.tanggal::date BETWEEN '2008-01-01' AND '$periode_akhir') AND j.nobukti $sql_not LIKE '$sql_nobukti%' AND dp.default_debet = 'f' 
			     THEN get_dk(j.dk)*- 1 * j.rupiah
  ELSE 0 END )AS proyek_rupiah_sd_thn_ini 
FROM dperkir dp 
LEFT JOIN ( SELECT jj.* FROM jurnal_V jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
WHERE true ) j ON ( j.kdperkiraan = dp.kdperkiraan AND j.tanggal::date <= '$periode_akhir' AND isdel = 'f' AND isapp = 't' ) 
LEFT JOIN mapping_neraca m ON m.group_name = dp.group_coa
WHERE
m.visibility='t' AND
--m.h_d = 'ST' AND 
$sql_kdspk AND
$add_where
$where_parent 
GROUP BY
m.parent, $group_subtot flag_rumus
ORDER BY $group_subtot parent;
		";
		//print "<pre>".$sql."</pre>";exit;
		// 	echo 'get_rows_labarugi: '.$sql; exit();
			$query = $this->db->query($sql);
			$result = $query->result_array();
			
			return $result;	
	}
	
	public function get_rows_labarugi_kompere($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='',$group_parent='',$is_subtotal=false)
	{
		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	 	
    	$kdwilayah = str_replace("'","",$kdwilayah);
    	$kodejurnal = '01';

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		$validDiv = array();
		$sq_lr 	= "
		SELECT * FROM ddivisi 
		WHERE 
			is_visible = TRUE 
			--AND '$year-".str_pad($month,2,'0',STR_PAD_LEFT)."-01'::DATE >= tgl_valid_start
			--AND '$year-".str_pad($month,2,'0',STR_PAD_LEFT)."-01'::DATE < (tgl_valid + INTERVAL '1 month')
			";
		//echo $sq_lr;
		//exit;
        $query = $this->db->query($sq_lr);
        $rs = $query->result_array($query);
		foreach($rs as $row){
			$validDiv[] = "'".$row['kddivisi']."'"; 
		}
		
		if (count($validDiv) >= 1) {
			$addsql .= " AND j.kddivisi IN (".implode(',', $validDiv).")";
		}


		if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		}else{
			$getsubtot = 'group_name, ';
			$group_subtot = 'group_name, ';
			$where_parent = "
							 m.parent <> '' AND 
							 m.parent IS NOT NULL ";
		}
		
		$add_where = '';
		if($group_parent ==''){
			$add_where .= '';//"m.parent = '$group_parent' ";
		}else{
			$add_where .= '';
		}

		if ($kdwilayah != '')
		{
			$sql_wil .= " AND kodewilayah='$kdwilayah'";
		}
		$year_before = ($tahun-1);
		$sql = 
		"SELECT
			 $getsubtot parent,
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$year_before-01-01' AND '$year_before-12-31') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$year_before-01-01' AND '$year_before-12-31') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			-------
			-------
			'0.00' AS persentase ,
			'-' as flag_rumus
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_$div jj 
				LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan AND 
				j.tanggal::date <= '$periode_akhir' AND 
				isdel = 'f' --AND 
				--isapp = 't'
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
			WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND 
					m.visibility='t' AND
					--m.h_d = 'ST' AND 
					$add_where
					$where_parent 
			GROUP BY
					m.parent, $group_subtot flag_rumus
			ORDER BY $group_subtot parent;";
			//echo $sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}

	public function get_rows_labarugikompre($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='',$group_parent='',$is_subtotal=false)
	{
		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	 	
    	$kdwilayah = str_replace("'","",$kdwilayah);
    	$kodejurnal = '01';
    	$last_year = ($tahun-1);
    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		}else{
			$getsubtot = 'group_name, ';
			$group_subtot = 'group_name, ';
			$where_parent = "
							 m.parent <> '' AND 
							 m.parent IS NOT NULL ";
		}
		
		$add_where = '';
		if($group_parent ==''){
			$add_where .= '';//"m.parent = '$group_parent' ";
		}else{
			$add_where .= '';
		}

		if ($kdwilayah != '')
		{
			$sql_wil .= " AND kodewilayah='$kdwilayah'";
		}
		
		$sql = 
		"SELECT
			 $getsubtot parent,
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$last_year-01-01' AND '$tahun-12-31') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$last_year-01-01' AND '$tahun-12-31') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			'0.00' AS persentase ,
			'-' as flag_rumus
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_$div jj 
				LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan AND 
				j.tanggal::date <= '$periode_akhir' AND 
				isdel = 'f' --AND 
				--isapp = 't'
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
			WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND group_coa <> '0' AND group_coa IS NOT NULL AND 
					m.visibility='t' AND
					AND date_trunc('month', j.tanggal) <= date('$periode_akhir')
					AND j.kddivisi IN ('F','V')
					$add_where
					$where_parent 
			GROUP BY
					m.parent, $group_subtot flag_rumus
			ORDER BY $group_subtot parent;";
			//echo $sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}
	public function get_rows_labarugi_kompre($div='',$tahun='', $bulan='', $kdwilayah='',$group_parent='',$is_subtotal=false)
	{
		
		$div 			= strtolower($div);
		$div 			= str_replace("'","",$div);    	
    	$tahun 			= str_replace("'","",$this->input->post('year_')); 	
    	$tbulanahun 	= str_replace("'","",$this->input->post('month_'));    	 	
    	$kdwilayah 		= str_replace("'","",$kdwilayah);
    	$periode_awal 	= str_replace("'","",$tahun).'-'.str_replace("'","",$bulan).'-01';
		$periode_akhir	= date('Y-m-t', strtotime($periode_awal));
    	$kodejurnal 	= '01';

    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if($is_subtotal==true){
			$getsubtot = '';
			$group_subtot = '';
			$where_parent = "m.parent ='$group_parent'";
		}else{
			$getsubtot = 'group_name, ';
			$group_subtot = 'group_name, ';
			$where_parent = "
							 m.parent <> '' AND 
							 m.parent IS NOT NULL ";
		}
		
		$add_where = '';
		if($group_parent ==''){
			$add_where .= '';//"m.parent = '$group_parent' ";
		}else{
			$add_where .= '';
		}

		if ($kdwilayah != '')
		{
			$sql_wil .= " AND kodewilayah='$kdwilayah'";
		}
		$last_year = ($tahun-1);
		
			$last_year = ($tahun-1);
			$sql = "SELECT group_coa as group_name, (select parent FROM mapping_neraca WHERE group_name = dp.group_coa GROUP BY parent) as parent,
						SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$tahun-01-01') AND date('$periode_akhir') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							WHEN date_trunc('month', j.tanggal) BETWEEN date('$tahun-01-01') AND date('$periode_akhir') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah
							ELSE 0 END) AS konsol_rp_divisi_bln_ini,
						SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$last_year-01-01') AND date('$last_year-12-31') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							WHEN date_trunc('month', j.tanggal) BETWEEN date('$last_year-01-01') AND date('$last_year-12-31') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah
							ELSE 0 END) AS konsol_rp_divisi_sd_bln_ini
					FROM dperkir dp
					LEFT JOIN jurnal_$div j ON (
						j.kdperkiraan=dp.kdperkiraan
						AND date_trunc('month', j.tanggal) <= date('$periode_akhir')
						AND isdel='f' 
						AND j.kddivisi IN ('F','V') 
						)
					WHERE dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND group_coa <> '0' AND group_coa IS NOT NULL
					GROUP BY group_coa
					ORDER BY group_coa ASC; ";
			//echo $sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}

	public function get_subtotal_labarugi($group_parent='',$div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='')
	{
		$div = strtolower($div);
		$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	 	
    	$kdwilayah = str_replace("'","",$kdwilayah);
    	
    	if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		
		$add_where = '';
		if($group_parent!==''){
			$add_where .= "m.parent = '$group_parent' AND ";
		}else{
			$add_where .= '';
		}

		if ($kdwilayah != '')
		{
			$sql_wil .= " AND kodewilayah='$kdwilayah'";
		}
		
		$sql = 
		"SELECT
			 group_name, parent,
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			-------
			-------
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-1-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-1-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
				ELSE 0 END )AS proyek_rupiah_bln_ini, 
			------
			------
			( 
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END ) 
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			  	ELSE 0 END )
				) AS konsol_rp_divisi_sd_bln_ini,
			
			(
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
				ELSE 0 END )
			) AS konsol_rp_divisi_bln_ini,
			'0.00' AS persentase ,
			'-' as flag_rumus
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_$div jj 
				LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan AND 
				j.tanggal::date <= '$periode_akhir' AND 
				isdel = 'f' --AND 
				--isapp = 't'
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
			WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND 
					m.visibility='t' AND
					--m.h_d = 'ST' AND 
					$add_where
					m.parent <> '' AND 
					m.parent IS NOT NULL 
			GROUP BY
					m.parent,group_name, flag_rumus
			ORDER BY group_name, parent";

			//echo $sql;
			$query = $this->db->query($sql);
			$result = $query->result_array();

			return $result;
	}
	
	public function get_nilai_labarugi_ELDIN($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='')
	{
    	
    	$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	
    	//$periode_awal = str_replace("'","",$periode_awal);    	
    	//$periode_akhir = str_replace("'","",date($periode_awal."-"."t"));    	
    	$kdwilayah = str_replace("'","",$kdwilayah);

    	//echo $tahun."::".$periode_awal."::".$periode_akhir.">:::";
    	//echo ">>".date($periode_awal."-"."t");
    	
    	$div = strtoupper($div);
		// $sql = "
		// SELECT
			// group_coa,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '01%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS divisi_rupiah_sd_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$periode_awal}-01' AND '{$periode_akhir}') AND j.nobukti LIKE '01%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS divisi_rupiah_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '01%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS proyek_rupiah_sd_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$periode_awal}-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '01%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS proyek_rupiah_bln_ini
		// FROM
			// dperkir dp
		// LEFT JOIN jurnal_{$div} j ON(
			// j.kdperkiraan = dp.kdperkiraan
			// AND j.tanggal <= '{$periode_akhir}'
			// AND isdel = 'f'
			// AND isapp = 't'
		// )
		// WHERE
			// dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%'
		// GROUP BY
			// group_coa
		// ";
		
		// by eldin
    	$kodejurnal = '01';
	    /*
	    $sql_add = '';
	    if ($kdwilayah != '')
	    {
	      $sql_add = " AND jj.nobukti NOT LIKE '01%'";
	      //$sql_add = " AND kk.kodewilayah='$kdwilayah'";
	      $tmp = $this->_DB->query("SELECT kodejurnal FROM dspk WHERE kdspk='$kdwilayah' AND kddiv='$div' LIMIT 1");
	      $item = $tmp->fetchObject();
	      $kodejurnal = $item->kodejurnal;
	      //if ($kodejurnal == '') $kodejurnal = '01';
	      //$sql_add .= " AND (jj.nobukti LIKE '$kodejurnal%' OR jj.nobukti LIKE kk.kodejurnal||'%')";
	      $sql_add .= " AND (jj.nobukti LIKE '$kodejurnal%' OR (jj.nobukti LIKE kk.kodejurnal||'%' AND kk.kodewilayah='$kdwilayah'))";

	    }
	    */

		if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if ($kdwilayah != '')
		{
		$sql_wil .= " AND kodewilayah='{$kdwilayah}'";
		}

		$sql = "
		SELECT
			group_coa,
			--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' THEN  (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			--		  WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND (dp.default_debet='f' OR dp.default_debet='') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			--	 ELSE 0 END )AS divisi_rupiah_sd_bln_ini,
			--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
			--		  WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti LIKE '$kodejurnal%' AND (dp.default_debet='f' OR dp.default_debet='') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	
			--	 ELSE 0 END )AS divisi_rupiah_bln_ini,
			--SUM( 
			--	CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			--		 WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '$kodejurnal%' AND (dp.default_debet='f' OR dp.default_debet='') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			--	ELSE 0 END )AS proyek_rupiah_sd_bln_ini,
			--SUM( 
			--	CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah	
			--	     WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '$kodejurnal%' AND (dp.default_debet='f' OR dp.default_debet='') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
			--	ELSE 0 END )AS proyek_rupiah_bln_ini
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
				ELSE 0 END )AS proyek_rupiah_bln_ini 
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_{$div} jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true $sql_add
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan
				AND j.tanggal::date <= '{$periode_akhir}'
				AND isdel = 'f'
				AND isapp = 't'
			)
			WHERE
				dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%'
			GROUP BY
				group_coa
			ORDER BY 
				group_coa ASC
		";
		// if ($_SERVER['REMOTE_ADDR'] == '10.10.5.108'){
			// $base->db= true;
			// echo $this->S['curr_divisi'];
			// die($sql);
		// }


		//echo '<pre>RINCI: '.$sql.";";
		return $this->_DB->query($sql);
	}

	public function get_nilai_labarugi($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='')
	{
    	
    	$div = str_replace("'","",$div);    	
    	$tahun = str_replace("'","",$tahun);    	
    	//$periode_awal = str_replace("'","",$periode_awal);    	
    	//$periode_akhir = str_replace("'","",date($periode_awal."-"."t"));    	
    	$kdwilayah = str_replace("'","",$kdwilayah);

    	//echo $tahun."::".$periode_awal."::".$periode_akhir.">:::";
    	//echo ">>".date($periode_awal."-"."t");
    	
    	$div = strtoupper($div);

		
		// by eldin
    	$kodejurnal = '01';


		if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

		if ($kdwilayah != '')
		{
		$sql_wil .= " AND kodewilayah='{$kdwilayah}'";
		}


		$sql = "
			SELECT
			group_name,parent,j.kdperkiraan,
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
			  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
				ELSE 0 END )AS proyek_rupiah_bln_ini 
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_{$div} jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true $sql_add 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan
				AND j.tanggal::date <= '{$periode_akhir}'
				AND isdel = 'f'
				AND isapp = 't'
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
				WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%'
					AND parent <> '' AND parent is not null 
				GROUP BY
					parent, group_name, j.kdperkiraan
				ORDER BY parent, j.kdperkiraan ASC

		";


		//echo '<pre>RINCI: '.$sql.";";
		return $this->db->query($sql);
	}
	
	public function get_total_labarugi($div='',$tahun='',$periode_awal='',$periode_akhir='',$kdwilayah='')
	{
		//echo($div);
		//echo($tahun);
		//echo($periode_awal."<br>");
		//echo($periode_akhir);
		//echo($kdwilayah);
		$div = str_replace("'","",$div);
		$tahun = str_replace("'","",$tahun);
		//$periode_awal = str_replace("'","",$periode_awal);
    	//$periode_akhir = str_replace("'","",date($periode_awal."-"."t")); 
    	$kdwilayah = str_replace("'","",$kdwilayah); 
    	$div = strtoupper($div);
		// $sql = "
			// SELECT
			// parent,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '01%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS divisi_rupiah_sd_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$periode_awal}-01' AND '{$periode_akhir}') AND j.nobukti LIKE '01%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS divisi_rupiah_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '01%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS proyek_rupiah_sd_bln_ini,
			// SUM( CASE WHEN (j.tanggal BETWEEN '{$periode_awal}-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '01%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS proyek_rupiah_bln_ini
				// FROM
					// dperkir dp
				// LEFT JOIN jurnal_{$div} j ON(
					// j.kdperkiraan = dp.kdperkiraan
					// AND j.tanggal <= '{$periode_akhir}'
					// AND isdel = 'f'
					// AND isapp = 't'
				// )
			// LEFT JOIN mapping_neraca m ON m.group_name = group_coa
				// WHERE
					// dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%'
					// AND parent <> '' AND parent is not null 
				// GROUP BY
					// parent
				// ORDER BY parent
		// ";
		$kodejurnal = '01';
		$sql_add = '';
		if ($kdwilayah != '')
		{
			$sql_add = " AND kk.kodewilayah='$kdwilayah'";
			$tmp = $this->_DB->query("SELECT kodejurnal FROM dspk WHERE kdspk='$kdwilayah' AND kddiv='$div' LIMIT 1");
			$item = $tmp->fetchObject();
			$kodejurnal = $item->kodejurnal;
			$sql_add .= " AND (jj.nobukti LIKE '$kodejurnal%' OR jj.nobukti LIKE kk.kodejurnal||'%')";
		}

		
		// by eldin
		$sql = "
			SELECT
				parent,
				--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS divisi_rupiah_sd_bln_ini,
				--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS divisi_rupiah_bln_ini,
				--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah ELSE	0 END )AS proyek_rupiah_sd_bln_ini,
				--SUM( CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' THEN	(CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah	ELSE 0 END )AS proyek_rupiah_bln_ini
				SUM(
					CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
									 THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
						WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
							     THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1  * j.rupiah
					ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
				SUM(
					CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
				    	WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
					ELSE 0 END )AS divisi_rupiah_bln_ini, 
				SUM( 
					CASE WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
						WHEN (j.tanggal::date BETWEEN '{$tahun}-01-01' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah
				  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
				SUM( 
					CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 't' 
					       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
						WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '{$periode_akhir}') AND j.nobukti NOT LIKE '{$kodejurnal}%' AND dp.default_debet = 'f'
					      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*- 1 * j.rupiah 
					ELSE 0 END )AS proyek_rupiah_bln_ini 
				FROM
					dperkir dp
				LEFT JOIN 
      (
       SELECT jj.* FROM jurnal_{$div} jj LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv)
       WHERE TRUE $sql_add
      ) j ON(
					j.kdperkiraan = dp.kdperkiraan
					AND j.tanggal::date <= '{$periode_akhir}'
					AND isdel = 'f'
					--- AND isapp = 't'
				)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
				WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%'
					AND parent <> '' AND parent is not null 
				GROUP BY
					parent
				ORDER BY parent
		";
		//echo "<pre>".$sql.";";
		return $this->_DB->query($sql);
	}
	
	public function get_namaperkiraan($kd)
	{
		$sql = "SELECT nmperkiraan FROM dperkir where kdperkiraan='{$kd}'";
		$query = $this->_DB->query($sql)->fetchObject();
		return $query->nmperkiraan;
	}
	
	public function get_namadivisi($kd,$kdwilayah='')
	{
    	$kd = strtoupper($kd);
    	$kd = str_replace("'","",$kd);
    	$kdwilayah = str_replace("'","",$kdwilayah);
		$sql = "SELECT nmdivisi FROM ddivisi WHERE kddivisi = '$kd'";
		$query = $this->_DB->query($sql)->fetchObject();
    	$ret = $query->nmdivisi;

	    if ($kdwilayah != '')
	    {
	 		$sql = "SELECT nmspk FROM dspk WHERE kdspk='$kdwilayah'";
			$query = $this->_DB->query($sql)->fetchObject();
			$ret .= " : " . $query->nmspk;
	    }
		return $ret;
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
/*
BACKUP QUERY

SELECT
			 $getsubtot parent,
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
				ELSE 0 END )AS divisi_rupiah_sd_bln_ini, 
			SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
				ELSE 0 END )AS divisi_rupiah_bln_ini, 
			-------
			-------
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
			  	ELSE 0 END )AS proyek_rupiah_sd_bln_ini, 
			SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)  * j.rupiah 
				ELSE 0 END )AS proyek_rupiah_bln_ini, 
			------
			------
			( 
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)* j.rupiah 
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
				ELSE 0 END ) 
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
					WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f' 
						THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
			  	ELSE 0 END )
				) AS konsol_rp_divisi_sd_bln_ini,
			
			(
				SUM(
				CASE WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 't' 
							THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
			    	WHEN (j.tanggal::date BETWEEN '$tahun-01-01' AND '$periode_akhir') AND j.nobukti LIKE '$kodejurnal%' AND dp.default_debet = 'f'  
					 		THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah
				ELSE 0 END )
				+
				SUM( 
				CASE WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 't' 
				       	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
					WHEN (j.tanggal::date BETWEEN '{$periode_awal}' AND '$periode_akhir') AND j.nobukti NOT LIKE '$kodejurnal%' AND dp.default_debet = 'f'
				      	THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END) * j.rupiah 
				ELSE 0 END )
			) AS konsol_rp_divisi_bln_ini,
			'0.00' AS persentase ,
			'-' as flag_rumus
		FROM
			dperkir dp
		LEFT JOIN 
    	(
     		SELECT jj.* FROM jurnal_$div jj 
				LEFT JOIN dspk kk ON (jj.kdspk=kk.kdspk AND jj.kddivisi=kk.kddiv) 
     		WHERE true 
    	) j  ON (
				j.kdperkiraan = dp.kdperkiraan AND 
				j.tanggal::date <= '$periode_akhir' AND 
				isdel = 'f' --AND 
				--isapp = 't'
			)
			LEFT JOIN mapping_neraca m ON m.group_name = group_coa
			WHERE
					dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND 
					m.visibility='t' AND
					--m.h_d = 'ST' AND 
					$add_where
					$where_parent 
			GROUP BY
					m.parent, $group_subtot flag_rumus
			ORDER BY $group_subtot parent;

*/