<?php
class Neraca_lajur extends CI_Controller {
	
	private $_BASE_PATH;
	private $_BASE_SIMDIV;
	
	function __construct() {
		parent::__construct();
		$this->load->model('mdl_eksternal');
		$this->load->model('mdl_dbeksternal');
		$this->load->model('mdl_guarantee_rating');
		$this->load->library(array('format', 'session', 'Encript'));
		$this->_BASE_PATH = $this->config->item('base_cli_path');
		$this->_BASE_SIMDIV = $this->config->item('base_url_simdiv');		
	}
	
	public function index()
	{
			$token 					= $this->input->get_post('token');
			$kddivisi 				= $this->input->get_post('kddivisi');
			$kdspk 					= $this->input->get_post('kdspk');
			
			
			//$this->view_session($token);
			
			$this->load->helper('form');
			
			$action = $this->input->get_post('action');
			
			$data_body['FRM_KDSPK'] 			= $this->mdl_guarantee_rating->frm_kdspk('kdspk', $kdspk);
			$data_body['token'] 				= $token;
			$data_body['kddivisi'] 				= $kddivisi;
			$data_body['kdspk'] 				= $kdspk;
			$data_body['BASE_SIMDIV'] 			= $this->_BASE_SIMDIV;
			$data_body['guarantee_rating'] 		= $this->mdl_guarantee_rating->get($kdspk);$data['BASE_SIMDIV'] 	= $this->_BASE_SIMDIV;
			$data['BODY'] 						= $this->load->view('form/guarantee_rating_list', $data_body, true);
			$data['kddivisi'] 					= $kddivisi;
			$data['token'] 						= urlencode($token);
			
			$this->load->view('form/guarantee_rating', $data);		
	}
	
	public function form()
	{				
		$token 		= $this->input->get_post('token');
		$kddivisi 	= $this->input->get_post('kddivisi');
		$kdspk 		= $this->input->get_post('kdspk');
		$jenis 		= $this->input->get_post('jenis');
		if($jenis == "")
		$jenis = "dobackground";
		$data['useradd'] 		= $this->input->get_post('useradd');
		if ($kdspk) {}
		else 
		{
			alert_set('msg', 'Pilih Kode SPK terlebih dahulu.', 'orange');
			//redirect("neraca_lajur/index/?kddivisi={$kddivisi}&token={$token}");
		}
		
		$this->view_session($token);
		
		$this->load->helper('form');
		
		$data['FRM_KDSPK'] 		= $this->mdl_guarantee_rating->frm_kdspk('kdspk', $kdspk);
		$data['token'] 			= $token;
		$data['kdspk'] 			= $kdspk;
		$data['BASE_SIMDIV'] 	= $this->_BASE_SIMDIV;	
		$data['kddivisi'] 		= $kddivisi;
		$data['jenis'] 			= $jenis;
		$data['token'] 			= urlencode($token);
		
		$this->load->view('form/neraca_lajur_form', $data);
	}
	
	public function cek_report()
    {
        $POST 			= $this->input->get_post('data');
		$div 			= strtoupper($POST['div']);
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
        $filename 		= APP_PATH."files/NERACA_LAJUR_".$div."_".$year."_".$month."_divisi.html";
       
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

	public function process()
	{
		
		$POST 			= $this->input->get_post('data');
    	if (!isset($POST['title'])) $POST['title'] = 'Laporan';
		$action 		= "dobackground";
		if($POST['jenis']=="dobackground2")
			$action = "dobackground2";
		else
			$action = "dobackground";
		$div 			= strtoupper($POST['div']);
		$periode 		= array(
							'year' 	=> $POST['periode']['year'],
							'month' => $POST['periode']['month']
						);	
		$data['BASE_SIMDIV'] 	= $this->_BASE_SIMDIV;
		$output 				= $this->load->view('report/loading_neraca', $data, true);
		$filename 				= "NERACA_LAJUR_{$div}_{$periode['year']}_".$periode['month']."_divisi.html";
		$filename_excel 		= "NERACA_LAJUR_{$div}_{$periode['year']}_".$periode['month']."_divisi_for_excel.html";
		
		//die($filename);
		write_file(APP_PATH.'files/'. $filename, $output, 'w');
		write_file(APP_PATH.'files/'. $filename_excel, $output, 'w');
		
		$pid_file = read_file(APP_PATH.'tmp/'.$filename.'.pid');
		if ($pid_file AND is_numeric($pid_file))
		{
			exec("kill {$pid_file}");
			unlink(APP_PATH.'tmp/'.$filename.'.pid');
		}
		
		$command_attr = (object)array(
			'kddivisi' 			=> escapeshellarg( strtolower($POST['div']) ),
			'periode_year'		=> escapeshellarg( $periode['year'] ),
			'periode_month'		=> escapeshellarg( $periode['month'] ),
			'periode' 			=> escapeshellarg( $periode['year'] .'-'. $periode['month'] ),
			'admin' 			=> escapeshellarg( $POST['admin_fullname'] ),
			'title' 			=> escapeshellarg( $POST['title'] )
		);
		
		$command =  "php {$this->_BASE_PATH} neraca_lajur {$action} {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin}" . ' > /dev/null 2>&1 & echo $\!';
		//die($command);
		$pid =  exec ( $command, $output );
		//print_r($pid);exit;
		write_file(APP_PATH.'tmp/'.$filename.'.pid', $pid, 'w');
		
		redirect('neraca_lajur/show_report/html/'. $filename);
	}


	public function dobackground($kddivisi='',$year='',$month='',$admin='')
	{
		$this->load->model('mdl_neraca_lajur');
		$data['bulan'] 			=$month;
		$data['tahun'] 			=$year;
		$data['admin'] 			=$admin;
		$data['month'] 			=$month;
		$month1 				= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 				= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 				= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$data['BASE_SIMDIV'] 	= $this->_BASE_SIMDIV;
		//$kdperkiraan 			= @array_unique($view['kdperkiraan']);
		$hasil 					= $this->mdl_neraca_lajur->get(strtolower($kddivisi),$year,$month);
		$labar_rugi_blnini 		= $this->mdl_neraca_lajur->laba_rugi_blnini(strtolower($kddivisi),$year,$month);
		
		//----setting view --------
		
		//----HEADER----
		$output = $this->load->view('report/neraca_lajur/neraca_lajur_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_header', $data, true);
		
		//----TABLE DATA-----------
		
		$saldo_d 	= 0;
		$saldo_k 	= 0;
		$mutasi_d 	= 0;
		$mutasi_k 	= 0;
		$perc_d 	= 0;
		$perc_k 	= 0;
		$lr_d 		= 0;
		$lr_k 		= 0;
		$ner_d 		= 0;
		$ner_k 		= 0;
		foreach($hasil->result() as $sa)
		{
			$saldo_d 					+=($sa->saldo_akhir > 0)?$sa->saldo_akhir:0;
			$saldo_k 					+=($sa->saldo_akhir > 0)?0:abs($sa->saldo_akhir);
			$saldo_debet 				= ($sa->saldo_akhir > 0)?$sa->saldo_akhir:0;
			$saldo_kredit 				= ($sa->saldo_akhir > 0)?0:abs($sa->saldo_akhir);
			$mutasi_debet 				= $sa->mutasi_debet;
			$mutasi_kredit 				= $sa->mutasi_kredit;
			$mutasi_d 					+=$sa->mutasi_debet;
			$mutasi_k 					+=$sa->mutasi_kredit;
			$percobaan_debet 			= $saldo_debet + $mutasi_debet;
			$percobaan_kredit 			= $saldo_kredit + $mutasi_kredit;
			$perc_d 					+=$percobaan_debet;
			$perc_k 					+=$percobaan_kredit;
			$data['kdperkiraan'] 		= $sa->kdperkiraan;
			$data['nmperkiraan'] 		= $sa->nmperkiraan;
			$data['neraca_awal_debet'] 	= $this->format->number($saldo_debet);
			$data['neraca_awal_kredit'] = $this->format->number($saldo_kredit);
			$data['mutasi_debet']		= $this->format->number($mutasi_debet);
			$data['mutasi_kredit']		= $this->format->number($mutasi_kredit);
			$data['percobaan_debet']	= $this->format->number($percobaan_debet);
			$data['percobaan_kredit']	= $this->format->number($percobaan_kredit);
			if (in_array(substr($sa->kdperkiraan,0,1), array(4,5,6,7,9))){
				$laba_rugi = $percobaan_debet - $percobaan_kredit;
				if ($laba_rugi < 0)
				{
					$lr_debet 	= 0;
					$lr_kredit 	= $laba_rugi;
					$lr_kredit 	= str_replace("-", "", $lr_kredit);
					$lr_d 		+=$lr_debet;
					$lr_k 		+=$lr_kredit;
				}
				else
				{
					$lr_debet = $laba_rugi;
					$lr_kredit = 0;
					$lr_d 		+=$lr_debet;
					$lr_k 		+=$lr_kredit;
				}
				$data['labarugi_debet']		= $this->format->number($lr_debet);
				$data['labarugi_kredit']	= $this->format->number($lr_kredit);	
				}
			else {
				$data['labarugi_debet']		= 0;
				$data['labarugi_kredit']	= 0;	
			}
			if (in_array(substr($sa->kdperkiraan,0,1), array(1,2,3))){
				$neraca = ($saldo_debet + $mutasi_debet) - ($saldo_kredit + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet 	= 0;
						$neraca 		= str_replace("-", "", $neraca);
						$neraca_kredit 	= $neraca;
						$ner_d 			+=$neraca_debet;
						$ner_k 			+=$neraca_kredit;
					}
					else
					{
						$neraca_debet 	= $neraca;
						$neraca_kredit 	= 0;
						$ner_d 			+=$neraca_debet;
						$ner_k 			+=$neraca_kredit;
					}
				$data['neraca_debet']		= $this->format->number($neraca_debet);
				$data['neraca_kredit']		= $this->format->number($neraca_kredit);	
				}
			else {
				$data['neraca_debet']		= 0;
				$data['neraca_kredit']		= 0;	
			}			
			
			$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_data', $data, true);
		}
		foreach($labar_rugi_blnini->result() as $lrblnini)
		{
			$data['kdperkiraan'] 		= "";
			$data['nmperkiraan'] 		= "LABA RUGI S/D BULAN INI";
			$data['neraca_awal_debet'] 	= 0;
			$data['neraca_awal_kredit'] = 0;
			$data['mutasi_debet']		= 0;
			$data['mutasi_kredit']		= 0;
			$data['percobaan_debet']	= 0;
			$data['percobaan_kredit']	= 0;
			$lr = $lrblnini->lr;
			if ($lr < 0)
			{
				$lr 			= str_replace("-", "", $lr);
				$lr_debet 		= $lr;
				$lr_kredit 		= 0;
				$neraca_debet 	= 0;
				$neraca_kredit 	= $lr;
				$ner_d 			+=$neraca_debet;
				$ner_k 			+=$neraca_kredit;
				$lr_d 			+=$lr_debet;
				$lr_k 			+=$lr_kredit;
			}
			else
			{
				$lr_debet 		= 0;
				$lr_kredit 		= $lr;
				$neraca_debet 	= $lr;
				$neraca_kredit 	= 0;
				$ner_d 			+=$neraca_debet;
				$ner_k 			+=$neraca_kredit;
				$lr_d 			+=$lr_debet;
				$lr_k 			+=$lr_kredit;
			}
			$data['labarugi_debet']		= $this->format->number($lr_debet);
			$data['labarugi_kredit']	= $this->format->number($lr_kredit);
			$data['neraca_debet']		= $this->format->number($neraca_debet);
			$data['neraca_kredit']		= $this->format->number($neraca_kredit);	
			$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_data', $data, true);
		}

		$data['saldo_d'] 	= $this->format->number($saldo_d);
		$data['saldo_k'] 	= $this->format->number($saldo_k);
		$data['mutasi_d'] 	= $this->format->number($mutasi_d);
		$data['mutasi_k'] 	= $this->format->number($mutasi_k);
		$data['perc_d'] 	= $this->format->number($perc_d);
		$data['perc_k'] 	= $this->format->number($perc_k);
		$data['lr_d'] 		= $this->format->number($lr_d);
		$data['lr_k'] 		= $this->format->number($lr_k);
		$data['ner_d'] 		= $this->format->number($ner_d);
		$data['ner_k'] 		= $this->format->number($ner_k);
		//----TOTAL TABLE---------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_total', $data, true);
		
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "NERACA_LAJUR_".strtoupper($kddivisi)."_".$year."_".$month."_divisi.html";		
		$filename_excel = "NERACA_LAJUR_".strtoupper($kddivisi)."_".$year."_".$month."_divisi_for_excel.html";
		write_file('./../files/'. $filename, $output, 'w');
		write_file('./../files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file('./tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			@unlink('./tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output(
			$output
		);
	}

	public function dobackground2($kddivisi='',$year='',$month='',$admin='')
	{
		$this->load->model('mdl_neraca_lajur');
		$data['bulan'] 			=$month;
		$data['tahun'] 			=$year;
		$data['admin'] 			=$admin;
		$data['month'] 			=$month;
		$month1 				= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 				= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 				= date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$data['BASE_SIMDIV'] 	= $this->_BASE_SIMDIV;
		//$kdperkiraan 			= @array_unique($view['kdperkiraan']);
		$hasil 					= $this->mdl_neraca_lajur->get2(strtolower($kddivisi),$year,$month);
		$labar_rugi_blnini 		= $this->mdl_neraca_lajur->laba_rugi_blnini(strtolower($kddivisi),$year,$month);
		
		//----setting view --------
		
		//----HEADER----
		$output = $this->load->view('report/neraca_lajur/neraca_lajur_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_header', $data, true);
		
		//----TABLE DATA-----------
		
		$saldo_d 	= 0;
		$saldo_k 	= 0;
		$mutasi_d 	= 0;
		$mutasi_k 	= 0;
		$perc_d 	= 0;
		$perc_k 	= 0;
		$lr_d 		= 0;
		$lr_k 		= 0;
		$ner_d 		= 0;
		$ner_k 		= 0;
		foreach($hasil->result() as $sa)
		{
			$saldo_d 					+=($sa->saldo_akhir > 0)?$sa->saldo_akhir:0;
			$saldo_k 					+=($sa->saldo_akhir > 0)?0:abs($sa->saldo_akhir);
			$saldo_debet 				= ($sa->saldo_akhir > 0)?$sa->saldo_akhir:0;
			$saldo_kredit 				= ($sa->saldo_akhir > 0)?0:abs($sa->saldo_akhir);
			$mutasi_debet 				= $sa->mutasi_debet;
			$mutasi_kredit 				= $sa->mutasi_kredit;
			$mutasi_d 					+=$sa->mutasi_debet;
			$mutasi_k 					+=$sa->mutasi_kredit;
			$percobaan_debet 			= $saldo_debet + $mutasi_debet;
			$percobaan_kredit 			= $saldo_kredit + $mutasi_kredit;
			$perc_d 					+=$percobaan_debet;
			$perc_k 					+=$percobaan_kredit;
			$data['kdperkiraan'] 		= $sa->kdperkiraan;
			$data['nmperkiraan'] 		= $sa->nmperkiraan;
			$data['neraca_awal_debet'] 	= $this->format->number($saldo_debet);
			$data['neraca_awal_kredit'] = $this->format->number($saldo_kredit);
			$data['mutasi_debet']		= $this->format->number($mutasi_debet);
			$data['mutasi_kredit']		= $this->format->number($mutasi_kredit);
			$data['percobaan_debet']	= $this->format->number($percobaan_debet);
			$data['percobaan_kredit']	= $this->format->number($percobaan_kredit);
			if (in_array(substr($sa->kdperkiraan,0,1), array(4,5,6,7,9))){
				$laba_rugi = $percobaan_debet - $percobaan_kredit;
				if ($laba_rugi < 0)
				{
					$lr_debet 	= 0;
					$lr_kredit 	= $laba_rugi;
					$lr_kredit 	= str_replace("-", "", $lr_kredit);
					$lr_d 		+=$lr_debet;
					$lr_k 		+=$lr_kredit;
				}
				else
				{
					$lr_debet = $laba_rugi;
					$lr_kredit = 0;
					$lr_d 		+=$lr_debet;
					$lr_k 		+=$lr_kredit;
				}
				$data['labarugi_debet']		= $this->format->number($lr_debet);
				$data['labarugi_kredit']	= $this->format->number($lr_kredit);	
				}
			else {
				$data['labarugi_debet']		= 0;
				$data['labarugi_kredit']	= 0;	
			}
			if (in_array(substr($sa->kdperkiraan,0,1), array(1,2,3))){
				$neraca = ($saldo_debet + $mutasi_debet) - ($saldo_kredit + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet 	= 0;
						$neraca 		= str_replace("-", "", $neraca);
						$neraca_kredit 	= $neraca;
						$ner_d 			+=$neraca_debet;
						$ner_k 			+=$neraca_kredit;
					}
					else
					{
						$neraca_debet 	= $neraca;
						$neraca_kredit 	= 0;
						$ner_d 			+=$neraca_debet;
						$ner_k 			+=$neraca_kredit;
					}
				$data['neraca_debet']		= $this->format->number($neraca_debet);
				$data['neraca_kredit']		= $this->format->number($neraca_kredit);	
				}
			else {
				$data['neraca_debet']		= 0;
				$data['neraca_kredit']		= 0;	
			}			
			
			$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_data', $data, true);
		}
		foreach($labar_rugi_blnini->result() as $lrblnini)
		{
			$data['kdperkiraan'] 		= "";
			$data['nmperkiraan'] 		= "LABA RUGI S/D BULAN INI";
			$data['neraca_awal_debet'] 	= 0;
			$data['neraca_awal_kredit'] = 0;
			$data['mutasi_debet']		= 0;
			$data['mutasi_kredit']		= 0;
			$data['percobaan_debet']	= 0;
			$data['percobaan_kredit']	= 0;
			$lr = $lrblnini->lr;
			if ($lr < 0)
			{
				$lr 			= str_replace("-", "", $lr);
				$lr_debet 		= $lr;
				$lr_kredit 		= 0;
				$neraca_debet 	= 0;
				$neraca_kredit 	= $lr;
				$ner_d 			+=$neraca_debet;
				$ner_k 			+=$neraca_kredit;
				$lr_d 			+=$lr_debet;
				$lr_k 			+=$lr_kredit;
			}
			else
			{
				$lr_debet 		= 0;
				$lr_kredit 		= $lr;
				$neraca_debet 	= $lr;
				$neraca_kredit 	= 0;
				$ner_d 			+=$neraca_debet;
				$ner_k 			+=$neraca_kredit;
				$lr_d 			+=$lr_debet;
				$lr_k 			+=$lr_kredit;
			}
			$data['labarugi_debet']		= $this->format->number($lr_debet);
			$data['labarugi_kredit']	= $this->format->number($lr_kredit);
			$data['neraca_debet']		= $this->format->number($neraca_debet);
			$data['neraca_kredit']		= $this->format->number($neraca_kredit);	
			$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_data', $data, true);
		}

		$data['saldo_d'] 	= $this->format->number($saldo_d);
		$data['saldo_k'] 	= $this->format->number($saldo_k);
		$data['mutasi_d'] 	= $this->format->number($mutasi_d);
		$data['mutasi_k'] 	= $this->format->number($mutasi_k);
		$data['perc_d'] 	= $this->format->number($perc_d);
		$data['perc_k'] 	= $this->format->number($perc_k);
		$data['lr_d'] 		= $this->format->number($lr_d);
		$data['lr_k'] 		= $this->format->number($lr_k);
		$data['ner_d'] 		= $this->format->number($ner_d);
		$data['ner_k'] 		= $this->format->number($ner_k);
		//----TOTAL TABLE---------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_total', $data, true);
		
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/neraca_lajur/neraca_lajur_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "NERACA_LAJUR_".strtoupper($kddivisi)."_".$year."_".$month."_divisi.html";		
		$filename_excel = "NERACA_LAJUR_".strtoupper($kddivisi)."_".$year."_".$month."_divisi_for_excel.html";
		write_file('./../files/'. $filename, $output, 'w');
		write_file('./../files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file('./tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			@unlink('./tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output(
			$output
		);
	}
	
	
	public function show_report($type='excel', $filename='')
	{
		if ( file_exists('./../files/'.$filename) )
		{
			if ($type === 'excel')
			{
				$this->output
					->set_header('Content-Type: application/vnd.ms-excel')
					->set_header('Content-Disposition: attachment; filename="Laporan_Labarugi.xls"');
			}
			$f_report_file 	= './../files/'.$filename;
			$fp_report_file = fopen($f_report_file, 'r');
			$report_file 	= fread($fp_report_file, filesize($f_report_file));
		}
		else 
			$report_file = 'Report Belum di Proses.';
			
		$this->output->set_output( $report_file );
	}
	
	
	public function popup($kddivisi='',$year='',$month='')
	{
		include('../files/NERACA_LAJUR_'.$kddivisi.'_'.$year.'_'.$month.'_divisi.html');
	}
	
	
	public function popup_excel($kddivisi='',$year='',$month='')
	{
		$filename = '/var/www/html/simdiv/files/NERACA_LAJUR_'.$kddivisi.'_'.$year.'_'.$month.'_divisi_for_excel.html'; 
		//echo $filename;
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=NERACA_LAJUR_'.$year.'_'.$month.'.xls');
				
		fclose ($fp);
		
		echo $contents;
	}
	
	private function view_session($id="")
	{
		$jumlah = $this->mdl_eksternal->get_session($id);
		if($jumlah == 0)
		die(":p sessionnya abis");
	}
	
}
