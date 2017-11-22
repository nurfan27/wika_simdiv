<?php
/**
 * Showing main App accounting reportiiii
 * @Yahya
 *
*/
loadClass('dateparse');
class neraca_lajur extends accounting_report	
{

  var $tpl = "";
  var $modname = "Neraca Lajur";
  var $pesan = "";


	function neraca_lajur(&$base,$submenu="")/*{{{*/
	{
		if (!defined("BASE_LOADED")) die("Accounting Report: base class not loaded");
		
		//run parent's constructor
		$this->modules();
		
		if (method_exists($this,"sub_$submenu"))
		{
		  $func = "sub_$submenu";
		  $this->$func($base);
		}
		else htmldie("Accounting Report: no submenu '$submenu' function");
		return $this;

	}/*}}}*/
	
	function sub_controller($base)/*{{{*/
	{
		$submenu = $this->get_var('cmd');
		if (method_exists($this,"sub_$submenu") && (!eregi('controller',$submenu)))
		{
		  $func = "sub_$submenu";
		  $this->$func($base);
		}
		else return $this->sub_mainpage($base);
	}/*}}}*/
	
	// penggantian filterisasi batasan tanggal
	function sub_mainpage($base)/*{{{*/
	{
		//$base->db->debug = true;
		if ($_SERVER['REMOTE_ADDR'] == '10.10.5.15'){
			// $base->db->debug = true;
		}
		$tpl = $base->_get_tpl('neraca_lajur.html');
    //default
    //print_r($this->S);
    $wil = '';
    $sql_wil = $this->S['userdata']->get_sql_wilayah($base, $this->S['curr_divisi']);
    if ($this->S['curr_wil'] != '')
    {
      $sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
    }
    $sql = "SELECT nmspk,kdspk FROM dspk where iswilayah='t' AND kddiv='{$this->S['curr_divisi']}' AND kodewilayah=kdspk $sql_wil ORDER BY kdspk";
    $rs = $base->dbquery($sql);
    $wil = $rs->getMenu2('kdwilayah',$this->S['curr_wil'],$this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']));
    //echo "IO:" . $this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']);


    $tpl->Assign('WILAYAH',$wil);
    
    if($this->get_var("sub")=="divisi")
		{
			
			$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
            
            $tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','');
				$tpl->assign('H4','');
                
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";

				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
      		'WILAYAH' => $wil,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur');
		}
		else if($this->get_var("sub")=="uker")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="spk" || $this->get_var("sub")=="rk")
		{			
			$tpl->assign('KDDIV1',$this->S['curr_divisi']);
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{				
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
            $tpl->assign('H1','');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','');
                
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="semester")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
			$ryear = dateparse::get_combo_option_year($y,date('Y')-10,date('Y'));
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="konstruksi")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
            
            $tpl->assign('H1','');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','');
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="direktorat_I")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="direktorat_II")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="divisi_t")
		{
			//die ($this->get_var("sub"));
			
			
			$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
			//die ($rep_type);
		}
		else if($this->get_var("sub")=="uker_t")
		{
			
			$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="spk_t")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
		else if($this->get_var("sub")=="direktorat_t")
		{
			
					$disp="";
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','');
				$tpl->assign('H3','');
				$tpl->assign('H4','none');					
			}				
			else if($this->get_var("show_tampil")=="yes")
			{					
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','none');
				$tpl->assign('H4','');
			}
			$dp = new dateparse;
			$m = $this->get_var('m',date('m'));
			$y = $this->get_var('y',date('Y'));
			
			if (!$m) $m=date("m");
			if (!$y) $y=date("Y");
			
			$start = $y."-".$m."-";
			$end = $y2."-".$m2."-".$d2;
		
			$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
			$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
				
			$tpl->Assign(array(
			'BLN'	=> $bln_,
			'TAHUN'	=> $tahun_,
				));
				
			$rep_type   = $this->get_var('rep_type','neraca_lajur')."_".$this->get_var("sub");
		}
        
        $tpl->assign('H1','');
		$tpl->assign('H2','none');
		$tpl->assign('H3','');
		$tpl->assign('H4','');
		
		
		loadClass('modules.accounting');
		$ynow = date('Y');

		// $kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),false,'','',"AND kddiv='{$this->S['curr_divisi']}'");
    	$sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base, $this->S['curr_divisi']);
    	//else $sql_wil = '';
		if ($this->S['curr_wil'] != '')
    		{
      			$sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
    		}
		$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}' $sql_wil");
		$semester = $this->get_var('semester');

		$tpl->Assign(array(
		  	'VSTGL'   	=> $this->get_var('stardate',date('1-m-Y')),
		  	'VETGL'   	=> $this->get_var('enddate',date('d-m-Y')),
		  	'SRYEAR'   	=> $ryear,
				'SRMONTH'  	=> $rmonth,
				'SPERIOD'  	=> $glperiods,
				'START_COA'	=> $start_coa,
				'END_COA' 	=> $end_coa,
				'KDSPK' 		=> $kdspk,
				'SEMESTER' 	=> $semester,
				'TITLE' 		=> ucwords(str_replace('_',' ',$rep_type)),
				'REP_TYPE' 	=> $rep_type,
				'SUB' 	    => $this->get_var('sub'),
				'DISP' 	    => $disp,
				'SID'      	=> MYSID,
                'KONSOLIDASI'   => $this->get_var('konsolidasi'),
		));
		
			//print $rep_type;
		$this->tpl =& $tpl;
	}/*}}}*/
  
  // Fungsi untuk menampilkan popup untuk tampilan menunggu (please wait)
  // Author       : rio@terakorp.com
  // Create date  : Sat Apr  5 19:10:41 WIT 2008
  function sub_pop_wait($base)/*{{{*/
  {
  		
  	
    	$tpl = $base->_get_tpl('pop_wait.html');
		$uker = $this->get_var('uker','');
		$kdspk = $this->get_var('kdspk','');
		$semester = $this->get_var('semester','');
		$tutuppop = $this->get_var('tutuppop','');
		$mod = 'neraca_lajur_print';
		$cmd = 'show_report';


			
      $tpl->Assign(array(
        'REPTYPE'     => $this->get_var('reptype'),
        'RYEAR'     	=> $this->get_var('ryear'),
        'SRYEAR'     	=> $this->get_var('sryear'),
        'RMONTH'    	=> $this->get_var('rmonth'),
        'KDPERKIRAAN' => $this->get_var('kdperkiraan'),
        'KDPERKIRAAN2'   => $this->get_var('kdperkiraan2'),
        'TBTYPE' 			=> $this->get_var('tbtype'),
		'MOD'					=> $mod,
		'CMD'					=> $cmd,
        'SID'       	=> MYSID,
		'UKER'				=>  $uker,
		'KDSPK'				=>  $kdspk,
		'SEMESTER'		=>  $semester,
		'TUTUPPOP'		=> $tutuppop,
        'KONSOLIDASI'   => $this->get_var('konsolidasi'),
        'VNOBUKTI' 		=> $this->get_var('nobukti', ''),
        'KDWILAYAH' => $this->get_var('kdwilayah'),
      ));
			
    $this->tpl =& $tpl;
		
  }/*}}}*/
  
//================================================================

	function _fill_static_report($base,$tpl)/*{{{*/
	{
		$tpl->Assign(array(
		  //'VCOMPANY'   => $comname,
			'PERIODE'  => $base->getLang('Month: '),
			'YEAR'  => $base->getLang('Year: '),
			'APERIODE'  => $base->getLang('Accounting Periode &nbsp;'),
			'SID'      => MYSID,
			'VCURR'      => '',
			'VOPERATOR' => $this->S['userdata']->real_name,
			'VTGLCETAK' => date('d M Y'),
			'VJAMCETAK' => date('H:i:s'),
			
		));
	}/*}}}*/

	function _fill_static_report_pdf($base,$month,$year,$accp)/*{{{*/
	{
    	$titles = array();
		$sql = "SELECT data FROM configs WHERE confname='company_name'";
		$comname = $base->db->getOne($sql);

		  $titles[] = array('titleText'=>$comname, 'align' => 'C');
			$titles[] = array('titleText'=>$base->getLang('Month: ') . $month . '  ' . $base->getLang('Year: ') . $year, 'align' => 'C');
			$titles[] = array('titleText'=>$base->getLang('Accounting Periode  ') . $accp, 'align' => 'C');
			$titles[]= array('titleText'=>'Generated on '.date('d M Y'), 'align' => 'C');
      return $titles;
	}/*}}}*/
	
	// dadan. 2008-03-24
	function cetak_to_file($base,$filename,$isi) /*{{{*/
	{
		$fp = fopen( $filename,"w+"); 
		if(fwrite( $fp, $isi, strlen($isi)))
		{
			fclose( $fp ); 
			return true;
		}
		else
		{
			fclose( $fp ); 
			return false;
		}
	} /*}}}*/	
	
	###---Memilih Jenis Neraca---###
	function sub_show_report($base) /*{{{*/
	{
		$rep = 'sub_report_' . $this->get_var('reptype');
		
		if (method_exists($this,$rep))
		{
		  return $this->$rep($base);
		}
		else
		{
		  return $base->get_message_page('Report not found');
		}
	} /*}}}*/
	
	function sub_show_report_2($base) /*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$sub = $this->get_var('sub');
		$uker = $this->get_var('uker');
		$kdspk = $this->get_var('kdspk');
		$kddiv = $this->S['curr_divisi'];
		$semester = $this->get_var('semester','');	
	    $kdwilayah = $this->get_var('kdwilayah','');
	    if ($kdwilayah != '') $fadd = "_" . $kdwilayah;
	    else $fadd = '';

        
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
		
		if ($sub == "divisi")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$thn_."_".$bln_.$fadd.".html";
		else if ($sub == "uker")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_uker_".$thn_."_".$bln_."_".$uker.".html";
		else if ($sub == "spk")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$thn_."_".$bln_."_".$kdspk.".html";
		else if ($sub == "semester")
		{
			if ($semester == "1")
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_1_".$thn_.".html";
			else
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_2_".$thn_.".html";
		}
					
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");
		$contents = fread ($fp, filesize ($filename));
		fclose ($fp);
		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);
		$this->tpl = $tpl;
	} /*}}}*/
	
	function sub_show_report_2_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$sub = $this->get_var('sub');
	    $uker = $this->get_var('uker');
	    $kdspk = $this->get_var('kdspk');
		$kddiv = $this->S['curr_divisi'];
		$semester = $this->get_var('semester','');
        
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
	
		if ($sub == "divisi")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$thn_."_".$bln_."_for_excel.html";
		else if ($sub == "uker")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_uker_".$thn_."_".$bln_."_".$uker."_for_excel.html";
		else if ($sub == "spk")
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$thn_."_".$bln_."_".$kdspk."_for_excel.html";
		else if ($sub == "semester")
		{
			if ($semester == "1")
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_1_".$thn_."_for_excel.html";
			else
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_2_".$thn_."_for_excel.html";
		}
			
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");
			
		
		$contents = fread ($fp, filesize ($filename));
		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename.'.xls');
				
		fclose ($fp);

		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);
		
		$this->tpl = $tpl;
	}/*}}}*/
	
	###---Proses Neraca Lajur---###
	###---Yahya 16/04/2008---###
	
	function sub_report_neraca_lajur_uker($base) /*{{{*/
	{		
		$base->db->debug=true;
		$this->get_valid_app('SDV');
		$uker = $this->get_var('uker','');	
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$nm_wilayah=$base->dbGetOne("SELECT namawilayah FROM dwilayah WHERE kdwilayah ='{$uker}'");
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_uker_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ====== FOR EXCEL
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_uker_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		//die($batasan_tgl_sal_akhir);
    	//START : Mencari akumulasi mutasi
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			FROM $table jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			AND SUBSTR(nobukti,1,2)='".$uker."'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		$rs2=$base->dbquery($sql);
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'KDWIL'		=> $uker,
				'NMWIL'		=> $nm_wilayah,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'KDWIL'		=> $uker,
				'NMWIL'		=> $nm_wilayah,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi);		
			//die($sql."yahya");
		}
		//die ($sql1);		
		$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			(SELECT jur.kdperkiraan,d.nmperkiraan,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			FROM $table jur,dperkir d
			WHERE jur.kdperkiraan=d.kdperkiraan AND
						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						AND isdel = 'f' -- AND isapp='t'
						AND SUBSTR(nobukti,1,2)='$uker'
			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk
			)
			UNION ALL (SELECT '32211' as kdperkiraan
      , 'LABA TAHUN BERJALAN---' as nmperkiraan
      , 0 as rupiah_debet
      , 0 as rupiah_kredit )) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		//die($sql1);
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'KDWIL'		=> $uker,
				'NMWIL'		=> $nm_wilayah,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
			// ===== FOR EXCEL
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'KDWIL'		=> $uker,
				'NMWIL'		=> $nm_wilayah,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp='';
			// ====== 
		
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');
			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				//die($counter);
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;			
					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 AND isdel = 'f' -- AND isapp='t'
												 AND SUBSTR(nobukti,1,2)='".$uker."'
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$neraca_awal = $base->db->getOne($sql3);
						
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = str_replace("-", "", $nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					else
					{
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = str_replace("-", "", $nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet = 0;
						$neraca = str_replace("-", "", $neraca);
						$neraca_kredit = $neraca;
					}
					else
					{
						$neraca_debet = $neraca;
						$neraca_kredit = 0;
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				//echo $curr_coa."<br>";
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$lr_debet = 0;
					$lr_kredit = 0;
				}
				
				
					
				$tot_nerawal_d += $nerawal_d;
				$tot_nerawal_k += $nerawal_k;
				$tot_mutasi_debet += $mutasi_debet;
				$tot_mutasi_kredit += $mutasi_kredit;
				$tot_neraca_debet += $neraca_debet;
				$tot_neraca_kredit += $neraca_kredit;
				$tot_nercoba_debet += $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet += $lr_debet;
				$tot_lr_kredit += $lr_kredit;
				
				
				// =====
				$tpl->assignDynamic('row', array(
						'VCOA'  	=> $rs1->fields['kdperkiraan'],
						'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
					'VNERAWAL_K'  	=> $this->format_money2($base,$nerawal_k),
					'VMUTASI_D'  	=> $this->format_money2($base,$mutasi_debet),
					'VMUTASI_K'  	=> $this->format_money2($base,$mutasi_kredit),
					'VNERACA_D'  	=> $this->format_money2($base,$neraca_debet),
					'VNERACA_K'  	=> $this->format_money2($base,$neraca_kredit),
					'VNERCOBA_D'  	=> $this->format_money2($base,$nercoba_debet),
					'VNERCOBA_K'  	=> $this->format_money2($base,$nercoba_kredit),
					'VRUGLAB_D'  	=> $this->format_money2($base,$lr_debet),
					'VRUGLAB_K'  	=> $this->format_money2($base,$lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp = $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
				$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 AND SUBSTR(nobukti,1,2)='".$uker."'
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						//die($sql3);
						$nama_lr = "Laba Rugi Bulan Ini";
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
						'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
						'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $neraca_debet,
						'VNERACA_K'  	=> $neraca_kredit,
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $lr_debet,
						'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
					
				$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'		=> $divname,
					'KDWIL'		=> $uker,
					'NMWIL'		=> $nm_wilayah,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $tot_nerawal_d,
						'VTOT_NERAWAL_K'  => $tot_nerawal_k,
						'VTOT_MUTASI_D'  => $tot_mutasi_debet,
						'VTOT_MUTASI_K'  => $tot_mutasi_kredit,
						'VTOT_NERACA_D'  => $tot_neraca_debet,
						'VTOT_NERACA_K'  => $tot_neraca_kredit,
						'VTOT_NERCOBA_D'  => $tot_nercoba_debet,
						'VTOT_NERCOBA_K'  => $tot_nercoba_kredit,
						'VTOT_RUGLAB_D'  => $tot_lr_debet,
						'VTOT_RUGLAB_K'  => $tot_lr_kredit,
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname,
						'KDWIL'		=> $uker,
						'NMWIL'		=> $nm_wilayah,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
		}	
			
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);
				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// ====== FOR EXCEL
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();
		
		// ======= FOR EXCEL
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		// =======
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_uker_".$ryear."_".$rmonth."_".$uker.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			// ==== FOR EXCEL
						$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_uker_".$ryear."_".$rmonth."_".$uker."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
	}/*}}}*/
	
	function sub_report_neraca_lajur_semester($base) /*{{{*/
	{		
		//$base->db->debug=true;
		$this->get_valid_app('SDV');
		$semester = $this->get_var('semester','');	
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$nmspk=$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk ='{$kdspk}'");
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');
		//die($semester);
		if ($semester == "1")
		{
			//////SEMESTER I
			$tpl = $base->_get_tpl('report_neraca_lajur_semester1_printable.html');
			$tpl_temp = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl);
			
			
			// ====== FOR EXCEL
			$tpl_excel = $base->_get_tpl('report_neraca_lajur_semester1_printable.html');
			$tpl_temp_excel = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl_excel);
		}
		else
		{
			$tpl = $base->_get_tpl('report_neraca_lajur_semester2_printable.html');
			$tpl_temp = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl);
			
			
			// ====== FOR EXCEL
				
			$tpl_excel = $base->_get_tpl('report_neraca_lajur_semester2_printable.html');
			$tpl_temp_excel = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl_excel);
		}
		
		$ryear = $this->get_var('sryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));

		$thn_ = $this->get_var('sryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
			$startdate = $this->get_var('startdate',date('d-m-Y'));
			$enddate = $this->get_var('enddate',date('d-m-Y'));

			if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
					$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
			else
					$sdate = date('Y-m-d');

			if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
					$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
			else
					$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		
		if ($semester == "1")
		{
			//START : Mencari akumulasi mutasi saldo awal semester 1 tahun dipilih
		
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			FROM $table jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) < '".$batasan_tahun_new."' 
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				$tpl->Assign(array(
					'VTHN'   => $ryear,
					'VBLN'  => $rmonth,
					'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' => $startdate,
					'EDATE' => $enddate,
					'SID'      => MYSID,
					'VCURR'      => '',
				));
					
					
				// ===== FOR EXCEL
				$tpl_excel->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
					'VCURR'   => '',
				));
				
				// ===== begin while $rs2
				$array_mutasi = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
					$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_mutasi);		
				//die($sql);
			}
			
			////Cari Mutasi Januari
			$sql = " SELECT
						jur.kdperkiraan,
							(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS januari_debet,
							(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS januari_kredit
						FROM $table jur
						WHERE true
						AND jur.tanggal >= '$batasan_tahun_new-01-01' 
						AND jur.tanggal < '$batasan_tahun_new-02-01'
						AND isdel = 'f' -- AND isapp='t' 
						GROUP BY jur.kdperkiraan,jur.dk
						ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_januari = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_januari[$rs2->fields['kdperkiraan']][]=$rs2->fields['januari_debet'];				
					$array_januari[$rs2->fields['kdperkiraan']][]=$rs2->fields['januari_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_januari);		
				//die($sql);
			}
			
			////Cari Mutasi Februari
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS februari_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS februari_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-02-01' 
			AND jur.tanggal < '$batasan_tahun_new-03-01'
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_februari = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_februari[$rs2->fields['kdperkiraan']][]=$rs2->fields['februari_debet'];				
					$array_februari[$rs2->fields['kdperkiraan']][]=$rs2->fields['februari_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Maret
			$sql = " SELECT
					jur.kdperkiraan,
						(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS maret_debet,
						(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS maret_kredit
					FROM $table jur
					WHERE true
					AND jur.tanggal >= '$batasan_tahun_new-03-01' 
					AND jur.tanggal < '$batasan_tahun_new-04-01'
					AND isdel = 'f'  -- AND isapp='t'
					GROUP BY jur.kdperkiraan,jur.dk
					ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_maret = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_maret[$rs2->fields['kdperkiraan']][]=$rs2->fields['maret_debet'];				
					$array_maret[$rs2->fields['kdperkiraan']][]=$rs2->fields['maret_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi April
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS april_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS april_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-04-01' 
			AND jur.tanggal < '$batasan_tahun_new-05-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_april = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_april[$rs2->fields['kdperkiraan']][]=$rs2->fields['april_debet'];				
					$array_april[$rs2->fields['kdperkiraan']][]=$rs2->fields['april_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Mei
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mei_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mei_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-05-01' 
			AND jur.tanggal < '$batasan_tahun_new-06-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_mei = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_mei[$rs2->fields['kdperkiraan']][]=$rs2->fields['mei_debet'];				
					$array_mei[$rs2->fields['kdperkiraan']][]=$rs2->fields['mei_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Juni
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS juni_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS juni_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-06-01' 
			AND jur.tanggal < '$batasan_tahun_new-07-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_juni = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_juni[$rs2->fields['kdperkiraan']][]=$rs2->fields['juni_debet'];				
					$array_juni[$rs2->fields['kdperkiraan']][]=$rs2->fields['juni_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
				FROM
				(
				(SELECT jur.kdperkiraan,d.nmperkiraan,
							 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
							 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
				FROM $table jur,dperkir d
				WHERE jur.kdperkiraan=d.kdperkiraan AND
							DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
							AND isdel = 'f' -- AND isapp='t'
				GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
				ORDER BY jur.kdperkiraan,jur.dk
				)
				UNION ALL (SELECT '32211' as kdperkiraan
				, 'LABA TAHUN BERJALAN' as nmperkiraan
				, 0 as rupiah_debet
				, 0 as rupiah_kredit )) t
				GROUP BY t.kdperkiraan,t.nmperkiraan
				ORDER BY t.kdperkiraan,t.nmperkiraan";
		//			die($sql1);
			
			$rs1=$base->dbquery($sql1);
			if ($rs1->EOF)
				{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
				{
				$tpl->Assign(array(
					'VTHN'   => $ryear,
					'VBLN'  => $rmonth,
					'DIVNAME'		=> $divname,
					'SEMESTER'		=> $semester, 
					'SDATE' => $startdate,
					'EDATE' => $enddate,
					'SID'      => MYSID,
					'VCURR'      => '',
				));
					
					
				// ===== FOR EXCEL
				
				$tpl_excel->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname,
					'SEMESTER'		=> $semester, 
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
					'VCURR'   => '',
				));
				
				// =====
				$tot_saldo_awal_d = 0;
				$tot_saldo_awal_k = 0;
				$tot_januari_d = 0;
				$tot_januari_k = 0;
				$tot_februari_d = 0;
				$tot_februari_k = 0;
				$tot_maret_d = 0;
				$tot_maret_k = 0;
				$tot_april_d =0;
				$tot_april_k =0;
				$tot_mei_d = 0;
				$tot_mei_k = 0;
				$tot_juni_d = 0;
				$tot_juni_k = 0;
				$tot_jumlah_d = 0;
				$tot_jumlah_k = 0;
				$tot_labarugi_d = 0;
				$tot_labarugi_k = 0;
				$tot_neraca_d = 0;
				$tot_neraca_k = 0;
				
				$kdperkiraan_tmp='';
				// ========TPL
				$tpl->defineDynamicBlock('row');
						
				// ===== FOR EXCEL
				$tpl_excel->defineDynamicBlock('row');
	
	
				while(!$rs1->EOF)
				{
					$curr_coa = $rs1->fields['kdperkiraan'];
					$saldo_akhir = $rs1->fields['saldo_akhir'];
					if ($kdperkiraan_tmp == $curr_coa)
						$rs1->moveNext();
					
					$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$mutasi_debet = 0;
						$mutasi_kredit = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
						$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
						$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_januari[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$januari_d = 0;
						$januari_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$januari_d = $array_januari[$rs1->fields['kdperkiraan']][0];
						$januari_k = $array_januari[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$januari_d = $array_januari[$rs1->fields['kdperkiraan']][0];
						$januari_k = $array_januari[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_februari[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$februari_d = 0;
						$februari_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$februari_d = $array_februari[$rs1->fields['kdperkiraan']][0];
						$februari_k = $array_februari[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$februari_d = $array_februari[$rs1->fields['kdperkiraan']][0];
						$februari_k = $array_februari[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_maret[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$maret_d = 0;
						$maret_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$maret_d = $array_maret[$rs1->fields['kdperkiraan']][0];
						$maret_k = $array_maret[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$maret_d = $array_maret[$rs1->fields['kdperkiraan']][0];
						$maret_k = $array_maret[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_april[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$april_d = 0;
						$april_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$april_d = $array_april[$rs1->fields['kdperkiraan']][0];
						$april_k = $array_april[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$april_d = $array_april[$rs1->fields['kdperkiraan']][0];
						$april_k = $array_april[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_mei[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$mei_d = 0;
						$mei_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$mei_d = $array_mei[$rs1->fields['kdperkiraan']][0];
						$mei_k = $array_mei[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$mei_d = $array_mei[$rs1->fields['kdperkiraan']][0];
						$mei_k = $array_mei[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_juni[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$juni_d = 0;
						$juni_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$juni_d = $array_juni[$rs1->fields['kdperkiraan']][0];
						$juni_k = $array_juni[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$juni_d = $array_juni[$rs1->fields['kdperkiraan']][0];
						$juni_k = $array_juni[$rs1->fields['kdperkiraan']][1];
					}
					
						
					$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
					if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
					{
						$saldo_awal = $mutasi_debet - $mutasi_kredit;			
						//Mencari laba bulan berjalan
						//yahya 22-04-2008
						if ($curr_coa == "32211")
						{
							$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
							FROM
							(
								SELECT
											(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
											(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
											,1 AS grp
										 FROM $table pjur
										 WHERE true
										 			AND 
													(
														DATE_PART('YEAR',pjur.tanggal) < '".$batasan_tahun_new."' 
													)
												  AND isdel = 'f' -- AND isapp='t'
												  AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
										 GROUP BY pjur.dk
							)t
							GROUP BY grp";
							//die($sql3);
							$saldo_awal = $base->db->getOne($sql3);
							
							if ($saldo_awal < 0)
							{
								$saldo_awal_d = 0;
								$saldo_awal_k = $saldo_awal;
								$saldo_awal_k = str_replace("-", "", $saldo_awal_k);
							}
							else
							{
								$saldo_awal_d = $saldo_awal;
								$saldo_awal_k = 0;
							}
						}
						else
						{
							if ($saldo_awal < 0)
							{
								$saldo_awal_d = 0;
								$saldo_awal_k = $saldo_awal;
								$saldo_awal_k = str_replace("-", "", $saldo_awal_k);
							}
							else
							{
								$saldo_awal_d = $saldo_awal;
								$saldo_awal_k = 0;
							}
						}
					}
					else
					{
						$saldo_awal_d = 0;
						$saldo_awal_k = 0;
					}				
					$jumlah_d = $saldo_awal_d+$januari_d+$februari_d+$maret_d+$april_d+$mei_d+$juni_d;
					$jumlah_k = $saldo_awal_k+$januari_k+$februari_k+$maret_k+$april_k+$mei_k+$juni_k;
					
					$tot_saldo_awal_d += $saldo_awal_d;
					$tot_saldo_awal_k += $saldo_awal_k;
					$tot_januari_d += $januari_d;
					$tot_januari_k += $januari_k;
					$tot_februari_d += $februari_d;
					$tot_februari_k += $februari_k;
					$tot_maret_d += $maret_d;
					$tot_maret_k += $maret_k;
					$tot_april_d += $april_d;
					$tot_april_k += $april_k;
					$tot_mei_d += $mei_d;
					$tot_mei_k += $mei_k;
					$tot_juni_d += $juni_d;
					$tot_juni_k += $juni_k;
					$tot_jumlah_d += $jumlah_d;
					$tot_jumlah_k += $jumlah_k;
					
					
					$tpl->assignDynamic('row', array(
							'VCOA'  	=> $rs1->fields['kdperkiraan'],
							'VNAMA'  	=> $rs1->fields['nmperkiraan'],
							'VD'  		=> 'D',
							'VK'  		=> 'K',
							'VSALDOAWAL_D' 	=> $this->format_money2($base, $saldo_awal_d),
							'VSALDOAWAL_K'  	=> $this->format_money2($base, $saldo_awal_k),
							'VJANUARI_D'  	=> $this->format_money2($base, $januari_d),
							'VJANUARI_K'  	=> $this->format_money2($base, $januari_k),
							'VFEBRUARI_D' 	=> $this->format_money2($base, $februari_d),
							'VFEBRUARI_K' 	=> $this->format_money2($base, $februari_k),
							'VMARET_D'  	=> $this->format_money2($base, $maret_d),
							'VMARET_K'  	=> $this->format_money2($base, $maret_k),
							'VAPRIL_D'  	=> $this->format_money2($base, $april_d),
							'VAPRIL_K'  	=> $this->format_money2($base, $april_k),
							'VMEI_D'  	=> $this->format_money2($base, $mei_d),
							'VMEI_K'  	=> $this->format_money2($base, $mei_k),
							'VJUNI_D'  	=> $this->format_money2($base, $juni_d),
							'VJUNI_K'  	=> $this->format_money2($base, $juni_k),
							'VJUMLAH_D'  	=> $this->format_money2($base, $jumlah_d),
							'VJUMLAH_K'  	=> $this->format_money2($base, $jumlah_k),
					 ));
					$tpl->parseConcatDynamic('row');
				
				
					// ==== FOR EXCEL
					
					$tpl_excel->assignDynamic('row', array(
						'VCOA'  			=> $rs1->fields['kdperkiraan'],
						'VNAMA'  			=> $rs1->fields['nmperkiraan'],
						'VD'  		=> 'D',
						'VK'  		=> 'K',
						'VSALDOAWAL_D' 	=> $saldo_awal_d,
						'VSALDOAWAL_K'  	=> $saldo_awal_k,
						'VJANUARI_D'  	=> $januari_d,
						'VJANUARI_K'  	=> $januari_k,
						'VFEBRUARI_D' 	=> $februari_d,
						'VFEBRUARI_K' 	=> $februari_k,
						'VMARET_D'  	=> $maret_d,
						'VMARET_K'  	=> $maret_k,
						'VAPRIL_D'  	=> $april_d,
						'VAPRIL_K'  	=> $april_k,
						'VMEI_D'  	=> $mei_d,
						'VMEI_K'  	=> $mei_k,
						'VJUNI_D'  	=> $juni_d,
						'VJUNI_K'  	=> $juni_k,
						'VJUMLAH_D'  	=> $jumlah_d,
						'VJUMLAH_K'  	=> $jumlah_k,
					));
					$tpl_excel->parseConcatDynamic('row');
					
					$kdperkiraan_tmp == $curr_coa;
						
					$rs1->moveNext();
									
				} // end of while	
				
					
					$realbalend = '';
					
				
				// =====
				
					$tpl->Assign(array(
						'VD'  => 'D',
						'VK'  => 'K',
						'VTOT_SALDOAWAL_D'  => $this->format_money2($base, $tot_saldo_awal_d),
						'VTOT_SALDOAWAL_K'  => $this->format_money2($base, $tot_saldo_awal_k),
						'VTOT_JANUARI_D'  => $this->format_money2($base, $tot_januari_k),
						'VTOT_JANUARI_K'  => $this->format_money2($base, $tot_januari_k),
						'VTOT_FEBRUARI_D'  => $this->format_money2($base, $tot_februari_d),
						'VTOT_FEBRUARI_K'  => $this->format_money2($base, $tot_februari_k),
						'VTOT_MARET_D'  => $this->format_money2($base, $tot_maret_d),
						'VTOT_MARET_K'  => $this->format_money2($base, $tot_maret_k),
						'VTOT_APRIL_D'  => $this->format_money2($base, $tot_april_d),
						'VTOT_APRIL_K'  => $this->format_money2($base, $tot_april_k),
						'VTOT_MEI_D'  => $this->format_money2($base, $tot_mei_d),
						'VTOT_MEI_K'  => $this->format_money2($base, $tot_mei_k),
						'VTOT_JUNI_D'  => $this->format_money2($base, $tot_juni_d),
						'VTOT_JUNI_K'  => $this->format_money2($base, $tot_juni_k),
						'VTOT_JUMLAH_D'  => $this->format_money2($base, $tot_jumlah_d),
						'VTOT_JUMLAH_K'  => $this->format_money2($base, $tot_jumlah_k),
							));				
					$this->_fill_static_report($base,&$tpl);
					
					$tpl->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
						'SEMESTER'		=> $semester, 
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
	
						// ===== FOR EXCEL
						
						$tpl_excel->Assign(array(
							'VD'  => 'D',
							'VK'  => 'K',
							'VTOT_SALDOAWAL_D'  => $tot_saldo_awal_d,
							'VTOT_SALDOAWAL_K'  => $tot_saldo_awal_k,
							'VTOT_JANUARI_D'  => $tot_januari_k,
							'VTOT_JANUARI_K'  => $tot_januari_k,
							'VTOT_FEBRUARI_D'  => $tot_februari_d,
							'VTOT_FEBRUARI_K'  => $tot_februari_k,
							'VTOT_MARET_D'  => $tot_maret_d,
							'VTOT_MARET_K'  => $tot_maret_k,
							'VTOT_APRIL_D'  => $tot_april_d,
							'VTOT_APRIL_K'  => $tot_april_k,
							'VTOT_MEI_D'  => $tot_mei_d,
							'VTOT_MEI_K'  => $tot_mei_k,
							'VTOT_JUNI_D'  => $tot_juni_d,
							'VTOT_JUNI_K'  => $tot_juni_k,
							'VTOT_JUMLAH_D'  => $tot_jumlah_d,
							'VTOT_JUMLAH_K'  => $tot_jumlah_k,
							));				
							
						$this->_fill_static_report($base,&$tpl_excel);
						
						$tpl_excel->Assign(array(
							'VTHN'   	=> $ryear,
							'VBLN'  	=> $rmonth,
							'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
							'SEMESTER'		=> $semester, 
							'SDATE' 	=> $startdate,
							'EDATE' 	=> $enddate,
							'SID'     => MYSID,
								));
							
					
					
			}	
				
			$dp = new dateparse();
			$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);
	
					
			$tpl->Assign(array(
				'PERIODE'  => $startdate.' s.d '.$enddate,
				'YEAR'  => $ryear,
				'VTHN'  => $ryear,
				'VBLN'  => $nm_bulan_,
				'VAP'  => '',
			));
			
			
			// ====== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'PERIODE'  => $startdate.' s.d '.$enddate,
				'YEAR'  => $ryear,
				'VTHN'  => $ryear,
				'VBLN'  => $nm_bulan_,
				'VAP'  => '',
			));
			
			// =======
			$tpl_temp->assign('ONE',$tpl,'template');
			$tpl_temp->parseConcat();
			
			$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
			$tpl_temp_excel->parseConcat();
			
	
			$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
			$tpl_temp->Assign('ONE', '
				<div id="pr" name="pr" align="left">
				<br>
				<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
				<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
				<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
				<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
									<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
				-->
				</div>'
			);
			$tpl_temp->parseConcat();
	
			
			// ======= FOR EXCEL
			
			$tpl_temp_excel->Assign('ONE', '
				<div id="pr" name="pr" align="left">
				<br>
				<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
				<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
				<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
				<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
									<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
				-->
				</div>'
			);
			
			$tpl_temp_excel->parseConcat();
			
			
			// =======
					
			
			$is_proses = $this->get_var('is_proses');
			$divname = str_replace(" ","_",$divname);
			if($is_proses=='t')
			{
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_1_".$ryear.".html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);	
				$this->tpl =& $tpl_temp;			
				
				// ==== FOR EXCEL
						
							$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_1_".$ryear."_for_excel.html";
							$isi2 = & $tpl_temp_excel->parsedPage();
							$this->cetak_to_file($base,$filename_excel,$isi2);
				// ====
			}
			else
			{
				$this->tpl_excel =& $tpl_temp_excel;
				$this->tpl =& $tpl_temp;			
			}
		}
		else
		{
			/////SEMESTER II
			//START : Mencari akumulasi mutasi saldo awal semester 1 tahun dipilih
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			FROM $table jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) < '".$batasan_tahun_new."' 
			)
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				$tpl->Assign(array(
					'VTHN'   => $ryear,
					'VBLN'  => $rmonth,
					'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' => $startdate,
					'EDATE' => $enddate,
					'SID'      => MYSID,
					'VCURR'      => '',
				));
					
					
				// ===== FOR EXCEL
				
				$tpl_excel->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
					'VCURR'   => '',
				));
				
				// ===== begin while $rs2
				$array_mutasi = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
					$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_mutasi);		
				//die($sql);
			}
			
			////Cari Mutasi Januari
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS januari_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS januari_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-01-01' 
			AND jur.tanggal < '$batasan_tahun_new-02-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_januari = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_januari[$rs2->fields['kdperkiraan']][]=$rs2->fields['januari_debet'];				
					$array_januari[$rs2->fields['kdperkiraan']][]=$rs2->fields['januari_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_januari);		
				//die($sql);
			}
			
			////Cari Mutasi Februari
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS februari_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS februari_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-02-01' 
			AND jur.tanggal < '$batasan_tahun_new-03-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_februari = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_februari[$rs2->fields['kdperkiraan']][]=$rs2->fields['februari_debet'];				
					$array_februari[$rs2->fields['kdperkiraan']][]=$rs2->fields['februari_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Maret
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS maret_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS maret_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-03-01' 
			AND jur.tanggal < '$batasan_tahun_new-04-01'
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_maret = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_maret[$rs2->fields['kdperkiraan']][]=$rs2->fields['maret_debet'];				
					$array_maret[$rs2->fields['kdperkiraan']][]=$rs2->fields['maret_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi April
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS april_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS april_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-04-01' 
			AND jur.tanggal < '$batasan_tahun_new-05-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_april = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_april[$rs2->fields['kdperkiraan']][]=$rs2->fields['april_debet'];				
					$array_april[$rs2->fields['kdperkiraan']][]=$rs2->fields['april_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Mei
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mei_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mei_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-05-01' 
			AND jur.tanggal < '$batasan_tahun_new-06-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_mei = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_mei[$rs2->fields['kdperkiraan']][]=$rs2->fields['mei_debet'];				
					$array_mei[$rs2->fields['kdperkiraan']][]=$rs2->fields['mei_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Juni
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS juni_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS juni_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-06-01' 
			AND jur.tanggal < '$batasan_tahun_new-07-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_juni = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_juni[$rs2->fields['kdperkiraan']][]=$rs2->fields['juni_debet'];				
					$array_juni[$rs2->fields['kdperkiraan']][]=$rs2->fields['juni_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Juli
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS juli_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS juli_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-07-01' 
			AND jur.tanggal < '$batasan_tahun_new-08-01'
			AND isdel = 'f' -- AND isapp='t' 
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_juli = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_juli[$rs2->fields['kdperkiraan']][]=$rs2->fields['juli_debet'];				
					$array_juli[$rs2->fields['kdperkiraan']][]=$rs2->fields['juli_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Agustus
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS agustus_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS agustus_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-08-01' 
			AND jur.tanggal < '$batasan_tahun_new-09-01'
			AND isdel = 'f' -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_agustus = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_agustus[$rs2->fields['kdperkiraan']][]=$rs2->fields['agustus_debet'];				
					$array_agustus[$rs2->fields['kdperkiraan']][]=$rs2->fields['agustus_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi September
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS september_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS september_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-09-01' 
			AND jur.tanggal < '$batasan_tahun_new-10-01'
			AND isdel = 'f' -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_september = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_september[$rs2->fields['kdperkiraan']][]=$rs2->fields['september_debet'];				
					$array_september[$rs2->fields['kdperkiraan']][]=$rs2->fields['september_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Oktober
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS oktober_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS oktober_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-10-01' 
			AND jur.tanggal < '$batasan_tahun_new-11-01'
			AND isdel = 'f' -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_oktober = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_oktober[$rs2->fields['kdperkiraan']][]=$rs2->fields['oktober_debet'];				
					$array_oktober[$rs2->fields['kdperkiraan']][]=$rs2->fields['oktober_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Nopember
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS nopember_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS nopember_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-11-01' 
			AND jur.tanggal < '$batasan_tahun_new-12-01'
			AND isdel = 'f' --  AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_nopember = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_nopember[$rs2->fields['kdperkiraan']][]=$rs2->fields['nopember_debet'];				
					$array_nopember[$rs2->fields['kdperkiraan']][]=$rs2->fields['nopember_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			////Cari Mutasi Desember
			$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS desember_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS desember_kredit
			FROM $table jur
			WHERE true
			AND jur.tanggal >= '$batasan_tahun_new-12-01' 
			AND jur.tanggal <= '$batasan_tahun_new-12-31'
			AND isdel = 'f' -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
			//die($sql);
			$rs2=$base->dbquery($sql);
			if ($rs2->EOF)
			{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
			{
				
				// ===== begin while $rs2
				$array_desember = array();
				while(!$rs2->EOF)
				{
					//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
					$array_desember[$rs2->fields['kdperkiraan']][]=$rs2->fields['desember_debet'];				
					$array_desember[$rs2->fields['kdperkiraan']][]=$rs2->fields['desember_kredit'];
					
					$rs2->moveNext();
					
				} // end of while	
				//print_r($array_februari);		
				//die($sql);
			}
			
			
			$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
				FROM
				(
				(SELECT jur.kdperkiraan,d.nmperkiraan,
							 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
							 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
				FROM $table jur,dperkir d
				WHERE jur.kdperkiraan=d.kdperkiraan AND
							DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
							AND isdel = 'f' -- AND isapp='t'
				GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
				ORDER BY jur.kdperkiraan,jur.dk
				)
				UNION ALL (SELECT '32211' as kdperkiraan
				, 'LABA TAHUN BERJALAN' as nmperkiraan
				, 0 as rupiah_debet
				, 0 as rupiah_kredit )) t
				GROUP BY t.kdperkiraan,t.nmperkiraan
				ORDER BY t.kdperkiraan,t.nmperkiraan";
		//			die($sql1);
			
			$rs1=$base->dbquery($sql1);
			if ($rs1->EOF)
				{
				$tpl->Assign('row','');
				
				// ==== FOR EXCEL
				$tpl_excel->Assign('row','');		
				// ====
			}
			else
				{
				$tpl->Assign(array(
					'VTHN'   => $ryear,
					'VBLN'  => $rmonth,
					'DIVNAME'		=> $divname,
					'SEMESTER'		=> $semester, 
					'SDATE' => $startdate,
					'EDATE' => $enddate,
					'SID'      => MYSID,
					'VCURR'      => '',
				));
					
					
				// ===== FOR EXCEL
				
				$tpl_excel->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname,
					'SEMESTER'		=> $semester, 
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
					'VCURR'   => '',
				));
				
				// =====
				$tot_saldo_awal_d = 0;
				$tot_saldo_awal_k = 0;
				$tot_januari_d = 0;
				$tot_januari_k = 0;
				$tot_februari_d = 0;
				$tot_februari_k = 0;
				$tot_maret_d = 0;
				$tot_maret_k = 0;
				$tot_april_d =0;
				$tot_april_k =0;
				$tot_mei_d = 0;
				$tot_mei_k = 0;
				$tot_juni_d = 0;
				$tot_juni_k = 0;
				$tot_juli_d = 0;
				$tot_juli_k = 0;
				$tot_agustus_d = 0;
				$tot_agustus_k = 0;
				$tot_september_d = 0;
				$tot_september_k = 0;
				$tot_oktober_d = 0;
				$tot_oktober_k = 0;
				$tot_nopember_d = 0;
				$tot_nopember_k = 0;
				$tot_desember_d = 0;
				$tot_desember_k = 0;
				
				$tot_jumlah_d = 0;
				$tot_jumlah_k = 0;
				$tot_labarugi_d = 0;
				$tot_labarugi_k = 0;
				$tot_neraca_d = 0;
				$tot_neraca_k = 0;
				
				$kdperkiraan_tmp='';
				// ========TPL
				$tpl->defineDynamicBlock('row');
						
				// ===== FOR EXCEL
				$tpl_excel->defineDynamicBlock('row');
	
	
				while(!$rs1->EOF)
				{
					$curr_coa = $rs1->fields['kdperkiraan'];
					$saldo_akhir = $rs1->fields['saldo_akhir'];
					if ($kdperkiraan_tmp == $curr_coa)
						$rs1->moveNext();
					
					$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$mutasi_debet = 0;
						$mutasi_kredit = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
						$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
						$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_januari[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$januari_d = 0;
						$januari_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$januari_d = $array_januari[$rs1->fields['kdperkiraan']][0];
						$januari_k = $array_januari[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$januari_d = $array_januari[$rs1->fields['kdperkiraan']][0];
						$januari_k = $array_januari[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_februari[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$februari_d = 0;
						$februari_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$februari_d = $array_februari[$rs1->fields['kdperkiraan']][0];
						$februari_k = $array_februari[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$februari_d = $array_februari[$rs1->fields['kdperkiraan']][0];
						$februari_k = $array_februari[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_maret[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$maret_d = 0;
						$maret_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$maret_d = $array_maret[$rs1->fields['kdperkiraan']][0];
						$maret_k = $array_maret[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$maret_d = $array_maret[$rs1->fields['kdperkiraan']][0];
						$maret_k = $array_maret[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_april[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$april_d = 0;
						$april_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$april_d = $array_april[$rs1->fields['kdperkiraan']][0];
						$april_k = $array_april[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$april_d = $array_april[$rs1->fields['kdperkiraan']][0];
						$april_k = $array_april[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_mei[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$mei_d = 0;
						$mei_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$mei_d = $array_mei[$rs1->fields['kdperkiraan']][0];
						$mei_k = $array_mei[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$mei_d = $array_mei[$rs1->fields['kdperkiraan']][0];
						$mei_k = $array_mei[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_juni[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$juni_d = 0;
						$juni_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$juni_d = $array_juni[$rs1->fields['kdperkiraan']][0];
						$juni_k = $array_juni[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$juni_d = $array_juni[$rs1->fields['kdperkiraan']][0];
						$juni_k = $array_juni[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_juli[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$juli_d = 0;
						$juli_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$juli_d = $array_juli[$rs1->fields['kdperkiraan']][0];
						$juli_k = $array_juli[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$juli_d = $array_juli[$rs1->fields['kdperkiraan']][0];
						$juli_k = $array_juli[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_agustus[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$agustus_d = 0;
						$agustus_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$agustus_d = $array_agustus[$rs1->fields['kdperkiraan']][0];
						$agustus_k = $array_agustus[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$agustus_d = $array_agustus[$rs1->fields['kdperkiraan']][0];
						$agustus_k = $array_agustus[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_september[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$september_d = 0;
						$september_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$september_d = $array_september[$rs1->fields['kdperkiraan']][0];
						$september_k = $array_september[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$september_d = $array_september[$rs1->fields['kdperkiraan']][0];
						$september_k = $array_september[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_oktober[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$oktober_d = 0;
						$oktober_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$oktober_d = $array_oktober[$rs1->fields['kdperkiraan']][0];
						$oktober_k = $array_oktober[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$oktober_d = $array_oktober[$rs1->fields['kdperkiraan']][0];
						$oktober_k = $array_oktober[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_nopember[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$nopember_d = 0;
						$nopember_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$nopember_d = $array_nopember[$rs1->fields['kdperkiraan']][0];
						$nopember_k = $array_nopember[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$nopember_d = $array_nopember[$rs1->fields['kdperkiraan']][0];
						$nopember_k = $array_nopember[$rs1->fields['kdperkiraan']][1];
					}
					
					$counter = count($array_desember[$rs1->fields['kdperkiraan']]);
				
					if (!$counter)
					{
						$desember_d = 0;
						$desember_k = 0;
					}
					else if(($counter==4) || ($counter==6))
					{
						$desember_d = $array_desember[$rs1->fields['kdperkiraan']][0];
						$desember_k = $array_desember[$rs1->fields['kdperkiraan']][3];
					}
					else if($counter==2)
					{
						$desember_d = $array_desember[$rs1->fields['kdperkiraan']][0];
						$desember_k = $array_desember[$rs1->fields['kdperkiraan']][1];
					}
					
						
					$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
					if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
					{
						$saldo_awal = $mutasi_debet - $mutasi_kredit;			
						//Mencari laba bulan berjalan
						//yahya 22-04-2008
						if ($curr_coa == "32211")
						{
							$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
							FROM
							(
								SELECT
											(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
											(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
											,1 AS grp
										 FROM $table pjur
										 WHERE true
										 			AND 
													(
														DATE_PART('YEAR',pjur.tanggal) < '".$batasan_tahun_new."' 
													)
												  AND isdel = 'f' -- AND isapp='t'
												  AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
										 GROUP BY pjur.dk
							)t
							GROUP BY grp";
							//die($sql3);
							$saldo_awal = $base->db->getOne($sql3);
							
							if ($saldo_awal < 0)
							{
								$saldo_awal_d = 0;
								$saldo_awal_k = $saldo_awal;
								$saldo_awal_k = str_replace("-", "", $saldo_awal_k);
							}
							else
							{
								$saldo_awal_d = $saldo_awal;
								$saldo_awal_k = 0;
							}
						}
						else
						{
							if ($saldo_awal < 0)
							{
								$saldo_awal_d = 0;
								$saldo_awal_k = $saldo_awal;
								$saldo_awal_k = str_replace("-", "", $saldo_awal_k);
							}
							else
							{
								$saldo_awal_d = $saldo_awal;
								$saldo_awal_k = 0;
							}
						}
					}
					else
					{
						$saldo_awal_d = 0;
						$saldo_awal_k = 0;
					}				
					$jumlah_d = $saldo_awal_d+$januari_d+$februari_d+$maret_d+$april_d+$mei_d+$juni_d+$juli_d+$agustus_d+$september_d+$oktober_d+$nopember_d+$desember_d;
					$jumlah_k = $saldo_awal_k+$januari_k+$februari_k+$maret_k+$april_k+$mei_k+$juni_k+$juli_k+$agustus_k+$september_k+$oktober_k+$nopember_k+$desember_k;
					
					////Mencari Neraca dan Laba Rugi
					if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
					{
						$labarugi_d = 0;
						$labarugi_k = 0;
						$neraca = $jumlah_d - $jumlah_k;
						if ($neraca < 0)
						{
							$neraca_d = 0;
							$neraca_k = $neraca;
							$neraca_k = str_replace("-", "", $neraca_k);
						}
						else
						{
							$neraca_d = $neraca;
							$neraca_k = 0;
						}
					}
					else if (in_array(substr($curr_coa,0,1), array(5,6,7,9)))
					{
						$neraca_d = 0;
						$neraca_k = 0;
							
						$labarugi = $jumlah_d - $jumlah_k;
						if ($labarugi < 0)
						{
							$labarugi_d = 0;
							$labarugi_k = $labarugi;
							$labarugi_k = str_replace("-", "", $labarugi_k);
						}
						else
						{
							$labarugi_d = $labarugi;
							$labarugi_k = 0;
						}
					}
					
					$tot_saldo_awal_d += $saldo_awal_d;
					$tot_saldo_awal_k += $saldo_awal_k;
					$tot_januari_d += $januari_d;
					$tot_januari_k += $januari_k;
					$tot_februari_d += $februari_d;
					$tot_februari_k += $februari_k;
					$tot_maret_d += $maret_d;
					$tot_maret_k += $maret_k;
					$tot_april_d += $april_d;
					$tot_april_k += $april_k;
					$tot_mei_d += $mei_d;
					$tot_mei_k += $mei_k;
					$tot_juni_d += $juni_d;
					$tot_juni_k += $juni_k;
					$tot_juli_d += $juli_d;
					$tot_juli_k += $juli_k;
					$tot_agustus_d += $agustus_d;
					$tot_agustus_k += $agustus_k;
					$tot_september_d += $september_d;
					$tot_september_k += $september_k;
					$tot_oktober_d += $oktober_d;
					$tot_oktober_k += $oktober_k;
					$tot_nopember_d += $nopember_d;
					$tot_nopember_k += $nopember_k;
					$tot_desember_d += $desember_d;
					$tot_desember_k += $desember_k;
					$tot_jumlah_d += $jumlah_d;
					$tot_jumlah_k += $jumlah_k;
					$tot_labarugi_d += $labarugi_d;
					$tot_labarugi_k += $labarugi_k;
					$tot_neraca_d += $neraca_d;
					$tot_neraca_k += $neraca_k;
					
					
					$tpl->assignDynamic('row', array(
							'VCOA'  	=> $rs1->fields['kdperkiraan'],
							'VNAMA'  	=> $rs1->fields['nmperkiraan'],
							'VD'  		=> 'D',
							'VK'  		=> 'K',
							'VJUNI_D' 	=> $this->format_money2($base, $juni_d),
							'VJUNI_K'  	=> $this->format_money2($base, $juni_k),
							'VJULI_D'  	=> $this->format_money2($base, $juli_d),
							'VJULI_K'  	=> $this->format_money2($base, $juli_k),
							'VAGUSTUS_D' 	=> $this->format_money2($base, $agustus_d),
							'VAGUSTUS_K' 	=> $this->format_money2($base, $agustus_k),
							'VSEPTEMBER_D'  	=> $this->format_money2($base, $september_d),
							'VSEPTEMBER_K'  	=> $this->format_money2($base, $september_k),
							'VOKTOBER_D'  	=> $this->format_money2($base, $oktober_d),
							'VOKTOBER_K'  	=> $this->format_money2($base, $oktober_k),
							'VNOPEMBER_D'  	=> $this->format_money2($base, $nopember_d),
							'VNOPEMBER_K'  	=> $this->format_money2($base, $nopember_k),
							'VDESEMBER_D'  	=> $this->format_money2($base, $desember_d),
							'VDESEMBER_K'  	=> $this->format_money2($base, $desember_k),
							'VJUMLAH_D'  	=> $this->format_money2($base, $jumlah_d),
							'VJUMLAH_K'  	=> $this->format_money2($base, $jumlah_k),
							'VLABARUGI_D'  	=> $this->format_money2($base, $labarugi_d),
							'VLABARUGI_K'  	=> $this->format_money2($base, $labarugi_k),
							'VNERACA_D'  	=> $this->format_money2($base, $neraca_d),
							'VNERACA_K'  	=> $this->format_money2($base, $neraca_k),
					 ));
					$tpl->parseConcatDynamic('row');
				
				
					// ==== FOR EXCEL
					
					$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> $rs1->fields['kdperkiraan'],
							'VNAMA'  	=> $rs1->fields['nmperkiraan'],
							'VD'  		=> 'D',
							'VK'  		=> 'K',
							'VJUNI_D' 	=> $juni_d,
							'VJUNI_K'  	=> $juni_k,
							'VJULI_D'  	=> $juli_d,
							'VJULI_K'  	=> $juli_k,
							'VAGUSTUS_D' 	=> $agustus_d,
							'VAGUSTUS_K' 	=> $agustus_k,
							'VSEPTEMBER_D'  	=> $september_d,
							'VSEPTEMBER_K'  	=> $september_k,
							'VOKTOBER_D'  	=> $oktober_d,
							'VOKTOBER_K'  	=> $oktober_k,
							'VNOPEMBER_D'  	=> $nopember_d,
							'VNOPEMBER_K'  	=> $nopember_k,
							'VDESEMBER_D'  	=> $desember_d,
							'VDESEMBER_K'  	=> $desember_k,
							'VJUMLAH_D'  	=> $jumlah_d,
							'VJUMLAH_K'  	=> $jumlah_k,
							'VLABARUGI_D'  	=> $labarugi_d,
							'VLABARUGI_K'  	=> $labarugi_k,
							'VNERACA_D'  	=> $neraca_d,
							'VNERACA_K'  	=> $neraca_k,
					));
					$tpl_excel->parseConcatDynamic('row');
					
					$kdperkiraan_tmp == $curr_coa;
						
					$rs1->moveNext();
									
				} // end of while	
				
					$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
					FROM
					(
						SELECT
									(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
									(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
									,1 AS grp
								 FROM $table pjur
								 WHERE true
											 AND 
												(
													DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
												)
											 AND isdel = 'f' -- AND isapp='t'
											 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
								 GROUP BY pjur.dk
					)t
					GROUP BY grp";
					//die($sql3);
					
					$lr = $base->db->getOne($sql3);
					$nama_lr = "Laba Rugi Tahun Ini";
					//die($sql3);
					if ($lr < 0)
					{
						$lr = str_replace("-", "", $lr);
						$labarugi_d = $lr;
						$labarugi_k = 0;
						$neraca_d = 0;
						$neraca_k = $lr;
					}
					else
					{
						$labarugi_d = 0;
						$labarugi_k = $lr;
						$neraca_d = $lr;
						$neraca_k = 0;
					}
						//die($neraca_debet);
					
					$tpl->assignDynamic('row', array(
							'VCOA'  	=> '&nbsp;',
							'VNAMA'  	=> $nama_lr,
							'VD'  		=> 'D',
							'VK'  		=> 'K',
							'VJUNI_D' 	=> '0',
							'VJUNI_K'  	=> '0',
							'VJULI_D'  	=> '0',
							'VJULI_K'  	=> '0',
							'VAGUSTUS_D' 	=> '0',
							'VAGUSTUS_K' 	=> '0',
							'VSEPTEMBER_D'  	=> '0',
							'VSEPTEMBER_K'  	=> '0',
							'VOKTOBER_D'  	=> '0',
							'VOKTOBER_K'  	=> '0',
							'VNOPEMBER_D'  	=> '0',
							'VNOPEMBER_K'  	=> '0',
							'VDESEMBER_D'  	=> '0',
							'VDESEMBER_K'  	=> '0',
							'VJUMLAH_D'  	=> '0',
							'VJUMLAH_K'  	=> '0',
							'VLABARUGI_D'  	=> $this->format_money2($base, $labarugi_d),
							'VLABARUGI_K'  	=> $this->format_money2($base, $labarugi_k),
							'VNERACA_D'  	=> $this->format_money2($base, $neraca_d),
							'VNERACA_K'  	=> $this->format_money2($base, $neraca_k),
					 ));
					$tpl->parseConcatDynamic('row');
				
				
					// ==== FOR EXCEL
					
					$tpl_excel->assignDynamic('row', array(
							'VCOA'  	=> '&nbsp;',
							'VNAMA'  	=> $nama_lr,
							'VD'  		=> 'D',
							'VK'  		=> 'K',
							'VJUNI_D' 	=> '0',
							'VJUNI_K'  	=> '0',
							'VJULI_D'  	=> '0',
							'VJULI_K'  	=> '0',
							'VAGUSTUS_D' 	=> '0',
							'VAGUSTUS_K' 	=> '0',
							'VSEPTEMBER_D'  	=> '0',
							'VSEPTEMBER_K'  	=> '0',
							'VOKTOBER_D'  	=> '0',
							'VOKTOBER_K'  	=> '0',
							'VNOPEMBER_D'  	=> '0',
							'VNOPEMBER_K'  	=> '0',
							'VDESEMBER_D'  	=> '0',
							'VDESEMBER_K'  	=> '0',
							'VJUMLAH_D'  	=> '0',
							'VJUMLAH_K'  	=> '0',
							'VLABARUGI_D'  	=> $labarugi_d,
							'VLABARUGI_K'  	=> $labarugi_k,
							'VNERACA_D'  	=> $neraca_d,
							'VNERACA_K'  	=> $neraca_k,
					));
					$tpl_excel->parseConcatDynamic('row');
					
				
					
					$tot_labarugi_d = $tot_labarugi_d+$labarugi_d;
					$tot_labarugi_k = $tot_labarugi_k+$labarugi_k;
					$tot_neraca_d = $tot_neraca_d+$neraca_d;
					$tot_neraca_k = $tot_neraca_k+$neraca_k;
		
		
					
					$realbalend = '';
					
				
				// =====
				
					$tpl->Assign(array(
						'VD'  => 'D',
						'VK'  => 'K',
						'VTOT_JUNI_D'  => $this->format_money2($base, $tot_juni_d),
						'VTOT_JUNI_K'  => $this->format_money2($base, $tot_juni_k),
						'VTOT_JULI_D'  => $this->format_money2($base, $tot_juli_k),
						'VTOT_JULI_K'  => $this->format_money2($base, $tot_juli_k),
						'VTOT_AGUSTUS_D'  => $this->format_money2($base, $tot_agustus_d),
						'VTOT_AGUSTUS_K'  => $this->format_money2($base, $tot_agustus_k),
						'VTOT_SEPTEMBER_D'  => $this->format_money2($base, $tot_september_d),
						'VTOT_SEPTEMBER_K'  => $this->format_money2($base, $tot_september_k),
						'VTOT_OKTOBER_D'  => $this->format_money2($base, $tot_oktober_d),
						'VTOT_OKTOBER_K'  => $this->format_money2($base, $tot_oktober_k),
						'VTOT_NOPEMBER_D'  => $this->format_money2($base, $tot_nopember_d),
						'VTOT_NOPEMBER_K'  => $this->format_money2($base, $tot_nopember_k),
						'VTOT_DESEMBER_D'  => $this->format_money2($base, $tot_desember_d),
						'VTOT_DESEMBER_K'  => $this->format_money2($base, $tot_desember_k),
						'VTOT_JUMLAH_D'  => $this->format_money2($base, $tot_jumlah_d),
						'VTOT_JUMLAH_K'  => $this->format_money2($base, $tot_jumlah_k),
						'VTOT_LABARUGI_D'  => $this->format_money2($base, $tot_labarugi_d),
						'VTOT_LABARUGI_K'  => $this->format_money2($base, $tot_labarugi_k),
						'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_d),
						'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_k),
							));				
					$this->_fill_static_report($base,&$tpl);
					
					$tpl->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
						'SEMESTER'		=> $semester, 
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
	
						// ===== FOR EXCEL
						
						$tpl_excel->Assign(array(
							'VD'  => 'D',
							'VK'  => 'K',
							'VTOT_JUNI_D'  => $tot_juni_d,
							'VTOT_JUNI_K'  => $tot_juni_k,
							'VTOT_JULI_D'  => $tot_juli_k,
							'VTOT_JULI_K'  => $tot_juli_k,
							'VTOT_AGUSTUS_D'  => $tot_agustus_d,
							'VTOT_AGUSTUS_K'  => $tot_agustus_k,
							'VTOT_SEPTEMBER_D'  => $tot_september_d,
							'VTOT_SEPTEMBER_K'  => $tot_september_k,
							'VTOT_OKTOBER_D'  => $tot_oktober_d,
							'VTOT_OKTOBER_K'  => $tot_oktober_k,
							'VTOT_NOPEMBER_D'  => $tot_nopember_d,
							'VTOT_NOPEMBER_K'  => $tot_nopember_k,
							'VTOT_DESEMBER_D'  => $tot_desember_d,
							'VTOT_DESEMBER_K'  => $tot_desember_k,
							'VTOT_JUMLAH_D'  => $tot_jumlah_d,
							'VTOT_JUMLAH_K'  => $tot_jumlah_k,
							'VTOT_LABARUGI_D'  => $tot_labarugi_d,
							'VTOT_LABARUGI_K'  => $tot_labarugi_k,
							'VTOT_NERACA_D'  => $tot_neraca_d,
							'VTOT_NERACA_K'  => $tot_neraca_k,
							));				
							
						$this->_fill_static_report($base,&$tpl_excel);
						
						$tpl_excel->Assign(array(
							'VTHN'   	=> $ryear,
							'VBLN'  	=> $rmonth,
							'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
							'SEMESTER'		=> $semester, 
							'SDATE' 	=> $startdate,
							'EDATE' 	=> $enddate,
							'SID'     => MYSID,
								));
							
					
					
			}	
				
			$dp = new dateparse();
			$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);
	
					
			$tpl->Assign(array(
				'PERIODE'  => $startdate.' s.d '.$enddate,
				'YEAR'  => $ryear,
				'VTHN'  => $ryear,
				'VBLN'  => $nm_bulan_,
				'VAP'  => '',
			));
			
			
			// ====== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'PERIODE'  => $startdate.' s.d '.$enddate,
				'YEAR'  => $ryear,
				'VTHN'  => $ryear,
				'VBLN'  => $nm_bulan_,
				'VAP'  => '',
			));
			
			// =======
			$tpl_temp->assign('ONE',$tpl,'template');
			$tpl_temp->parseConcat();
			
			$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
			$tpl_temp_excel->parseConcat();
			
	
			$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
			$tpl_temp->Assign('ONE', '
				<div id="pr" name="pr" align="left">
				<br>
				<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
				<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
				<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
				<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
									<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
				-->
				</div>'
			);
			$tpl_temp->parseConcat();
	
			
			// ======= FOR EXCEL
			
			$tpl_temp_excel->Assign('ONE', '
				<div id="pr" name="pr" align="left">
				<br>
				<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
				<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
				<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
				<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
									<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
				-->
				</div>'
			);
			
			$tpl_temp_excel->parseConcat();
			
			
			// =======
					
			
			$is_proses = $this->get_var('is_proses');
			$divname = str_replace(" ","_",$divname);
			if($is_proses=='t')
			{
				$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_2_".$ryear.".html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);	
				$this->tpl =& $tpl_temp;			
				// ==== FOR EXCEL
						
							$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_semester_2_".$ryear."_for_excel.html";
							$isi2 = & $tpl_temp_excel->parsedPage();
							$this->cetak_to_file($base,$filename_excel,$isi2);
				// ====
			}
			else
			{
				$this->tpl_excel =& $tpl_temp_excel;
				$this->tpl =& $tpl_temp;			
			}
		}
	}/*}}}*/
	

	function sub_report_neraca_lajur_konstruksi($base) /*{{{*/
	{		
		//$base->db->debug=true;
		$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '$kddiv' ");
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ====== FOR EXCEL
			
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		/////BFORTUZ
		//die("Konstruksi");
    	//START : Mencari akumulasi mutasi divisi B
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_b,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_b
			FROM jurnal_b jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		
		$array_mutasi_b = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_b[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_b'];				
				$array_mutasi_b[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_b'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_a);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi F
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_f,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_f
			FROM jurnal_f jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_f = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_f[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_f'];				
				$array_mutasi_f[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_f'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi O
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_o,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_o
			FROM jurnal_o jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_o = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_o[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_o'];				
				$array_mutasi_o[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_o'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi R
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_r,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_r
			FROM jurnal_r jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_r = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_r[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_r'];				
				$array_mutasi_r[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_r'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi T
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_t,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_t
			FROM jurnal_t jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_t = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_t[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_t'];				
				$array_mutasi_t[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_t'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi U
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_u,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_u
			FROM jurnal_r jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f' -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_u = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_r[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_u'];				
				$array_mutasi_r[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_u'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		//START : Mencari akumulasi mutasi divisi Z
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_z,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_z
			FROM jurnal_r jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  --- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		$array_mutasi_z = array();
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_z[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_z'];				
				$array_mutasi_z[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_z'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_f);		
			//die($sql);
		}
		
		////Mulai dihitung dan diisi
		$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			(SELECT jur.kdperkiraan,d.nmperkiraan,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			FROM $table jur,dperkir d
			WHERE jur.kdperkiraan=d.kdperkiraan AND
						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						AND isdel = 'f' --AND isapp='t'
			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk
			)
			UNION ALL (SELECT '32211' as kdperkiraan
      , 'LABA TAHUN BERJALAN' as nmperkiraan
      , 0 as rupiah_debet
      , 0 as rupiah_kredit )) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp = '';

			// ====== 
		
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');
			// =====
			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;			
					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 AND isdel = 'f' -- AND isapp='t'
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$neraca_awal = $base->db->getOne($sql3);
						
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = str_replace("-", "", $nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					else
					{
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = str_replace("-", "", $nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet = 0;
						$neraca = str_replace("-", "", $neraca);
						$neraca_kredit = $neraca;
					}
					else
					{
						$neraca_debet = $neraca;
						$neraca_kredit = 0;
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if ((substr($curr_coa,0,1) == "4") || (substr($curr_coa,0,1) == "5"))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$lr_debet = 0;
					$lr_kredit = 0;
				}
				
				
					
				$tot_nerawal_d += $nerawal_d;
				$tot_nerawal_k += $nerawal_k;
				$tot_mutasi_debet += $mutasi_debet;
				$tot_mutasi_kredit += $mutasi_kredit;
				$tot_neraca_debet += $neraca_debet;
				$tot_neraca_kredit += $neraca_kredit;
				$tot_nercoba_debet += $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet += $lr_debet;
				$tot_lr_kredit += $lr_kredit;
				//die("yahya");
				
				
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> $rs1->fields['kdperkiraan'],
					  'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $nerawal_d,
					'VNERAWAL_K'  	=> $nerawal_k,
					'VMUTASI_D'  	=> $mutasi_debet,
					'VMUTASI_K'  	=> $mutasi_kredit,
					'VNERACA_D'  	=> $neraca_debet,
					'VNERACA_K'  	=> $neraca_kredit,
					'VNERCOBA_D'  	=> $nercoba_debet,
					'VNERCOBA_K'  	=> $nercoba_kredit,
					'VRUGLAB_D'  	=> $lr_debet,
					'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp = $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
				$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						$nama_lr = "Laba Rugi Bulan Ini";
						//die($sql3);
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $neraca_debet,
						'VNERACA_K'  	=> $neraca_kredit,
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $lr_debet,
						'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  		$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'		=> $divname,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $tot_nerawal_d,
						'VTOT_NERAWAL_K'  => $tot_nerawal_k,
						'VTOT_MUTASI_D'  => $tot_mutasi_debet,
						'VTOT_MUTASI_K'  => $tot_mutasi_kredit,
						'VTOT_NERACA_D'  => $tot_neraca_debet,
						'VTOT_NERACA_K'  => $tot_neraca_kredit,
						'VTOT_NERCOBA_D'  => $tot_nercoba_debet,
						'VTOT_NERCOBA_K'  => $tot_nercoba_kredit,
						'VTOT_RUGLAB_D'  => $tot_lr_debet,
						'VTOT_RUGLAB_K'  => $tot_lr_kredit,
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
						
				
				
		}	
			
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$ryear."_".$rmonth.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
						$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
		
	}/*}}}*/
	
	function sub_report_neraca_lajur_direktorat_I($base) /*{{{*/
	{		
		//$base->db->debug=true;
		$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ====== FOR EXCEL
			
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		//die("Direktorat I");
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_a,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_a
			FROM jurnal_a jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			$array_mutasi_a = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_a[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_a'];				
				$array_mutasi_a[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_a'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi_a);		
			//die($sql);
		}
		
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet_f,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit_f
			FROM jurnal_f jur
			WHERE true
			AND 
			(
				DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			)
			AND isdel = 'f'  -- AND isapp='t'
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
		if ($rs2->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			// ===== begin while $rs2
			$array_mutasi_f = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi_f[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet_f'];				
				$array_mutasi_f[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit_f'];
				
				$rs2->moveNext();
				
			} // end of while	
			print_r($array_mutasi_f);		
			die($sql);
		}
		
    	//START : Mencari akumulasi mutasi
	}/*}}}*/
	
	function sub_report_neraca_lajur_direktorat_II($base) /*{{{*/
	{		
		//$base->db->debug=true;
		$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ====== FOR EXCEL
			
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		die("Direktorat 2");
    	//START : Mencari akumulasi mutasi
	}/*}}}*/
	
	function sub_report_neraca_lajur_irul($base, $cron_konsolidasi=false) /*{{{*/
	{		
    	//$base->db->debug = true;
        
        if($cron_konsolidasi)
        {
            $this->Q['konsolidasi'] = 'yes';
        }

	    $kdwilayah = $this->get_var('kdwilayah');
	    if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
	    {
	    	$divnameplus = ' : ' . $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdwilayah'");
	    }
	    else 
	    {
	    	$kdwilayah = '';
	    	$divnameplus = '';
	    }
        
		//$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
    	$divname .= " $divnameplus";
		
        if($this->get_var('konsolidasi') == 'yes')
        {
            $table = "v_jurnal_konsolidasi";
        }
        else
        {
            $table = ($this->S['curr_divisi'] == '') ? "jurnal" : "jurnal_".strtolower($this->S['curr_divisi']);    
        }
		
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');
		$rnobukti = $this->get_var('nobukti', '');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ====== FOR EXCEL
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));

		$start_periode = $this->get_var('startdate',date('Y-m-d'));
		//die($batasan_tgl_sal_akhir);
    	//START : Mencari akumulasi mutasi
    	
    	$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";

      	if ($kdwilayah != '')
        	$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah' OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
    	
    	$date_end_of_month = date('Y-m-t',strtotime($final_batasan_tanggal));
		
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_kredit
			FROM $table jur LEFT JOIN dspk d ON (jur.kdspk=d.kdspk  AND d.kddiv=jur.kddivisi)
			WHERE 
			--(
			--	DATE_PART('YEAR',jur.tanggal) = '$batasan_tahun_new' 
			--	AND DATE_PART('MONTH',jur.tanggal) = '$batasan_bulan_new'
			--)
			jur.tanggal >='$final_batasan_tanggal'::DATE AND jur.tanggal <= '$date_end_of_month'::DATE
			AND isdel = 'f' -- AND isapp='t'
			{$sql_and_nobukti}
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		//echo $sql;
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
        if ($rs2->EOF)
        {
        	$tpl->Assign('row','');
        	
        	// ==== FOR EXCEL
        	$tpl_excel->Assign('row','');		
        	// ====
        }
		else
        {
			$tpl->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID' 		=> MYSID,
				'VCURR' 	=> '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi);		
			//die($sql);
		}
		//die ($sql1);		
		
		$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";
      	if ($kdwilayah != '')
        	$sql_and_nobukti .= " AND (e.kodewilayah='$kdwilayah' OR e.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
		
			$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			     (SELECT jur.kdperkiraan,d.nmperkiraan,
        						 (CASE WHEN dk='D' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_debet,
        						 (CASE WHEN dk='K' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_kredit
        			FROM $table jur LEFT JOIN dspk e ON (jur.kdspk=e.kdspk   AND e.kddiv=jur.kddivisi), dperkir d
        			WHERE jur.kdperkiraan=d.kdperkiraan AND 
        						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
        						AND isdel = 'f' -- AND isapp='t'
        						--AND jur.kdperkiraan <> '32212'
        						{$sql_and_nobukti}
        			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
        			ORDER BY jur.kdperkiraan,jur.dk
			     )
      		    UNION ALL 
      		    	(
      		    		SELECT 
      		    		'32211' as kdperkiraan, 'LABA TAHUN BERJALAN' as nmperkiraan, 0 as rupiah_debet, 0 as rupiah_kredit 
      		    	)
      		    --UNION ALL 
      		    --	(
      		    --		SELECT 
      		    --		'32212' as kdperkiraan, 'PENDAPATAN KSO' as nmperkiraan, 0 as rupiah_debet, 0 as rupiah_kredit 
      		    --	)
              ) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		//die($sql1);
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'  		=> MYSID,
				'VCURR' 	=> '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp = '';

			// ====== 
		
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');
			// =====
			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;		
					
					$sql_and_nobukti = '';
			    	if ($rnobukti != '')
						$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	

          			if ($kdwilayah != '')
            			$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%' ";

					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir

						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
									 WHERE true
												 --AND DATE(pjur.tanggal) >= '1-1-$batasan_tahun_new' 
                                                 AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 AND isdel = 'f' -- AND isapp='t'
												 {$sql_and_nobukti}
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211' 
												 OR pjur.kdperkiraan='32212'
												 )
												
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
                        /*echo '<pre>';
                        echo $sql3;
                        echo '</pre>';*/
						$neraca_awal = $base->dbGetOne($sql3);
                       
                       	

							if ($neraca_awal < 0)
							{
								if ($curr_coa === "32212"){
	                       			$nerawal_k = 0;
									$nerawal_d = 0;
								}else{
									$nerawal_d = 0;
									$nerawal_k = $neraca_awal;
									// $nerawal_k = str_replace("-", "", $nerawal_k);
									$nerawal_k = abs($nerawal_k); 
								}
							}
							else
							{
								if ($curr_coa === "32212"){
	                       			$nerawal_k = 0;
									$nerawal_d = 0;
								}else{
									$nerawal_k = 0;
									$nerawal_d = $neraca_awal;
								}
							}
						

					}
					else
					{
						if ($neraca_awal < 0)
						{
							if ($curr_coa === "32212"){
								$nerawal_d = 0;
								$nerawal_k = 0;
								// $nerawal_k = str_replace("-", "", $nerawal_k);
								$nerawal_k = abs($nerawal_k);
							}else{
								$nerawal_d = 0;
								$nerawal_k = $neraca_awal;
								// $nerawal_k = str_replace("-", "", $nerawal_k);
								$nerawal_k = abs($nerawal_k);
							}
						}
						else
						{
							if ($curr_coa === "32212"){
								$nerawal_d = 0;
								$nerawal_k = 0;
							}else{
								$nerawal_k = 0;
								$nerawal_d = $neraca_awal;
							}
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						/*$neraca_debet = 0;
						// $neraca = str_replace("-", "", $neraca);
						$neraca = abs($neraca);
						$neraca_kredit = $neraca;*/
						if ($curr_coa === "32212"){
							$neraca_debet = 0;
							$neraca_kredit = 0;
						}else{
							$neraca_debet = 0;
							// $neraca = str_replace("-", "", $neraca);
							$neraca = abs($neraca);
							$neraca_kredit = $neraca;
						}

					}
					else
					{
						if ($curr_coa == "32212"){
							$neraca_debet = 0;//$neraca;
							$neraca_kredit = 0;//$neraca;
						}else{
							$neraca_debet = $neraca;
							$neraca_kredit = 0;
						}
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(4,5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					//$lr_debet = 0;
					//$lr_kredit = 0;
					/*if ($laba_rugi < 0)
					{
						if ($curr_coa == "32212"){
							$lr_debet = $laba_rugi;
							$lr_kredit = 0;
							//$lr_kredit = str_replace("-", "", $lr_kredit);
						}else{
							$lr_debet = 0;
							$lr_kredit = 0;
						}
					}
					else
					{
						if ($curr_coa == "32212"){
							$lr_debet = $laba_rugi;
							$lr_kredit = 0;
						}else{
							$lr_debet = 0;
							$lr_kredit = 0;
							//$lr_kredit = str_replace("-", "", $lr_kredit);
						}
					}*/
					if ($curr_coa == "32212"){
						if ($laba_rugi < 0)
						{
							$lr_debet = 0;
							$lr_kredit = $laba_rugi;
							$lr_kredit = str_replace("-", "", $lr_kredit);
						}else{
							$lr_debet = $laba_rugi;
							$lr_kredit = 0;
						}
					}else{
						$lr_debet = 0;
						$lr_kredit = 0;
					}
				}
				
				
				
				$tot_nerawal_d 		+= $nerawal_d;
				$tot_nerawal_k 		+= $nerawal_k;
				$tot_mutasi_debet 	+= $mutasi_debet;
				$tot_mutasi_kredit 	+= $mutasi_kredit;
				$tot_neraca_debet 	+= $neraca_debet;
				$tot_neraca_kredit 	+= $neraca_kredit;
				$tot_nercoba_debet 	+= $nercoba_debet;
				$tot_nercoba_kredit	+= $nercoba_kredit;
				$tot_lr_debet 		+= $lr_debet;
				$tot_lr_kredit 		+= $lr_kredit;
				//die("yahya");
                
				if($rs1->fields['kdperkiraan'] != "32211")
				{
					$test['coa'][] 			= $rs1->fields['kdperkiraan'];
					$test['debet'][] 		= $neraca_debet;
					$test['kredit'][] 		= $neraca_kredit;
					$test['debet_laba'][]	= $lr_debet;
					$test['kredit_laba'][] 	= $lr_kredit;
				}
				
				$tpl->assignDynamic('row', array(
					  	'VCOA'  		=> $rs1->fields['kdperkiraan'],
					  	'VNAMA'  		=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			/*
			var_dump(array(
					  'VCOA'  	=> $rs1->fields['kdperkiraan'],
					  'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
			*/

				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $this->format_money2($base,$nerawal_d),
					'VNERAWAL_K'  	=> $this->format_money2($base,$nerawal_k),
					'VMUTASI_D'  	=> $this->format_money2($base,$mutasi_debet),
					'VMUTASI_K'  	=> $this->format_money2($base,$mutasi_kredit),
					'VNERACA_D'  	=> $this->format_money2($base,$neraca_debet),
					'VNERACA_K'  	=> $this->format_money2($base,$neraca_kredit),
					'VNERCOBA_D'  	=> $this->format_money2($base,$nercoba_debet),
					'VNERCOBA_K'  	=> $this->format_money2($base,$nercoba_kredit),
					'VRUGLAB_D'  	=> $this->format_money2($base,$lr_debet),
					'VRUGLAB_K'  	=> $this->format_money2($base,$lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp = $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
            
            $sql_and_nobukti = '';
	    	if ($rnobukti != '')
				$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	
          	if ($kdwilayah != '')
            	$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
            
            //print_r($test);
				$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
									 WHERE true
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 {$sql_and_nobukti}
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan = '32212')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						$nama_lr = "Laba Rugi Bulan Ini------------";
						//die($sql3);
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
					  	'VCOA'  		=> '&nbsp;',
					  	'VNAMA'  		=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');

        //luki: cari dulu siapa tau coa 32211 sudah ada
        $found  = false;
        foreach ($test['coa'] as $k=>$v)
        {
          if ($v == '32211')
          {
            $found  = true;
            $test['debet'][$k] += $neraca_debet;
            $test['kredit'][$k] += $neraca_kredit;
            $test['debet_laba'][$k]+=$lr_debet;
            $test['kredit_laba'][$k] +=$lr_kredit;
            //$test['coa'][] = '32211';
            break;
          }
         /* if ($v == '32212')
          {
            $found  = true;
            $test['debet'][$k] = 0;
            $test['kredit'][$k] = 0;
            $test['debet_laba'][$k]=0;
            $test['kredit_laba'][$k] =0;
            //$test['coa'][] = '32211';
            //var_dump($test);
            break;
          }*/
        }
        //
        if ($found == false)
        {
			    $test['debet'][] = $neraca_debet;
    			$test['kredit'][] = $neraca_kredit;
				$test['debet_laba'][]=$lr_debet;
				$test['kredit_laba'][] =$lr_kredit;
			    $test['coa'][] = '32211';
        }
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  		=> '&nbsp;',
					  	'VNAMA'  		=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  			$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  	=> $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  	=> $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  	=> $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  	=> $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  	=> $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  	=> $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  	=> $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  	=> $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  	=> $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  	=> $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  	=> $this->format_money2($base, $tot_nerawal_d),
						'VTOT_NERAWAL_K'  	=> $this->format_money2($base, $tot_nerawal_k),
						'VTOT_MUTASI_D'  	=> $this->format_money2($base, $tot_mutasi_debet),
						'VTOT_MUTASI_K'  	=> $this->format_money2($base, $tot_mutasi_kredit),
						'VTOT_NERACA_D'  	=> $this->format_money2($base, $tot_neraca_debet),
						'VTOT_NERACA_K'  	=> $this->format_money2($base, $tot_neraca_kredit),
						'VTOT_NERCOBA_D' 	=> $this->format_money2($base, $tot_nercoba_debet),
						'VTOT_NERCOBA_K'  	=> $this->format_money2($base, $tot_nercoba_kredit),
						'VTOT_RUGLAB_D'  	=> $this->format_money2($base, $tot_lr_debet),
						'VTOT_RUGLAB_K'  	=> $this->format_money2($base, $tot_lr_kredit),
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
		}	
			$sql_anto = "INSERT INTO z_temp_neraca_t_new (kddivisi,tanggal,group_name,rupiah_debit,rupiah_kredit,rupiah_laba_debit,rupiah_laba_kredit,tanggal_create,kodewilayah) VALUES ";
            for($i = 0; $i < count($test['debet']);$i++)
            {
                $sql_anto .= "('{$kddiv}','{$ryear}-".((strlen($rmonth)==1)?"0".$rmonth:$rmonth)."-01','".$test['coa'][$i]."','".(($test['debet'][$i]=="")?0:$test['debet'][$i])."','".(($test['kredit'][$i]=="")?0:$test['kredit'][$i])."','".
                (($test['debet_laba'][$i]=="")?0:$test['debet_laba'][$i])."','".(($test['kredit_laba'][$i]=="")?0:$test['kredit_laba'][$i])."','".date("Y-m-d")."','$kdwilayah')";
                if(($i+1) <> count($test['debet'])) 
                {
                    $sql_anto .=",";
                }
                else
                {
                    $sql_anto .=";";
                }
            }
            //echo $sql_anto;
            //$base->db->BeginTrans();
    		$sql_delete="DELETE FROM z_temp_neraca_t_new WHERE kddivisi = '{$kddiv}' and tanggal = '{$ryear}-".((strlen($rmonth)==1)?'0'.$rmonth:$rmonth)."-01' AND kodewilayah='$kdwilayah';";
		
			$base->db->Execute($sql_delete);
			$okS = true;
			$okS = $base->db->Execute($sql_anto);
			if(!$okS)
			{
				$pesan = $base->db->ErrorMsg();
				$pesan = str_replace('"','',$pesan);
				$pesan = trim($pesan);
                die($pesan);
				break;
			}

      //testing
     // $base->db->commitTrans();exit;
			//kodokkodok&tutuppop
			//$tutuppop = $this->get_var('tutuppop','none');
      //echo "<pre>";print_r($test);echo "$sql_anto";
      //die('<h1>TESTAH</h1>');
			
			$tutuppop = $this->get_var('tutuppop','');
		
			if($tutuppop == 'yes')
			{
				echo '
					<script>	
					window.opener.location.reload();			
					window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes2')
			{
				echo '
					<script>				
					 window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes3')
			{
				echo '
					<script>			
          alert("Silahkan klik tombol: Membuat Laporan");
					 window.opener.close();					
					 window.close();					
					</script>
				';
			}

			/*if($okS)
    		{
    			$base->db->commitTrans();
    		}
    		else
    		{
    			$base->db->rollBackTrans();	
    		}
            */
            //print_r($test);
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
        
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
      $fadd = '';
      if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
      {
        $fadd = "_" . $kdwilayah;
      }
            $filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
			$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd."_for_excel.html";
			$isi2 = & $tpl_temp_excel->parsedPage();
			$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
	} /*}}}*/	

	function sub_report_neraca_lajur_09092017($base, $cron_konsolidasi=false) /*{{{*/
	{		
    	//$base->db->debug = true;
        if($cron_konsolidasi)
        {
            $this->Q['konsolidasi'] = 'yes';
        }

	    $kdwilayah = $this->get_var('kdwilayah');
	    if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
	    {
	      $divnameplus = ' : ' . $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdwilayah'");
	    }
	    else 
	    {
	      $kdwilayah = '';
	      $divnameplus = '';
	    }
        
		//$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
    	$divname .= " $divnameplus";
		
        if($this->get_var('konsolidasi') == 'yes')
        {
            $table = "v_jurnal_konsolidasi";
        }
        else
        {
            $table = ($this->S['curr_divisi'] == '') ? "jurnal" : "jurnal_".strtolower($this->S['curr_divisi']);    
        }
		
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');
		$rnobukti = $this->get_var('nobukti', '');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ====== FOR EXCEL
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		
		//die($batasan_tgl_sal_akhir);
    	//START : Mencari akumulasi mutasi
    	
    	$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";

      	if ($kdwilayah != '')
        	$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah' OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
    	
    	$date_end_of_month = date('Y-m-t',strtotime($final_batasan_tanggal));
		
		$sql = " SELECT
				jur.kdperkiraan,
					(CASE WHEN jur.dk='D' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_debet,
					(CASE WHEN jur.dk='K' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_kredit
				FROM $table jur LEFT JOIN dspk d ON (jur.kdspk=d.kdspk  AND d.kddiv=jur.kddivisi)
				WHERE 
					--(
					--	DATE_PART('YEAR',jur.tanggal) = '$batasan_tahun_new' 
					--	AND DATE_PART('MONTH',jur.tanggal) = '$batasan_bulan_new'
					--)
					jur.tanggal BETWEEN '$batasan_tahun_new-$batasan_bulan_new-01'::DATE AND '$date_end_of_month'::DATE
					AND isdel = 'f' -- AND isapp='t'
					{$sql_and_nobukti}
				GROUP BY jur.kdperkiraan,jur.dk
				ORDER BY jur.kdperkiraan,jur.dk";
		
		//die ($sql);		
		
		$rs2=$base->dbquery($sql);
        if ($rs2->EOF)
        {
        	$tpl->Assign('row','');
        	
        	// ==== FOR EXCEL
        	$tpl_excel->Assign('row','');		
        	// ====
        }
		else
        {
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			} // end of while
			//echo '<pre>';	
			//print_r($array_mutasi);		
			//echo '</pre>';
			//die($sql);
		}
		//die ($sql1);		
		
		$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";
      	if ($kdwilayah != '')
        	$sql_and_nobukti .= " AND (e.kodewilayah='$kdwilayah' OR e.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
		//SA
		$sql1 = "
				SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
				FROM
				(
				    (
				    SELECT jur.kdperkiraan,d.nmperkiraan,
							(CASE WHEN dk='D' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_debet,
							(CASE WHEN dk='K' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_kredit
	    			FROM $table jur 
	    			LEFT JOIN dspk e ON (jur.kdspk=e.kdspk   AND e.kddiv=jur.kddivisi), dperkir d
	    			WHERE jur.kdperkiraan=d.kdperkiraan AND 
	        						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
	        						AND isdel = 'f' -- AND isapp='t'
	        						{$sql_and_nobukti}
	    			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
	    			ORDER BY jur.kdperkiraan,jur.dk
				    )
	      		    UNION ALL (SELECT '32211' as kdperkiraan
	              	, 'LABA TAHUN BERJALAN' as nmperkiraan
	              	, 0 as rupiah_debet
	              	, 0 as rupiah_kredit )
	              	) t
				GROUP BY t.kdperkiraan,t.nmperkiraan
				ORDER BY t.kdperkiraan,t.nmperkiraan";
		//die($sql1);
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID' 		=> MYSID,
				'VCURR'		=> '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'		=> MYSID,
				'VCURR'		=> '',
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp = '';

			// ====== 
		
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');
			// =====
			while(!$rs1->EOF)
			{
				$curr_coa 		= $rs1->fields['kdperkiraan'];
				$saldo_akhir 	= $rs1->fields['saldo_akhir'];
				
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==1)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;		
					
					$sql_and_nobukti = '';
			    	if ($rnobukti != '')
						$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	

          			if ($kdwilayah != '')
            			$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%' ";

					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir

						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS kredit
										,1 AS grp
							FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
							WHERE true
								--AND DATE(pjur.tanggal) >= '1-1-$batasan_tahun_new' 
								AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
								AND isdel = 'f' -- AND isapp='t'
								{$sql_and_nobukti}
								AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')					
							GROUP BY pjur.dk
						)t
						GROUP BY grp";
                        /*echo '<pre>';
                        echo $sql3;
                        echo '</pre>';*/
						$neraca_awal = $base->dbGetOne($sql3);
                        
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							// $nerawal_k = str_replace("-", "", $nerawal_k);
							$nerawal_k = abs($nerawal_k); 
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					else
					{
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							// $nerawal_k = str_replace("-", "", $nerawal_k);
							$nerawal_k = abs($nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet = 0;
						// $neraca = str_replace("-", "", $neraca);
						$neraca = abs($neraca);
						$neraca_kredit = $neraca;
					}
					else
					{
						$neraca_debet = $neraca;
						$neraca_kredit = 0;
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$lr_debet = 0;
					$lr_kredit = 0;
				}
				
				
					
				$tot_nerawal_d += $nerawal_d;
				$tot_nerawal_k += $nerawal_k;
				$tot_mutasi_debet += $mutasi_debet;
				$tot_mutasi_kredit += $mutasi_kredit;
				$tot_neraca_debet += $neraca_debet;
				$tot_neraca_kredit += $neraca_kredit;
				$tot_nercoba_debet += $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet += $lr_debet;
				$tot_lr_kredit += $lr_kredit;
				//die("yahya");
                
				//if($rs1->fields['kdperkiraan'] != "32211")
				//{
					$test['coa'][] = $rs1->fields['kdperkiraan'];
					$test['debet'][] = $neraca_debet;
					$test['kredit'][] = $neraca_kredit;
					$test['debet_laba'][]=$lr_debet;
					$test['kredit_laba'][] =$lr_kredit;
				//}
				
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> $rs1->fields['kdperkiraan'],
					  'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
					'VNERAWAL_K'  	=> $this->format_money2($base,$nerawal_k),
					'VMUTASI_D'  	=> $this->format_money2($base,$mutasi_debet),
					'VMUTASI_K'  	=> $this->format_money2($base,$mutasi_kredit),
					'VNERACA_D'  	=> $this->format_money2($base,$neraca_debet),
					'VNERACA_K'  	=> $this->format_money2($base,$neraca_kredit),
					'VNERCOBA_D'  	=> $this->format_money2($base,$nercoba_debet),
					'VNERCOBA_K'  	=> $this->format_money2($base,$nercoba_kredit),
					'VRUGLAB_D'  	=> $this->format_money2($base,$lr_debet),
					'VRUGLAB_K'  	=> $this->format_money2($base,$lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp = $curr_coa;
				$arr[] = array(
					  'VCOA'  	=> $rs1->fields['kdperkiraan'],
					  'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit));
				
				$rs1->moveNext();
								
			} // end of while	
			//echo '<pre>'.print_r($arr).'</pre>';
			//	exit;
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
            
            $sql_and_nobukti = '';
	    	if ($rnobukti != '')
				$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	
          if ($kdwilayah != '')
            $sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
            
            //print_r($test);
				$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
									 WHERE true
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 {$sql_and_nobukti}
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						$nama_lr = "Laba Rugi Bulan Ini";
						//die($sql3);
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
        //luki: cari dulu siapa tau coa 32211 sudah ada
        $found  = false;
        foreach ($test['coa'] as $k=>$v)
        {
          if ($v == '32211')
          {
            $found  = true;
            $test['debet'][$k] += $neraca_debet;
            $test['kredit'][$k] += $neraca_kredit;
            $test['debet_laba'][$k]+=$lr_debet;
            $test['kredit_laba'][$k] +=$lr_kredit;
            //$test['coa'][] = '32211';
            break;
          }
        }
        //
        if ($found == false)
        {
			    $test['debet'][] = $neraca_debet;
    			$test['kredit'][] = $neraca_kredit;
				$test['debet_laba'][]=$lr_debet;
				$test['kredit_laba'][] =$lr_kredit;
			    $test['coa'][] = '32211';
        }
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  		$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'		=> $divname,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
						'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
						'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
						'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
						'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
						'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
						'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
						'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
						'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
						'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
						
				
				
		}	
			$sql_anto = "INSERT INTO z_temp_neraca_t_new (kddivisi,tanggal,group_name,rupiah_debit,rupiah_kredit,rupiah_laba_debit,rupiah_laba_kredit,tanggal_create,kodewilayah) VALUES ";
            for($i = 0; $i < count($test['debet']);$i++)
            {
                $sql_anto .= "('{$kddiv}','{$ryear}-".((strlen($rmonth)==1)?"0".$rmonth:$rmonth)."-01','".$test['coa'][$i]."','".(($test['debet'][$i]=="")?0:$test['debet'][$i])."','".(($test['kredit'][$i]=="")?0:$test['kredit'][$i])."','".
                (($test['debet_laba'][$i]=="")?0:$test['debet_laba'][$i])."','".(($test['kredit_laba'][$i]=="")?0:$test['kredit_laba'][$i])."','".date("Y-m-d")."','$kdwilayah')";
                if(($i+1) <> count($test['debet'])) 
                {
                    $sql_anto .=",";
                }
                else
                {
                    $sql_anto .=";";
                }
            }
            //echo $sql_anto;
            //$base->db->BeginTrans();
    		$sql_delete="DELETE FROM z_temp_neraca_t_new WHERE kddivisi = '{$kddiv}' and tanggal = '{$ryear}-".((strlen($rmonth)==1)?'0'.$rmonth:$rmonth)."-01' AND kodewilayah='$kdwilayah';";
		
			$base->db->Execute($sql_delete);
			$okS = true;
			$okS = $base->db->Execute($sql_anto);
			if(!$okS)
			{
				$pesan = $base->db->ErrorMsg();
				$pesan = str_replace('"','',$pesan);
				$pesan = trim($pesan);
                die($pesan);
				break;
			}

      //testing
     // $base->db->commitTrans();exit;
			//kodokkodok&tutuppop
			//$tutuppop = $this->get_var('tutuppop','none');
      //echo "<pre>";print_r($test);echo "$sql_anto";
      //die('<h1>TESTAH</h1>');
			
			$tutuppop = $this->get_var('tutuppop','');
		
			if($tutuppop == 'yes')
			{
				echo '
					<script>	
					window.opener.location.reload();			
					window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes2')
			{
				echo '
					<script>				
					 window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes3')
			{
				echo '
					<script>			
          alert("Silahkan klik tombol: Membuat Laporan");
					 window.opener.close();					
					 window.close();					
					</script>
				';
			}

			/*if($okS)
    		{
    			$base->db->commitTrans();
    		}
    		else
    		{
    			$base->db->rollBackTrans();	
    		}
            */
            //print_r($test);
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
        
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
      $fadd = '';
      if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
      {
        $fadd = "_" . $kdwilayah;
      }
            $filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
			$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd."_for_excel.html";
			$isi2 = & $tpl_temp_excel->parsedPage();
			$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
	}

	function sub_report_neraca_lajur_spk_print($base, $cron_konsolidasi=false) /*{{{*/#start eraca lajur spk nurfan
	{	
		
		$this->get_valid_app('SDV');
		$kdspk = $this->get_var('kdspk','');
		$rnobukti = $this->get_var('nobukti','');
			
		
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$nmspk=$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk ='{$kdspk}'");
		//die($divname);
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_spk_printable.html');
		$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ====== FOR EXCEL
			
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_spk_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
		$startdate = $this->get_var('startdate',date('d-m-Y'));
		$enddate = $this->get_var('enddate',date('d-m-Y'));

		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
				$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
				$sdate = date('Y-m-d');

		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
				$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
				$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		

		$sql_and_kdspk = '';
		if ($kdspk != '')
			$sql_and_kdspk = " AND kdspk='".$kdspk."' ";
		if ($rnobukti != '')
	    {
			$sql_and_nobukti = " AND nobukti LIKE '{$rnobukti}%' ";
	    }
		else 
	    {
			$sql_and_nobukti = " AND nobukti NOT LIKE '01%'";

			$sql = "SELECT kodejurnal FROM dspk WHERE iswilayah='t' AND kdspk=(SELECT kodewilayah FROM dspk WHERE kddiv='{$kddiv}' AND kdspk='$kdspk')";
			$kdjurnalwil = $base->dbGetOne($sql);
			if ($kdjurnalwil != '')
			{
			$sql_and_nobukti .= " AND nobukti NOT LIKE '$kdjurnalwil%'";
			}
    	}
		/**
		 * Add condition where kdperkiran not like '01%'
		 * by: Eldin
		 */	
		$sql = " SELECT
			jur.kdperkiraan,
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			FROM $table jur
			WHERE true
			AND 
			--(
			--	DATE_PART('YEAR',jur.tanggal) = '$batasan_tahun_new'  
			--	AND DATE_PART('MONTH',jur.tanggal) = '$batasan_bulan_new'
			--)
			jur.tanggal >='$final_batasan_tanggal'::DATE AND jur.tanggal <= '$batasan_tgl_sal_akhir'::DATE
			AND isdel = 'f'  -- AND isapp='t'
			{$sql_and_kdspk} 
			{$sql_and_nobukti}
			GROUP BY jur.kdperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk";
		
		//die($sql);#lagi die		
		
		$rs2=$base->dbquery($sql);
		if ($rs2->EOF)
		{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
		{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
				'NMSPK' => $nmspk
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
				'NMSPK' => $nmspk
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			}
		}
		
		$sql1 = "
			SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			(SELECT jur.kdperkiraan,d.nmperkiraan,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			FROM $table jur,dperkir d
			WHERE jur.kdperkiraan=d.kdperkiraan AND
						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						AND isdel = 'f' -- AND isapp='t'
						-- AND kdspk='$kdspk'
						-- AND jur.nobukti NOT LIKE '01%' 
						{$sql_and_kdspk} 
						{$sql_and_nobukti}
			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk
			)
			UNION ALL 
      		    	(
      		    		SELECT 
      		    		'32211' as kdperkiraan, 'LABA TAHUN BERJALAN' as nmperkiraan, 0 as rupiah_debet, 0 as rupiah_kredit 
      		    	)
      		    UNION ALL 
      		    	(
      		    		SELECT 
      		    		'32212' as kdperkiraan, 'PENDAPATAN KSO' as nmperkiraan, 0 as rupiah_debet, 0 as rupiah_kredit 
      		    	)
              ) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		
		//mutasi die($sql1);
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
			{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
			{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
				'NMSPK' => $nmspk
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
				'NMSPK' => $nmspk
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp='';
			// ========TPL
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');


			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;			
					// laba bulan berjalan
					if ($curr_coa == "32211")
					{
						
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 AND isdel = 'f' -- AND isapp='t'
												 --AND kdspk='".$kdspk."' 
												 {$sql_and_kdspk}
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211' OR pjur.kdperkiraan='32212')
												 --AND pjur.nobukti NOT LIKE '01%' 
												 {$sql_and_nobukti} 
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$neraca_awal = $base->db->getOne($sql3);
						#echo "test by bsi "
						
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = abs($nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					else
					{
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							$nerawal_k = abs($nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet = 0;
						$neraca = abs($neraca);
						$neraca_kredit = $neraca;
					}
					else
					{
						$neraca_debet = $neraca;
						$neraca_kredit = 0;
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(4,5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$lr_debet = 0;
					$lr_kredit = 0;
				}
				
				
					
				$tot_nerawal_d += $nerawal_d;
				$tot_nerawal_k += $nerawal_k;
				$tot_mutasi_debet += $mutasi_debet;
				$tot_mutasi_kredit += $mutasi_kredit;
				$tot_neraca_debet += $neraca_debet;
				$tot_neraca_kredit += $neraca_kredit;
				$tot_nercoba_debet += $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet += $lr_debet;
				$tot_lr_kredit += $lr_kredit;
				
				$tpl->assignDynamic('row', array(
						'VCOA'  	=> $rs1->fields['kdperkiraan'],
						'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $nerawal_d,
					'VNERAWAL_K'  	=> $nerawal_k,
					'VMUTASI_D'  	=> $mutasi_debet,
					'VMUTASI_K'  	=> $mutasi_kredit,
					'VNERACA_D'  	=> $neraca_debet,
					'VNERACA_K'  	=> $neraca_kredit,
					'VNERCOBA_D'  	=> $nercoba_debet,
					'VNERCOBA_K'  	=> $nercoba_kredit,
					'VRUGLAB_D'  	=> $lr_debet,
					'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp == $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			//die("yahya");
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
						// $sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						// FROM
						// (
							// SELECT
										// (CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										// (CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										// ,1 AS grp
									 // FROM $table pjur
									 // WHERE true
												 // AND 
													// (
														// DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														// AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													// )
												 // AND isdel = 'f' AND isapp='t'
												 // AND kdspk='".$kdspk."'
												 // AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									 // GROUP BY pjur.dk
						// )t
						// GROUP BY grp";
						
            /***
						$sql_and_kdspk = '';
						if ($kdspk != '')
							$sql_and_kdspk = " AND pjur.kdspk='".$kdspk."' ";
						if ($rnobukti != '')
							$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";
						else 
							$sql_and_nobukti = " AND pjur.nobukti NOT LIKE '01%' ";

            **/
						/**
						 * Add condition where kdperkiran not like '01%'
						 * by: Eldin
						 */
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 -- AND kdspk='".$kdspk."' 
												 {$sql_and_kdspk} 
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
												 -- AND pjur.nobukti NOT LIKE '01%' 
												 {$sql_and_nobukti} 
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						//test by bsi : LR die($sql3);
						$nama_lr = "Laba Rugi Bulan Ini******";
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}

				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
					  	'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $neraca_debet,
						'VNERACA_K'  	=> $neraca_kredit,
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $lr_debet,
						'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  			$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $tot_nerawal_d,
						'VTOT_NERAWAL_K'  => $tot_nerawal_k,
						'VTOT_MUTASI_D'  => $tot_mutasi_debet,
						'VTOT_MUTASI_K'  => $tot_mutasi_kredit,
						'VTOT_NERACA_D'  => $tot_neraca_debet,
						'VTOT_NERACA_K'  => $tot_neraca_kredit,
						'VTOT_NERCOBA_D'  => $tot_nercoba_debet,
						'VTOT_NERCOBA_K'  => $tot_nercoba_kredit,
						'VTOT_RUGLAB_D'  => $tot_lr_debet,
						'VTOT_RUGLAB_K'  => $tot_lr_kredit,
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
						
				
				
		}	
			
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$ryear."_".$rmonth."_".$kdspk.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
						$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$ryear."_".$rmonth."_".$kdspk."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
	} /*}}}*/#end neraca lajur spk nurfan

	function sub_report_neraca_lajur($base, $cron_konsolidasi=false) /*{{{*/ #neraca_lajur_work
	{		
   		//$base->db->debug = true;
        if($cron_konsolidasi)
        {
            $this->Q['konsolidasi'] = 'yes';
        }

	    $kdwilayah = $this->get_var('kdwilayah');
	    if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
	    {
	      $divnameplus = ' : ' . $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdwilayah'");
	    }
	    else 
	    {
	      $kdwilayah = '';
	      $divnameplus = '';
	    }
        
		//$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' AND is_visible='t' ");
    	$divname .= " $divnameplus";
		
        if($this->get_var('konsolidasi') == 'yes')
        {
            $table = "v_jurnal_konsolidasi";
        }
        else
        {
            $table = ($this->S['curr_divisi'] == '') ? "jurnal" : "jurnal_".strtolower($this->S['curr_divisi']);    
        }
		
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');
		$rnobukti = $this->get_var('nobukti', '');

		$tpl = $base->_get_tpl('report_neraca_lajur_printable.html');
    	$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ====== FOR EXCEL
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		//echo '<pre>';
		//print $thn_cari
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$sdate = date('Y-m-d');

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
      		$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
    	else
      		$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		//die($batasan_tgl_sal_akhir);
    	//START : Mencari akumulasi mutasi
    	
    	$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";

      if ($kdwilayah != '')
        $sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah' OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
    	
		$sql = "SELECT
					jur.kdperkiraan,
					(CASE WHEN jur.dk='D' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_debet,
					(CASE WHEN jur.dk='K' THEN SUM(coalesce(jur.rupiah,0)) END) AS mutasi_kredit
				FROM $table jur LEFT JOIN dspk d ON (jur.kdspk=d.kdspk  AND d.kddiv=jur.kddivisi)
				WHERE 
				(
					DATE_PART('YEAR',jur.tanggal) = '$batasan_tahun_new' 
					AND DATE_PART('MONTH',jur.tanggal) = '$batasan_bulan_new'
				)
				AND isdel = 'f' 
				{$sql_and_nobukti}
				GROUP BY jur.kdperkiraan,jur.dk
				ORDER BY jur.kdperkiraan,jur.dk";
		
		//echo 'MUTASI:: '.$sql;exit();
		
		$rs2=$base->dbquery($sql);
        if ($rs2->EOF)
        {
        	$tpl->Assign('row','');
        	
        	// ==== FOR EXCEL
        	$tpl_excel->Assign('row','');		
        	// ====
        }
		else
        {
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			} // end of while	
			//print_r($array_mutasi);		
			//die($sql);
		}
		//die ($sql1);		
		
		$sql_and_nobukti = '';
    	if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";
      if ($kdwilayah != '')
        $sql_and_nobukti .= " AND (e.kodewilayah='$kdwilayah' OR e.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
		
		//NERACA (NERACA-posisi paling kanan)
		$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
				FROM
				(
			     	(SELECT jur.kdperkiraan,d.nmperkiraan,
        						 (CASE WHEN dk='D' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_debet,
        						 (CASE WHEN dk='K' THEN SUM(coalesce(rupiah,0)) END) AS rupiah_kredit
        			FROM $table jur LEFT JOIN dspk e ON (jur.kdspk=e.kdspk   AND e.kddiv=jur.kddivisi), dperkir d
        			WHERE jur.kdperkiraan=d.kdperkiraan AND 
        						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
        						AND isdel = 'f' 
        						{$sql_and_nobukti}
        			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
        			ORDER BY jur.kdperkiraan,jur.dk
			     )
      		    UNION ALL (
      		    	SELECT '32211' as kdperkiraan, 'LABA TAHUN BERJALAN' as nmperkiraan, 0 as rupiah_debet, 0 as rupiah_kredit )
      		    ) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		
		//echo 'NERACA:: '.$sql1;
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
    	{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
    	{
			$tpl->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'   	=> MYSID,
				'VCURR'  	=> '',
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp = '';

			// ====== 
		
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');
			// =====
			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;		
					
					$sql_and_nobukti = '';
			    	if ($rnobukti != '')
						$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	

          			if ($kdwilayah != '')
            			$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%' ";

					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir

						FROM
						(
							SELECT
								(CASE WHEN pjur.dk='D' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS debet,
								(CASE WHEN pjur.dk='K' THEN SUM (coalesce(pjur.rupiah,0)) ELSE 0 END) AS kredit
								,1 AS grp
							FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
							WHERE true
								AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
								AND isdel = 'f' -- AND isapp='t'
								$sql_and_nobukti
								AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR (pjur.kdperkiraan='32211' 
								OR pjur.kdperkiraan='32212')
								)

							GROUP BY pjur.dk
						)t
						GROUP BY grp";
                        
                        //echo 'lr:: '. $sql3."<BR><BR>";
						$neraca_awal = $base->dbGetOne($sql3);
                       
                       	

							if ($neraca_awal < 0)
							{
								if ($curr_coa === "32212"){
	                       			$nerawal_k = 0;
									$nerawal_d = 0;
								}else{
									$nerawal_d = 0;
									$nerawal_k = $neraca_awal;
									// $nerawal_k = str_replace("-", "", $nerawal_k);
									$nerawal_k = abs($nerawal_k); 
								}
							}
							else
							{
								if ($curr_coa === "32212"){
	                       			$nerawal_k = 0;
									$nerawal_d = 0;
								}else{
									$nerawal_k = 0;
									$nerawal_d = $neraca_awal;
								}
							}
						

					}
					else
					{
						if ($neraca_awal < 0)
						{
							if ($curr_coa === "32212"){
								$nerawal_d = 0;
								$nerawal_k = 0;
								// $nerawal_k = str_replace("-", "", $nerawal_k);
								$nerawal_k = abs($nerawal_k);
							}else{
								$nerawal_d = 0;
								$nerawal_k = $neraca_awal;
								// $nerawal_k = str_replace("-", "", $nerawal_k);
								$nerawal_k = abs($nerawal_k);
							}
						}
						else
						{
							if ($curr_coa === "32212"){
								$nerawal_d = 0;
								$nerawal_k = 0;
							}else{
								$nerawal_k = 0;
								$nerawal_d = $neraca_awal;
							}
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						/*$neraca_debet = 0;
						// $neraca = str_replace("-", "", $neraca);
						$neraca = abs($neraca);
						$neraca_kredit = $neraca;*/
						if ($curr_coa === "32212"){
							$neraca_debet = 0;
							$neraca_kredit = 0;
						}else{
							$neraca_debet = 0;
							// $neraca = str_replace("-", "", $neraca);
							$neraca = abs($neraca);
							$neraca_kredit = $neraca;
						}

					}
					else
					{
						if ($curr_coa == "32212"){
							$neraca_debet = 0;//$neraca;
							$neraca_kredit = 0;//$neraca;
						}else{
							$neraca_debet = $neraca;
							$neraca_kredit = 0;
						}
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(4,5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					//var_dump($laba_rugi);
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					//$lr_debet = 0;
					//$lr_kredit = 0;
					/*if ($laba_rugi < 0)
					{
						if ($curr_coa == "32212"){
							$lr_debet = $laba_rugi;
							$lr_kredit = 0;
							//$lr_kredit = str_replace("-", "", $lr_kredit);
						}else{
							$lr_debet = 0;
							$lr_kredit = 0;
						}
					}
					else
					{
						if ($curr_coa == "32212"){
							$lr_debet = $laba_rugi;
							$lr_kredit = 0;
						}else{
							$lr_debet = 0;
							$lr_kredit = 0;
							//$lr_kredit = str_replace("-", "", $lr_kredit);
						}
					}*/
					if ($curr_coa === "32212"){
						if ($laba_rugi < 0)
						{
							$lr_debet = 0;
							//$lr_kredit = $laba_rugi;
							$lr_kredit = str_replace("-", "", $laba_rugi);
						}else{
							$lr_debet = $laba_rugi;
							$lr_kredit = str_replace("-", "", $laba_rugi);
						}
					}else{
						$lr_debet = 0;
						$lr_kredit = 0;
					}
				}
				
				
					
				$tot_nerawal_d 		+= $nerawal_d;
				$tot_nerawal_k 		+= $nerawal_k;
				$tot_mutasi_debet 	+= $mutasi_debet;
				$tot_mutasi_kredit 	+= $mutasi_kredit;
				$tot_neraca_debet 	+= $neraca_debet;
				$tot_neraca_kredit 	+= $neraca_kredit;
				$tot_nercoba_debet 	+= $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet 		+= $lr_debet;
				$tot_lr_kredit 		+= $lr_kredit;
				//die("lu gf...wakakakakakak");
                
				if($rs1->fields['kdperkiraan'] != "32211")
				{
					$test['coa'][] = $rs1->fields['kdperkiraan'];
					$test['debet'][] = $neraca_debet;
					$test['kredit'][] = $neraca_kredit;
					$test['debet_laba'][]=$lr_debet;
					$test['kredit_laba'][] =$lr_kredit;
				}
				
				$tpl->assignDynamic('row', array(
					  	'VCOA'  	=> $rs1->fields['kdperkiraan'],
					  	'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
					'VNERAWAL_K'  	=> $this->format_money2($base,$nerawal_k),
					'VMUTASI_D'  	=> $this->format_money2($base,$mutasi_debet),
					'VMUTASI_K'  	=> $this->format_money2($base,$mutasi_kredit),
					'VNERACA_D'  	=> $this->format_money2($base,$neraca_debet),
					'VNERACA_K'  	=> $this->format_money2($base,$neraca_kredit),
					'VNERCOBA_D'  	=> $this->format_money2($base,$nercoba_debet),
					'VNERCOBA_K'  	=> $this->format_money2($base,$nercoba_kredit),
					'VRUGLAB_D'  	=> $this->format_money2($base,$lr_debet),
					'VRUGLAB_K'  	=> $this->format_money2($base,$lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp = $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
            
            $sql_and_nobukti = '';
	    	if ($rnobukti != '')
				$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";	
          	if ($kdwilayah != '')
            	$sql_and_nobukti .= " AND (d.kodewilayah='$kdwilayah'  OR d.kdspk='$kdwilayah') AND nobukti NOT LIKE '01%'";
            
            $date_between_now = $batasan_tahun_new.'-'.$batasan_bulan_new.'-01';
            $date_between_last_date = date('Y-m-t', strtotime($date_between_now));
            //print_r($test);
				$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur LEFT JOIN dspk d ON (pjur.kdspk=d.kdspk  AND d.kddiv=pjur.kddivisi)
									 WHERE true
												AND 
												--	(
												--		DATE_PART('YEAR',pjur.tanggal) = '$batasan_tahun_new' 
												--		AND DATE_PART('MONTH',pjur.tanggal) = '$batasan_bulan_new'
												--	)
												pjur.tanggal BETWEEN '$date_between_now'::date AND   '$date_between_last_date'::DATE
												AND isdel = 'f' 
												-- AND isapp='t'
												{$sql_and_nobukti}
												AND (pjur.kdperkiraan similar to '(4|5)%' 
												--OR pjur.kdperkiraan='32211'  
												OR pjur.kdperkiraan='32212'
												)
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";

						//echo 'RL::: '.$sql3;
						//buat dibawah
						$lr = $base->db->getOne($sql3);
						$nama_lr = "Laba Rugi Bulan Ini_";
						//die($sql3);
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
        //luki: cari dulu siapa tau coa 32211 sudah ada
        $found  = false;
        foreach ($test['coa'] as $k=>$v)
        {
          if ($v == '32211')
          {
            $found  = true;
            $test['debet'][$k] += $neraca_debet;
            $test['kredit'][$k] += $neraca_kredit;
            $test['debet_laba'][$k]+=$lr_debet;
            $test['kredit_laba'][$k] +=$lr_kredit;
            //$test['coa'][] = '32211';
            break;
          }
        }
        //
        if ($found == false)
        {
			    $test['debet'][] = $neraca_debet;
    			$test['kredit'][] = $neraca_kredit;
				$test['debet_laba'][]=$lr_debet;
				$test['kredit_laba'][] =$lr_kredit;
			    $test['coa'][] = '32211';
        }
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
					  	'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  			$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'	=> $divname,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     	=> MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
						'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
						'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
						'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
						'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
						'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
						'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
						'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
						'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
						'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
						
				
				
		}	
			$sql_insert = "INSERT INTO z_temp_neraca_t_new (kddivisi,tanggal,group_name,rupiah_debit,rupiah_kredit,rupiah_laba_debit,rupiah_laba_kredit,tanggal_create,kodewilayah) VALUES ";
            for($i = 0; $i < count($test['debet']);$i++)
            {
                $sql_insert .= "('{$kddiv}','{$ryear}-".((strlen($rmonth)==1)?"0".$rmonth:$rmonth)."-01','".$test['coa'][$i]."','".(($test['debet'][$i]=="")?0:$test['debet'][$i])."','".(($test['kredit'][$i]=="")?0:$test['kredit'][$i])."','".
                (($test['debet_laba'][$i]=="")?0:$test['debet_laba'][$i])."','".(($test['kredit_laba'][$i]=="")?0:$test['kredit_laba'][$i])."','".date("Y-m-d")."','$kdwilayah')";
                if(($i+1) <> count($test['debet'])) 
                {
                    $sql_insert .=",";
                }
                else
                {
                    $sql_insert .=";";
                }
            }
            //echo $sql_insert;
            //$base->db->BeginTrans();
    		$sql_delete="DELETE FROM z_temp_neraca_t_new WHERE kddivisi = '{$kddiv}' and tanggal = '{$ryear}-".((strlen($rmonth)==1)?'0'.$rmonth:$rmonth)."-01' AND kodewilayah='$kdwilayah';";
		
			$base->db->Execute($sql_delete);
			$okS = true;
			$okS = $base->db->Execute($sql_insert);
			if(!$okS)
			{
				$pesan = $base->db->ErrorMsg();
				$pesan = str_replace('"','',$pesan);
				$pesan = trim($pesan);
                die($pesan);
				break;
			}

      //testing
     // $base->db->commitTrans();exit;
			//kodokkodok&tutuppop
			//$tutuppop = $this->get_var('tutuppop','none');
      //echo "<pre>";print_r($test);echo "$sql_anto";
      //die('<h1>TESTAH</h1>');
			
			$tutuppop = $this->get_var('tutuppop','');
		
			if($tutuppop == 'yes')
			{
				echo '
					<script>	
					window.opener.location.reload();			
					window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes2')
			{
				echo '
					<script>				
					 window.close();					
					</script>
				';
			}
			elseif($tutuppop == 'yes3')
			{
				echo '
					<script>			
          alert("Silahkan klik tombol: Membuat Laporan");
					 window.opener.close();					
					 window.close();					
					</script>
				';
			}

			/*if($okS)
    		{
    			$base->db->commitTrans();
    		}
    		else
    		{
    			$base->db->rollBackTrans();	
    		}
            */
            //print_r($test);
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
		//		die($nm_bulan_);

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
        
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
      $fadd = '';
      if ($kdwilayah != '{KDWILAYAH}' && $kdwilayah != '')
      {
        $fadd = "_" . $kdwilayah;
      }
            $filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
			$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_".$txt_konsolidasi.$ryear."_".$rmonth.$fadd."_for_excel.html";
			$isi2 = & $tpl_temp_excel->parsedPage();
			$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
	} /*}}}*/	
    
    
    function _spk($base) /*{{{*/
	{
		
	}/*}}}*/
    function sub_report_neraca_lajur_divisi_t($base)/*{{{*/
    {
        return $this->sub_report_neraca_t($base);
    } /*}}}*/
	
	function sub_report_neraca_lajur_divisi_t_OLD($base) /*{{{*/
	{
		//$base->db->debug = true;
		//die ('neraca lajur divisi t');
		return $this->sub_report_neraca_t($base);
        loadclass('dateparse');
			
		$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		//$ryear = $this->get_var('ryear',date('Y'));

		$tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
		//$tpl->defineDynamicBlock('row');
		
		$tpl1 = & $tpl->defineDynamicBlock('row');
		$tpl2 = & $tpl1->defineDynamicBlock('row1');
		$tpl3 = & $tpl1->defineDynamicBlock('row2');
		
   	    $tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ===== FOR EXCEL					
			$tpl_excel = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
			
			$tpl1_excel = & $tpl_excel->defineDynamicBlock('row');
			$tpl2_excel = & $tpl1_excel->defineDynamicBlock('row1');
			$tpl3_excel = & $tpl1_excel->defineDynamicBlock('row2');
    	
			$tpl_temp_excel = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl_excel);		
		// ===== 
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		//----- membuat rumusan no perkiraan mana saja yang akan di tampilkan dalam query

		//sql_perk digunakan untuk penampung filter no_perk yang dicari(kondisi is_need = 't')
		$sql_perk = ''; 
  		//sql_perk_min digunakan untuk penampung filter no_perk yang akan diabaikan (kondisi is_need = 'f')
		$sql_perk_min = '';
		
		//$tglawal_per1 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear-1));
		//$tglawal_per2 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear));
		
		$tglawal_per1 = $ryear - 1 . '-01-01';
		$tglawal_per2 = $ryear.'-01-01';
		
		//die ($tglawal_per1.'  '.$tglawal_per2);
				
		$tglakhir_per1 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1));
		$contoh_3 = $thn_.'-12-31';
		$periode1 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1));
		$tglakhir_per2 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear)); // 2005-12-31
		
		$tgl_ini = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear)); //	 > < $tgl2	
		
		$tgl1 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1)); // periode 1
		$tgl2 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear)); // periode 2

		$tglawal1 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear-1)); // tgl awal periode 1 alias tahun lalu		(2004-11-30)
		$tglawal2 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear)); // tgl awal periode 2 alias tahun sekarang (2005-11-30)

		//$tgl1_periode1 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear-1)); // tanggal satu periode 1 (2004-11-30)
		//$tgl1_periode2 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear)); // tanggal satu periode 1 (2005-11-30)
			
		$spasi3 = '&nbsp;&nbsp;&nbsp;';	
		$spasi5 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$spasi10 = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		/*		$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			(SELECT jur.kdperkiraan,d.nmperkiraan,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			FROM $table jur,dperkir d
			WHERE jur.kdperkiraan=d.kdperkiraan AND
						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						AND isdel = 'f'
			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk
			)
			UNION ALL (SELECT '32211' as kdperkiraan
      , 'LABA TAHUN BERJALAN' as nmperkiraan
      , 0 as rupiah_debet
      , 0 as rupiah_kredit )) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		die($sql1);*/


		/*		
		$sql_a = "SELECT to_char(date(a.tanggal),'YYYY-MM-DD') as tanggal, a.kdperkiraan, a.dk, a.rupiah
							FROM {$table} a
							WHERE substr(a.kdperkiraan,1,1) <= 3
										AND (
												(DATE(a.tanggal) >= '$tglawal_per1' AND DATE(a.tanggal) <= '$tglakhir_per1')
												OR
												(DATE(a.tanggal) >= '$tglawal_per2' AND DATE(a.tanggal) <= '$tglakhir_per2')
												)
							ORDER BY a.kdperkiraan";*/
		
		$sql_a = "SELECT to_char(date(a.tanggal),'YYYY-MM-DD') as tanggal, a.kdperkiraan, a.dk, COALESCE(a.rupiah,0) as rupiah
							FROM {$table} a
							WHERE substr(a.kdperkiraan,1,1)::int <= 3
										AND DATE(a.tanggal) <= '$tgl2'
							ORDER BY a.kdperkiraan";						
    	//die($sql_a);
    	$rs_a = $base->dbquery($sql_a);
		
		if (!$rs_a->EOF)
		{
			$tpl->defineDynamicBlock('row');
			$tpl->Assign(array(
				'PERIODE' 	=> $tglakhir_per2,
				'PERIODE1' 	=> $tglakhir_per1,
				'PERIODE2' 	=> $tglakhir_per2,
				'DIVNAME'		=> $divname,
				'KDSPK'			=> '',
				'NMSPK'			=> '',
				'SID'    		=> MYSID,
			));
			// =========== FOR EXCEL 
			$tpl_excel->Assign(array(
				'PERIODE' 	=> $tglakhir_per2,
				'PERIODE1' 	=> $tglakhir_per1,
				'PERIODE2' 	=> $tglakhir_per2,
				'DIVNAME'		=> $divname,
				'KDSPK'			=> '',
				'NMSPK'			=> '',
				'SID'     	=> MYSID,
			));
			// =====================
            
            $rs_a->moveNext();
		}
		
		$posisi = 1;
		while ($posisi <= 41)
		{
			if ($posisi == 1)
			{
				$giliran = 'Kas';
				$kdgil = '11011';
				
				$jatah = 'UTANG USAHA';
				$kdjat = '21031';
			}
			else if ($posisi == 2)
			{
				$giliran = 'Bank Pemerintah';
				$kdgil = '1101211';
				
				$jatah = 'UTANG PAJAK';
			}
			else if ($posisi == 3)
			{
				$giliran = 'Bank Swasta';
				$kdgil = '1101212';
				
				$jatah = 'POTONGAN PEGAWAI';
				$kdjat = '21121';
			}
			else if ($posisi == 4)
			{
				$giliran = 'Uang Dalam Pengiriman';
				$kdgil = '11013';

				$jatah = 'UTANG LAIN-LAIN';
				$kdjat = '21131';
			}
			else if ($posisi == 5)
			{
				$giliran = 'SURAT BERHARGA';
				$kdgil = '11014';

				$jatah = 'KEWAJIBAN BRUTO PEMBERI KERJA';
				$kdjat = '21041';
			}
			else if ($posisi == 6)
			{
				$giliran = 'Piutang Usaha';
				$kdgil = '11041';
				
				$jatah = 'PYD PENJUALAN';
				$kdjat = '2161'; //tidak ada	
			}
			else if ($posisi == 7)
			{
				$giliran = 'Ak. Cad. Piutang Ragu-2';
				$kdgil = '1104119'; 
				
				$jatah = 'PYD FAKTUR';
				$kdjat = '2162'; //tidak ada	
			}
			else if ($posisi == 8)
			{
				$giliran = 'PIUTANG PERUSAHAAN AFILIASI';
				$kdgil = '11043';
				
				$jatah = 'PINJAMAN JK.PENDEK - HUTANG BANK';
				$kdjat = '21011';	//tidak ada			
			}
			else if ($posisi == 9)
			{
				$giliran = 'PIUTANG PAJAK';
				//$kdgil = '1125';

				$jatah = 'UANG MUKA DITERIMA';
				$kdjat = '21061';
			}
			else if ($posisi == 10)
			{
				$giliran = 'PIUTANG PEGAWAI';
				$kdgil = '1106111';
				
				$jatah = 'BIAYA AKAN DIBAYAR';
				$kdjat = '21081';
			}
			else if ($posisi == 11)
			{
				$giliran = 'PIUTANG LAIN-LAIN';
				$kdgil = '11061';

				$jatah = 'CADANGAN BY. PEMELIHARAAN';
				$kdjat = '21082';
			}
			else if ($posisi == 12)
			{
				$giliran = 'PYD. PENJUALAN';
				$kdgil = '2161'; //tidak ada

				$jatah = 'HASIL DITERIMA DIMUKA';
				$kdjat = '215'; //tidak ada
			}
			else if ($posisi == 13)
			{
				$giliran = 'PYD. FAKTUR';
				$kdgil = '2162';//tidak ada

				$jatah = 'HUBUNGAN REKENING KORAN';
				$kdjat = '217';//tidak ada
			}
			else if ($posisi == 14)
			{
				$giliran = 'TAGIHAN BRUTO PEMBERI KERJA';
				$kdgil = '11091';

				$jatah = 'UTANG INVESTASI';
				$kdjat = '221';
			}
			else if ($posisi == 15)
			{
				$giliran = 'UANG MUKA DIBERIKAN';
				$kdgil = '113';

				$jatah = 'CAD. IMBALAN KERJA';
				$kdjat = '222';
			}			
			else if ($posisi == 16)
			{
				$giliran = 'PERSEDIAAN';
				$kdgil = '114';

				$jatah = 'KEWAJIBAN PAJAK TANGGUHAN';
				$kdjat = '223';
			}
			else if ($posisi == 17)
			{
				$giliran = 'BIAYA DIBAYAR DIMUKA';
				$kdgil = '115';

				$jatah = 'MODAL DASAR';
				$kdjat = '31111';
			}
			else if ($posisi == 18)
			{
				$giliran = 'HASIL AKAN DITERIMA';
				$kdgil = '116';

				$jatah = 'SAHAM DALAM PORTEPEL';
				$kdjat = '31121';
			}
			else if ($posisi == 19)
			{
				$giliran = 'PDP KONSTRUKSI';
				$kdgil = '117';

				$jatah = 'MODAL DISETOR';
				$kdjat = '31131';
			}	
			else if ($posisi == 20)
			{
				$giliran = 'HUBUNGAN REKENING KORAN';
				$kdgil = '217';

				$jatah = 'MODAL DITEMPATKAN DI DIVISI';
				$kdjat = '31132';
			}
			else if ($posisi == 21)
			{
				$giliran = 'PENYERTAAN';
				$kdgil = '121';

				$jatah = 'MODAL DISETOR LAINNYA';
				$kdjat = '31139';
			}	
			else if ($posisi == 22)
			{
				$giliran = 'AKTIVA PAJAK TANGGUHAN';
				$kdgil = '122';

				$jatah = 'SEL. PENIL. KEMBALI AKT TETAP';
				$kdjat = '31211';
			}	
			else if ($posisi == 23)
			{
				$giliran = 'TANAH';
				$kdgil = '13111';

				$jatah = 'SEL. TRANS. RESTRUK. ENTITAS';
				$kdjat = '31311';
			}	
			else if ($posisi == 24)
			{
				$giliran = 'PRASARANA';
				$kdgil = '13121';

				$jatah = 'SEL. PERUB EKUI PERUS. AFILIASI';
				$kdjat = '31312';
			}	
			else if ($posisi == 25)
			{
				$giliran = 'Akumulasi Penyusutan Prasarana';
				$kdgil = '13132';

				$jatah = 'AKU. SEL KURS KRN PENJAB LAPKEU';
				$kdjat = '31411';
			}
			else if ($posisi == 26)
			{
				$giliran = 'BANGUNAN';
				$kdgil = '13131';
				  
				$jatah = 'AKU. PENY. NILAI WAJAR INVEST.';
				$kdjat = '31511';
			}
			else if ($posisi == 27)
			{
				$giliran = 'Akumulasi Penyusutan Bangunan';
				$kdgil = '13132';

				$jatah = 'SISA LABA TAHUN LALU';
				$kdjat = '32111';
			}
			else if ($posisi == 28)
			{
				$giliran = 'PERLENGKAPAN KANTOR';
				$kdgil = '13141';

				$jatah = 'CADANGAN UMUM';
				$kdjat = '32112';
			}
			else if ($posisi == 29)
			{
				$giliran = 'Akumulasi Penyusutan Perl Kantor';
				$kdgil = '13142';

				$jatah = 'LABA SAMPAI DENGAN BULAN LALU';
				$kdjat = '32211';
			}
			else if ($posisi == 30)
			{
				$giliran = 'KENDARAAN';
				$kdgil = '13151';
				
				$jatah = 'LABA BULAN INI';
				$kdjat = '';
			}	
			else if ($posisi == 31)
			{
				$giliran = 'Akumulasi Penyusutan Kendaraan';
				$kdgil = '13152';

				$jatah = 'PASIVA BERES';
				$kdjat = 'hehehehehehehe';
			}
			else if ($posisi == 32)
			{
				$giliran = 'PERALATAN';
				$kdgil = '13161';
			}
			else if ($posisi == 33)
			{
				$giliran = 'Akumulasi Penyusutan Peralatan';
				$kdgil = '13162';
			}
			else if ($posisi == 34)
			{
				$giliran = 'HAK SEWA';
				$kdgil = '13211';
			}
			else if ($posisi == 35)
			{
				$giliran = 'Amortisasi Hak Sewa';
				$kdgil = '13212';
			}
						else if ($posisi == 36)
			{
				$giliran = 'LEASING';
				$kdgil = '13221';
			}
						else if ($posisi == 37)
			{
				$giliran = 'Amortisasi Leasing';
				$kdgil = '13222';
			}
			else if ($posisi == 38)
			{
				$giliran = 'JAMINAN';
				$kdgil = '1411';
			}
			else if ($posisi == 39)
			{
				$giliran = 'INVESTASI DALAM PELAKSANAAN';
				$kdgil = '1412';
			}
			else if ($posisi == 40)
			{
				$giliran = 'BEBAN DITANGGUHKAN';
				$kdgil = '1413';
			}
			else if ($posisi == 41)
			{
				$giliran = 'AKTIVALAIN-LAIN';
				$kdgil = '1414';
			}


			if ($giliran == 'Kas') //01
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA LANCAR',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> '111'.$spasi5.'KAS & BANK',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');		
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.$spasi3.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');

				$sum_kasbank_akt1 += $neraca_akt['neraca1'];
				$sum_kasbank_akt2 += $neraca_akt['neraca2'];												
			} // end of if giliran
			
			
			if ($jatah == 'UTANG USAHA') //pasiva 01
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'KEWAJIBAN SEGERA',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> '211'.$spasi5.'UTANG',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');		
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utus_pas1 = $neraca_pas['neraca1'];
				$sum_utus_pas2 = $neraca_pas['neraca2'];				
			} // end of jatah
			
			if ($giliran == 'Bank Pemerintah') //02
			{				
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');	

				$sum_kasbank_akt1 += $neraca_akt['neraca1'];
				$sum_kasbank_akt2 += $neraca_akt['neraca2'];											
			} // end of if giliran

			if ($jatah == 'UTANG PAJAK') //pasiva 02
			{
				$debet1 = 0; $kredit1 = 0; $debet2 = 0; $kredit2 = 0; $m_debet1 = 0; $m_kredit1 = 0; $m_debet2 = 0; $m_kredit2 = 0;
				
				$rs_a->moveFirst();				
				while (!$rs_a->EOF)
				{
					$kdper = $rs_a->fields['kdperkiraan'];
					
					if ((substr($kdper,0,4) == '2112') or (substr($kdper,0,4) == '2113'))
					{
						if ((date($rs_a->fields['tanggal']) > date($tglawal1)) && (date($rs_a->fields['tanggal']) <= date($tgl1)))
						{				
							if ($rs_a->fields['dk'] == 'D')
								$m_debet1 += $rs_a->fields['rupiah'];
							else
								$m_kredit1 += $rs_a->fields['rupiah'];
						}
						
						if ((date($rs_a->fields['tanggal']) > date($tglawal2)) && (date($rs_a->fields['tanggal']) <= date($tgl2))) //2005-11-30  
						{					
							if ($rs_a->fields['dk'] == 'D')
								$m_debet2 += $rs_a->fields['rupiah'];
							else
								$m_kredit2 += $rs_a->fields['rupiah'];					
						}

						if (date($rs_a->fields['tanggal']) <= date($tglawal1))
						{
							if ($rs_a->fields['dk'] == 'D')
								$debet1 += $rs_a->fields['rupiah'];
							else
								$kredit1 += $rs_a->fields['rupiah'];
						}
						
						if (date($rs_a->fields['tanggal']) <= date($tglawal2))
						{
							if ($rs_a->fields['dk'] == 'D')
								$debet2 += $rs_a->fields['rupiah'];
							else
								$kredit2 += $rs_a->fields['rupiah'];
						}
					}
			
					$rs_a->moveNext();
					if ($rs_a->EOF)
					{
					//print ('debet 2 = '.$debet2.' kredit 2 = '.$kredit2.' ---');
										
						if (($debet1 - $kredit1) >= 0) //
							$awal_d = ($debet1 - $kredit1);
						else
							$awal_k = ($debet1 - $kredit1) * -1;
							
						$percobaan_d = $awal_d + $m_debet1;
						$percobaan_k = $awal_k + $m_kredit1;
								
						$sum_utpaj_pas1 = ($percobaan_d - $percobaan_k) * -1;
						
						if (($debet2 - $kredit2) >= 0)
							$awal_d = ($debet2 - $kredit2);
						else
							$awal_k = ($debet2 - $kredit2) * -1;
							
						$percobaan_d = $awal_d + $m_debet2;
						$percobaan_k = $awal_k + $m_kredit2;
								
						$sum_utpaj_pas2 = ($percobaan_d - $percobaan_k) * -1;
						
						//print ($sum_utpaj_pas2.' huh.....');	
															
						$tpl1->assignDynamic('row2', array(
							'KODE_P'		=> '2112-3'.$spasi5.$jatah,
							'VPP1'  		=> $this->format_money2($base, $sum_utpaj_pas1),
							'VPP2'			=> $this->format_money2($base, $sum_utpaj_pas2),
						));			  
						$tpl1->parseConcatDynamic('row2');					
					}
				} // end of while rs_a
			} // end of if giliran


			if ($giliran == 'Bank Swasta') //03
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');
				
				$sum_kasbank_akt1 += $neraca_akt['neraca1'];
				$sum_kasbank_akt2 += $neraca_akt['neraca2'];																	
			} // end of if giliran

			if ($jatah == 'POTONGAN PEGAWAI') //pasiva 03
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_potpeg_pas1 = $neraca_pas['neraca1'];
				$sum_potpeg_pas2 = $neraca_pas['neraca2'];
			} // end of jatah

			if ($giliran == 'Uang Dalam Pengiriman') //04
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');
				
				$sum_kasbank_akt1 += $neraca_akt['neraca1'];
				$sum_kasbank_akt2 += $neraca_akt['neraca2'];																	
										
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Kas Bank',
					'VAP1'  		=> $this->format_money2($base, $sum_kasbank_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_kasbank_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');										
			} // end of if giliran

			if ($jatah == 'UTANG LAIN-LAIN') //pasiva 04
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utanglain_pas1 = $neraca_pas['neraca1'];
				$sum_utanglain_pas2 = $neraca_pas['neraca2'];
			} // end of jatah

			if ($giliran == 'SURAT BERHARGA') //05
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_surber_akt1 = $neraca_akt['neraca1'];
				$sum_surber_akt2 = $neraca_akt['neraca2'];						
			} // end of if giliran

			if ($jatah == 'KEWAJIBAN BRUTO PEMBERI KERJA') //pasiva 05
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_kbpk_pas1 = $neraca_pas['neraca1'];
				$sum_kbpk_pas2 = $neraca_pas['neraca2'];
			} // end of jatah


			if ($giliran == 'Piutang Usaha') //06
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> '112'.$spasi5.'PIUTANG USAHA',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutus_akt1 += $neraca_akt['neraca1'];
				$sum_piutus_akt2 += $neraca_akt['neraca2'];						
			} // end of if giliran

			if ($jatah == 'PYD PENJUALAN') //pasiva 06
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.'UTANG PRESTASI',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');	

				if ($neraca_pas['neraca1'] > 0)
					$pydjual1 = $neraca_pas['neraca1'];
				else
					$pydjual1 = 0;

				if ($neraca_pas['neraca2'] > 0)
					$pydjual2 = $neraca_pas['neraca2'];
				else
					$pydjual2 = 0;				
			
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $pydjual1),
					'VPP2'			=> $this->format_money2($base, $pydjual2),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utpres_pas1 += $pydjual1;
				$sum_utpres_pas2 += $pydjual2;
			} // end of jatah

			if ($giliran == 'Ak. Cad. Piutang Ragu-2') //07
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutus_akt1 += $neraca_akt['neraca1'];
				$sum_piutus_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Piutang Usaha',
					'VAP1'  		=> $this->format_money2($base, $sum_piutus_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_piutus_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');														
			} // end of if giliran

			if ($jatah == 'PYD FAKTUR') //pasiva 07
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);

				if ($neraca_pas['neraca1'] > 0)
					$pydfak1 = $neraca_pas['neraca1'];
				else
					$pydfak1 = 0;

				if ($neraca_pas['neraca2'] > 0)
					$pydfak2 = $neraca_pas['neraca2'];
				else
					$pydfak2 = 0;	
		/*
				$pydfaktur1 = str_replace('-','',$pydfak1);	
				$pydfaktur2 = str_replace('-','',$pydfak2);
				
				$pydfaktur1 = abs($pydfak1);	
				$pydfaktur2 = abs($pydfak1);
		*/
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $pydfak1),
					'VPP2'			=> $this->format_money2($base, $pydfak2),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utpres_pas1 += $pydfak1;
				$sum_utpres_pas2 += $pydfak2;
				
				//$sum_prestasipasiva1 = $sum_pydjual1 + $sum_pydfaktur1;
				//$sum_prestasipasiva2 = $sum_pydjual2 + $sum_pydfaktur2;
					
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.'Jumlah Utang Prestasi',
					'VPP1'  		=> $this->format_money2($base, $sum_utpres_pas1),
					'VPP2'			=> $this->format_money2($base, $sum_utpres_pas2),
				));			  
				$tpl1->parseConcatDynamic('row2');
			} // end of jatah

			if ($giliran == 'PIUTANG PERUSAHAAN AFILIASI') //08
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutperaf_akt1 = $neraca_akt['neraca1'];
				$sum_piutperaf_akt2 = $neraca_akt['neraca2'];						
			} // end of if giliran

			if ($jatah == 'UTANG MODAL KERJA') //pasiva 08
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utmodker_pas1 = $neraca_pas['neraca1'];
				$sum_utmodker_pas2 = $neraca_pas['neraca2'];
			} // end of jatah

			if ($giliran == 'PIUTANG PAJAK') //09
			{
				$debet1 = 0; $kredit1 = 0; $debet2 = 0; $kredit2 = 0; $m_debet1 = 0; $m_kredit1 = 0; $m_debet2 = 0; $m_kredit2 = 0;
				
				$rs_a->moveFirst();
				
				while (!$rs_a->EOF)
				{
					$kdper = $rs_a->fields['kdperkiraan'];
					
					if ((substr($kdper,0,4) == '1123') or (substr($kdper,0,4) == '1124'))
					{
						if ((date($rs_a->fields['tanggal']) > date($tglawal1)) && (date($rs_a->fields['tanggal']) <= date($tgl1)))
						{				
							if ($rs_a->fields['dk'] == 'D')
								$m_debet1 += $rs_a->fields['rupiah'];
							else
								$m_kredit1 += $rs_a->fields['rupiah'];
						}
						
						if ((date($rs_a->fields['tanggal']) > date($tglawal2)) && (date($rs_a->fields['tanggal']) <= date($tgl2))) //2005-11-30  
						{					
							if ($rs_a->fields['dk'] == 'D')
								$m_debet2 += $rs_a->fields['rupiah'];
							else
								$m_kredit2 += $rs_a->fields['rupiah'];					
						}
						
						if (date($rs_a->fields['tanggal']) <= date($tglawal1))
						{
							if ($rs_a->fields['dk'] == 'D')
								$debet1 += $rs_a->fields['rupiah'];
							else
								$kredit1 += $rs_a->fields['rupiah'];
						}
						if (date($rs_a->fields['tanggal']) <= date($tglawal2))
						{
							if ($rs_a->fields['dk'] == 'D')
								$debet2 += $rs_a->fields['rupiah'];
							else
								$kredit2 += $rs_a->fields['rupiah'];
						}
					}
			
					$rs_a->moveNext();
					if ($rs_a->EOF)
					{
						$awal_d = 0;
						$awal_k = 0;
						
		/*print ('debet mutasi = '.$m_debet2.'<br>');
		print ('debet kredit = '.$m_kredit2.'<br>');
		die();*/
					
						if (($debet1 - $kredit1) >= 0)
							$awal_d = $debet1 - $kredit1;
						else
							$awal_k = ($debet1 - $kredit1) * -1;
							
						$percobaan_d = $awal_d + $m_debet1;
						$percobaan_k = $awal_k + $m_kredit1;
								
						$sum_piutpaj_akt1 = $percobaan_d - $percobaan_k;
						
						if (($debet2 - $kredit2) >= 0)
							$awal_d = $debet2 - $kredit2;
						else
							$awal_k = ($debet2 - $kredit2) * -1;
							
						$percobaan_d = $awal_d + $m_debet2;
						$percobaan_k = $awal_k + $m_kredit2;
								
						$sum_piutpaj_akt2 = $percobaan_d - $percobaan_k;	
															
						$tpl1->assignDynamic('row1', array(
							'KODE_A'		=> '1123-4'.$spasi5.$giliran,
							'VAP1'  		=> $this->format_money2($base, $sum_piutpaj_akt1),
							'VAP2'			=> $this->format_money2($base, $sum_piutpaj_akt2),
						));			  
						$tpl1->parseConcatDynamic('row1');					
					}
				} // end of while rs_a
				$rs_a->moveFirst();
			} // end of if giliran

			if ($jatah == 'UANG MUKA DITERIMA') //pasiva 09
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_umdit_pas1 = $neraca_pas['neraca1'];
				$sum_umdit_pas2 = $neraca_pas['neraca2'];
			} // end of jatah

			if ($giliran == 'PIUTANG PEGAWAI') //10
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutpeg_akt1 = $neraca_akt['neraca1'];
				$sum_piutpeg_akt2 = $neraca_akt['neraca2'];	
			} // end of if giliran

			if ($jatah == 'BIAYA AKAN DIBAYAR') //pasiva 10
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.'BIAYA AKAN DIBAYAR',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');
								
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_biakdib_pas1 += $neraca_pas['neraca1'];
				$sum_biakdib_pas2 += $neraca_pas['neraca2'];
			} // end of jatah

			if ($giliran == 'PIUTANG LAIN-LAIN') //11
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutlain_akt1 = $neraca_akt['neraca1'];
				$sum_piutlain_akt2 = $neraca_akt['neraca2'];				
			} // end of if giliran

			if ($jatah == 'CADANGAN BY. PEMELIHARAAN') //pasiva 11
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
								
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_biakdib_pas1 += $neraca_pas['neraca1'];
				$sum_biakdib_pas2 += $neraca_pas['neraca2'];

				//$sum_biayaakandibayar1 = $sum_badb1 + $sum_cbp1;
				//$sum_biayaakandibayar2 = $sum_badb2 + $sum_cbp2;
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.'Jumlah Biaya Akan Di Bayar',
					'VPP1'  		=> $this->format_money2($base, $sum_biakdib_pas1),
					'VPP2'			=> $this->format_money2($base, $sum_biakdib_pas2),
				));			  
				$tpl1->parseConcatDynamic('row2');				
			} // end of jatah
			
			if ($giliran == 'PYD. PENJUALAN') //12
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> '216'.$spasi5.'PIUTANG PRESTASI',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');	
				
				if ($neraca_akt['neraca1'] < 0)
					$pyd1 = 0;
				else
					$pyd1 = $neraca_akt['neraca1'];

				if ($neraca_akt['neraca2'] < 0)
					$pyd2 = 0;
				else
					$pyd2 = $neraca_akt['neraca2'];
																
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $pyd1),
					'VAP2'			=> $this->format_money2($base, $pyd2),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutpres_akt1 += $pyd1;
				$sum_piutpres_akt2 += $pyd2;	
			} // end of if giliran

			if ($jatah == 'HASIL DITERIMA DIMUKA') //pasiva 12
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);
								
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_hasditdim_pas1 = $neraca_pas['neraca1'];
				$sum_hasditdim_pas2 = $neraca_pas['neraca2'];			
			} // end of jatah
			
			if ($giliran == 'PYD. FAKTUR') //13
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);

				if ($neraca_akt['neraca1'] > 0)
					$pydtur1 = $neraca_akt['neraca1'];
				else
					$pydtur1 = 0;

				if ($neraca_akt['neraca2'] > 0)
					$pydtur2 = $neraca_akt['neraca2'];
				else
					$pydtur2 = 0;
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $pydtur1),
					'VAP2'			=> $this->format_money2($base, $pydtur2),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_piutpres_akt1 += $pydtur1;
				$sum_piutpres_akt2 += $pydtur2;
										
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Piutang Prestasi',
					'VAP1'  		=> $this->format_money2($base, $sum_piutpres_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_piutpres_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');											
			} // end of if giliran

			if ($jatah == 'HUBUNGAN REKENING KORAN') //pasiva 13
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);

				if ($neraca_pas['neraca1'] > 0)
					$hrkpas1 = $neraca_pas['neraca1'];
				else
					$hrkpas1 = 0;

				if ($neraca_pas['neraca2'] > 0)
					$hrkpas2 = $neraca_pas['neraca2'];
				else
					$hrkpas2 = 0;
																
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $hrkpas1),
					'VPP2'			=> $this->format_money2($base, $hrkpas2),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_hubrekor_pas1 = $hrkpas1;
				$sum_hubrekor_pas2 = $hrkpas2;
				
				$sum_kewajiban_segera1 = $sum_utus_pas1 + $sum_utpaj_pas1 + $sum_potpeg_pas1 + $sum_utanglain_pas1 + $sum_kbpk_pas1 + $sum_utpres_pas1 + $sum_utmodker_pas1 + $sum_umdit_pas1 + $sum_biakdib_pas1 + $sum_hasditdim_pas1 + $sum_hubrekor_pas1;				
				
				$sum_kewajiban_segera2 = $sum_utus_pas2 + $sum_utpaj_pas2 + $sum_potpeg_pas2 + $sum_utanglain_pas2 + $sum_kbpk_pas2 + $sum_utpres_pas2 + $sum_utmodker_pas2 + $sum_umdit_pas2 + $sum_biakdib_pas2 + $sum_hasditdim_pas2 + $sum_hubrekor_pas2;
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'Jumlah Kewajiban Segera',
					'VPP1'  		=> $this->format_money2($base, $sum_kewajiban_segera1),
					'VPP2'			=> $this->format_money2($base, $sum_kewajiban_segera2),
				));			  
				$tpl1->parseConcatDynamic('row2');							
			} // end of jatah

			
			if ($giliran == 'TAGIHAN BRUTO PEMBERI KERJA') //14
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_tagbrupemker_akt1 = $neraca_akt['neraca1'];
				$sum_tagbrupemker_akt2 = $neraca_akt['neraca2'];											
			} // end of if giliran

			if ($jatah == 'UTANG INVESTASI') //pasiva 14
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'KEWAJIBAN TIDAK SEGERA',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_utin_pas1 = $neraca_pas['neraca1'];
				$sum_utin_pas2 = $neraca_pas['neraca2'];						
			} // end of jatah

			if ($giliran == 'UANG MUKA DIBERIKAN') //15
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_uangmukdib_akt1 = $neraca_akt['neraca1'];
				$sum_uangmukdib_akt2 = $neraca_akt['neraca2'];											
			} // end of if giliran

			if ($jatah == 'CAD IMBALAN KERJA') //pasiva 15
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_cadik_pas1 = $neraca_pas['neraca1'];
				$sum_cadik_pas2 = $neraca_pas['neraca2'];						
			} // end of jatah

			if ($giliran == 'PERSEDIAAN') //16
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_persediaan_akt1 = $neraca_akt['neraca1'];
				$sum_persediaan_akt2 = $neraca_akt['neraca2'];											
			} // end of if giliran

			if ($jatah == 'KEWAJIBAN PAJAK TANGGUHAN') //pasiva 16
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_kepatang_pas1 = $neraca_pas['neraca1'];
				$sum_kepatang_pas2 = $neraca_pas['neraca2'];
				
				$sum_kewajiban_tidak_segera1 = $sum_utin_pas1 + $sum_cadik_pas1 + $sum_kepatang_pas1;	
				$sum_kewajiban_tidak_segera2 = $sum_utin_pas2 + $sum_cadik_pas2 + $sum_kepatang_pas2;	

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'Jumlah Kewajiban Tidak Segera',
					'VPP1'  		=> $this->format_money2($base, $sum_kewajiban_tidak_segera1),
					'VPP2'			=> $this->format_money2($base, $sum_kewajiban_tidak_segera2),
				));			  
				$tpl1->parseConcatDynamic('row2');				
			} // end of jatah

			if ($giliran == 'BIAYA DIBAYAR DIMUKA') //17
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_biadibdim_akt1 = $neraca_akt['neraca1'];
				$sum_biadibdim_akt2 = $neraca_akt['neraca2'];											
			} // end of if giliran

			if ($jatah == 'MODAL DASAR') //pasiva 17
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_modas_pas1 = $neraca_pas['neraca1'];
				$sum_modas_pas2 = $neraca_pas['neraca2'];						
			} // end of jatah

 			if ($giliran == 'HASIL AKAN DITERIMA') //18
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_hasakdit_akt1 = $neraca_akt['neraca1'];
				$sum_hasakdit_akt2 = $neraca_akt['neraca2'];
			} // end of if giliran

			if ($jatah == 'SAHAM DALAM PORTEPEL') //pasiva 18
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_sahdapor_pas1 = $neraca_pas['neraca1'];
				$sum_sahdapor_pas2 = $neraca_pas['neraca2'];						
			} // end of jatah

			
 			if ($giliran == 'PDP KONSTRUKSI') //19
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_pdpkon_akt1 = $neraca_akt['neraca1'];
				$sum_pdpkon_akt2 = $neraca_akt['neraca2'];
			} // end of if giliran

			if ($jatah == 'MODAL DI SETOR') //pasiva 19
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_modis_pas1 = $neraca_pas['neraca1'];
				$sum_modis_pas2 = $neraca_pas['neraca2'];
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> '',
					'VPP1'  		=> $sum_modas_pas1 + $sum_sahdapor_pas1 + $sum_modis_pas1,
					'VPP2'			=> $sum_modas_pas2 + $sum_sahdapor_pas2 + $sum_modis_pas2,
				));			  
				$tpl1->parseConcatDynamic('row2');										
			} // end of jatah	

 			if ($giliran == 'HUBUNGAN REKENING KORAN') //20
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
				
				//print ($neraca_akt['neraca1'].' -- ');
				//print ($neraca_akt['neraca2']);
				
				if ($neraca_akt['neraca1'] > 0)
					$hrk1 = $neraca_akt['neraca1'];
				else
					$hrk1 = 0;

				if ($neraca_akt['neraca2'] > 0)
					$hrk2 = $neraca_akt['neraca2'];
				else
					$hrk2 = 0;
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $hrk1),
					'VAP2'			=> $this->format_money2($base, $hrk2),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_hubrekor_akt1 = $hrk1;
				$sum_hubrekor_akt2 = $hrk2;
				
				$sum_aktiva_lancar1 = $sum_kasbank_akt1 + $sum_surber_akt1 + $sum_piutus_akt1 + $sum_piutperaf_akt1 + $sum_piutpaj_akt1 + $sum_piutpeg_akt1 + $sum_piutlain_akt1 + $sum_piutpres_akt1 + $sum_tagbrupemker_akt1 + $sum_uangmukdib_akt1 + $sum_persediaan_akt1 + $sum_biadibdim_akt1 + $sum_hasakdit_akt1 + $sum_pdpkon_akt1 + $sum_hubrekor_akt1;
				
				$sum_aktiva_lancar2 = $sum_kasbank_akt2 + $sum_surber_akt2 + $sum_piutus_akt2 + $sum_piutperaf_akt2 + $sum_piutpaj_akt2 + $sum_piutpeg_akt2 + $sum_piutlain_akt2 + $sum_piutpres_akt2 + $sum_tagbrupemker_akt2 + $sum_uangmukdib_akt2 + $sum_persediaan_akt2 + $sum_biadibdim_akt2 + $sum_hasakdit_akt2 + $sum_pdpkon_akt2 + $sum_hubrekor_akt2;
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Lancar',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_lancar1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_lancar2),
				));			  
				$tpl1->parseConcatDynamic('row1');
		/*print ('kasbank = '.$sum_kasbank_akt2.'<br>');
		print ('kasbank = '.$sum_surber_akt2.'<br>');
		print ('kasbank = '.$sum_piutus_akt2.'<br>');
		print ('kasbank = '.$sum_piutperaf_akt2.'<br>');
		print ('kasbank = '.$sum_piutpaj_akt2.'<br>');
		print ('kasbank = '.$sum_piutpeg_akt2.'<br>');
		print ('kasbank = '.$sum_piutlain_akt2.'<br>');
		print ('kasbank = '.$sum_piutpres_akt2.'<br>');
		print ('kasbank = '.$sum_tagbrupemker_akt2.'<br>');
		print ('kasbank = '.$sum_uangmukdib_akt2.'<br>');
		print ('kasbank = '.$sum_persediaan_akt2.'<br>');
		print ('kasbank = '.$sum_biadibdim_akt2.'<br>');
		print ('kasbank = '.$sum_hasakdit_akt2.'<br>');
		print ('kasbank = '.$sum_pdpkon_akt2.'<br>');
		print ('kasbank = '.$sum_hubrekor_akt2.'<br>');
		die();*/
	
			} // end of if giliran

			if ($jatah == 'MODAL DITEMPATKAN DI DIVISI') //pasiva 20
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_moddidiv_pas1 = $neraca_pas['neraca1'];
				$sum_moddidiv_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah	

 			if ($giliran == 'PENYERTAAN') //21
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA KURANG LANCAR',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');
																			
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_kurang_lancar1 += $neraca_akt['neraca1'];
				$sum_aktiva_kurang_lancar2 += $neraca_akt['neraca2'];
			} // end of if giliran

			if ($jatah == 'MODAL DISETOR LAINNYA') //pasiva 21
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_modislain_pas1 = $neraca_pas['neraca1'];
				$sum_modislain_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah	
			
 			if ($giliran == 'AKTIVA PAJAK TANGGUHAN') //22
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $neraca_akt['neraca1'],
					'VAP2'			=> $neraca_akt['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_kurang_lancar1 += $neraca_akt['neraca1'];
				$sum_aktiva_kurang_lancar1 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Kurang Lancar',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_kurang_lancar1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_kurang_lancar2),
				));			  
				$tpl1->parseConcatDynamic('row1');					
			} // end of if giliran

			if ($jatah == 'SEL. PENIL. KEMBALI AKT TETAP') //pasiva 22
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_spkat_pas1 = $neraca_pas['neraca1'];
				$sum_spkat_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah

			if ($giliran == 'TANAH') //23
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA TETAP',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');	

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA TETAP BERWUJUD',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_tanah_akt1 = $neraca_akt['neraca1'];
				$sum_tanah_akt2 = $neraca_akt['neraca2'];				
			} // end of if giliran

			if ($jatah == 'SEL. TRANS. RESTRUK. ENTITAS') //pasiva 23
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_stre_pas1 = $neraca_pas['neraca1'];
				$sum_stre_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah

			if ($giliran == 'PRASARANA') //24
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_prasarana_akt1 += $neraca_akt['neraca1'];
				$sum_prasarana_akt2 += $neraca_akt['neraca2'];				
			} // end of if giliran

			if ($jatah == 'SEL. PERUB EKUI PERUS. AFILIASI') //pasiva 24
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_spepa_pas1 = $neraca_pas['neraca1'];
				$sum_spepa_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah

			if ($giliran == 'Akumulasi Penyusutan Prasarana') //25
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_prasarana_akt1 += $neraca_akt['neraca1'];
				$sum_prasarana_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Prasarana',
					'VAP1'  		=> $this->format_money2($base, $sum_prasarana_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_prasarana_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($jatah == 'AKU. SEL KURS KRN PENJAB LAPKEU') //pasiva 25
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_askkpl_pas1 = $neraca_pas['neraca1'];
				$sum_askkpl_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah

			if ($giliran == 'BANGUNAN') //26
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_bangunan_akt1 += $neraca_akt['neraca1'];
				$sum_bangunan_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($jatah == 'AKU. PENY. NILAI WAJAR INVEST.') //pasiva 26
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
												
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_apnwi_pas1 = $neraca_pas['neraca1'];
				$sum_apnwi_pas2 = $neraca_pas['neraca2'];
				
				$sum_maksa1 = $sum_modas_pas1 + $sum_sahdapor_pas1 + $sum_modis_pas1 + $sum_moddidiv_pas1 + $sum_modislain_pas1 + $sum_spkat_pas1 + $sum_stre_pas1 + $sum_spepa_pas1 + $sum_askkpl_pas1 + $sum_apnwi_pas1;
				$sum_maksa2 = $sum_modas_pas2 + $sum_sahdapor_pas2 + $sum_modis_pas2 + $sum_moddidiv_pas2 + $sum_modislain_pas2 + $sum_spkat_pas2 + $sum_stre_pas2 + $sum_spepa_pas2 + $sum_askkpl_pas2 + $sum_apnwi_pas2;
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'	=> '',
					'VPP1'  	=> $this->format_money2($base, $sum_maksa1),
					'VPP2'		=> $this->format_money2($base, $sum_maksa2),
				));			  
				$tpl1->parseConcatDynamic('row2');														
			} // end of jatah
			
			if ($giliran == 'Akumulasi Penyusutan Bangunan') //27
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_bangunan_akt1 += $neraca_akt['neraca1'];
				$sum_bangunan_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Bangunan',
					'VAP1'  		=> $this->format_money2($base, $sum_bangunan_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_bangunan_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($jatah == 'SISA LABA TAHUN LALU') //pasiva 27
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'SISA LABA',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'SISA LABA TAHUN LALU :',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');
																
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $neraca_pas['neraca1']),
					'VPP2'			=> $this->format_money2($base, $neraca_pas['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_sltl_pas1 = $neraca_pas['neraca1'];
				$sum_sltl_pas2 = $neraca_pas['neraca2'];										
			} // end of jatah

			if ($giliran == 'PERLENGKAPAN KANTOR') //28
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_perkan_akt1 += $neraca_akt['neraca1'];
				$sum_perkan_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($jatah == 'CADANGAN UMUM') //pasiva 28
			{
				$neraca_pas = $this->get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat);	
																
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $neraca_pas['neraca1'],
					'VPP2'			=> $neraca_pas['neraca2'],
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_cadum_pas1 = $neraca_pas['neraca1'];
				$sum_cadum_pas2 = $neraca_pas['neraca2'];

				$sum_sisalabatahunlalu1 = $sum_sltl_pas1 + $sum_cadum_pas1;
				$sum_sisalabatahunlalu2 = $sum_sltl_pas2 + $sum_cadum_pas2;
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'Jumlah Sisa Laba Tahun Lalu',
					'VPP1'  		=> $this->format_money2($base, $sum_sisalabatahunlalu1),
					'VPP2'			=> $this->format_money2($base, $sum_sisalabatahunlalu2),
				));			  
				$tpl1->parseConcatDynamic('row2');														
			} // end of jatah

			if ($giliran == 'Akumulasi Penyusutan Perl Kantor') //29
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_perkan_akt1 += $neraca_akt['neraca1'];
				$sum_perkan_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Perlengkapan Kantor',
					'VAP1'  		=> $this->format_money2($base, $sum_perkan_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_perkan_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($jatah == 'LABA SAMPAI DENGAN BULAN LALU') //pasiva 29
			{
				$tgl = $ryear-1 .'-'. $rmonth .'-01'; 
				$laba1 = $this->get_laba_sdbl($base, $table, $tgl);
				
				$tgl = $ryear.'-'. $rmonth .'-01';	
				$laba2 = $this->get_laba_sdbl($base, $table, $tgl); 
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'LABA TAHUN INI',
					'VPP1'  		=> '',
					'VPP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row2');
																			
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $laba1),
					'VPP2'			=> $this->format_money2($base, $laba2),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_lsdbl_pas1 = $laba1;
				$sum_lsdbl_pas2 = $laba2;														
			} // end of jatah
			
			if ($giliran == 'KENDARAAN') //30
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_kendaraan_akt1 += $neraca_akt['neraca1'];
				$sum_kendaraan_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($jatah == 'LABA BULAN INI') //pasiva 30
			{
				$tgla = $ryear-1 .'-'.$rmonth.'-01';
				$tglb = $tglakhir_per1;				
				$laba_bln_ini1 = $this->get_laba_bln_ini($base, $table, $ryear-1, $rmonth);
				
				$tgla = $ryear .'-'.$rmonth.'-01';
				$tglb = $tglakhir_per2;				
				$laba_bln_ini2 = $this->get_laba_bln_ini($base, $table, $ryear, $rmonth);					
																			
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> $spasi10.$kdjat.$spasi3.$jatah,
					'VPP1'  		=> $this->format_money2($base, $laba_bln_ini1),
					'VPP2'			=> $this->format_money2($base, $laba_bln_ini2),
				));			  
				$tpl1->parseConcatDynamic('row2');

				$sum_lbl_pas1 = $laba_bln_ini1;
				$sum_lbl_pas2 = $laba_bln_ini2;
				
				$sum_lababulanini1 = $sum_lsdbl_pas1 + $sum_lbl_pas1;
				$sum_lababulanini2 = $sum_lsdbl_pas2 + $sum_lbl_pas2;

				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'Jumlah Laba Tahun Ini',
					'VPP1'  		=> $this->format_money2($base, $sum_lababulanini1),
					'VPP2'			=> $this->format_money2($base, $sum_lababulanini2),
				));			  
				$tpl1->parseConcatDynamic('row2');		
				
				$sum_sisa_laba1 = $sum_sisalabatahunlalu1 + $sum_lababulanini1;
				$sum_sisa_laba2 = $sum_sisalabatahunlalu2 + $sum_lababulanini2;
				
				$tpl1->assignDynamic('row2', array(
					'KODE_P'		=> 'Jumlah Sisa Laba',
					'VPP1'  		=> $this->format_money2($base, $sum_sisa_laba1),
					'VPP2'			=> $this->format_money2($base, $sum_sisa_laba2),
				));			  
				$tpl1->parseConcatDynamic('row2');
				
				$sum_jumlah_pasiva1 = $sum_kewajiban_segera1 + $sum_kewajiban_tidak_segera1 + $sum_maksa1 +  $sum_sisa_laba1;
				$sum_jumlah_pasiva2 = $sum_kewajiban_segera2 + $sum_kewajiban_tidak_segera2 + $sum_maksa2 +  $sum_sisa_laba1;
				
				//print ($sum_jumlah_pasiva1.' <---> '.$sum_jumlah_pasiva2);
		/*				 
						$tpl->assignDynamic('row', array(
							'VJPP1'			=> $sum_jumlah_pasiva1,
							'VJPP2'			=> $sum_jumlah_pasiva2,
						));			  
						$tpl->parseConcatDynamic('row');					
		*/									
			} // end of jatah

			if ($giliran == 'Akumulasi Penyusutan Kendaraan') //31
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_kendaraan_akt1 += $neraca_akt['neraca1'];
				$sum_kendaraan_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Kendaraan',
					'VAP1'  		=> $this->format_money2($base, $sum_kendaraan_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_kendaraan_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($giliran == 'PERALATAN') //32
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_peralatan_akt1 += $neraca_akt['neraca1'];
				$sum_peralatan_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($giliran == 'Akumulasi Penyusutan Peralatan') //33
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_peralatan_akt1 += $neraca_akt['neraca1'];
				$sum_peralatan_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Peralatan',
					'VAP1'  		=> $this->format_money2($base, $sum_peralatan_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_peralatan_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');

				$sum_aktiva_tetap_berwujud1 = $sum_tanah_akt1 + $sum_prasarana_akt1 + $sum_bangunan_akt1 + $sum_perkan_akt1 + $sum_kendaraan_akt1 + $sum_peralatan_akt1;
				$sum_aktiva_tetap_berwujud2 = $sum_tanah_akt2 + $sum_prasarana_akt2 + $sum_bangunan_akt2 + $sum_perkan_akt2 + $sum_kendaraan_akt2 + $sum_peralatan_akt2;
				
				//print ('aktiva tetep berwujud -> '. $sum_tanah_akt1.' + '.$sum_prasarana_akt1 .' + '. $sum_bangunan_akt1 .' + '. $sum_perkan_akt1 .' + '. $sum_kendaraan_akt1 .' + '. $sum_peralatan_akt1);
							
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Tetap Berwujud',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_tetap_berwujud1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_tetap_berwujud2),
				));			  
				$tpl1->parseConcatDynamic('row1');												
			} // end of if giliran

		/*			
					if ($jatah == 'PASIVA BERES') //pasiva 31
					{																			
						$tpl1->assignDynamic('row2', array(
							KODE_P'		=> ' ',
							VPP1'  		=> ' ',
							VPP2'			=> ' ',
						));			  
						$tpl1->parseConcatDynamic('row2');
															
					} // end of jatah
		*/
			
			if ($giliran == 'HAK SEWA') //34
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA TETAP TIDAK BERWUJUD',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_haksewa_akt1 += $neraca_akt['neraca1'];
				$sum_haksewa_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($giliran == 'Amortisasi Hak Sewa') //35
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_haksewa1_akt += $neraca_akt['neraca1'];
				$sum_haksewa_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Hak Sewa',
					'VAP1'  		=> $this->format_money2($base, $sum_haksewa1),
					'VAP2'			=> $this->format_money2($base, $sum_haksewa2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($giliran == 'LEASING') //36
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_leasing_akt1 += $neraca_akt['neraca1'];
				$sum_leasing_akt2 += $neraca_akt['neraca2'];								
			} // end of if giliran

			if ($giliran == 'Amortisasi Leasing') //37
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
															
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $spasi10.$kdgil.' '.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_leasing_akt1 += $neraca_akt['neraca1'];
				$sum_leasing_akt2 += $neraca_akt['neraca2'];

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Leasing',
					'VAP1'  		=> $this->format_money2($base, $sum_leasing_akt1),
					'VAP2'			=> $this->format_money2($base, $sum_leasing_akt2),
				));			  
				$tpl1->parseConcatDynamic('row1');

				$sum_aktiva_tetap_tidak_berwujud1 = $sum_haksewa_akt1 + $sum_leasing_akt1;
				$sum_aktiva_tetap_tidak_berwujud2 = $sum_haksewa_akt2 + $sum_leasing_akt2;
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Tetap Tidak Berwujud',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_tetap_tidak_berwujud1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_tetap_tidak_berwujud2),
				));			  
				$tpl1->parseConcatDynamic('row1');

				$sum_aktiva_tetap1 = $sum_aktiva_tetap_berwujud1 + $sum_aktiva_tetap_tidak_berwujud1;
				$sum_aktiva_tetap2 = $sum_aktiva_tetap_berwujud2 + $sum_aktiva_tetap_tidak_berwujud2;
				
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Tetap',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_tetap1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_tetap2),
				));			  
				$tpl1->parseConcatDynamic('row1');								
			} // end of if giliran

			if ($giliran == 'JAMINAN') //38
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'AKTIVA LAIN-LAIN',
					'VAP1'  		=> '',
					'VAP2'			=> '',
				));			  
				$tpl1->parseConcatDynamic('row1');
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_lain1 += $neraca_akt['neraca1'];
				$sum_aktiva_lain2 += $neraca_akt['neraca2'];								
			} // end of if giliran
					
			if ($giliran == 'INVESTASI DALAM PELAKSANAAN') //39
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_lain1 += $neraca_akt['neraca1'];
				$sum_aktiva_lain2 += $neraca_akt['neraca2'];								
			} // end of if giliran					

			if ($giliran == 'BEBAN DITANGGUHKAN') //40
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_lain1 += $neraca_akt['neraca1'];
				$sum_aktiva_lain2 += $neraca_akt['neraca2'];								
			} // end of if giliran	

			if ($giliran == 'AKTIVALAIN-LAIN') //41
			{
				$neraca_akt = $this->get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil);	
																		
				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> $kdgil.$spasi5.$giliran,
					'VAP1'  		=> $this->format_money2($base, $neraca_akt['neraca1']),
					'VAP2'			=> $this->format_money2($base, $neraca_akt['neraca2']),
				));			  
				$tpl1->parseConcatDynamic('row1');					

				$sum_aktiva_lain1 += $neraca_akt['neraca1'];
				$sum_aktiva_lain2 += $neraca_akt['neraca2'];	

				$tpl1->assignDynamic('row1', array(
					'KODE_A'		=> 'Jumlah Aktiva Lain Lain',
					'VAP1'  		=> $this->format_money2($base, $sum_aktiva_lain1),
					'VAP2'			=> $this->format_money2($base, $sum_aktiva_lain2),
				));			  
				$tpl1->parseConcatDynamic('row1');
				
				$sum_jumlah_aktiva1 = $sum_aktiva_lancar1 + $sum_aktiva_kurang_lancar1 + $sum_aktiva_tetap1 + $sum_aktiva_lain1;
				$sum_jumlah_aktiva2	= $sum_aktiva_lancar2 + $sum_aktiva_kurang_lancar2 + $sum_aktiva_tetap2 + $sum_aktiva_lain2;
				
				$tpl1->emptyParsedPage();				
				$tpl->assignDynamic('row', array(
					'VJAP1'			=> $this->format_money2($base, $sum_jumlah_aktiva1),
					'VJAP2'			=> $this->format_money2($base, $sum_jumlah_aktiva2),
					
					'VJPP1'			=> $this->format_money2($base, $sum_jumlah_pasiva1),
					'VJPP2'			=> $this->format_money2($base, $sum_jumlah_pasiva2),
				));			  
				$tpl->parseConcatDynamic('row');		
		/*				
						$tpl2->emptyParsedPage();				
						$tpl->assignDynamic('row1', array(
							'VJAP1'			=> 'baru nyampe sum kewajiban segera',
							'VJAP2'			=> 'baru nyampe sum kewajiban segera',
						));			  
						$tpl->parseConcatDynamic('row1');							
			*/							
			} // end of if giliran
			
			if ($posisi == 41)
			{
				$tpl_temp->assign('ONE',$tpl,'template');
				$tpl_temp->parseConcat();
				
				// ======== FOR EXCEL
				$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
				$tpl_temp_excel->parseConcat();
				// ========						
			}
			$posisi++;
			$rs_a->moveFirst();
		} // end of while posisi
		
	

		$PDF_URL = "?mod=accounting_report_other&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
	
		$tpl_temp->Assign('ONE', '
					<div id="pr" name="pr" align="left">
					<br>
					<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
					<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
					<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
					<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
										<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report_other&cmd=mainpage&rep_type=overview_ledger\')">
					-->
					</div>'
		);
		//create file untuk biaya usaha (group/ikhtisar)		
		$tpl_temp->parseConcat();
		
		// === FOR EXCEL
		$PDF_URL = "?mod=accounting_report_other&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
	
		$tpl_temp_excel->Assign('ONE', '
					<div id="pr" name="pr" align="left">
					<br>
					<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
					<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
					<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
					<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
										<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report_other&cmd=mainpage&rep_type=overview_ledger\')">
					-->
					</div>'
		);		
		$tpl_temp_excel->parseConcat();
		// ===

		$is_proses = $this->get_var('is_proses');
		
		if($is_proses=='t')
		{
			$filename = $base->kcfg['basedir']."files/"."NERACA_LAJUR_T".$kddiv."_".$thn_."_".$bln_.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);
			$this->tpl =& $tpl_temp;
			
			// === FOR EXCEL
			$filename_excel = $base->kcfg['basedir']."files/"."NERACA_LAJUR_T".$kddiv."_".$thn_."_".$bln_."_for_excel.html";
			$isi_excel = & $tpl_temp_excel->parsedPage();
			$this->cetak_to_file($base,$filename_excel,$isi_excel);			
			// ===
		}
		else
        {
            $this->tpl =& $tpl_temp;
			$this->tpl_excel =& $tpl_temp_excel;
        }
				
	}/*}}}*/	
	
	function sub_report_neraca_lajur_uker_t($base) /*{{{*/
	{
		return $this->sub_report_neraca_t($base);
	}/*}}}*/

	function sub_report_neraca_lajur_spk($base)
	{
		// foreach ($base as $key => $value) {
		// 	echo $key.' ->'.$value.'<br>';
		// 	foreach ($value  as $key_isi => $value_isi) {
		// 		echo $key_isi.' ->'.$value_isi.'<br>';
		// 	}
		// 	echo "<br>";
		// }exit();
		return $this->sub_report_neraca_lajur_spk_print($base);
	}	
    
    function sub_report_neraca_lajur_spk_t($base) /*{{{*/
    {
        return $this->sub_report_neraca_t($base);
    }/*}}}*/

	function sub_report_neraca_lajur_spk_t_OLD($base) /*{{{*/
	{
		//die ('neraca lajur spk t');
        return $this->sub_report_neraca_t($base);
        
		$this->get_valid_app('SDV');
		$kdspk = $this->get_var('kdspk','');

		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$nmspk=$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk ='{$kdspk}'");
		//die($divname);
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
		//$tpl->defineDynamicBlock('row');
		
		$tpl1 = & $tpl->defineDynamicBlock('row');
		$tpl2 = & $tpl1->defineDynamicBlock('row1');
		$tpl3 = & $tpl1->defineDynamicBlock('row2');
		
   		$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ===== FOR EXCEL					
			$tpl_excel = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
			
			$tpl1_excel = & $tpl_excel->defineDynamicBlock('row');
			$tpl2_excel = & $tpl1_excel->defineDynamicBlock('row1');
			$tpl3_excel = & $tpl1_excel->defineDynamicBlock('row2');
    	
			$tpl_temp_excel = $base->_get_tpl('one_var.html');
			$this->_fill_static_report($base,&$tpl_excel);		
		// ===== 
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		$tglawal_per1 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear-1));
		$tglawal_per2 = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear));
		$tglakhir_per1 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1));
		$contoh_3 = $thn_.'-12-31';
		$periode1 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1));
		$tglakhir_per2 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear));		
		
		//print $thn_cari
		
		$startdate = $this->get_var('startdate',date('d-m-Y'));
		$enddate = $this->get_var('enddate',date('d-m-Y'));

		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
				$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
				$sdate = date('Y-m-d');

		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
				$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
				$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		
		$sql_a = "SELECT to_char(date(a.tanggal),'YYYY-MM-DD') as tanggal, a.kdperkiraan, a.dk, a.rupiah
							FROM {$table} a
							WHERE substr(a.kdperkiraan,1,1) <= 3
										AND a.kdspk = '{$kdspk}'
										AND (
												(DATE(a.tanggal) > '$tglawal_per1' AND DATE(a.tanggal) <= '$tglakhir_per1')
												OR
												(DATE(a.tanggal) > '$tglawal_per2' AND DATE(a.tanggal) <= '$tglakhir_per2')
												)
							ORDER BY a.kdperkiraan";
		die ($sql_a);						
    	$rs_a = $base->dbquery($sql_a);
		
	}/*}}}*/	

	function sub_report_neraca_lajur_direktorat_t($base) /*{{{*/
	{
		die ('neraca lajur direktorat t');
	}/*}}}*/
	
	
	//==================PRIVATE==================

	function get_hasil_akt($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdgil) /*{{{*/
	{
		$debet1 = 0; $kredit1 = 0; $debet2 = 0; $kredit2 = 0; $m_debet1 = 0; $m_kredit1 = 0; $m_debet2 = 0; $m_kredit2 = 0;

		$range = strlen($kdgil);
		
		$rs_a->moveFirst();
		while (!$rs_a->EOF)
		{
			$kdper = $rs_a->fields['kdperkiraan'];
							
			if (substr($kdper,0,$range) == $kdgil)
			{
		/*
						$a = mktime(0,0,0,1,2,2008);
						$b = mktime(0,0,0,1,1,2008);
						if($a > $b) // true
						
						
						$date_arr = explode("-",$rs_a->fields['tanggal']);
						$tanggal =  date("Y-m-d", mktime(0,0,0,$date_arr[1],$date_arr[2],$date_arr[0]));
						$tanggal2 = date("Y-m-d",mktime(0,0,0,2,1,2005));
						echo ($tanggal2<$tanggal);
						
						echo $tanggal;
						echo $tanggal2;
						die($tanggal);
		*/			
				if ((date($rs_a->fields['tanggal']) > date($tglawal1)) && (date($rs_a->fields['tanggal']) <= date($tgl1)))
				{				
					if ($rs_a->fields['dk'] == 'D')
						$m_debet1 += $rs_a->fields['rupiah'];
					else
						$m_kredit1 += $rs_a->fields['rupiah'];
				}
				
				if ((date($rs_a->fields['tanggal']) > date($tglawal2)) && (date($rs_a->fields['tanggal']) <= date($tgl2))) //2005-11-30  
				{					
					if ($rs_a->fields['dk'] == 'D')
						$m_debet2 += $rs_a->fields['rupiah'];
					else
						$m_kredit2 += $rs_a->fields['rupiah'];					
				}
				
				if (date($rs_a->fields['tanggal']) <= date($tglawal1)) //periode 1
				{
					if ($rs_a->fields['dk'] == 'D')
						$debet1 += $rs_a->fields['rupiah'];
					else
						$kredit1 += $rs_a->fields['rupiah'];
				}
			 	
				if (date($rs_a->fields['tanggal']) <= date($tglawal2)) //periode 2
				{
					if ($rs_a->fields['dk'] == 'D')
						$debet2 += $rs_a->fields['rupiah'];
					else
						$kredit2 += $rs_a->fields['rupiah'];
				}
			}
			$rs_a->moveNext();
		} // end of while rs_a		
		
		$awal_d = 0;
		$awal_k = 0;

		if (($debet1 - $kredit1) >= 0)
			$awal_d = ($debet1 - $kredit1);
		else
			$awal_k = ($debet1 - $kredit1) * -1;
			
		$percobaan_d = $awal_d + $m_debet1;
		$percobaan_k = $awal_k + $m_kredit1;
				
		$neraca1 = $percobaan_d - $percobaan_k;

		$awal_d = 0;
		$awal_k = 0;
				
		if (($debet2 - $kredit2) >= 0)
			$awal_d = ($debet2 - $kredit2);
		else
			$awal_k = ($debet2 - $kredit2) * -1;
			
		$percobaan_d = $awal_d + $m_debet2;
		$percobaan_k = $awal_k + $m_kredit2;
				
		$neraca2 = $percobaan_d - $percobaan_k;	
		
		/*		if ($kdgil == '217')
				{
					print ('debet periode lalu = '.$debet2.'<br>');
					print ('kredit periode lalu = '.$kredit2.'<br>');
					
					print ('neraca awal debet = '.$awal_d.'<br>');
					print ('neraca awal kredit = '.$awal_k.'<br>');
					
					print ('mutasi debet = '.$m_debet2.'<br>');
					print ('mutasi kredit = '.$m_kredit2.'<br>');
					
					print ('percobaan debet = '.$percobaan_d.'<br>');
					print ('percobaan kredit = '.$percobaan_k.'<br>');
					
					print ('jumlah neraca = '.$neraca2);
					die();						
				}*/
				
				return array('neraca1'=> $neraca1, 'neraca2'=> $neraca2);
	} /*}}}*/	

	function get_hasil_pas($base, $rs_a, $tglawal1, $tgl1, $tglawal2, $tgl2, $kdjat) /*{{{*/
	{
		$debet1 = 0; $kredit1 = 0; $debet2 = 0; $kredit2 = 0; $m_debet1 = 0; $m_kredit1 = 0; $m_debet2 = 0; $m_kredit2 = 0;

		$range = strlen($kdjat);
		
		while (!$rs_a->EOF)
		{
			$kdper = $rs_a->fields['kdperkiraan'];
							
			if (substr($kdper,0,$range) == $kdjat)
			{
				if ((date($rs_a->fields['tanggal']) > date($tglawal1)) && (date($rs_a->fields['tanggal']) <= date($tgl1)))
				{				
					if ($rs_a->fields['dk'] == 'D')
						$m_debet1 += $rs_a->fields['rupiah'];
					else
						$m_kredit1 += $rs_a->fields['rupiah'];
				}
				
				if ((date($rs_a->fields['tanggal']) > date($tglawal2)) && (date($rs_a->fields['tanggal']) <= date($tgl2))) //2005-11-30  
				{					
					if ($rs_a->fields['dk'] == 'D')
						$m_debet2 += $rs_a->fields['rupiah'];
					else
						$m_kredit2 += $rs_a->fields['rupiah'];					
				}
				
				if (date($rs_a->fields['tanggal']) <= date($tglawal1)) //periode 1
				{
					if ($rs_a->fields['dk'] == 'D')
						$debet1 += $rs_a->fields['rupiah'];
					else
						$kredit1 += $rs_a->fields['rupiah'];
				}
			 	
				if (date($rs_a->fields['tanggal']) <= date($tglawal2)) //periode 2
				{
					if ($rs_a->fields['dk'] == 'D')
						$debet2 += $rs_a->fields['rupiah'];
					else
						$kredit2 += $rs_a->fields['rupiah'];
				}
			}
			$rs_a->moveNext();
		} // end of while rs_a
		//$rs_a->moveFirst();

		$awal_d = 0;
		$awal_k = 0;
						
		if (($debet1 - $kredit1) >= 0)
			$awal_d = $debet1 - $kredit1;
		else
			$awal_k = ($debet1 - $kredit1) * -1;
			
		$percobaan_d = $awal_d + $m_debet1;
		$percobaan_k = $awal_k + $m_kredit1;
				
		$neraca1 = ($percobaan_d - $percobaan_k) * -1;

		$awal_d = 0;
		$awal_k = 0;
		
		if (($debet2 - $kredit2) >= 0)
			$awal_d = $debet2 - $kredit2;
		else
			$awal_k = ($debet2 - $kredit2) * -1;
			
		$percobaan_d = $awal_d + $m_debet2;
		$percobaan_k = $awal_k + $m_kredit2;
				
		$neraca2 = ($percobaan_d - $percobaan_k) * -1;
		
		/*		if ($kdjat == '32111')
				{
					print ('debet periode lalu = '.$debet2.'<br>');
					print ('kredit periode lalu = '.$kredit2.'<br>');
					
					print ('neraca awal debet = '.$awal_d.'<br>');
					print ('neraca awal kredit = '.$awal_k.'<br>');
					
					print ('mutasi debet = '.$m_debet2.'<br>');
					print ('mutasi kredit = '.$m_kredit2.'<br>');
					
					print ('percobaan debet = '.$percobaan_d.'<br>');
					print ('percobaan kredit = '.$percobaan_k.'<br>');
					
					print ($neraca2);
					die();						
				}*/
				
				return array('neraca1'=> $neraca1, 'neraca2'=> $neraca2);
	} /*}}}*/

	function get_laba_sdbl($base, $table, $tgl) /*{{{*/
	{
		$sql = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo
						FROM(
									SELECT (CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
												 (CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
												 ,1 AS grp
									FROM $table pjur
									WHERE true AND DATE(pjur.tanggal) < '$tgl' AND isdel = 'f' -- AND isapp='t'
												AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
									GROUP BY pjur.dk
								 )t
						GROUP BY grp";
		$rs = $base->dbquery($sql);	
		
		return $rs->fields['saldo'] * -1;
	} /*}}}*/

	function get_laba_bln_ini($base, $table, $ryear, $rmonth) /*{{{*/
	{
		//print $ryear; 2004
		
		$sql = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo
						FROM (
									SELECT (CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
												 (CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
												 ,1 AS grp
									FROM $table pjur
									WHERE true AND ( DATE_PART('YEAR',pjur.tanggal) = '".$ryear."' 
																	 AND DATE_PART('MONTH',pjur.tanggal) = '".$rmonth."') 
														 AND isdel = 'f' --- AND isapp='t' 
                             AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									GROUP BY pjur.dk
									)t
						GROUP BY grp";
		$rs = $base->dbquery($sql);	
		
		return $rs->fields['saldo'] * -1;
	} /*}}}*/
    
    function sub_report_neraca_t($base)/*{{{*/
    {
        //$base->db->debug = true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $konsolidasi = $this->get_var('konsolidasi');
        $txt_konsolidasi = ($konsolidasi == 'yes') ? "konsolidasi_" : "";
        $kddiv = $this->S['curr_divisi'];
        $table = ($konsolidasi == 'yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($this->S['curr_divisi']);
        if($kddiv == '') $table = "jurnal";
        $divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $is_proses = $this->get_var('is_proses');
        $is_excel = $this->get_var('is_excel');
        $sub = $this->get_var('sub', 'divisi_t');
        
        $tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl->defineDynamicBlock(array('row1', 'row2'));
        $this->_fill_static_report($base,&$tpl);
        
        $tpl_excel = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl_excel->defineDynamicBlock(array('row1', 'row2'));
        $this->_fill_static_report($base,&$tpl_excel);
        		
		// getValidDivisiByDate - by Eldin
		$validDiv = array();
		$sq_lr 	= "
		SELECT * FROM ddivisi 
		WHERE 
			is_visible = TRUE 
			AND '{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01'::DATE >= tgl_valid_start
			AND '{$year}-".str_pad($month,2,'0',STR_PAD_LEFT)."-01'::DATE < (tgl_valid + INTERVAL '1 month')";
        $rs = $base->dbQuery($sq_lr);
		while (!$rs->EOF) {
			$validDiv[] = "'".$rs->fields['kddivisi']."'";
			$rs->moveNext();
		}
		if (count($validDiv) >= 1 AND $table === 'v_jurnal_konsolidasi') 
			$addsql = " AND j.kddivisi IN (".implode(',', $validDiv).")";
		else 
			$addsql = '';
		// end getValidDivisiByDate		
		
        if($is_proses == 't')
        {
            $dp = new dateparse();
            
            // $sql = "SELECT --dp.kdperkiraan, dp.nmperkiraan,
                        // group_coa,
                    // SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                            // --THEN get_dk(j.dk)*(case when dp.kdperkiraan like '1%' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
			// THEN get_dk(j.dk)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
                        // ELSE 0 END) AS rupiah_now,
                    // SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-12-".($year-1)."')
                            // --THEN get_dk(j.dk)*(case when dp.kdperkiraan like '1%' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
			// THEN get_dk(j.dk)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
                        // ELSE 0 END) AS rupiah_last
                    // FROM mapping_neraca m, dperkir dp
                    // LEFT JOIN {$table} j ON (
                    		// j.kdperkiraan=dp.kdperkiraan 
                    		// AND date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                            // AND isdel='f' AND isapp='t'
                        // )
                    // WHERE dp.kdperkiraan SIMILAR TO '(1|2|3)%'  AND dp.group_coa=m.group_name
                    // GROUP BY group_coa --dp.kdperkiraan, dp.nmperkiraan
                    // ";
          
            if($table == "jurnal_l"){
 
            	if($year <= 2012 ){            		
					$tambahan = " 0";
            	}
				else {
					$tambahan = " SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-12-".($year-1)."')
					THEN (
					CASE WHEN j.dk='D' THEN rupiah*1
					ELSE rupiah*-1 
					END)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
                        ELSE 0 END) ";
				}
				//'1-".$month."-".$year."'
				 $sql = "SELECT 
                        group_coa,												
                    SUM(CASE WHEN date_trunc('month', j.tanggal) <= (date('$year-$month-01') + interval '1 month')
                           
				THEN (
						CASE WHEN j.dk='D' THEN rupiah*1
						ELSE rupiah*-1 
						END)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
	                        ELSE 0 END) AS rupiah_now,
	            			{$tambahan}
				AS rupiah_last
	                    FROM mapping_neraca m, dperkir dp
	                    LEFT JOIN {$table} j ON (
	                    		j.kdperkiraan=dp.kdperkiraan 
	                    		AND date_trunc('month', j.tanggal) <= (date('$year-$month-01') + interval '1 month')
	                            AND isdel='f' -- AND isapp='t'
	                            )
	                    WHERE dp.kdperkiraan SIMILAR TO '(1|2|3)%'  AND dp.group_coa=m.group_name
	                    GROUP BY group_coa";
            }else{
            	  // irul
            	$last_year = ($year-1);
            	  $sql = "SELECT 
                        group_coa,												
	                    SUM(CASE WHEN date_trunc('month', j.tanggal) <= (date('$year-$month-01') + interval '1 month')
	                           
				THEN 	(
						CASE WHEN j.dk='D' THEN rupiah*1
						ELSE rupiah*-1 
						END)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
	                        ELSE 0 END) AS rupiah_now,
	                    SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('$last_year-12-01')
	            
				THEN (
						CASE WHEN j.dk='D' THEN rupiah*1
						ELSE rupiah*-1 
						END)*(case when m.l_r = 'L' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
	                        ELSE 0 END) AS rupiah_last
	                    FROM mapping_neraca m, dperkir dp
	                    LEFT JOIN {$table} j ON (
	                    		j.kdperkiraan=dp.kdperkiraan 
	                    		AND date_trunc('month', j.tanggal) <= (date('$year-$month-01') + interval '1 month')
	                            AND isdel='f' -- AND isapp='t'
	                            {$addsql})
	                    WHERE dp.kdperkiraan SIMILAR TO '(1|2|3)%'  AND dp.group_coa=m.group_name
	                    GROUP BY group_coa";
            // end of irul
            }
            
                    
             //die($sql);
                    
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                //if($rs->fields['kdperkiraan'] == '32211')
                if($rs->fields['group_coa'] == 'B4530') //Laba Tahun Berjalan
                {
                	/*
					 * $sqllaba = "SELECT SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                                        THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_now,
                                      SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-12-".($year-1)."')
                                      THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_last
                                FROM {$table} j
                                WHERE kdperkiraan similar to '(4|4|5|6|7|9)%' OR kdperkiraan='32211' AND isdel='f' AND isapp='t'";
					 * */
					
					// eldin 
                    // $sqllaba = "SELECT SUM(CASE WHEN date_trunc('month', j.tanggal) <= (date('1-".$month."-".$year."') + interval '1 month')
                                        // THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_now,
                                      // SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-12-".($year-1)."')
                                      // THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_last
                                // FROM {$table} j
                                // WHERE kdperkiraan similar to '(4|4|5|6|7|9)%' OR kdperkiraan='32211' AND isdel='f' AND isapp='t'";
                                
                    // $sqllaba = "SELECT
							// SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".$year."') AND date('1-".$month."-".$year."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) AS rupiah_now,
				                    // SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".($year-1)."') AND date('1-12-".($year-1)."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) AS rupiah_last
				                    // FROM dperkir dp
				                    // LEFT JOIN {$table} j ON (
				                    		// j.kdperkiraan=dp.kdperkiraan 
				                    		// AND date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
				                            // AND isdel='f' AND isapp='t' {$addsql}
				                        // )
				                    // WHERE dp.kdperkiraan SIMILAR TO '(4|4|5|6|7|9)%' 
				                    // GROUP BY group_coa 
				                    // ";
				if($table == "jurnal_l"){ 
	            	if($year <= 2012 ){            		
						$tambahan = " 0";
	            	}
					else {
						$tambahan = " SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".($year-1)."') AND date('1-12-".($year-1)."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) ";
					}
					$sqllaba = "
				    SELECT SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".$year."') AND date('1-".$month."-".$year."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) AS rupiah_now,
					       {$tambahan} AS rupiah_last
					FROM {$table} j
					WHERE kdperkiraan SIMILAR TO '(4|4|5|6|7|9)%' OR kdperkiraan='32211' AND isdel='f' -- AND isapp='t'
				    ";
				}
				else {
					$sqllaba = "
				    SELECT SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".$year."') AND date('1-".$month."-".$year."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) AS rupiah_now,
					       SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('1-1-".($year-1)."') AND date('1-12-".($year-1)."') THEN get_dk(j.dk)*-1*j.rupiah ELSE 0 END) AS rupiah_last
					FROM {$table} j
					WHERE kdperkiraan SIMILAR TO '(4|5|6|7|9)%' OR kdperkiraan='32211' AND isdel='f' -- AND isapp='t'
          {$addsql}
				    ";
				}
				    
                    // end of eldin    
                    
                    // die ($sqllaba);
                                
                    $rslaba = $base->dbQuery($sqllaba);
                    $rs->fields['rupiah_now'] = $rslaba->fields['rupiah_now'];
                    $rs->fields['rupiah_last'] = $rslaba->fields['rupiah_last'];
					
					// print $rs->fields['group_coa']. ' | ' . $rs->fields['rupiah_now']. ' | ' . $rs->fields['rupiah_last']. '<br />';
					// die();
                }
                
                $rupiah[ $rs->fields['group_coa'] ]['rupiah_now'] = $rs->fields['rupiah_now'];
                $rupiah[ $rs->fields['group_coa'] ]['rupiah_last'] = $rs->fields['rupiah_last'];
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                
                // print $rs->fields['group_coa']. ' | ' . $rs->fields['rupiah_now']. ' | ' . $rs->fields['rupiah_last']. '<br />';
				
                //parent kdperkiraan
                /*$parent_kdperkiraan = substr($rs->fields['kdperkiraan'], 0,4);
                $rupiah[$parent_kdperkiraan]['rupiah_now'] += $rs->fields['rupiah_now'];
                $rupiah[$parent_kdperkiraan]['rupiah_last'] += $rs->fields['rupiah_last'];
                 */
                $rs->moveNext();
            }
            
            // print '<pre>'; print_r($nama_perkiraan); print '</pre>';
            
            // $sq_activa = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r --,b.kdperkiraan
                            // FROM mapping_neraca a
                            // --LEFT JOIN dperkir b ON (b.group_coa=a.group_name)
                            // WHERE a.visibility='t' AND group_type='neraca'
                            // ORDER BY a.urutan";
                            
            // eldin
            $sq_activa = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r
                            FROM mapping_neraca a
                            WHERE a.visibility='t' AND group_type='neraca'
                            ORDER BY a.urutan";
            // end of eldin
                            
			// die($sq_activa);
            $rs = $base->dbQuery($sq_activa);
            
            while(!$rs->EOF)
            {
                $parent_header = '';
                $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['group_name'] = $rs->fields['group_name'];
                $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['description'] = $rs->fields['description'];
                
                if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_now'] =  $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_last'] =  $rupiah[$rs->fields['group_name']]['rupiah_last'];
                    $subtotal[$rs->fields['parent']]['rupiah_now'] += $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$rs->fields['parent']]['rupiah_last'] += $rupiah[$rs->fields['group_name']]['rupiah_last'];
                 
                    if ($parent_header <> $rs->fields['parent'])
                    {
                    $subtotal[$parent_header]['rupiah_now'] += $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$parent_header]['rupiah_last'] += $rupiah[$rs->fields['group_name']]['rupiah_last'];
                    }
                    
                    $subtotal[$grand_header]['rupiah_now'] += $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$grand_header]['rupiah_last'] += $rupiah[$rs->fields['group_name']]['rupiah_last'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_now'] = $subtotal[$rs->fields['parent']]['rupiah_now'];
                    $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_last'] = $subtotal[$rs->fields['parent']]['rupiah_last'];
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold; padding-bottom:20px; font-size: .7em;' : '';
                $rupiah_now = $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_now'];
                $rupiah_last = $data_neraca[$rs->fields['parent']][$rs->fields['group_name']]['rupiah_last'];
                
                // if($rupiah_now != 0 OR $rupiah_last != 0)
                if($rupiah_now != 0 OR $rupiah_last != 0) 		// eldin
                {
                	/*if ($rs->fields['description'] == '32211 Saldo Laba Tahun Berjalan')
					{
						$rupiah_now_ = $this->sub_result_data(strtoupper($kddiv),$year.'-'.((strlen($month == 1)?$month:'0'.$month)),$base);
						
					}
					else {
						$rupiah_now_ = $rupiah_now;
					}*/
                    $tpl->assignDynamic($parserow, array(
                        //'KODE_A'    
                        'NAMA_A' => $rs->fields['description'],
                        'VAP1'  => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now),
                        'VAP2'  => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                        'VSTYLE'    => $style
                    ));
                    $tpl->parseConcatDynamic($parserow);
                    
                    $tpl_excel->assignDynamic($parserow, array(
                        //'KODE_A'    
                        'NAMA_A' => $rs->fields['description'],
                        'VAP1'  => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now_),
                        'VAP2'  => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                        'VSTYLE'    => $style
                    ));
                    $tpl_excel->parseConcatDynamic($parserow);    
                }
                
				// print '<pre>'; print_r($rs->fields); print '</pre>'; 	// eldin
				// print $rs->fields['description'] .' | ' . $subtotal[$parent_header]['rupiah_now'] .' | ' . $subtotal[$parent_header]['rupiah_last'] .'<br/ >';
                $rs->moveNext();
            }

			// print '<pre>'; print_r($rs->fields); print '</pre>';
            //$nilai_total_t = 
            $record_static = array(
                'VJUM_AKTIVA1'  => $this->format_money2($base, $total_rupiah_all['row1']['rupiah_now']),
                'VJUM_AKTIVA2'  => $this->format_money2($base, $total_rupiah_all['row1']['rupiah_last']),
                'VJUM_PASIVA1'  => $this->format_money2($base, $total_rupiah_all['row2']['rupiah_now']),
                'VJUM_PASIVA2'  => $this->format_money2($base, $total_rupiah_all['row2']['rupiah_last']),
                'DIVNAME'		=> $divname,
    			'KDSPK'			=> '',
    			'NMSPK'			=> '',
    			'SID'    		=> MYSID,
                'PERIODE'       => $dp->monthnamelong[$month].' '.$year,
                'PERIODE_SEBELUM'   => ($year-1),
                'PERIODE1'      => $dp->monthname[$month].' '.$year,
                'PERIODE2'      => $dp->monthname[12].' '.($year-1),
            );
            $tpl->assign($record_static);
            $tpl_excel->assign($record_static);
            
			// print '<pre>'; print_r($record_static); print '</pre>'; 	// eldin
			
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month."_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month.".html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);   
            
            $this->tpl = $tpl; 
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month."_for_excel.html";
            $fp = @fopen($filename,"r"); 
    		if (!$fp) 
    			die("The file does not exists!");
    			
    		
    		$contents = fread ($fp, filesize ($filename));
    		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename.'.xls');
    				
    		fclose ($fp);
    
    		$tpl = $base->_get_tpl('one_var.html');
    		$tpl->assign('ONE' ,	$contents);
    		
    		$this->tpl = $tpl;
        }
        else
        {
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month.".html";
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' , $contents);
            
            $this->tpl = $tpl;
        }
    }/*}}}*/

    function sub_result_data($div,$tgl,$base)
	{
		$nilai="";
		$sql = "SELECT rupiah_now FROM z_temp_neraca_t WHERE kddivisi ='{$div}' and tanggal = '{$tgl}-01' and group_name = 'J000'";
		//die($sql);
		$rs = $base->dbQuery($sql);            
        while(!$rs->EOF)
        {
        	$nilai = $rs->fields['rupiah_now'];
		}
		return $nilai;
	}

    function sub_report_neraca_t_old($base)/*{{{*/
    {
        //$base->db->debug = true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $konsolidasi = $this->get_var('konsolidasi');
        $txt_konsolidasi = ($konsolidasi == 'yes') ? "konsolidasi_" : "";
        $kddiv = $this->S['curr_divisi'];
        $table = ($konsolidasi == 'yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($this->S['curr_divisi']);
        if($kddiv == '') $table = "jurnal";
        $divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $is_proses = $this->get_var('is_proses');
        $is_excel = $this->get_var('is_excel');
        $sub = $this->get_var('sub', 'divisi_t');
        
        $tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl->defineDynamicBlock(array('row1', 'row2'));
        $this->_fill_static_report($base,&$tpl);
        
        $tpl_excel = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl_excel->defineDynamicBlock(array('row1', 'row2'));
        $this->_fill_static_report($base,&$tpl_excel);
        
        
        if($is_proses == 't')
        {
            $dp = new dateparse();
            
            $sql = "SELECT dp.kdperkiraan, dp.nmperkiraan,
                    SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                            THEN get_dk(j.dk)*(case when dp.kdperkiraan like '1%' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
                        ELSE 0 END) AS rupiah_now,
                    SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')-INTERVAL '1 YEAR'
                            THEN get_dk(j.dk)*(case when dp.kdperkiraan like '1%' then 1 ELSE -1 END)*COALESCE(j.rupiah,0)
                        ELSE 0 END) AS rupiah_last
                    FROM dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                            AND isdel='f' -- AND isapp='t'
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(1|2|3)%' 
                    GROUP BY dp.kdperkiraan, dp.nmperkiraan
                    ";
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                if($rs->fields['kdperkiraan'] == '32211')
                {
                    $sqllaba = "SELECT SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                                        THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_now,
                                      SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".($year-1)."')
                                      THEN get_dk(j.dk)*(case when kdperkiraan like '1%' then 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS rupiah_last
                                FROM {$table} j
                                WHERE kdperkiraan similar to '(4|4|5|6|7|9)%' OR kdperkiraan='32211' AND isdel='f' --AND isapp='t'
                                ";
                    $rslaba = $base->dbQuery($sqllaba);
                    $rs->fields['rupiah_now'] = $rslaba->fields['rupiah_now'];
                    $rs->fields['rupiah_last'] = $rslaba->fields['rupiah_last'];
                                                     
                }
                
                $rupiah[$rs->fields['kdperkiraan']]['rupiah_now'] = $rs->fields['rupiah_now'];
                $rupiah[$rs->fields['kdperkiraan']]['rupiah_last'] = $rs->fields['rupiah_last'];
                $nama_perkiraan[$rs->fields['kdperkiraan']] = $rs->fields['nmperkiraan'];
                
                //parent kdperkiraan
                $parent_kdperkiraan = substr($rs->fields['kdperkiraan'], 0,4);
                $rupiah[$parent_kdperkiraan]['rupiah_now'] += $rs->fields['rupiah_now'];
                $rupiah[$parent_kdperkiraan]['rupiah_last'] += $rs->fields['rupiah_last'];
                 
                $rs->moveNext();
            }
            
            //parsing activa
            $data = array('row1' => $this->_get_activa($base), 'row2' => $this->_get_pasiva($base));
            foreach($data as $parserow => $data_neraca)
            {
                $record_static = array(
                    'KODE_A'    => '&nbsp',
                    'NAMA_A'    => '<b>'.strtoupper(($parserow=='row1')?'ASSET':'LIABILITAS').'</b>',
                    'VAP1'  => '&nbsp',
                    'VAP2'  => '&nbsp',
                );
                
                $tpl->assignDynamic($parserow, $record_static);
                $tpl->parseConcatDynamic($parserow);
                
                $tpl_excel->assignDynamic($parserow, $record_static);
                $tpl_excel->parseConcatDynamic($parserow);
                        
                foreach($data_neraca as $k => $v)
                {
                    if(is_array($v))
                    {
                        $record_static = array(
                            'KODE_A'    => '&nbsp;',
                            'NAMA_A'    => '<b>'.strtoupper($k).'</b>',
                            'VAP1'  => '',
                            'VAP2'  => ''
                        );
                        $tpl->assignDynamic($parserow, $record_static);
                        $tpl->parseConcatDynamic($parserow);
                        
                        $tpl_excel->assignDynamic($parserow, $record_static);
                        $tpl_excel->parseConcatDynamic($parserow);
                        
                        foreach($v as $kk => $vv)
                        {
                            if(is_array($vv))
                            {
                                if(is_numeric($kk) && strlen($kk)<2)
                                {
                                    //no parse   
                                }
                                else
                                {
                                    $record_static = array(
                                        'KODE_A'    => '&nbsp;',
                                        'NAMA_A'    => (strlen($kk)<2) ? '&nbsp;' : '<b>'.strtoupper($kk).'</b>',
                                        'VAP1'  => '',
                                        'VAP2'  => ''
                                    );
                                    $tpl->assignDynamic($parserow,$record_static);
                                    $tpl->parseConcatDynamic($parserow);
                                    
                                    $tpl_excel->assignDynamic($parserow,$record_static);
                                    $tpl_excel->parseConcatDynamic($parserow);
                                }
                                
                                
                                foreach($vv as $kkk => $vvv)
                                {
                                    if(!is_numeric($vvv))
                                    {
                                        $arr_vvv = explode(',', $vvv);
                                        $nama_perkiraan[$vvv] = $kkk;
                                        $kode = ''; //str_replace(',', '<br />', $vvv);;
                                        foreach($arr_vvv as $key => $rp)
                                        {
                                            $rupiah[$vvv]['rupiah_now'] += $rupiah[$rp]['rupiah_now'];
                                            $rupiah[$vvv]['rupiah_last'] += $rupiah[$rp]['rupiah_last'];
                                        }
                                    }
                                    else
                                    {
                                        $kode = $vvv;
                                    }
                                    $tpl->assignDynamic($parserow, array(
                                        'KODE_A'    => $kode,
                                        'NAMA_A'    => $nama_perkiraan[$vvv],
                                        'VAP1'  => $this->format_money($base, $rupiah[$vvv]['rupiah_now']),
                                        'VAP2'  => $this->format_money($base, $rupiah[$vvv]['rupiah_last'])
                                    ));
                                    $tpl->parseConcatDynamic($parserow);
                                    
                                    $tpl_excel->assignDynamic($parserow, array(
                                        'KODE_A'    => $kode,
                                        'NAMA_A'    => $nama_perkiraan[$vvv],
                                        'VAP1'  => $rupiah[$vvv]['rupiah_now'],
                                        'VAP2'  => $rupiah[$vvv]['rupiah_last']
                                    ));
                                    $tpl_excel->parseConcatDynamic($parserow);
                                    
                                    $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                                    $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                                    
                                    $subtotal_rupiah_all[$k]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                                    $subtotal_rupiah_all[$k]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                                    
                                    $subtotal_rupiah_all[$kk]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                                    $subtotal_rupiah_all[$kk]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                                }
                                
                                $tpl->assignDynamic($parserow, array(
                                    'KODE_A'    => '&nbsp;',
                                    'NAMA_A'    => '&nbsp;',
                                    'VAP1'  => '<b>'.$this->format_money($base, $subtotal_rupiah_all[$kk]['rupiah_now']).'</b>',
                                    'VAP2'  => '<b>'.$this->format_money($base, $subtotal_rupiah_all[$kk]['rupiah_last']).'</b>'
                                ));
                                $tpl->parseConcatDynamic($parserow);
                                
                                $tpl_excel->assignDynamic($parserow, array(
                                    'KODE_A'    => '&nbsp;',
                                    'NAMA_A'    => '&nbsp;',
                                    'VAP1'  => '<b>'.$subtotal_rupiah_all[$kk]['rupiah_now'].'</b>',
                                    'VAP2'  => '<b>'.$subtotal_rupiah_all[$kk]['rupiah_last'].'</b>'
                                ));
                                $tpl_excel->parseConcatDynamic($parserow);
                                  
                            }
                            else
                            {
                                //echo $kk.' '.$vv.'<br />';
                                if(!is_numeric($kk))
                                { 
                                    $arr_vv = explode(',', $vv);
                                    $nama_perkiraan[$vv] = $kk;
                                    $kode = ''; //str_replace(',', '<br />', $vv);
                                    foreach($arr_vv as $key => $rp)
                                    {
                                        $rupiah[$vv]['rupiah_now'] += $rupiah[$rp]['rupiah_now'];
                                        $rupiah[$vv]['rupiah_last'] += $rupiah[$rp]['rupiah_last'];
                                    }
                                }
                                else
                                {
                                    $kode = $vv;
                                }
                                
                                $tpl->assignDynamic($parserow, array(
                                    'KODE_A'    => $kode,
                                    'NAMA_A'    => strtoupper($nama_perkiraan[$vv]),
                                    'VAP1'  => $this->format_money($base, $rupiah[$vv]['rupiah_now']),
                                    'VAP2'  => $this->format_money($base, $rupiah[$vv]['rupiah_last'])
                                ));
                                $tpl->parseConcatDynamic($parserow);
                                
                                $tpl_excel->assignDynamic($parserow, array(
                                    'KODE_A'    => $kode,
                                    'NAMA_A'    => strtoupper($nama_perkiraan[$vv]),
                                    'VAP1'  => $rupiah[$vv]['rupiah_now'],
                                    'VAP2'  => $rupiah[$vv]['rupiah_last']
                          ));
                                $tpl_excel->parseConcatDynamic($parserow);
                                
                                $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$vv]['rupiah_now'];
                                $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$vv]['rupiah_last'];
                                
                                $subtotal_rupiah_all[$k]['rupiah_now'] += $rupiah[$vv]['rupiah_now'];
                                $subtotal_rupiah_all[$k]['rupiah_last'] += $rupiah[$vv]['rupiah_last'];
                            }  
                        }
                        
                        $tpl->assignDynamic($parserow, array(
                            'KODE_A'    => '&nbsp;',
                            'NAMA_A'    => '<b>JUMLAH ' . strtoupper($k) . '</b>',
                            'VAP1'  => '<b>'.$this->format_money($base, $subtotal_rupiah_all[$k]['rupiah_now']).'</b>',
                            'VAP2'  => '<b>'.$this->format_money($base, $subtotal_rupiah_all[$k]['rupiah_last']).'</b>'
                        ));
                        $tpl->parseConcatDynamic($parserow);
                        
                        $tpl_excel->assignDynamic($parserow, array(
                            'KODE_A'    => '&nbsp;',
                            'NAMA_A'    => '<b>JUMLAH ' . strtoupper($k) . '</b>',
                            'VAP1'  => '<b>'.$subtotal_rupiah_all[$k]['rupiah_now'].'</b>',
                            'VAP2'  => '<b>'.$subtotal_rupiah_all[$k]['rupiah_last'].'</b>'
                        ));
                        $tpl_excel->parseConcatDynamic($parserow);
                        
                    }
                    else
                    {
                        $tpl->assignDynamic($parserow, array(
                            'KODE_A'    => $v,
                            'NAMA_A'    => '<b>'.strtoupper($nama_perkiraan[$v]).'</b>',
                            'VAP1'  => $this->format_money($base, $rupiah[$v]['rupiah_now']),
                            'VAP2'  => $this->format_money($base, $rupiah[$v]['rupiah_last'])
                        ));
                        $tpl->parseConcatDynamic($parserow);
                        
                        $tpl_excel->assignDynamic($parserow, array(
                            'KODE_A'    => $v,
                            'NAMA_A'    => '<b>'.strtoupper($nama_perkiraan[$v]).'</b>',
                            'VAP1'  => $rupiah[$v]['rupiah_now'],
                            'VAP2'  => $rupiah[$v]['rupiah_last']
                        ));
                        $tpl_excel->parseConcatDynamic($parserow);
                        
                        $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$v]['rupiah_now'];
                        $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$v]['rupiah_last'];
                        
                    }
                }
            }
            
            $record_static = array(
                'VJUM_AKTIVA1'  => $this->format_money($base, $total_rupiah_all['row1']['rupiah_now']),
                'VJUM_AKTIVA2'  => $this->format_money($base, $total_rupiah_all['row1']['rupiah_last']),
                'VJUM_PASIVA1'  => $this->format_money($base, $total_rupiah_all['row2']['rupiah_now']),
                'VJUM_PASIVA2'  => $this->format_money($base, $total_rupiah_all['row2']['rupiah_last']),
                'DIVNAME'		=> $divname,
    			'KDSPK'			=> '',
    			'NMSPK'			=> '',
    			'SID'    		=> MYSID,
                'PERIODE'       => $dp->monthnamelong[$month].' '.$year,
                'PERIODE_SEBELUM'   => ($year-1),
                'PERIODE1'      => $dp->monthname[$month].' '.$year,
                'PERIODE2'      => $dp->monthname[$month].' '.($year-1),
            );
            $tpl->assign($record_static);
            $tpl_excel->assign($record_static);
            
            
            
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month."_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month.".html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);   
            
            $this->tpl = $tpl; 
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month."_for_excel.html";
            $fp = @fopen($filename,"r"); 
    		if (!$fp) 
    			die("The file does not exists!");
    			
    		
    		$contents = fread ($fp, filesize ($filename));
    		
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename.'.xls');
    				
    		fclose ($fp);
    
    		$tpl = $base->_get_tpl('one_var.html');
    		$tpl->assign('ONE' ,	$contents);
    		
    		$this->tpl = $tpl;
        }
        else
        {
            $kdreport = "NER_LAJ";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_neraca_lajur_".$sub."_".$txt_konsolidasi.$year."_".$month.".html";
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' , $contents);
            
            $this->tpl = $tpl;
        }
        
    }/*}}}*/
    
    function _get_activa($base)/*{{{*/
    {
        
        $data_account = array(
            'Asset Lancar' => array(
                'Kas dan Setara Kas'    => array(
                    '1101111',
                    '1101121',
                    'Bank Pemerintah' => '1101211,1101221,1101231,1101241,1101291',
                    'Bank Swasta' => '1101212,1101222,1101232,1101242,1101292',
                    '1101311',
                ),
                'Surat Berharga' => array(
                    '1101411',
                    '1101421',
                    '1101431',
                    '1101441',
                    '1101491'
                ),
                'Piutang Usaha' => array(
                    '1104111',
                    '1104119',
                    '1104211',
                    '1104219',
                    '1105111',
                    '1105211',
                    '1104311',
                    '1104321',
                    '1104331',
                    '1104341',
                    '1104351',
                    '1104361'
                ),
                'Pajak Dibayar Dimuka' => array(
                    '1110111',
                    '1110121',
                    '1110131',
                    '1110141',
                    '1110151',
                    '1110161',
                    '1110171',
                    '1110181',
                    '1110211',
                    '1110221',
                    '1110231',
                    '1110241'
                ),
                'Piutang Lain-lain' => array(
                    '1106111',
                    '1106121',
                    '1106131',
                    '1106141',
                    '1106191',
                    '1104112'
                ),
                '1109111',
                'Uang Muka Kepada Pihak Luar' => array(
                    '1107111',
                    '1107121',
                    '1107131',
                    '1107141',
                    
                ),
                'Uang Muka Kepada Pegawai' => array(
                    '1107211',
                    '1107221',
                ),
                'Persediaan'    => array(
                    '1108111',
                    '1108411',
                    '1108511',
                    '1108611',
                    '1108711',
                    '1108811',
                ),
                'Biaya Dibayar Dimuka' => array(
                    '1111111',
                    '1111211',
                    '1111311',
                    '1111411',
                    '1111491',
                    '1111511',
                    '1111611',
                ),
                'Jaminan' => array(
                    '1112111',
                    '1112121',
                    '1112131',
                    '1112141',
                    '1112151',
                ),
                //'1114111',
                'Pekerjaan Dalam Proses Konstruksi' => array(
                    '1113111',
                    '1113121',
                    '1113131',
                    '1113141',
                ),
                '1114111',
                '1102191',
                '1199111'
            ),

            'ASET TIDAK LANCAR' => array(
                '1202111',
                '1203111',
                '1203211',
            ),
            'ASET TETAP BERWUJUD' => array(
                'Harga Perolehan' => array(
                    '1205111',
                    '1205211',
                    '1205311',
                    '1205411',
                    '1205511',
                    '1205611',
                ),
                'Akumulasi Penyusutan' => array(
                    '1205291',
                    '1205391',
                    '1205491',
                    '1205591',
                    '1205691',
                ),
                'Aset Tetap Dalam Pelaksanaan' => array(
                    '1206111',
                    '1206121',
                    '1206131',
                    '1206141',
                    '1206151',
                ),
                'Aset Tidak Berwujud' => array(
                    '1207111',
                    '1207121',
                    '1207131',
                ),
            ),
            'ASET LAIN-LAIN' => array(
                'Beban Ditangguhkan' => array(
                    '1299111',
                    '1299121',
                    '1299151',
                    '1299191',
                ),
                '1208111',
                '1299211',
                '1299911',
            )
        );
        
        return $data_account;
    }/*}}}*/
    
    function _get_pasiva($base)/*{{{*/
    {
        $data_account = array(
            'LIABILITAS JANGKA PENDEK' => array(
                'Pinjaman Jangka Pendek' => array(
                    'Hutang Bank Pemerintah' => '2101111,2101121,2101131,2101141,2101191',
                    'Hutang Bank Swasta' => '2101112,2101122,2101132,2101142,2101192',
                ),
                'Wesel Bayar' => '2102111,2102121,2102131,2102141,2102191',
                'Hutang Usaha'  => array(
                    '2103111',
                    '2103112',
                    '2103113',
                    '2103114',
                    '2103115',
                    '2103119',
                ),
                '2104111',
                'Hutang Pajak' => array(
                    '2105111',
                    '2105121',
                    '2105131',
                    '2105141',
                    '2105151',
                    '2105161',
                    '2105211',
                    '2105221',
                    '2105231',
                    '2105241',
                ),
                'Uang Muka Diterima'    => '2106111,2106211',
                'Beban Yang Masih Harus Dibayar' => array(
                    '2108111',
                    '2108121',
                    '2108131',
                    '2108141',
                    '2108151',
                    '2108161',
                    '2108191',
                    '2108211',
                    '2108221',
                    '2105171',
                ),
                '2109111',
                'Potongan Pegawai' => array(
                    '2112111',
                    '2112121',
                    '2112131',
                    '2112141',
                    '2112151',
                    '2112191',
                ),
                'Hutang Lain-lain' => array(
                    '2113111',
                    '2113121',
                    '2113131',
                    '2113141',
                    '2113151',
                    '2113161',
                    '2113191',
                    'Akun tidak terdefinisi' => '2111599,2161199,2162199,2172799,2173199,2173499,2173599,2174499,2174699,2174799,2175899,2176199,2176299,2176399,2176499',
                ),
                '2199111',
                '2202111'
            ),
            'LIABILITAS JANGKA PANJANG' => array(
                'Hutang Bank Pemerintah' => '2203111,2203121,2203131,2203141,2203191',
                'Hutang Bank Swasta' => '2203112,2203122,2203132,2203142,2203192',
                '2206111',
                '2206121',
                '2207111',
                '2209111',
                'Hubungan Rekening Koran' => array(
                    '2110111',
                    '2110201',
                    '2110211',
                    '2110221',
                    '2110231',
                    '2110241',
                    '2110251',
                    '2110261',
                    '2110271',
                    '2110281',
                    '2110291',
                    '2110311',
                    '2110321',
                    '2110331',
                    '2110341',
                    '2110351',
                ),
            ),
            'Ekuitas' => array(
                '3101111',
                '3102111',
                '3103111',
                '3104111',
                '3105111',
                '3201111',
                '3201121',
                '3201131',
                '3201141',
                '3201151',
                '3201161',
                '3701111',
                '3701121',
                '32211',
                /*'Kepentingan Non Pengendali' => array(
                    '2110251',
                    '2110261',
                    '2110271',
                    '2110281',
                    '2110291',
                    '2110311',
                    '2110321',
                    '2110331',
                    '2110341',
                    '2110351',
                )*/
            )
        );
        
        return $data_account;
    }/*}}}*/
    
    function sub_cek_report($base)/*{{{*/
    {
        $kdreport = $this->get_var('kdreport');
        $grptype = $this->get_var('grptype');
        $rmonth = $this->get_var('month_');
        $ryear = $this->get_var('year_');
        $type = $this->get_var('tbtype');
        $kddiv = $this->S['curr_divisi'];
        $sub = $this->get_var('sub');
        $konsolidasi = $this->get_var('konsolidasi');
		$kdspk = (isset($_POST['kdspk']))? '_'.$this->get_var('kdspk'):'';
        
        if($konsolidasi == 'yes')
            $txt_konsolidasi = 'konsolidasi_';
        
        $sub = ($sub == 'divisi') ? "" : "_".$sub;
        
        $filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur".$sub."_".$txt_konsolidasi.$ryear."_".$rmonth.".html";
        //print $filename .'<br />';
        if(file_exists($filename))
        {
            echo 1;
        }
        else
        {
            echo 0;
        }
        
        exit;
    }/*}}}*/
    function sub_neraca_browse_spk($base)/*{{{*/
    {		
    	$tpl = $base->_get_tpl('neraca_browse_spk.html');
		$tpl->defineDynamicBlock('row');
		$tempKd = $this->get_var('kdspk','');

		$otherKd = $this->get_var('kddivisi','');
		$tempKd = $this->get_var('kdspk','');
		$tempNm = $this->get_var('nmspk','');
		$tpl->assign('vkdspk', $tempKd);
		$tpl->assign('vnmspk', $tempNm);
		$tpl->assign('vkddiv', $otherKd);
				

	    if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base,$this->S['curr_divisi']);
	    else $sql_wil = '';
				  if ($this->S['curr_wil'] != '')
	    {
	      $sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
	    }

		$sql = "SELECT kdspk, nmspk, lokasi FROM dspk WHERE lower(kdspk) LIKE lower('{$tempKd}%') and kddiv = '{$otherKd}' AND lower(nmspk) LIKE lower('%{$tempNm}%')  $sql_wil ORDER BY kdspk";

		//$base->db->debug=true;		
		$p = new TeraNavbar($base);
		$rs = $p->Execute($sql,$base->kcfg['maxnum'],$base->kcfg['maxnum']);
		$rs = $p->Execute($sql,1000,$base->kcfg['maxnum']);
		//$base->db->debug=false;
		$l = "";
		$links = $p->getlinks("all", "on");
		for ($y = 0; $y < count($links); $y++)
		{
				$l .= $links[$y] . "&nbsp;&nbsp;";
		}
		$tpl->assign('PREVNEXT',$l);
		if($rs->EOF)
		{
			$tpl->assign('row' , '<tr><td colspan="5" align="center"><em>'.$base->getLang('Data tidak ada').'</em></td></tr>');
		}
		$iiii = 0;
		while(!$rs->EOF)
		{	
			$tpl->assignDynamic('row',array(
				'KDSPK'		=> $rs->fields['kdspk'],
				'VKDS'		=> $rs->fields['kdspk'],
				'VNMSPK'	=> $rs->fields['nmspk'],
				'VLOKASI'	=> $rs->fields['lokasi'],
				'VKDSPK'	=> $rs->fields['kdspk'],
				'SID'		=> MYSID,
				'VIDIN'		=> $iiii
			));
			$iiii++;
			$tpl->parseConcatDynamic('row');
			$rs->movenext();
		}
				
		$this->tpl = $tpl;
    }/*}}}*/

    function sub_report_neraca_lajur_rk($base) /*{{{*/
	{
		 //$base->db->debug=true;		
		$this->get_valid_app('SDV');
		$kdspk = $this->get_var('kdspk','');
		$rnobukti = $this->get_var('nobukti','');
			
		
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$nmspk=$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk ='{$kdspk}'");
		//die($divname);
		
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_neraca_lajur_spk_printable.html');
		$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ====== FOR EXCEL
			
		$tpl_excel = $base->_get_tpl('report_neraca_lajur_spk_printable.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
		

		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		
		
		//print $thn_cari
		
			$startdate = $this->get_var('startdate',date('d-m-Y'));
			$enddate = $this->get_var('enddate',date('d-m-Y'));

			if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
					$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
			else
					$sdate = date('Y-m-d');

			if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
					$edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
			else
					$edate = date('Y-m-d');
			
		$batasan_bulan_new = $rmonth;
		$batasan_tahun_new = $ryear;
		$final_batasan_tanggal = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
		$batasan_tgl_sal_akhir = date("Y-m-d",mktime(0,0,0,$batasan_bulan_new+1,1,$batasan_tahun_new));
		//die($batasan_bulan_new);
		//die($batasan_tgl_sal_akhir);
		//START : Mencari akumulasi mutasi
		// $sql = " SELECT
			// jur.kdperkiraan,
				// (CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				// (CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			// FROM $table jur
			// WHERE true
			// AND 
			// (
				// DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
				// AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
			// )
			// AND isdel = 'f'  AND isapp='t'
			// AND kdspk='".$kdspk."'
			// GROUP BY jur.kdperkiraan,jur.dk
			// ORDER BY jur.kdperkiraan,jur.dk";
			
		$sql_and_kdspk = '';
		if ($kdspk != '')
			$sql_and_kdspk = " AND kdspk='".$kdspk."' ";
		if ($rnobukti != '')
			$sql_and_nobukti = " AND nobukti LIKE '{$rnobukti}%' ";
		else 
			$sql_and_nobukti = " --AND nobukti NOT LIKE '01%' ";
		/**
		 * Add condition where kdperkiran not like '01%'
		 * by: Eldin
		 */	
		$sql = " SELECT
			jur.kdperkiraan,
				--(CASE WHEN jur.buktipelunasan ISNULL THEN SUM(jur.rupiah) END) AS mutasi_debet,
				--(CASE WHEN jur.dk='D' AND jur.buktipelunasan NOTNULL THEN SUM(jur.rupiah) END) AS mutasi_kredit
				(CASE WHEN jur.dk='D' THEN SUM(jur.rupiah) END) AS mutasi_debet,
				(CASE WHEN jur.dk='K' THEN SUM(jur.rupiah) END) AS mutasi_kredit
			FROM $table jur
			WHERE true
			AND jur.refjid=-11
			AND 
			--(
			--	DATE_PART('YEAR',jur.tanggal) = '$batasan_tahun_new' 
			--	AND DATE_PART('MONTH',jur.tanggal) = '$batasan_bulan_new'
			--)
			jur.tanggal >='$final_batasan_tanggal'::DATE AND jur.tanggal <= '$batasan_tgl_sal_akhir'::DATE
			AND isdel = 'f'  -- AND isapp='t'
			-- AND kdspk='$kdspk'
			-- AND nobukti NOT LIKE '01%'
			{$sql_and_kdspk} 
			{$sql_and_nobukti}
			GROUP BY jur.kdperkiraan,jur.dk,jur.buktipelunasan
			ORDER BY jur.kdperkiraan,jur.dk";
		
		 //die ("<pre>$sql</pre>");		
		
		$rs2=$base->dbquery($sql);
		if ($rs2->EOF)
		{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
		{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
				'NMSPK' => $nmspk
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
				'NMSPK' => $nmspk
			));
			
			// ===== begin while $rs2
			$array_mutasi = array();
			while(!$rs2->EOF)
			{
				//$array_neraca_awal[$rs2->fields['kdperkiraan']][]=$rs2->fields['kdperkiraan'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_debet'];				
				$array_mutasi[$rs2->fields['kdperkiraan']][]=$rs2->fields['mutasi_kredit'];
				
				$rs2->moveNext();
				
			} // end of while	

			/*
			echo "<pre>";
			print_r($array_mutasi);		
			echo "</pre>";
			*/

			//die($sql);
		}
		//die ($sql1);		
		// $sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			// FROM
			// (
			// (SELECT jur.kdperkiraan,d.nmperkiraan,
						 // (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 // (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			// FROM $table jur,dperkir d
			// WHERE jur.kdperkiraan=d.kdperkiraan AND
						// DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						// AND isdel = 'f' AND isapp='t'
						// AND kdspk='".$kdspk."'
			// GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			// ORDER BY jur.kdperkiraan,jur.dk
			// )
			// UNION ALL (SELECT '32211' as kdperkiraan
      // , 'LABA TAHUN BERJALAN' as nmperkiraan
      // , 0 as rupiah_debet
      // , 0 as rupiah_kredit )) t
			// GROUP BY t.kdperkiraan,t.nmperkiraan
			// ORDER BY t.kdperkiraan,t.nmperkiraan";
			
		$sql_and_kdspk = '';
		if ($kdspk != '')
			$sql_and_kdspk = " AND jur.kdspk='".$kdspk."' ";
		if ($rnobukti != '')
			$sql_and_nobukti = " AND jur.nobukti LIKE '{$rnobukti}%' ";
		else 
			$sql_and_nobukti = " --AND jur.nobukti NOT LIKE '01%' ";
		/**
		 * Add condition where kdperkiran not like '01%'
		 * by: Eldin
		 */	
		$sql1 = "SELECT t.kdperkiraan,t.nmperkiraan,(COALESCE(SUM(t.rupiah_debet),0)-COALESCE(SUM(t.rupiah_kredit),0)) AS saldo_akhir
			FROM
			(
			(SELECT jur.kdperkiraan,d.nmperkiraan,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
			FROM $table jur,dperkir d
			WHERE jur.kdperkiraan=d.kdperkiraan AND
						DATE(jur.tanggal) < '$batasan_tgl_sal_akhir'
						AND jur.refjid=-11
						AND isdel = 'f' -- AND isapp='t'
						-- AND kdspk='".$kdspk."'
						-- AND jur.nobukti NOT LIKE '01%' 
						{$sql_and_kdspk} 
						{$sql_and_nobukti}
			GROUP BY jur.kdperkiraan,d.nmperkiraan,jur.dk
			ORDER BY jur.kdperkiraan,jur.dk
			)
			UNION ALL (SELECT '32211' as kdperkiraan
      , 'LABA TAHUN BERJALAN' as nmperkiraan
      , 0 as rupiah_debet
      , 0 as rupiah_kredit )) t
			GROUP BY t.kdperkiraan,t.nmperkiraan
			ORDER BY t.kdperkiraan,t.nmperkiraan";
		//echo "<pre>$sql1</pre>";
		//die($sql1);
		
		$rs1=$base->dbquery($sql1);
		if ($rs1->EOF)
			{
			$tpl->Assign('row','');
			
			// ==== FOR EXCEL
			$tpl_excel->Assign('row','');		
			// ====
		}
		else
			{
			$tpl->Assign(array(
				'VTHN'   => $ryear,
				'VBLN'  => $rmonth,
				'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' => $startdate,
				'EDATE' => $enddate,
				'SID'      => MYSID,
				'VCURR'      => '',
				'NMSPK' => $nmspk
			));
				
				
			// ===== FOR EXCEL
			
			$tpl_excel->Assign(array(
				'VTHN'   	=> $ryear,
				'VBLN'  	=> $rmonth,
				'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
				'NMSPK' => $nmspk
			));
			
			// =====
			$tot_nerawal_d = 0;
			$tot_nerawal_k = 0;
			$tot_mutasi_debet = 0;
			$tot_mutasi_kredit = 0;
			$tot_neraca_debet = 0;
			$tot_neraca_kedit = 0;
			$tot_nercoba_debet = 0;
			$tot_nercoba_kedit = 0;
			$tot_nercoba_debet =0;
			$tot_nercoba_kredit =0;
			$tot_lr_debet = 0;
			$tot_lr_kredit = 0;
			
			$kdperkiraan_tmp='';
			// ========TPL
			$tpl->defineDynamicBlock('row');
					
			// ===== FOR EXCEL
			$tpl_excel->defineDynamicBlock('row');


			while(!$rs1->EOF)
			{
				$curr_coa = $rs1->fields['kdperkiraan'];
				$saldo_akhir = $rs1->fields['saldo_akhir'];
				if ($kdperkiraan_tmp == $curr_coa)
					$rs1->moveNext();
				
				$counter = count($array_mutasi[$rs1->fields['kdperkiraan']]);
			
				if (!$counter)
				{
					$mutasi_debet = 0;
					$mutasi_kredit = 0;
				}
				else if($counter==4)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][3];
				}
				else if($counter==2)
				{
					$mutasi_debet = $array_mutasi[$rs1->fields['kdperkiraan']][0];
					$mutasi_kredit = $array_mutasi[$rs1->fields['kdperkiraan']][1];
				}
				
				
					
				$jml_peng_mutasi_debet = $mutasi_debet - $mutasi_kredit;
				if ((substr($curr_coa,0,1) == "1") || (substr($curr_coa,0,1) == "2") || (substr($curr_coa,0,1) == "3"))
				{
					$neraca_awal = $saldo_akhir - $jml_peng_mutasi_debet;			
					//Mencari laba bulan berjalan
					//yahya 22-04-2008
					if ($curr_coa == "32211")
					{
						// $sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
						// FROM
						// (
							// SELECT
										// (CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										// (CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										// ,1 AS grp
									 // FROM $table pjur
									 // WHERE true
												 // AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 // AND isdel = 'f' AND isapp='t'
												 // AND kdspk='".$kdspk."'
												 // AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
									 // GROUP BY pjur.dk
						// )t
						// GROUP BY grp";
						
						$sql_and_kdspk = '';
						if ($kdspk != '')
							$sql_and_kdspk = " AND pjur.kdspk='".$kdspk."' ";
						if ($rnobukti != '')
							$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";
						else 
							$sql_and_nobukti = " AND pjur.nobukti NOT LIKE '01%' ";
						/**
						 * Add condition where kdperkiran not like '01%'
						 * by: Eldin
						 */
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS saldo_akhir
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
									 			AND pjur.refjid=-11
												 AND DATE(pjur.tanggal) < '$final_batasan_tanggal'
												 AND isdel = 'f' -- AND isapp='t'
												 --AND kdspk='".$kdspk."' 
												 {$sql_and_kdspk}
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%' OR pjur.kdperkiraan='32211')
												 --AND pjur.nobukti NOT LIKE '01%' 
												 {$sql_and_nobukti} 
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$neraca_awal = $base->db->getOne($sql3);
						
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							// $nerawal_k = str_replace("-", "", $nerawal_k);
							$nerawal_k = abs($nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					else
					{
						if ($neraca_awal < 0)
						{
							$nerawal_d = 0;
							$nerawal_k = $neraca_awal;
							// $nerawal_k = str_replace("-", "", $nerawal_k);
							$nerawal_k = abs($nerawal_k);
						}
						else
						{
							$nerawal_k = 0;
							$nerawal_d = $neraca_awal;
						}
					}
					
					$neraca = ($nerawal_d + $mutasi_debet) - ($nerawal_k + $mutasi_kredit);
					if ($neraca < 0)
					{
						$neraca_debet = 0;
						// $neraca = str_replace("-", "", $neraca);
						$neraca = abs($neraca);
						$neraca_kredit = $neraca;
					}
					else
					{
						$neraca_debet = $neraca;
						$neraca_kredit = 0;
					}
				}
				else
				{
					$nerawal_d = 0;
					$nerawal_k = 0;
					$neraca_debet = 0;
					$neraca_kredit = 0;					
					$nercoba_debet = 0;
					$nercoba_kredit = 0;
				}				
				$nercoba_debet = $nerawal_d + $mutasi_debet;
				$nercoba_kredit = $nerawal_k + $mutasi_kredit;
				
				if (in_array(substr($curr_coa,0,1), array(5,6,7,9)))
				{
					$laba_rugi = $nercoba_debet - $nercoba_kredit;
					if ($laba_rugi < 0)
					{
						$lr_debet = 0;
						$lr_kredit = $laba_rugi;
						$lr_kredit = str_replace("-", "", $lr_kredit);
					}
					else
					{
						$lr_debet = $laba_rugi;
						$lr_kredit = 0;
					}
				}
				else
				{
					$lr_debet = 0;
					$lr_kredit = 0;
				}
				
				
					
				$tot_nerawal_d += $nerawal_d;
				$tot_nerawal_k += $nerawal_k;
				$tot_mutasi_debet += $mutasi_debet;
				$tot_mutasi_kredit += $mutasi_kredit;
				$tot_neraca_debet += $neraca_debet;
				$tot_neraca_kredit += $neraca_kredit;
				$tot_nercoba_debet += $nercoba_debet;
				$tot_nercoba_kredit += $nercoba_kredit;
				$tot_lr_debet += $lr_debet;
				$tot_lr_kredit += $lr_kredit;
				
				$tpl->assignDynamic('row', array(
						'VCOA'  	=> $rs1->fields['kdperkiraan'],
						'VNAMA'  	=> $rs1->fields['nmperkiraan'],
						'VNERAWAL_D'  	=> $this->format_money2($base, $nerawal_d),
						'VNERAWAL_K'  	=> $this->format_money2($base, $nerawal_k),
						'VMUTASI_D'  	=> $this->format_money2($base, $mutasi_debet),
						'VMUTASI_K'  	=> $this->format_money2($base, $mutasi_kredit),
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> $this->format_money2($base, $nercoba_debet),
						'VNERCOBA_K'  	=> $this->format_money2($base, $nercoba_kredit),
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
					'VCOA'  			=> $rs1->fields['kdperkiraan'],
					'VNAMA'  			=> $rs1->fields['nmperkiraan'],
					'VNERAWAL_D'  	=> $nerawal_d,
					'VNERAWAL_K'  	=> $nerawal_k,
					'VMUTASI_D'  	=> $mutasi_debet,
					'VMUTASI_K'  	=> $mutasi_kredit,
					'VNERACA_D'  	=> $neraca_debet,
					'VNERACA_K'  	=> $neraca_kredit,
					'VNERCOBA_D'  	=> $nercoba_debet,
					'VNERCOBA_K'  	=> $nercoba_kredit,
					'VRUGLAB_D'  	=> $lr_debet,
					'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
				
				$kdperkiraan_tmp == $curr_coa;
					
				$rs1->moveNext();
								
			} // end of while	
			//die("yahya");
			///Mencari Rugi Laba Bulan Ini
			//yahya 22-04-2008
						// $sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						// FROM
						// (
							// SELECT
										// (CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										// (CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										// ,1 AS grp
									 // FROM $table pjur
									 // WHERE true
												 // AND 
													// (
														// DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														// AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													// )
												 // AND isdel = 'f' AND isapp='t'
												 // AND kdspk='".$kdspk."'
												 // AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
									 // GROUP BY pjur.dk
						// )t
						// GROUP BY grp";
						
						$sql_and_kdspk = '';
						if ($kdspk != '')
							$sql_and_kdspk = " AND pjur.kdspk='".$kdspk."' ";
						if ($rnobukti != '')
							$sql_and_nobukti = " AND pjur.nobukti LIKE '{$rnobukti}%' ";
						else 
							$sql_and_nobukti = " AND pjur.nobukti NOT LIKE '01%' ";
						/**
						 * Add condition where kdperkiran not like '01%'
						 * by: Eldin
						 */
						$sql3 = "SELECT (COALESCE(SUM(t.debet),0)-COALESCE(SUM(t.kredit),0)) AS lr
						FROM
						(
							SELECT
										(CASE WHEN pjur.dk='D' THEN SUM (pjur.rupiah) ELSE 0 END) AS debet,
										(CASE WHEN pjur.dk='K' THEN SUM (pjur.rupiah) ELSE 0 END) AS kredit
										,1 AS grp
									 FROM $table pjur
									 WHERE true
									 			AND pjur.refjid=-11
												 AND 
													(
														DATE_PART('YEAR',pjur.tanggal) = '".$batasan_tahun_new."' 
														AND DATE_PART('MONTH',pjur.tanggal) = '".$batasan_bulan_new."'
													)
												 AND isdel = 'f' -- AND isapp='t'
												 -- AND kdspk='".$kdspk."' 
												 {$sql_and_kdspk} 
												 AND (pjur.kdperkiraan similar to '(4|5|6|7|9)%')
												 -- AND pjur.nobukti NOT LIKE '01%' 
												 {$sql_and_nobukti} 
									 GROUP BY pjur.dk
						)t
						GROUP BY grp";
						$lr = $base->db->getOne($sql3);
						//die($sql3);
						$nama_lr = "Laba Rugi Bulan Ini";
						if ($lr < 0)
						{
							$lr = str_replace("-", "", $lr);
							$lr_debet = $lr;
							$lr_kredit = 0;
							$neraca_debet = 0;
							$neraca_kredit = $lr;
						}
						else
						{
							$lr_debet = 0;
							$lr_kredit = $lr;
							$neraca_debet = $lr;
							$neraca_kredit = 0;
						}
				//die($neraca_debet);
				$tpl->assignDynamic('row', array(
					  'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $this->format_money2($base, $neraca_debet),
						'VNERACA_K'  	=> $this->format_money2($base, $neraca_kredit),
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $this->format_money2($base, $lr_debet),
						'VRUGLAB_K'  	=> $this->format_money2($base, $lr_kredit),
				 ));
				$tpl->parseConcatDynamic('row');
			
			
				// ==== FOR EXCEL
				
				$tpl_excel->assignDynamic('row', array(
						'VCOA'  	=> '&nbsp;',
					  'VNAMA'  	=> $nama_lr,
						'VNERAWAL_D'  	=> '0',
						'VNERAWAL_K'  	=> '0',
						'VMUTASI_D'  	=> '0',
						'VMUTASI_K'  	=> '0',
						'VNERACA_D'  	=> $neraca_debet,
						'VNERACA_K'  	=> $neraca_kredit,
						'VNERCOBA_D'  	=> '0',
						'VNERCOBA_K'  	=> '0',
						'VRUGLAB_D'  	=> $lr_debet,
						'VRUGLAB_K'  	=> $lr_kredit,
				));
				$tpl_excel->parseConcatDynamic('row');
          
	  		$tot_neraca_debet = $tot_neraca_debet+$neraca_debet;
				$tot_neraca_kredit = $tot_neraca_kredit+$neraca_kredit;
				$tot_lr_debet = $tot_lr_debet+$lr_debet;
				$tot_lr_kredit = $tot_lr_kredit+$lr_kredit;
				
				$realbalend = '';
				
				$tpl->Assign(array(
					'VTOT_NERAWAL_D'  => $this->format_money2($base, $tot_nerawal_d),
					'VTOT_NERAWAL_K'  => $this->format_money2($base, $tot_nerawal_k),
					'VTOT_MUTASI_D'  => $this->format_money2($base, $tot_mutasi_debet),
					'VTOT_MUTASI_K'  => $this->format_money2($base, $tot_mutasi_kredit),
					'VTOT_NERACA_D'  => $this->format_money2($base, $tot_neraca_debet),
					'VTOT_NERACA_K'  => $this->format_money2($base, $tot_neraca_kredit),
					'VTOT_NERCOBA_D'  => $this->format_money2($base, $tot_nercoba_debet),
					'VTOT_NERCOBA_K'  => $this->format_money2($base, $tot_nercoba_kredit),
					'VTOT_RUGLAB_D'  => $this->format_money2($base, $tot_lr_debet),
					'VTOT_RUGLAB_K'  => $this->format_money2($base, $tot_lr_kredit),
						));				
				$this->_fill_static_report($base,&$tpl);
				
				$tpl->Assign(array(
					'VTHN'   	=> $ryear,
					'VBLN'  	=> $rmonth,
					'DIVNAME'		=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
					'SDATE' 	=> $startdate,
					'EDATE' 	=> $enddate,
					'SID'     => MYSID,
						));

					// ===== FOR EXCEL
					
					$tpl_excel->Assign(array(
						'VTOT_NERAWAL_D'  => $tot_nerawal_d,
						'VTOT_NERAWAL_K'  => $tot_nerawal_k,
						'VTOT_MUTASI_D'  => $tot_mutasi_debet,
						'VTOT_MUTASI_K'  => $tot_mutasi_kredit,
						'VTOT_NERACA_D'  => $tot_neraca_debet,
						'VTOT_NERACA_K'  => $tot_neraca_kredit,
						'VTOT_NERCOBA_D'  => $tot_nercoba_debet,
						'VTOT_NERCOBA_K'  => $tot_nercoba_kredit,
						'VTOT_RUGLAB_D'  => $tot_lr_debet,
						'VTOT_RUGLAB_K'  => $tot_lr_kredit,
						));				
						
					$this->_fill_static_report($base,&$tpl_excel);
					
					$tpl_excel->Assign(array(
						'VTHN'   	=> $ryear,
						'VBLN'  	=> $rmonth,
						'DIVNAME'	=> $divname, //'KDSPK'		=> $kdspk, 'NMSPK'		=> $nmspk,
						'SDATE' 	=> $startdate,
						'EDATE' 	=> $enddate,
						'SID'     => MYSID,
							));
						
				
				
		}	
			
		$dp = new dateparse();
		$nm_bulan_ = $dp->monthnamelong[$rmonth];
//		die($nm_bulan_);

				
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		
		// ====== FOR EXCEL
		
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => $ryear,
			'VTHN'  => $ryear,
			'VBLN'  => $nm_bulan_,
			'VAP'  => '',
		));
		
		// =======
		$tpl_temp->assign('ONE',$tpl,'template');
		$tpl_temp->parseConcat();
		
		$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
		$tpl_temp_excel->parseConcat();
		

		$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
		$tpl_temp->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		$tpl_temp->parseConcat();

		
		// ======= FOR EXCEL
		
		$tpl_temp_excel->Assign('ONE', '
			<div id="pr" name="pr" align="left">
			<br>
			<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
			<a href="javascript:void(0);" onclick="displayHTML(printarea.innerHTML)">Print Preview</a>
			<!--a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
			<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
								<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
			-->
			</div>'
		);
		
		$tpl_temp_excel->parseConcat();
		
		
		// =======
				
		
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
			$filename = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$ryear."_".$rmonth."_".$kdspk.".html";
			$isi = & $tpl_temp->parsedPage();
			$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;			
			
			// ==== FOR EXCEL
					
						$filename_excel = $base->kcfg['basedir']."files/"."NER_LAJ_".$kddiv."_neraca_lajur_spk_".$ryear."_".$rmonth."_".$kdspk."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
			
			// ====
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;			
		}
		
		
	}/*}}}*/

}
?>
