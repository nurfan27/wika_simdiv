<?php
class Neraca extends CI_Controller {
	
	private $_BASE_PATH;
	private $_BASE_SIMDIV;
	
	function __construct() {
		parent::__construct();
		// $this->output->enable_profiler(TRUE);
		$this->load->model('mdl_report_neraca');
		$this->load->library('format');
		$this->_BASE_PATH = $this->config->item('base_cli_path');
		$this->_BASE_SIMDIV = $this->config->item('base_url_simdiv');
	}
	
	public function index ()
	{
		
	}
	public function neraca_t($kddiv='',$tanggal='',$tahun='',$bulan='',$jenis='',$kdwilayah='')
	{

		
		//echo $kddiv.','.$tanggal.','.$tahun.','.$bulan.','.$jenis.','.$kdwilayah;die();
		
		$POST = $this->input->get_post('data');
		//var_dump($POST);die();
	    if (!isset($POST['title'])) $POST['title'] = 'Laporan';
	    if (!isset($POST['kdwilayah'])) $POST['kdwilayah'] = $kdwilayah;
	    if (!isset($POST['periode'])) $POST['periode'] = array(
	        'year' => $tahun,
	        'month' => $bulan,
	    );
	    	
	    if (!isset($POST['div'])) $POST['div'] = $kddiv;

			$periode 		= $POST['periode']['year'] .'-'. $POST['periode']['month'];
			$div 			= strtoupper($POST['div']);
			$year 			= $POST['periode']['year'] ;
			$month  		 = $POST['periode']['month'];       
			$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
	    	$kdwilayah = $POST['kdwilayah'];

			if($jenis==1)
			{
				echo "1";
				echo "<html>
				        ";
							$this->popFullScreen("{$this->_BASE_SIMDIV}popup.php?mod=neraca_lajur_print&cmd=pop_wait&reptype=neraca_lajur&sryear=&ryear=".$tahun."&rmonth=".$bulan."&tbtype=rinci&semester=1&sub=divisi&uker=&kdspk=000000&is_proses=t&konsolidasi=&tutuppop=yes3&SDV=&kdwilayah=$kdwilayah");
							echo '	<div align="center">
										<img src="'.$this->_BASE_SIMDIV.'images/wait2.gif" ><br>
										Sedang Mengambil Data....
										<!--<input type="button" name="reload" value="Lihat Laporan" onClick="location.reload()">-->
									</div>
				          </html>
				          ';
			
			}else{
				//echo "2";
				$jumlah = $this->mdl_report_neraca->get_count($div,$year,((strlen($month) == 1)?'0'.$month:$month),$kdwilayah);

				if($jumlah > 0)
				{
					//echo "2a";die();
					$data['report_neraca'] = $this->mdl_report_neraca->get_saldo_neraca_t($div,$year,((strlen($month) == 1)?'0'.$month:$month),$kdwilayah);
			        $data['report_neraca1'] = $this->mdl_report_neraca->get_saldo_neraca_t($div,$year,((strlen($month) == 1)?'0'.$month:$month),$kdwilayah);
			        
			        
			        // $val_32111 = $this->mdl_report_neraca->get_sql32111();
			        // $val_32212 = $this->mdl_report_neraca->get_sql32212();
			        // $val_4	= $this->mdl_report_neraca->get_4();
			        // $val_5	= $this->mdl_report_neraca->get_5();

			        // $data['result_32211'] = ($val_32111+$val_32212+$val_4+$val_5)* (-1);

			        $data['result_32211'] = $this->mdl_report_neraca->get_32211_now($year,((strlen($month) == 1)?'0'.$month:$month));
			        $data['result_32211_last'] = $this->mdl_report_neraca->get_32211_last($year);

			        $data['month']   = $month;
			        $data['year']    = $year;
					$data['divisiname'] = $this->divisi($div);
					$data['rmonth'] = $this->rmonth($month);
					$data['timestamp'] = date('d M Y : h:i:s');
					$data['pembuat'] = 'Administrator';
					if ($kdwilayah != '') $data['pembuat'] .= " , " . $kdwilayah;
					
					$output =$this->load->view('report/report_neraca_t', $data, true);
					
					$filename 		= "POSISIKEUANGAN_{$div}_{$year}_".$month."_divisi.html";		
					$filename_excel = "POSISIKEUANGAN_{$div}_{$year}_".$month."_divisi_for_excel.html";
					//support bila kdwilayah tertentu
			        if ($kdwilayah != '')
			        {
						$filename 		= "POSISIKEUANGAN_{$div}_{$year}_".$month."_".$kdwilayah."_divisi.html";		
			  			$filename_excel = "POSISIKEUANGAN_{$div}_{$year}_".$month."_".$kdwilayah."_divisi_for_excel.html";
			        }

			        //var_dump($data['report_neraca']);die();

					write_file('./files/'. $filename, $output, 'w');
					write_file('./files/'. $filename_excel, $output, 'w');			

					$this->output->set_output( $output );
				}else{		
					//echo "2b";
					echo "<script>alert('Neraca Lajur Bulan ".$this->rmonth($month)." Tahun ".$year." Belum di Proses ..');</script>";
					$this->popFullScreen("{$this->_BASE_SIMDIV}popup.php?mod=neraca_lajur_print&cmd=pop_wait&reptype=neraca_lajur&sryear=&ryear=".$year."&rmonth=".$month."&tbtype=rinci&semester=1&sub=divisi&uker=&kdspk=000000&is_proses=t&konsolidasi=&tutuppop=yes&SDV=&kdwilayah=$kdwilayah");
					echo '	<div align="center">
								<img src="../../images/wait2.gif" ><br>
								Sedang Mengambil Data....
								<!--<input type="button" name="reload" value="Lihat Laporan" onClick="location.reload()">-->
							</div>';
				}
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
		elseif($div == 'V')
		 return 'WikaGedung';
		elseif($div == 'P')
		 return 'WikaGedung Property';
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
	
	public function konsolidasi()
	{
		$POST 		= $this->input->get_post('data');
    	if (!isset($POST['title'])) $POST['title'] = 'Laporan';
		$periode = array(
			'year' 	=> $POST['periode']['year'],
			'month' => $POST['periode']['month']
		);	
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		$output = $this->load->view('report/loading_neraca', $data, true);
		$filename 		= "NERACA_LAJUR_{$periode['year']}_".ltrim($periode['month'],'0')."_divisi.html";
		$filename_excel = "NERACA_LAJUR_{$periode['year']}_".ltrim($periode['month'],'0')."_divisi_for_excel.html";
		
		write_file('./files/'. $filename, $output, 'w');
		write_file('./files/'. $filename_excel, $output, 'w');
		
		$pid_file = read_file('./tmp/'.$filename.'.pid');
		if ($pid_file AND is_numeric($pid_file))
		{
			exec("kill {$pid_file}");
			@unlink('./tmp/'.$filename.'.pid');
		}
		
    	//print_r($POST);
		$command_attr = (object)array(
			'kddivisi' 			=> escapeshellarg( strtolower($POST['div']) ),
			'periode_year'		=> escapeshellarg( $periode['year'] ),
			'periode_month'		=> escapeshellarg( $periode['month'] ),
			'periode' 			=> escapeshellarg( $periode['year'] .'-'. $periode['month'] ),
			'admin' 			=> escapeshellarg( $POST['admin_fullname'] ),
			'title' 			=> escapeshellarg( $POST['title'])
		);
		
		// $command =  "php /var/www/html/ci/index.php labarugi dobackground {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} {$command_attr->title}" . ' > /dev/null 2>&1 & echo $!; ';
		$command =  "php {$this->_BASE_PATH} neraca dobackground {$command_attr->kddivisi} {$command_attr->periode_year} {$command_attr->periode_month} {$command_attr->admin} {$command_attr->title} ; > /dev/null 2>&1 & echo $\!; "; // ' > /dev/null 2>&1 & echo $\!; ';
    	//die($command);
		$kddiv 		= $POST['div'];
		$tahun 		= $periode['year'];
		$bulan 		= $periode['month'];
		$periode 	= $periode['year'] .'-'. $periode['month'];
		$admin 		= $POST['admin_fullname'];
		$title 		= $POST['title'];

    	return $this->dobackground($kddiv, $tahun, $bulan, $admin, $title);
		$pid =  exec ( $command, $output );
		write_file('./tmp/'.$filename.'.pid', $pid, 'w');
		
		redirect('neraca/show_report/html/'. $filename);
	}	

	public function dobackground($kddivisi='',$year='',$month='',$admin='',$title='')
	{
		$data['bulan']=$month;
		$data['tahun']=$year;
		$data['admin']=$admin;
		$data['month']=$month;
		$month1= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;

		//echo '<pre>';
		$hasil		= $this->mdl_report_neraca->get_z_temp_old($year."-".$month."-01");
		//var_dump($hasil);
		$ddivisi 	= $this->mdl_report_neraca->getDdivisiByValidDate( $year.'-'.str_pad($month, '2', '0', STR_PAD_LEFT) );
		//var_dump($ddivisi); 
		$data['ddivisi'] = $ddivisi;

		//$item = $this->mdl_report_neraca->get_saldo_neraca_t_v2($kddivisi,$year,$month,'');
		//var_dump($item);
		while(($item = $hasil->fetchObject()) !== false)
		{
			$view['kdperkiraan'][]						= $item->group_name;
			$view['nmperkiraan'][$item->group_name][]	= $item->nmperkiraan;	
			$rupiah[$item->kddivisi][$item->group_name] = $item->rupiah;
			$tanggal[$item->kddivisi] 					= $item->tanggal_create;

			//if($item->group_name = '5201111' and $item->kddivisi = 'R')
			//die()
		}

		$kdperkiraan = array_unique($view['kdperkiraan']);
		
		//die($rupiah['R']['5201111']);
		
		//----setting view --------
		
		//----HEADER----
		$output = $this->load->view('report/neraca/neraca_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/neraca/neraca_table_header_v1', $data, true);
		
		//----TABLE DATA-----------
		if(is_array($kdperkiraan)):

		foreach($kdperkiraan as $perk)
		{
			if($perk == "32211")
				$tambah = " + Laba Rugi Bulan Ini";
			else
				$tambah = "";
			$nmperkiraan = array_unique($view['nmperkiraan'][$perk]);
			$data['kdperkiraan'] = $perk;
			$data['nmperkiraan'] = $nmperkiraan[0].$tambah;
			
			$data_total = 0;
			foreach ($ddivisi as $key=>$val)
			{
				$data[$key] = $this->format->number($rupiah[$key][$perk]);
				$data_total += (double)$rupiah[$key][$perk];
			}

			// $data['V'] = $this->format->number(@$rupiah['V'][$perk]);
			// $data['T'] = $this->format->number(@$rupiah['T'][$perk]);
			// $data['F'] = $this->format->number(@$rupiah['F'][$perk]);
			// $data['U'] = $this->format->number(@$rupiah['U'][$perk]);
			// $data['A'] = $this->format->number(@$rupiah['A'][$perk]);
			// $data['L'] = $this->format->number(@$rupiah['L'][$perk]);
			// $data['N'] = $this->format->number(@$rupiah['N'][$perk]);
			// $data['O'] = $this->format->number(@$rupiah['O'][$perk]);
			
			// $data['TOTAL'] = $this->format->number(@$rupiah['R'][$perk]+@$rupiah['T'][$perk]+
			// @$rupiah['F'][$perk]+@$rupiah['U'][$perk]+
			// @$rupiah['A'][$perk]+@$rupiah['L'][$perk]+
			// @$rupiah['N'][$perk]+@$rupiah['O'][$perk]);
			
			$data['TOTAL'] = $this->format->number($data_total);
			
			$output .= $this->load->view('report/neraca/neraca_table_data_v1', $data, true);
		}

			foreach ($ddivisi as $key=>$val)
			{
				$total[$key] = $this->format->number(array_sum($rupiah[$key]));
				$total['terakhir_proses'][$key] = $this->format->tgl2($tanggal[$key]);
			}
			
			// $total['R'] = $this->format->number(@array_sum($rupiah['R']));
			// $total['T'] = $this->format->number(@array_sum($rupiah['T']));
			// $total['F'] = $this->format->number(@array_sum($rupiah['F']));
			// $total['U'] = $this->format->number(@array_sum($rupiah['U']));
			// $total['A'] = $this->format->number(@array_sum($rupiah['A'])); 
			// $total['L'] = $this->format->number(@array_sum($rupiah['L']));
			// $total['N'] = $this->format->number(@array_sum($rupiah['N']));
			// $total['O'] = $this->format->number(@array_sum($rupiah['O']));
			
			// $total['tgl_pusat'] = $this->format->tgl2(@$tanggal['A']);
			// $total['tgl_dsu'] = $this->format->tgl2(@$tanggal['R']);
			// $total['tgl_dwl'] = $this->format->tgl2(@$tanggal['T']);
			// $total['tgl_dip'] = $this->format->tgl2(@$tanggal['U']);
			// $total['tgl_dkpw'] = $this->format->tgl2(@$tanggal['O']);
			// $total['tgl_dipw'] = $this->format->tgl2(@$tanggal['N']);
			// $total['tgl_dbg'] = $this->format->tgl2(@$tanggal['F']);
			// $total['tgl_dln'] = $this->format->tgl2(@$tanggal['L']);
			
			$output .= $this->load->view('report/neraca/neraca_table_total_v1', $total, true);

		endif;
		
		//----FOOTER TABLE--------		
		//$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		$output .= $this->load->view('report/neraca/neraca_table_footer', $data, true);
		//---- FOOTER-------------
		//$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);
		$output .= $this->load->view('report/neraca/neraca_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "NERACA_LAJUR_{$year}_".ltrim($month,'0')."_divisi.html";		
		$filename_excel = "NERACA_LAJUR_{$year}_".ltrim($month,'0')."_divisi_for_excel.html";
		write_file('./files/'. $filename, $output, 'w');
		write_file('./files/'. $filename_excel, $output, 'w');
		
		
		//------ DELETE PID FILE------
		$pid_file = read_file('./tmp/'.$filename.'.pid');
		if ($pid_file)
		{
			unlink('./tmp/'.$filename.'.pid');
		}
		
		$this->output->set_output( $output );

	}

	public function dobackground_old($kddivisi='',$year='',$month='',$admin='',$title)
	{
		$data['bulan']=$month;
		$data['tahun']=$year;
		$data['admin']=$admin;
		$data['month']=$month;
		$month1= date("Y-m-d",mktime(0,0,0,$month,1-1,$year));
		$month3 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year-1));
		$month4 = date("Y-m-d",mktime(0,0,0,$month+1,1-1,$year));
		$data['BASE_SIMDIV'] = $this->_BASE_SIMDIV;
		$hasil=$this->mdl_report_neraca->get_z_temp($year."-".$month."-01");
		while(($item = $hasil->fetchObject()) !== false)
		{
			$view['kdperkiraan'][]=$item->group_name;
			$view['nmperkiraan'][$item->group_name][]=$item->nmperkiraan;	
			$rupiah[$item->kddivisi][$item->group_name] = $item->rupiah;
			$tanggal[$item->kddivisi] = $item->tanggal_create;
			//if($item->group_name = '5201111' and $item->kddivisi = 'R')
			//die()
		}
		$kdperkiraan = @array_unique($view['kdperkiraan']);
		//die($rupiah['R']['5201111']);
		
		//----setting view --------
		
		//----HEADER----
		$output = $this->load->view('report/neraca/neraca_header', $data, true);
		
		//----HEADER TABLE--------
		$output .= $this->load->view('report/neraca/neraca_table_header', $data, true);
		
		//----TABLE DATA-----------
		if(is_array($kdperkiraan)):
		foreach($kdperkiraan as $perk)
		{
			if($perk == "32211")
			$tambah = " + Laba Rugi Bulan Ini";
			else
			$tambah = "";
			$nmperkiraan = array_unique($view['nmperkiraan'][$perk]);
			$data['kdperkiraan'] = $perk;
			$data['nmperkiraan'] = $nmperkiraan[0].$tambah;
			$data['R'] = $this->format->number(@$rupiah['R'][$perk]);
			$data['T'] = $this->format->number(@$rupiah['T'][$perk]);
			$data['F'] = $this->format->number(@$rupiah['F'][$perk]);
			$data['U'] = $this->format->number(@$rupiah['U'][$perk]);
			$data['A'] = $this->format->number(@$rupiah['A'][$perk]);
			$data['L'] = $this->format->number(@$rupiah['L'][$perk]);
			$data['N'] = $this->format->number(@$rupiah['N'][$perk]);
			$data['O'] = $this->format->number(@$rupiah['O'][$perk]);
			$data['TOTAL'] = $this->format->number(@$rupiah['R'][$perk]+@$rupiah['T'][$perk]+
			@$rupiah['F'][$perk]+@$rupiah['U'][$perk]+
			@$rupiah['A'][$perk]+@$rupiah['L'][$perk]+
			@$rupiah['N'][$perk]+@$rupiah['O'][$perk]);
			$output .= $this->load->view('report/neraca/neraca_table_data', $data, true);
		}
			$total['R'] = $this->format->number(@array_sum($rupiah['R']));
			$total['T'] = $this->format->number(@array_sum($rupiah['T']));
			$total['F'] = $this->format->number(@array_sum($rupiah['F']));
			$total['U'] = $this->format->number(@array_sum($rupiah['U']));
			$total['A'] = $this->format->number(@array_sum($rupiah['A']));
			$total['L'] = $this->format->number(@array_sum($rupiah['L']));
			$total['N'] = $this->format->number(@array_sum($rupiah['N']));
			$total['O'] = $this->format->number(@array_sum($rupiah['O']));
			$total['tgl_pusat'] = $this->format->tgl2(@$tanggal['A']);
			$total['tgl_dsu'] = $this->format->tgl2(@$tanggal['R']);
			$total['tgl_dwl'] = $this->format->tgl2(@$tanggal['T']);
			$total['tgl_dip'] = $this->format->tgl2(@$tanggal['U']);
			$total['tgl_dkpw'] = $this->format->tgl2(@$tanggal['O']);
			$total['tgl_dipw'] = $this->format->tgl2(@$tanggal['N']);
			$total['tgl_dbg'] = $this->format->tgl2(@$tanggal['F']);
			$total['tgl_dln'] = $this->format->tgl2(@$tanggal['L']);
			$output .= $this->load->view('report/neraca/neraca_table_total', $total, true);
		endif;
		
		//----FOOTER TABLE--------		
		$output .= $this->load->view('report/labarugi/labarugi_table_footer', $data, true);
		//---- FOOTER-------------
		$output .= $this->load->view('report/labarugi/labarugi_footer', $data, true);
		
		//----- CREATE FILE-------
		$filename 		= "NERACA_LAJUR_{$year}_".ltrim($month,'0')."_divisi.html";		
		$filename_excel = "NERACA_LAJUR_{$year}_".ltrim($month,'0')."_divisi_for_excel.html";
		write_file('../files/'. $filename, $output, 'w');
		write_file('../files/'. $filename_excel, $output, 'w');
		
		
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
		if ( file_exists('../files/'.$filename) )
		{
			if ($type === 'excel')
			{
				$this->output
					->set_header('Content-Type: application/vnd.ms-excel')
					->set_header('Content-Disposition: attachment; filename="Laporan_Labarugi.xls"');
			}
			$f_report_file 	= '../files/'.$filename;
			$fp_report_file = fopen($f_report_file, 'r');
			$report_file = fread($fp_report_file, filesize($f_report_file));
		}
		else 
			$report_file = 'Report Belum di Proses.';
			
		$this->output->set_output( $report_file );
	}
	public function cek_report()
    {
        $POST = $this->input->get_post('data');
		$div 			= strtoupper($POST['div']);
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
        $filename = "/homw/webmin/vhosts/simdiv.wikagedung.co.id/files/NERACA_LAJUR_".$year."_".$month."_divisi.html";
       
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
	public function cek_reportposisi()
    {
        $POST = $this->input->get_post('data');
		$div 			= strtoupper($POST['div']);
		$year 			= $POST['periode']['year'] ;
		$month  		= $POST['periode']['month'] ;
        $filename = "/var/www/html/simdiv/files/POSISIKEUANGAN_".$div."_".$year."_".$month."_divisi.html";
       
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
	public function popup($year='',$month='')
	{
        //$filename = "/var/www/html/files/bius_".$type."_".strtoupper($div)."_".$year."_".$month.".html";
		include('../files/NERACA_LAJUR_'.$year.'_'.$month.'_divisi.html');
	}
	public function popup_excel($year='',$month='')
	{
		$filename = '/var/www/html/simdiv/files/NERACA_LAJUR_'.$year.'_'.$month.'_divisi_for_excel.html'; 
		echo $filename;
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=NERACA_LAJUR_'.$year.'_'.$month.'.xls');

		fclose ($fp);
		
		echo $contents;
	}
	public function popupposisi($div='',$year='',$month='',$kdwilayah='')
	{
        //$filename = "/var/www/html/files/bius_".$type."_".strtoupper($div)."_".$year."_".$month.".html";
		$fname = '../files/POSISIKEUANGAN_'.strtoupper($div).'_'.$year.'_'.$month.'_divisi.html';
		if ($kdwilayah != '')
			$fname = '../files/POSISIKEUANGAN_'.strtoupper($div).'_'.$year.'_'.$month.'_'.$kdwilayah.'_divisi.html';
	}
	public function popup_excelposisi($div='',$year='',$month='')
	{
		$filename = '/var/www/html/simdiv/files/POSISIKEUANGAN_'.strtoupper($div).'_'.$year.'_'.$month.'_divisi_for_excel.html'; 
		echo $filename;
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=POSISIKEUANGAN_'.strtoupper($div).'_'.$year.'_'.$month.'.xls');
				
		fclose ($fp);
		
		echo $contents;
	}
	
}
