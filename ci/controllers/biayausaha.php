<?php
class Biayausaha extends CI_Controller {
	private $_BASE_PATH;
	private $_BASE_SIMDIV;
	function __construct() {
		parent::__construct();
		// $this->output->enable_profiler(TRUE);
		$this->load->model('mdl_report_biayausaha');
		$this->load->library('format');
		$this->_BASE_PATH = $this->config->item('base_cli_path');
		// $this->_BASE_SIMDIV = "http://". gethostname() ."/e-accounting/";
		$this->_BASE_SIMDIV = $this->config->item('base_url_simdiv');
	}
	
	public function index ()
	{
		
	}

	public function rincian()
	{
		//echo "coba dong";exit;
		
		$POST = $this->input->get_post('data');
		//var_dump($POST);die();
		//print_r($POST);exit;
		//print_r($POST['admin_fullname']);
		$admin_fullname = $POST['admin_fullname'];
		$periode 		= $POST['periode']['year'] .'-'. $POST['periode']['month'];
		$div 			= strtoupper($POST['div']);
		$type 			= $POST['periode']['type'];
		$uker_ 			= $POST['periode']['uker'];
		$year 			= $POST['periode']['year'];
		$month  		= $POST['periode']['month'];
		$type2 			= $POST['periode']['type2'];
		$month1= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$month5 = date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		//echo $month5;
		$month2 = $year.'-01-01';
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		
		//if($type === 'rinci')
		//echo $type2;
		//exit;
		if(($type === 'rinci') and ($type2 == 'uker'))
		{
			$saldo_tahunlalu 	= $this->mdl_report_biayausaha->saldo_tahunlalu(strtolower($div),$month2,$month5,$uker_);
			$rincian_biayausaha	= $this->mdl_report_biayausaha->rincian_biayausaha(strtolower($div),$year,$month,$uker_);
			$i = 0;
			$j = 0;
			while ( ($item = $saldo_tahunlalu->fetchObject()) !== false ) 
			{
				$data['saldo']['kode'][]=$item->kduker;
				$data['rincian']['kodes'][$j]=$item->kduker;
				$data['rincian'][$item->kduker]['kdperkiraans'][$j]= $item->kdperkiraan;
				$data['saldo']['debet'][$item->kduker.'-'.$item->kdperkiraan]=$item->debet;
				$data['saldo']['kredit'][$item->kduker.'-'.$item->kdperkiraan]=$item->kredit;
				$j++;
			}
			while ( ($item = $rincian_biayausaha->fetchObject()) !== false ) 
			{
				$data['rincian']['kode'][$i]=$item->kduker;
				$data['rincian']['kodes'][$j]=$item->kduker;
				$data['rincian'][$item->kduker]['kdperkiraans'][$j]= $item->kdperkiraan;
				$data['rincian'][$item->kduker]['kdperkiraan'][$i]= $item->kdperkiraan;
				$data['rincian'][$item->kduker]['nmperkiraan'][$i]= $item->nmperkiraan;
				$data['rincian']['nmperkiraan'][$item->kdperkiraan]= $item->nmperkiraan;
				$data['rincian'][$item->kduker.'-'.$item->kdperkiraan][$i]['nobukti']=$item->nobukti;
				$data['rincian'][$item->kduker.'-'.$item->kdperkiraan][$i]['tanggal']=$item->tanggal;
				$data['rincian'][$item->kduker.'-'.$item->kdperkiraan][$i]['keterangan']=$item->keterangan;
				$data['rincian'][$item->kduker.'-'.$item->kdperkiraan][$i]['rupiah']=$item->rupiah;			
				$data['rincian']['novi'][] = $item->namasingkatan;
				$i++;
				$j++;
			}
				$data['month']   	= $this->rmonth($month);
		        $data['year']    	= $year;
				$data['divisi'] 	= $this->divisi($div);
				$data['timestamp'] 	= date('d M Y : H:i:s');
				$data['pembuat'] 	= $admin_fullname;
			//$this->load->view('report/biayausaha_rinci', $data);
			$output = $this->load->view('report/biayausaha_rinci', $data, true);
		
			$filename 		= "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month.".html";
			$filename_excel = "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month."_for_excel.html";
			write_file(APP_PATH.'files/'. $filename, $output);
			write_file(APP_PATH.'files/'. $filename_excel, $output);
			
			$this->output->set_output($output);
		}
		elseif(($type === 'rinci') and ($type2 == 'perkiraan'))
		{
			$data['div'] = $div;
			$data['month2'] = $month2;
			$data['month5'] = $month5;
			$data['uker_'] = $uker_;

			$bulan_ini = date("Y-m-d",mktime(0,0,0,$month,1,$year));
			$bulan_depan = date("Y-m-d",mktime(0,0,0,$month+1,1,$year));
			$awal_tahun = $year.'-01-01';

			$data['bulan_ini'] = $bulan_ini;
			$data['bulan_depan'] = $bulan_depan;
			$data['awal_tahun'] = $awal_tahun;


 			$data['datarows'] = $this->mdl_report_biayausaha->data_transaksi_coa($bulan_ini,$bulan_depan);

			$data['month']   	= $this->rmonth($month);
	        $data['year']    	= $year;
			$data['divisi'] 	= $this->divisi($div);
			$data['timestamp'] 	= date('d M Y : H:i:s');
			$data['pembuat'] 	= $admin_fullname;
			//$this->load->view('report/biayausaha_rinci', $data);
			//var_dump($data);exit;
			
			$output = $this->load->view('report/biayausaha_rinciperkiraan', $data, true);
		
			$filename 		= "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month.".html";
			$filename_excel = "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month."_for_excel.html";
			//write_file('./../files/'. $filename, $output);
			write_file(APP_PATH.'files/'. $filename, $output);
			write_file(APP_PATH.'files/'. $filename_excel, $output);
			
			$this->output->set_output($output);
		}
		elseif(($type === 'group') and ($type2 == 'uker')){
			$i = 0;
			$j=0;
			$ikhtisarnow = $this->mdl_report_biayausaha->ikhtisar_now(strtolower($div),$year,$month,$uker_);
			$ikhtisarsdnow = $this->mdl_report_biayausaha->ikhtisar_sdnow(strtolower($div),$month2,$month4,$uker_);
			$anggar = $this->mdl_report_biayausaha->anggaran(strtoupper($div),$year,$uker_);
			while(($item = $ikhtisarsdnow->fetchObject()) !== false)
			{
				$data['ikhtisar']['kode'][$i]=$item->kduker;
				$data['ikhtisar']['kodes'][$j]=$item->kduker;
				$data['ikhtisar'][$item->kduker]['kdperkiraans'][$j]= $item->kdperkiraan;
				$data['ikhtisar']['nmperkiraan'][$item->kdperkiraan]= $item->nmperkiraan;
				$data['ikhtisar'][$item->kduker]['kdperkiraan'][$i]=$item->kdperkiraan;
				$data['ikhtisar'][$item->kduker]['kodegroup'][$i]=substr($item->kdperkiraan,0,4);
				//$data['ikhtisar'][$item->kduker]['kdperkiraan'][substr($item->kdperkiraan,0,4)][$i]=$item->kdperkiraan;
				$data['ikhtisar'][$item->kduker]['nmperkiraan'][$i]=$item->nmperkiraan;
				$data['ikhtisar'][$item->kduker]['rupiah'][$item->kdperkiraan]=$item->debet;		
				$data['ikhtisar']['novi'][$item->kduker] = $item->namasingkatan;
				$i++;
				$j++;
			}
			$i=0;
			while(($item = $ikhtisarnow->fetchObject()) !== false)
			{
				$data['ikhtisar2']['kode'][$i]=$item->kduker;
				$data['ikhtisar']['kodes'][$j]=$item->kduker;
				$data['ikhtisar'][$item->kduker]['kdperkiraans'][$j]= $item->kdperkiraan;
				$data['ikhtisar2'][$item->kduker]['kdperkiraan']=$item->kdperkiraan;
				$data['ikhtisar2'][$item->kduker][$item->kdperkiraan]['rupiah']=$item->debet;	
				//$data['ikhtisar2'][$item->kduker][$item->kdperkiraan]['rab']=$item->rab;
				$i++;
				$j++;
			}
			$i=0;
			while(($item = $anggar->fetchObject()) !== false)
			{
				//die('aaaa');
				$data['ikhtisar']['kodes'][$j]=$item->kduker;
				$data['ikhtisar'][$item->kduker]['kdperkiraans'][$j]= $item->kdperkiraan;
				$data['ikhtisar2'][$item->kduker][$item->kdperkiraan]['rab']=$item->rupiah;
				
				$j++;
				$i++;
			}
			
				//print_r($data['ikhtisar']['kodes']);
				$data['month']   = $this->rmonth($month);
		        $data['year']    = $year;
				$data['divisi'] = $this->divisi($div);
				$data['timestamp'] = date('d M Y : H:i:s');
				$data['pembuat'] = $admin_fullname;
				//print_r($data['ikhtisar2']);
				//$this->load->view('report/biayausaha_ikhtisar', $data);
			$output = $this->load->view('report/biayausaha_ikhtisar', $data, true);
		
			$filename 		= "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month.".html";
			$filename_excel = "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month."_for_excel.html";
			write_file(APP_PATH.'files/'. $filename, $output);
			write_file(APP_PATH.'files/'. $filename_excel, $output);
			
			$this->output->set_output($output);
		}
		//elseif($type === 'groupperkiraan'){
	    elseif(($type === 'group') and ($type2 == 'perkiraan')){
	    	
			$data['div'] = $div;
			$data['month2'] = $month2;
			$data['month5'] = $month5;
			$data['uker_'] = $uker_;

			$bulan_ini = date("Y-m-d",mktime(0,0,0,$month,1,$year));
			$bulan_depan = date("Y-m-d",mktime(0,0,0,$month+1,1,$year));
			$awal_tahun = $year.'-01-01';

			$data['bulan_ini'] = $bulan_ini;
			$data['bulan_depan'] = $bulan_depan;
			$data['awal_tahun'] = $awal_tahun;

 			$data['datarows'] = $this->mdl_report_biayausaha->data_ikhtisar_coa3();

			$data['month']   	= $this->rmonth($month);
	        $data['year']    	= $year;
			$data['divisi'] 	= $this->divisi($div);
			$data['timestamp'] 	= date('d M Y : H:i:s');
			$data['pembuat'] 	= $admin_fullname;

			$output = $this->load->view('report/biayausaha_ikhtisarperkiraan', $data, true);
		
			$filename 		= "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month.".html";
			$filename_excel = "bius_".$type."_".$type2."_".$uker_."_".strtoupper($POST['div'])."_".$year."_".$month."_for_excel.html";
			write_file('./../files/'. $filename, $output);
			write_file('./../files/'. $filename_excel, $output);
			
			$this->output->set_output($output);
		}
	}
	
	
	public function neraca_t($kddiv='',$tanggal='')
	{
	
		$POST = $this->input->get_post('data');
		$periode 		= $POST['periode']['year'] .'-'. $POST['periode']['month'];
		$div 			= strtoupper($POST['div']);
		$year 			= $POST['periode']['year'] ;
		$month  		 = $POST['periode']['month'] ;       
		
		$jumlah = $this->mdl_report_neraca->get_count($div,$year,((strlen($month) == 1)?'0'.$month:$month));
		if($jumlah > 0)
		{
			$data['report_neraca'] = $this->mdl_report_neraca->get_saldo_neraca_t($div,$year,((strlen($month) == 1)?'0'.$month:$month));
	        $data['report_neraca1'] = $this->mdl_report_neraca->get_saldo_neraca_t($div,$year,((strlen($month) == 1)?'0'.$month:$month));
	        $data['month']   = $month;
	        $data['year']    = $year;
			$data['divisiname'] = $this->divisi($div);
			$data['rmonth'] = $this->rmonth($month);
			$data['timestamp'] = date('d M Y : h:i:s');
			$data['pembuat'] = 'Administrator';
			$this->load->view('report/report_neraca_t', $data);
		}
		else {		
			echo "<script>alert('Neraca Lajur Bulan ".$this->rmonth($month)." Tahun ".$year." Belum di Proses');</script>";
			$this->popFullScreen("http://e-accounting.wika.co.id/popup.php?mod=neraca_lajur_print&cmd=pop_wait&reptype=neraca_lajur&sryear=&ryear=".$year."&rmonth=".$month."&tbtype=rinci&semester=1&sub=divisi&uker=&kdspk=000000&is_proses=t&konsolidasi=&SDV=");
			echo '	<div align="center">
						<img src="../../../images/wait2.gif" ><br>
						<input type="button" name="reload" value="Lihat Laporan" onClick="location.reload()">
					</div>';
		}
		
	}


	private function popFullScreen($url)
	{
		echo '
			<script>
			function popFullScreen(Url){
			w=window.parent.screen.width;
			h=window.parent.screen.height;	
			jendela=window.open("","jendela","menubar=yes,resizable=yes,scrollbars=yes, width=" + (w-15) + ", height=" + (h-60));
			jendela.location.replace(Url);
			jendela.moveTo(0,0);
				if (parseInt(navigator.appVersion) >= 4) { jendela.window.focus(); }
							//window.jendela.moveTo(0,0);
				}
			popFullScreen("'.$url.'");
			</script>
			';
	}


	private function divisi($div='')
	{
		if($div == 'R')
		 return 'Dept. Sipil Umum';
		elseif($div == 'F')
		 return 'Dept. Bangunan dan Gedung';
		elseif($div == 'A')
		 return 'Kantor Pusat';
		elseif($div == 'L')
		 return 'Dept. Luar Negeri';
		elseif($div == 'O')
		 return 'Dept. Energi';
		elseif($div == 'T')
		 return 'Dept. Wilayah';
		elseif($div == 'U')
		 return 'Dept. Industrial Plant';
		elseif($div == 'Y')
		 return 'Dept. Departemen Baru';
		elseif($div == 'N')
		 return 'Dept. Biro Investasi';
	}


	private function rmonth($month='')
	{
		if($month == '01')
			return 'Januari';
		elseif($month == '02')
			return 'Februari';
		elseif($month == '03')
			return 'Maret';
		elseif($month == '04')
			return 'April';
		elseif($month == '05')
			return 'Mei';
		elseif($month == '06')
			return 'Juni';
		elseif($month == '07')
			return 'Juli';
		elseif($month == '08')
			return 'Agustus';
		elseif($month == '09')
			return 'September';
		elseif($month == '10')
			return 'Oktober';
		elseif($month == '11')
			return 'November';
		elseif($month == '12')
			return 'Desember';
	}	


	public function cek_report()
    {
        $POST = $this->input->get_post('data');
		$periode 		= $POST['periode']['year'] .'-'. $POST['periode']['month'];
		$div 			= strtoupper($POST['div']);
		$type 			= $POST['periode']['type'];
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
		$uker  			= $POST['periode']['uker'] ;
		$type2  		= $POST['periode']['type2'] ;
         	
        $filename = "/var/www/html/simdiv/files/bius_".$type."_".$type2."_".$uker."_".$div."_".$year."_".$month.".html";
       
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
	public function popup($div='',$year='',$month='',$type='',$type2='',$uker='')
	{
        //$filename = "/var/www/html/files/bius_".$type."_".strtoupper($div)."_".$year."_".$month.".html";
		include('../files/bius_'.$type.'_'.$type2."_".$uker."_".strtoupper($div).'_'.$year.'_'.$month.'.html');
	}
	public function popup_excel($div='',$year='',$month='',$type='',$type2='',$uker='')
	{
		$filename = '/var/www/html/simdiv/files/bius_'.$type.'_'.$type2.'_'.$uker.'_'.strtoupper($div).'_'.$year.'_'.$month.'_for_excel.html'; 
		echo $filename;
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=bius_'.$type.'_'.strtoupper($div).'_'.$year.'_'.$month.'.xls');
				
		fclose ($fp);
		
		echo $contents;
	}
}
