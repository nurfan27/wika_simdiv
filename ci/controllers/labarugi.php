<?php
class Labarugi extends CI_Controller {
	
	private $_BASE_PATH;
	private $_BASE_SIMDIV;
	
	function __construct() {
		parent::__construct();
		$this->output->enable_profiler(FALSE);
		$this->load->model('mdl_report_labarugi','mdl_rl');
		$this->load->library('format');
		$this->load->helper('irulutility');
		$this->_BASE_PATH = $this->config->item('base_cli_path');
		// $this->_BASE_SIMDIV = "http://". gethostname() ."/e-accounting/";
		$this->_BASE_SIMDIV = $this->config->item('base_url_simdiv');
	}
	
	public function index ()
	{
		
	}
	public function cek($month,$year)
	{
		echo date("Y-m-d",mktime(0,0,0,$month,1-1,$year))."<br>";
		echo date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1))."<br>";
		echo date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year))."<br>";
		echo date("Y-m-d",strtotime(mktime(0,0,0,$month+1,1-1,$year)))."<br>";
	}
	
	public function dobackground_ELDIN($kddivisi='',$year='',$month='',$admin='',$title='',$kdwilayah='')
	{
		$POST 		= $this->input->get_post('data');

		$data['bulan'] 	= $month;
		$data['tahun'] 	= $year;
		$data['admin'] 	= $admin;
		$data['month'] 	= $month;
		$data['kdwilayah'] 	= $kdwilayah;
		$month1= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2014-02-04';

		$periode_awal = str_replace("'","",$data['tahun']).'-'.str_replace("'","",$data['bulan']).'-01';
		$periode_akhir = date('Y-m-t', strtotime($periode_awal));
		// First day of the month.
		//echo date('Y-m-01', strtotime($query_date))."<br>";

		// Last day of the month.
		//echo date('Y-m-t', strtotime($query_date));

		//echo $periode_awal."::".$periode_akhir;

		//exit;
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;

		//-----setting model-------
		if ($_SERVER['REMOTE_ADDR'] == '10.10.5.108'){
			// $base->db= true;
			// echo $POST ;
			// print_r($data);exit;
		}
		//echo "<pre>";
		$data['divisi'] 	= $this->mdl_report_labarugi->get_namadivisi(strtoupper($kddivisi),$kdwilayah);
		$hasil 				= $this->mdl_report_labarugi->get_nilai_labarugi(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah);
		$hasil_total 		= $this->mdl_report_labarugi->get_total_labarugi(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah);
		$urutan 			= $this->mdl_report_labarugi->urutan_report_labarugi();
		echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'DEPARTEMEN';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Proyek';
	    if ($kdwilayah != '')
	    {
	      $data['mytitle'] 			= 'DIVISI';
	      $data['mytitle_proyek'] 	= 'PROYEK';
	    }
		
		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header', $data, true);
		
		//----TABLE DATA-----------

    	//print '<pre>'. var_export($hasil,true) .'</pre>';
		$div_bul = 0;
		while(($item = $hasil->fetchObject()) !== false)
		//while(($item = $hasil->result()) !== false)
		{
			$look['divisi_bln_ini'][$item->group_coa][] 		= $item->divisi_rupiah_bln_ini;
			$look['divisi_sd_bln_ini'][$item->group_coa][]  	= $item->divisi_rupiah_sd_bln_ini;
			$look['proyek_bln_ini'][$item->group_coa][]  		= $item->proyek_rupiah_bln_ini;
			$look['proyek_sd_bln_ini'][$item->group_coa][]  	= $item->proyek_rupiah_sd_bln_ini;
			$look['konsolidasi_bln_ini'][$item->group_coa][] 	= $item->divisi_rupiah_bln_ini + $item->proyek_rupiah_bln_ini;
			$look['konsolidasi_sd_bln_ini'][$item->group_coa][] = $item->divisi_rupiah_sd_bln_ini + $item->proyek_rupiah_sd_bln_ini;
			$lihat[$item->group_coa] 	= (object)array('nilai'=>$item->divisi_rupiah_bln_ini);
			$liat[$item->flag_rumus] 	= (object)array('valoe'=>$item->divisi_rupiah_bln_ini);
			
			//$output .= $this->load->view('report/labarugi/labarugi_table_data', $data, true);
			$div_bul +=$item->divisi_rupiah_bln_ini + $item->proyek_rupiah_bln_ini;
		}
		//echo $div_bul;

		while(($item = $hasil_total->fetchObject())!== false)
		{
			$vTotal['divisi_sd'][$item->parent][] 	= $item->divisi_rupiah_sd_bln_ini;
			$vTotal['divisi_ini'][$item->parent][] 	= $item->divisi_rupiah_bln_ini;
			$vTotal['proyek_sd'][$item->parent][] 	= $item->proyek_rupiah_sd_bln_ini;
			$vTotal['proyek_ini'][$item->parent][]	= $item->proyek_rupiah_bln_ini;

		}
		//print_r($vTotal['divisi_sd']);
		$total=array();
		//$vSTotal=array();
		$i=0;
		$total_st = 0;
		//var_dump($urutan->fetchObject());exit;
		$A600 = $B400 = $C000 = $E000 = $G000 = $I600 = $J000 = 0;

		while(($item = $urutan->fetchObject()) !== false)
		{
			$data['nmperkiraan']=(trim($item->h_d)==='H')?'<b><h3>'.$item->description.'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item->description;

			//ADD DATA TO DISPLAY h_d == D.
			//if($item->group_name=='A600'){
				//echo $item->group_name.' >> '.$lihat[$item->group_name]->nilai."::<br>";
			//}
			//else{
			//	return false;
			//} 



			$data['konsolidasi_bln_ini'] 	= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['konsolidasi_bln_ini'][$item->group_name]) ):'' ;
			$data['konsolidasi_sd_bln_ini'] = (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['konsolidasi_sd_bln_ini'][$item->group_name]) ):'' ;
			$data['divisi_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['divisi_bln_ini'][$item->group_name]) ):'' ;
			$data['divisi_sd_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['divisi_sd_bln_ini'][$item->group_name]) ):'' ;
			$data['proyek_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['proyek_bln_ini'][$item->group_name]) ):'' ;			
			$data['proyek_sd_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['proyek_sd_bln_ini'][$item->group_name]) ):'';
			
			//SETTING TOTAL 
		 	$vSTotal['divisi_sd'][$item->group_name][] 	= array_sum($vTotal['divisi_sd'][$item->parent]);
			$vSTotal['divisi_ini'][$item->group_name][] = array_sum($vTotal['divisi_ini'][$item->parent]);
			$vSTotal['proyek_sd'][$item->group_name][] 	= array_sum($vTotal['proyek_sd'][$item->parent]);
			$vSTotal['proyek_ini'][$item->group_name][] = array_sum($vTotal['proyek_ini'][$item->parent]);

			
			//print_r(array($item->flag_rumus=>$lihat[$item->group_name]->nilai));
			//print_r(array($item->flag_rumus=>$item->sum_type));
				/*if($item->group_name=='A600')
					$A600 = $lihat[$item->group_name]->nilai;
				if($item->group_name=='B400')
					$B400 = $lihat[$item->group_name]->nilai;
				if($item->group_name=='C000')
					$C000 = $lihat[$item->group_name]->nilai;
				if($item->group_name=='G000')
					$G000 = $lihat[$item->group_name]->nilai;
				if($item->group_name=='I600')
					$I600 = $lihat[$item->group_name]->nilai;

				echo $J000 = $A600-$B400+$E000-$G000+$I600;
				echo 'TOTAL = '.$J000.":::<br>";
		*/
			if( (trim($item->h_d)==='T') AND ($item->sum_type!='') ):
				$sum_item = explode(';',$item->sum_type);

				$sum_type1 = $item->flag_rumus;
				var_dump($sum_type1);var_dump($sum_item);
				foreach($sum_item as $ival)
                {
                	$vTotalGroup['divisi_sd'][$item->group_name][]	= array_sum($vSTotal['divisi_sd'][$ival]);
                	$vTotalGroup['divisi_ini'][$item->group_name][]	= array_sum($vSTotal['divisi_ini'][$ival]);
                	$vTotalGroup['proyek_sd'][$item->group_name][]	= array_sum($vSTotal['proyek_sd'][$ival]);
					$vTotalGroup['proyek_ini'][$item->group_name][]	= array_sum($vSTotal['proyek_ini'][$ival]);
					//var_dump($ival);
				}
				
				
				//ADD DATA TO TOTAL
				$vTotal['divisi_sd'][$item->parent][] 	= array_sum($vTotalGroup['divisi_sd'][$item->group_name]);
				$vTotal['divisi_ini'][$item->parent][] 	= array_sum($vTotalGroup['divisi_ini'][$item->group_name]);
				$vTotal['proyek_sd'][$item->parent][] 	= array_sum($vTotalGroup['proyek_sd'][$item->group_name]);
				$vTotal['proyek_ini'][$item->parent][]	= array_sum($vTotalGroup['proyek_ini'][$item->group_name]);
				
				//TOTAL DATA
				$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['proyek_ini'][$item->group_name])+array_sum($vTotalGroup['divisi_ini'][$item->group_name]));
				$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotalGroup['proyek_sd'][$item->group_name])+array_sum($vTotalGroup['divisi_sd'][$item->group_name]));
				$data['total_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotalGroup['divisi_ini'][$item->group_name]));
				$data['total_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['divisi_sd'][$item->group_name]));
				$data['total_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotalGroup['proyek_ini'][$item->group_name]));
				$data['total_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['proyek_sd'][$item->group_name]));
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total', $data, true);	
							
			elseif(trim($item->h_d)==='T'):
					if($sum_item==='B400'){
						$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]))*-1;
					}else{
						$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
					}
					//$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
					$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent])+array_sum($vTotal['divisi_sd'][$item->parent]));
					$data['total_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotal['divisi_ini'][$item->parent]));
					$data['total_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['divisi_sd'][$item->parent]));
					$data['total_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
					$data['total_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent]));
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total', $data, true);
				
			elseif(trim($item->h_d)==='ST'):
				if($sum_item==='B400'){
					$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]))*-1;
				}else{
					$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
				}
				//$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
				$data['subtotal_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent])+array_sum($vTotal['divisi_sd'][$item->parent]));
				$data['subtotal_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotal['divisi_ini'][$item->parent]));
				$data['subtotal_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['divisi_sd'][$item->parent]));
				$data['subtotal_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
				$data['subtotal_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent]));
				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal', $data, true);

			elseif(trim($item->h_d)==='GT'):
				$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
				//$data['subtotal_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent])+array_sum($vTotal['divisi_sd'][$item->parent]));
				//$data['subtotal_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotal['divisi_ini'][$item->parent]));
				//$data['subtotal_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['divisi_sd'][$item->parent]));
				//$data['subtotal_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
				//$data['subtotal_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent]));
				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal', $data, true);

				
				
			else:

			 	$output .= $this->load->view('report/labarugi/labarugi_table_data', $data, true);
			endif;
			
		}
		//echo $output; 

		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi.html";		
		$filename_excel = "LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi_for_excel.html";
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}
	
	public function generate_report($kddivisi='',$year='',$month='',$admin='',$title='',$kdwilayah='')
	{
		//ini_set('error_reporting', E_ALL);
		//$data = array();
		
		$POST 		= $this->input->get_post('data');
		//var_dump($POST);
		$data['bulan'] 	= $POST['periode']['month'];
		$data['namabulan'] 	= bulan($data['bulan'] );
		$data['tahun'] 	= $POST['periode']['year'];
		$data['admin'] 	= $admin;
		$data['month'] 	= $month;
		$data['kdwilayah'] 	= $kdwilayah;
		$year = $POST['periode']['year'];
		$bulan = $POST['periode']['month'];
		//$month1		= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		//$month3 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		//$month4 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2008-01-01';

		$periode_awal = str_replace("'","",$data['tahun'].'-'.$data['bulan'].'-01');
		$periode_akhir = date("Y-m-t", strtotime($periode_awal));
		
		

		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		//echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'PUSAT';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Pusat';
	    if ($kdwilayah != '')
	    {
	      $data['mytitle'] 			= 'PUSAT';
	      $data['mytitle_proyek'] 	= 'PUSAT';
	    }
		
		$TPU = $TBLP = $TBPP = $TBTL = $TBK = $LKO = $TBD = $LKJ = $LKJO = $TBU = $LUS = $TPBL = $LSB = array();
	    $data['divisi'] 	= $this->mdl_rl->get_namadivisi(strtoupper($kddivisi),$kdwilayah);
		//print_r(array(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdwilayah,"51"));
		$TPU 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"51"); 
		$TBLP 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"41"); 
		$TBPP 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"43"); 
		$TBTL 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"48"); 
		$TBU 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"49"); 
		$PBL 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"52"); 
		$TBK 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"41|43|48"); 
		//var_dump($TBK);exit;
		$LKO 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$bulan,$kdwilayah,"322");
		$TPBL_ 	= $PBL[0]['konsol_rp_divisi_bln_ini']<0? ($PBL[0]['konsol_rp_divisi_bln_ini']*-1):$PBL[0]['konsol_rp_divisi_bln_ini'];
		//var_dump($TPBL_);exit;
		$LKJO 	= array(
					'konsol_rp_divisi_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ),
					'konsol_rp_divisi_sd_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] ),
					'divisi_rupiah_bln_ini'=> ( ($TPU[0]['divisi_rupiah_bln_ini']-$TBK[0]['divisi_rupiah_bln_ini']) + $LKO[0]['divisi_rupiah_bln_ini'] ),
					'divisi_rupiah_sd_bln_ini'=> ( ($TPU[0]['divisi_rupiah_sd_bln_ini']-$TBK[0]['divisi_rupiah_sd_bln_ini']) + $LKO[0]['divisi_rupiah_sd_bln_ini'] ),
					'proyek_rupiah_bln_ini'=> ( ($TPU[0]['proyek_rupiah_bln_ini']-$TBK[0]['proyek_rupiah_bln_ini']) + $LKO[0]['proyek_rupiah_bln_ini'] ),
					'proyek_rupiah_sd_bln_ini'=> ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ),
					)
				;
		$lsb_proyek = (( ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ) - $TBU[0]['proyek_rupiah_sd_bln_ini'] ) );

		//OLD STYLE DULU, YG PENTING JALAN DULU LAH
		//---------
		//var_dump($TPU[0]['konsol_rp_divisi_bln_ini'].'----'.$TBK[0]['konsol_rp_divisi_bln_ini']);exit;
		$total_rp = array(
		'TPU'=>array(
				'konsol_rp_divisi_bln_ini'=>$TPU[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TPU[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TPU[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TPU[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TPU[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TPU[0]['proyek_rupiah_sd_bln_ini']
			),
		'TBLP'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBLP[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBLP[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TBLP[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TBLP[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TBLP[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TBLP[0]['proyek_rupiah_sd_bln_ini']
			),
		'TBPP'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBPP[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBPP[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TBPP[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TBPP[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TBPP[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TBPP[0]['proyek_rupiah_sd_bln_ini']
			),
		'TBTL'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBTL[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBTL[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TBTL[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TBTL[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TBTL[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TBTL[0]['proyek_rupiah_sd_bln_ini']
			),
		// 'TBK'=>array(
				// 'konsol_rp_divisi_bln_ini'		=>$TBK[0]['konsol_rp_divisi_bln_ini'],
				// 'konsol_rp_divisi_sd_bln_ini'	=>$TBK[0]['konsol_rp_divisi_sd_bln_ini'],
				// 'divisi_rupiah_bln_ini'			=>$TBK[0]['divisi_rupiah_bln_ini'],
				// 'divisi_rupiah_sd_bln_ini'		=>$TBK[0]['divisi_rupiah_sd_bln_ini'],
				// 'proyek_rupiah_bln_ini'			=>$TBK[0]['proyek_rupiah_bln_ini'],
				// 'proyek_rupiah_sd_bln_ini'		=>$TBK[0]['proyek_rupiah_sd_bln_ini']
			// ),
		'TBK'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBK[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBK[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TBK[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TBK[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TBK[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TBK[0]['proyek_rupiah_sd_bln_ini']
			),
		'LKO'=>array(
				'konsol_rp_divisi_bln_ini'=>($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']),
				'konsol_rp_divisi_sd_bln_ini'=>($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']),
				'divisi_rupiah_bln_ini'=>($TPU[0]['divisi_rupiah_bln_ini']-$TBK[0]['divisi_rupiah_bln_ini']),
				'divisi_rupiah_sd_bln_ini'=>($TPU[0]['divisi_rupiah_sd_bln_ini']-$TBK[0]['divisi_rupiah_sd_bln_ini']),
				'proyek_rupiah_bln_ini'=>($TPU[0]['proyek_rupiah_bln_ini']-$TBK[0]['proyek_rupiah_bln_ini']),
				'proyek_rupiah_sd_bln_ini'=>($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini'])
			),
		'LKJO' => array(
					'konsol_rp_divisi_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ),
					'konsol_rp_divisi_sd_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] ),
					'divisi_rupiah_bln_ini'=> ( ($TPU[0]['divisi_rupiah_bln_ini']-$TBK[0]['divisi_rupiah_bln_ini']) + $LKO[0]['divisi_rupiah_bln_ini'] ),
					'divisi_rupiah_sd_bln_ini'=> ( ($TPU[0]['divisi_rupiah_sd_bln_ini']-$TBK[0]['divisi_rupiah_sd_bln_ini']) + $LKO[0]['divisi_rupiah_sd_bln_ini'] ),
					'proyek_rupiah_bln_ini'=> ( ($TPU[0]['proyek_rupiah_bln_ini']-$TBK[0]['proyek_rupiah_bln_ini']) + $LKO[0]['proyek_rupiah_bln_ini'] ),
					'proyek_rupiah_sd_bln_ini'=> ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] )
					),
		'TBU'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBU[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBU[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>$TBU[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>$TBU[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>$TBU[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$TBU[0]['proyek_rupiah_sd_bln_ini']
			),
		'LUS'=> array(
				'konsol_rp_divisi_bln_ini'=>( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ) - $TBU[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] ) - $TBU[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=>( ($TPU[0]['divisi_rupiah_bln_ini']-$TBK[0]['divisi_rupiah_bln_ini']) + $LKO[0]['divisi_rupiah_bln_ini'] ) - $TBU[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=>( ($TPU[0]['divisi_rupiah_sd_bln_ini']-$TBK[0]['divisi_rupiah_sd_bln_ini']) + $LKO[0]['divisi_rupiah_sd_bln_ini'] ) - $TBU[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=>( ($TPU[0]['proyek_rupiah_bln_ini']-$TBK[0]['proyek_rupiah_bln_ini']) + $LKO[0]['proyek_rupiah_bln_ini'] ) - $TBU[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ) - $TBU[0]['proyek_rupiah_sd_bln_ini']
			),
		'PBL'=>array(
				'konsol_rp_divisi_bln_ini' => $PBL[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=> $PBL[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'=> $PBL[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'=> $PBL[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'=> $PBL[0]['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=> $PBL[0]['proyek_rupiah_sd_bln_ini']
				),
		'LSB'=> array(
				'konsol_rp_divisi_bln_ini'		=>(( ( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ) - $TBU[0]['konsol_rp_divisi_bln_ini']) - $TPBL_),
				'konsol_rp_divisi_sd_bln_ini'	=>(( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] ) - $TBU[0]['konsol_rp_divisi_sd_bln_ini'])+$PBL[0]['konsol_rp_divisi_sd_bln_ini'],
				'divisi_rupiah_bln_ini'			=>(( ($TPU[0]['divisi_rupiah_bln_ini']-$TBK[0]['divisi_rupiah_bln_ini']) + $LKO[0]['divisi_rupiah_bln_ini'] ) - $TBU[0]['divisi_rupiah_bln_ini'])+$PBL[0]['divisi_rupiah_bln_ini'],
				'divisi_rupiah_sd_bln_ini'		=>(( ($TPU[0]['divisi_rupiah_sd_bln_ini']-$TBK[0]['divisi_rupiah_sd_bln_ini']) + $LKO[0]['divisi_rupiah_sd_bln_ini'] ) - $TBU[0]['divisi_rupiah_sd_bln_ini'])+$PBL[0]['divisi_rupiah_sd_bln_ini'],
				'proyek_rupiah_bln_ini'			=>(( ( ($TPU[0]['proyek_rupiah_bln_ini']-$TBK[0]['proyek_rupiah_bln_ini']) + $LKO[0]['proyek_rupiah_bln_ini'] ) - $TBU[0]['proyek_rupiah_bln_ini'] )+$PBL[0]['proyek_rupiah_bln_ini'] ),
				'proyek_rupiah_sd_bln_ini'		=>(( ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ) - $TBU[0]['proyek_rupiah_sd_bln_ini'] )+ $PBL[0]['proyek_rupiah_sd_bln_ini']),
				),
		
		);
		//var_dump($total_rp);exit;
		//print_r($total_rp);//['LSB'][0]['konsol_rp_divisi_bln_ini'];
		//exit;
		/*
		$tbk_konsol_rp_divisi_bln_ini 		=  $TBK[0]['konsol_rp_divisi_bln_ini'];
		$tbk_konsol_rp_divisi_sd_bln_ini 	=  $TBK[0]['konsol_rp_divisi_sd_bln_ini'];
		$tbk_divisi_rupiah_bln_ini 			=  $TBK[0]['divisi_rupiah_bln_ini'];
		$tbk_divisi_rupiah_sd_bln_ini 		=  $TBK[0]['divisi_rupiah_sd_bln_ini'];
		$tbk_proyek_rupiah_bln_ini 			=  $TBK[0]['proyek_rupiah_bln_ini'];
		$tbk_proyek_rupiah_sd_bln_ini 		=  $TBK[0]['proyek_rupiah_sd_bln_ini'];

		$tbpp_konsol_rp_divisi_bln_ini 		=  $TBPP[0]['konsol_rp_divisi_bln_ini'];
		$tbpp_konsol_rp_divisi_sd_bln_ini 	=  $TBPP[0]['konsol_rp_divisi_sd_bln_ini'];
		$tbpp_divisi_rupiah_bln_ini 		=  $TBPP[0]['divisi_rupiah_bln_ini'];
		$tbpp_divisi_rupiah_sd_bln_ini 		=  $TBPP[0]['divisi_rupiah_sd_bln_ini'];
		$tbpp_proyek_rupiah_bln_ini 		=  $TBPP[0]['proyek_rupiah_bln_ini'];
		$tbpp_proyek_rupiah_sd_bln_ini 		=  $TBPP[0]['proyek_rupiah_sd_bln_ini'];

		$tbu_konsol_rp_divisi_bln_ini 		=  $TBU[0]['konsol_rp_divisi_bln_ini'];
		$tbu_konsol_rp_divisi_sd_bln_ini 	=  $TBU[0]['konsol_rp_divisi_sd_bln_ini'];
		$tbu_divisi_rupiah_bln_ini 			=  $TBU[0]['divisi_rupiah_bln_ini'];
		$tbu_divisi_rupiah_sd_bln_ini 		=  $TBU[0]['divisi_rupiah_sd_bln_ini'];
		$tbu_proyek_rupiah_bln_ini 			=  $TBU[0]['proyek_rupiah_bln_ini'];
		$tbu_proyek_rupiah_sd_bln_ini 		=  $TBU[0]['proyek_rupiah_sd_bln_ini'];

		$lko_konsol_rp_divisi_bln_ini 		=  $LKO[0]['konsol_rp_divisi_bln_ini'];
		$lko_konsol_rp_divisi_sd_bln_ini 	=  $LKO[0]['konsol_rp_divisi_sd_bln_ini'];
		$lko_divisi_rupiah_bln_ini 			=  $LKO[0]['divisi_rupiah_bln_ini'];
		$lko_divisi_rupiah_sd_bln_ini 		=  $LKO[0]['divisi_rupiah_sd_bln_ini'];
		$lko_proyek_rupiah_bln_ini 			=  $LKO[0]['proyek_rupiah_bln_ini'];
		$lko_proyek_rupiah_sd_bln_ini 		=  $LKO[0]['proyek_rupiah_sd_bln_ini'];

		$lkjo_konsol_rp_divisi_bln_ini 		=  $LKJO[0]['konsol_rp_divisi_bln_ini'];
		$lkjo_konsol_rp_divisi_sd_bln_ini 	=  $LKJO[0]['konsol_rp_divisi_sd_bln_ini'];
		$lkjo_divisi_rupiah_bln_ini 			=  $LKJO[0]['divisi_rupiah_bln_ini'];
		$lkjo_divisi_rupiah_sd_bln_ini 		=  $LKJO[0]['divisi_rupiah_sd_bln_ini'];
		$lkjo_proyek_rupiah_bln_ini 			=  $LKJO[0]['proyek_rupiah_bln_ini'];
		$lkjo_proyek_rupiah_sd_bln_ini 		=  $LKJO[0]['proyek_rupiah_sd_bln_ini'];
		*/
		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header', $data, true);
		
		$rows = $this->mdl_rl->get_group_report('D');
		//var_dump($rows);
		foreach ($rows as $row)
		{
	        $data['parent'][] = $row['parent']; //$subtot['rows'][$row['parent']][]
	        $subtot['rows'][$row['parent']][] = $this->mdl_rl->get_rows_labarugi(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdwilayah,$row['parent'],true);
			//print_r($subtot['rows']);
		}
		//$subtot = $data['row_data'][0]['konsol_rp_divisi_bln_ini'];

		foreach($subtot['rows'] as $rows => $val){
			//print_r($val[0][0]);
			$valu = $val[0][0]['parent'];
			$niai_subtot[$valu] = $val[0][0];
			//print_r($niai_subtot);
		}
		//print_r($niai_subtot);
		//print_r($subtot['rows'][$row['parent']]);
		//exit;
		$urutan = $this->mdl_rl->get_report_format();
		$result = $this->mdl_rl->get_rows_labarugi(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdwilayah,'','');
		// echo '<pre>';
		// print_r($result);
		// die;

		//var_dump($result);
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';

		foreach($result as $item){
			
			//var_dump($items);
			$baris['konsol_rp_divisi_bln_ini'][$item['group_name']][] 		= $item['konsol_rp_divisi_bln_ini'];
			$baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][] 	= $item['konsol_rp_divisi_sd_bln_ini'];
			$baris['divisi_rupiah_bln_ini'][$item['group_name']][] 			= $item['divisi_rupiah_bln_ini'];
			$baris['divisi_rupiah_sd_bln_ini'][$item['group_name']][] 		= $item['divisi_rupiah_sd_bln_ini'];
			$baris['proyek_rupiah_bln_ini'][$item['group_name']][]  		= $item['proyek_rupiah_bln_ini'];
			$baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][]  		= $item['proyek_rupiah_sd_bln_ini'];

		}

		$i=0;
		//var_dump($baris);exit;
		$konsol_rp_divisi_bln_ini = 0;
		$konsol_rp_divisi_sd_bln_ini = 0;
		$divisi_rupiah_bln_ini = 0;
		$proyek_rupiah_bln_ini = 0;
		$divisi_rupiah_sd_bln_ini = 0;
		$proyek_rupiah_sd_bln_ini = 0;
		

		foreach($urutan as $items => $item) {

			$data['nmperkiraan']=( $item['h_d'] ==='H')?'<b><h3>'.$item['description'].'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item['description'];
			$data['h_d']		=$item['h_d'];
			
			$data['konsol_divisi_bln_ini']			= ( $item['h_d'] ==='D')?$this->format->number($baris['konsol_rp_divisi_bln_ini'][$item['group_name']][0]):'';
			$data['konsol_divisi_sd_bln_ini']		= ( $item['h_d'] ==='D')?$this->format->number($baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][0]):'';
			$data['divisi_bln_ini']					= ( $item['h_d'] ==='D')?$this->format->number($baris['divisi_rupiah_bln_ini'][$item['group_name']][0]):'';
			$data['divisi_sd_bln_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['divisi_rupiah_sd_bln_ini'][$item['group_name']][0]):'';
			$data['proyek_bln_ini']					= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_bln_ini'][$item['group_name']][0]):'';
			$data['proyek_sd_bln_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0]):'';
			
			$konsol_rp_divisi_bln_ini 		+= $baris['konsol_rp_divisi_bln_ini'][$item['group_name']][0];
			$konsol_rp_divisi_sd_bln_ini 	+= $baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][0];
			$divisi_rupiah_bln_ini 			+= $baris['divisi_rupiah_bln_ini'][$item['group_name']][0];
			$divisi_rupiah_sd_bln_ini 		+= $baris['divisi_rupiah_sd_bln_ini'][$item['group_name']][0];
			$proyek_rupiah_bln_ini 			+= $baris['proyek_rupiah_bln_ini'][$item['group_name']][0];
			$proyek_rupiah_sd_bln_ini 		+= $baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0];

			if($urutan[$items+1]['parent']!==$item['parent']){
				$konsol_rp_divisi_bln_ini = 0;
				$konsol_rp_divisi_sd_bln_ini = 0;
				$divisi_rupiah_bln_ini = 0;
				$divisi_rupiah_sd_bln_ini = 0;
				$proyek_rupiah_bln_ini = 0;
				$proyek_rupiah_sd_bln_ini = 0;
			}
			

			if( $item['h_d'] === 'ST') {

				$data['subtotal_konsolidasi_bln_ini']		= $this->format->number($konsol_rp_divisi_bln_ini); // $this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_bln_ini']);
				$data['subtotal_konsolidasi_sd_bln_ini']	= $this->format->number($konsol_rp_divisi_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_sd_bln_ini']);
				$data['subtotal_divisi_bln_ini']			= $this->format->number($divisi_rupiah_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['divisi_rupiah_bln_ini']);
				$data['subtotal_divisi_sd_bln_ini']			= $this->format->number($divisi_rupiah_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['divisi_rupiah_sd_bln_ini']);
				$data['subtotal_proyek_bln_ini']			= $this->format->number($proyek_rupiah_bln_ini);
				$data['subtotal_proyek_sd_bln_ini']			= $this->format->number($proyek_rupiah_sd_bln_ini);
				$data['subtot_parent']						= $item['parent'];
				$data['koderumus']							= $item['flag_rumus'];

				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal', $data, true);
			}
			elseif( trim($item['h_d'])==='T') {

				if(trim($item['flag_rumus'])==='LSB'){
					$total_konsolidasi_bln_ini 		= $total_rp['LSB']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LSB']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['LSB']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['LSB']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['LSB']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['LSB']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBPP'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBPP']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBPP']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['TBPP']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['TBPP']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['TBPP']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['TBPP']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBU'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBU']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBU']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['TBU']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['TBU']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['TBU']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['TBU']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LKO'){
					$total_konsolidasi_bln_ini 		= $total_rp['LKO']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LKO']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['LKO']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['LKO']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['LKO']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['LKO']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LKJO'){
					$total_konsolidasi_bln_ini 		= $total_rp['LKJO']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LKJO']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['LKJO']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['LKJO']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['LKJO']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['LKJO']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBK'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBK']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBK']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['TBK']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['TBK']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['TBK']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['TBK']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='PBL'){
					$total_konsolidasi_bln_ini 		= $total_rp['PBL']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['PBL']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['PBL']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['PBL']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['PBL']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['PBL']['proyek_rupiah_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LUS'){
					$total_konsolidasi_bln_ini 		= $total_rp['LUS']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LUS']['konsol_rp_divisi_sd_bln_ini'];
					$total_divisi_rupiah_bln_ini 	= $total_rp['LUS']['divisi_rupiah_bln_ini'];
					$total_divisi_rupiah_sd_bln_ini = $total_rp['LUS']['divisi_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_bln_ini 	= $total_rp['LUS']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini = $total_rp['LUS']['proyek_rupiah_sd_bln_ini'];
				}

				//$total_konsol_rp_divisi_bln_ini += $konsol_rp_divisi_bln_ini;
				
				$data['total_konsolidasi_bln_ini'] 		= $this->format->number($total_konsolidasi_bln_ini);//$this->format->number($konsol_rp_divisi_bln_ini);//$this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_bln_ini']);
				$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number($total_konsolidasi_sd_bln_ini);//$this->format->number($konsol_rp_divisi_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_sd_bln_ini']);
				$data['total_divisi_bln_ini'] 			= $this->format->number($total_divisi_rupiah_bln_ini);
				$data['total_divisi_sd_bln_ini'] 		= $this->format->number($total_divisi_rupiah_sd_bln_ini);
				$data['total_proyek_bln_ini'] 			= $this->format->number($total_proyek_rupiah_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_bln_ini']);
				$data['total_proyek_sd_bln_ini'] 		= $this->format->number($total_proyek_rupiah_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_sd_bln_ini']);
				$data['subtot_parent']					= $item['parent'];
				$data['koderumus']						= $item['flag_rumus'];
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total', $data, true);
			}
			
			$output .= $this->load->view('report/labarugi/labarugi_table_data', $data, true);
		}
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);

		//----- CREATE FILE-------
		$filename 		= str_replace("'","","LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi.html");		
		$filename_excel = str_replace("'","","LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi_for_excel.html");
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}
	
	public function generate_report_proyek($kddivisi='',$year='',$month='',$admin='',$title='',$nobukti='',$kdspk = '')
	{
		//ini_set('error_reporting', E_ALL);
		//$data = array();
		$POST 		= $this->input->get_post('data');
		//var_dump($POST);
		$data['bulan'] 	= $POST['periode']['month'];
		$data['namabulan'] 	= bulan($data['bulan'] );
		$data['tahun'] 	= $POST['periode']['year'];
		$data['admin'] 	= $admin;
		$data['month'] 	= $month;
		$data['kdspk'] 	= $kdspk;
		$year = $POST['periode']['year'];
		$bulan = $POST['periode']['month'];
		//$month1		= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		//$month3 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		//$month4 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2008-01-01';

		$periode_awal = str_replace("'","",$data['tahun'].'-'.$data['bulan'].'-01');
		$periode_akhir = date("Y-m-t", strtotime($periode_awal));

		print_r($periode_awal);
		print_r($periode_akhir);
		die;
		
		

		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		//echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'DEPARTEMEN';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Proyek';
	    if ($kdspk != '')
	    {
	      $data['mytitle'] 			= 'PROYEK';
	      $data['mytitle_proyek'] 	= 'PROYEK';
	    }
		
		$TPU = $TBLP = $TBPP = $TBTL = $TBK = $LKO = $TBD = $LKJ = $LKJO = $TBU = $LUS = $TPBL = $LSB = array();
	    $data['divisi'] 	= $this->mdl_rl->get_namadivisi(strtoupper($kddivisi),$kdspk);
	    $data['nobukti'] 	= str_replace("'","",$nobukti);
	    //var_dump($data['divisi'] );exit;
		//print_r(array(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,"51"));
		$TPU 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"A000",'');
		$TBLP 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"B100",''); 
		$TBPP 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"B200",''); 
		$TBTL 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"B300",''); 
		$TBU 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"F810",''); 
		$PBL 	= $this->mdl_rl->get_rows_labarugi_proyek_st(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,"I000",''); 
		$TBK 	= array(
				'proyek_rupiah_sd_thn_lalu'=>($TBLP[0]['proyek_rupiah_sd_thn_lalu']+$TBPP[0]['proyek_rupiah_sd_thn_lalu']+$TBTL[0]['proyek_rupiah_sd_thn_lalu']),
				'proyek_rupiah_sd_bln_lalu'=>($TBLP[0]['proyek_rupiah_sd_bln_lalu']+$TBPP[0]['proyek_rupiah_sd_bln_lalu']+$TBTL[0]['proyek_rupiah_sd_bln_lalu']),
				'proyek_rupiah_bln_ini'=>($TBLP[0]['proyek_rupiah_bln_ini']+$TBPP[0]['proyek_rupiah_bln_ini']+$TBTL[0]['proyek_rupiah_bln_ini']),
				'proyek_rupiah_sd_bln_ini'=>($TBLP[0]['proyek_rupiah_sd_bln_ini']+$TBPP[0]['proyek_rupiah_sd_bln_ini']+$TBTL[0]['proyek_rupiah_sd_bln_ini']),
				'proyek_rupiah_sd_thn_ini'=>($TBLP[0]['proyek_rupiah_sd_thn_ini']+$TBPP[0]['proyek_rupiah_sd_thn_ini']+$TBTL[0]['proyek_rupiah_sd_thn_ini'])
			);
		$LKO 	= array(
				'proyek_rupiah_sd_thn_lalu'=>($TPU[0]['proyek_rupiah_sd_thn_lalu']-$TBK['proyek_rupiah_sd_thn_lalu']),
				'proyek_rupiah_sd_bln_lalu'=>($TPU[0]['proyek_rupiah_sd_bln_lalu']-$TBK['proyek_rupiah_sd_bln_lalu']),
				'proyek_rupiah_bln_ini'=>($TPU[0]['proyek_rupiah_bln_ini']-$TBK['proyek_rupiah_bln_ini']),
				'proyek_rupiah_sd_bln_ini'=>($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK['proyek_rupiah_sd_bln_ini']),
				'proyek_rupiah_sd_thn_ini'=>($TPU[0]['proyek_rupiah_sd_thn_ini']-$TBK['proyek_rupiah_sd_thn_ini'])
			);
		$LKJO 	= array(
				'proyek_rupiah_sd_thn_lalu'=>$LKO['proyek_rupiah_sd_thn_lalu'],
				'proyek_rupiah_sd_bln_lalu'=>$LKO['proyek_rupiah_sd_bln_lalu'],
				'proyek_rupiah_bln_ini'=>$LKO['proyek_rupiah_bln_ini'],
				'proyek_rupiah_sd_bln_ini'=>$LKO['proyek_rupiah_sd_bln_ini'],
				'proyek_rupiah_sd_thn_ini'=>$LKO['proyek_rupiah_sd_thn_ini']
			);
		$LUS 	= array(
				'proyek_rupiah_sd_thn_lalu'=>($LKO['proyek_rupiah_sd_thn_lalu']-$TBU[0]['proyek_rupiah_sd_thn_lalu']),
				'proyek_rupiah_sd_bln_lalu'=>($LKO['proyek_rupiah_sd_bln_lalu']-$TBU[0]['proyek_rupiah_sd_bln_lalu']),
				'proyek_rupiah_bln_ini'=>($LKO['proyek_rupiah_bln_ini']-$TBU[0]['proyek_rupiah_bln_ini']),
				'proyek_rupiah_sd_bln_ini'=>($LKO['proyek_rupiah_sd_bln_ini']-$TBU[0]['proyek_rupiah_sd_bln_ini']),
				'proyek_rupiah_sd_thn_ini'=>($LKO['proyek_rupiah_sd_thn_ini']-$TBU[0]['proyek_rupiah_sd_thn_ini']),
			);
		$LSB 	= array(
				'proyek_rupiah_sd_thn_lalu'=>($LUS['proyek_rupiah_sd_thn_lalu']+$PBL[0]['proyek_rupiah_sd_thn_lalu']),
				'proyek_rupiah_sd_bln_lalu'=>($LUS['proyek_rupiah_sd_bln_lalu']+$PBL[0]['proyek_rupiah_sd_bln_lalu']),
				'proyek_rupiah_bln_ini'=>($LUS['proyek_rupiah_bln_ini']+$PBL[0]['proyek_rupiah_bln_ini']),
				'proyek_rupiah_sd_bln_ini'=>($LUS['proyek_rupiah_sd_bln_ini']+$PBL[0]['proyek_rupiah_sd_bln_ini']),
				'proyek_rupiah_sd_thn_ini'=>($LUS['proyek_rupiah_sd_thn_ini']+$PBL[0]['proyek_rupiah_sd_thn_ini']),
			);

		// echo '<h3>ini LKO<h3>';
		// echo '<br>';
		// echo '<hr>';
		// echo '<br>';
		// print_r($LKO);
		// echo '<br>';
		// echo '<br>';
		// echo '<hr>';
		// echo '<br>';
		// die;

		//var_dump($TBK);exit;
		
		//$TPBL_ 	= $PBL[0]['konsol_rp_divisi_bln_ini']<0? ($PBL[0]['konsol_rp_divisi_bln_ini']*-1):$PBL[0]['konsol_rp_divisi_bln_ini'];
		//var_dump($TPBL_);exit;
		//OLD STYLE DULU, YG PENTING JALAN DULU LAH
		//---------
		//var_dump($TPU[0]['konsol_rp_divisi_bln_ini'].'----'.$TBK[0]['konsol_rp_divisi_bln_ini']);exit;
		$total_rp = array(
		'TPU'	=> $TPU,
		'TBLP'	=> $TBLP,
		'TBPP'	=> $BPP,
		'TBTL'	=> $TBTL,
		'TBK'	=> $TBK,
		'LKO'	=> $LKO,
		'LKJO' 	=> $LKJO,
		'TBU'	=> $TBU,
		'PBL'	=> $PBL,
		'LUS'	=> $LUS,
		'LSB'	=> $LSB,
		
		);
		//var_dump($total_rp);exit;
		//print_r($total_rp);//['LSB'][0]['konsol_rp_divisi_bln_ini'];
		//exit;

		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header_proyek', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header_proyek', $data, true);
		
		$rows = $this->mdl_rl->get_group_report('D');
		//var_dump($rows);
		foreach ($rows as $row)
		{
	        $data['parent'][] = $row['parent']; //$subtot['rows'][$row['parent']][]
	        $subtot['rows'][$row['parent']][] = $this->mdl_rl->get_rows_labarugi_proyek(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,$row['parent'],true);
			//print_r($subtot['rows']);
		}
		
		foreach($subtot['rows'] as $rows => $val){
			//print_r($val[0][0]);
			$valu = $val[0][0]['parent'];
			$niai_subtot[$valu] = $val[0][0];
			//print_r($niai_subtot);
		}
		//print_r($niai_subtot);
		//print_r($subtot['rows'][$row['parent']]);
		//exit;
		$urutan = $this->mdl_rl->get_report_proyek_format();
		$result = $this->mdl_rl->get_rows_labarugi_proyek(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdspk,$nobukti,'','');
		//echo '<pre>';
		//print_r($result);
		//die;

		//var_dump($result);
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';

		foreach($result as $item){
			
			//var_dump($items);
			$baris['proyek_rupiah_sd_thn_lalu'][$item['group_name']][]  	= $item['proyek_rupiah_sd_thn_lalu'];
			$baris['proyek_rupiah_sd_bln_lalu'][$item['group_name']][]  	= $item['proyek_rupiah_sd_bln_lalu'];
			$baris['proyek_rupiah_bln_ini'][$item['group_name']][]  		= $item['proyek_rupiah_bln_ini'];
			$baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][]  		= $item['proyek_rupiah_sd_bln_ini'];
			$baris['proyek_rupiah_sd_thn_ini'][$item['group_name']][]  		= $item['proyek_rupiah_sd_thn_ini'];
		}

		$i=0;
		//var_dump($baris);exit;
		$proyek_rupiah_bln_ini = 0;
		$proyek_rupiah_sd_bln_ini = 0;
		$proyek_rupiah_sd_bln_lalu = 0;
		

		foreach($urutan as $items => $item) {

			// if($item['h_d'] != 'ST') 
			// {
			// 	if($item['h_d'] == 'H')
			// 	{
			// 		$data['nmperkiraan']='<b><h3>'.$item['description'].'</h3></b>';
			// 		$data['h_d']		=$item['h_d'];
			// 	}
			// 	elseif($item['h_d'] == 'D')
			// 	{
			// 		$data['nmperkiraan']='&nbsp;&nbsp;&nbsp;'.$item['description'];
			// 		$data['h_d']		=$item['h_d'];
			// 	}
			// }
			// else
			// {

			// }
			$data['nmperkiraan']=( $item['h_d'] ==='H')?'<b><h3>'.$item['description'].'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item['description'];
			$data['h_d']		=$item['h_d'];
			
			$data['proyek_sd_thn_lalu']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_thn_lalu'][$item['group_name']][0]):'';
			$data['proyek_sd_bln_lalu']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_bln_lalu'][$item['group_name']][0]):'';
			$data['proyek_bln_ini']					= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_bln_ini'][$item['group_name']][0]):'';
			$data['proyek_sd_bln_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0]):'';
			$data['proyek_sd_thn_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_thn_ini'][$item['group_name']][0]):'';

			$proyek_rupiah_sd_thn_lalu 		+= $baris['proyek_rupiah_sd_thn_lalu'][$item['group_name']][0];
			$proyek_rupiah_sd_bln_lalu 		+= $baris['proyek_rupiah_sd_bln_lalu'][$item['group_name']][0];
			$proyek_rupiah_bln_ini 			+= $baris['proyek_rupiah_bln_ini'][$item['group_name']][0];
			$proyek_rupiah_sd_bln_ini 		+= $baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0];
			$proyek_rupiah_sd_thn_ini 		+= $baris['proyek_rupiah_sd_thn_ini'][$item['group_name']][0];

			if($urutan[$items+1]['parent']!==$item['parent']){
				$proyek_rupiah_sd_thn_lalu = 0;
				$proyek_rupiah_sd_bln_lalu = 0;
				$proyek_rupiah_bln_ini = 0;
				$proyek_rupiah_sd_bln_ini = 0;
				$proyek_rupiah_sd_thn_ini = 0;
			}
			// }

			if( $item['h_d'] === 'ST') {
				// $data['nmperkiraan']='&nbsp;&nbsp;&nbsp;'.$item['description'];
				// $data['h_d']		=$item['h_d'];
				$data['subtotal_proyek_sd_thn_lalu']		= $this->format->number($proyek_rupiah_sd_thn_lalu);
				$data['subtotal_proyek_sd_bln_lalu']		= $this->format->number($proyek_rupiah_sd_bln_lalu);
				$data['subtotal_proyek_bln_ini']			= $this->format->number($proyek_rupiah_bln_ini);
				$data['subtotal_proyek_sd_bln_ini']			= $this->format->number($proyek_rupiah_sd_bln_ini);
				$data['subtotal_proyek_sd_thn_ini']			= $this->format->number($proyek_rupiah_sd_thn_ini);
				$data['subtot_parent']						= $item['parent'];
				$data['koderumus']							= $item['flag_rumus'];

				// Dimatiin sementara
				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal_proyek', $data, true);
			}
			elseif( trim($item['h_d'])==='T') {

				if(trim($item['flag_rumus'])==='LSB'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['LSB']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['LSB']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['LSB']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['LSB']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['LSB']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='TBPP'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['TBPP']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['TBPP']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['TBPP']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['TBPP']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['TBPP']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='TBU'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['TBU']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['TBU']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['TBU']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['TBU']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['TBU']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='LKO'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['LKO']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['LKO']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['LKO']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['LKO']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['LKO']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='LKJO'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['LKJO']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['LKJO']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['LKJO']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['LKJO']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['LKJO']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='TBK'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['TBK']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['TBK']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['TBK']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['TBK']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['TBK']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='PBL'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['PBL']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['PBL']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['PBL']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['PBL']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['PBL']['proyek_rupiah_sd_thn_ini'];
				}elseif(trim($item['flag_rumus'])==='LUS'){
					$total_proyek_rupiah_sd_thn_lalu 	= $total_rp['LUS']['proyek_rupiah_sd_thn_lalu'];
					$total_proyek_rupiah_sd_bln_lalu 	= $total_rp['LUS']['proyek_rupiah_sd_bln_lalu'];
					$total_proyek_rupiah_bln_ini 		= $total_rp['LUS']['proyek_rupiah_bln_ini'];
					$total_proyek_rupiah_sd_bln_ini 	= $total_rp['LUS']['proyek_rupiah_sd_bln_ini'];
					$total_proyek_rupiah_sd_thn_ini 	= $total_rp['LUS']['proyek_rupiah_sd_thn_ini'];
				}

				//$total_konsol_rp_divisi_bln_ini += $konsol_rp_divisi_bln_ini;
				$data['total_proyek_sd_thn_lalu'] 		= $this->format->number($total_proyek_rupiah_sd_thn_lalu); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_sd_bln_ini']);
				$data['total_proyek_sd_bln_lalu'] 		= $this->format->number($total_proyek_rupiah_sd_bln_lalu); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_sd_bln_ini']);
				$data['total_proyek_bln_ini'] 			= $this->format->number($total_proyek_rupiah_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_bln_ini']);
				$data['total_proyek_sd_bln_ini'] 		= $this->format->number($total_proyek_rupiah_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_sd_bln_ini']);
				$data['total_proyek_sd_thn_ini'] 		= $this->format->number($total_proyek_rupiah_sd_thn_ini); //$this->format->number($niai_subtot[$item['parent']]['proyek_rupiah_sd_bln_ini']);
				$data['subtot_parent']					= $item['parent'];
				$data['koderumus']						= $item['flag_rumus'];
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total_proyek', $data, true);
			}
			
			$output .= $this->load->view('report/labarugi/labarugi_table_data_proyek', $data, true);
		}
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);

		//----- CREATE FILE-------
		$filename 		= str_replace("'","","LABARUGIPROYEK_".strtoupper($kddivisi)."_".strtoupper($kdspk)."_".strtoupper($nobukti)."_{$year}_".ltrim($month,'0')."_proyek.html");		
		$filename_excel = str_replace("'","","LABARUGIPROYEK_".strtoupper($kddivisi)."_".strtoupper($kdspk)."_".strtoupper($nobukti)."_{$year}_".ltrim($month,'0')."_proyek_for_excel.html");
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}

	public function generate_report_kompre($kddivisi='',$year='',$month='',$admin='',$title='',$kdwilayah='')
	{

		//ini_set('error_reporting', E_ALL);
		//$data = array();
		//echo '<pre>';
		$POST 		= $this->input->post();
		//var_dump($POST);die();
		$data['bulan'] 			= $POST['month_'];
		$data['namabulan'] 		= bulan($data['bulan'] );
		$data['tahun'] 			= $POST['year_'];
		$data['admin'] 			= $admin;
		$data['month'] 			= $month;
		$data['kdwilayah'] 		= $kdwilayah;
		$year 					= $POST['year_'];
		$bulan 					= $POST['month_'];
		$month1		= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2008-01-01';

		$periode_awal = str_replace("'","",$POST['year_']).'-'.str_replace("'","",$data['bulan']).'-01';
		$periode_akhir = date('Y-m-t', strtotime($periode_awal));
		
		$data['tahun_ini'] = str_replace("'","",$year);
		$data['tahun_lalu'] = str_replace("'","",$year)-1;

		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		//echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'DEPARTEMEN';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Proyek';
	    if ($kdwilayah != '')
	    {
	      $data['mytitle'] 			= 'KOMPREHENSIF';
	      $data['mytitle_proyek'] 	= 'PROYEK';
	    }

	    //$qq = $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"32212");

	    if ($bulan == 1 || $bulan == 3 || $bulan == 5 || $bulan == 7 || $bulan == 9 || $bulan == 10 || $bulan == 12 ) {
			$tanggal = date("Y-m-d",mktime(0,0,0,$bulan,31,$year));
		}elseif ($bulan == 2) {
			$tanggal = date("Y-m-d",mktime(0,0,0,$bulan,28,$year));
		}else{
			$tanggal = date("Y-m-d",mktime(0,0,0,$bulan,30,$year));
		}

	    $d=0;
	    $c=0;
	    $sql = $this->db->query("SELECT dk, SUM(rupiah) as jml FROM jurnal_v WHERE tanggal BETWEEN '".$year."-01-01' AND '".$tanggal."' AND kdperkiraan = '32212' GROUP BY dk;")->result();
		foreach ($sql as $isi) {
			if ($isi->dk == 'D') {
				$d = $isi->jml;
			}else{
				$c = $isi->jml;
			}
		}
		$coa32212 = $d - $c;

		$TPU = $TBLP = $TBPP = $TBTL = $TBK = $LKO = $TBD = $LKJ = $LKJO = $TBU = $LUS = $TPBL = $LSB = array();
	    $data['divisi'] 	= $this->mdl_rl->get_namadivisi(strtoupper($kddivisi),$kdwilayah);
		
		$TPU 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"51"); 
		$TBLP 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"41"); 
		$TBPP 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"43"); 
		$TBTL 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"48"); 
		$TBU 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"49"); 
		$PBL 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"52"); 
		$TBK 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"41|43|48"); 
		$LKO 	= $this->mdl_rl->get_total_bygroup_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"322"); 


		$TPBL_ 	= $PBL[0]['konsol_rp_divisi_bln_ini']<0? ($PBL[0]['konsol_rp_divisi_bln_ini']*-1):$PBL[0]['konsol_rp_divisi_bln_ini'];

		$LKJO 	= array(
					'konsol_rp_divisi_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ),
					'konsol_rp_divisi_sd_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] )
					)
				;
		//var_dump($LKO);die();

		$lsb_proyek = (( ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ) - $TBU[0]['proyek_rupiah_sd_bln_ini'] ) );

		//TEMBAK LANGSUNG DULU, YG PENTING JALAN DULU LAH
		//---------

		$tpu_0a = $TPU[0]['konsol_rp_divisi_bln_ini'] * -1;
		$tpu_0b = $TPU[0]['konsol_rp_divisi_sd_bln_ini'] * -1;

		$LKJO_a = (($tpu_0a-($TBLP[0]['konsol_rp_divisi_bln_ini']+$TBPP[0]['konsol_rp_divisi_bln_ini']+$TBTL[0]['konsol_rp_divisi_bln_ini']))+($coa32212 *-1));
		$LKJO_b = (($tpu_0b-($TBLP[0]['konsol_rp_divisi_sd_bln_ini']+$TBPP[0]['konsol_rp_divisi_sd_bln_ini']+$TBTL[0]['konsol_rp_divisi_sd_bln_ini']))+($coa32212 *-1));
		$LUS_a = ($tpu_0a-($TBLP[0]['konsol_rp_divisi_bln_ini']+$TBPP[0]['konsol_rp_divisi_bln_ini']+$TBTL[0]['konsol_rp_divisi_bln_ini']))+($coa32212 *-1)-$TBU[0]['konsol_rp_divisi_bln_ini'];
		$LUS_b = ($tpu_0b-($TBLP[0]['konsol_rp_divisi_sd_bln_ini']+$TBPP[0]['konsol_rp_divisi_sd_bln_ini']+$TBTL[0]['konsol_rp_divisi_sd_bln_ini']))+($coa32212 *-1) - $TBU[0]['konsol_rp_divisi_sd_bln_ini'];

		$total_rp = array(
		'TPU'=>array(
				'konsol_rp_divisi_bln_ini'=>$TPU[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TPU[0]['konsol_rp_divisi_sd_bln_ini']
			),
		'TBLP'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBLP[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBLP[0]['konsol_rp_divisi_sd_bln_ini']
			),
		'TBPP'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBPP[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBPP[0]['konsol_rp_divisi_sd_bln_ini']
			),
		'TBTL'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBTL[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBTL[0]['konsol_rp_divisi_sd_bln_ini']
			),
		//Total biaya konstruksi
		'TBK'=>array(
				'konsol_rp_divisi_bln_ini'=>($TBLP[0]['konsol_rp_divisi_bln_ini']+$TBPP[0]['konsol_rp_divisi_bln_ini']+$TBTL[0]['konsol_rp_divisi_bln_ini']),
				'konsol_rp_divisi_sd_bln_ini'=>($TBLP[0]['konsol_rp_divisi_sd_bln_ini']+$TBPP[0]['konsol_rp_divisi_sd_bln_ini']+$TBTL[0]['konsol_rp_divisi_sd_bln_ini'])
			),
		'LKO'=>array(
				'konsol_rp_divisi_bln_ini'=>($tpu_0a-($TBLP[0]['konsol_rp_divisi_bln_ini']+$TBPP[0]['konsol_rp_divisi_bln_ini']+$TBTL[0]['konsol_rp_divisi_bln_ini'])),
				'konsol_rp_divisi_sd_bln_ini'=>($tpu_0b-($TBLP[0]['konsol_rp_divisi_sd_bln_ini']+$TBPP[0]['konsol_rp_divisi_sd_bln_ini']+$TBTL[0]['konsol_rp_divisi_sd_bln_ini']))
			),
		'LKJO' => array(
					// 'konsol_rp_divisi_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_bln_ini']-$TBK[0]['konsol_rp_divisi_bln_ini']) + $LKO[0]['konsol_rp_divisi_bln_ini'] ),
					// 'konsol_rp_divisi_sd_bln_ini'=> ( ($TPU[0]['konsol_rp_divisi_sd_bln_ini']-$TBK[0]['konsol_rp_divisi_sd_bln_ini']) + $LKO[0]['konsol_rp_divisi_sd_bln_ini'] )
					'konsol_rp_divisi_bln_ini'=> $LKJO_a,
					'konsol_rp_divisi_sd_bln_ini'=> $LKJO_b
					),
		'TBU'=>array(
				'konsol_rp_divisi_bln_ini'=>$TBU[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=>$TBU[0]['konsol_rp_divisi_sd_bln_ini']
			),
		'LUS'=> array(
				'konsol_rp_divisi_bln_ini'=> $LUS_a,
				'konsol_rp_divisi_sd_bln_ini'=> $LUS_b
			),
		'PBL'=>array(
				'konsol_rp_divisi_bln_ini' => $PBL[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'=> $PBL[0]['konsol_rp_divisi_sd_bln_ini']
				),
		'LSB'=> array(
				'konsol_rp_divisi_bln_ini'		=>$LUS_a - $PBL[0]['konsol_rp_divisi_bln_ini'],
				'konsol_rp_divisi_sd_bln_ini'	=>$LUS_b - $PBL[0]['konsol_rp_divisi_sd_bln_ini']
				),
		
		);
		// echo '<pre>';
		// // print_r($total_rp['LKO']);
		// print_r($total_rp);//['LSB'][0]['konsol_rp_divisi_bln_ini'];
		// exit;
		
		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header_kompre', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header_kompre', $data, true);
		
		$rows = $this->mdl_rl->get_group_report('D');
		//var_dump($rows);
		foreach ($rows as $row)
		{
	        $data['parent'][] = $row['parent']; //$subtot['rows'][$row['parent']][]
	        $subtot['rows'][$row['parent']][] = $this->mdl_rl->get_rows_labarugi_kompre(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdwilayah,$row['parent'],true);
			//print_r($subtot['rows']);
		}
		//$subtot = $data['row_data'][0]['konsol_rp_divisi_bln_ini'];

		foreach($subtot['rows'] as $rows => $val){
			//print_r($val[0][0]);
			$valu = $val[0][0]['parent'];
			$niai_subtot[$valu] = $val[0][0];
			//print_r($niai_subtot);
		}
		//print_r($niai_subtot);
		//print_r($subtot['rows'][$row['parent']]);
		//exit;
		$urutan = $this->mdl_rl->get_report_format();
		$result = $this->mdl_rl->get_rows_labarugi_kompre(strtolower($kddivisi),$year,$bulan,$periode_awal,$periode_akhir,$kdwilayah,'','');

		//var_dump($result);
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';

		foreach($result as $item){
			
			//var_dump($items);
			$baris['konsol_rp_divisi_bln_ini'][$item['group_name']][] 		= $item['konsol_rp_divisi_bln_ini'];
			$baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][] 	= $item['konsol_rp_divisi_sd_bln_ini'];
		}

		$i=0;
		//var_dump($baris);exit;
		$konsol_rp_divisi_bln_ini = 0;
		$konsol_rp_divisi_sd_bln_ini = 0;
		

		foreach($urutan as $items => $item) {

			$data['nmperkiraan']=( $item['h_d'] ==='H')?'<b><h3>'.$item['description'].'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item['description'];
			$data['h_d']		=$item['h_d'];
			
			$data['rupiah_now']		= ( $item['h_d'] ==='D')?$this->format->number($baris['konsol_rp_divisi_bln_ini'][$item['group_name']][0]):'';
			$data['rupiah_last']	= ( $item['h_d'] ==='D')?$this->format->number($baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][0]):'';
			//$data['divisi_bln_ini']					= ( $item['h_d'] ==='D')?$this->format->number($baris['divisi_rupiah_bln_ini'][$item['group_name']][0]):'';
			//$data['divisi_sd_bln_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['divisi_rupiah_sd_bln_ini'][$item['group_name']][0]):'';
			//$data['proyek_bln_ini']					= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_bln_ini'][$item['group_name']][0]):'';
			//$data['proyek_sd_bln_ini']				= ( $item['h_d'] ==='D')?$this->format->number($baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0]):'';
			
			$konsol_rp_divisi_bln_ini 		+= $baris['konsol_rp_divisi_bln_ini'][$item['group_name']][0];
			$konsol_rp_divisi_sd_bln_ini 	+= $baris['konsol_rp_divisi_sd_bln_ini'][$item['group_name']][0];
			//$divisi_rupiah_bln_ini 			+= $baris['divisi_rupiah_bln_ini'][$item['group_name']][0];
			//$divisi_rupiah_sd_bln_ini 		+= $baris['divisi_rupiah_sd_bln_ini'][$item['group_name']][0];
			//$proyek_rupiah_bln_ini 			+= $baris['proyek_rupiah_bln_ini'][$item['group_name']][0];
			//$proyek_rupiah_sd_bln_ini 		+= $baris['proyek_rupiah_sd_bln_ini'][$item['group_name']][0];

			if($urutan[$items+1]['parent']!==$item['parent']){
				$konsol_rp_divisi_bln_ini = 0;
				$konsol_rp_divisi_sd_bln_ini = 0;
				//$divisi_rupiah_bln_ini = 0;
				//$divisi_rupiah_sd_bln_ini = 0;
				//$proyek_rupiah_bln_ini = 0;
				//$proyek_rupiah_sd_bln_ini = 0;
			}
			

			if( $item['h_d'] === 'ST') {

				$data['subtotal_konsolidasi_bln_ini']		= $this->format->number($konsol_rp_divisi_bln_ini); // $this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_bln_ini']);
				$data['subtotal_konsolidasi_sd_bln_ini']	= $this->format->number($konsol_rp_divisi_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_sd_bln_ini']);
				//$data['subtotal_divisi_bln_ini']			= $this->format->number($divisi_rupiah_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['divisi_rupiah_bln_ini']);
				//$data['subtotal_divisi_sd_bln_ini']			= $this->format->number($divisi_rupiah_sd_bln_ini); //$this->format->number($niai_subtot[$item['parent']]['divisi_rupiah_sd_bln_ini']);
				//$data['subtotal_proyek_bln_ini']			= $this->format->number($proyek_rupiah_bln_ini);
				//$data['subtotal_proyek_sd_bln_ini']			= $this->format->number($proyek_rupiah_sd_bln_ini);
				$data['subtot_parent']						= $item['parent'];
				$data['koderumus']							= $item['flag_rumus'];

				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal_kompre', $data, true);
			}
			elseif( trim($item['h_d'])==='T') {

				if(trim($item['flag_rumus'])==='LSB'){
					$total_konsolidasi_bln_ini 		= $total_rp['LSB']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LSB']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBPP'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBPP']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBPP']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBU'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBU']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBU']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LKO'){
					$total_konsolidasi_bln_ini 		= $total_rp['LKO']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LKO']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LKJO'){
					$total_konsolidasi_bln_ini 		= $total_rp['LKJO']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LKJO']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='TBK'){
					$total_konsolidasi_bln_ini 		= $total_rp['TBK']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['TBK']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='PBL'){
					$total_konsolidasi_bln_ini 		= $total_rp['PBL']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['PBL']['konsol_rp_divisi_sd_bln_ini'];
				}elseif(trim($item['flag_rumus'])==='LUS'){
					$total_konsolidasi_bln_ini 		= $total_rp['LUS']['konsol_rp_divisi_bln_ini'];
					$total_konsolidasi_sd_bln_ini 	= $total_rp['LUS']['konsol_rp_divisi_sd_bln_ini'];
				}

				//$total_konsol_rp_divisi_bln_ini += $konsol_rp_divisi_bln_ini;
				
				$data['total_konsolidasi_bln_ini'] 		= $this->format->number($total_konsolidasi_bln_ini);//$this->format->number($konsol_rp_divisi_bln_ini);//$this->format->number($niai_subtot[$item['parent']]['konsol_rp_divisi_bln_ini']);
				$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number($total_konsolidasi_sd_bln_ini);//$this->format->number($konsol_rp_divisi_sd_bln_ini); //$this->format->
				$data['subtot_parent']					= $item['parent'];
				$data['koderumus']						= $item['flag_rumus'];
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total_kompre', $data, true);
			}
			
			$output .= $this->load->view('report/labarugi/labarugi_table_data_kompre', $data, true);
		}
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);

		//----- CREATE FILE-------
		$filename 		= str_replace("'","","LABARUGI_KOMPREHENSIF_".strtoupper($kddivisi)."_".$year."_".ltrim($month,'0')."_divisi.html");		
		$filename_excel = str_replace("'","","LABARUGI_KOMPREHENSIF_".strtoupper($kddivisi)."_".$year."_".ltrim($month,'0')."_divisi_for_excel.html");
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}

	public function generate_report_kompreQQ($kddivisi='',$year='',$month='',$admin='',$title='',$kdwilayah='')
	{
		//ini_set('error_reporting', E_ALL);
		//$data = array();
		$kddivisi = 'v';
		$month = str_replace("'","",$month);
		$tahun = str_replace("'","",$year);
		$POST 		= $this->input->get_post('data');

		$data['bulan'] 	= $month;
		$data['namabulan'] 	= bulan($month);
		$data['tahun'] 	= $year;
		$data['admin'] 	= $admin;
		$data['month'] 	= $month;
		$data['kdwilayah'] 	= $kdwilayah;
		$month1		= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 	= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2014-02-04';

		$periode_awal = str_replace("'","",$data['tahun']).'-'.str_replace("'","",$data['bulan']).'-01';
		$periode_akhir = date('Y-m-t', strtotime($periode_awal));
		
		

		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		//echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'DEPARTEMEN';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Proyek';
	    if ($kdwilayah != '')
	    {
	      $data['mytitle'] 			= 'DIVISI';
	      $data['mytitle_proyek'] 	= 'PROYEK';
	    }
		
		//$TPU = $TBLP = $TBPP = $TBTL = $TBK = $LKO = $TBD = $LKJ = $LKJO = $TBU = $LUS = $TPBL = $LSB = array();
	    $data['divisi'] 	= $this->mdl_rl->get_namadivisi(strtoupper($kddivisi),$kdwilayah);
		/*
		$TPU 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"51"); 
		$TBLP 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"41"); 
		$TBPP 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"43"); 
		$TBTL 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"48"); 
		$TBU 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"49"); 
		$PBL 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"52"); 
		$TBK 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"41|43|48"); 
		$LKO 	= $this->mdl_rl->get_total_bygroup(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,"322"); 

		$TPBL_ 	= $PBL[0]['tahun_ini']<0? ($PBL[0]['tahun_ini']*-1):$PBL[0]['tahun_ini'];

		$LKJO 	= array(
					'tahun_ini'=> ( ($TPU[0]['tahun_ini']-$TBK[0]['tahun_ini']) + $LKO[0]['tahun_ini'] ),
					'tahun_lalu'=> ( ($TPU[0]['tahun_lalu']-$TBK[0]['tahun_lalu']) + $LKO[0]['tahun_lalu'] )
					)
				;
		$lsb_proyek = (( ( ($TPU[0]['proyek_rupiah_sd_bln_ini']-$TBK[0]['proyek_rupiah_sd_bln_ini']) + $LKO[0]['proyek_rupiah_sd_bln_ini'] ) - $TBU[0]['proyek_rupiah_sd_bln_ini'] ) );

		//OLD STYLE DULU, YG PENTING JALAN DULU LAH
		//---------
		$total_rp = array(
		'TPU'=>array(
				'rupiah_now'=>$TPU[0]['rupiah_now'],
				'tahun_lalu'=>$TPU[0]['tahun_lalu']
			),
		'TBLP'=>array(
				'tahun_ini'=>$TBLP[0]['tahun_ini'],
				'tahun_lalu'=>$TBLP[0]['tahun_lalu']
			),
		'TBPP'=>array(
				'rupiah_now'=>$TBPP[0]['tahun_ini'],
				'tahun_lalu'=>$TBPP[0]['tahun_lalu']
			),
		'TBTL'=>array(
				'rupiah_now'=>$TBTL[0]['tahun_ini'],
				'tahun_lalu'=>$TBTL[0]['tahun_lalu']
			),
		'TBK'=>array(
				'rupiah_now'=>$TBK[0]['tahun_ini'],
				'tahun_lalu'=>$TBK[0]['tahun_lalu']
			),
		'LKO'=>array(
				'rupiah_now'=>($TPU[0]['tahun_ini']-$TBK[0]['tahun_ini']),
				'tahun_lalu'=>($TPU[0]['tahun_lalu']-$TBK[0]['tahun_lalu'])
			),
		'LKJO' => array(
				'rupiah_now'=> ( ($TPU[0]['tahun_ini']-$TBK[0]['tahun_ini']) + $LKO[0]['tahun_ini'] ),
				'tahun_lalu'=> ( ($TPU[0]['tahun_lalu']-$TBK[0]['tahun_lalu']) + $LKO[0]['tahun_lalu'] )
					),
		'TBU'=>array(
				'rupiah_now'=>$TBU[0]['tahun_ini'],
				'tahun_lalu'=>$TBU[0]['tahun_lalu']
			),
		'LUS'=> array(
				'rupiah_now'=>( ($TPU[0]['tahun_ini']-$TBK[0]['tahun_ini']) + $LKO[0]['tahun_ini'] ) - $TBU[0]['tahun_ini'],
				'tahun_lalu'=>( ($TPU[0]['tahun_lalu']-$TBK[0]['tahun_lalu']) + $LKO[0]['tahun_lalu'] ) - $TBU[0]['tahun_lalu']
				),
		'PBL'=>array(
				'rupiah_now' => $PBL[0]['tahun_ini'],
				'tahun_lalu'=> $PBL[0]['tahun_lalu']
				),
		'LSB'=> array(
				'rupiah_now'		=>(( ( ($TPU[0]['tahun_ini']-$TBK[0]['tahun_ini']) + $LKO[0]['tahun_ini'] ) - $TBU[0]['tahun_ini']) - $TPBL_),
				'tahun_lalu'	=>(( ( ($TPU[0]['tahun_lalu']-$TBK[0]['tahun_lalu']) + $LKO[0]['tahun_lalu'] ) - $TBU[0]['tahun_lalu'] ) - $TPBL_)
				),
		
		);
		*/
		//print_r($total_rp);//['LSB'][0]['tahun_ini'];
		//exit;
		
		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header_kompre', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header_kompre', $data, true);
		
		$rows = $this->mdl_rl->get_group_report('D');
		//var_dump($rows);
		foreach ($rows as $row)
		{
	        $data['parent'][] = $row['parent']; //$subtot['rows'][$row['parent']][]
	        $subtot['rows'][$row['parent']][] = $this->mdl_rl->get_rows_labarugi_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,$row['parent'],true);
			//print_r($subtot['rows']);
		}
		//$subtot = $data['row_data'][0]['tahun_ini'];

		foreach($subtot['rows'] as $rows => $val){
			//print_r($val[0][0]);
			$valu = $val[0][0]['parent'];
			$niai_subtot[$valu] = $val[0][0];
			//print_r($niai_subtot);
		}
		//print_r($niai_subtot);
		//print_r($subtot['rows'][$row['parent']]);
		//exit;
		$urutan = $this->mdl_rl->get_report_format();
		$result = $this->mdl_rl->get_rows_labarugi_kompre(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah,'','');
		echo '<pre>';
		//var_dump($result);
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';
		//echo '------------------------------------------------------------------------------------------------------------------------------------------<br>';

		foreach($result as $item){
			
			//var_dump($item);echo '<br>';
			//$baris['rupiah_now'][$item['parent']][]		= $item['rupiah_now'];
			$baris['rupiah_last'][$item['parent']][] 	= $item['rupiah_last'];
			$baris['rupiah_now_1'][$item['parent']][$item['group_coa']][]  = $item['rupiah_now'];
			$baris['rupiah_now'][$item['group_coa']][]  = $item['rupiah_now'];
			//echo '<br>';
			$t['tes'][$item['parent']][$item['group_coa']][] = array($item['rupiah_now']);echo '<br>';
		}

		//echo '+++++++++++++<br>';
		//print_r($bris['rupiah_now'][$item['parent']][0]);
		//print_r($baris['rupiah_now_1']);
		echo '================================NOW<br>';
		print_r($baris['rupiah_now']);
		echo '================================LAST<br>';
		print_r($baris['rupiah_last']['A000']);
		echo '================================NOW_1<br>';
		print_r($baris['rupiah_now_1']);
		//exit;
		$tahun_ini = 0;
		$tahun_lalu = 0;
		
		//exit;
		foreach($urutan as $items => $item) {
			//echo '>>>'.print_r($baris['rupiah_now'][$item['parent']][$item['group_coa']][0][0]);
			$data['nmperkiraan']=( $item['h_d'] ==='H')?'<b><h3>'.$item['description'].'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item['description'];
			
			$data['rupiah_now']		= ( $item['h_d'] ==='D')?$baris['rupiah_now'][$item['parent']][0]:'';
			$data['rupiah_last']	= ( $item['h_d'] ==='D')?$this->format->number($baris['rupiah_last'][$item['parent']][0]):'';
			
			$tahun_ini 		+= $baris['rupiah_now'][$item['group_coa']][0];
			$tahun_lalu 	+= $baris['rupiah_last'][$item['group_coa']][0];

			if($urutan[$items+1]['parent']!==$item['parent']){
				$tahun_ini = 0;
				$tahun_lalu = 0;
			}
			

			if( $item['h_d'] === 'ST') {

				$data['subtotal_tahun_ini']		= $this->format->number($tahun_ini); // $this->format->number($niai_subtot[$item['parent']]['tahun_ini']);
				$data['subtotal_tahun_lalu']	= $this->format->number($tahun_lalu); //$this->format->number($niai_subtot[$item['parent']]['tahun_lalu']);
				$data['subtot_parent']			= $item['parent'];
				$data['koderumus']				= $item['flag_rumus'];

				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal_kompre', $data, true);
			}
			elseif( trim($item['h_d'])==='T') {

				if(trim($item['flag_rumus'])==='LSB'){
					$total_tahun_ini 		= $total_rp['LSB']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['LSB']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='TBPP'){
					$total_tahun_ini 		= $total_rp['TBPP']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['TBPP']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='TBU'){
					$total_tahun_ini 		= $total_rp['TBU']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['TBU']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='LKO'){
					$total_tahun_ini 		= $total_rp['LKO']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['LKO']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='LKJO'){
					$total_tahun_ini 		= $total_rp['LKJO']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['LKJO']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='TBK'){
					$total_tahun_ini 		= $total_rp['TBK']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['TBK']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='PBL'){
					$total_tahun_ini 		= $total_rp['PBL']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['PBL']['tahun_lalu'];
				}elseif(trim($item['flag_rumus'])==='LUS'){
					$total_tahun_ini 		= $total_rp['LUS']['rupiah_now'];
					$total_tahun_lalu 	= $total_rp['LUS']['tahun_lalu'];
				}

				//$total_tahun_ini += $tahun_ini;
				
				$data['total_tahun_ini'] 		= $this->format->number($total_tahun_ini);//$this->format->number($tahun_ini);//$this->format->number($niai_subtot[$item['parent']]['tahun_ini']);
				$data['total_tahun_lalu'] 	= $this->format->number($total_tahun_lalu);//$this->format->number($tahun_lalu); //$this->format->number($niai_subtot[$item['parent']]['tahun_lalu']);
				$data['subtot_parent']					= $item['parent'];
				$data['koderumus']						= $item['flag_rumus'];
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total_kompre', $data, true);
			}
			
			$output .= $this->load->view('report/labarugi/labarugi_table_data_kompre', $data, true);
		}
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);

		//----- CREATE FILE-------
		$filename 		= str_replace("'","","LABARUGI_KOMPREHENSIF_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi.html");		
		$filename_excel = str_replace("'","","LABARUGI_KOMPREHENSIF_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi_for_excel.html");
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}
	public function dobackground($kddivisi='',$year='',$month='',$admin='',$title='',$kdwilayah='')
	{
		
		//ini_set('error_reporting', E_ALL);

		$POST 		= $this->input->get_post('data');

		$data['bulan'] 	= $month;
		$data['tahun'] 	= $year;
		$data['admin'] 	= $admin;
		$data['month'] 	= $month;
		$data['kdwilayah'] 	= $kdwilayah;
		$month1= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$query_date = '2014-02-04';

		$periode_awal = str_replace("'","",$data['tahun']).'-'.str_replace("'","",$data['bulan']).'-01';
		$periode_akhir = date('Y-m-t', strtotime($periode_awal));
		// First day of the month.
		//echo date('Y-m-01', strtotime($query_date))."<br>";

		// Last day of the month.
		//echo date('Y-m-t', strtotime($query_date));

		//echo $periode_awal."::".$periode_akhir;

		//exit;
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;

		//-----setting model-------
		if ($_SERVER['REMOTE_ADDR'] == '10.10.5.108'){
			// $base->db= true;
			// echo $POST ;
			// print_r($data);exit;
		}
		//echo "<pre>";
		$data['divisi'] 	= $this->mdl_rl->get_namadivisi(strtoupper($kddivisi),$kdwilayah);

		$hasil_total 		= $this->mdl_rl->get_total_labarugi(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah);
		$urutan 			= $this->mdl_rl->urutan_report_labarugi();
		$hasil 				= $this->mdl_rl->get_nilai_labarugi_ELDIN(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah);
		$hasil2 			= $this->mdl_rl->get_nilai_labarugi_ELDIN(strtolower($kddivisi),$year,$periode_awal,$periode_akhir,$kdwilayah);
		$group_report		= $this->mdl_rl->get_group_report();
		//echo '<pre>';
		
		//----setting view --------
	    $data['mytitle'] = 'DEPARTEMEN';
	    // $data['mytitle_proyek'] = 'KONSOLIDASI DIVISI';
	    $data['mytitle_proyek'] 	= 'Proyek';
	    if ($kdwilayah != '')
	    {
	      $data['mytitle'] 			= 'DIVISI';
	      $data['mytitle_proyek'] 	= 'PROYEK';
	    }
		
		//----HEADER----
		$output = $this->load->view('report/labarugi/labarugi_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/labarugi/labarugi_table_header', $data, true);
		
		//var_dump($group_report);exit;
		//----TABLE DATA-----------

    	//print '<pre>'. var_export($hasil,true) .'</pre>';exit;
		$div_bul = 0;
		while(($item = $hasil->fetchObject()) !== false)
		//while(($item = $hasil->result()) !== false)
		{
			$look['divisi_bln_ini'][$item->group_coa][] 		= $item->divisi_rupiah_bln_ini;
			$look['divisi_sd_bln_ini'][$item->group_coa][]  	= $item->divisi_rupiah_sd_bln_ini;
			$look['proyek_bln_ini'][$item->group_coa][]  		= $item->proyek_rupiah_bln_ini;
			$look['proyek_sd_bln_ini'][$item->group_coa][]  	= $item->proyek_rupiah_sd_bln_ini;
			$look['konsolidasi_bln_ini'][$item->group_coa][] 	= $item->divisi_rupiah_bln_ini + $item->proyek_rupiah_bln_ini;
			$look['konsolidasi_sd_bln_ini'][$item->group_coa][] = $item->divisi_rupiah_sd_bln_ini + $item->proyek_rupiah_sd_bln_ini;
			$lihat[$item->group_coa] 	= (object)array('nilai'=>$item->divisi_rupiah_bln_ini);
			
			//$output .= $this->load->view('report/labarugi/labarugi_table_data', $data, true);
			//$div_bul +=$item->divisi_rupiah_bln_ini + $item->proyek_rupiah_bln_ini;
		}
		//echo $div_bul;

		while(($item = $hasil_total->fetchObject())!== false)
		{
			$vTotal['divisi_sd'][$item->parent][] 	= $item->divisi_rupiah_sd_bln_ini;
			$vTotal['divisi_ini'][$item->parent][] 	= $item->divisi_rupiah_bln_ini;
			$vTotal['proyek_sd'][$item->parent][] 	= $item->proyek_rupiah_sd_bln_ini;
			$vTotal['proyek_ini'][$item->parent][]	= $item->proyek_rupiah_bln_ini;

		}
		//print_r($vTotal['divisi_sd']);
		$total=array();
		//$vSTotal=array();
		$i=0;
		$total_st = 0;
		//var_dump($urutan->fetchObject());exit;
		$A600 = $B400 = $C000 = $E000 = $G000 = $I600 = $J000 = 0;

		while(($item = $urutan->fetchObject()) !== false)
		{
			$data['nmperkiraan']=(trim($item->h_d)==='H')?'<b><h3>'.$item->description.'</h3></b>':'&nbsp;&nbsp;&nbsp;'.$item->description;

			//ADD DATA TO DISPLAY h_d == D.
			//if($item->group_name=='A600'){
				//echo $item->group_name.' >> '.$lihat[$item->group_name]->nilai."::<br>";



			$data['konsolidasi_bln_ini'] 	= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['konsolidasi_bln_ini'][$item->group_name]) ):'' ;
			$data['konsolidasi_sd_bln_ini'] = (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['konsolidasi_sd_bln_ini'][$item->group_name]) ):'' ;
			$data['divisi_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['divisi_bln_ini'][$item->group_name]) ):'' ;
			$data['divisi_sd_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['divisi_sd_bln_ini'][$item->group_name]) ):'' ;
			$data['proyek_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['proyek_bln_ini'][$item->group_name]) ):'' ;			
			$data['proyek_sd_bln_ini'] 		= (trim($item->h_d)==='D')?$this->format->number(array_sum( $look['proyek_sd_bln_ini'][$item->group_name]) ):'';
			
			//SETTING TOTAL 
		 	$vSTotal['divisi_sd'][$item->group_name][] 	= array_sum($vTotal['divisi_sd'][$item->parent]);
			$vSTotal['divisi_ini'][$item->group_name][] = array_sum($vTotal['divisi_ini'][$item->parent]);
			$vSTotal['proyek_sd'][$item->group_name][] 	= array_sum($vTotal['proyek_sd'][$item->parent]);
			$vSTotal['proyek_ini'][$item->group_name][] = array_sum($vTotal['proyek_ini'][$item->parent]);

			
			if( (trim($item->h_d)==='T') AND ($item->sum_type!='') ):
				$sum_item = explode(';',$item->sum_type);

				$sum_type1 = $item->flag_rumus;
				var_dump($sum_type1);var_dump($sum_item);
				foreach($sum_item as $ival)
                {
                	$vTotalGroup['divisi_sd'][$item->group_name][]	= array_sum($vSTotal['divisi_sd'][$ival]);
                	$vTotalGroup['divisi_ini'][$item->group_name][]	= array_sum($vSTotal['divisi_ini'][$ival]);
                	$vTotalGroup['proyek_sd'][$item->group_name][]	= array_sum($vSTotal['proyek_sd'][$ival]);
					$vTotalGroup['proyek_ini'][$item->group_name][]	= array_sum($vSTotal['proyek_ini'][$ival]);
					//var_dump($ival);
				}
				
				
				//ADD DATA TO TOTAL
				$vTotal['divisi_sd'][$item->parent][] 	= array_sum($vTotalGroup['divisi_sd'][$item->group_name]);
				$vTotal['divisi_ini'][$item->parent][] 	= array_sum($vTotalGroup['divisi_ini'][$item->group_name]);
				$vTotal['proyek_sd'][$item->parent][] 	= array_sum($vTotalGroup['proyek_sd'][$item->group_name]);
				$vTotal['proyek_ini'][$item->parent][]	= array_sum($vTotalGroup['proyek_ini'][$item->group_name]);
				
				//TOTAL DATA
				$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['proyek_ini'][$item->group_name])+array_sum($vTotalGroup['divisi_ini'][$item->group_name]));
				$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotalGroup['proyek_sd'][$item->group_name])+array_sum($vTotalGroup['divisi_sd'][$item->group_name]));
				$data['total_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotalGroup['divisi_ini'][$item->group_name]));
				$data['total_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['divisi_sd'][$item->group_name]));
				$data['total_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotalGroup['proyek_ini'][$item->group_name]));
				$data['total_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotalGroup['proyek_sd'][$item->group_name]));
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total', $data, true);	
							
			elseif(trim($item->h_d)==='T'):
					if($sum_item==='B400'){
						$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]))*-1;
					}else{
						$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
					}
					//$data['total_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
					$data['total_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent])+array_sum($vTotal['divisi_sd'][$item->parent]));
					$data['total_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotal['divisi_ini'][$item->parent]));
					$data['total_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['divisi_sd'][$item->parent]));
					$data['total_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
					$data['total_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent]));
				
				$output .= $this->load->view('report/labarugi/labarugi_table_data_total', $data, true);
				
			elseif(trim($item->h_d)==='ST'):
				if($sum_item==='B400'){
					$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]))*-1;
				}else{
					$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
				}
				//$data['subtotal_konsolidasi_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent])+array_sum($vTotal['divisi_ini'][$item->parent]));
				$data['subtotal_konsolidasi_sd_bln_ini'] 	= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent])+array_sum($vTotal['divisi_sd'][$item->parent]));
				$data['subtotal_divisi_bln_ini'] 			= $this->format->number(array_sum($vTotal['divisi_ini'][$item->parent]));
				$data['subtotal_divisi_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['divisi_sd'][$item->parent]));
				$data['subtotal_proyek_bln_ini'] 			= $this->format->number(array_sum($vTotal['proyek_ini'][$item->parent]));
				$data['subtotal_proyek_sd_bln_ini'] 		= $this->format->number(array_sum($vTotal['proyek_sd'][$item->parent]));
				$output .= $this->load->view('report/labarugi/labarugi_table_data_subtotal', $data, true);
	
			else:

			 	$output .= $this->load->view('report/labarugi/labarugi_table_data', $data, true);
			endif;
			
		}
		//echo $output; 

		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi.html";		
		$filename_excel = "LABARUGI_".strtoupper($kddivisi)."_{$year}_".ltrim($month,'0')."_divisi_for_excel.html";
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );
	}

	public function divisi($is_kompre='')
	{
		
		if(isset($is_kompre) && $is_kompre==='komprehensif'){
			$POST 		= $this->input->post();
			$periode = array(
					'year' 	=> $POST['year_'],
					'month' => $POST['month_']
				);
			$firstname = 'LABARUGI_KOMPREHENSIF_';
		}else{
			$POST 		= $this->input->get_post('data');
		
		    if (!isset($POST['title'])) $POST['title'] = 'Laporan';
		    if (!isset($POST['kdwilayah'])) $POST['kdwilayah'] = '';
				$periode = array(
					'year' 	=> $POST['periode']['year'],
					'month' => $POST['periode']['month']
				);

			$firstname = 'LABARUGI_';
		}
		
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		global $BASE_SIMDIV;
		$BASE_SIMDIV = $this->_BASE_SIMDIV;
		
		//echo 'SPK: '.$POST['kdspk'];
		if(isset($POST['kdspk']) || $POST['kdspk']==''){
			$pos_div  = 'V';
		}else{
			$pos_div  = 'V'; //$pos_div  = 'V_'.trim($POST['kdspk']); //strtoupper($POST['kdspk']);
		}


		$output = $this->load->view('report/loading_labarugi', $data, true);
		$filename 		= str_replace("'","",$firstname.$pos_div."_".$periode['year']."_".ltrim($periode['month'],'0')."_divisi.html");
		$filename_excel = str_replace("'","",$firstname.$pos_div."_".$periode['year']."_".ltrim($periode['month'],'0')."_divisi_for_excel.html");
		
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file AND is_numeric($pid_file))
		{
			exec("kill ".$pid_file);
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$command_attr = (object)array(
			'kddivisi' 			=> escapeshellarg( $pos_div ) ,
			'periode_year'		=> escapeshellarg( str_replace("'",'',$periode['year'] ) ),
			'periode_month'		=> escapeshellarg( str_replace("'",'',$periode['month'] ) ),
			'periode' 			=> escapeshellarg( str_replace("'",'',$periode['year'] .'-'. $periode['month'] ) ),
			'admin' 			=> escapeshellarg( $POST['admin_fullname'] ),
			'title' 			=> escapeshellarg( $POST['title'] ),
			'kdwilayah' 		=> escapeshellarg( $POST['kdwilayah'] )
		);
		
		if(isset($is_kompre) && $is_kompre==='komprehensif'){
			$step = '1';
			$command =  "php ".$this->_BASE_PATH." labarugi generate_report_kompre {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} 'LAPORAN RUGI LABA KOMPREHENSIF' {$command_attr->kdwilayah}" . '; > /dev/null 2>&1 & echo $\!; ';
			//echo '1 '.$command;
			return $this->generate_report_kompre( $command_attr->kddivisi, $command_attr->periode_year, $command_attr->periode_month, $command_attr->admin, $command_attr->title, $command_attr->kdwilayah, false);
		}else{
			$step = '2';
			$command =  "php ".$this->_BASE_PATH." labarugi generate_report {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} {$command_attr->title} {$command_attr->kdwilayah}" . '; > /dev/null 2>&1 & echo $\!; ';
			
			return $this->generate_report( $command_attr->kddivisi, $command_attr->periode_year, $command_attr->periode_month,  $command_attr->admin,  $command_attr->title,  $command_attr->kdwilayah,'0');
		}
    	
		$pid =  exec ( $command, $output, $error );
		
		//exit;
		//echo $pid;exit; //exec( $pid );
		//print ($command .' > /dev/null 2>&1 & echo $\!;');exit;
		if ($error){ 
	          exec('/usr/bin/top n 1 b 2>&1', $error );
	              echo "Error exec: ";
	                  exit($error[0]);
	    }
    	//die('DEBUG'); 
		write_file(APP_PATH.'tmp/'.$filename.'.pid', $pid, 'w');
		//print_r($command); print_r($output);
		redirect('labarugi/show_report/html/'. $filename);
			
	}
	
	public function divisi_proyek($is_kompre='')
	{
		
		$POST 		= $this->input->get_post('data');
		
		    if (!isset($POST['title'])) $POST['title'] = 'Laporan';
		    if (!isset($POST['kdspk'])) $POST['kdspk'] = '';
				$periode = array(
					'year' 	=> $POST['periode']['year'],
					'month' => $POST['periode']['month']
				);

			$firstname = 'LABARUGIPROYEK_';

		$NOBUKTI = $_POST['data']['nobukti'];
		$KDSPK   = $_POST['data']['kdspk'];
		// print_r($NOBUKTI);
		// print_r($KDSPK);
		// die;
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		global $BASE_SIMDIV;
		$BASE_SIMDIV = $this->_BASE_SIMDIV;
		
		//echo 'SPK: '.$POST['kdspk'];
		if(isset($POST['kdspk']) || $POST['kdspk']==''){
			$pos_div  = 'V';
		}else{
			$pos_div  = 'V'; //$pos_div  = 'V_'.trim($POST['kdspk']); //strtoupper($POST['kdspk']);
		}


		$output = $this->load->view('report/loading_labarugi', $data, true);
		$filename 		= str_replace("'","",$firstname.$pos_div."_".$KDSPK."_".$NOBUKTI."_".$periode['year']."_".ltrim($periode['month'],'0')."_proyek.html");
		$filename_excel = str_replace("'","",$firstname.$pos_div."_".$KDSPK."_".$NOBUKTI."_".$periode['year']."_".ltrim($periode['month'],'0')."_proyek_for_excel.html");
		
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file AND is_numeric($pid_file))
		{
			exec("kill ".$pid_file);
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$command_attr = (object)array(
			'kddivisi' 			=> escapeshellarg( $pos_div ) ,
			'periode_year'		=> escapeshellarg( str_replace("'",'',$periode['year'] ) ),
			'periode_month'		=> escapeshellarg( str_replace("'",'',$periode['month'] ) ),
			'periode' 			=> escapeshellarg( str_replace("'",'',$periode['year'] .'-'. $periode['month'] ) ),
			'admin' 			=> escapeshellarg( $POST['admin_fullname'] ),
			'title' 			=> escapeshellarg( $POST['title'] ),
			'nobukti' 			=> escapeshellarg( $NOBUKTI ),
			'kdspk' 			=> escapeshellarg( $KDSPK )
		);

		// print_r($command_attr->kdspk);
		// print_r($command_attr->nobukti);
		// die;
		
		// if(isset($is_kompre) && $is_kompre==='komprehensif'){
		// 	$step = '1';
		// 	$command =  "php ".$this->_BASE_PATH." labarugi generate_report_kompre {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} 'LAPORAN RUGI LABA PROYEK' {$command_attr->kdwilayah}" . '; > /dev/null 2>&1 & echo $\!; ';
		// 	//echo '1 '.$command;
		// 	return $this->generate_report_kompre( $command_attr->kddivisi, $command_attr->periode_year, $command_attr->periode_month, $command_attr->admin, $command_attr->title, $command_attr->kdwilayah, false);
		// }else{
			$step = '2';
			$command =  "php ".$this->_BASE_PATH." labarugi generate_report_proyek {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} {$command_attr->title} {$command_attr->nobukti} {$command_attr->kdspk}" . '; > /dev/null 2>&1 & echo $\!; ';
			
			return $this->generate_report_proyek( $command_attr->kddivisi, $command_attr->periode_year, $command_attr->periode_month,  $command_attr->admin,  $command_attr->title, $command_attr->nobukti, $command_attr->kdspk);
		//}
    	
		$pid =  exec ( $command, $output, $error );
		
		//exit;
		//echo $pid;exit; //exec( $pid );
		//print ($command .' > /dev/null 2>&1 & echo $\!;');exit;
		if ($error){ 
	          exec('/usr/bin/top n 1 b 2>&1', $error );
	              echo "Error exec: ";
	                  exit($error[0]);
	    }
    	//die('DEBUG'); 
		write_file(APP_PATH.'tmp/'.$filename.'.pid', $pid, 'w');
		//print_r($command); print_r($output);
		redirect('labarugi/show_report/html/'. $filename);
			
	}

	public function show_report($type='excel', $filename='')
	{

		if ( file_exists(APP_PATH.'files/'.$filename) )
		{
			if ($type === 'excel')
			{
				$this->output
					->set_header('Content-Type: application/vnd.ms-excel')
					->set_header('Content-Disposition: attachment; filename="Laporan_Labarugi.xls"');
			}
			$f_report_file 	= APP_PATH.'files/'.$filename;
			$fp_report_file = fopen($f_report_file, 'r');
			
			$report_file = fread($fp_report_file, filesize($f_report_file));
		}
		else 
			$report_file = 'Report Belum di Proses.';
			
		$this->output->set_output( $report_file );
	}

	public function cek_report()
    {
        $POST 			= $this->input->get_post('data');
		$div 			= strtoupper($POST['div']);
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
        $filename 		= APP_PATH."files/LABARUGI_".$div."_".$year."_".$month."_divisi.html";
       
        if(file_exists($filename))
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
        
        exit;
    }
    public function cek_report_lrp()
    {
        $POST 			= $this->input->get_post('data');
		$div 			= strtoupper($POST['div']);
		$kdspk 			= $POST['kdspk'];
		$nobukti 		= $POST['nobukti'];
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
        $filename 		= APP_PATH."files/LABARUGIPROYEK_".$div."_".$kdspk."_".$nobukti."_".$year."_".$month."_proyek.html";
       
        if(file_exists($filename))
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
        
        exit;
    }

	public function popup($div='',$year='',$month='')
	{
        //$filename = "/var/www/html/files/bius_".$type."_".strtoupper($div)."_".$year."_".$month.".html";

		include('../files/LABARUGI_'.strtoupper($div).'_'.$year.'_'.$month.'_divisi.html');
	}
	public function popup_lrp($div='',$kdspk='',$nobukti='',$year='',$month='')
	{
		include('../files/LABARUGIPROYEK_'.strtoupper($div).'_'.$kdspk.'_'.$nobukti.'_'.$year.'_'.$month.'_proyek.html');
	}
	
	public function popup_excel($div='',$year='',$month='')
	{
		$filename = '/var/www/html/simdiv/files/LABARUGI_'.strtoupper($div).'_'.$year.'_'.$month.'_divisi_for_excel.html'; 
		echo $filename;
		$fp = fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=LABARUGI_'.strtoupper($div).'_'.$year.'_'.$month.'.xls');
				
		fclose ($fp);
		
		echo $contents;
	}

	public function popup_excel_lrp($div='',$kdspk='',$nobukti='',$year='',$month='')
	{
		$filename = '/var/www/html/simdiv/files/LABARUGIPROYEK_'.strtoupper($div).'_'.$kdspk.'_'.$nobukti.'_'.$year.'_'.$month.'_proyek_for_excel.html'; 
		echo $filename;
		$fp = fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=LABARUGIPROYEK_'.strtoupper($div).'_'.$kdspk.'_'.$nobukti.'_'.$year.'_'.$month.'.xls');
				
		fclose ($fp);
		
		echo $contents;
	}
}
