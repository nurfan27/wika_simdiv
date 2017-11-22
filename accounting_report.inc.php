<?php
/**
 * Showing main App accounting report
 * @author luki@karet.org
 * @last 2007-10-30
 *
*/

loadClass('dateparse');
error_reporting(E_ALL);

class accounting_report extends modules
{

    var $tpl = "";
    var $modname = "Accounting Report";
    var $pesan = "";
	
	function accounting_report(&$base,$submenu="")/*{{{*/
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
	 
   // diguanakan untuk laporan persediaan bahan
   // author      : yuniar kurniawan
   // date        : 06 maret 2008
   // modify      : 
   // modify date : 
    function sub_mainpage_persediaan_bahan($base)/*{{{*/
	{
		$tpl = $base->_get_tpl('report_mainpage_persediaan_bahan.html');
		
		//print '<pre>';
		//print_r($this->S);
		//print '</pre>';
		
		if(session_name() == 'SRM')
		{
				$tpl->assign('H1','');
				$tpl->assign('H2','none');
				$tpl->assign('H3','');
				$tpl->assign('H4','');
		}
		else	
		{
			if($this->get_var("show_tampil")=="no")
			{
				$tpl->assign('H1','none');
				$tpl->assign('H2','none');
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
			
		}		
		
		
		loadClass('modules.accounting');
		$ynow = date('Y');
		
		$start_coa = accounting::get_htmlselect_coaid($base,'coaid',$this->get_var('coaid'),true,'','');
    	$end_coa = accounting::get_htmlselect_coaid($base,'coaid2',$this->get_var('coaid2'),true,'','');

		$rep_type   = $this->get_var('rep_type','overview_ledger');
	  	
		if($rep_type=='persediaan_produk_dalam_proses')
		{
			$tujuan = 'show_report_persediaan_produk';
			$nilai_code='11421';
			$nilai_code2='AKT015G';
		}
		else if($rep_type=='persediaan_bahan')
		{
			$tujuan = 'show_report_persediaan_bahan';
			$nilai_code='11411';
			$nilai_code2='AKT015F';
		}
		else if($rep_type=='persediaan_komponen')
		{
			$tujuan = 'show_report_persediaan_komponen';
			$nilai_code='11422';
			$nilai_code2='AKT015H';
		}
		else if($rep_type=='persediaan_barang_jadi')
		{
			$tujuan = 'show_report_persediaan_barangjadi';
			$nilai_code='11433';			
			$nilai_code2='AKT015H';
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
				
		$tpl->Assign(array(
		  	'VSTGL'   	  => $this->get_var('stardate',date('1-m-Y')),
		  	'VETGL'   	  => $this->get_var('enddate',date('d-m-Y')),
		  	'SRYEAR'   	  => $ryear,
			'SRMONTH'  	  => $rmonth,
			'SPERIOD'  	  => $glperiods,
			'START_COA'	  => $start_coa,
			'END_COA' 	  => $end_coa,
			'VNILAI_KODE' => $nilai_code,
			'TUJUAN'        => $tujuan,
			'TITLE' 		=> str_replace('_',' ',$rep_type),
			'REP_TYPE' 	  => $rep_type,
			'SUB' 	      => $this->get_var('sub'),
			'DISP' 	      => $disp,
			'SID'      	  => MYSID,
		));
		
		$this->tpl = $tpl;
	
	}/*}}}*/
	
	// diguanakan untuk memilih jenis laporan
    // author      : yuniar kurniawan
    // date        : 10 maret 2008
    // modify      : 
    // modify date : 
	
	function sub_report_persediaan_bahan($base)/*{{{*/
	{
		if(session_name() == 'SRM')
			$this->get_valid_app('SRM');
		else	
			$this->get_valid_app('SDV');
		
		
		$group = $this->get_var('tbtype','none');
		
		if ($group != 'rinci')
			$this->sub_report_persediaan_bahan_ikhtisar($base);
		else
			$this->sub_report_persediaan_bahan_rinci($base);
	
	}/*}}}*/
	
	// diguanakan untuk laporan persediaan rinci (print out)
    // author      : yuniar kurniawan
    // date        : 11 maret 2008
    // modify      : 13 maret 2008
    // modify date : 
	
	function sub_report_persediaan_bahan_rinci($base)/*{{{*/
	{
		//die('testing');
		
		if(session_name() == 'SRM')
		{
			//print session_name();
			$this->get_valid_app('SRM');
			$table = "jurnal";
			$kdspk = $this->S['curr_proyek'];
			$add_sqlNew = " AND kdspk='".$kdspk."' ";
		}
		else	
		{
			//print session_name();
			$this->get_valid_app('SDV');
			$kddiv = $this->S['curr_divisi'];
			$table = "jurnal_".strtolower($kddiv);
			$add_sqlNew = " ";
		}		
		
		loadclass('dateparse');
		//$this->get_valid_app('SDV');
		//$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		//$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$ryear = $this->get_var('ryear',date('Y'));

		$tpl = $base->_get_tpl('report_persediaan_printable_rinci.html');
        $tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		
		// ===== FOR EXCEL
					
		$tpl_excel = $base->_get_tpl('report_persediaan_printable_rinci.html');
		$tpl_temp_excel = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl_excel);
		
		// ===== 
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
    						
        $startdate = $this->get_var('startdate',date('d-m-Y'));
    	
		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
		  $sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
		  $sdate = date('Y-m-d');

	
		if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $enddate, $regs))
		  $edate = $regs[3].'-'.$regs[2].'-'.$regs[1];
		else
		  $edate = date('Y-m-d');
	
	
		$code_nilai = $this->get_var('to_code');
		if($code_nilai=='11411')
			$qry='AKT015F';
		else if($code_nilai=='11421')
			$qry='AKT015G';
		else if($code_nilai=='11422')
			$qry='AKT015H';
		else if($code_nilai=='11433')
			$qry='AKT015I';	
		
				
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
				
		// ================== BEGIN DIGUNAKAN UNTUK MENGHITUNG SALDO AWAL
		
		$ryear_ = $this->get_var('ryear',date('Y'));
		$rmonth_ = $this->get_var('rmonth',date('m'));
		$tanggal_x = $ryear_.'-'.$rmonth_.'-1';

		$sql_saldo_awal = "SELECT kdsbdaya,
			 	(CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,
				(CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
			 	
				(CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit,
				(CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
				
				FROM {$table} jur
				WHERE DATE(jur.tanggal) < '$tanggal_x' 
				
				{$add_sqlNew}
				
				AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='$qry')
				GROUP BY kdsbdaya,dk ORDER BY kdsbdaya,dk";
					
										
		$array_saldo_awal = array();
		$rs3=$base->dbquery($sql_saldo_awal);
		while(!$rs3->EOF)
		{
			$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['kdsbdaya'];
			
			$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['volume_debet'];
			$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['rupiah_debet'];
			
			$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['volume_kredit'];
			$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['rupiah_kredit'];
			
			$rs3->movenext();
		}
		
		
		// ===================== END 
			
						
		// ================== BEGIN DIGUNAKAN UNTUK VOLUME ATAU HARGA PER NOMER BUKTI
		
		$ryear_ = $this->get_var('ryear',date('Y'));
		$rmonth_ = $this->get_var('rmonth',date('m'));
		$tanggal_x = $ryear_.'-'.$rmonth_.'-1';

		$sql_saldo_now = "SELECT kdsbdaya,nobukti,keterangan,to_char(date(tanggal),'DD-MM-YYYY') AS tanggal_f,
						 	(CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,
							(CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 	
							(CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit,
							(CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit
							
							FROM {$table} jur
							WHERE TRUE
							
							AND 
							( 
									DATE_PART ('YEAR',jur.tanggal) = '$thn_'
									AND DATE_PART ('MONTH',jur.tanggal) = '$bln_' 
							)
							
							{$add_sqlNew}
							
							-- AND jur.kdperkiraan='$code_nilai'
							AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='$qry')
							GROUP BY kdsbdaya,dk,jur.nobukti,jur.keterangan,jur.tanggal ORDER BY kdsbdaya,dk";
					
		$array_saldo_now = array();
		$rs4=$base->dbquery($sql_saldo_now);
		while(!$rs4->EOF)
		{
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['kdsbdaya'];
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['nobukti'];
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['keterangan'];
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['tanggal_f'];
									
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['volume_debet'];
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['rupiah_debet'];
			
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['volume_kredit'];
			$array_saldo_now[$rs4->fields['kdsbdaya']][]=$rs4->fields['rupiah_kredit'];
			
			$rs4->movenext();
		}
					
						
		$sql="SELECT jur.kdperkiraan,jur.kdsbdaya,per.nmsbdaya,COUNT(jur.kdperkiraan) AS couter_loop FROM {$table} jur left join dsbdaya per
                ON (per.kdsbdaya=jur.kdsbdaya) WHERE TRUE
                AND 
                ( 
                	DATE_PART ('YEAR',jur.tanggal) = '$thn_'
                	AND DATE_PART ('MONTH',jur.tanggal) = '$bln_' 
                )
                
                --AND jur.kddivisi = '{}'
                
                AND isdel = 'f' 
                -- AND jur.kdperkiraan = '$code_nilai'
                
                {$add_sqlNew}
                
                AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='$qry')
                GROUP BY jur.kdsbdaya,per.nmsbdaya,jur.kdperkiraan
                ORDER BY jur.kdsbdaya";
	
		
        //die($sql);
        $rs2 = $base->dbquery($sql);
        if ($rs2->EOF)
        {
            $tpl->Assign('row','');
            $tpl_excel->Assign('row','');
        }
        else
        {

			$tpl->Assign(array(
				'VTHN'  		=> $ryear,
				'VBLN'  		=> $rmonth,
				'SDATE' 		=> $startdate,
				'EDATE' 		=> $enddate,
				'DIVNAME'		=> $divname,
				'SID'      	=> MYSID,
				'VCURR'     => '',
				'VKODE'  => $rs2->fields['kdsbdaya'],
				'VNAMA_SUMBER'  => $rs2->fields['nmsbdaya'],
				'VTAHAP'  => $rs2->fields['kdtahap'],
			));
					
				
			// ====== FOR EXCEL
					
			$tpl_excel->Assign(array(
				'VTHN'      	=> $ryear,
				'VBLN'  		=> $rmonth,
				'SDATE' 		=> $startdate,
				'EDATE' 		=> $enddate,
				'DIVNAME'		=> $divname,
				'SID'      		=> MYSID,
				'VCURR'     	=> '',
				'VKODE'     	=> $rs2->fields['kdsbdaya'],
				'VNAMA_SUMBER'  => $rs2->fields['nmsbdaya'],
				'VTAHAP'    	=> $rs2->fields['kdtahap'],
            ));						
            // ======					
            	
			$tmp_nobukti = '';
			$nobukti_tmp = '';
			
			$temporer='r6e56356';
			$kdsbdaya_tmp='';
	
			
			$VVMSK=0;
			$VHMSK=0;
			$VVKEL=0;
			$VHKEL=0;
			$VVSISA=0;
			$VHSISA=0;
					
		
            while (!$rs2->EOF)
            {
                $kdsbdaya_tmp=$rs2->fields['kdsbdaya'];
				if ($temporer != $kdsbdaya_tmp)
				{
					$tpl->defineDynamicBlock(array('row'));
					// ====== FOR EXCEL
							$tpl_excel->defineDynamicBlock(array('row'));
					// ======
										
					$counter = count($array_saldo_awal[$rs2->fields['kdsbdaya']]);
					
					if($counter==5)
					{
    					$VVMSK = $array_saldo_awal[$rs2->fields['kdsbdaya']][1];
    					$VHMSK = $array_saldo_awal[$rs2->fields['kdsbdaya']][2];
    					$VVKEL = $array_saldo_awal[$rs2->fields['kdsbdaya']][3];
    					$VHKEL = $array_saldo_awal[$rs2->fields['kdsbdaya']][4];
											
					}
					else if($counter==10)
					{
						$VVMSK = $array_saldo_awal[$rs2->fields['kdsbdaya']][1] + $array_saldo_awal[$rs2->fields['kdsbdaya']][6];
						$VHMSK = $array_saldo_awal[$rs2->fields['kdsbdaya']][2] + $array_saldo_awal[$rs2->fields['kdsbdaya']][7];
						$VVKEL = $array_saldo_awal[$rs2->fields['kdsbdaya']][3] + $array_saldo_awal[$rs2->fields['kdsbdaya']][8];
						$VHKEL = $array_saldo_awal[$rs2->fields['kdsbdaya']][4] + $array_saldo_awal[$rs2->fields['kdsbdaya']][9];
					}
					
					$temporer = $rs2->fields['kdsbdaya'];
																							
					$VVSISA = $VVMSK - $VVKEL;
					$VHSISA = $VHMSK - $VHKEL;
					
					
					$string4=strval($VVKEL);
					if(ereg('([.])',$string4))
						$VVKEL2=$VVKEL.'000';
					else 
						$VVKEL2=$VVKEL.'.00000';
					
					
					$string5=strval($VVMSK);
					if(ereg('([.])',$string5))
						$VVMSK2=$VVMSK.'000';
					else 
						$VVMSK2=$VVMSK.'.00000';
					
											
					$string6=strval($VVSISA);
					if(ereg('([.])',$string6))
						$VVSISA2=$VVSISA.'000';
					else 
						$VVSISA2=$VVSISA.'.00000';
										
					
					// ======= FOR EXCEL
					$tpl_excel->assign(array(
						'VVMSK'=>$VVMSK2,
						'VHMSK'=>$VHMSK,
						'VVKEL'=>$VVKEL2,
						'VHKEL'=>$VHKEL,
						'VVSISA'=>$VVSISA2,
						'VHSISA'=>$VHSISA,
                    ));
					
					// =======
					
					// hasil kalkulasi
					$VHSISA = $this->format_money2($base,$VHSISA);
																
					// format money
					$VHMSK2 = $this->format_money2($base,$VHMSK);
					$VHKEL2 = $this->format_money2($base,$VHKEL);
								
					$tpl->assign(array(
							'VVMSK'=>$VVMSK2,
							'VHMSK'=>$VHMSK2,
							'VVKEL'=>$VVKEL2,
							'VHKEL'=>$VHKEL2,
							'VVSISA'=>$VVSISA2,
							'VHSISA'=>$VHSISA,
                    ));						
				}
					 			
				
				$counter_loop = $rs2->fields['couter_loop'];
				
				$my_handle=1;
				$buf=1; // buffer untuk nobukti
				$buf2=2; // buffer untuk keterangan
				$buf3=3; // buffer untuk tanggal
				
				// variabel untuk harga dan volume
				
				$buf4=4; // buffer untuk volume debet
				$buf5=5; // buffer untuk harga debet
				$buf6=6; // buffer untuk volume kredit
				$buf7=7; // buffer untuk harga kredit
				
				$total_vol_masuk=0.00000;
				$total_harga_masuk=0;
				$total_vol_keluar=0.00000;
				$total_harga_keluar=0;
				
				$total_sisa_1=0.00000;
				$total_sisa_2=0;
				$harga_masuk=0;
						
				for($ab=0;$ab<$counter_loop;$ab+=1)
				{
					if($my_handle==1)
					{
						$nobukti_ 	= $array_saldo_now[$rs2->fields['kdsbdaya']][$buf];
						$keterangan = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf2];
						$tanggal_ 	= $array_saldo_now[$rs2->fields['kdsbdaya']][$buf3];
						
						$vol_masuk 	= $array_saldo_now[$rs2->fields['kdsbdaya']][$buf4];
						$harga_masuk = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf5];
						$vol_keluar = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf6];
						$harga_keluar = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf7];
					}
					else
					{
						$nobukti_ = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf];
						$keterangan = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf2];
						$tanggal_ = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf3];
						
						$vol_masuk = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf4];
						$harga_masuk = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf5];
						
						$vol_keluar = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf6];
						$harga_keluar = $array_saldo_now[$rs2->fields['kdsbdaya']][$buf7];
					}
						
						
					$sisa_1 = $vol_masuk - $vol_keluar;
					$sisa_2 = $harga_masuk - $harga_keluar;
                    	
					// ========= perhitungan total
    				$total_vol_masuk+=$vol_masuk;
    				$total_harga_masuk+=$harga_masuk;
    				
    				$total_vol_keluar+=$vol_keluar;
    				$total_harga_keluar+=$harga_keluar;
    		
    				$total_sisa_1+=$sisa_1;
    				$total_sisa_2+=$sisa_2;
					// =========
                    
					$string=strval($sisa_1);
			
					if(ereg('([.])',$string))
						$sisa_1=$sisa_1.'000';
					else 
						$sisa_1=$sisa_1.'.00000';
					
					
					$hasil_=$this->_setVolume($base,$vol_masuk);
					$hasil_2=$this->_setVolume($base,$vol_keluar);
					$hasil_3=$this->_setVolume($base,$sisa_1);
						
							
					$tpl->assignDynamic('row', array(
						'VTGL'  	=> $tanggal_,
						'VVMK'    => (empty($hasil_)||($hasil_==0))?'0.00000':$hasil_,
						'VHMK'  	=> $this->format_money2($base,$harga_masuk),
						'VVKK'  	=> (empty($hasil_2)||($hasil_2==0))?'0.00000':$hasil_2,
						'VHKK'  	=> $this->format_money2($base,$harga_keluar),
						'VSISA1'  => (empty($hasil_3)||($hasil_3==0))?'0.00000':$hasil_3,
						'VSISA2'  => $this->format_money2($base,$sisa_2),
						'VURAIAN' => $keterangan,
						'VBUKTI'  => $nobukti_,
					));
				
					$tpl->parseConcatDynamic('row');
										
					// ======= FOR EXCEL
					
					$tpl_excel->assignDynamic('row', array(
						'VTGL'  	=> $tanggal,
						'VVMK'    => (empty($hasil_)||($hasil_==0))?'0.00000':$hasil_,
						'VHMK'  	=> $harga_masuk,
						'VVKK'  	=> (empty($hasil_2)||($hasil_2==0))?'0.00000':$hasil_2,
						'VHKK'  	=> $harga_keluar,
						'VSISA1'  => (empty($hasil_3)||($hasil_3==0))?'0.00000':$hasil_3,
						'VSISA2'  => $sisa_2,
						'VURAIAN' => $keterangan,
						'VBUKTI'  => $nobukti,
					));
	
					$tpl_excel->parseConcatDynamic('row');

					$my_handle=2;
					$buf=$buf+8;
					$buf2=$buf2+8;
					$buf3=$buf3+8;
					
					$buf4=$buf4+8;
					$buf5=$buf5+8;
					$buf6=$buf6+8;
					$buf7=$buf7+8;			
					
					// =======
					
				}
					
						
				$total_vol_masuk = $total_vol_masuk + $VVMSK;
				$string=strval($total_vol_masuk);
				if(ereg('([.])',$string))
					$total_vol_masuk=$total_vol_masuk.'000';
				else 
					$total_vol_masuk=$total_vol_masuk.'.00000';
										  
			
				$total_vol_keluar = $total_vol_keluar + $VVKEL;
				$string2=strval($total_vol_keluar);
				if(ereg('([.])',$string2))
					$total_vol_keluar=$total_vol_keluar.'000';
				else 
					$total_vol_keluar=$total_vol_keluar.'.00000';
			
									          	
				$total_sisa_1=$total_sisa_1 + $VVSISA;
				$string3=strval($total_sisa_1);
				if(ereg('([.])',$string3))
					$total_sisa_1=$total_sisa_1.'000';
				else 
					$total_sisa_1=$total_sisa_1.'.00000';
				
									
				$total_harga_masuk = $total_harga_masuk + $VHMSK;
				$total_harga_keluar = $total_harga_keluar + $VHKEL;
				$total_sisa_2 = $total_sisa_2 + $VHSISA;
						
	        	$rs2->moveNext();
          
                if ($rs2->EOF)
                {
                    $realbalend = $balend;
													
					$tpl->Assign(array(
							'TVMSK' 	=> $total_vol_masuk,
							'THMSK' 	=> $this->format_money2($base,$total_harga_masuk),
							'TVKEL' 	=> $total_vol_keluar,
							'THKEL' 	=> $this->format_money2($base,$total_harga_keluar),
							'TSISA1' 	=> $total_sisa_1 ,
							'TSISA2' 	=> $this->format_money2($base,$total_sisa_2),
					));
					$tpl_temp->assign('ONE',$tpl,'template');
					$tpl_temp->parseConcat();
                    
					// ====== FOR EXCEL
							
					$tpl_excel->Assign(array(
						'TVMSK' 	=> $total_vol_masuk,
						'THMSK' 	=> $total_harga_masuk,
						'TVKEL' 	=> $total_vol_keluar,
						'THKEL' 	=> $total_harga_keluar,
						'TSISA1' 	=> $total_sisa_1,
						'TSISA2' 	=> $total_sisa_2,
					));
						
					$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
					$tpl_temp_excel->parseConcat();
							// ======
                }
                else
                {
                    $realbalend = '';
						
					if ($curr_coa != $rs2->fields['kdperkiraan'] || $curr_thp != $rs2->fields['kdtahap'] )
					{
						//parsing total debet/kreditnya sebelum diparsing datanya
						$tpl->Assign(array(
							'TVMSK' 	=> $total_vol_masuk,
							'THMSK' 	=> $this->format_money2($base,$total_harga_masuk),
							'TVKEL' 	=> $total_vol_keluar,//round($total_vol_keluar,4),
							'THKEL' 	=> $this->format_money2($base,$total_harga_keluar),
							'TSISA1' 	=> $total_sisa_1,
							'TSISA2' 	=> $this->format_money2($base,$total_sisa_2),
						));
								
						$tpl_temp->assign('ONE',$tpl,'template');
						$tpl_temp->parseConcat();
						$tpl = $base->_get_tpl('report_persediaan_printable_rinci.html');
						$this->_fill_static_report($base,&$tpl);
													
						$tpl->Assign(array(
							'VTHN'   		=> $ryear,
							'VBLN'  		=> $rmonth,
							'VNAMA_SUMBER'	=> $rs2->fields['nmsbdaya'],
							'SDATE' 		=> $startdate,
							'EDATE' 		=> $enddate,
							'SID'     		=> MYSID,
							'VCURR'   		=> '',
							'DIVNAME'		=> $divname . $no,
							'VKODE'  		=> $rs2->fields['kdsbdaya'],
							'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
							'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
							'VTAHAP'  		=> $rs2->fields['kdtahap'],
						));		
						// ===== FOR EXCEL	
						$tpl_excel->Assign(array(
							'TVMSK' 	=> $total_vol_masuk,
							'THMSK' 	=> $total_harga_masuk,
							'TVKEL' 	=> $total_vol_keluar,
							'THKEL' 	=> $total_harga_keluar,
							'TSISA1' 	=> $total_sisa_1,
							'TSISA2' 	=> $total_sisa_2,
						));
									
						$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
						$tpl_temp_excel->parseConcat();
						$tpl_excel = $base->_get_tpl('report_persediaan_printable_rinci.html');
						$this->_fill_static_report($base,&$tpl_excel);
						
						$tpl_excel->Assign(array(
							'VTHN'   		=> $ryear,
							'VBLN'  		=> $rmonth,
							'VNAMA_SUMBER'	=> $rs2->fields['nmsbdaya'],
							'SDATE' 		=> $startdate,
							'EDATE' 		=> $enddate,
							'SID'     		=> MYSID,
							'VCURR'   		=> '',
							'DIVNAME'		=> $divname,
							'VKODE'  		=> $rs2->fields['kdsbdaya'],
							'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
							'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
							'VTAHAP'  		=> $rs2->fields['kdtahap'],
						));
						// ======
					}
				} // END OF IF
		  
            } // END OF WHILE
      
        } // END OF IF
	  

		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => '',
			'VTHN'  => '',
			'VBLN'  => '',
			'VAP'  => '',
		));
		
		// ===== FOR EXCEL
		$tpl_excel->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => '',
			'VTHN'  => '',
			'VBLN'  => '',
			'VAP'  => '',
		));
				
		
		// =====
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
		
		
		// ==== FOR EXCEL
		
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
		
		
		// =====
					
				
		$is_proses = $this->get_var('is_proses');
		$divname = str_replace(" ","_",$divname);
		if($is_proses=='t')
		{
			//die('Kode Nilai : ' . $code_nilai);
			if($code_nilai=='11411')
			{
				
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_bahan_rinci_".$ryear."_".$rmonth."_.html";
				//die('File name : ' . $filename);
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
				$this->tpl =& $tpl_temp;
				
				// ==== FOR EXCEL
						
						$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_bahan_rinci_".$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
						
				// ====
				
			}
			else if($code_nilai=='11421')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_rinci_'.$ryear."_".$rmonth."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);	
			$this->tpl =& $tpl_temp;
				
				// ==== FOR EXCEL

						$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_rinci_'.$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);	
				
				// =====
										
			}
			else if($code_nilai=='11422')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_komponen_rinci_'.$ryear."_".$rmonth."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			$this->tpl =& $tpl_temp;
				
				// ===== FOR EXCEL
				
							$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_komponen_rinci_'.$ryear."_".$rmonth."_for_excel.html";
							$isi2 = & $tpl_temp_excel->parsedPage();
							$this->cetak_to_file($base,$filename_excel,$isi2);
				
				// =====
				
			}
			else if($code_nilai=='11433')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_barangjadi_rinci_'.$ryear."_".$rmonth."_.html";
				$isi = & $tpl_temp_excel->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);			
			$this->tpl =& $tpl_temp;
				
				// ===== FOR EXCEL
				
						$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_barangjadi_rinci_'.$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
						
				// =====
				
			}
			
		}
		else
		{
			$this->tpl =& $tpl_temp;
			$this->tpl_excel =& $tpl_temp_excel;
		}
		
	}/*}}}*/ 
	
	// diguanakan untuk laporan persediaan ikhtisar (print out)
    // author      : yuniar kurniawan
    // date        : 10 maret 2008
    // modify      : yuniar , penambahan file untuk save excel
    // modify date : 02 April 2008
	
	function sub_report_persediaan_bahan_ikhtisar($base)/*{{{*/
	{
		loadclass('dateparse');
	  //$base->db->debug=true;	
		if(session_name() == 'SRM')
		{
			$this->get_valid_app('SRM');
			$table = "jurnal";
			$kdspk = $this->S['curr_proyek'];
			$add_sqlNew = " AND kdspk='".$kdspk."' ";
		}
		else	
		{
			$this->get_valid_app('SDV');
			$kddiv = $this->S['curr_divisi'];
			$table = "jurnal_".strtolower($kddiv);
			$add_sqlNew = " ";
		}		
		
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		
		$ryear = $this->get_var('ryear',date('Y'));
		$group = $this->get_var('tbtype','none');

		$tpl = $base->_get_tpl('report_persediaan_bahan_printable_ikhtisar.html');
        $tpl->defineDynamicBlock('row');
		$tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
				
		// ====== FOR EXCEL
        $tpl_excel = $base->_get_tpl('report_persediaan_bahan_printable_ikhtisar.html');
        $tpl_excel->defineDynamicBlock('row');
        $tpl_temp_excel = $base->_get_tpl('one_var.html');
        $this->_fill_static_report($base,&$tpl_excel);
		// ====== 
		
		$ryear_ = $this->get_var('ryear',date('Y'));
		$rmonth_ = $this->get_var('rmonth',date('m'));	// AKT015B
		
		$sql_perk = ''; //sql_perk digunakan untuk penampung filter no_perk yang dicari
		$sql_perk_min = ''; //sql_perk_min digunakan untuk penampung filter no_perk yang akan diabaikan
    	$startdate = $this->get_var('startdate',date('d-m-Y'));
    	$enddate = $this->get_var('enddate',date('d-m-Y'));

    	if (eregi("([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})", $startdate, $regs))
			{
      		$sdate = $regs[3].'-'.$regs[2].'-'.$regs[1];
					$thn_ = $regs[3];
					$bln_ = $regs[2];
					$hr_ = $regs[1];
			}
			else
      		$sdate = date('Y-m-d');

				   
	    $code_nilai = $this->get_var('to_code');
			if($code_nilai=='11411')
				$qry='AKT015F';
			else if($code_nilai=='11421')
				$qry='AKT015G';
			else if($code_nilai=='11422')
				$qry='AKT015H';
			else if($code_nilai=='11433')
				$qry='AKT015I';	
					
			
			$rmonth2_=0;
			$ryear2_=$ryear_;
			if($rmonth_=='12')
			{
				$rmonth2_='01';
				$ryear2_+=1;		
	    }
			else
				$rmonth2_=$rmonth_+1;
				
				
				$tanggal_x2 = $ryear2_.'-'.$rmonth2_.'-1';
				$tanggal_x = $ryear_.'-'.$rmonth_.'-1';	
		
		// ================== BEGIN DIGUNAKAN UNTUK MENGHITUNG SALDO AWAL
		
					$sql_saldo_awal = "SELECT kdsbdaya,
						 (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						 (CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,
			
						 (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit,
						 (CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit
			
			FROM {$table} jur
			WHERE DATE(jur.tanggal) < '".$tanggal_x."' 
			
			--AND jur.kdperkiraan='".$code_nilai."'
			
			{$add_sqlNew}
			
			AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='".$qry."')
			
			GROUP BY kdsbdaya,dk
			ORDER BY kdsbdaya,dk";
					
					$array_saldo_awal = array();
					$rs3=$base->dbquery($sql_saldo_awal);
					while(!$rs3->EOF)
					{
						$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['kdsbdaya'];
						$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['rupiah_debet'];
						$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['volume_debet'];
						$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['rupiah_kredit'];
						$array_saldo_awal[$rs3->fields['kdsbdaya']][]=$rs3->fields['volume_kredit'];
						
						$rs3->movenext();
		}
		
		/*
		print '<pre>';
		print_r($array_saldo_awal);
		print '</pre>';
		
		die('SALDO AWAL');
		*/
		// === END 
			
		$sq_="		SELECT  jur.kdsbdaya,
						
						(CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
						(CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,
						(CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit,
						(CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit

						FROM {$table} jur LEFT JOIN dsbdaya sbdy ON (jur.kdsbdaya=sbdy.kdsbdaya)
						WHERE TRUE 
							AND DATE_PART ('YEAR',jur.tanggal) = '$ryear_' 
							AND DATE_PART('MONTH',jur.tanggal) = '$rmonth_' 
							
							$add_sqlNew
							
							AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='$qry')
							
							AND jur.isdel='f'
       				GROUP BY jur.kdsbdaya,sbdy.nmsbdaya,jur.dk";
		
		//-- AND 	jur.kdperkiraan='".$code_nilai."'
		
		$array_saldo_now2 = array();
		$rs_=$base->dbquery($sq_);
		while(!$rs_->EOF)
		{
				$array_saldo_now2[$rs_->fields['kdsbdaya']][]=$rs_->fields['kdsbdaya'];
				$array_saldo_now2[$rs_->fields['kdsbdaya']][]=$rs_->fields['rupiah_debet'];
				$array_saldo_now2[$rs_->fields['kdsbdaya']][]=$rs_->fields['volume_debet'];
				$array_saldo_now2[$rs_->fields['kdsbdaya']][]=$rs_->fields['rupiah_kredit'];
				$array_saldo_now2[$rs_->fields['kdsbdaya']][]=$rs_->fields['volume_kredit'];
						
				$rs_->movenext();
		}
		
		/*			
		print $sq_;
		
		print '<pre>';
		print_r($array_saldo_now2);
		print '</pre>';
		
		die('BULAN SEKARANG');
		*/
		
		// old sql
		/*
		$sql = "SELECT t.kdsbdaya,t.nmsbdaya
       ,(SUM(rupiah_debet)-SUM(rupiah_kredit)) AS saldo_rp
       ,(SUM(volume_debet)-SUM(volume_kredit)) AS saldo_vol
			FROM
(

SELECT  jur.kdsbdaya,sbdy.nmsbdaya,

        (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
        (CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,

        (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit,
        (CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit

        FROM {$table} jur LEFT JOIN dsbdaya sbdy ON
(jur.kdsbdaya=sbdy.kdsbdaya)

WHERE TRUE

      AND DATE(jur.tanggal)< '".$tanggal_x2."' 
			
			-- AND jur.kdperkiraan='".$code_nilai."'
      
			AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='".$qry."')
			
			{$add_sqlNew}
			
			AND jur.isdel='f'

GROUP BY jur.kdsbdaya,sbdy.nmsbdaya,jur.dk
ORDER BY jur.kdsbdaya,sbdy.nmsbdaya
)t

GROUP BY t.kdsbdaya,t.nmsbdaya
ORDER BY t.kdsbdaya,t.nmsbdaya";
		
		*/
		
		
		$sql=" SELECT t.kdsbdaya,t.nmsbdaya
   
       ,(SUM(
             
             (CASE WHEN rupiah_debet IS NULL THEN 0
                   ELSE
                   rupiah_debet END))

       -

       SUM(

           (CASE WHEN rupiah_kredit IS NULL THEN 0
                 ELSE
                 rupiah_kredit END)

       )) AS saldo_rp



       ,(SUM(

             (CASE WHEN volume_debet IS NULL THEN 0
                   ELSE
                   volume_debet END))

       -

       SUM(

           (CASE WHEN volume_kredit IS NULL THEN 0
                 ELSE
                 volume_kredit END)

       )) AS saldo_vol


	FROM
	(
	SELECT  jur.kdsbdaya,sbdy.nmsbdaya,

	    (CASE WHEN dk='D' THEN SUM(rupiah) END) AS rupiah_debet,
	    (CASE WHEN dk='D' THEN SUM(volume) END) AS volume_debet,

	    (CASE WHEN dk='K' THEN SUM(rupiah) END) AS rupiah_kredit,
	    (CASE WHEN dk='K' THEN SUM(volume) END) AS volume_kredit

	    FROM jurnal_o jur LEFT JOIN dsbdaya sbdy ON
	(jur.kdsbdaya=sbdy.kdsbdaya)

	WHERE TRUE

	  AND DATE(jur.tanggal)< '$tanggal_x2'

	  			AND jur.kdperkiraan IN (SELECT kdperkiraan FROM report_reff WHERE kdreport='$qry')
			
			AND jur.isdel='f'

	GROUP BY jur.kdsbdaya,sbdy.nmsbdaya,jur.dk
	ORDER BY jur.kdsbdaya,sbdy.nmsbdaya
	)t

	GROUP BY t.kdsbdaya,t.nmsbdaya
	ORDER BY t.kdsbdaya,t.nmsbdaya

		";
		
		//die($sql);
				
		$rs2=$base->dbquery($sql);
			
						
		if ($rs2->EOF)
		{
        	$tpl->Assign('row','');
					$tpl_excel->Assign('row','');
    }
		else
    {

				$tpl->Assign(array(
					'VTHN'   => $ryear_,
					'VBLN'  => $rmonth_,
					'DIVNAME'		=> $divname,
					'SDATE' => $startdate,
					'EDATE' => $enddate,
					'SID'      => MYSID,
					'VCURR'      => '',
				));
				
				
				// ===== FOR EXCEL
				
						$tpl_excel->Assign(array(
							'VTHN'   => $ryear_,
							'VBLN'  => $rmonth_,
							'DIVNAME'		=> $divname,
							'SDATE' => $startdate,
							'EDATE' => $enddate,
							'SID'      => MYSID,
							'VCURR'      => '',
						));
				
				// =====

        $curr_coa = '';
        $curr_thp = '';
				
				$total_volume_saldo=0;
				$total_harga_saldo=0;
				
				$total_harga_mutasi_masuk=0;
				$total_vol_mutasi_masuk=0;
				
				$total_vol_mutasi_kredit=0;
				$total_harga_mutasi_kredit=0;
				
				$total_volume_saldo_akhir=0;
				$total_harga_saldo_akhir=0;
				
				$total_rata=0;
		
		
		
				// total semua
				$THAK=0;
				$TMMH=0;
				$TMKH=0;
				$TSKH=0;
							
		
		
		$no=0;
		while(!$rs2->EOF)
		{
			
			// kalkulasi saldo awal per kode sumber daya
			// untuk 
			$total_harga_saldo = 0;
			$total_vol_saldo = 0;
			
			$total_harga_mutasi_masuk = 0;
			$total_harga_mutasi_kredit = 0;
			
			$total_vol_mutasi_masuk = 0;
			$total_vol_mutasi_kredit = 0;
			
			$total_vol_masuk = 0;
			$total_vol_kreidt = 0;
			
			$tmp_my = $array_saldo_awal[$rs->fields['kdsbdaya']];
			if($tmp_my=='')
			{
				$total_vol_saldo = '0.00000'; 
				$total_harga_saldo = '0';			
			}
			else
			{
				$counter2 = count($array_saldo_awal[$rs2->fields['kdsbdaya']]);
				if($counter2==5)
				{
					$total_harga_saldo = $array_saldo_awal[$rs2->fields['kdsbdaya']][1] - $array_saldo_awal[$rs2->fields['kdsbdaya']][3];
					$total_vol_saldo = $array_saldo_awal[$rs2->fields['kdsbdaya']][2] - $array_saldo_awal[$rs2->fields['kdsbdaya']][4];							
				}
				else if($counter2>=6)
				{
					$total_harga_saldo = ($array_saldo_awal[$rs2->fields['kdsbdaya']][1] + $array_saldo_awal[$rs2->fields['kdsbdaya']][6]) - ($array_saldo_awal[$rs2->fields['kdsbdaya']][3] + $array_saldo_awal[$rs2->fields['kdsbdaya']][8]);
								
					$total_vol_saldo = ($array_saldo_awal[$rs2->fields['kdsbdaya']][2] + $array_saldo_awal[$rs2->fields['kdsbdaya']][7]) - ($array_saldo_awal[$rs2->fields['kdsbdaya']][4] + $array_saldo_awal[$rs2->fields['kdsbdaya']][9]);	
																
				}
				
						
				
				$counter = count($array_saldo_now2[$rs2->fields['kdsbdaya']]);
				if($counter==5)
				{
						$total_harga_mutasi_masuk = $array_saldo_now2[$rs2->fields['kdsbdaya']][1];
						$total_vol_mutasi_masuk = $array_saldo_now2[$rs2->fields['kdsbdaya']][2];
						
						$total_harga_mutasi_kredit = $array_saldo_now2[$rs2->fields['kdsbdaya']][3];		
						$total_vol_mutasi_kredit = $array_saldo_now2[$rs2->fields['kdsbdaya']][4];
						
				}
				else if($counter>=6)
				{
						$total_harga_mutasi_masuk = $array_saldo_now2[$rs2->fields['kdsbdaya']][1] + $array_saldo_now2[$rs2->fields['kdsbdaya']][6];
						$total_vol_mutasi_masuk = $array_saldo_now2[$rs2->fields['kdsbdaya']][2]  + $array_saldo_now2[$rs2->fields['kdsbdaya']][7];
						
						
						
						$total_harga_mutasi_kredit = $array_saldo_now2[$rs2->fields['kdsbdaya']][3] + $array_saldo_now2[$rs2->fields['kdsbdaya']][8];
						$total_vol_mutasi_kredit = $array_saldo_now2[$rs2->fields['kdsbdaya']][4] + $array_saldo_now2[$rs2->fields['kdsbdaya']][9];
												
				}				
								
			}
			
			
						
			$total_harga_saldo_akhir = $total_harga_saldo + $total_harga_mutasi_masuk - $total_harga_kredit;
			$total_volume_saldo_akhir = $total_vol_saldo + $total_vol_mutasi_masuk - $total_vol_mutasi_kredit;
			
			if($total_volume_saldo_akhir==0)
				$rata = 0;
			else
				$rata = $total_harga_saldo_akhir / $total_volume_saldo_akhir;
					
			
			$THAK+=$total_harga_saldo;			
			$TMMH+=$total_harga_mutasi_masuk;
			$TMKH+=$total_harga_mutasi_kredit;
			$TSKH+=$total_harga_saldo_akhir;
			
					
			$hasil_=$this->_setVolume($base,$total_vol_saldo);
			$hasil_2=$this->_setVolume($base,$total_vol_mutasi_masuk);
			$hasil_3=$this->_setVolume($base,$total_vol_mutasi_kredit);
			$hasil_4=$this->_setVolume($base,$rs2->fields['saldo_vol']);
			$hasil_4=$rs2->fields['saldo_vol'];
									
			$tpl->assignDynamic('row', array(
				  'VKODE'  	=> (empty($rs2->fields['kdsbdaya']))?'-':$rs2->fields['kdsbdaya'],
				  'VNAMA'  	=> (empty($rs2->fields['nmsbdaya']))?'N/A':$rs2->fields['nmsbdaya'],
				  'VSAT'		=> '&nbsp;',
				  'VSAW'    => (empty($hasil_)||($hasil_==0))?'0.00000':$hasil_,
				  'VHAK'		=> (empty($total_harga_saldo))?'0':$this->format_money2($base,$total_harga_saldo),
				  'VMMV'  	=> $total_vol_mutasi_masuk,//(empty($hasil_2)||($hasil_2==0))?'0.00000':$hasil_2,
				  'VMMH' 		=> (empty($total_harga_mutasi_masuk))?'0':$this->format_money2($base,$total_harga_mutasi_masuk),
				  'VMKV'  	=> (empty($hasil_3)||($hasil_3==0))?'0.00000':$hasil_3,
				  'VMKH'  	=> (empty($total_harga_mutasi_kredit))?'0':$this->format_money2($base,$total_harga_mutasi_kredit),
				  'VSKV'  	=> (empty($hasil_4)||($hasil_4==0))?'0.00000':$hasil_4,
				  'VSKH' 		=> (empty($rs2->fields['saldo_rp']))?'0':$this->format_money2($base,$rs2->fields['saldo_rp']),
				  'VRATA'		=> (empty($rata))?'0':$this->format_money2($base,$rata),
			 				));

			$tpl->parseConcatDynamic('row');
			
			
			// ==== FOR EXCEL
					
						$tpl_excel->assignDynamic('row', array(
								'VKODE'  	=> (empty($rs2->fields['kdsbdaya']))?'-':$rs2->fields['kdsbdaya'],
								'VNAMA'  	=> (empty($rs2->fields['nmsbdaya']))?'N/A':$rs2->fields['nmsbdaya'],
								'VSAT'		=> '&nbsp;',
								'VSAW'    => (empty($hasil_)||($hasil_==0))?'0.00000':$hasil_,
								'VHAK'		=> (empty($total_harga_saldo))?'0':$total_harga_saldo,
								'VMMV'  	=> (empty($hasil_2)||($hasil_2==0))?'0.00000':$hasil_2,
								'VMMH' 		=> (empty($total_harga_mutasi_masuk))?'0':$total_harga_mutasi_masuk,
								'VMKV'  	=> (empty($hasil_3)||($hasil_3==0))?'0.00000':$hasil_3,
								'VMKH'  	=> (empty($total_harga_mutasi_kredit))?'0':$total_harga_mutasi_kredit,
								'VSKV'  	=> (empty($hasil_4)||($hasil_4==0))?'0.00000':$hasil_4,
								'VSKH' 		=> (empty($total_harga_saldo_akhir))?'0':$total_harga_saldo_akhir,
								'VRATA'		=> (empty($rata))?'0':$rata,
										));

						$tpl_excel->parseConcatDynamic('row');		
			
			// ====
                    		
	        $rs2->moveNext();
          
		  
          if ($rs2->EOF)
          {
		  				
            	$realbalend = $balend;
							$tpl->Assign(array(
								'THAK' => $this->format_money2($base,$THAK),
								'TMMH' => $this->format_money2($base,$TMMH),
								'TMKH' => $this->format_money2($base,$TMKH),
								'TSKH' => $this->format_money2($base,$TSKH),								
									));
							
							
							$tpl_temp->assign('ONE',$tpl,'template');
							$tpl_temp->parseConcat();
					
					
							// ==== FOR EXCEL
								
								$tpl_excel->Assign(array(
									'THAK' => $THAK,									
									'TMMH' => $TMMH,									
									'TMKH' => $TMKH,									
									'TSKH' => $TSKH,									
											));
				
								$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
    						$tpl_temp_excel->parseConcat();
					
					
					// ===== 
					
							
 	      }
          else
          {
           		$realbalend = '';
							if ($curr_coa != $rs2->fields['kdperkiraan'] )
							{
								//parsing total debet/kreditnya sebelum diparsing datanya
								$tpl->Assign(array(
									'VTOTDEBET'  => $this->format_money2($base, $curr_debet),
									'VTOTKREDIT' => $this->format_money2($base, $curr_kredit),
									'VTOTAWAL'   => $this->format_money2($base, $curr_saldoawal),
									'VTOTAKHIR'  => $this->format_money2($base, $curr_saldoakhir),
									
										));
					
										$tpl_temp->assign('ONE',$tpl,'template');
										$tpl_temp->parseConcat();
										$tpl = $base->_get_tpl('report_persediaan_bahan_printable_ikhitisar.html');
										$this->_fill_static_report($base,&$tpl);
										
										$tpl->Assign(array(
											'VTHN'   	=> $ryear,
											'VBLN'  	=> $rmonth,
											'DIVNAME'		=> $divname,
											'SDATE' 	=> $startdate,
											'EDATE' 	=> $enddate,
											'SID'     => MYSID,
												));
							
							
								// ==== FOR  EXCEL
					
											$tpl_excel->Assign(array(
												'VTOTDEBET'  => $curr_debet,
												'VTOTKREDIT' => $curr_kredit,
												'VTOTAWAL'   => $curr_saldoawal,
												'VTOTAKHIR'  => $curr_saldoakhir,
														));
								
											$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
											$tpl_temp_excel->parseConcat();
											$tpl_excel = $base->_get_tpl('report_persediaan_bahan_printable_ikhitisar.html');
											$this->_fill_static_report($base,&$tpl_excel);
								
											$tpl_excel->Assign(array(
												'VTHN'   	=> $ryear,
												'VBLN'  	=> $rmonth,
												'DIVNAME'		=> $divname,
												'SDATE' 	=> $startdate,
												'EDATE' 	=> $enddate,
												'SID'     => MYSID,
													));
								
					
								// ====		
							
						}

          }
        
		
			} // end of while				
				
		}
		   
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => '',
			'VTHN'  => '',
			'VBLN'  => '',
			'VAP'  => '',
			'TSAW'		 => 'tes total',
		));
		
		
		// ===== FOR EXCEL
			
					$tpl_excel->Assign(array(
						'PERIODE'  => $startdate.' s.d '.$enddate,
						'YEAR'  => '',
						'VTHN'  => '',
						'VBLN'  => '',
						'VAP'  => '',
						'TSAW'		 => 'tes total',
					));
		
		
		// ======
		

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
		
			
		// ====== FOR EXCEL
		
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
		
				
		// =====
		
		
		$is_proses = $this->get_var('is_proses');
		if($is_proses=='t')
		{
			if($code_nilai=='11411')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_bahan_ikhtisar_".$ryear_."_".$rmonth_."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			$this->tpl =& $tpl_temp;
				
				// ==== FOR EXCEL
						
						$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_bahan_ikhtisar_".$ryear_."_".$rmonth_."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
				
				// ====
				
			}
			else if($code_nilai=='11421')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_produk_ikhtisar_".$ryear_."_".$rmonth_."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);			
			$this->tpl =& $tpl_temp;
				
				// ===== FOR EXCEL
				
							$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_produk_ikhtisar_".$ryear_."_".$rmonth_."_for_excel.html";
							$isi2 = & $tpl_temp_excel->parsedPage();
							$this->cetak_to_file($base,$filename_excel,$isi2);
				
				// =====
				
			}
			else if($code_nilai=='11422')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_komponen_ikhtisar_".$ryear_."_".$rmonth_."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			$this->tpl =& $tpl_temp;
				
				
				// ===== FOR EXCEL
				
							$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_komponen_ikhtisar_".$ryear_."_".$rmonth_."_for_excel.html";
							$isi2 = & $tpl_temp_excel->parsedPage();
							$this->cetak_to_file($base,$filename_excel,$isi2);
				
				// =====
				
			}
			else if($code_nilai=='11433')
			{
				$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_ikhtisar_".$ryear_."_".$rmonth_."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			$this->tpl =& $tpl_temp;
				
				// ==== FOR EXCEL +009
				
						$filename_excel = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_ikhtisar_".$ryear_."_".$rmonth_."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
				
				// ====
							
			}
		}
		else
		{
			$this->tpl_excel =& $tpl_temp_excel;
			$this->tpl =& $tpl_temp;				
		}
		
	}/*}}}*/
	
	// created by yuniar kurniawan
	// tanggal 17 april 2008
	// digunakan untuk seting nilai balik volume
	function _setVolume($base,$tmpInput)/*{{{*/
	{
			$cek_input = strpos(strval($tmpInput),".");
			if($cek_input<>0)
			{
				$tmpInput=(round($tmpInput,5));
				$tmp_string = strval($tmpInput);
				
				$tmp_count = strlen(strstr($tmp_string,'.'));
				if($tmp_count==1)
					$hasil_coba = $tmpInput . '00000';
				else if($tmp_count==2)
					$hasil_coba = $tmpInput . '0000';
				else if($tmp_count==3)
					$hasil_coba = $tmpInput . '000';
				else if($tmp_count==4)
					$hasil_coba = $tmpInput . '00';
				else if($tmp_count==5)
					$hasil_coba = $tmpInput;
				else if($tmp_count>5)
					$hasil_coba = round($tmpInput,5);
			}	
			else
					$hasil_coba = $tmpInput . '.00000';
					
			
			return $hasil_coba;
						
	
	}/*}}}*/
		
	// last update by yuniar kurniawan
	// tanggal 19 maret 2008
	// penggantian filterisasi batasan tanggal
	function sub_mainpage($base)/*{{{*/
	{
	  	if($this->get_var("sub")=="uker")
    	{
		  		$tpl = $base->_get_tpl('report_mainpage_uker2.html');
      		$disp="";
					$kdreport = "AKT001";
                    $report = 'bukubesar_perwilayah';
					if(session_name() == 'SRM')
					{
						$tpl->assign('H1','');
						$tpl->assign('H2','none');
						$tpl->assign('H3','');
						$tpl->assign('H4','');
					}
					else	
					{
						if($this->get_var("show_tampil")=="no")
						{
							$tpl->assign('H1','none');
							$tpl->assign('H2','none');
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
    	}
    	else if($this->get_var("sub")=="harian_bank") // new untuk harian bank
			{
					$tpl = $base->_get_tpl('report_mainpage_harian_bank.html');
					$disp="";
					
										
					if(session_name() == 'SRM')
					{
						
						$tpl->assign('H1','');
						$tpl->assign('H2','none');
						$tpl->assign('H3','');
						$tpl->assign('H4','');
					}
					else	
					{
						if($this->get_var("show_tampil")=="no")
						{
							$tpl->assign('H1','none');
							$tpl->assign('H2','none');
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
	
		}
		else if($this->get_var("sub")=="ledger")
		{
				// untuk buku besar
				$tpl = $base->_get_tpl('report_buku_besar.html');
				$kdreport = 'AKT001';
				if(session_name() == 'SRM')
				{
					$tpl->assign('H1','');
					$tpl->assign('H2','none');
					$tpl->assign('H3','');
					$tpl->assign('H4','');
				}
				else	
				{
					if($this->get_var("show_tampil")=="no")
					{
						$tpl->assign('H1','none');
						$tpl->assign('H2','none');
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
				
				}		
				
				
				$dp = new dateparse;
				$m = $this->get_var('m',date('m'));
        		$y = $this->get_var('y',date('Y'));
			
				if (!$m) $m=date("m");
				if (!$y) $y=date("Y");
			
				$start = $y."-".$m."-";
				$end = $y2."-".$m2."-".$d2;
                
                $report = 'bukubesar';
		
				$bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
				$tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
	
				$tpl->Assign(array(
					'BLN'	=> $bln_,
					'TAHUN'	=> $tahun_,
						));
			
      		$disp="none";
		}
		else
    	{
		  $tpl = $base->_get_tpl('report_mainpage2.html');
			
			
				if($this->get_var("show_tampil")=="no")
				{
					$tpl->assign('H1','none');
					$tpl->assign('H2','none');
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
					
      		$disp="none";
    }
		
		loadClass('modules.accounting');
		$ynow = date('Y');

    $start_coa = accounting::get_htmlselect_coaid($base,'coaid',$this->get_var('coaid'),true,'','');
    $end_coa = accounting::get_htmlselect_coaid($base,'coaid2',$this->get_var('coaid2'),true,'','');


	  $rep_type   = $this->get_var('rep_type','overview_ledger');
	  
		  	
		$tpl->Assign(array(
		  	'VSTGL'   	=> $this->get_var('stardate',date('1-m-Y')),
		  	'VETGL'   	=> $this->get_var('enddate',date('d-m-Y')),
		  	'SRYEAR'   	=> $ryear,
				'SRMONTH'  	=> $rmonth,
				'SPERIOD'  	=> $glperiods,
				'START_COA'	=> $start_coa,
				'END_COA' 	=> $end_coa,
				'TITLE' 		=> str_replace('_',' ',$rep_type),
				'REP_TYPE' 	=> $rep_type,
				'SUB' 	    => $this->get_var('sub'),
				'DISP' 	    => $disp,
				'SID'      	=> MYSID,
                'KONSOLIDASI'   => $this->get_var('konsolidasi'),
                'KDREPORT'  => $kdreport,
                'REPORT'   => $report,
		));
		$this->tpl =& $tpl;
	}/*}}}*/
  
  // Fungsi untuk menampilkan popup untuk tampilan menunggu (please wait)
  // Author       : rio@terakorp.com
  // Create date  : Sat Apr  5 19:10:41 WIT 2008
  function sub_pop_wait($base)/*{{{*/
  {
  	//$base->db->debug = true;
  	//print "<pre>";
  	//var_dump($_REQUEST);exit;
	$tpl 		= $base->_get_tpl('pop_wait.html');
	$untuk 		= $this->get_var('untuk');
	$uker 		= $this->get_var('uker','');
    $kdreport 	= $this->get_var('kdreport');
    $grptype 	= $this->get_var('grptype');
    $kdspk 		= $this->get_var('kdspk');
	$ses 		= session_name();
	//die('Sesion  : ' . $ses);
		
		if(session_name() == 'SRM')
		{
			$this->get_valid_app('SRM');
			$table = "jurnal";
			$kdspk = $this->S['curr_proyek'];
			$add_sqlNew = " AND kdspk='".$kdspk."' ";
		}
		else	
		{
			$this->get_valid_app('SDV');
			$kddiv = $this->S['curr_divisi'];
			$table = "jurnal_".strtolower($kddiv);
			$add_sqlNew = " ";
		}		
		
		$mod = 'accounting_report_print';
        $cmd = $untuk;
        
		if($untuk == 'harian_bank')
		{
			$cmd = 'show_report_harian';
		}
		else if($untuk == 'kas_bank')
		{
			$cmd = 'show_report';
		}
		else if($untuk=='buku_besar')
		{
			$cmd = 'show_report';
			
		}
		else if($untuk == 'bukubesarwil')
		{
			$cmd = 'show_report';
		}
		else if($untuk == 'persediaan')
		{
		
			$to_code = $this->get_var('to_code',0);
			if($to_code == '11411')
			{
				$cmd = 'show_report';
			}
			
			if($to_code == '11421')
			{
				$cmd = 'show_report';
			}
			
			if($to_code == '11422')
			{
				$cmd = 'show_report';
			}
			
			if($to_code == '11433')
			{
				$cmd = 'show_report';
			}
		
		}
		//echo $cmd;exit;
		$tpl->Assign(array(
			'MOD'			=>  $mod,
			'CMD'			=>  $cmd,
			'NILAI_CODE' 	=>	$to_code,
			'UKER'				=>  $uker
				));
		
	//print '<pre>'. var_export($_GET, true);
		
      $tpl->Assign(array(
        'MONTH_START'	=> $this->get_var('month_start'),
        'MONTH_END' 	=> $this->get_var('month_end'),
        'YEAR_START'  	=> $this->get_var('year_start'),
        'YEAR_END'     	=> $this->get_var('year_end'),
        'REPTYPE'     	=> $this->get_var('reptype'),
        'RYEAR'     	=> $this->get_var('ryear'),
        'RMONTH'    	=> $this->get_var('rmonth'),
        'KDPERKIRAAN'   => $this->get_var('kdperkiraan'),
        'KDPERKIRAAN2' 	=> $this->get_var('kdperkiraan2'),
        'TBTYPE' 		=> $this->get_var('tbtype'),
        'KDREPORT'  	=> $kdreport,
        'GRPTYPE'   	=> $grptype,
        'SID'       	=> MYSID,
        'VASMID'    	=> $this->get_var('asmid'),
        'KONSOLIDASI'   => $this->get_var('konsolidasi'),
        'KDSPK' 		=> $kdspk,
        'UKER'  		=> $uker,
        'KDDIV' 		=> $this->get_var('kddiv'),
        'VNOBUKTI' 		=> $this->get_var('nobukti', ''),
        'FORM_ACTION'	=> 'ci/index.php/labarugi/divisi/komprehensif',
        'IS_KOMPRE'		=> 'true',
      ));
/*
			var_dump(array(
        'MONTH_START'	=> $this->get_var('month_start'),
        'MONTH_END' 	=> $this->get_var('month_end'),
        'YEAR_START'  	=> $this->get_var('year_start'),
        'YEAR_END'     	=> $this->get_var('year_end'),
        'REPTYPE'     	=> $this->get_var('reptype'),
        'RYEAR'     	=> $this->get_var('ryear'),
        'RMONTH'    	=> $this->get_var('rmonth'),
        'KDPERKIRAAN'   => $this->get_var('kdperkiraan'),
        'KDPERKIRAAN2' 	=> $this->get_var('kdperkiraan2'),
        'TBTYPE' 		=> $this->get_var('tbtype'),
        'KDREPORT'  	=> $kdreport,
        'GRPTYPE'   	=> $grptype,
        'SID'       	=> MYSID,
        'VASMID'    	=> $this->get_var('asmid'),
        'KONSOLIDASI'   => $this->get_var('konsolidasi'),
        'KDSPK' 		=> $kdspk,
        'UKER'  		=> $uker,
        'KDDIV' 		=> $this->get_var('kddiv'),
        'VNOBUKTI' 		=> $this->get_var('nobukti', '')
      ));echo  '</pre>'; exit;
      */
   	$this->tpl = $tpl;
		
  }/*}}}*/
	  
	function sub_show_report($base)/*{{{*/
	{
		//$base->db->debug= true;
        $rep = 'sub_report_' . $this->get_var('reptype');
        if (method_exists($this,$rep))
        {
            return $this->$rep($base);
        }
		else
		{
            return $base->get_message_page('Report not found');
		}
	}/*}}}*/
	
	// last update by yuniar kurniawan
	// tanggal 19 maret 2008
	// filterisasi tanggal
	
	function sub_report_overview_ledger($base,$cron_konsolidasi=false)/*{{{*/
	{//
		//$base->db->debug= true;

        if($cron_konsolidasi)
        {
            $this->Q['konsolidasi'] = 'yes';
        }
        
        $kduker = $this->get_var('uker','');	
		loadclass('dateparse');
		
		if(session_name() == 'SRM')
		{
			$this->get_valid_app('SRM');
			$table = "jurnal";
			$kdspk = $this->S['curr_proyek'];
			$add_sqlNew = " AND kdspk='".$kdspk."' ";
		}
		else	
		{
			$this->get_valid_app('SDV');
			$kddiv = $this->S['curr_divisi'];
            $table = ($this->get_var('konsolidasi')=='yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($kddiv);
			$add_sqlNew = " ";
		}	
		
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '$kddiv' ");
		
		$tpl = $base->_get_tpl('report_overview_ledger_printable.html');
        $tpl_temp = $base->_get_tpl('one_var.html');
		$this->_fill_static_report($base,&$tpl);
		
		// ===== FOR EXCEL
        $tpl_excel = $base->_get_tpl('report_overview_ledger_printable.html');
        $tpl_temp_excel = $base->_get_tpl('one_var.html');
        $this->_fill_static_report($base,&$tpl_excel);
		// =====
		
		$ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        $sub = $this->get_var('sub');
    
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

        if($this->S['curr_wil'])
          $addsql_where .= " AND jur.kdspk IN (select x.kdspk FROM dspk x WHERE x.kddiv='{$this->S['curr_divisi']}' AND x.kodewilayah='".$this->S['curr_wil']."')";
        
				
        $tpl->assign('KONSOLIDASI', (($this->get_var('konsolidasi')=='yes')? 'KONSOLIDASI':''));
		if($kduker!='')
		{
			// pencarian nama wilayah
			$nama_wilyah=$base->dbGetOne("SELECT namawilayah FROM dwilayah WHERE kdwilayah ='$kduker'");
		
			$tpl->assign(array(
				'PER'=>'PER WILAYAH',
				'KODE WILAYAH : '=>'Kode Wilayah : ',
				'KODE'=> $kduker,
				'NAMA_WIL'=>$nama_wilyah,
				'NAMA WILAYAH : '=>'Nama Wilayah : ',
			));
			
			$tpl_excel->assign(array(
				'PER WILAYAH' => 'PER WILAYAH',
				'KODE WILAYAH : '=>'Kode Wilayah : ',
				'NAMA WILAYAH : '=>'Nama Wilayah : ',
				'KODE'=> $kduker,
				'NAMA_WIL'=>$nama_wilyah,
			));
			
            $addsql1="AND SUBSTR(nobukti,1,2)='{$kduker}' AND";
        }
        else
        {
			$tpl->assign(array(
				'PER'=> ($this->get_var('konsolidasi') == 'yes') ? 'KONSOLIDASI' :'',
				'KODE WILAYAH : '=>'  ',
				'NAMA WILAYAH : '=>' ',
				'KODE'=> '',
				'NAMA_WIL'=>'',
			));
			
			$tpl_excel->assign(array(
                'PER'=>($this->get_var('konsolidasi') == 'yes') ? 'KONSOLIDASI' :'',
                'KODE WILAYAH : '=>' ',
                'NAMA WILAYAH : '=>' ',
                'KODE'=> '',
                'NAMA_WIL'=>'',
			));
			
            $addsql1="AND";
        }
		
	    $batasan_tahun_new = $this->get_var('ryear');
		$batasan_bulan_new = $this->get_var('rmonth');
		$final_batasan = $batasan_tahun_new . '-' . $batasan_bulan_new . '-' . '01';
	   
	   	$sql = "SELECT jur.*, to_char(date(tanggal),'DD-MM-YYYY') as tanggal_f, 
				per.kdperkiraan,per.nmperkiraan
				FROM {$table} jur
                JOIN dperkir per ON (per.kdperkiraan=jur.kdperkiraan)
                WHERE isdel='f' AND coalesce(isapp,'f')='t'
                {$addsql1}
                (
        			  DATE_PART('YEAR',jur.tanggal) = '".$batasan_tahun_new."' 
        			  AND DATE_PART('MONTH',jur.tanggal) = '".$batasan_bulan_new."'
        		    )
        				
        				{$add_sqlNew}

                {$addsql_where}
        				
        				--AND jur.kddivisi = '{$kddiv}'
                ORDER BY jur.kdperkiraan, DATE(jur.tanggal), jur.nobukti, jur.jid
                ";
			
        $rs2 = $base->dbquery($sql);
        if ($rs2->EOF)
        {
            $tpl->Assign('row','');
            $tpl_excel->Assign('row','');
        }
        else
        {
            $tpl->Assign(array(
            	'VTHN'  	=> $ryear,
            	'VBLN' 		=> $rmonth,
            	'DIVNAME'	=> $divname,
            	'SDATE' 	=> $startdate,
            	'EDATE' 	=> $enddate,
            	'SID'     => MYSID,
            	'VCURR'   => '',
            	'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
            	'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
            ));
				// ===== FOR EXCEL
			$tpl_excel->Assign(array(
				'VTHN'  	=> $ryear,
				'VBLN' 		=> $rmonth,
				'DIVNAME'	=> $divname,
				'SDATE' 	=> $startdate,
				'EDATE' 	=> $enddate,
				'SID'     => MYSID,
				'VCURR'   => '',
				'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
				'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
			));
				// ====== 
        //Addd by RIOO
        //Begin create saldo awal
			if($kduker=='')
			{
				$sql_saldo_awal = "
					SELECT kdperkiraan,
						(CASE WHEN dk='D' THEN SUM(rupiah) END) AS debet,
						(CASE WHEN dk='K' THEN SUM(rupiah) END) AS kredit 
                        FROM {$table} jur
					WHERE  isdel='f' AND coalesce(isapp,'f')='t' AND (kdperkiraan LIKE '1%' OR kdperkiraan LIKE '2%' OR kdperkiraan LIKE '3%') AND DATE(jur.tanggal) < '{$final_batasan}' 
					 
					{$add_sqlNew}
           			{$addsql_where}
					 
					GROUP BY kdperkiraan,dk
					ORDER BY kdperkiraan,dk";
			}
			else
			{
					
				$sql_saldo_awal = "
					SELECT kdperkiraan,
						(CASE WHEN dk='D' THEN SUM(rupiah) END) AS debet,
						(CASE WHEN dk='K' THEN SUM(rupiah) END) AS kredit 
                        FROM {$table} jur
					WHERE  isdel='f' AND coalesce(isapp,'f')='t' AND (kdperkiraan LIKE '1%' OR kdperkiraan LIKE '2%' OR kdperkiraan LIKE '3%') AND DATE(jur.tanggal) < '{$final_batasan}' 
						AND SUBSTR(jur.nobukti,1,2)='$kduker'
					 
					{$add_sqlNew}
           			{$addsql_where}
					 
					GROUP BY kdperkiraan,dk
					ORDER BY kdperkiraan,dk";				
				
			}
            
            //die($sql_saldo_awal);	
            $rs_saldo_awal = $base->dbquery($sql_saldo_awal);
            $hasil_saldo = array();  
            while(!$rs_saldo_awal->EOF)
            {
                $hasil_saldo[$rs_saldo_awal->fields['kdperkiraan']] += $rs_saldo_awal->fields['debet']-$rs_saldo_awal->fields['kredit'];
                
                $rs_saldo_awal->moveNext();
            }
					
            // End create saldo awal
            $first_start = true;
            $curr_coa = '';
        while (!$rs2->EOF)
        {
            if ($first_start)
            {
                $first_start = false;
            }
            else
            {
                $balbegin = '';
            }
				
			//kalao kode coa nya beda maka 
			if ($curr_coa != $rs2->fields['kdperkiraan'])
			{
                $tpl->defineDynamicBlock(array('row'));
                // ===== FOR EXCEL
                $tpl_excel->defineDynamicBlock(array('row'));
                // =====
                $curr_coa = $rs2->fields['kdperkiraan'];
                $curr_debet 	= 0;
                $curr_kredit 	= 0;
                $curr_saldo 	= 0;
                //menghitung saldo terakhir
                //$curr_saldo = ($hasil_saldo[$rs2->fields['kdperkiraan']][0] + $hasil_saldo[$rs2->fields['kdperkiraan']][1])-($hasil_saldo[$rs2->fields['kdperkiraan']][2] + $hasil_saldo[$rs2->fields['kdperkiraan']][3]);
                //$curr_saldo = ($hasil_saldo[$rs2->fields['kdperkiraan']][0] - $hasil_saldo[$rs2->fields['kdperkiraan']][1]);
                $curr_saldo = $hasil_saldo[$rs2->fields['kdperkiraan']];
                $tpl->assign('VSALDOAWAL',$this->format_money2($base, $curr_saldo));
                // ====== FOR EXCEL
                $tpl_excel->assign('VSALDOAWAL',$this->format_money2($base, $curr_saldo));
				// ======
			}
       		$detailnote = $rs2->fields['detailnote'];
            $gltid = $rs2->fields['gltid'];
            $tdate = $rs2->fields['tdate'];
            $jocode = $rs2->fields['jocode'];
            if ($rs2->fields['default_debet'] == 't')
            {
                $val = $rs2->fields['debet'] - $rs2->fields['credit'];
            }
            else
            {
                $val = $rs2->fields['credit'] - $rs2->fields['debet'];
            }
					
					
			$curr_saldo =  $curr_saldo + ( $rs2->fields['dk'] == 'D' ?  $rs2->fields['rupiah'] : (-1) *  $rs2->fields['rupiah'] );
            $tpl->assignDynamic('row', array(
				'VTGL'  	=> $rs2->fields['tanggal_f'],
				'VCOA'  	=> $rs2->fields['kdperkiraan'],
				'VNAMA'  	=> $rs2->fields['nmperkiraan'],
				'VBUKTI'  => $rs2->fields['nobukti'],
				'VSPK'    => $rs2->fields['kdspk'],
				'VTAHAP'  => $rs2->fields['kdtahap'],
				'VNSBH'   => $rs2->fields['kdnasabah'],
				'VHARTA'  => $rs2->fields['kdalat'],
				'VDESC'   => $rs2->fields['keterangan'],
				'VDEBET'  => $rs2->fields['dk'] == 'D' ? $this->format_money2($base, $rs2->fields['rupiah']) : '0',
				'VKREDIT' => $rs2->fields['dk'] == 'K' ? $this->format_money2($base, $rs2->fields['rupiah']) : '0',
				'VSALDO'  => $this->format_money2($base,  $curr_saldo),
				'BALBEGIN' => $balbegin,
				'BALEND' 	=> $realbalend,
				'BBAL'  	=> $this->format_money2($base,$bb),
				'EB'    	=> $this->format_money2($base,$eb),
				'GLTID'   => $rs2->fields['glcode'],
				'DESC'  	=> $detailnote,
				'TDATE' 	=> $tdate,
				'JOCODE' 	=> $jocode,
				'DEB'   	=> $this->format_money2($base,$val),
          ));
          $tpl->parseConcatDynamic('row');
          // ===== FOR EXCEL
		  $tpl_excel->assignDynamic('row', array(
				'VTGL'  	=> $rs2->fields['tanggal_f'],
				'VCOA'  	=> $rs2->fields['kdperkiraan'],
				'VNAMA'  	=> $rs2->fields['nmperkiraan'],
				'VBUKTI'  => $rs2->fields['nobukti'],
				'VSPK'    => $rs2->fields['kdspk'],
				'VTAHAP'  => $rs2->fields['kdtahap'],
				'VNSBH'   => $rs2->fields['kdnasabah'],
				'VHARTA'  => $rs2->fields['kdalat'],
				'VDESC'   => $rs2->fields['keterangan'],
				'VDEBET'  => $rs2->fields['dk'] == 'D' ? $this->format_money2($base, $rs2->fields['rupiah']) : '0',
				'VKREDIT' => $rs2->fields['dk'] == 'K' ? $this->format_money2($base, $rs2->fields['rupiah']) : '0',
				'VSALDO'  => $this->format_money2($base, $curr_saldo),
				'BALBEGIN' => $balbegin,
				'BALEND' 	=> $realbalend,
				'BBAL'  	=> $this->format_money2($base, $bb),
				'EB'    	=> $this->format_money2($base, $eb),
				'GLTID'   => $rs2->fields['glcode'],
				'DESC'  	=> $detailnote,
				'TDATE' 	=> $tdate,
				'JOCODE' 	=> $jocode,
				'DEB'   	=> $this->format_money2($base,$val),
			));
          	$tpl_excel->parseConcatDynamic('row');
					// =====
            $curr_debet  += $rs2->fields['dk'] == 'D' ? $rs2->fields['rupiah'] : 0;
            $curr_kredit += $rs2->fields['dk'] == 'K' ? $rs2->fields['rupiah'] : 0; 
 	
	        $rs2->moveNext();
          
            if ($rs2->EOF)
            {
                $realbalend = $balend;
				$tpl->Assign(array(
					'VTOTDEBET'  => $this->format_money2($base, $curr_debet),
					'VTOTKREDIT' => $this->format_money2($base, $curr_kredit),
				));
				$tpl_temp->assign('ONE',$tpl,'template');
				$tpl_temp->parseConcat();
				// ===== FOR EXCEL
				$tpl_excel->Assign(array(
					'VTOTDEBET'  => $this->format_money2($base, $curr_debet),
					'VTOTKREDIT' => $this->format_money2($base, $curr_kredit),
				));

				$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
				$tpl_temp_excel->parseConcat();
				// =====
 	        }
            else
            {
                $realbalend = '';

				if ($curr_coa != $rs2->fields['kdperkiraan'])
				{
					//parsing total debet/kreditnya sebelum diparsing datanya
					$tpl->Assign(array(
						'VTOTDEBET'  => $this->format_money2($base, $curr_debet),
						'VTOTKREDIT' => $this->format_money2($base, $curr_kredit),
					));
					$tpl_temp->assign('ONE',$tpl,'template');
					$tpl_temp->parseConcat();
					$tpl = $base->_get_tpl('report_overview_ledger_printable.html');
					$this->_fill_static_report($base,&$tpl);
					
					if($kduker!='')
					{
						// pencarian nama wilayah
						$nama_wilyah=$base->dbGetOne("SELECT namawilayah FROM dwilayah WHERE kdwilayah = '{$kduker}'");
						$tpl->assign(array(
							'PER'=>'PER WILAYAH',
							'KODE WILAYAH : '=>'Kode Wilayah : ',
							'KODE'=> $kduker,
							'NAMA_WIL'=>$nama_wilyah,
							'NAMA WILAYAH : '=>'Nama Wilayah : ',
							));
						
						$tpl_excel->assign(array(
							'PER WILAYAH' => 'PER WILAYAH',
							'KODE WILAYAH : '=>'Kode Wilayah : ',
							'NAMA WILAYAH : '=>'Nama Wilayah : ',
							'KODE'=> $kduker,
							'NAMA_WIL'=>$nama_wilyah,
							));
						
						$addsql1="AND SUBSTR(nobukti,1,2)={$kduker} AND";
					}
					else
					{
						$tpl->assign(array(
							'PER'=>'',
							'KODE WILAYAH : '=>'',
							'NAMA WILAYAH : '=>'',
							'KODE'=> '',
							'NAMA_WIL'=>'',
								));
						
						$tpl_excel->assign(array(
						  'PER'=>'',
						  'KODE WILAYAH : '=>'Kode Wilayah : ',
						  'NAMA WILAYAH : '=>'Nama Wilayah : ',
							'KODE'=> '',
							'NAMA_WIL'=>'',
							));
						
						$addsql1="AND";
					}
					
					$tpl->Assign(array(
						'VTHN'   => $ryear,
						'VBLN'  => $rmonth,
						'DIVNAME'		=> $divname,
						'SDATE' => $startdate,
						'EDATE' => $enddate,
						'SID'      => MYSID,
						'VCURR'      => '',
						'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
						'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
					));
					// ====== FOR EXCEL
					$tpl_excel->Assign(array(
						'VTOTDEBET'  => $this->format_money2($base, $curr_debet),
						'VTOTKREDIT' => $this->format_money2($base, $curr_kredit),
					));
											
					$tpl_temp_excel->assign('ONE',$tpl_excel,'template');
					$tpl_temp_excel->parseConcat();
					$tpl_excel = $base->_get_tpl('report_overview_ledger_printable.html');
					$this->_fill_static_report($base,&$tpl_excel);
					$tpl_excel->Assign(array(
						'VTHN'   => $ryear,
						'VBLN'  => $rmonth,
						'DIVNAME'		=> $divname,
						'SDATE' => $startdate,
						'EDATE' => $enddate,
						'SID'      => MYSID,
						'VCURR'      => '',
						'vkdperkiraan'  => $rs2->fields['kdperkiraan'],
						'vnmperkiraan'  => $rs2->fields['nmperkiraan'],
					));
					// =====
				}
            }
        }//end while
    }
							
		
		$tpl->Assign(array(
			'PERIODE'  => $startdate.' s.d '.$enddate,
			'YEAR'  => '',
			'VTHN'  => '',
			'VBLN'  => '',
			'VAP'  => '',
		));
		// ===== FOR EXCEL
				$tpl_excel->Assign(array(
					'PERIODE'  => $startdate.' s.d '.$enddate,
					'YEAR'  => '',
					'VTHN'  => '',
					'VBLN'  => '',
					'VAP'  => '',
				));
		// =====
		
		
        $PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;
        $tpl_temp->Assign('ONE', '
				<div id="pr" name="pr" align="left">
				<br>
				<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
				<a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
				<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
									<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
				-->
				</div>'
			);
		$tpl_temp->parseConcat();
		
		
		
		// ==== FOR EXCEL
		
					$PDF_URL = "?mod=accounting_report&cmd=report_overview_ledger&ryear=$ryear&rmonth=$rmonth" ;

    			$tpl_temp_excel->Assign('ONE', '
							<div id="pr" name="pr" align="left">
							<br>
							<!--img src="images/print.gif" title=" Print HTML " onClick="GetObjectByName(\'pr0\').style.display=\'none\';GetObjectByName(\'pr\').style.display=\'none\';window.print();">&nbsp;
							<a href="'.$PDF_URL.'"  target="_blank"><img src="images/pdf.gif" border="0" title=" Export PDF "></a> &nbsp;
							<a href="{CSV_URL}" target="_blank"><img src="images/excel.gif" border="0" title=" Export CSV "></a>
												<input type="button" value=" Kembali " class="buttons" onClick="window.location.replace(\'?mod=accounting_report&cmd=mainpage&rep_type=overview_ledger\')">
							-->
							</div>'
						);
					
					$tpl_temp_excel->parseConcat();
		
		
		// ====
		
		
		
		// cetak file untuk bukubesar
		$is_proses = $this->get_var('is_proses');
		$kduker = $this->get_var('uker','');
		
		//die('Uker : ' . $kduker);
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes') ? 'konsolidasi_':'';
		if($is_proses=='t')
		{
			$sub_2 = $this->get_var('sub');
			if($kduker=='')
			{	
				$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_".$txt_konsolidasi.$ryear."_".$rmonth."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
				$this->tpl =& $tpl_temp;
				// ====== FOR EXCEL ~007
				
						$filename_excel = $base->kcfg['basedir']."files/BUKUBESAR_AKT001_".$kddiv."_".$txt_konsolidasi.$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
				// ======
			}
			elseif($kduker!='')
			{
				$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_perwilayah_".$kduker."_".$ryear."_".$rmonth."_.html";
				$isi = & $tpl_temp->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
				$this->tpl =& $tpl_temp;
				// ====== FOR EXCEL
						$filename_excel = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_perwilayah_".$kduker."_".$ryear."_".$rmonth."_for_excel.html";
						$isi2 = & $tpl_temp_excel->parsedPage();
						$this->cetak_to_file($base,$filename_excel,$isi2);
				// ======
			}
		}
		else
		{	
		    $this->tpl_excel =& $tpl_temp_excel;
				$this->tpl =& $tpl_temp;
		}
	}/*}}}*/
	
	function _fill_static_report($base,$tpl)/*{{{*/
	{
		$sql = "SELECT data FROM configs WHERE confname='company_name'";
		$comname = $base->db->getOne($sql);

				
		$tpl->Assign(array(
		  'VCOMPANY'   => $comname,
			'PERIODE'  => $base->getLang('Bulan: '),
			'YEAR'  => $base->getLang('Tahun: '),
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
			$titles[] = array('titleText'=>$base->getLang('Bulan: ') . $month . '  ' . $base->getLang('Tahun: ') . $year, 'align' => 'C');
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
		
	// yuniar 
	// report persediaan produk rinci
	// 26 maret 2008
	// updated : 27 maret
	function sub_show_report_persediaan_barangjadi($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		
		
		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_ikhtisar_".$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_rinci_".$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar 
	// report persediaan produk rinci
	// 02 april 2008
	function sub_show_report_persediaan_barangjadi_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		

		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_ikhtisar_".$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_barangjadi_ikhtisar_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_barangjadi_rinci_".$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_barangjadi_rinci_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar 
	// report persediaan produk rinci
	// 26 maret 2008
	// updated : 27 maret
	function sub_show_report_persediaan_komponen($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
				
		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_komponen_ikhtisar_".$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_komponen_rinci_".$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);		
		}		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar 
	// report persediaan produk rinci
	// 02 april
	function sub_show_report_persediaan_komponen_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		

		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv."_persediaan_komponen_ikhtisar_".$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_komponen_ikhtisar_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_komponen_rinci_'.$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv."_persediaan_komponen_rinci_".$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);		
		}		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar 
	// report persediaan produk rinci
	// 26 maret 2008
	// updated : 27 maret
	function sub_show_report_persediaan_produk($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		

		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_ikhtisar_'.$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_rinci_'.$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);		
		}		
		
		$this->tpl = $tpl;
		
	}/*}}}*/
	
	// yuniar 
	// report persediaan produk rinci
	// 02 april
	function sub_show_report_persediaan_produk_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$divname = str_replace(" ","_",$divname);

		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_ikhtisar_'.$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_produk_ikhtisar_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		else
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_produk_rinci_'.$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_produk_rinci_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
						
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);		
		}		
		
		$this->tpl = $tpl;
		
	}/*}}}*/
	
	// yuniar	
	// 26 maret 2008
	// updated : 27 maret 2008
	function sub_show_report_persediaan_bahan($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		

		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_bahan_ikhtisar_'.$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);	
		}
		else
		{		
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_bahan_rinci_'.$thn_."_".$bln_."_.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		
		
		$this->tpl = $tpl;
		
	}/*}}}*/
	
	// yuniar	
	// 02 april 2008
	function sub_show_report_persediaan_bahan_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi'];
		
		$group = $this->get_var('tbtype','none');
		if ($group != 'rinci')
		{
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_bahan_ikhtisar_'.$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_bahan_ikhtisar_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
			
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);	
					
		}
		else
		{		
			$filename = $base->kcfg['basedir']."files/"."AKT015F_".$kddiv.'_persediaan_bahan_rinci_'.$thn_."_".$bln_."_for_excel.html"; 
			$fp = @fopen($filename,"r"); 
			if (!$fp) 
				die("The file does not exists!");			
			
			$contents = fread ($fp, filesize ($filename));
			
			header('content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename=AKT015F_'.$kddiv.'_persediaan_bahan_rinci_'.$thn_.'_'.$bln_.'_for_excel'.'.xls');
			
			fclose ($fp);
	
			$tpl = $base->_get_tpl('one_var.html');
			$tpl->assign('ONE' ,	$contents);
		}
		
		
		$this->tpl = $tpl;
		
	}/*}}}*/
		
	// yuniar kurniawan
	// 25 maret 2008
	// untuk menampilkan hasil proses buku besar
	function sub_show_report_overlegder($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi']; // oye
		
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes')?'konsolidasi_':'';
		$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_".$txt_konsolidasi.$thn_."_".$bln_."_.html";
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		fclose ($fp);

		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);
		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar kurniawan
	// 31 maret 2008
	// sebagai fasilitas penyimpanan ke file excel 
	function sub_show_report_overlegder_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi']; 
		
        $txt_konsolidasi = ($this->get_var('konsolidasi') == 'yes')?'konsolidasi_':'';
		$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_".$txt_konsolidasi.$thn_."_".$bln_."_for_excel.html";
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
							
		header('content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename=BUKUBESAR_AKT001_'.$kddiv."_".$txt_konsolidasi.$thn_.'_'.$bln_.'_for_excel'.'.xls');
				
		fclose ($fp);
		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);		
		$this->tpl = $tpl;
	}/*}}}*/
			
	// yuniar kurniawan
	// 25 maret 2008
	// untuk menampilkan hasil proses buku besar per unit kerja / wilayah
	function sub_show_report_overlegder_uker($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi']; 
		$kduker = $this->get_var('uker');
		
		$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_perwilayah_".$kduker."_".$thn_."_".$bln_."_.html";
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
		fclose ($fp);

		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);
		
		$this->tpl = $tpl;
	}/*}}}*/
	
	// yuniar kurniawan
	// 31 maret 2008
	// sebagai fasilitas penyimpanan ke file excel  AKT001_2005_3_bukubesar_perwilayah
	function sub_show_report_overlegder_uker_excel($base)/*{{{*/
	{
		$thn_ = $this->get_var('ryear',date('Y'));
		$bln_ = $this->get_var('rmonth',date('m'));
		$tbtype = $this->get_var('tbtype');
		$kddiv = $this->S['curr_divisi']; 
		$kduker = $this->get_var('uker');
	
		
		$filename = $base->kcfg['basedir']."files/"."BUKUBESAR_AKT001_".$kddiv."_perwilayah_".$kduker."_".$thn_."_".$bln_."_for_excel.html";
		$fp = @fopen($filename,"r"); 
		if (!$fp) 
			die("The file does not exists!");			
		
		$contents = fread ($fp, filesize ($filename));
							
		header('content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename=BUKUBESAR_AKT001_'.$kddiv."_perwilayah_".$kduker."_".$thn_.'_'.$bln_.'_for_excel'.'.xls');
				
		fclose ($fp);
		$tpl = $base->_get_tpl('one_var.html');
		$tpl->assign('ONE' ,	$contents);		
		$this->tpl = $tpl;
	}/*}}}*/
  
  
    function sub_opensystem($base,$type='',$kdreport='')/*{{{*/
    {	//$base->db= true;
		//$this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $konsolidasi = $this->get_var('konsolidasi');
        if($konsolidasi == 'yes')
        {
            $table = "v_opensystem_konsolidasi";
        }
        else
        {
            $table = "v_opensystem_".strtolower($this->S['curr_divisi']);    
            if($this->S['curr_divisi'] == '') $table = "jurnal";
        }
		
		$type = $this->get_var('tbtype', $type);
        $kdreport = $this->get_var('kdreport',$kdreport);
        $grptype = $this->get_var('grptype');
        $ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        //$base->db= true;
        $is_proses = $this->get_var('is_proses');
		if($is_proses=='t')
		{
		  
            if($type == 'rinci')
            {
                if($kdreport == "OSHUTANGDLMPROSES")
                {
                    $tpl = $base->_get_tpl('report_opensystem_hutang_printable_rinci.html');
                    $tpl_excel = $base->_get_tpl('report_opensystem_hutang_printable_rinci.html');
                }
                else
                {
                    $tpl = $base->_get_tpl('report_opensystem_printable_rinci.html');
                    $tpl_excel = $base->_get_tpl('report_opensystem_printable_rinci.html');    
                }
                
            }
            else
            {
                $tpl = $base->_get_tpl('report_opensystem_printable_ikhtisar.html');
                $tpl_excel = $base->_get_tpl('report_opensystem_printable_ikhtisar.html');
            }
            
            $tpl1 = & $tpl->defineDynamicBlock('row');
            $tpl2 = & $tpl1->defineDynamicBlock('row_subtot');
            $tpl3 = & $tpl2->defineDynamicBlock('row1');
            
            $tpl1_excel = & $tpl_excel->defineDynamicBlock('row');
            $tpl2_excel = & $tpl1_excel->defineDynamicBlock('row_subtot');
            $tpl3_excel = & $tpl2_excel->defineDynamicBlock('row1');
        
            $tpl->assign(array(
                'TITLE' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT keterangan FROM report_reff WHERE kdreport='{$kdreport}'") . ($grptype=='perspk'?" PER SPK" : " PER NASABAH"),
		'HEADER' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT keterangan FROM report_reff WHERE kdreport='{$kdreport}'") . ($grptype=='perspk'?" PER SPK" : " PER NASABAH"),
            ));
            $tpl_excel->assign(array(
                'TITLE' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT keterangan FROM report_reff WHERE kdreport='{$kdreport}'") . ($grptype=='perspk'?" PER SPK" : " PER NASABAH"),
            ));
        
            $tpl_temp = $base->_get_tpl('one_var.html');
            $tpl_temp_excel = $base->_get_tpl('one_var.html');
    		$this->_fill_static_report($base,&$tpl);
            $this->_fill_static_report($base,&$tpl_excel);
        
            
            $contoh = date("Y-m-d",mktime(0,0,0,$rmonth,1-1,$ryear));
            $contoh_2 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear-1));
            $contoh_3 = $ryear.'-12-31';
            $contoh_4 = date("Y-m-d",mktime(0,0,0,$rmonth+1,1-1,$ryear));
            $dp = new dateparse();
            $nm_bulan_ = $dp->monthnamelong[$rmonth];
            //$base->db= true; 
            $addorder = ($grptype=='perspk')? " j.kdspk,":" j.kdnasabah,j.kdspk,";
            if($type == 'rinci')
            {
                $sql_a = "SELECT j.* , p.total_pelunasan as rp_lunas, p.max_tgl, p.nobukti_penerbitan as bukti_lunas,
                            (j.rupiah_penerbitan-COALESCE(p.total_pelunasan,0)) as sisa
                            , to_char(j.tanggal, 'dd-mm-yyyy') as tanggal ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , j.faktur_pajak " : "";
                $sql_a .= "FROM {$table} j
                        LEFT JOIN (
                        	SELECT SUM(jr.rupiah_pel) as total_pelunasan, jr.nobukti_penerbitan, jr.kdnasabah, jr.kdspk, MAX(jr.tanggal) as max_tgl ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , jr.faktur_pajak " : "";
                $sql_a .= " FROM {$table} jr
                            WHERE jr.is_pelunasan='t' AND DATE(jr.tanggal) <= '$contoh_4'
                            AND jr.kdperkiraan IN (SELECT b.kdperkiraan FROM report_reff b WHERE b.kdreport = '{$kdreport}')
                            AND jr.isapp='t'";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND COALESCE(jr.faktur_pajak,'') != '' AND COALESCE(jr.kdnasabah,'') != '' " : "";
                $sql_a .= " GROUP BY nobukti_penerbitan, jr.kdnasabah, jr.kdspk";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , jr.faktur_pajak " : "";
                $sql_a .= " ) p ON (p.nobukti_penerbitan=j.nobukti AND p.kdnasabah=j.kdnasabah AND p.kdspk=j.kdspk ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND p.faktur_pajak=j.faktur_pajak " : "";
                $sql_a .= ")
                        WHERE DATE(j.tanggal) <= '$contoh_4' 
                            AND j.isapp='t'
                            AND j.kdperkiraan IN (SELECT b.kdperkiraan FROM report_reff b WHERE b.kdreport = '{$kdreport}')";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND COALESCE(j.faktur_pajak,'') != '' AND COALESCE(j.kdnasabah,'') != '' " : "";
                $sql_a .= " ORDER BY {$addorder} ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " j.faktur_pajak, " : "";
                $sql_a .= " j.nobukti_penerbitan, j.is_pelunasan, j.tanggal, j.rupiah ";
	
            }
            else
            {
              $sql_a = "SELECT j.* , p.total_pelunasan as rp_lunas, p.max_tgl, p.nobukti_penerbitan as bukti_lunas,
                            (j.rupiah_penerbitan-COALESCE(p.total_pelunasan,0)) as sisa
                            , to_char(j.tanggal, 'dd-mm-yyyy') as tanggal ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , j.faktur_pajak " : "";
                $sql_a .= "FROM {$table} j
                        LEFT JOIN (
                        	SELECT SUM(jr.rupiah_pel) as total_pelunasan, jr.nobukti_penerbitan, jr.kdnasabah, jr.kdspk, MAX(jr.tanggal) as max_tgl ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , jr.faktur_pajak " : "";
                $sql_a .= " FROM {$table} jr
                            WHERE jr.is_pelunasan='t' AND DATE(jr.tanggal) <= '$contoh_4'
                            AND jr.kdperkiraan IN (SELECT b.kdperkiraan FROM report_reff b WHERE b.kdreport = '{$kdreport}')
                            AND jr.isapp='t'";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND COALESCE(jr.faktur_pajak,'') != '' AND COALESCE(jr.kdnasabah,'') != '' " : "";
                $sql_a .= " GROUP BY nobukti_penerbitan, jr.kdnasabah, jr.kdspk";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " , jr.faktur_pajak " : "";
                $sql_a .= " ) p ON (p.nobukti_penerbitan=j.nobukti AND p.kdnasabah=j.kdnasabah AND p.kdspk=j.kdspk ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND p.faktur_pajak=j.faktur_pajak " : "";
                $sql_a .= ")
                        WHERE DATE(j.tanggal) <= '$contoh_4' 
                            AND j.isapp='t'
                            AND j.kdperkiraan IN (SELECT b.kdperkiraan FROM report_reff b WHERE b.kdreport = '{$kdreport}')";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " AND COALESCE(j.faktur_pajak,'') != '' AND COALESCE(j.kdnasabah,'') != '' " : "";
                $sql_a .= " ORDER BY {$addorder} ";
                $sql_a .= ( $kdreport == "OSHUTANGDLMPROSES" ) ? " j.faktur_pajak, " : "";
                $sql_a .= " j.nobukti_penerbitan, j.is_pelunasan, j.tanggal, j.rupiah ";
            }
            //echo '<pre>'.$sql_a.'</pre>'; die();
            
            $rs_a = $base->dbquery($sql_a);
            
            if($rs_a->EOF)
            {
              $tpl = $base->_get_tpl('one_var.html');
              $tpl->assign('ONE' , 'No Data Availabe');
              $this->tpl = $tpl; echo 'No Data Availabe !';  exit;

            }
            
            if (!$rs_a->EOF)
            {
            	$record_static = array(
            		'VTHN'  	=> $ryear,
            		'VBLN'  	=> $nm_bulan_,
            		'SDATE' 	=> $startdate,
            		'EDATE' 	=> $enddate,
            		'DIVNAME'	=> $divname,
            		'SID'     => MYSID,
            	); 
                $tpl->Assign($record_static);
                $tpl_excel->Assign($record_static);
                
            }
            
            foreach(array('excel','html') as $key => $val)
            {
                $rs_a->moveFirst();
                
                //reset data
                $orbuk = '';
                $jumterbit_spk = 0;
                $jumlun_spk = 0;
                $sisa = 0;
                $jsisa = 0;
                $v1 = 0;
                $v1s3 = 0;
                $v3s6 = 0;
                $v6s12 = 0;
                $v12 = 0;
                $vj1 = 0;
                $vj1s3 = 0;
                $vj3s6 = 0;
                $vj6s12 = 0;
                $vj12  = 0;
                $jsisa = 0;
                
                /*if($val == 'excel')
                    $this->no_format_money = true;
                else
                    $this->no_format_money = false;
                */
                 
                $parsing = false;
                
                while (!$rs_a->EOF)
                {											
                	$tglpenerbitan = substr($rs_a->fields['tanggal'],0,2);
                	$blnpenerbitan = substr($rs_a->fields['tanggal'],3,2);
                	$thnpenerbitan = substr($rs_a->fields['tanggal'],6,4);
                	
                	//2005-12-31
                	$tglperiode = substr($contoh_4,8,2);
                	$blnperiode = substr($contoh_4,5,2);
                	$thnperiode = substr($contoh_4,0,4);
                	
                	$date1 = array($thnpenerbitan,$blnpenerbitan,$tglpenerbitan);
                	$date2 = array($thnperiode,$blnperiode,$tglperiode);			
                	
                	$mkt1 =mktime(0,0,0,$date1[1], $date1[2], $date1[0]);
                	$mkt2 =mktime(0,0,0,$date2[1], $date2[2], $date2[0]);
                	
                	$utime = $mkt2 - $mkt1;
                	$tanggel = $utime / 86400;
                    $umur = $utime / 86400; // days diff
                    //$umur = floor($utime/2628000);// months diff
                    
                    if($type == 'rinci')
                    {
                        $kdspk = $rs_a->fields['kdspk'];
                        $nmspk= $rs_a->fields['nmspk'];
                        $kdnasabah = $rs_a->fields['kdnasabah'];
                        $nmnasabah = $rs_a->fields['nmnasabah'];
                        
                        $parsing = false;
                        $vsisa = $vumur = '-';
                    
                        if ($rs_a->fields['is_pelunasan'] == 'f')
                        {
                        	$tag = "<b>";
                        	$endtag = "</b>";
                        }
                        else
                        {
                        	$tag = "";
                        	$endtag = "";
                        }
                    
                        if ($rs_a->fields['max_tgl'] == null)
                        	$a = date("0000-00-00");
                        else
                        	$a = date($rs_a->fields['max_tgl']);
                    
                        $b = date($contoh);
                        
                        if (($rs_a->fields['is_pelunasan'] == 't') AND ($rs_a->fields['nobukti_penerbitan'] == $orbuk))
                        {
                        	if (($rs_a->fields['sisa'] <= $sisanya) AND (date($max_tgl) <= date($contoh)))
                        	{
                        		if ($oo == 'penerbitan muncul')
                        		{
                        			$jumterbit_spk += $rs_a->fields['rupiah_penerbitan'];
                        			$jumlun_spk += $rs_a->fields['rupiah_pel'];
                                    
                                    $subtot_penerbitan += $rs_a->fields['rupiah_penerbitan'];
                                    $subtot_pelunasan += $rs_a->fields['rupiah_pel'];
                                    
                                    $vpenerbitan = '-';
                                    $vpelunasan = $this->format_money2($base, $rs_a->fields['rupiah_pel']);	
                                    $sisa = '-';
                                    $umur = '-';
                                    
                                    $parsing = true;
                         		     $parsing_subtot = true;
                        		}
                                else
                                {
                                    $parsing = false;
                                }					
                        		
                        	}
                        	else
                        	{				
                        		if ($ket == 'Salah Membuku')
                        			$ket = 'Salah Membuku';
                        		else
                        			$ket = '';
                        
                        		$jumterbit_spk += $rs_a->fields['rupiah_penerbitan'];
                        		$jumlun_spk += $rs_a->fields['rupiah_pel'];
                                
                                $subtot_penerbitan += $rs_a->fields['rupiah_penerbitan'];
                                $subtot_pelunasan += $rs_a->fields['rupiah_pel'];
                                    
                                $vpenerbitan = '-';
                                $vpelunasan = $this->format_money2($base, $rs_a->fields['rupiah_pel']);
                                $sisa = '-';
                                $umur = '-';
                                
                                $parsing = true;
                                $parsing_subtot = true;	
                        	}
                        }
                        else if (($rs_a->fields['is_pelunasan'] == 'f') AND ($rs_a->fields['sisa'] == 0) AND (date($a) <= date($b)))
                        {
                        	$oo = 'penerbitan tidak muncul';
                            $vpenerbitan = '-';
                            $vpelunasan = $this->format_money2($base, $rs_a->fields['rupiah_pel']);
                            $parsing = false;
                        }
                        else if (($rs_a->fields['is_pelunasan'] != 'f') AND ($rs_a->fields['nobukti_penerbitan'] != $orbuk))
                        {	
                        	$ket = 'Salah Membuku';
                        	
                        	$jumterbit_spk += $rs_a->fields['rupiah_penerbitan'];
                        	$jumlun_spk += $rs_a->fields['rupiah_pel'];
                            
                            $subtot_penerbitan += $rs_a->fields['rupiah_penerbitan'];
                            $subtot_pelunasan += $rs_a->fields['rupiah_pel'];
                                    
                            $vpenerbitan = '-';
                            $vpelunasan = $this->format_money2($base, $rs_a->fields['rupiah_pel']);	
                            
                            $parsing = true;
                            $parsing_subtot = true;		
                        }
                        else
                        {	
                        	$o = 1;
                        	$ket = '-';
                        	$oo = 'penerbitan muncul';
                        
                        	$jumterbit_spk += $rs_a->fields['rupiah_penerbitan'];
                        	$jumlun_spk += $rs_a->fields['rupiah_pel'];
                            
                            $subtot_penerbitan += $rs_a->fields['rupiah_penerbitan'];
                            $subtot_pelunasan += $rs_a->fields['rupiah_pel'];
                          
                            $vsisa = $this->format_money2($base, $rs_a->fields['sisa']);
                            $jsisa += $rs_a->fields['sisa'];
                            
                            $subtot_sisa += $rs_a->fields['sisa'];
                            
                            $vumur = $tanggel;
                            $vpenerbitan = $this->format_money2($base, $rs_a->fields['rupiah_penerbitan']);
                            $vpelunasan = '-';
                            
                            $parsing = true;
                            $parsing_subtot = true;
                          
                        }
                        
                        if($parsing)
                        {
                            //echo $rs_a->fields['nobukti']. '<br />';
                            $record_dynamic = array(
                        		'VTGL'				=> $tag.$rs_a->fields['tanggal'].$endtag,
                        		'VNOBUKTI'  	=> $tag.$rs_a->fields['nobukti'].$endtag,
                                'VKODEFAKTUR'  	=> $tag.$rs_a->fields['faktur_pajak'].$endtag,
                        		'VURAIAN'			=> $tag.$rs_a->fields['keterangan'].$endtag, 
                        		'VPENERBITAN' => $tag.$vpenerbitan.$endtag,
                        		'VPELUNASAN'  => $tag.$vpelunasan.$endtag,
                        		'VSISA'				=> $tag.$vsisa.$endtag,
                        		'VUMUR'				=> $tag.$vumur.$endtag,
                        		'VSPK'				=> $tag.(($grptype == 'perspk')? $rs_a->fields['kdnasabah']:$rs_a->fields['kdspk']).$endtag,
                        		'VKET'				=> $tag.$ket.$endtag,
                                'VRUPPER'   => $rs_a->fields['rupiah_penerbitan'],
                                'VRUPPEL'   => $rs_a->fields['rupiah_pel'],
                        	);
                            
                            if($val == 'excel')
                            {
                                $tpl2_excel->assignDynamic('row1', $record_dynamic);			  
                                $tpl2_excel->parseConcatDynamic('row1');
                            }
                            else
                            {
                                $tpl2->assignDynamic('row1', $record_dynamic);			  
                                $tpl2->parseConcatDynamic('row1');
                            }    
                        }
                        
                        if ($rs_a->fields['is_pelunasan'] == 'f')
                        {
                        	$max_tgl = $rs_a->fields['max_tgl'];
                        	$sisanya = $rs_a->fields['sisa'];					
                        }
                        
                        $orbuk = $rs_a->fields['nobukti_penerbitan'];
                        			
                        $rs_a->moveNext();
                        
                        //if($grptype == 'pernsb')
                        //{
                        
                        if($kdspk != $rs_a->fields['kdspk'] || $kdnasabah != $rs_a->fields['kdnasabah'])
                        {
                            if (!$parsing_subtot)
                        	{
                        		//dont parsing
                                //echo $nmspk.' '.$rs_a->fields['kdspk'].$subtot_penerbitan.' not parse<br />';
                        	}
                        	else
                        	{
                                
                                $record_dynamic = array(
                                    'TITLE_HEADSUBTOTAL'    => 'SPK : ',
                                    'VSPK'  => $kdspk,
                                    'VNAMA_SPK' => $nmspk, //$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."'"),
                                    'TITLE_SUBTOTAL'    => 'Jumlah Per SPK '.$kdspk.' '.$nmspk,
                                    'VJUM_PENERBITANSPK' => $this->format_money2($base, $subtot_penerbitan),
                                    'VJUM_PELUNASANSPK' => $this->format_money2($base, $subtot_pelunasan),
                                    'VJUM_SISASPK'  => $this->format_money2($base, ($subtot_sisa)),
                                    'DISP_SUBTOT'   => ($grptype == 'pernsb') ? '' : 'none',
                                );
                                if($val == 'excel')
                                {
                                    $tpl1_excel->assignDynamic('row_subtot', $record_dynamic);
                                    $tpl1_excel->parseConcatDynamic('row_subtot');
                                }
                                else
                                {
                                    $tpl1->assignDynamic('row_subtot', $record_dynamic);
                                    $tpl1->parseConcatDynamic('row_subtot');
                                }
                                
                                if($val == 'excel')
                                    $tpl3_excel->emptyParsedPage();
                                else
                                    $tpl3->emptyParsedPage();
                                
                                $parsing_subtot = false;
                            }
                            
                            $subtot_penerbitan = 0;
                            $subtot_pelunasan = 0; 
                            $subtot_sisa = 0;
                        }
                            
                            
                        //}
                        /*else
                        {
                            $record_dynamic = array(
                                'DISP_SUBTOT'   => 'none'
                            );
                            
                            if($val == 'excel')
                            {
                                $tpl1_excel->assignDynamic('row_subtot', $record_dynamic);			  
                                $tpl1_excel->parseConcatDynamic('row_subtot');
                            }
                            else
                            {
                                $tpl1->assignDynamic('row_subtot', $record_dynamic);			  
                                $tpl1->parseConcatDynamic('row_subtot');
                            }
                            
                            if($val == 'excel')
                                $tpl3_excel->emptyParsedPage();
                            else
                                $tpl3->emptyParsedPage();  
                            
                        }*/
                        
                        
                    	$condition_grouping = false;
                        if($grptype == 'perspk')
                        {
                            $condition_grouping = ($kdspk != $rs_a->fields['kdspk']);    
                        }		
                        else//pernsb
                        {
                            $condition_grouping = ($kdnasabah != $rs_a->fields['kdnasabah']);
                        }
                        
                        if ($condition_grouping)
                        {
                          $ii = 'v';
                        	if ($o < 1)
                        	{
                        		$x = 1;
                        	}
                        	else
                        	{	
                        		$o = 0;
                                //$nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdspk'");
                                //$nmnasabah = $base->dbGetOne("SELECT nmnasabah FROM dnasabah WHERE kdnasabah='$kdnasabah'");
                                
                                $vkdnasabah = ($grptype == 'perspk')?$kdspk:$kdnasabah;
                                $vnmnasabah = ($grptype == 'perspk')?$nmspk:$nmnasabah;
                        		
                                $record_dynamic = array(
                        			'VPENERBITANSPK'	=> $tag.$this->format_money2($base, $jumterbit_spk).$endtag,
                        			'VPELUNASANSPK'  	=> $tag.$this->format_money2($base, $jumlun_spk).$endtag,
                        			'VSISASPK'			=> $tag.$this->format_money2($base, ($jsisa)).$endtag,
                        			'VKDNASABAH'		=> $tag.$vkdnasabah.$endtag,
                        			'VNMNASABAH' 		=> $tag.$vnmnasabah.$endtag,
                                    'GROUP_TYPE'        => ($grptype == 'perspk') ? 'SPK' : 'Nasabah',
                                    'SPK_COL'           => ($grptype == 'perspk') ? 'NSB' : 'SPK',
                        		);
                                
                                if($val == 'excel')
                                {
                                    $tpl_excel->assignDynamic('row', $record_dynamic);
                                    $tpl_excel->parseConcatDynamic('row');
                                }
                                else
                                {
                                    $tpl->assignDynamic('row', $record_dynamic);
                                    $tpl->parseConcatDynamic('row');
                                }
                                $jsisa = 0;

                        	}
                            if($val == 'excel')
                            {
                                $tpl2_excel->emptyParsedPage();
                                $tpl3_excel->emptyParsedPage();
                                
                            }
                            else
                            {
                                $tpl2->emptyParsedPage();
                                $tpl3->emptyParsedPage();
                            }
                                
                        	
                        	$jumlun_spk = 0;
                        	$jumterbit_spk = 0;						  									
                        }
                        else
                          $ii='m';
                    			
                        if ($rs_a->EOF)
                        {	
                          if ($ii == 'm')
                          {
                            //$nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdspk'");
                            //$nmnasabah = $base->dbGetOne("SELECT nmnasabah FROM dnasabah WHERE kdnasabah='$kdnasabah'");
                            
                            $vkdnasabah = ($grptype == 'perspk')?$kdspk:$kdnasabah;
                            $vnmnasabah = ($grptype == 'perspk')?$nmspk:$nmnasabah;
                            
                            $record_dynamic = array(
                                'VPENERBITANSPK'  => $tag.$this->format_money2($base, $jumterbit_spk).$endtag,
                                'VPELUNASANSPK'   => $tag.$this->format_money2($base, $jumlun_spk).$endtag,
                                'VSISASPK'        => $tag.$this->format_money2($base, ($jumterbit_spk - $jumlun_spk)).$endtag,
                                'VKDNASABAH'      => $tag.$kdnasabah.$endtag,
                                'VKDNASABAH'		=> $tag.$vkdnasabah.$endtag,
                                'VNMNASABAH' 		=> $tag.$vnmnasabah.$endtag,
                                'GROUP_TYPE'        => ($grptype == 'perspk') ? 'SPK' : 'Nasabah',
                                'SPK_COL'           => ($grptype == 'perspk') ? 'NSB' : 'SPK',
                            );
                            
                            if($val == 'excel')
                            {
                                $tpl_excel->assignDynamic('row', $record_dynamic);
                                $tpl_excel->parseConcatDynamic('row'); 
                            }
                            else
                            {
                                $tpl->assignDynamic('row', $record_dynamic);
                                $tpl->parseConcatDynamic('row'); 
                            }
                            
                          }
                        	
                            if($val == 'excel')
                            {
                                $tpl_temp_excel->assign('ONE',$tpl_excel,'template');
                                $tpl_temp_excel->parseConcat();
                            }
                            else
                            {
                                $tpl_temp->assign('ONE',$tpl,'template');
                                $tpl_temp->parseConcat();    
                            }

                        }
                    }
                    else //ikhtisar
                    {
                        
                        if (($rs_a->fields['is_pelunasan'] == 'f') && ($rs_a->fields['sisa'] != 0))
                		{
                			$sisa += $rs_a->fields['sisa'];
                			
                            if (intval($umur) < 30)
                				$v1 += $rs_a->fields['sisa'];
                			else if (intval($umur) <= 90)
                				$v1s3 += $rs_a->fields['sisa'];
                            else if (intval($umur) <= 180)
                				$v3s6 += $rs_a->fields['sisa'];
                            else if (intval($umur) <= 365)
                				$v6s12 += $rs_a->fields['sisa'];      
                			else
                				$v12 += $rs_a->fields['sisa'];
                            
                            $parsing = true;
                		}
                        else if (($rs_a->fields['is_pelunasan'] == 'f') && ($rs_a->fields['sisa'] == 0) && (date($rs_a->fields['max_tgl']) <= date($contoh)))
                        {
                            //no action   
                        }
                        else if(($rs_a->fields['is_pelunasan'] == 'f') && ($rs_a->fields['sisa'] == 0) && (date($rs_a->fields['max_tgl']) > date($contoh)))
                        {
                            $parsing = true;
                        }
                		else
                        {
                            $i = 1;
                            //$parsing = true;
                        }
                    	
                        $penerbitan = $rs_a->fields['rupiah_penerbitan'];		
                    		$rs_a->moveNext();
                    
                    		$condition_grouping = false;
                            if($grptype == 'perspk')
                            {
                                $condition_grouping = ($kdspk != $rs_a->fields['kdspk']);
                                $kdvendor = $kdspk;
                                $nmvendor = $nmspk; //$base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='$kdspk'");
                            }		
                            else//pernsb
                            {
                                $condition_grouping = ($kdnsb != $rs_a->fields['kdnasabah']);
                                $kdvendor = $kdnsb;
                                $nmvendor = $nmnsb; //$base->dbGetOne("SELECT nmnasabah FROM dnasabah WHERE kdnasabah='$kdnsb'");
                            }
                        
                            if ($condition_grouping)
                            {
                                $i = 'berubah';
                            }
                    		else
                            {
                                $i = 'tetep';
                            }
                                
                    		if ($condition_grouping)
                    		{
                    			$kdspk = $rs_a->fields['kdspk'];
                                $kdnsb = $rs_a->fields['kdnasabah'];
                                $nmspk = $rs_a->fields['nmspk'];
                                $nmnsb = $rs_a->fields['nmnasabah'];
                                
                                $sum = $sisa - $v1 - $v1s3 - $v3s6 - $v6s12 - $v12;
                    			if ($sum < 0)
                    				$ket = 'Salah Pembukuan';
                    			else
                    				$ket = '-';
                    				
                    			$jsisa += $sisa;
                    			$vj1 += $v1;
                    			$vj1s3 += $v1s3;
                    			$vj3s6 += $v3s6;
                                $vj6s12 += $v6s12;
                                $vj12 += $v12;
                                
                                if($parsing)
                                {
                                    $record_dynamic = array(
                        				'VKDNASABAH'    => $tag.$kdvendor.$endtag,
                        				'VNMNASABAH'    => $tag.$nmvendor.$endtag,
                        				'VSISA'         => $tag.$this->format_money2($base, $sisa).$endtag, 
                        				'V1' 			=> $tag.$this->format_money2($base, $v1).$endtag,
                        				'V1S3'  		=> $tag.$this->format_money2($base, $v1s3).$endtag,
                        				'V3S6'			=> $tag.$this->format_money2($base, $v3s6).$endtag,
                                        'V6S12'			=> $tag.$this->format_money2($base, $v6s12).$endtag,
                                        'V12'			=> $tag.$this->format_money2($base, $v12).$endtag,
                        				'VKET'		    => $tag.$ket.$endtag,
                        			);
                                    //RIOOO & HAEKAL
                                    /*if($val == 'excel')
                                    {
                                        $tpl1_excel->assignDynamic('row1', $record_dynamic);			  
                                        $tpl1_excel->parseConcatDynamic('row1');
                                    }
                                    else
                                    {
                                        $tpl1->assignDynamic('row1', $record_dynamic);			  
                                        $tpl1->parseConcatDynamic('row1');
                                    }*/    
									
									if($val == 'excel')
									{
										$tpl2_excel->assignDynamic('row1', $record_dynamic);			  
										$tpl2_excel->parseConcatDynamic('row1');
									}
									else
									{
										$tpl2->assignDynamic('row1', $record_dynamic);			  
										$tpl2->parseConcatDynamic('row1');
									}
                                }
                                
                    				
                    			
                    			$sisa = 0;
                    			$v1 = 0;
                    			$v1s3 = 0;
                    			$v3s6 = 0;
                                $v6s12 = 0;
                                $v12 = 0;
                                $penerbitan = 0;
                                $parsing = false;					
                    		} 
                    		
                    		if ($rs_a->EOF)
                    		{
                    			if ($i == 'tetep')
                    			{
                    				$sum = $sisa - $v1 - $v1s3 - $v3s6 - $v6s12 - $v12;
                    				if ($sum < 0)
                    					$ket = 'Salah Pembukuan';
                    				else
                    					$ket = '-';
                    					
                    				$jsisa += $sisa;
                                    $vj1 += $v1;
                                    $vj1s3 += $v1s3;
                                    $vj3s6 += $v3s6;
                                    $vj6s12 += $v6s12;
                                    $vj12 += $v12; 
                                    
                                    $record_dynamic = array(
                    					'VKDNASABAH'   => $tag.$kdvendor.$endtag,
                    					'VNMNASABAH'   => $tag.$nmvendor.$endtag,
                    					'VSISA'        => $tag.$this->format_money2($base, $sisa).$endtag, 
                    					'V1'           => $tag.$this->format_money2($base, $v1).$endtag,
                        				'V1S3'  	   => $tag.$this->format_money2($base, $v1s3).$endtag,
                        				'V3S6'         => $tag.$this->format_money2($base, $v3s6).$endtag,
                                        'V6S12'		   => $tag.$this->format_money2($base, $v6s12).$endtag,
                                        'V12'           => $tag.$this->format_money2($base, $v12).$endtag,
                    					'VKET'		   => $tag.$ket.$endtag,
                    				);
									//RIOOO & HAEKAL
                                    /*
                                    if($val == 'excel')
                                    {
                                        $tpl1_excel->assignDynamic('row1', $record_dynamic);			  
                    				    $tpl1_excel->parseConcatDynamic('row1');
                                    }
                                    else
                                    {
                                        $tpl1->assignDynamic('row1', $record_dynamic);			  
                    				    $tpl1->parseConcatDynamic('row1');
                                    }
                    				*/
									if($val == 'excel')
									{
										$tpl2_excel->assignDynamic('row1', $record_dynamic);			  
										$tpl2_excel->parseConcatDynamic('row1');
									}
									else
									{
										$tpl2->assignDynamic('row1', $record_dynamic);			  
										$tpl2->parseConcatDynamic('row1');
									}
									
                    				$sisa = 0;
                    				$v1 = 0;
                        			$v1s3 = 0;
                        			$v3s6 = 0;
                                    $v6s12 = 0;
                                    $v12 = 0;									
                    			}
                    			
                                $record_static = array(
                    				'VJSISA'	=> $this->format_money2($base, $jsisa),
                    				'VJ1' 			=> $tag.$this->format_money2($base, $vj1).$endtag,
                    				'VJ1S3'  		=> $tag.$this->format_money2($base, $vj1s3).$endtag,
                    				'VJ3S6'			=> $tag.$this->format_money2($base, $vj3s6).$endtag,
                                    'VJ6S12'			=> $tag.$this->format_money2($base, $vj6s12).$endtag,
                                    'VJ12'			=> $tag.$this->format_money2($base, $vj12).$endtag,
                                    'KODE_VENDOR'   => ($grptype == 'perspk')? 'Kode SPK':'Kode Vendor',
                                    'NAMA_VENDOR'   => ($grptype == 'perspk')? 'Nama Proyek':'Nama Vendor',
                    			);
                                
                    			if($val == 'excel')
                                {
                                    $tpl_excel->Assign($record_static);
                                    $tpl_temp_excel->assign('ONE',$tpl_excel,'template');
                                    $tpl_temp_excel->parseConcat();
                                }
                                else
                                {
                                    $tpl->Assign($record_static);
                                    $tpl_temp->assign('ONE',$tpl,'template');
                                    $tpl_temp->parseConcat();
                                }
		
                    		}
                    }
                  																										 
                }	//end of while
                
                if($val == 'excel')
                {
                    $filename = $base->kcfg['basedir']."files/".$kdreport."_PER_".(($grptype=='perspk')?"SPK":"NSB")."_".$kddiv."_".$ryear."_".$rmonth."_".$type."_for_excel.html";
                    $isi_excel = & $tpl_temp_excel->parsedPage();
        			$this->cetak_to_file($base,$filename,$isi_excel);
                }
                else
                {
                    $filename = $base->kcfg['basedir']."files/".$kdreport."_PER_".(($grptype=='perspk')?"SPK":"NSB")."_".$kddiv."_".$ryear."_".$rmonth."_".$type.".html";
                    $isi = & $tpl_temp->parsedPage();
        			$this->cetak_to_file($base,$filename,$isi);
        			$this->tpl =& $tpl_temp;
                }
            }
            	
        }
        else if($this->get_var('is_excel')=='t')
        {
            $filename = $base->kcfg['basedir']."files/".$kdreport."_PER_".(($grptype=='perspk')?"SPK":"NSB")."_".$kddiv."_".$ryear."_".$rmonth."_".$type."_for_excel.html";
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
            $filename = $base->kcfg['basedir']."files/".$kdreport."_PER_".(($grptype=='perspk')?"SPK":"NSB")."_".$kddiv."_".$ryear."_".$rmonth."_".$type.".html";
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
  
    function sub_accounting_report_form___($base)/*{{{*/
    {
        if (@$_GET['kdreport'] === 'rekon_pajak')
			$tpl = $base->_get_tpl('accounting_report_form_rekon.html');
		else
        	$tpl = $base->_get_tpl('accounting_report_form.html');
        
        $dp = new dateparse;
        $m = $this->get_var('m',date('m'));
        $y = $this->get_var('y',date('Y'));
        $show_tampil = $this->get_var('show_tampil');
        
		$rekon_year_start 	= "<select name=\"year_start\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        $rekon_year_end 	= "<select name=\"year_end\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
		
        $bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
        $tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        loadClass('modules.accounting');
        //$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}'");
        if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

        
		if ($this->S['curr_wil'] != '')
                {
					$sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
                }
		$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}' $sql_wil");

        
        $rsdiv = $base->dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
        $combo_div = $rsdiv->getMenu2('kddiv', $this->get_var('kddiv'), false, false);    
  
        $tpl->Assign(array(
            'BLN'	=> $bln_,
            'TAHUN'	=> $tahun_,
            'TAHUN_START' => $rekon_year_start,
            'TAHUN_END'   => $rekon_year_end,
            'KDREPORT'  => $this->get_var('kdreport'),
            'GRPTYPE'  => $this->get_var('grptype'),
            'TITLE' => 'Report Type : ' . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid='".$this->get_var('asmid')."'"),
            'SID'   => MYSID,
            'RSID'   => MYSID,
            'HIDE_TAMPIL'   => '',
            'HIDE_PROCESS'   => '',
            'HIDE_EXCEL'   => '',
            'UNTUK' => $this->get_var('sub','opensystem'),
            'VCMD'  => $this->get_var('sub','opensystem'),
            'VMOD'  => $this->get_var('mod'),
            'VSUB'  => $this->get_var('sub'),
            'VASMID'    => $this->get_var('asmid'),
            'REP_TYPE'  => $this->get_var('sub'),
            'REP_NAME'   => $this->get_var('kdreport'),
            'VSPK'  => $kdspk,
            'VKONSOLIDASI'   => $this->get_var('konsolidasi'),
            'DIV_TITLE' => 'Departemen RK',
            'CO_DIV'    => $combo_div,
            'FORM_ACTION' => 'ci/index.php/labarugi/divisi',
            'IS_KOMPRE' => 'true',
        ));
        
        $this->tpl = $tpl;
    }/*}}}*/

    function sub_accounting_report_form($base)/*{{{*/
    {
        //$base->db->debug = true;
        if (@$_GET['kdreport'] === 'rekon_pajak')
			$tpl = $base->_get_tpl('accounting_report_form_rekon.html');
		else
        	$tpl = $base->_get_tpl('accounting_report_form.html');
        
        $dp = new dateparse;
        $m = $this->get_var('m',date('m'));
        $y = $this->get_var('y',date('Y'));
        $show_tampil = $this->get_var('show_tampil');
        
		$rekon_year_start 	= "<select name=\"year_start\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        $rekon_year_end 	= "<select name=\"year_end\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
		
        $bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
        $tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        loadClass('modules.accounting');
        //$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}'");
        if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

        
		if ($this->S['curr_wil'] != '')
                {
					$sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
                }
		$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}' $sql_wil");

        
        $rsdiv = $base->dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
        $combo_div = $rsdiv->getMenu2('kddiv', $this->get_var('kddiv'), false, false);    
  
        $tpl->Assign(array(
            'BLN'	=> $bln_,
            'TAHUN'	=> $tahun_,
            'TAHUN_START' => $rekon_year_start,
            'TAHUN_END'   => $rekon_year_end,
            'KDREPORT'  => $this->get_var('kdreport'),
            'GRPTYPE'  => $this->get_var('grptype'),
            'TITLE' => 'Report Type : ' . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid='".$this->get_var('asmid')."'"),
            'SID'   => MYSID,
            'RSID'   => MYSID,
            'HIDE_TAMPIL'   => '',
            'HIDE_PROCESS'   => '',
            'HIDE_EXCEL'   => '',
            'UNTUK' => $this->get_var('sub','opensystem'),
            'VCMD'  => $this->get_var('sub','opensystem'),
            'VMOD'  => $this->get_var('mod'),
            'VSUB'  => $this->get_var('sub'),
            'VASMID'    => $this->get_var('asmid'),
            'REP_TYPE'  => $this->get_var('sub'),
            'REP_NAME'   => $this->get_var('kdreport'),
            'VSPK'  => $kdspk,
            'VKONSOLIDASI'   => $this->get_var('konsolidasi'),
            'DIV_TITLE' => 'Departemen RK',
            'CO_DIV'    => $combo_div,
        ));
        
        $this->tpl = $tpl;
    }/*}}}*/

     function sub_accounting_form_rlkompre($base)/*{{{*/
    {
        if (@$_GET['kdreport'] === 'rekon_pajak')
			$tpl = $base->_get_tpl('accounting_report_form_rekon.html');
		else
        	$tpl = $base->_get_tpl('accounting_report_form_kompre.html');
        
        $dp = new dateparse;
        $m = $this->get_var('m',date('m'));
        $y = $this->get_var('y',date('Y'));
        $show_tampil = $this->get_var('show_tampil');
        
		$rekon_year_start 	= "<select name=\"year_start\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        $rekon_year_end 	= "<select name=\"year_end\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
		
        $bln_  = "<select name=\"month_\" class=\"buttons\">\r\n".dateparse::get_combo_option_month_long($m)."</select>&nbsp;\r\n";
        $tahun_ = "<select name=\"year_\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        loadClass('modules.accounting');
        //$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}'");
        if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
		else $sql_wil = '';

        
		if ($this->S['curr_wil'] != '')
                {
					$sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
                }
		$kdspk = accounting::get_htmlselect_kdspk($base,'kdspk',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}' $sql_wil");

        
        $rsdiv = $base->dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
        $combo_div = $rsdiv->getMenu2('kddiv', $this->get_var('kddiv'), false, false);    
  
        $tpl->Assign(array(
            'BLN'	=> $bln_,
            'TAHUN'	=> $tahun_,
            'TAHUN_START' => $rekon_year_start,
            'TAHUN_END'   => $rekon_year_end,
            'KDREPORT'  => $this->get_var('kdreport'),
            'GRPTYPE'  => $this->get_var('grptype'),
            'TITLE' => 'Report Type : ' . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid='".$this->get_var('asmid')."'"),
            'SID'   => MYSID,
            'RSID'   => MYSID,
            'HIDE_TAMPIL'   => '',
            'HIDE_PROCESS'   => '',
            'HIDE_EXCEL'   => '',
            'UNTUK' => $this->get_var('sub','opensystem'),
            'VCMD'  => $this->get_var('sub','opensystem'),
            'VMOD'  => $this->get_var('mod'),
            'VSUB'  => $this->get_var('sub'),
            'VASMID'    => $this->get_var('asmid'),
            'REP_TYPE'  => $this->get_var('sub'),
            'REP_NAME'   => $this->get_var('kdreport'),
            'VSPK'  => $kdspk,
            'VKONSOLIDASI'   => $this->get_var('konsolidasi'),
            'DIV_TITLE' => 'Departemen RK',
            'CO_DIV'    => $combo_div,
            'FORM_ACTION' => 'ci/index.php/labarugi/divisi/komprehensif',
            'IS_KOMPRE' => 'true',
        ));
        
        $this->tpl = $tpl;
    }/*}}}*/
    
        
    function sub_bpdp($base)/*{{{*/
    {
    	//echo 'bpdp_coy';
        $this->get_valid_app('SDV');

		$kddiv 		= $this->S['curr_divisi'];
		$divname 	= $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$table 		= "v_opensystem_".strtolower($this->S['curr_divisi']);
		$type 		= $this->get_var('tbtype');
        $kdreport 	= $this->get_var('kdreport',$kdreport);
        $grptype 	= $this->get_var('grptype');
        $ryear 		= $this->get_var('ryear',date('Y'));
        $rmonth 	= $this->get_var('rmonth', date('m'));
        $is_proses 	= $this->get_var('is_proses');
        
        $period = date("Y-m-d",mktime(0,0,0,$rmonth,1,$ryear));
        $period_begin = date("Y-m-d",mktime(0,0,0,1,1,$ryear));
        $dp = new dateparse();
		
		// rul
		$el_total = array(
			array(
				'Saldo Tahun Lalu_' => array(
					'VTOTMATERIAL' 		=> 0,
					'VTOTUPAH' 			=> 0,
					'VTOTPERALATAN' 	=> 0,
					'VTOTSUBKON' 		=> 0,
		            'VTOTSEKRETARIAT'  	=> 0,
		            'VTOTFASILITAS'  	=> 0,
		            'VTOTPERSONALIA'  	=> 0,
		            'VTOTKEUANGAN'  	=> 0,
		            'VTOTPPH'  			=> 0,
		            'VTOTKENDARAAN'  	=> 0,
		            'VTOTPENGUJIAN'  	=> 0,
		            'VTOTUMUM'  		=> 0,
		            'VTOTJUMLAH'   		=> 0,
					'VTOTPEMASARAN' 	=> 0,
					'VTOTBEBAN' 		=> 0
				),
				'Tahun ini : Bulan ini' => array(
					'VTOTMATERIAL' 		=> 0,
					'VTOTUPAH' 			=> 0,
					'VTOTPERALATAN' 	=> 0,
					'VTOTSUBKON' 		=> 0,
		            'VTOTSEKRETARIAT'  	=> 0,
		            'VTOTFASILITAS'  	=> 0,
		            'VTOTPERSONALIA'  	=> 0,
		            'VTOTKEUANGAN'  	=> 0,
		            'VTOTPPH'  			=> 0,
		            'VTOTKENDARAAN'  	=> 0,
		            'VTOTPENGUJIAN'  	=> 0,
		            'VTOTUMUM'  		=> 0,
		            'VTOTJUMLAH'   		=> 0,
					'VTOTPEMASARAN' 	=> 0,
					'VTOTBEBAN' 		=> 0
				),
				's/d Bulan ini' => array(
					'VTOTMATERIAL' 		=> 0,
					'VTOTUPAH' 			=> 0,
					'VTOTPERALATAN' 	=> 0,
					'VTOTSUBKON' 		=> 0,
					
		            'VTOTSEKRETARIAT'  	=> 0,
		            'VTOTFASILITAS'  	=> 0,
		            'VTOTPERSONALIA'  	=> 0,
		            'VTOTKEUANGAN'  	=> 0,
					'VTOTPPH' 			=> 0,
		            'VTOTKENDARAAN'  	=> 0,
		            'VTOTPENGUJIAN'  	=> 0,
		            'VTOTUMUM'  		=> 0,
		            'VTOTJUMLAH'   		=> 0,
					'VTOTPEMASARAN' 	=> 0,
					'VTOTBEBAN' 		=> 0
				),
				'Saldo s/d saat ini' => array(
					'VTOTMATERIAL' 		=> 0,
					'VTOTUPAH' 			=> 0,
					'VTOTPERALATAN' 	=> 0,
					'VTOTSUBKON' 		=> 0,
		            'VTOTSEKRETARIAT'  	=> 0,
		            'VTOTFASILITAS'  	=> 0,
		            'VTOTPERSONALIA'  	=> 0,
		            'VTOTKEUANGAN'  	=> 0,
		            'VTOTPPH'  			=> 0,
		            'VTOTKENDARAAN'  	=> 0,
		            'VTOTPENGUJIAN'  	=> 0,
		            'VTOTUMUM'  		=> 0,
		            'VTOTJUMLAH'   		=> 0,
					'VTOTPEMASARAN' 	=> 0,
					'VTOTBEBAN' 		=> 0
				)
			)
		);
		$el_increment = 1;
		// end of rul
        error_reporting(E_ALL & ~E_NOTICE);

        $jurnal = 'jurnal_'.$kddiv;
        //$base->db->debug= true;
        if($is_proses=='t')
		{
			//echo 'type::_'.$type;
            $addorder = ($grptype=='perspk')? " j.kdspk,j.kdnasabah,":" j.kdnasabah,j.kdspk,";
            if($type == 'rinci')
            {
                $tpl 		= $base->_get_tpl('report_bpdp_printable_rinci_ch.html');
                $tpl_excel 	= $base->_get_tpl('report_bpdp_printable_rinci.html');
            }
            else
            {
                $tpl = $base->_get_tpl('report_bpdp_printable_ikhtisar.html');
                // $tpl_excel = $base->_get_tpl('report_bpdp_printable_ikhtisar.html');
                $tpl_excel 	= $base->_get_tpl('report_bpdp_printable_ikhtisar_excel_ch.html'); 	// rul
            }
            
            if($grptype == 'perspk')
            {
                $addsql_select 	= "sp.nmspk,";
                $addsql_from 	= " LEFT JOIN dspk sp ON (j.kdspk=sp.kdspk AND sp.kddiv=j.kddivisi)";
                $addsql_group 	= "j.kdspk, sp.nmspk, j.kdnasabah,";
            }
            else if($grptype == 'perwil')
            {
                $addsql_select 	= "wl.namawilayah,";
                $addsql_from 	= "LEFT JOIN dwilayah wl ON (substr(j.nobukti,1,2)=wl.kdwilayah AND j.kddivisi=wl.kddivisi)";
                $addsql_group 	= "substr(j.nobukti,1,2), wl.namawilayah, j.kdspk, j.kdnasabah,";
            }

            $monthperiod = explode('-',$period);

            $curr_divisi = strtolower($this->S['curr_divisi']);
            $sql = "	SELECT 
                            SUM(CASE WHEN tp.kdprs = 'L1' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS material,
                            SUM(CASE WHEN tp.kdprs = 'L2' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS upah,
                            SUM(CASE WHEN tp.kdprs = 'L3' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS peralatan,
                            SUM(CASE WHEN tp.kdprs = 'L4' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS subkon,
                            SUM(CASE WHEN tp.kdprs = 'T1' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS sekretariat,
                            SUM(CASE WHEN tp.kdprs = 'T2' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS fasilitas,
                            SUM(CASE WHEN tp.kdprs = 'T3' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS personalia,
                            SUM(CASE WHEN tp.kdprs = 'T4' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS keuangan,
                            (SUM(CASE WHEN tp.kdprs = 'TH' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END)+SUM(CASE WHEN tp.kdprs = 'TI' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END)) AS pph,
                            SUM(CASE WHEN tp.kdprs = 'T5' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS kendaraan,
                            SUM(CASE WHEN tp.kdprs = 'T6' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS pengujian,
                            SUM(CASE WHEN tp.kdprs = 'T7' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS umum,							
                            SUM(CASE WHEN tp.kdprs = 'T8' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS pemasaran,
                            SUM(CASE WHEN tp.kdprs = 'T9' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0) ELSE 0 END) AS beban,
                            SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*COALESCE(j.rupiah,0)) AS jumlah,
                            to_char(j.tanggal,'yyyy-mm-dd') as tanggal,j.nobukti,j.keterangan, j.kdnasabah, j.kdspk,substr(j.nobukti,1,2) as kdwilayah,
                            {$addsql_select}
                            CASE WHEN DATE(date_trunc('month', j.tanggal)) = '{$period}' THEN 'bln_ini'
                                WHEN DATE(date_trunc('month', j.tanggal)) BETWEEN '{$period_begin}' AND '{$period}' THEN 'sd_bln_ini'
                                ELSE 'thn_lalu' END AS ket_total  
                        FROM {$jurnal} j {$addsql_from}, t_perk tp 
                        WHERE DATE(date_trunc('month', j.tanggal)) <= '{$period}' {$addsql_where}
                            AND j.kdperkiraan BETWEEN tp.perawal AND tp.perakhir
                            AND j.isapp='t' AND coalesce(j.isdel,'f')='f'
							AND (tp.kdprs LIKE 'L%' OR tp.kdprs LIKE 'T%')
                            AND j.kdspk IN (SELECT DISTINCT x.kdspk FROM jurnal_{$curr_divisi} x, t_perk tpx, dspk ds
                            				WHERE 
                            				ds.kdspk = x.kdspk AND
											date_part('year', ds.tglselesai) >= '{$monthperiod[2]}'
											AND
                            				DATE(date_trunc('month', x.tanggal)) <= '{$period}' 
                                            	AND x.kdperkiraan BETWEEN tpx.perawal AND tpx.perakhir
                                                AND (tpx.kdprs LIKE 'L%' OR tpx.kdprs LIKE 'T%') )
                        GROUP BY {$addsql_group} tanggal,j.nobukti,j.keterangan,ket_total
                        ORDER BY {$addsql_group} tanggal,nobukti";
                        
                      // echo '<PRE>'.$sql.'</PRE>';
            
            //$base= true;            
            $rs = $base->dbQuery($sql);            
            $tpl1 = & $tpl->defineDynamicBlock('row');
            $tpl2 = & $tpl1->defineDynamicBlock('row1');
            $tpl3 = & $tpl1->defineDynamicBlock('row2');
            
            $tpl1_excel = & $tpl_excel->defineDynamicBlock('row');
            $tpl2_excel = & $tpl1_excel->defineDynamicBlock('row1');
            $tpl3_excel = & $tpl1_excel->defineDynamicBlock('row2');
            
            $tpl_temp = $base->_get_tpl('one_var.html');
    		$this->_fill_static_report($base,&$tpl);
            $this->_fill_static_report($base,&$tpl_excel);
            
            $first = true;
            $array_total = array(
                'thn_lalu'  	=> 'Saldo Tahun Lalu',
                'bln_ini'   	=> 'Tahun ini : Bulan ini',
                'sd_bln_ini'    => 's/d Bulan ini',
                'sd_thn_ini'    => 'Saldo s/d saat ini'
            );
            
            $fld_total = array(
                        'material',
                        'upah',
                        'peralatan',
                        'subkon',
                        'sekretariat',
                        'fasilitas',
                        'personalia',
                        'keuangan',
                        'pph',
                        'kendaraan',
                        'pengujian',
                        'umum',
                        'jumlah',
                        'pemasaran',
                        'beban'
            );
            
            if($rs->EOF)
            {
                $tpl->assign('row', 'No Data Available');
                $tpl_excel->assign('row', 'No Data Available');

               // $tpl2->assign('row1', '');
               // $tpl2_excel->assign('row1', '');
            }
            
            while(!$rs->EOF)
            {
                //if($type == 'rinci')
                //{
                	$conditional_group = ($grptype == 'perspk') ? ($kdspk != $rs->fields['kdspk']) : ($kdspk != $rs->fields['kdwilayah']);
                    if($conditional_group)
                    {
                        $kdspk = ($grptype == 'perspk') ? $rs->fields['kdspk'] : $rs->fields['kdwilayah'];
                        $nmspk = ($grptype == 'perspk') ? $rs->fields['nmspk'] : $rs->fields['namawilayah'];
                        
                        if(!$first)
                        {
                            foreach($array_total as $k => $v)
                            {
                                if($k == 'sd_bln_ini')
                                {
                                    foreach($fld_total as $vvv)
                                    {
                                        $data_total[$vvv][$k] += $data_total[$vvv]['bln_ini'];
                                    }    
                                }
                                
                                foreach($loop = array('normal','excel') as $loop_value)
                                {
                                    if($loop_value == 'excel')
                                    {
                                        $this->no_format_money = false;
                                    }
                                    else
                                    {
                                        $this->no_format_money = false;
                                    }
                                    
                                    
                                    	$dynamic_record = array(
	                                        'VSTR_SALDO'    	=> $v,
	                                        'VTOTMATERIAL' 		=> number_format($data_total['material'][$k] ),
	                                        'VTOTUPAH' 			=> $this->format_money2($base, $data_total['upah'][$k]),
	                                        'VTOTPERALATAN'    	=> $this->format_money2($base, $data_total['peralatan'][$k]),
	                                        'VTOTSUBKON'   		=> $this->format_money2($base, $data_total['subkon'][$k]),
	                                        'VTOTSEKRETARIAT'  	=> $this->format_money2($base, $data_total['sekretariat'][$k]),
	                                        'VTOTFASILITAS'  	=> $this->format_money2($base, $data_total['fasilitas'][$k]),
	                                        'VTOTPERSONALIA'  	=> $this->format_money2($base, $data_total['personalia'][$k]),
	                                        'VTOTKEUANGAN'  	=> $this->format_money2($base, $data_total['keuangan'][$k]),
	                                        'VTOTPPH'  			=> $this->format_money2($base, $data_total['pph'][$k]),
	                                        'VTOTKENDARAAN'  	=> $this->format_money2($base, $data_total['kendaraan'][$k]),
	                                        'VTOTPENGUJIAN'  	=> $this->format_money2($base, $data_total['pengujian'][$k]),
	                                        'VTOTUMUM'  		=> $this->format_money2($base, $data_total['umum'][$k]),
	                                        'VTOTJUMLAH'   		=> $this->format_money2($base, $data_total['jumlah'][$k]),
	                                        'VTOTPEMASARAN'   	=> $this->format_money2($base, $data_total['pemasaran'][$k]),
	                                        'VTOTBEBAN'   		=> $this->format_money2($base, $data_total['beban'][$k])
	                                    );
                                    
                                    
									
									// rul
									if ($el_increment%2 === 1)
									{
										$el_total [$v] ['VTOTMATERIAL'] 	+= $data_total ['material'] [$k];
										$el_total [$v] ['VTOTUPAH'] 		+= $data_total ['upah'] [$k];
										$el_total [$v] ['VTOTPERALATAN'] 	+= $data_total ['peralatan'] [$k];
										$el_total [$v] ['VTOTSUBKON'] 		+= $data_total ['subkon'] [$k];	
																			
										$el_total [$v] ['VTOTSEKRETARIAT'] 	+= $data_total ['sekretariat'] [$k];
										$el_total [$v] ['VTOTFASILITAS'] 	+= $data_total ['fasilitas'] [$k];
										$el_total [$v] ['VTOTPERSONALIA'] 	+= $data_total ['personalia'] [$k];
										$el_total [$v] ['VTOTKEUANGAN'] 	+= $data_total ['keuangan'] [$k];
										$el_total [$v] ['VTOTPPH'] 			+= $data_total ['pph'] [$k];
										$el_total [$v] ['VTOTKENDARAAN'] 	+= $data_total ['kendaraan'] [$k];
										$el_total [$v] ['VTOTPENGUJIAN'] 	+= $data_total ['pengujian'] [$k];
										$el_total [$v] ['VTOTUMUM'] 		+= $data_total ['umum'] [$k];
										
										$el_total [$v] ['VTOTJUMLAH'] 		+= $data_total ['jumlah'] [$k];
										
										$el_total [$v] ['VTOTPEMASARAN'] 	+= $data_total ['pemasaran'] [$k];
										$el_total [$v] ['VTOTBEBAN'] 		+= $data_total ['beban'] [$k];
									}
									
									// print '<h3>'. $dynamic_record['VSTR_SALDO'] .' | '. $dynamic_record['VTOTMATERIAL'] .' | '. $data_total['material'][$k]  .' | '. ($el_increment % 2) .'</h1>'; 	// rul
									$el_increment++;
									// end of rul
                                    
                                    if($loop_value == 'excel')
                                    {
                                        $tpl1_excel->assignDynamic('row2', $dynamic_record);
                                        $tpl1_excel->parseConcatDynamic('row2');
                                    }
                                    else
                                    {
                                        $tpl1->assignDynamic('row2', $dynamic_record);
                                        $tpl1->parseConcatDynamic('row2');    
                                    }   
                                }
                            }
                            
                            //parsing spk/wil
                            $tpl->parseConcatDynamic('row');
                            $tpl2->emptyParsedPage();
                            $tpl3->emptyParsedPage();
                            
                            $tpl_excel->parseConcatDynamic('row');
                            $tpl2_excel->emptyParsedPage();
                            $tpl3_excel->emptyParsedPage();
                        }
                        
                        $dynamic_record = array(
                            'GROUP_TYPE'    => ($grptype == 'perspk') ? 'SPK' : 'Wilayah',
                            'VKDNASABAH'    => $kdspk,
                            'VNMNASABAH'   	=> $nmspk,
                        );
                        
                        $tpl->assignDynamic('row', $dynamic_record);
                        $tpl_excel->assignDynamic('row', $dynamic_record);
                        
                        $first = false;
                        //reset data total
                        $data_total = array();
                     }

                     $adarow=false;
                     if($rs->fields['ket_total'] == 'bln_ini')
                     {
                        foreach($loop = array('normal','excel') as $loop_value)
                        {
                          //echo 'masuk2';
                            if($loop_value == 'excel')
                            {
                                $this->no_format_money = true;
                            }
                            else
                            {
                                $this->no_format_money = false;
                            }
                            $adarow=true;
                            $dynamic_record = array(
                                'VTGL'  		=> $rs->fields['tanggal'],
                                'VNOBUKTI'  	=> $rs->fields['nobukti'],
                                'VURAIAN'   	=> $rs->fields['keterangan'],
                                'VMATERIAL' 	=> number_format( $rs->fields['material']),
                                'VUPAH' 		=> number_format( $rs->fields['upah']),
                                'VPERALATAN'    => number_format( $rs->fields['peralatan']),
                                'VSUBKON'   	=> number_format( $rs->fields['subkon']),
                                'VSEKRETARIAT'  => number_format( $rs->fields['sekretariat']),
                                'VFASILITAS'  	=> number_format( $rs->fields['fasilitas']),
                                'VPERSONALIA' 	=> number_format( $rs->fields['personalia']),
                                'VKEUANGAN'  	=> number_format( $rs->fields['keuangan']),
                                'VPPH'  		=> number_format( $rs->fields['pph']),
                                'VKENDARAAN'  	=> number_format( $rs->fields['kendaraan']),
                                'VPENGUJIAN'  	=> number_format( $rs->fields['pengujian']),
                                'VUMUM'  		=> number_format( $rs->fields['umum']),
                                'VJUMLAH'   	=> number_format( $rs->fields['jumlah']),                              
                                'VPEMASARAN' 	=> number_format( $rs->fields['pemasaran']),
                                'VBEBAN' 		=> number_format( $rs->fields['beban']),
                                'none' 			=> '',
                             );
							//var_dump($dynamic_record);
							//print '<h3>'. $dynamic_record['VSTR_SALDO'] .' | '. $dynamic_record['VTOTMATERIAL'] .'</h1>'; 	// rul
                             
                            if($loop_value == 'excel')
                            {
                                $tpl1_excel->assignDynamic('row1', $dynamic_record);
                                $tpl1_excel->parseConcatDynamic('row1');
                            }
                            else
                            {
                                $tpl1->assignDynamic('row1', $dynamic_record);
                                $tpl1->parseConcatDynamic('row1');
                            }
                        }  
                     }                     
                     else 
                   	{

	                   	$dynamic_record = array(
	                                'VTGL'  		=> '',
	                                'VNOBUKTI'  	=> '',
	                                'VURAIAN'   	=> '',
	                                'VMATERIAL' 	=> '',
	                                'VUPAH' 		=> '',
	                                'VPERALATAN'    => '',
	                                'VSUBKON'   	=> '',
	                                'VSEKRETARIAT'  => '',
	                                'VFASILITAS'  	=> '',
	                                'VPERSONALIA'	=> '',
	                                'VKEUANGAN'  	=> '',
	                                'VPPH'  		=> '',
	                                'VKENDARAAN'  	=> '',
	                                'VPENGUJIAN'  	=> '',
	                                'VUMUM'  		=> '',
	                                'VJUMLAH'   	=> '',
	                                'VPEMASARAN' 	=> '',
	                                'VBEBAN' 		=> '',
	                                'none' 			=> 'none',
	                             );

	                    $tpl1->assignDynamic('row1',$dynamic_record);
	                    $tpl1->parseConcatDynamic('row1');
                   	}
                   	//echo "masuk3";
                     //--- total----//
                    foreach($fld_total as $k => $v)
                    {
                        $data_total[$v][$rs->fields['ket_total']] += $rs->fields[$v]; 
                        $data_total[$v]['sd_thn_ini'] += $rs->fields[$v];
                    }
                    //--- end total----//
                    //var_dump($data_total);
                    $rs->moveNext();
                    //exit;
                    if($rs->EOF)
                    {
                        foreach($array_total as $k => $v)
                        {
                            if($k == 'sd_bln_ini')
                            {
                                foreach($fld_total as $vvv)
                                {
                                    $data_total[$vvv][$k] += $data_total[$vvv]['bln_ini'];
                                }    
                            }
                            
                            foreach($loop = array('normal','excel') as $loop_value)
                            {
                                if($loop_value == 'excel')
                                {
                                    $this->no_format_money = false;
                                }
                                else
                                {
                                    $this->no_format_money = false;
                                }
                                
                                $dynamic_record = array(
                                    'VSTR_SALDO'    	=> $v,
                                    'VTOTMATERIAL' 		=> number_format($data_total['material'][$k]), //$this->format_money2($base, $data_total['material'][$k]),
                                    'VTOTUPAH' 			=> $this->format_money2($base, $data_total['upah'][$k]),
                                    'VTOTPERALATAN' 	=> $this->format_money2($base, $data_total['peralatan'][$k]),
                                    'VTOTSUBKON'   		=> $this->format_money2($base, $data_total['subkon'][$k]),
                                    'VTOTSEKRETARIAT'	=> $this->format_money2($base, $data_total['sekretariat'][$k]),
                                    'VTOTFASILITAS'		=> $this->format_money2($base, $data_total['fasilitas'][$k]),
                                    'VTOTPERSONALIA' 	=> $this->format_money2($base, $data_total['personalia'][$k]),
                                    'VTOTKEUANGAN'  	=> $this->format_money2($base, $data_total['keuangan'][$k]),
                                    'VTOTPPH'  			=> $this->format_money2($base, $data_total['pph'][$k]),
                                    'VTOTKENDARAAN'  	=> $this->format_money2($base, $data_total['kendaraan'][$k]),
                                    'VTOTPENGUJIAN'  	=> $this->format_money2($base, $data_total['pengujian'][$k]),
                                    'VTOTUMUM'  		=> $this->format_money2($base, $data_total['umum'][$k]),
                                    'VTOTJUMLAH'   		=> $this->format_money2($base, $data_total['jumlah'][$k]),
                                    'VTOTPEMASARAN'   	=> $this->format_money2($base, $data_total['pemasaran'][$k]),
                                    'VTOTBEBAN'   		=> $this->format_money2($base, $data_total['beban'][$k])
                                );
									
								// rul
								if ($el_increment%2 === 1)
								{
									$el_total [$v] ['VTOTMATERIAL'] 	+= $data_total ['material'] [$k];
									$el_total [$v] ['VTOTUPAH'] 		+= $data_total ['upah'] [$k];
									$el_total [$v] ['VTOTPERALATAN'] 	+= $data_total ['peralatan'] [$k];
									$el_total [$v] ['VTOTSUBKON'] 		+= $data_total ['subkon'] [$k];	
									
									$el_total [$v] ['VTOTSEKRETARIAT'] 	+= $data_total ['sekretariat'] [$k];
									$el_total [$v] ['VTOTFASILITAS'] 	+= $data_total ['fasilitas'] [$k];
									$el_total [$v] ['VTOTPERSONALIA'] 	+= $data_total ['personalia'] [$k];
									$el_total [$v] ['VTOTKEUANGAN'] 	+= $data_total ['keuangan'] [$k];
									$el_total [$v] ['VTOTPPH'] 			+= $data_total ['pph'] [$k];
									$el_total [$v] ['VTOTKENDARAAN'] 	+= $data_total ['kendaraan'] [$k];
									$el_total [$v] ['VTOTPENGUJIAN'] 	+= $data_total ['pengujian'] [$k];
									$el_total [$v] ['VTOTUMUM'] 		+= $data_total ['umum'] [$k];
																		
									$el_total [$v] ['VTOTJUMLAH'] 		+= $data_total ['jumlah'] [$k];
									
									$el_total [$v] ['VTOTPEMASARAN'] 	+= $data_total ['pemasaran'] [$k];
									$el_total [$v] ['VTOTBEBAN'] 		+= $data_total ['beban'] [$k];
								}else{
									$el_total [$v] ['VTOTMATERIAL'] 	+= $data_total ['material'] [$k];
									$el_total [$v] ['VTOTUPAH'] 		+= $data_total ['upah'] [$k];
								}
								// print '<h3>'. $dynamic_record['VSTR_SALDO'] .' | '. $dynamic_record['VTOTMATERIAL'] .' | '. $data_total['material'][$k]  .' | '. ($el_increment % 2) .'</h1>'; 	// rul
								$el_increment++;
								// end of rul
								
								// print '<h3>'. $dynamic_record['VSTR_SALDO'] .' | '. $dynamic_record['VTOTMATERIAL'] .'</h1>'; 	// rul
                                
                                if($loop_value == 'excel')
                                {
                                    $tpl1_excel->assignDynamic('row2', $dynamic_record);
                                    $tpl1_excel->parseConcatDynamic('row2');
                                }
                                else
                                {
                                    $tpl1->assignDynamic('row2', $dynamic_record);
                                    $tpl1->parseConcatDynamic('row2');
                                }
                            }   
                        }
                        
                        //parsing spk/wil
                        $tpl->parseConcatDynamic('row');
                        $tpl_excel->parseConcatDynamic('row');
                    }                      
                /*}
                else
                {
                    
                }*/               
            }

			include('./get_map_kd_perkiraan.php'); 		// rul
            
            $static_record = array(
                'TITLE' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid=".$this->get_var('asmid').""),
                'VBLN'  => $dp->monthnamelong[$rmonth],
                'VTHN'  => $ryear,
                'DIVNAME'   => $divname,
                'VSPK'  => ($grptype == 'perwil') ? 'Wilayah' : 'SPK',
                
				// rul
				'DEPT_NAME' => strtoupper( get_dept_name($kddiv) ),
				
				'Saldo_Tahun_Lalu_VTOTMATERIAL' 	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTMATERIAL']),
				'Tahun_ini_VTOTMATERIAL' 			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTMATERIAL']),
				'sd_Bulan_Ini_VTOTMATERIAL'	 		=> number_format($el_total ['s/d Bulan ini'] ['VTOTMATERIAL']),
				'Saldo_sd_saat_ini_VTOTMATERIAL' 	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTMATERIAL']),
				
				'Saldo_Tahun_Lalu_VTOTUPAH' 		=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTUPAH']),
				'Tahun_ini_VTOTUPAH' 				=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTUPAH']),
				'sd_Bulan_Ini_VTOTUPAH' 			=> number_format($el_total ['s/d Bulan ini'] ['VTOTUPAH']),
				'Saldo_sd_saat_ini_VTOTUPAH' 		=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTUPAH']),
				
				'Saldo_Tahun_Lalu_VTOTPERALATAN' 	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTPERALATAN']),
				'Tahun_ini_VTOTPERALATAN' 			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTPERALATAN']),
				'sd_Bulan_ini_VTOTPERALATAN' 		=> number_format($el_total ['s/d Bulan ini'] ['VTOTPERALATAN']),
				'Saldo_sd_saat_ini_VTOTPERALATAN' 	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTPERALATAN']),
				
				'Saldo_Tahun_Lalu_VTOTSUBKON' 		=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTSUBKON']),
				'Tahun_ini_VTOTSUBKON' 				=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTSUBKON']),
				'sd_Bulan_Ini_VTOTSUBKON' 			=> number_format($el_total ['s/d Bulan ini'] ['VTOTSUBKON']),
				'Saldo_sd_saat_ini_VTOTSUBKON' 		=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTSUBKON']),
				
				'Saldo_Tahun_Lalu_VTOTSEKRETARIAT'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTSEKRETARIAT']),
				'Tahun_ini_VTOTSEKRETARIAT'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTSEKRETARIAT']),
				'sd_Bulan_Ini_VTOTSEKRETARIAT'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTSEKRETARIAT']),
				'Saldo_sd_saat_ini_VTOTSEKRETARIAT'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTSEKRETARIAT']),
				
				'Saldo_Tahun_Lalu_VTOTFASILITAS'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTFASILITAS']),
				'Tahun_ini_VTOTFASILITAS'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTFASILITAS']),
				'sd_Bulan_Ini_VTOTFASILITAS'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTFASILITAS']),
				'Saldo_sd_saat_ini_VTOTFASILITAS'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTFASILITAS']),
				
				'Saldo_Tahun_Lalu_VTOTPERSONALIA'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTPERSONALIA']),
				'Tahun_ini_VTOTPERSONALIA'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTPERSONALIA']),
				'sd_Bulan_Ini_VTOTPERSONALIA'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTPERSONALIA']),
				'Saldo_sd_saat_ini_VTOTPERSONALIA'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTPERSONALIA']),
				
				'Saldo_Tahun_Lalu_VTOTKEUANGAN'		=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTKEUANGAN']),
				'Tahun_ini_VTOTKEUANGAN'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTKEUANGAN']),
				'sd_Bulan_Ini_VTOTKEUANGAN'			=> number_format($el_total ['s/d Bulan ini'] ['VTOTKEUANGAN']),
				'Saldo_sd_saat_ini_VTOTKEUANGAN'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTKEUANGAN']),
				
				'Saldo_Tahun_Lalu_VTOTPPH'			=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTPPH']),
				'Tahun_ini_VTOTPPH'					=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTPPH']),
				'sd_Bulan_Ini_VTOTPPH'				=> number_format($el_total ['s/d Bulan ini'] ['VTOTPPH']),
				'Saldo_sd_saat_ini_VTOTPPH'			=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTPPH']),
				
				'Saldo_Tahun_Lalu_VTOTKENDARAAN'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTKENDARAAN']),
				'Tahun_ini_VTOTKENDARAAN'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTKENDARAAN']),
				'sd_Bulan_Ini_VTOTKENDARAAN'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTKENDARAAN']),
				'Saldo_sd_saat_ini_VTOTKENDARAAN'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTKENDARAAN']),
				
				'Saldo_Tahun_Lalu_VTOTPENGUJIAN'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTPENGUJIAN']),
				'Tahun_ini_VTOTPENGUJIAN'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTPENGUJIAN']),
				'sd_Bulan_Ini_VTOTPENGUJIAN'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTPENGUJIAN']),
				'Saldo_sd_saat_ini_VTOTPENGUJIAN'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTPENGUJIAN']),
				
				'Saldo_Tahun_Lalu_VTOTUMUM'			=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTUMUM']),
				'Tahun_ini_VTOTUMUM'				=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTUMUM']),
				'sd_Bulan_Ini_VTOTUMUM'				=> number_format($el_total ['s/d Bulan ini'] ['VTOTUMUM']),
				'Saldo_sd_saat_ini_VTOTUMUM'		=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTUMUM']),
								
				'Saldo_Tahun_Lalu_VTOTJUMLAH'		=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTJUMLAH']),
				'Tahun_ini_VTOTJUMLAH'				=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTJUMLAH']),
				'sd_Bulan_Ini_VTOTJUMLAH'			=> number_format($el_total ['s/d Bulan ini'] ['VTOTJUMLAH']),
				'Saldo_sd_saat_ini_VTOTJUMLAH'		=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTJUMLAH']),
				
				'Saldo_Tahun_Lalu_VTOTPEMASARAN'	=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTPEMASARAN']),
				'Tahun_ini_VTOTPEMASARAN'			=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTPEMASARAN']),
				'sd_Bulan_Ini_VTOTPEMASARAN'		=> number_format($el_total ['s/d Bulan ini'] ['VTOTPEMASARAN']),
				'Saldo_sd_saat_ini_VTOTPEMASARAN'	=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTPEMASARAN']),
				
				'Saldo_Tahun_Lalu_VTOTBEBAN'		=> number_format($el_total ['Saldo Tahun Lalu'] ['VTOTBEBAN']),
				'Tahun_ini_VTOTBEBAN'				=> number_format($el_total ['Tahun ini : Bulan ini'] ['VTOTBEBAN']),
				'sd_Bulan_Ini_VTOTBEBAN'			=> number_format($el_total ['s/d Bulan ini'] ['VTOTBEBAN']),
				'Saldo_sd_saat_ini_VTOTBEBAN'		=> number_format($el_total ['Saldo s/d saat ini'] ['VTOTBEBAN'])
				// end of rul
            );
            
            $tpl->assign($static_record);
            $tpl_excel->assign($static_record);
            
            //CREATE FILE -- IRUL
            //CREATE FILE -- IRUL
            if($grptype=='perspk'){
            	$grp_type = 'SPK';
            }else{
            	$grp_type = 'WILAYAH';
            }

            $filename2 = $base->kcfg['basedir']."files/BPDP_PER_".$grp_type."_".$kddiv."_".$ryear."_".$rmonth."_".$type."_for_excel.html";
            //echo $filename;
            $isi_excel = & $tpl_excel->parsedPage();
			$this->cetak_to_file($base,$filename2,$isi_excel);
            
            $filename1 = $base->kcfg['basedir']."files/BPDP_PER_".$grp_type."_".$kddiv."_".$ryear."_".$rmonth."_".$type.".html";
            $isi = & $tpl->parsedPage();
			$this->cetak_to_file($base,$filename1,$isi); 


			//print '<pre>'; print_r($data_total); print '</pre>';	// rul
            //echo 'RINCI_'.$grptype;
            
            $filename  = $base->kcfg['basedir']."files/BPDP_PER_".$grp_type."_".$kddiv."_".$ryear."_".$rmonth."_".$type."_for_excel.html";
            //echo $filename;
            //echo "SATU";


			$fp = fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' , $contents);
            
            $this->tpl = & $tpl;

            
            
        }
        else if($this->get_var('is_excel')=='t')
        {
        	echo 'EXCEL';
            $filename = $base->kcfg['basedir']."files/BPDP_PER_".(($grptype=='perspk')?"SPK":"WILAYAH")."_".$kddiv."_".$ryear."_".$rmonth."_".$type."_for_excel.html";
            $fp = @fopen($filename,"r"); 
    		if (!$fp) 
    			die("The file does not exists!");
    			
    		$contents = fread ($fp, filesize ($filename));
    		
    			header('content-type: application/vnd.ms-excel');
    			header('Content-Disposition: attachment; filename='.$filename.'.xls');
    				
    		fclose ($fp);
    
    		$tpl = $base->_get_tpl('one_var.html');
    		$tpl->assign('ONE', $contents);
    		
    		$this->tpl = $tpl;
        }
        else
        {
        	echo 'IKHTISAR';
            $filename = $base->kcfg['basedir']."files/BPDP_PER_".(($grptype=='perspk')?"SPK":"WILAYAH")."_".$kddiv."_".$ryear."_".$rmonth."_".$type.".html";
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' ,	$contents);
            
            $this->tpl = $tpl;
        }
         

    }/*}}}*/
    
    function sub_laba_rugi__($base)/*{{{*/
    {
		//$base->db->debug= true;
		error_reporting('E_ALL');
    	
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $konsolidasi = $this->get_var('konsolidasi');
        $table = ($konsolidasi == 'yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($this->S['curr_divisi']);
        $kddiv = $this->S['curr_divisi'];
        $divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk');
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $uker = $this->get_var('uker');
        $nobukti = $this->get_var('nobukti');
        $is_proses = $this->get_var('is_proses');
		
		// print '<pre>'. var_export($_REQUEST, true) .'</pre>'; exit;
        
        //$tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl = $base->_get_tpl('laporan_laba_rugi.html');		
        $tpl->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        
		$tpl_spk = $base->_get_tpl('laporan_laba_rugi_spk.html');
		$tpl_spk->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl_spk);
		
        $tpl_excel = $base->_get_tpl('laporan_laba_rugi.html');
        $tpl_excel->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl_excel);
        
		if(($is_proses == 't') and ($nobukti != ''))
		{
			$periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl_spk->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."' ";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."' ";
            }
			
			if ($nobukti != '')
				$and_nobukti_not_like = " AND j.nobukti LIKE '{$nobukti}%' ";
			else
				$and_nobukti_not_like = " AND j.nobukti NOT LIKE '01%' ";
            
            // $sq_bln_lalu = ($month == 1) ? 0 : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sq_bln_lalu = ($month == 1) ? '0' : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sql = "SELECT 
						group_coa,
                        SUM(CASE WHEN date(j.tanggal) < '1-1-".$year."' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END ) AS rupiah_thn_lalu,
                        {$sq_bln_lalu} AS rupiah_bln_lalu,
                        SUM(CASE WHEN date_trunc('month', j.tanggal) = to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-".$year."', 'D-MM-YYYY') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_bln_ini
                    FROM 
						dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-". $year ."', 'D-MM-YYYY')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} 
                            {$and_nobukti_not_like} 
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%' 
                    GROUP BY group_coa 
                    ";
              //echo $sql;
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_thn_lalu'] = $rs->fields['rupiah_thn_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_lalu'] = $rs->fields['rupiah_bln_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_ini'] = $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'] = $rs->fields['rupiah_bln_lalu'] + $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_thn_ini'] = $rs->fields['rupiah_thn_lalu'] + $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'];
                
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            while(!$rs->EOF)
            {
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$parent_header]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                        $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'];    
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'];
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_thn_ini'];
                            }
                            else
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] += $data_neraca[$ival]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] += $data_neraca[$ival]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] += $data_neraca[$ival]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] += $data_neraca[$ival]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] += $data_neraca[$ival]['rupiah_sd_thn_ini'];
                            }
                            
                        }
                    }
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_thn_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'];
                $rupiah_bln_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'];
                $rupiah_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'];
                $rupiah_sd_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                $rupiah_sd_thn_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
				
				/*if ((string)$month === '1')
				{
					$rupiah_sd_bln_ini 	= $rupiah_sd_bln_ini / 2;
					$rupiah_bln_lalu 	= 0;
				}				
                */
                if($rupiah_sd_thn_ini <> 0 || trim($rs->fields['h_d'])=='H')
                {
                    $tpl_spk->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_spk->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_excel->parseConcatDynamic('row');    
                }
                
                
                $rs->moveNext();
            }
            
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl_spk->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl_spk;    
			
		}
        		
		elseif(($is_proses == 't') and ($kdspk == ''))
        {
            $periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."'";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."'";
            }
			
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
			if (count($validDiv) >= 1) {
				$addsql .= " AND j.kddivisi IN (".implode(',', $validDiv).")";
			}
			// end getValidDivisiByDate
			$year_before = ($year-1);
            $sql = "SELECT group_coa, 
					SUM(
						CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$year-01-01') AND date('$year-$month-01')  AND dp.default_debet = 't'
								THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah 
							WHEN date_trunc('month', j.tanggal) BETWEEN date('$year-01-01') AND date('$year-$month-01')   AND dp.default_debet = 'f'
								THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah 
							ELSE 0 
						END) AS rupiah_now,
                    SUM(
                    	CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$year_before-01-01') AND date('$year_before-12-31') AND dp.default_debet = 't'
                    			THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah 
                    	 	 WHEN date_trunc('month', j.tanggal) BETWEEN date('$year_before-01-01') AND date('$year_before-12-31') AND dp.default_debet = 'f'
                    			THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah 
                    		ELSE 0 
                    	END) AS rupiah_last  
                    FROM dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= date('$year-$month-01')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} --AND j.nobukti NOT LIKE '01%'
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%'
                    GROUP BY group_coa
                    ORDER BY group_coa ASC
                    ";
			//die($sql);
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_now'] = $rs->fields['rupiah_now'];
                $rupiah[$rs->fields['group_coa']]['rupiah_last'] = $rs->fields['rupiah_last'];
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type, a.flag_rumus 
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            
            //BEGIN ANTO
            
            
            
            //END ANTO
            while(!$rs->EOF)
            {
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_now'] 	=  $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_last'] 	=  $rupiah[$rs->fields['group_name']]['rupiah_last'];
                    $subtotal[$rs->fields['parent']]['rupiah_now'] 			+= $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$rs->fields['parent']]['rupiah_last'] 		+= $rupiah[$rs->fields['group_name']]['rupiah_last'];
                    
                    $st_A600 = $subtotal[$parent_header]['rupiah_now'];
                   
                    $subtotal[$parent_header]['rupiah_now'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$parent_header]['rupiah_last'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_last'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                    	//print_r($rs->fields['sum_type']);
                        $data_neraca[$rs->fields['group_name']]['rupiah_now'] 	= $subtotal[$rs->fields['parent']]['rupiah_now'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_last'] 	= $subtotal[$rs->fields['parent']]['rupiah_last'];    
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        //var_dump($sum_item);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_now'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_now'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_last'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_last'];
                            }
                            else
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_now'] += $data_neraca[$ival]['rupiah_now'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_last'] += $data_neraca[$ival]['rupiah_last'];    
                            }
                            
                        }
                    }

                    


                    /*if($rs->fields[$items+1]['parent']!==$rs->fields['parent']){
						$konsol_rp_divisi_bln_ini = 0;
						$konsol_rp_divisi_sd_bln_ini = 0;
						$divisi_rupiah_bln_ini = 0;
						$divisi_rupiah_sd_bln_ini = 0;
						$proyek_rupiah_bln_ini = 0;
						$proyek_rupiah_sd_bln_ini = 0;
					}*/
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_now = $data_neraca[$rs->fields['group_name']]['rupiah_now'];
                $rupiah_last = $data_neraca[$rs->fields['group_name']]['rupiah_last'];
				if($rs->fields['group_name'] <> '')
				{
					$z_temp_neraca_t[] =  "INSERT INTO z_temp_neraca_t (kddivisi,tanggal,group_name,rupiah_now,rupiah_last) VALUES ('{$kddiv}','{$year}-".((strlen($month)==1)?'0'.$month:$month) ."-01','".$rs->fields['group_name']."',".(($rupiah_now == '')?0:$rupiah_now).",".(($rupiah_last == '')?0:$rupiah_last).");";
				}
				
                $tpl->assignDynamic('row', array(
                    'VKODE' 			=> '',
                    'VURAIAN'   		=> (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                    'VRUPIAH'   		=> ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now),
                    'VRUPIAH_SEBELUM'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                    'VSTYLE'    		=> $style,
                ));
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', array(
                    'VKODE' 			=> '',
                    'VURAIAN'   		=> (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                    'VRUPIAH'   		=> ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now),
                    'VRUPIAH_SEBELUM'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                    'VSTYLE'    		=> $style,
                ));
                $tpl_excel->parseConcatDynamic('row');
                
                $rs->moveNext();
				
            }
            
			/*$base->db->BeginTrans();
			$sql_delete="DELETE FROM z_temp_neraca_t WHERE kddivisi = '{$kddiv}' and tanggal = '{$year}-".((strlen($month)==1)?'0'.$month:$month)."-01';";
			//die($sql_delete);
			$base->db->Execute($sql_delete);
			$ok = true;
    		foreach($z_temp_neraca_t as $key => $val)
    		{
    			$ok = $base->db->Execute($val);
    			if(!$ok)
    			{
    				$pesan = $base->db->ErrorMsg();
    				$pesan = str_replace('"','',$pesan);
    				$pesan = trim($pesan);
    				break;
    			}
    			//echo $val."<br>";
    		}
			//$base->db->Execute($z_temp_neraca_t);
			
			if($ok)
    		{
    			$base->db->commitTrans();#die('debug');
    			//die("<script language='javascript'>alert('Data Telah Diimport');window.location.replace('?mod=save_excel&cmd=hapus_tmp_excel&random_no=".$random_no."&bs_tmp=$bs_tmp&".SID."')</script>");
    		}
    		else
    		{
    			$base->db->rollBackTrans();
    			//die("<script language='javascript'>alert('".$pesan."');')</script>");	
    		}*/
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        
		elseif(($is_proses == 't') and ($kdspk <> ''))
		{
			$periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl_spk->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."'";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."'";
            }
            
            // $sq_bln_lalu = ($month == 1) ? 0 : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sq_bln_lalu = ($month == 1) ? '0' : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '$year-01-01' AND '$year-".($month-1) ."-01' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sql = "SELECT 
						group_coa,
                        SUM(CASE WHEN date(j.tanggal) < '$year-01-01' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END ) AS rupiah_thn_lalu,
                        {$sq_bln_lalu} AS rupiah_bln_lalu,
                        SUM(CASE WHEN date_trunc('month', j.tanggal) = to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-".$year."', 'D-MM-YYYY') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_bln_ini
                    FROM 
						dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-". $year ."', 'D-MM-YYYY')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} AND j.nobukti NOT LIKE '01%'
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%' 
                    GROUP BY group_coa 
                    ";
              //echo $sql;
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_thn_lalu'] = $rs->fields['rupiah_thn_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_lalu'] = $rs->fields['rupiah_bln_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_ini'] = $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'] = $rs->fields['rupiah_bln_lalu'] + $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_thn_ini'] = $rs->fields['rupiah_thn_lalu'] + $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'];
                
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
		/*
            echo "data: <pre>";
            print_r($rupiah);
            echo "</pre>";
		*/            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type, a.flag_rumus 
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            while(!$rs->EOF)
            {
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$parent_header]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                        $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'];    
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'];
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_thn_ini'];
                            }
                            else
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] += $data_neraca[$ival]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] += $data_neraca[$ival]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] += $data_neraca[$ival]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] += $data_neraca[$ival]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] += $data_neraca[$ival]['rupiah_sd_thn_ini'];
                            }
                            
                        }
                    }
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_thn_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'];
                $rupiah_bln_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'];
                $rupiah_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'];
                $rupiah_sd_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                $rupiah_sd_thn_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
				
				/*if ((string)$month === '1')
				{
					$rupiah_sd_bln_ini 	= $rupiah_sd_bln_ini / 2;
					$rupiah_bln_lalu 	= 0;
				}				
                */
                if($rupiah_sd_thn_ini <> 0 || trim($rs->fields['h_d'])=='H')
                {
                    $tpl_spk->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_spk->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_excel->parseConcatDynamic('row');    
                }
                
                
                $rs->moveNext();
            }
            
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl_spk->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl_spk;    
			
		}
        
		else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
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
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
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

    function sub_laba_rugi($base)/*{{{*/
    {
		//$base->db->debug= true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $konsolidasi = $this->get_var('konsolidasi');
        $table = ($konsolidasi == 'yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($this->S['curr_divisi']);
        $kddiv = $this->S['curr_divisi'];
        $divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk');
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $uker = $this->get_var('uker');
        $nobukti = $this->get_var('nobukti');
        $is_proses = $this->get_var('is_proses');
		
		// print '<pre>'. var_export($_REQUEST, true) .'</pre>'; exit;
        
        //$tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl = $base->_get_tpl('laporan_laba_rugi.html');		
        $tpl->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        
		$tpl_spk = $base->_get_tpl('laporan_laba_rugi_spk.html');
		$tpl_spk->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl_spk);
		
        $tpl_excel = $base->_get_tpl('laporan_laba_rugi.html');
        $tpl_excel->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl_excel);
        
		if(($is_proses == 't') and ($nobukti != ''))
		{
			$periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl_spk->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."' ";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."' ";
            }
			
			if ($nobukti != '')
				$and_nobukti_not_like = " AND j.nobukti LIKE '{$nobukti}%' ";
			else
				$and_nobukti_not_like = " AND j.nobukti NOT LIKE '01%' ";
            
            // $sq_bln_lalu = ($month == 1) ? 0 : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sq_bln_lalu = ($month == 1) ? '0' : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sql = "SELECT 
						group_coa,
                        SUM(CASE WHEN date(j.tanggal) < '1-1-".$year."' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END ) AS rupiah_thn_lalu,
                        {$sq_bln_lalu} AS rupiah_bln_lalu,
                        SUM(CASE WHEN date_trunc('month', j.tanggal) = to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-".$year."', 'D-MM-YYYY') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_bln_ini
                    FROM 
						dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-". $year ."', 'D-MM-YYYY')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} 
                            {$and_nobukti_not_like} 
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%' 
                    GROUP BY group_coa 
                    ";
              //echo $sql;
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_thn_lalu'] = $rs->fields['rupiah_thn_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_lalu'] = $rs->fields['rupiah_bln_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_ini'] = $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'] = $rs->fields['rupiah_bln_lalu'] + $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_thn_ini'] = $rs->fields['rupiah_thn_lalu'] + $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'];
                
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            while(!$rs->EOF)
            {
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$parent_header]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                        $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'];    
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'];
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_thn_ini'];
                            }
                            else
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] += $data_neraca[$ival]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] += $data_neraca[$ival]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] += $data_neraca[$ival]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] += $data_neraca[$ival]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] += $data_neraca[$ival]['rupiah_sd_thn_ini'];
                            }
                            
                        }
                    }
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_thn_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'];
                $rupiah_bln_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'];
                $rupiah_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'];
                $rupiah_sd_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                $rupiah_sd_thn_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
				
				/*if ((string)$month === '1')
				{
					$rupiah_sd_bln_ini 	= $rupiah_sd_bln_ini / 2;
					$rupiah_bln_lalu 	= 0;
				}				
                */
                if($rupiah_sd_thn_ini <> 0 || trim($rs->fields['h_d'])=='H')
                {
                    $tpl_spk->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_spk->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_excel->parseConcatDynamic('row');    
                }
                
                
                $rs->moveNext();
            }
            
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl_spk->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl_spk;    
			
		}
        		
		elseif(($is_proses == 't') and ($kdspk == ''))
        {
            $periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."'";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."'";
            }
			
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
			if (count($validDiv) >= 1) {
				$addsql .= " AND j.kddivisi IN (".implode(',', $validDiv).")";
			}
			// end getValidDivisiByDate
			$year_before = ($year-1);
            $sql = "SELECT group_coa,
						SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$year-01-01') AND date('$year-$month-01') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
							     WHEN date_trunc('month', j.tanggal) BETWEEN date('$year-01-01') AND date('$year-$month-01') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah
							ELSE 0 END) AS rupiah_now,
	                    SUM(CASE WHEN date_trunc('month', j.tanggal) BETWEEN date('$year_before-01-01') AND date('$year_before-12-31') AND dp.default_debet = 't' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah
	                    		 WHEN date_trunc('month', j.tanggal) BETWEEN date('$year_before-01-01') AND date('$year_before-12-31') AND dp.default_debet = 'f' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah
	                    	ELSE 0 END) AS rupiah_last 
           			FROM dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= date('$year-$month-01')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} --AND j.nobukti NOT LIKE '01%'
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(3|4|5|6|7|9)%' AND group_coa <> '0' AND group_coa IS NOT NULL
                    GROUP BY group_coa 
                    ORDER BY group_coa ASC
                    ";
			//die($sql);
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_now'] = $rs->fields['rupiah_now'];
                $rupiah[$rs->fields['group_coa']]['rupiah_last'] = $rs->fields['rupiah_last'];
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            
            //BEGIN ANTO
            
            
            $st_A600 = 0;
            $st_B400 = 0;
            //END ANTO
            while(!$rs->EOF)
            {
            	var_dump($rs->fields['group_name']);
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_now'] =  $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_last'] =  $rupiah[$rs->fields['group_name']]['rupiah_last'];

                    $subtotal[$rs->fields['parent']]['rupiah_now'] 		+= $rupiah[$rs->fields['group_name']]['rupiah_now'];
                    $subtotal[$rs->fields['parent']]['rupiah_last'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_last'];
                    if($rs->fields['group_name']=='A600' ){
	                    $subtotal[$parent_header]['rupiah_now'] 	+= 172701360992;//$rupiah[$rs->fields['group_name']]['rupiah_now'];
	                    $subtotal[$parent_header]['rupiah_last'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_last'];
	                    $st_A600 = 172000000;//$rupiah[$rs->fields['group_name']]['rupiah_now'];
	                    //$st_B400 += $rupiah[$rs->fields['group_name']]['rupiah_now'];
	                    echo 'st_A600::'.var_dump($st_A600);
                	}else{
	                    $subtotal[$parent_header]['rupiah_now'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_now'];
	                    $subtotal[$parent_header]['rupiah_last'] 	+= $rupiah[$rs->fields['group_name']]['rupiah_last'];
                	}
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                        $data_neraca[$rs->fields['group_name']]['rupiah_now'] = $subtotal[$rs->fields['parent']]['rupiah_now'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_last'] = $subtotal[$rs->fields['parent']]['rupiah_last'];    
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_now'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_now'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_last'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_last'];
                            }
                            else
                            {
                            	if($rs->fields['group_name']=='E000'){
                            		$data_neraca[$rs->fields['group_name']]['rupiah_now'] = 112948881+(172701360992-160667961702);
                                	$data_neraca[$rs->fields['group_name']]['rupiah_last'] += $data_neraca[$ival]['rupiah_last']; 
	                            }else{
	                            	$data_neraca[$rs->fields['group_name']]['rupiah_now'] += $data_neraca[$ival]['rupiah_now'];
	                                $data_neraca[$rs->fields['group_name']]['rupiah_last'] += $data_neraca[$ival]['rupiah_last']; 
	                            }
                            }
                        }
                    }
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_now = $data_neraca[$rs->fields['group_name']]['rupiah_now'];
                $rupiah_last = $data_neraca[$rs->fields['group_name']]['rupiah_last'];
				if($rs->fields['group_name'] <> '')
				{
					$z_temp_neraca_t[] =  "INSERT INTO z_temp_neraca_t (kddivisi,tanggal,group_name,rupiah_now,rupiah_last) VALUES ('{$kddiv}','{$year}-".((strlen($month)==1)?'0'.$month:$month)
				."-01','".$rs->fields['group_name']."',".(($rupiah_now == '')?0:$rupiah_now).",".(($rupiah_last == '')?0:$rupiah_last).");";
                
				}
				
                $tpl->assignDynamic('row', array(
                    'VKODE' 			=> '',
                    'VURAIAN'   		=> (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                    'VRUPIAH'   		=> ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now),
                    'VRUPIAH_SEBELUM'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                    'VSTYLE'    		=> $style,
                ));
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', array(
                    'VKODE' 			=> '',
                    'VURAIAN'   		=> (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                    'VRUPIAH'   		=> ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_now),
                    'VRUPIAH_SEBELUM'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_last),
                    'VSTYLE'    		=> $style,
                ));
                $tpl_excel->parseConcatDynamic('row');
                
                $rs->moveNext();
				
            }
            
			/*$base->db->BeginTrans();
			$sql_delete="DELETE FROM z_temp_neraca_t WHERE kddivisi = '{$kddiv}' and tanggal = '{$year}-".((strlen($month)==1)?'0'.$month:$month)."-01';";
			//die($sql_delete);
			$base->db->Execute($sql_delete);
			$ok = true;
    		foreach($z_temp_neraca_t as $key => $val)
    		{
    			$ok = $base->db->Execute($val);
    			if(!$ok)
    			{
    				$pesan = $base->db->ErrorMsg();
    				$pesan = str_replace('"','',$pesan);
    				$pesan = trim($pesan);
    				break;
    			}
    			//echo $val."<br>";
    		}
			//$base->db->Execute($z_temp_neraca_t);
			
			if($ok)
    		{
    			$base->db->commitTrans();#die('debug');
    			//die("<script language='javascript'>alert('Data Telah Diimport');window.location.replace('?mod=save_excel&cmd=hapus_tmp_excel&random_no=".$random_no."&bs_tmp=$bs_tmp&".SID."')</script>");
    		}
    		else
    		{
    			$base->db->rollBackTrans();
    			//die("<script language='javascript'>alert('".$pesan."');')</script>");	
    		}*/
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        
		elseif(($is_proses == 't') and ($kdspk <> ''))
		{
			$periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
            $dp = new dateparse();
            
            $static_record = array(
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => ($year - 1),
                'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
                'PERIODE_TANGGAL'   => $dp->monthnamelong[$month],
                'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
                'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
                'UKER'  => $uker,
                'KDSPK' => $kdspk,
                'KDDIVISI'  => $kddiv,
                'MONTH' => $month,
                'TAHUN' => $year,
                'TAHUN_SEBELUM' => $year-1,
            );
            
            $tpl_spk->assign($static_record);
            $tpl_excel->assign($static_record);
            
            if($kdspk != '')
            {
                $addsql .= " AND j.kdspk='".$kdspk."'";
            }
            
            if($uker != '')
            {
                $addsql .= " AND substr(nobukti,1,2)='".$uker."'";
            }
            
            // $sq_bln_lalu = ($month == 1) ? 0 : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '1-1-{$year}' AND '1-". ($month-1) ."-{$year}' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sq_bln_lalu = ($month == 1) ? '0' : " SUM (CASE WHEN date_trunc('month', j.tanggal) BETWEEN '$year-01-01' AND '$year-".($month-1) ."-01' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END)";
            $sql = "SELECT 
						group_coa,
                        SUM(CASE WHEN date(j.tanggal) < '$year-01-01' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END ) AS rupiah_thn_lalu,
                        {$sq_bln_lalu} AS rupiah_bln_lalu,
                        SUM(CASE WHEN date_trunc('month', j.tanggal) = to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-".$year."', 'D-MM-YYYY') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_bln_ini
                    FROM 
						dperkir dp
                    LEFT JOIN {$table} j ON (
                    		j.kdperkiraan=dp.kdperkiraan 
                    		AND date_trunc('month', j.tanggal) <= to_date('1-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-". $year ."', 'D-MM-YYYY')
                            AND isdel='f' --AND isapp='t' 
                            {$addsql} AND j.nobukti NOT LIKE '01%'
                        )
                    WHERE dp.kdperkiraan SIMILAR TO '(4|5|6|7|9)%' 
                    GROUP BY group_coa 
                    ";
              //echo $sql;
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                $rupiah[$rs->fields['group_coa']]['rupiah_thn_lalu'] = $rs->fields['rupiah_thn_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_lalu'] = $rs->fields['rupiah_bln_lalu'];
                $rupiah[$rs->fields['group_coa']]['rupiah_bln_ini'] = $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'] = $rs->fields['rupiah_bln_lalu'] + $rs->fields['rupiah_bln_ini'];
                $rupiah[$rs->fields['group_coa']]['rupiah_sd_thn_ini'] = $rs->fields['rupiah_thn_lalu'] + $rupiah[$rs->fields['group_coa']]['rupiah_sd_bln_ini'];
                
                $nama_perkiraan[$rs->fields['group_coa']] = $rs->fields['nmperkiraan'];
                 
                $rs->moveNext();
            }
		/*
            echo "data: <pre>";
            print_r($rupiah);
            echo "</pre>";
		*/            
            $sq_lr = "SELECT a.description,a.parent, a.h_d, a.total_group, a.group_name, a.priority, a.l_r,a.sum_type
                                FROM mapping_neraca a
                                WHERE a.visibility='t' AND group_type='laba_rugi'
                                ORDER BY a.urutan";
            $rs = $base->dbQuery($sq_lr);
            while(!$rs->EOF)
            {
                /*if($rs->fields['parent'] == '')
                {
                    $grand_header = $rs->fields['group_name'];
                }
                else */ if( trim($rs->fields['h_d']) == 'H' )
                {
                    $parent_header = $rs->fields['parent'];
                }
                else if(trim($rs->fields['h_d']) == 'D')
                {
                    $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] =  $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$rs->fields['parent']]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                    
                    $subtotal[$parent_header]['rupiah_thn_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_thn_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_lalu'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_lalu'];
                    $subtotal[$parent_header]['rupiah_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_bln_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                    $subtotal[$parent_header]['rupiah_sd_thn_ini'] += $rupiah[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
                }
                else if( in_array(trim($rs->fields['h_d']), array('ST', 'T',  'GT')) )
                {
                    if($rs->fields['sum_type'] == '')
                    {
                        $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_thn_lalu'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] = $subtotal[$rs->fields['parent']]['rupiah_bln_lalu'];    
                        $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_bln_ini'];
                        $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] = $subtotal[$rs->fields['parent']]['rupiah_sd_thn_ini'];
                    }
                    else
                    {
                        $sum_item = explode(";", $rs->fields['sum_type']);
                        foreach($sum_item as $ival)
                        {
                            if( substr(0,1) == '-' )
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] -= $data_neraca[str_replace('-','',$ival)]['rupiah_sd_thn_ini'];
                            }
                            else
                            {
                                $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'] += $data_neraca[$ival]['rupiah_thn_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'] += $data_neraca[$ival]['rupiah_bln_lalu'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'] += $data_neraca[$ival]['rupiah_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'] += $data_neraca[$ival]['rupiah_sd_bln_ini'];
                                $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'] += $data_neraca[$ival]['rupiah_sd_thn_ini'];
                            }
                            
                        }
                    }
                    
                }
                
                $parserow = ( $rs->fields['l_r'] == 'L' )  ? 'row1' : 'row2';
                $style = ( in_array(trim($rs->fields['h_d']), array('H','T','ST','GT'))) ? 'font-weight: bold;' : '';
                $rupiah_thn_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_thn_lalu'];
                $rupiah_bln_lalu = $data_neraca[$rs->fields['group_name']]['rupiah_bln_lalu'];
                $rupiah_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_bln_ini'];
                $rupiah_sd_bln_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_bln_ini'];
                $rupiah_sd_thn_ini = $data_neraca[$rs->fields['group_name']]['rupiah_sd_thn_ini'];
				
				/*if ((string)$month === '1')
				{
					$rupiah_sd_bln_ini 	= $rupiah_sd_bln_ini / 2;
					$rupiah_bln_lalu 	= 0;
				}				
                */
                if($rupiah_sd_thn_ini <> 0 || trim($rs->fields['h_d'])=='H')
                {
                    $tpl_spk->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_spk->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', array(
                        'VKODE' => '',
                        'VURAIAN'   => (trim($rs->fields['description']) == '') ? '&nbsp;' : $rs->fields['description'],
                        'VRUPIAH_TAHUN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_thn_lalu),
                        'VRUPIAH_BULAN_LALU'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_lalu),
                        'VRUPIAH_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_bln_ini),
                        'VRUPIAH_SD_BULAN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_bln_ini),
                        'VRUPIAH_SD_TAHUN_INI'   => ( trim($rs->fields['h_d'])=='H' ) ? '' : $this->format_money2($base, $rupiah_sd_thn_ini),
                        'VSTYLE'    => $style,
                    ));
                    $tpl_excel->parseConcatDynamic('row');    
                }
                
                
                $rs->moveNext();
            }
            
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
            $isi = & $tpl_spk->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl_spk;    
			
		}
        
		else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci_for_excel.html";
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
            $kdreport = "laba_rugi";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$vkdspk.$vuker.$year."_".$month."_rinci.html";
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
    
    function sub_laba_rugi_oldnew($base)/*{{{*/
    {
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $konsolidasi = $this->get_var('konsolidasi');
        $table = ($konsolidasi == 'yes') ? "v_jurnal_konsolidasi" : "jurnal_".strtolower($this->S['curr_divisi']);
        $kddiv = $this->S['curr_divisi'];
        $divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk');
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $uker = $this->get_var('uker');
        
        
        //$tpl = $base->_get_tpl('report_neraca_lajur_divisi_t_neo.html');
        $tpl = $base->_get_tpl('laporan_laba_rugi.html');
        $tpl->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        $periode_tanggal = date("d",mktime(0,0,0,$month,-1,$year));
        $dp = new dateparse();
        $tpl->assign(array(
            'TAHUN' => $year,
            'TAHUN_SEBELUM' => ($year - 1),
            'DIVNAME'   => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' "),
            'KDSPK' => $kdspk,
            'NMSPK' => $nmspk,
            'PERIODE_TANGGAL'   => $periode_tanggal.' '. $dp->monthnamelong[$month],
            'UNIT KERJA'    => ($uker == '') ? '' : 'UNIT KERJA : ',
            'VUNIT_KERJA'   => ($uker == '') ? '' : $uker,
            'UKER'  => $uker,
            'KDSPK' => $kdspk,
            'KDDIVISI'  => $kddiv,
            'MONTH' => $month,
            'TAHUN' => $year,
            'TAHUN_SEBELUM' => $year-1,
        ));
        
        $sql = "SELECT rupiah, item_id, year FROM labarugi_form 
                WHERE kddivisi='".$kddiv."' AND kdspk='".$kdspk."' 
                    AND uker='".$uker."' AND month=".$month." AND year IN (".$year.",".($year-1).")";
        $rs = $base->dbQuery($sql);
        while(!$rs->EOF)
        {
            $man_input[$rs->fields['item_id']][$rs->fields['year']] = $this->format_money3($base, $rs->fields['rupiah']);
            $rs->moveNext();
        }
        
        if($kdspk != '')
        {
            $addsql .= " AND j.kdspk='".$kdspk."'";
        }
        
        if($uker != '')
        {
            $addsql .= " AND substr(nobukti,1,2)='".$uker."'";
        }
        
        $sql = "SELECT dp.kdperkiraan, dp.nmperkiraan,
                SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_now,
                SUM(CASE WHEN date_trunc('month', j.tanggal) <= date('1-".$month."-".($year-1)."') THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*-1*j.rupiah ELSE 0 END) AS rupiah_last
                FROM dperkir dp
                LEFT JOIN {$table} j ON (
                		j.kdperkiraan=dp.kdperkiraan 
                		AND date_trunc('month', j.tanggal) <= date('1-".$month."-".$year."')
                        AND isdel='f' AND isapp='t' {$addsql}
                    )
                WHERE dp.kdperkiraan SIMILAR TO '(3|5|6|7|9)%' 
                GROUP BY dp.kdperkiraan, dp.nmperkiraan
                ";
        $rs = $base->dbQuery($sql);
        
        while(!$rs->EOF)
        {
            
            
            $rupiah[$rs->fields['kdperkiraan']]['rupiah_now'] = $rs->fields['rupiah_now'];
            $rupiah[$rs->fields['kdperkiraan']]['rupiah_last'] = $rs->fields['rupiah_last'];
            $nama_perkiraan[$rs->fields['kdperkiraan']] = $rs->fields['nmperkiraan'];
            
            //parent kdperkiraan
            $parent_kdperkiraan = substr($rs->fields['kdperkiraan'], 0,4);
            $rupiah[$parent_kdperkiraan]['rupiah_now'] += $rs->fields['rupiah_now'];
            $rupiah[$parent_kdperkiraan]['rupiah_last'] += $rs->fields['rupiah_last'];
             
            $rs->moveNext();
        }
        
        $parserow = 'row';
        foreach($this->_laba_rugi_item() as $k => $v)
        {
            if(is_array($v))
            {
                $tpl->assignDynamic($parserow, array(
                    'VKODE'    => '&nbsp;',
                    'VURAIAN'    => '<b>'.strtoupper($k).'</b>',
                    'VRUPIAH'  => '',
                    'VRUPIAH_SEBELUM'  => ''
                ));
                $tpl->parseConcatDynamic($parserow);
                
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
                            if(!is_numeric($kk))
                            {
                                $nama_perkiraan[$kk] = $kk;
                            }
                            $tpl->assignDynamic($parserow, array(
                                'VKODE'    => '&nbsp;',
                                'VURAIAN'    => (strlen($nama_perkiraan[$kk])<2) ? '&nbsp;' : '<b>'.strtoupper($nama_perkiraan[$kk]).'</b>',
                                'VRUPIAH'  => '',
                                'VRUPIAH_SEBELUM'  => ''
                            ));
                            $tpl->parseConcatDynamic($parserow);
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
                                'VKODE'    => $kode,
                                'VURAIAN'    => $nama_perkiraan[$vvv],
                                'VRUPIAH'  => $this->format_money3($base, $rupiah[$vvv]['rupiah_now']),
                                'VRUPIAH_SEBELUM'  => $this->format_money3($base, $rupiah[$vvv]['rupiah_last'])
                            ));
                            $tpl->parseConcatDynamic($parserow);
                            
                            $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                            $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                            
                            $subtotal_rupiah_all[$k]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                            $subtotal_rupiah_all[$k]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                            
                            $subtotal_rupiah_all[$kk]['rupiah_now'] += $rupiah[$vvv]['rupiah_now'];
                            $subtotal_rupiah_all[$kk]['rupiah_last'] += $rupiah[$vvv]['rupiah_last'];
                        }
                        
                        $tpl->assignDynamic($parserow, array(
                            'VKODE'    => '&nbsp;',
                            'VURAIAN'    => '&nbsp;',
                            'VRUPIAH'  => '<b>'.$this->format_money3($base, $subtotal_rupiah_all[$kk]['rupiah_now']).'</b>',
                            'VRUPIAH_SEBELUM'  => '<b>'.$this->format_money3($base, $subtotal_rupiah_all[$kk]['rupiah_last']).'</b>'
                        ));
                        $tpl->parseConcatDynamic($parserow);
                          
                    }
                    else
                    {
                        //echo $kk.' '.$vv.'<br />';
                        if(substr($vv,0,5) == 'input')
                        {
                            $input_now = "<input type='text' name='now[".substr($vv,-1,1)."]' value='".$man_input[substr($vv,-1,1)][$year]."' id='n' onkeyup='entryFormatMoney(this)' onblur='change_negativ_val(this)' />";
                            $input_bef = "<input type='text' name='bef[".substr($vv,-1,1)."]' value='".$man_input[substr($vv,-1,1)][$year-1]."' id='b' onkeyup='entryFormatMoney(this)' onblur='change_negativ_val(this)' />";
                        }
                        
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
                            'VKODE'    => $kode,
                            'VURAIAN'    => strtoupper($nama_perkiraan[$vv]),
                            'VRUPIAH'  => (substr($vv,0,5) == 'input') ? $input_now : $this->format_money3($base, $rupiah[$vv]['rupiah_now']),
                            'VRUPIAH_SEBELUM'  => (substr($vv,0,5) == 'input') ? $input_bef : $this->format_money3($base, $rupiah[$vv]['rupiah_last'])
                        ));
                        $tpl->parseConcatDynamic($parserow);
                        
                        $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$vv]['rupiah_now'];
                        $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$vv]['rupiah_last'];
                        
                        $subtotal_rupiah_all[$k]['rupiah_now'] += $rupiah[$vv]['rupiah_now'];
                        $subtotal_rupiah_all[$k]['rupiah_last'] += $rupiah[$vv]['rupiah_last'];
                    }  
                }
                
                if(substr($vv,0,5) != 'input')
                {
                    $tpl->assignDynamic($parserow, array(
                        'VKODE'    => '&nbsp;',
                        'VURAIAN'    => '<b>TOTAL ' . strtoupper($k) . '</b>',
                        'VRUPIAH'  => '<b>'.$this->format_money3($base, $subtotal_rupiah_all[$k]['rupiah_now']).'</b>',
                        'VRUPIAH_SEBELUM'  => '<b>'.$this->format_money3($base, $subtotal_rupiah_all[$k]['rupiah_last']).'</b>'
                    ));
                    $tpl->parseConcatDynamic($parserow);    
                }
                
                $tpl->assignDynamic($parserow, array(
                    'VKODE'    => '&nbsp;',
                    'VURAIAN'    => '&nbsp;',
                    'VRUPIAH'  => '&nbsp;',
                    'VRUPIAH_SEBELUM'  => '&nbsp;'
                ));
                $tpl->parseConcatDynamic($parserow); 
                
                
            }
            else
            {
                if(substr($v,0,5) == 'input')
                {
                    
                    $input_now = "<input type='text' name='now[".substr($v,-1,1)."]' id='n' value='".$man_input[substr($v,-1,1)][$year]."' onkeyup='entryFormatMoney(this)' onblur='change_negativ_val(this)' />";
                    $input_bef = "<input type='text' name='bef[".substr($v,-1,1)."]' id='b' value='".$man_input[substr($v,-1,1)][$year-1]."' onkeyup='entryFormatMoney(this)' onblur='change_negativ_val(this)' />";
                }
                        
                if(substr($v, 0,4) == 'sum:')
                {
                    switch($v)
                    {
                        case 'sum:labakotor_sebelum_kso':
                            $rupiah[$v]['rupiah_now'] = $rupiah['5101111,5101211,5101311,5101511']['rupiah_now']+$subtotal_rupiah_all['Harga Pokok Penjualan']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['5101111,5101211,5101311,5101511']['rupiah_last']+$subtotal_rupiah_all['Harga Pokok Penjualan']['rupiah_last'];
                            break;
                        case 'sum:labakotor_setelah_kso':
                            $rupiah[$v]['rupiah_now'] = $rupiah['sum:labakotor_sebelum_kso']['rupiah_now']+$rupiah['5201111']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['sum:labakotor_sebelum_kso']['rupiah_last']+$rupiah['5201111']['rupiah_last'];
                            break;
                        case 'sum:labausaha':
                            $rupiah[$v]['rupiah_now'] = $rupiah['sum:labakotor_setelah_kso']['rupiah_now']+$subtotal_rupiah_all['BEBAN USAHA']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['sum:labakotor_setelah_kso']['rupiah_last']+$subtotal_rupiah_all['BEBAN USAHA']['rupiah_last'];
                            break;
                        case 'sum:laba_sebelum_pajak':
                            $rupiah[$v]['rupiah_now'] = $rupiah['sum:labausaha']['rupiah_now']+$subtotal_rupiah_all['PENDAPATAN (BEBAN) LAIN-LAIN']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['sum:labausaha']['rupiah_last']+$subtotal_rupiah_all['PENDAPATAN (BEBAN) LAIN-LAIN']['rupiah_last'];
                            break;
                        case 'sum:laba_setelah_pajak':
                            $rupiah[$v]['rupiah_now'] = $rupiah['sum:laba_sebelum_pajak']['rupiah_now']+$subtotal_rupiah_all['PENGHASILAN (BEBAN) PAJAK']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['sum:laba_sebelum_pajak']['rupiah_last']+$subtotal_rupiah_all['PENGHASILAN (BEBAN) PAJAK']['rupiah_last'];
                            break;
                        case 'sum:total_laba_komprehensif':
                            $rupiah[$v]['rupiah_now'] = $rupiah['sum:laba_setelah_pajak']['rupiah_now']+$subtotal_rupiah_all['PENDAPATAN KOMPREHENSIF LAIN']['rupiah_now'];
                            $rupiah[$v]['rupiah_last'] = $rupiah['sum:laba_setelah_pajak']['rupiah_last']+$subtotal_rupiah_all['PENDAPATAN KOMPREHENSIF LAIN']['rupiah_last'];
                            break;
                        
                    }
                    $nama_perkiraan[$v] = $k;
                }
                /*else if($v == 'input')
                {
                    $rupiah[$v]['rupiah_now'] = "<input type='text' name='t' />";
                    $rupiah[$v]['rupiah_last'] = "<input type='text' name='t' />";
                }*/
                else if(!is_numeric($k))
                { 
                    $arr_v = explode(',', $v);
                    $nama_perkiraan[$v] = $k;
                    $kode = ''; //str_replace(',', '<br />', $vv);
                    foreach($arr_v as $key => $rp)
                    {
                        $rupiah[$v]['rupiah_now'] += $rupiah[$rp]['rupiah_now'];
                        $rupiah[$v]['rupiah_last'] += $rupiah[$rp]['rupiah_last'];
                    }
                }
                else
                {
                    $kode = $v;
                }
                
                $tpl->assignDynamic($parserow, array(
                    'VKODE'    => '', //$v,
                    'VURAIAN'    => '<b>'.strtoupper($nama_perkiraan[$v]).'</b>',
                    'VRUPIAH'  => (substr($v,0,5) == 'input') ? $input_now : '<b>'.$this->format_money3($base, $rupiah[$v]['rupiah_now']).'</b>',
                    'VRUPIAH_SEBELUM'  => (substr($v,0,5) == 'input') ? $input_bef : '<b>'.$this->format_money3($base, $rupiah[$v]['rupiah_last']).'</b>'
                ));
                $tpl->parseConcatDynamic($parserow);
                
                $total_rupiah_all[$parserow]['rupiah_now'] += $rupiah[$v]['rupiah_now'];
                $total_rupiah_all[$parserow]['rupiah_last'] += $rupiah[$v]['rupiah_last'];
                
            }
        }
        
        $this->tpl = $tpl;
    }/*}}}*/
    
    function _laba_rugi_item()/*{{{*/
    {
        $list_lr = array(
            'Pendapatan' => '5101111,5101211,5101311,5101511',
            'Harga Pokok Penjualan' => array(
                '6101'  => array(
                    '6101111',
                    '6101211',
                    'Beban Peralatan' => '6101311,6101321,6101331,6101341,6101351,6101391,6209111',
                    '6101411'
                ),
                '6201' => array(
                    '62011',
                    '62012',
                    '62013'
                ),
                '6301' => array(
                    '63011',
                    '63012',
                    '63013',
                    '63014',
                    '6301511',
                    '6301611',
                    '6301711',
                    '6301811'
                )
            ),
            'Laba Kotor sebelum Bagian Laba (Rugi) Proyek KSO' => 'sum:labakotor_sebelum_kso',
            '5201111',
            'Laba Kotor setelah Bagian Laba (Rugi) Proyek KSO' => 'sum:labakotor_setelah_kso',
            'BEBAN USAHA' => array(
                'Beban Pemasaran' => '7101,7102',
                '7201',
                '7202',
                '7203',
                '7204',
                '7205',
                '7206'
            ),
            'LABA USAHA' => 'sum:labausaha',
            'PENDAPATAN (BEBAN) LAIN-LAIN' => array(
                '91991',
                '9104111',
                '91051',
                '91021',
                '9106111',
                '9106211',
                '9101111',
                '9199211',
            ),
            'Laba sebelum Pajak' => 'sum:laba_sebelum_pajak',
            'PENGHASILAN (BEBAN) PAJAK' => array(
                'Pajak Kini' => array(
                    '6301521',
                    'Pajak Tidak Final' => ''
                ),
                'Pajak Tangguhan' => '',
            ),
            'Laba Setelah Pajak' => 'sum:laba_setelah_pajak',
            'PENDAPATAN KOMPREHENSIF LAIN' => array(
                '3301111',
                '3501111',
                'Lindung Nilai Arus Kas' => '',
                '3601111',
                'Keuntungan (kerugian) aktuarial program pensiun manfaat pasti' => '',
                'Pajak atas Pendapatan Komprehensif' => '',
            ),
            'Total Laba Rugi Komprehensif Tahun Berjalan' => 'sum:total_laba_komprehensif',
            'Laba yang dapat diatribusikan kepada' => array(
                'Pemilik entitas induk' => 'input:1',
                'Kepentingan nonpengendali' => 'input:2'
            ),
            'Jumlah Laba Rugi Komprehensif yang dapat diatribusikan kepada' => array(
                'Pemilik entitas induk' => 'input:3',
                'Kepentingan nonpengendali' => 'input:4'
            ),
            'Laba per saham (dalam Rupiah)' => 'input:5',
            'Dasar dan dilusian' => 'input:6' 
        );
        
        return $list_lr;
        
    }/*}}}*/
    
    function sub_laba_rugi_spk($base)/*{{{*/
    {
    	error_reporting('E_ALL');

    	//$base->db->debug=true;
    	//echo "<pre>"."STOP!!";exit;
        $this->get_valid_app('SDV');
		$kddiv 		= $this->S['curr_divisi'];
		$divname 	= $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$table 		= "jurnal_".strtolower($this->S['curr_divisi']);
		$type 		= $this->get_var('tbtype');
        $kdreport 	= $this->get_var('kdreport',$kdreport);
        $grptype 	= $this->get_var('grptype');
        $ryear 		= $this->get_var('ryear',date('Y'));
        $rmonth 	= $this->get_var('rmonth', date('m'));
        $is_proses 	= $this->get_var('is_proses');
        $period 	= date("Y-m-d",mktime(0,0,0,$rmonth,1,$ryear));
        $period_begin = date("Y-m-d",mktime(0,0,0,1,1,$ryear));//date("d-m-Y",mktime(0,0,0,1,1,$ryear));
        $period_end = date("Y-m-d",mktime(0,0,0,$rmonth+1,0,$ryear));
        $dp = new dateparse();
        
		switch($grptype)
        {
            case 'perspk': 
                $grptype_text = 'PER_SPK_';
                break;
            case 'pernsb':
                $grptype_text = 'PER_NSB_';
                break;
            case 'perwil':
                $grptype_text = 'PER_WILAYAH_';
                break;
            default:
                $grptype_text = '';
                break;
        }
		
        $type = 'group';
        if($is_proses=='t')
		{
            if($type == 'rinci')
            {
                $tpl = $base->_get_tpl('report_labarugi_printable_rinci.html');
                $tpl_excel = $base->_get_tpl('report_labarugi_printable_rinci.html');
                
                $tpl1 = & $tpl->defineDynamicBlock('row');
                $tpl2 = & $tpl1->defineDynamicBlock('row1');
                $tpl3 = & $tpl1->defineDynamicBlock('row2');
            }
            else
            {
                $tpl = $base->_get_tpl('report_labarugi_printable_ikhtisar.html');
                $tpl->defineDynamicBlock('row');
                $this->_fill_static_report($base,&$tpl);
                
                $tpl_excel = $base->_get_tpl('report_labarugi_printable_ikhtisar.html');
                $tpl_excel->defineDynamicBlock('row');
                $this->_fill_static_report($base,&$tpl_excel);
            }
            
        
        
            if($type == 'rinci')
            {
                $addselect =   ", j.nobukti , j.keterangan, to_char(j.tanggal,'dd-mm-yyyy') as tanggal";
                $addwhere =  ", j.nobukti, j.keterangan, j.tanggal";
                $addorder = ", j.tanggal, j.nobukti";
            }

        if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
        else $sql_wil = '';
		if ($this->S['curr_wil'] != '')
			{
			  $sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
			}

            //$base->db->debug= true;
            $sql = "--LABA RUGI";
            $sql.= "
                    SELECT j.kdspk, ds.nmspk, ds.omzet {$addselect}, 
                    SUM(CASE WHEN j.kdperkiraan LIKE '51%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah*-1 ELSE 0 END) AS penjualan,
                    SUM(CASE WHEN j.kdperkiraan LIKE '41%' OR  j.kdperkiraan LIKE '42%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS bl,
                    SUM(CASE WHEN j.kdperkiraan LIKE '43%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS btl,
                    CASE WHEN DATE_PART('YEAR', j.tanggal) < '{$ryear}' THEN 'sd_thn_lalu'
                        WHEN DATE_TRUNC('MONTH',j.tanggal) < '{$period}' THEN 'sd_bln_lalu' 
                        WHEN DATE_TRUNC('MONTH',j.tanggal) = '{$period}' THEN 'bln_ini'
                    END AS ket_total,
                    CASE WHEN ds.tglmulai >= '$period_begin' THEN 'new' ELSE 'old' END AS status
                    FROM {$table} j, (SELECT * FROM dspk WHERE 1=1 $sql_wil) ds
                    WHERE ds.kddiv = j.kddivisi AND j.kdspk=ds.kdspk AND date_trunc('month', j.tanggal) <= '{$period}' AND j.kddivisi=ds.kddiv
                    AND j.kdperkiraan SIMILAR TO '(51|41|42|43)%' AND j.isapp='t' AND j.isdel='f' AND j.nobukti NOT LIKE '01%'
                    --AND '{$period_end}' BETWEEN date_trunc('month', ds.tglmulai) AND date_trunc('month', ds.tglselesai)
                    AND ds.tglmulai <= '{$period_end}'
                    AND ds.tglselesai >= '$period_begin'
                    GROUP BY j.kdspk, ds.nmspk, ds.omzet, ket_total,status {$addwhere}
                    ORDER BY j.kdspk {$addorder}";
            //echo $sql;die;
            $rs = $base->dbQuery($sql);
            
            $array_total = array(
                'sd_thn_lalu'   => 'S/D Tahun Lalu',
                'bln_ini'       => 'Tahun ini : Bulan ini',
                'sd_bln_ini'    => 'S/D Bulan ini',
                'sd_saat_ini'   => 'S/D saat ini'
            );
            
            while(!$rs->EOF)
            {
                $sum_omzet = ($kdspk != $rs->fields['kdspk']) ? true : false;
                
                $kdspk = $rs->fields['kdspk'];
                $data[$rs->fields['kdspk']]['kdspk'] = $rs->fields['kdspk'];
                $data[$rs->fields['kdspk']]['nmspk'] = $rs->fields['nmspk'];
                $data[$rs->fields['kdspk']]['omzet'] = $this->format_money2($base, $rs->fields['omzet']);
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['penjualan'] = $rs->fields['penjualan'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['bl'] = $rs->fields['bl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['btl'] = $rs->fields['btl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['biaya'] = $rs->fields['bl'] + $rs->fields['btl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['laba'] = $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']);
                
                //total
                $total['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                $total[$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                $total[$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                $total[$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                $total[$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                $total[$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                
                //spk baru
                $status = $rs->fields['status'];
                if($status == 'new')
                {
                    $data[$rs->fields['kdspk']]['trstyle'] = 'style="background-color: #EEEEEE;"';
                    
                    $spkbaru['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                    $spkbaru[$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                    $spkbaru[$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                    $spkbaru[$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                    $spkbaru[$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                    $spkbaru[$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                    
                    $kdgrp = substr($rs->fields['kdspk'],0,1);
                    $group[$kdgrp]['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                    $group[$kdgrp][$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                    $group[$kdgrp][$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                    $group[$kdgrp][$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                    $group[$kdgrp][$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                    $group[$kdgrp][$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                }
                else
                {
                    $data[$rs->fields['kdspk']]['trstyle'] = "";
                }
                
                $rs->moveNext();
                
                if(($kdgrp != substr($rs->fields['kdspk'],0,1) || $rs->EOF) && $status != 'old')
                {
                    $data[$kdgrp]['nmspk'] = 'JUMLAH SPK-SPK BARU';
                    $data[$kdgrp]['omzet'] = $this->format_money2($base, $group[$kdgrp]['omzet']);
                    foreach(array_merge($array_total, array('sd_bln_lalu'=>'')) as $k => $v)
                    {
                        $data[$kdgrp][$k]['penjualan'] = $group[$kdgrp][$k]['penjualan'];
                        $data[$kdgrp][$k]['bl'] = $group[$kdgrp][$k]['bl'];
                        $data[$kdgrp][$k]['btl'] = $group[$kdgrp][$k]['btl'];
                        $data[$kdgrp][$k]['biaya'] = $group[$kdgrp][$k]['biaya'];
                        $data[$kdgrp][$k]['laba'] = $group[$kdgrp][$k]['laba'];
                    }
                    
                    $data[$kdgrp]['sd_bln_ini'] = array(
                        'penjualan' => $data[$kdgrp]['sd_bln_lalu']['penjualan'] + $data[$kdgrp]['bln_ini']['penjualan'],
                        'bl' => $data[$kdgrp]['sd_bln_lalu']['bl'] + $data[$kdgrp]['bln_ini']['bl'],
                        'btl' => $data[$kdgrp]['sd_bln_lalu']['btl'] + $data[$kdgrp]['bln_ini']['btl'],
                        'biaya' => $data[$kdgrp]['sd_bln_lalu']['biaya'] + $data[$kdgrp]['bln_ini']['biaya'],
                        'laba' => $data[$kdgrp]['sd_bln_lalu']['laba'] + $data[$kdgrp]['bln_ini']['laba'],
                    );
                    
                    $data[$kdgrp]['sd_saat_ini'] = array(
                        'penjualan' => $data[$kdgrp]['sd_bln_ini']['penjualan'] + $data[$kdgrp]['sd_thn_lalu']['penjualan'],
                        'bl' => $data[$kdgrp]['sd_bln_ini']['bl'] + $data[$kdgrp]['sd_thn_lalu']['bl'],
                        'btl' => $data[$kdgrp]['sd_bln_ini']['btl'] + $data[$kdgrp]['sd_thn_lalu']['btl'],
                        'biaya' => $data[$kdgrp]['sd_bln_ini']['biaya'] + $data[$kdgrp]['sd_thn_lalu']['biaya'],
                        'laba' => $data[$kdgrp]['sd_bln_ini']['laba'] + $data[$kdgrp]['sd_thn_lalu']['laba'],
                    );
                    
                }
                
                if($rs->fields['kdspk'] != $kdspk || $rs->EOF)
                {
                    $data[$kdspk]['sd_bln_ini'] = array(
                        'penjualan' => $data[$kdspk]['sd_bln_lalu']['penjualan'] + $data[$kdspk]['bln_ini']['penjualan'],
                        'bl' => $data[$kdspk]['sd_bln_lalu']['bl'] + $data[$kdspk]['bln_ini']['bl'],
                        'btl' => $data[$kdspk]['sd_bln_lalu']['btl'] + $data[$kdspk]['bln_ini']['btl'],
                        'biaya' => $data[$kdspk]['sd_bln_lalu']['biaya'] + $data[$kdspk]['bln_ini']['biaya'],
                        'laba' => $data[$kdspk]['sd_bln_lalu']['laba'] + $data[$kdspk]['bln_ini']['laba'],
                    );
                    
                    $data[$kdspk]['sd_saat_ini'] = array(
                        'penjualan' => $data[$kdspk]['sd_bln_ini']['penjualan'] + $data[$kdspk]['sd_thn_lalu']['penjualan'],
                        'bl' => $data[$kdspk]['sd_bln_ini']['bl'] + $data[$kdspk]['sd_thn_lalu']['bl'],
                        'btl' => $data[$kdspk]['sd_bln_ini']['btl'] + $data[$kdspk]['sd_thn_lalu']['btl'],
                        'biaya' => $data[$kdspk]['sd_bln_ini']['biaya'] + $data[$kdspk]['sd_thn_lalu']['biaya'],
                        'laba' => $data[$kdspk]['sd_bln_ini']['laba'] + $data[$kdspk]['sd_thn_lalu']['laba'],
                    );
                    
                }
            }
            
            $data['spkbaru'] = array(
                'nmspk' => 'JUMLAH SPK-SPK BARU ',
                'omzet' => $this->format_money2($base, $spkbaru['omzet']),
            );
            
            $data['total'] = array(
                'nmspk' => 'JUMLAH SELURUH SPK',
                'omzet' => $this->format_money2($base, $total['omzet']),
            );
            
            foreach(array_merge($array_total, array('sd_bln_lalu'=>'')) as $k => $v)
            {
                $data['spkbaru'][$k]['penjualan'] = $spkbaru[$k]['penjualan'];
                $data['spkbaru'][$k]['bl'] = $spkbaru[$k]['bl'];
                $data['spkbaru'][$k]['btl'] = $spkbaru[$k]['btl'];
                $data['spkbaru'][$k]['biaya'] = $spkbaru[$k]['biaya'];
                $data['spkbaru'][$k]['laba'] = $spkbaru[$k]['laba'];
                
                $data['total'][$k]['penjualan'] = $total[$k]['penjualan'];
                $data['total'][$k]['bl'] = $total[$k]['bl'];
                $data['total'][$k]['btl'] = $total[$k]['btl'];
                $data['total'][$k]['biaya'] = $total[$k]['biaya'];
                $data['total'][$k]['laba'] = $total[$k]['laba'];
            }
            
            foreach(array('spkbaru','total') as $k => $v)
            {
                $data[$v]['sd_bln_ini']['penjualan'] = $data[$v]['sd_bln_lalu']['penjualan'] + $data[$v]['bln_ini']['penjualan'];
                $data[$v]['sd_bln_ini']['bl'] = $data[$v]['sd_bln_lalu']['bl'] + $data[$v]['bln_ini']['bl'];
                $data[$v]['sd_bln_ini']['btl'] = $data[$v]['sd_bln_lalu']['btl'] + $data[$v]['bln_ini']['btl'];
                $data[$v]['sd_bln_ini']['biaya'] = $data[$v]['sd_bln_lalu']['biaya'] + $data[$v]['bln_ini']['biaya'];
                $data[$v]['sd_bln_ini']['laba'] = $data[$v]['sd_bln_lalu']['laba'] + $data[$v]['bln_ini']['laba'];
                
                $data[$v]['sd_saat_ini']['penjualan'] = $data[$v]['sd_thn_lalu']['penjualan'] + $data[$v]['sd_bln_ini']['penjualan'];
                $data[$v]['sd_saat_ini']['bl'] = $data[$v]['sd_thn_lalu']['bl'] + $data[$v]['sd_bln_ini']['bl'];
                $data[$v]['sd_saat_ini']['btl'] = $data[$v]['sd_thn_lalu']['btl'] + $data[$v]['sd_bln_ini']['btl'];
                $data[$v]['sd_saat_ini']['biaya'] = $data[$v]['sd_thn_lalu']['biaya'] + $data[$v]['sd_bln_ini']['biaya'];
                $data[$v]['sd_saat_ini']['laba'] = $data[$v]['sd_thn_lalu']['laba'] + $data[$v]['sd_bln_ini']['laba'];    
            }
            
            
            
            foreach($data as $k => $v)
            {
                $parsedata = array(
                    'penjualan' => '',
                    'bl' => '',
                    'btl' => '',
                    'biaya' => '',
                    'laba' => '',
                );
                $tpl->assignDynamic('row', array_merge($v,$parsedata));
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', array_merge($v,$parsedata));
                $tpl_excel->parseConcatDynamic('row');
                
                foreach($array_total as $kk => $vv)
                {
                    $dynamic_record = array(
                        'kdspk' => '',
                        'nmspk' => $vv,
                        'omzet' => '',
                        'trstyle' => $v['trstyle'],
                        'penjualan' => $this->format_money2($base, $v[$kk]['penjualan']),
                        'bl' => $this->format_money2($base, $v[$kk]['bl']),
                        'btl' => $this->format_money2($base, $v[$kk]['btl']),
                        'biaya' => $this->format_money2($base, $v[$kk]['biaya']),
                        'laba' => $this->format_money2($base, $v[$kk]['laba']),
                    );
                    
                    $tpl->assignDynamic('row', $dynamic_record);
                    $tpl->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', $dynamic_record);
                    $tpl_excel->parseConcatDynamic('row');
                }
                
                $dynamic_record = array(
                    'kdspk' => '&nbsp',
                    'nmspk' => '&nbsp',
                    'omzet' => '&nbsp',
                    'penjualan' => '&nbsp',
                    'bl' => '&nbsp',
                    'btl' => '&nbsp',
                    'biaya' => '&nbsp',
                    'laba' => '&nbsp',
                    'trstle' => '',
                );
                $tpl->assignDynamic('row', $dynamic_record);
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', $dynamic_record);
                $tpl_excel->parseConcatDynamic('row');
            }
            
            $static_record = array(
                'TITLE' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid=".$this->get_var('asmid').""),
                'VBLN'  => $dp->monthnamelong[$rmonth],
                'VTHN'  => $ryear,
                'DIVNAME'   => $divname,
            );
            $tpl->assign($static_record);
            $tpl_excel->assign($static_record);
            
            $kdreport = "laba_rugi_spk";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".($grptype_text).$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".($grptype_text).$kddiv."_".$ryear."_".$rmonth."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
            
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "laba_rugi_spk";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".($grptype_text).$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
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
            $kdreport = "laba_rugi_spk";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".($grptype_text).$kddiv."_".$ryear."_".$rmonth."_rinci.html";
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
    function sub_laba_rugi_spk_visible($base)/*{{{*/
    {
        $this->get_valid_app('SDV');
		$kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
		$table = "jurnal_".strtolower($this->S['curr_divisi']);
		$type = $this->get_var('tbtype');
        $kdreport = $this->get_var('kdreport',$kdreport);
        $grptype = $this->get_var('grptype');
        $ryear = $this->get_var('ryear',date('Y'));
        $rmonth = $this->get_var('rmonth', date('m'));
        $is_proses = $this->get_var('is_proses');
        
        $period = date("d-m-Y",mktime(0,0,0,$rmonth,1,$ryear));
        $period_begin = date("d-m-Y",mktime(0,0,0,1,1,$ryear));
        $period_end = date("d-m-Y",mktime(0,0,0,$rmonth+1,0,$ryear));
        $dp = new dateparse();
        
        $type = 'group';
        if($is_proses=='t')
		{
            if($type == 'rinci')
            {
                $tpl = $base->_get_tpl('report_labarugi_printable_rinci.html');
                $tpl_excel = $base->_get_tpl('report_labarugi_printable_rinci.html');
                
                $tpl1 = & $tpl->defineDynamicBlock('row');
                $tpl2 = & $tpl1->defineDynamicBlock('row1');
                $tpl3 = & $tpl1->defineDynamicBlock('row2');
            }
            else
            {
                $tpl = $base->_get_tpl('report_labarugi_printable_ikhtisar.html');
                $tpl->defineDynamicBlock('row');
                $this->_fill_static_report($base,&$tpl);
                
                $tpl_excel = $base->_get_tpl('report_labarugi_printable_ikhtisar.html');
                $tpl_excel->defineDynamicBlock('row');
                $this->_fill_static_report($base,&$tpl_excel);
            }
            
        
        
            if($type == 'rinci')
            {
                $addselect =   ", j.nobukti , j.keterangan, to_char(j.tanggal,'dd-mm-yyyy') as tanggal";
                $addwhere =  ", j.nobukti, j.keterangan, j.tanggal";
                $addorder = ", j.tanggal, j.nobukti";
            }
            //$base->db= true;
            $sql = "--LABA RUGI ";
            $sql.= " 
                    SELECT j.kdspk, ds.nmspk, ds.omzet {$addselect}, 
                    SUM(CASE WHEN j.kdperkiraan LIKE '51%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah*-1 ELSE 0 END) AS penjualan,
                    SUM(CASE WHEN j.kdperkiraan LIKE '41%' OR  j.kdperkiraan LIKE '42%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS bl,
                    SUM(CASE WHEN j.kdperkiraan LIKE '43%' THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS btl,
                    CASE WHEN DATE_PART('YEAR', j.tanggal) < '{$ryear}' THEN 'sd_thn_lalu'
                        WHEN DATE_TRUNC('MONTH',j.tanggal) < '{$period}' THEN 'sd_bln_lalu' 
                        WHEN DATE_TRUNC('MONTH',j.tanggal) = '{$period}' THEN 'bln_ini'
                    END AS ket_total,
                    CASE WHEN ds.tglmulai >= '$period_begin' THEN 'new' ELSE 'old' END AS status
                    FROM {$table} j, dspk ds
                    WHERE j.kdspk=ds.kdspk AND date_trunc('month', j.tanggal) <= '{$period}' AND j.kddivisi=ds.kddiv
                    AND j.kdperkiraan SIMILAR TO '(51|41|42|43)%' AND j.isapp='t' AND j.isdel='f' AND j.nobukti NOT LIKE '01%'
                    --AND '{$period_end}' BETWEEN date_trunc('month', ds.tglmulai) AND date_trunc('month', ds.tglselesai)
                    AND ds.visible='t'
                    GROUP BY j.kdspk, ds.nmspk, ds.omzet, ket_total,status {$addwhere}
                    ORDER BY j.kdspk {$addorder}";
            //echo $sql;
            $rs = $base->dbQuery($sql);
            
            $array_total = array(
                'sd_thn_lalu'   => 'S/D Tahun Lalu',
                'bln_ini'       => 'Tahun ini : Bulan ini',
                'sd_bln_ini'    => 'S/D Bulan ini',
                'sd_saat_ini'   => 'S/D saat ini'
            );
            
            while(!$rs->EOF)
            {
                $sum_omzet = ($kdspk != $rs->fields['kdspk']) ? true : false;
                
                $kdspk = $rs->fields['kdspk'];
                $data[$rs->fields['kdspk']]['kdspk'] = $rs->fields['kdspk'];
                $data[$rs->fields['kdspk']]['nmspk'] = $rs->fields['nmspk'];
                $data[$rs->fields['kdspk']]['omzet'] = $this->format_money2($base, $rs->fields['omzet']);
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['penjualan'] = $rs->fields['penjualan'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['bl'] = $rs->fields['bl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['btl'] = $rs->fields['btl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['biaya'] = $rs->fields['bl'] + $rs->fields['btl'];
                $data[$rs->fields['kdspk']][$rs->fields['ket_total']]['laba'] = $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']);
                
                //total
                $total['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                $total[$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                $total[$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                $total[$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                $total[$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                $total[$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                
                //spk baru
                $status = $rs->fields['status'];
                if($status == 'new')
                {
                    $data[$rs->fields['kdspk']]['trstyle'] = 'style="background-color: #EEEEEE;"';
                    
                    $spkbaru['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                    $spkbaru[$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                    $spkbaru[$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                    $spkbaru[$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                    $spkbaru[$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                    $spkbaru[$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                    
                    $kdgrp = substr($rs->fields['kdspk'],0,1);
                    $group[$kdgrp]['omzet'] += ($sum_omzet) ? $rs->fields['omzet'] : 0;
                    $group[$kdgrp][$rs->fields['ket_total']]['penjualan'] += $rs->fields['penjualan'];
                    $group[$kdgrp][$rs->fields['ket_total']]['bl'] += $rs->fields['bl'];
                    $group[$kdgrp][$rs->fields['ket_total']]['btl'] += $rs->fields['btl'];
                    $group[$kdgrp][$rs->fields['ket_total']]['biaya'] += ($rs->fields['bl'] + $rs->fields['btl']);
                    $group[$kdgrp][$rs->fields['ket_total']]['laba'] += ( $rs->fields['penjualan'] - ($rs->fields['bl'] + $rs->fields['btl']) );
                }
                else
                {
                    $data[$rs->fields['kdspk']]['trstyle'] = "";
                }
                
                $rs->moveNext();
                
                if(($kdgrp != substr($rs->fields['kdspk'],0,1) || $rs->EOF) && $status != 'old')
                {
                    $data[$kdgrp]['nmspk'] = 'JUMLAH SPK-SPK BARU';
                    $data[$kdgrp]['omzet'] = $this->format_money2($base, $group[$kdgrp]['omzet']);
                    foreach(array_merge($array_total, array('sd_bln_lalu'=>'')) as $k => $v)
                    {
                        $data[$kdgrp][$k]['penjualan'] = $group[$kdgrp][$k]['penjualan'];
                        $data[$kdgrp][$k]['bl'] = $group[$kdgrp][$k]['bl'];
                        $data[$kdgrp][$k]['btl'] = $group[$kdgrp][$k]['btl'];
                        $data[$kdgrp][$k]['biaya'] = $group[$kdgrp][$k]['biaya'];
                        $data[$kdgrp][$k]['laba'] = $group[$kdgrp][$k]['laba'];
                    }
                    
                    $data[$kdgrp]['sd_bln_ini'] = array(
                        'penjualan' => $data[$kdgrp]['sd_bln_lalu']['penjualan'] + $data[$kdgrp]['bln_ini']['penjualan'],
                        'bl' => $data[$kdgrp]['sd_bln_lalu']['bl'] + $data[$kdgrp]['bln_ini']['bl'],
                        'btl' => $data[$kdgrp]['sd_bln_lalu']['btl'] + $data[$kdgrp]['bln_ini']['btl'],
                        'biaya' => $data[$kdgrp]['sd_bln_lalu']['biaya'] + $data[$kdgrp]['bln_ini']['biaya'],
                        'laba' => $data[$kdgrp]['sd_bln_lalu']['laba'] + $data[$kdgrp]['bln_ini']['laba'],
                    );
                    
                    $data[$kdgrp]['sd_saat_ini'] = array(
                        'penjualan' => $data[$kdgrp]['sd_bln_ini']['penjualan'] + $data[$kdgrp]['sd_thn_lalu']['penjualan'],
                        'bl' => $data[$kdgrp]['sd_bln_ini']['bl'] + $data[$kdgrp]['sd_thn_lalu']['bl'],
                        'btl' => $data[$kdgrp]['sd_bln_ini']['btl'] + $data[$kdgrp]['sd_thn_lalu']['btl'],
                        'biaya' => $data[$kdgrp]['sd_bln_ini']['biaya'] + $data[$kdgrp]['sd_thn_lalu']['biaya'],
                        'laba' => $data[$kdgrp]['sd_bln_ini']['laba'] + $data[$kdgrp]['sd_thn_lalu']['laba'],
                    );
                    
                }
                
                if($rs->fields['kdspk'] != $kdspk || $rs->EOF)
                {
                    $data[$kdspk]['sd_bln_ini'] = array(
                        'penjualan' => $data[$kdspk]['sd_bln_lalu']['penjualan'] + $data[$kdspk]['bln_ini']['penjualan'],
                        'bl' => $data[$kdspk]['sd_bln_lalu']['bl'] + $data[$kdspk]['bln_ini']['bl'],
                        'btl' => $data[$kdspk]['sd_bln_lalu']['btl'] + $data[$kdspk]['bln_ini']['btl'],
                        'biaya' => $data[$kdspk]['sd_bln_lalu']['biaya'] + $data[$kdspk]['bln_ini']['biaya'],
                        'laba' => $data[$kdspk]['sd_bln_lalu']['laba'] + $data[$kdspk]['bln_ini']['laba'],
                    );
                    
                    $data[$kdspk]['sd_saat_ini'] = array(
                        'penjualan' => $data[$kdspk]['sd_bln_ini']['penjualan'] + $data[$kdspk]['sd_thn_lalu']['penjualan'],
                        'bl' => $data[$kdspk]['sd_bln_ini']['bl'] + $data[$kdspk]['sd_thn_lalu']['bl'],
                        'btl' => $data[$kdspk]['sd_bln_ini']['btl'] + $data[$kdspk]['sd_thn_lalu']['btl'],
                        'biaya' => $data[$kdspk]['sd_bln_ini']['biaya'] + $data[$kdspk]['sd_thn_lalu']['biaya'],
                        'laba' => $data[$kdspk]['sd_bln_ini']['laba'] + $data[$kdspk]['sd_thn_lalu']['laba'],
                    );
                    
                }
            }
            
            $data['spkbaru'] = array(
                'nmspk' => 'JUMLAH SPK-SPK BARU ',
                'omzet' => $this->format_money2($base, $spkbaru['omzet']),
            );
            
            $data['total'] = array(
                'nmspk' => 'JUMLAH SELURUH SPK',
                'omzet' => $this->format_money2($base, $total['omzet']),
            );
            
            foreach(array_merge($array_total, array('sd_bln_lalu'=>'')) as $k => $v)
            {
                $data['spkbaru'][$k]['penjualan'] = $spkbaru[$k]['penjualan'];
                $data['spkbaru'][$k]['bl'] = $spkbaru[$k]['bl'];
                $data['spkbaru'][$k]['btl'] = $spkbaru[$k]['btl'];
                $data['spkbaru'][$k]['biaya'] = $spkbaru[$k]['biaya'];
                $data['spkbaru'][$k]['laba'] = $spkbaru[$k]['laba'];
                
                $data['total'][$k]['penjualan'] = $total[$k]['penjualan'];
                $data['total'][$k]['bl'] = $total[$k]['bl'];
                $data['total'][$k]['btl'] = $total[$k]['btl'];
                $data['total'][$k]['biaya'] = $total[$k]['biaya'];
                $data['total'][$k]['laba'] = $total[$k]['laba'];
            }
            
            foreach(array('spkbaru','total') as $k => $v)
            {
                $data[$v]['sd_bln_ini']['penjualan'] = $data[$v]['sd_bln_lalu']['penjualan'] + $data[$v]['bln_ini']['penjualan'];
                $data[$v]['sd_bln_ini']['bl'] = $data[$v]['sd_bln_lalu']['bl'] + $data[$v]['bln_ini']['bl'];
                $data[$v]['sd_bln_ini']['btl'] = $data[$v]['sd_bln_lalu']['btl'] + $data[$v]['bln_ini']['btl'];
                $data[$v]['sd_bln_ini']['biaya'] = $data[$v]['sd_bln_lalu']['biaya'] + $data[$v]['bln_ini']['biaya'];
                $data[$v]['sd_bln_ini']['laba'] = $data[$v]['sd_bln_lalu']['laba'] + $data[$v]['bln_ini']['laba'];
                
                $data[$v]['sd_saat_ini']['penjualan'] = $data[$v]['sd_thn_lalu']['penjualan'] + $data[$v]['sd_bln_ini']['penjualan'];
                $data[$v]['sd_saat_ini']['bl'] = $data[$v]['sd_thn_lalu']['bl'] + $data[$v]['sd_bln_ini']['bl'];
                $data[$v]['sd_saat_ini']['btl'] = $data[$v]['sd_thn_lalu']['btl'] + $data[$v]['sd_bln_ini']['btl'];
                $data[$v]['sd_saat_ini']['biaya'] = $data[$v]['sd_thn_lalu']['biaya'] + $data[$v]['sd_bln_ini']['biaya'];
                $data[$v]['sd_saat_ini']['laba'] = $data[$v]['sd_thn_lalu']['laba'] + $data[$v]['sd_bln_ini']['laba'];    
            }
            
            
            
            foreach($data as $k => $v)
            {
                $parsedata = array(
                    'penjualan' => '',
                    'bl' => '',
                    'btl' => '',
                    'biaya' => '',
                    'laba' => '',
                );
                $tpl->assignDynamic('row', array_merge($v,$parsedata));
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', array_merge($v,$parsedata));
                $tpl_excel->parseConcatDynamic('row');
                
                foreach($array_total as $kk => $vv)
                {
                    $dynamic_record = array(
                        'kdspk' => '',
                        'nmspk' => $vv,
                        'omzet' => '',
                        'trstyle' => $v['trstyle'],
                        'penjualan' => $this->format_money2($base, $v[$kk]['penjualan']),
                        'bl' => $this->format_money2($base, $v[$kk]['bl']),
                        'btl' => $this->format_money2($base, $v[$kk]['btl']),
                        'biaya' => $this->format_money2($base, $v[$kk]['biaya']),
                        'laba' => $this->format_money2($base, $v[$kk]['laba']),
                    );
                    
                    $tpl->assignDynamic('row', $dynamic_record);
                    $tpl->parseConcatDynamic('row');
                    
                    $tpl_excel->assignDynamic('row', $dynamic_record);
                    $tpl_excel->parseConcatDynamic('row');
                }
                
                $dynamic_record = array(
                    'kdspk' => '&nbsp',
                    'nmspk' => '&nbsp',
                    'omzet' => '&nbsp',
                    'penjualan' => '&nbsp',
                    'bl' => '&nbsp',
                    'btl' => '&nbsp',
                    'biaya' => '&nbsp',
                    'laba' => '&nbsp',
                    'trstle' => '',
                );
                $tpl->assignDynamic('row', $dynamic_record);
                $tpl->parseConcatDynamic('row');
                
                $tpl_excel->assignDynamic('row', $dynamic_record);
                $tpl_excel->parseConcatDynamic('row');
            }
            
            $static_record = array(
                'TITLE' => ($type=='rinci'?'RINCIAN ':'IKHTISAR ') . $base->dbGetOne("SELECT description FROM appsubmodule WHERE asmid=".$this->get_var('asmid').""),
                'VBLN'  => $dp->monthnamelong[$rmonth],
                'VTHN'  => $ryear,
                'DIVNAME'   => $divname,
            );
            $tpl->assign($static_record);
            $tpl_excel->assign($static_record);
            
            $kdreport = "laba_rugi_spk_visible";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
            $isi_excel = & $tpl_excel->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
            
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "laba_rugi_spk_visible";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
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
            $kdreport = "laba_rugi_spk_visible";
            $vkdspk = ($kdspk == '') ? "" : $kdspk."_";
            $vuker = ($uker == '') ? "" : $uker."_";
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci.html";
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
    
    function sub_rk_proyek($base)/*{{{*/
    {
        $kddivisi = $this->S['curr_divisi'];
        $jurnal = "jurnal_".strtolower($kddivisi);
        $month = $this->get_var('rmonth');
        $year = $this->get_var('ryear');
        
        $tpl = $base->_get_tpl('laporan_rk.html');
        $tpl1 =& $tpl->defineDynamicBlock('page');
        $tpl2 =& $tpl1->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        
        if($this->get_var('is_proses') == 't')
        {
        
            $kdperkiraan = $base->dbGetOne("SELECT kdperkiraan FROM config_rk WHERE kddivisi_1='' AND kddivisi_2=''");
            $periode_end = date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode = date('Y-m-d', mktime(0,0,0,$month,1,$year));
            
            $dp = new dateparse();
            
            $record_static = array(
                'TITLE' => 'Rekon Hub R/K Div-Spl',
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => 'Kantor Divisi',
                'DIV2' => 'SPL'
            );
            $tpl->assign($record_static);
            
            $sqlg = "SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, j.kdspk, ds.nmspk
                    	,CASE WHEN substr(j.nobukti,1,2) = '01' THEN 'div' ELSE 'spl' END AS grp
                    FROM {$jurnal} j
                    LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
                    WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                    AND date_trunc('month', j.tanggal) <= '{$periode}'
                    GROUP BY j.kdspk,nmspk,grp
                    ORDER BY j.kdspk, grp";

            $rsg = $base->dbQuery($sqlg);
            
            $data = array();
            while(!$rsg->EOF)
            {
                $data[$rsg->fields['kdspk']]['nmspk'] = $rsg->fields['nmspk'];
                $data[$rsg->fields['kdspk']][$rsg->fields['grp']] = $rsg->fields['rupiah'];
                $data[$rsg->fields['kdspk']]['sum'] += $rsg->fields['rupiah'];
                
                $rsg->moveNext();
            }
            
            $sqld = "SELECT * FROM
                    (
                    SELECT j.nobukti, j.kdspk, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, 'SPL' as src, j.tanggal
                    FROM {$jurnal} j
                    LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND j2.nobukti NOT LIKE '01%' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j.kdspk=j2.kdspk AND j2.isdel='f' AND j2.isapp='t' AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j.nobukti LIKE '01%' AND j2.nobukti IS NULL AND date_trunc('month', j.tanggal) <= '{$periode}' 
                    
                    UNION ALL
                    
                    SELECT j.nobukti, j.kdspk, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, 'Kantor Divisi' as src, j.tanggal
                    FROM {$jurnal} j
                    LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND j2.nobukti LIKE '01%' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j.kdspk=j2.kdspk AND j2.isdel='f' AND j2.isapp='t'AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j.nobukti NOT LIKE '01%' AND j2.nobukti IS NULL  AND date_trunc('month', j.tanggal) <= '{$periode}'
                    ) ax
                    ORDER BY kdspk, src DESC, tanggal";
            $rsd = $base->dbQuery($sqld);
            while(!$rsd->EOF)
            {
                $detail[$rsd->fields['kdspk']][$rsd->fields['src']][] = $rsd->fields;
                $rsd->moveNext();
            }
            
            foreach($detail as $kdspk => $v)
            {
                foreach($v as $kk => $vv)
                {
                    $subtotal = 0;
                    //subgroup
                    $dynamic_record = array(
                        'tanggal'   => '<b>Belum dibuku di ' . $kk . '</b>',
                        'no_bukti'  => '',
                        'keterangan' => '',
                        'dk' => '',
                        'rupiah' => ''
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                    foreach($vv as $kkk => $vvv)
                    {
                        $dynamic_record = array(
                            'tanggal'   => $vvv['tgl'],
                            'no_bukti'  => $vvv['nobukti'],
                            'keterangan' => $vvv['keterangan'],
                            'dk' => $vvv['dk'],
                            'rupiah' => $this->format_money2($base, $vvv['rupiah'])
                        );
                        $tpl1->assignDynamic('row', $dynamic_record);
                        $tpl1->parseConcatDynamic('row');
                        
                        $subtotal += $vvv['rupiah'];
                    }
                    
                    //subtotal
                    $dynamic_record = array(
                        'tanggal'   => '',
                        'no_bukti'  => '',
                        'keterangan' => '',
                        'dk' => '',
                        'rupiah' => '<b>' . $this->format_money2($base, $subtotal) . '</b>'
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                }
                
                //group
                $dynamic_record = array(
                    'kdspk' => $kdspk,
                    'nmspk' => $data[$kdspk]['nmspk'],
                    'title_kdspk' => 'Kode SPL :',
                    'title_nmspk' => 'Nama SPL :',
                    'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
                    'saldo_div2' => $this->format_money2($base, $data[$kdspk]['spl']),
                    'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
                );
                $tpl->assignDynamic('page', $dynamic_record);
                $tpl->parseConcatDynamic('page');
                $tpl2->emptyParsedPage();
                
            }
            
            $kdreport = "rk_proyek";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "rk_proyek";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
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
            $kdreport = "rk_proyek";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci.html";
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
    
    function sub_rk_jo($base)/*{{{*/
    {
        $kddivisi = $this->S['curr_divisi'];
        $jurnal = "jurnal_".strtolower($kddivisi);
        $month = $this->get_var('rmonth');
        $year = $this->get_var('ryear');
        //$base->db= 1; 
        $tpl = $base->_get_tpl('laporan_rk.html');
        $tpl1 =& $tpl->defineDynamicBlock('page');
        $tpl2 =& $tpl1->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        //echo 's_proses : '. $this->get_var('is_proses') . '<hr>';
        if($this->get_var('is_proses') == 't')
        {
            //echo "SATU";
            $kdperkiraan = "1208111";
            $periode_end = date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode = date('Y-m-d', mktime(0,0,0,$month,1,$year));
            
            $dp = new dateparse();
            
            $record_static = array(
                'TITLE' => 'Rekon Hub R/K JO',
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => 'Kantor Divisi',
                'DIV2' => 'Proyek KSO'
            );
            $tpl->assign($record_static);
            
            $sqlg = "SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, j.kdspk, ds.nmspk
                    	,CASE WHEN substr(j.nobukti,1,2) = '01' THEN 'div' ELSE 'kso' END AS grp
                    FROM {$jurnal} j
                    LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
                    WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                    AND date_trunc('month', j.tanggal) <= '{$periode}'
                    GROUP BY j.kdspk,nmspk,grp
                    ORDER BY j.kdspk, grp";
            $rsg = $base->dbQuery($sqlg);
            
            $data = array();
            while(!$rsg->EOF)
            {
                $data[$rsg->fields['kdspk']]['nmspk'] = $rsg->fields['nmspk'];
                $data[$rsg->fields['kdspk']][$rsg->fields['grp']] = $rsg->fields['rupiah'];
                $data[$rsg->fields['kdspk']]['sum'] += $rsg->fields['rupiah'];
                
                $rsg->moveNext();
            }
            
            $sqld = "SELECT * FROM
                    (
                    SELECT j.nobukti, j.kdspk, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, 'Proyek KSO' as src, j.tanggal
                    FROM {$jurnal} j
                    LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND j2.nobukti NOT LIKE '01%' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j.kdspk=j2.kdspk AND j2.isdel='f' AND j2.isapp='t' AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j.nobukti LIKE '01%' AND j2.nobukti IS NULL AND date_trunc('month', j.tanggal) <= '{$periode}' 
                    
                    UNION ALL
                    
                    SELECT j.nobukti, j.kdspk, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, 'Kantor Divisi' as src, j.tanggal
                    FROM {$jurnal} j
                    LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND j2.nobukti LIKE '01%' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j.kdspk=j2.kdspk AND j2.isdel='f' AND j2.isapp='t'AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j.nobukti NOT LIKE '01%' AND j2.nobukti IS NULL  AND date_trunc('month', j.tanggal) <= '{$periode}'
                    ) ax
                    ORDER BY kdspk, src DESC, tanggal";
            //echo "<pre>$sqld</pre>";
            $rsd = $base->dbQuery($sqld);
            while(!$rsd->EOF)
            {
                $detail[$rsd->fields['kdspk']][$rsd->fields['src']][] = $rsd->fields;
                $rsd->moveNext();
            }
            
            foreach($detail as $kdspk => $v)
            {
                foreach($v as $kk => $vv)
                {
                    $subtotal = 0;
                    //subgroup
                    $dynamic_record = array(
                        'tanggal'   => '<b>Belum dibuku di ' . $kk . '</b>',
                        'no_bukti'  => '',
                        'keterangan' => '',
                        'dk' => '',
                        'rupiah' => ''
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                    foreach($vv as $kkk => $vvv)
                    {
                        $dynamic_record = array(
                            'tanggal'   => $vvv['tgl'],
                            'no_bukti'  => $vvv['nobukti'],
                            'keterangan' => $vvv['keterangan'],
                            'dk' => $vvv['dk'],
                            'rupiah' => $this->format_money2($base, $vvv['rupiah'])
                        );
                        $tpl1->assignDynamic('row', $dynamic_record);
                        $tpl1->parseConcatDynamic('row');
                        
                        $subtotal += $vvv['rupiah'];
                    }
                    
                    //subtotal
                    $dynamic_record = array(
                        'tanggal'   => '',
                        'no_bukti'  => '',
                        'keterangan' => '',
                        'dk' => '',
                        'rupiah' => '<b>' . $this->format_money2($base, $subtotal) . '</b>'
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                }
                
                //group
                $dynamic_record = array(
                    'kdspk' => $kdspk,
                    'nmspk' => $data[$kdspk]['nmspk'],
                    'title_kdspk' => 'Kode Proyek :',
                    'title_nmspk' => 'Nama Proyek :',
                    'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
                    'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
                    'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
                );
                $tpl->assignDynamic('page', $dynamic_record);
                $tpl->parseConcatDynamic('page');
                $tpl2->emptyParsedPage();
                
            }
            
            $kdreport = "rk_jo";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        else if($this->get_var('is_excel')=='t')
        {
            //echo "DUA";
            $kdreport = "rk_jo";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
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
            //echo "TIGA";
            $kdreport = "rk_jo";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci.html";
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
    function sub_rk_jo_16($base)/*{{{*/
    {
		// $base->db= true;
        $kddivisi = $this->S['curr_divisi'];
        $jurnal = "jurnal_".strtolower($kddivisi);
        $month = $this->get_var('rmonth');
        $year = $this->get_var('ryear');
		$per	= $this->get_var('per');
		$nospk	= $this->get_var('kdspk');
		//rincian
        if ($this->get_var('tbtype') == 'rinci'){
			$tpl = $base->_get_tpl('laporan_rk_16.html');
			$tpl0 =& $tpl->defineDynamicBlock('spk');
			$tpl1 =& $tpl0->defineDynamicBlock('page');
			$tpl2 =& $tpl1->defineDynamicBlock('row');
			$this->_fill_static_report($base,&$tpl);
			// print_r('oke '.$this->get_var('is_proses'));exit;
			// if($this->get_var('is_proses') == 't')
			// {
        
            $kdperkiraan 	= "21911";//"1208111";
            $periode_end	= date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode 		= date('Y-m-d', mktime(0,0,0,$month,1,$year));
            $periode_akhir 	= date('Y-m-d', mktime(0,0,0,$month+1,0,$year));
            // print_r($periode_akhir."".$periode);exit;
            $dp = new dateparse();
            
            $record_static = array(
                'TITLE' => 'Rincian RK KSO',
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => 'Kantor Divisi',
                'DIV2' => 'Proyek KSO'
            );
            $tpl->assign($record_static);
            
            $sqlg = "SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, j.kdspk, ds.nmspk
                    	,CASE WHEN substr(j.nobukti,1,2) = '01' THEN 'div' ELSE 'kso' END AS grp
                    FROM {$jurnal} j
                    LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
                    WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                    AND date_trunc('month', j.tanggal) <= '{$periode}'
                    GROUP BY j.kdspk,nmspk,grp
                    ORDER BY j.kdspk, grp";
            $rsg = $base->dbQuery($sqlg);
            
            $data = array();
            while(!$rsg->EOF)
            {
                $data[$rsg->fields['kdspk']]['nmspk'] = $rsg->fields['nmspk'];
                $data[$rsg->fields['kdspk']][$rsg->fields['grp']] = $rsg->fields['rupiah'];
                $data[$rsg->fields['kdspk']]['sum'] += $rsg->fields['rupiah'];
                
                $rsg->moveNext();
            }
			           
			
			$sqld	= "
					-- SELECT
					--    *
					-- FROM
					--    (". 
						  "SELECT
							 j.nobukti,
							 j.kdspk,
							 to_char(j.tanggal, 'DD-Mon-YYYY') AS tgl,
							 j.keterangan,
							 j.dk,
							 j.kdalat,
							 CASE WHEN upper(j.dk)='D' THEN COALESCE(j.rupiah,0) ELSE 0 END as debet,
							 CASE WHEN upper(j.dk)='K' THEN COALESCE(j.rupiah,0) ELSE 0 END as kredit,
							 'Proyek KSO' AS src,
							 j.tanggal
						  FROM
							{$jurnal} j
							LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan = '{$kdperkiraan}' 
							-- AND j2.nobukti NOT LIKE '01%' 
							AND (j.nobukti = j2.buktipelunasan OR j.buktipelunasan = j2.nobukti)
							AND j.rupiah = j2.rupiah
							AND j.kdspk = j2.kdspk
							AND j2.isdel = 'f'
							AND j2.isapp = 't'
							AND date_trunc('month', j2.tanggal) <= '{$periode_akhir}' 
						  )
						  WHERE
							 j.isdel = 'f' AND j.isapp = 't' AND j.kdperkiraan = '{$kdperkiraan}' 
							-- AND j.nobukti LIKE '01%'
							-- AND j2.nobukti IS NULL
						  AND  j.tanggal  BETWEEN '{$periode}' AND '{$periode_akhir}'
				--	  UNION ALL
				--		 SELECT
				--			j.nobukti,
				--			j.kdspk,
				--			to_char(j.tanggal, 'DD-Mon-YYYY') AS tgl,
				--			j.keterangan,
				--			j.dk,
				--			j.kdalat,
				--			CASE WHEN upper(j.dk)='D' THEN COALESCE(j.rupiah,0) ELSE 0 END as debet,
				--			CASE WHEN upper(j.dk)='K' THEN COALESCE(j.rupiah,0) ELSE 0 END as kredit,
				--			'Kantor Divisi' AS src,
				--			j.tanggal
				--		 FROM
				--			{$jurnal} j
				--		 LEFT JOIN {$jurnal} j2 ON (
				--			j2.kdperkiraan = '{$kdperkiraan}'
				--			-- AND j2.nobukti LIKE '01%'
				--			AND (j.nobukti = j2.buktipelunasan OR j.buktipelunasan = j2.nobukti)
				--			AND j.rupiah = j2.rupiah
				--			AND j.kdspk = j2.kdspk
				--			AND j2.isdel = 'f'
				--			AND j2.isapp = 't'
				--			AND date_trunc('month', j2.tanggal) <= '{$periode_akhir}'
				--		 )
				--		 WHERE
				--			j.isdel = 'f'
				--		 AND j.isapp = 't'
				--		 AND j.kdperkiraan = '{$kdperkiraan}'
				--		 -- AND j.nobukti NOT LIKE '01%'
				--		 -- AND j2.nobukti IS NULL
				--		 AND j.kdalat LIKE 'DJO%'
				--		 AND j.tanggal  BETWEEN '{$periode}' AND '{$periode_akhir}'
				--   ) ax
					ORDER BY
					   kdspk,
					   kdalat,
					   src DESC,
					   tanggal
			";
            $rsd = $base->dbQuery($sqld);
            while(!$rsd->EOF)
            {
                $detail[$rsd->fields['kdspk']][$rsd->fields['src']][] = $rsd->fields;
                $rsd->moveNext();
            }
			
			$sqljo 	= 	"SELECT DISTINCT(kdalat) as kdalat FROM {$jurnal} j LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
							WHERE j.kdperkiraan ='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
							AND date_trunc('month',j.tanggal) <= '{$periode}' ORDER BY kdalat
						";
			$rsjo	= $base->dbQuery($sqljo);
			while(!$rsjo->EOF)
            {
                $jo[] = $rsjo->fields['kdalat'];
                $rsjo->moveNext();
            }
			
			$sqlsa = "
				SELECT
					ax.kdalat, ax.kdspk, sum(ax.rupiah) as saldoawal, 'Proyek KSO' AS src
				FROM
				(
					SELECT
					j.kdspk,
					j.kdalat,
					(CASE WHEN upper(j.dk)='D' THEN COALESCE(j.rupiah,0) ELSE COALESCE(j.rupiah,0)*-1 END) as rupiah
					FROM
					{$jurnal} j
					LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan = '{$kdperkiraan}' 
					-- AND j2.nobukti NOT LIKE '01%' 
					AND (j.nobukti = j2.buktipelunasan OR j.buktipelunasan = j2.nobukti)
					AND j.rupiah = j2.rupiah
					AND j.kdspk = j2.kdspk
					AND j2.isdel = 'f'
					AND j2.isapp = 't'
					AND date_trunc('month', j2.tanggal) < '{$periode}'
					)
					WHERE
					j.isdel = 'f' AND j.isapp = 't' AND j.kdperkiraan = '{$kdperkiraan}' 
					-- AND j.nobukti LIKE '01%'
					-- AND j2.nobukti IS NULL
					AND date_trunc('month', j.tanggal) < '{$periode}'
				) ax
				GROUP BY
				kdalat, kdspk
				ORDER BY kdspk, kdalat
			";
			$rssa = $base->dbQuery($sqlsa);
            while(!$rssa->EOF)
            {
                $data1[] = $rssa->fields;
                $rssa->moveNext();
            }
			
           	// parsing per jenis
			if ($this->get_var('per') == 'spk'){
				if ($nospk!=''){
					foreach($detail as $kdspk => $v){
						if ($kdspk == $nospk){
							$no[$kdspk] =0;
							foreach ($jo as $key=>$val){
								foreach($v as $kk => $vv){
									$subtotal[$kdspk] = 0;
									$saldo[$kdspk]	= 0;
									//subgroup
									foreach($data1 as $k => $val2 ){
										if ($val2['kdalat'] == $val and $kdspk == $val2['kdspk']){
											$dynamic_record = array(
												'tanggal'   => '<b>Saldo Awal ' . $val . '</b>',
												'no_bukti'  => '',
												'keterangan' => '',
												'debet' => '',
												'kredit' => '',
												'nasabah' => '',
												'saldo' => $this->format_money2($base,$val2['saldoawal'])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											$subtotal[$kdspk] = $val2['saldoawal'];
										}
									}
									foreach($vv as $kkk => $vvv)
									{
										if ($val == $vvv['kdalat'] ){
											$saldo[$kdspk] = $saldo[$kdspk] + $vvv['debet']-$vvv['kredit'];
											$dynamic_record = array(
												'tanggal'   => $vvv['tgl'],
												'no_bukti'  => $vvv['nobukti'],
												'keterangan' => $vvv['keterangan'],
												'nasabah' => $vvv['kdalat'],
												'kredit' => $this->format_money2($base, $vvv['kredit']),
												'debet' => $this->format_money2($base, $vvv['debet']),
												'saldo' => $this->format_money2($base, $saldo[$kdspk]+$subtotal[$kdspk])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											
										}
									}
									
									//subtotal
									$dynamic_record = array(
										'tanggal'   => '<b>' . 'SUBTOTAL'. '</b>',
										'no_bukti'  => '',
										'keterangan' => '',
										'debet' => '',
										'kredit' => '',
										'nasabah' => '',
										'saldo' => '<b>' . $this->format_money2($base,$saldo[$kdspk]+$subtotal[$kdspk]) . '</b>',
									);
									$tpl1->assignDynamic('row', $dynamic_record);
									$tpl1->parseConcatDynamic('row');
									
								}
								
								//page
								$dynamic_record = array(
									'kdspk' => $kdspk,
									'nmspk' => $data[$kdspk]['nmspk'],
									'title_kdspk' => 'KODE PROYEK :',
									'title_nmspk' => 'NAMA PROYEK :',
									'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
									'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
									'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
									'LBLKODE' => 'KODE JO',
									'LBLNAMA' =>'JENIS JO',
									'KDNASABAH' => $val,
									'NAMANASABAH' => $this->get_jenis_jo($base, $val)
								);
								$tpl0->assignDynamic('page', $dynamic_record);
								$tpl0->parseConcatDynamic('page');
								$tpl2->emptyParsedPage();
							
								$no[$kdspk]++;
							}
							
							//spk
							$dynamic_record = array(
								'PER'	=> ' PER SPK',
								'kdspk' => $kdspk,
								'nmspk' => $data[$kdspk]['nmspk'],
								'title_kdspk' => 'KODE PROYEK :',
								'title_nmspk' => 'NAMA PROYEK :',
								'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
								'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
								'selisih'   => $this->format_money2($base, $data[$kdspk]['sum'])
							);
							$tpl->assignDynamic('spk', $dynamic_record);
							$tpl->parseConcatDynamic('spk');
							$tpl1->emptyParsedPage();
						}
					}
				}else{
					foreach($detail as $kdspk => $v){
							$no[$kdspk] =0;
							foreach ($jo as $key=>$val){
								foreach($v as $kk => $vv){
									$subtotal[$kdspk] = 0;
									$saldo[$kdspk]	= 0;
									//subgroup
									foreach($data1 as $k => $val2 ){
										if ($val2['kdalat'] == $val and $kdspk == $val2['kdspk']){
											$dynamic_record = array(
												'tanggal'   => '<b>Saldo Awal ' . $val . '</b>',
												'no_bukti'  => '',
												'keterangan' => '',
												'debet' => '',
												'kredit' => '',
												'nasabah' => '',
												'saldo' => $this->format_money2($base,$val2['saldoawal'])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											$subtotal[$kdspk] = $val2['saldoawal'];
										}
									}
											
									foreach($vv as $kkk => $vvv)
									{
										
										if ($val == $vvv['kdalat'] ){
											$saldo[$kdspk] = $saldo[$kdspk] + $vvv['debet']-$vvv['kredit'];
											$dynamic_record = array(
												'tanggal'   => $vvv['tgl'],
												'no_bukti'  => $vvv['nobukti'],
												'keterangan' => $vvv['keterangan'],
												'nasabah' => $vvv['kdalat'],
												'kredit' => $this->format_money2($base, $vvv['kredit']),
												'debet' => $this->format_money2($base, $vvv['debet']),
												'saldo' => $this->format_money2($base, $saldo[$kdspk]+$subtotal[$kdspk])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											
										}
									}
									
									//subtotal
									$dynamic_record = array(
										'tanggal'   => '<b>' . 'SUBTOTAL'. '</b>',
										'no_bukti'  => '',
										'keterangan' => '',
										'debet' => '',
										'kredit' => '',
										'nasabah' => '',
										'saldo' => '<b>' . $this->format_money2($base,$saldo[$kdspk]+$subtotal[$kdspk]) . '</b>',
									);
									$tpl1->assignDynamic('row', $dynamic_record);
									$tpl1->parseConcatDynamic('row');
								}
								
								//page
								$dynamic_record = array(
									'kdspk' => $kdspk,
									'nmspk' => $data[$kdspk]['nmspk'],
									'title_kdspk' => 'KODE PROYEK :',
									'title_nmspk' => 'NAMA PROYEK :',
									'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
									'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
									'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
									'LBLKODE' => 'KODE JO',
									'LBLNAMA' =>'JENIS JO',
									'KDNASABAH' => $val,
									'NAMANASABAH' => $this->get_jenis_jo($base, $val)
								);
								$tpl0->assignDynamic('page', $dynamic_record);
								$tpl0->parseConcatDynamic('page');
								$tpl2->emptyParsedPage();
							
								$no[$kdspk]++;
							}
							
							//spk
							$dynamic_record = array(
								'PER'	=> ' PER SPK',
								'kdspk' => $kdspk,
								'nmspk' => $data[$kdspk]['nmspk'],
								'title_kdspk' => 'KODE PROYEK :',
								'title_nmspk' => 'NAMA PROYEK :',
								'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
								'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
								'selisih'   => $this->format_money2($base, $data[$kdspk]['sum'])
							);
							$tpl->assignDynamic('spk', $dynamic_record);
							$tpl->parseConcatDynamic('spk');
							$tpl1->emptyParsedPage();
					}
				}
				
				$kdreport = "rk_jo_16";
				
				$filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
				$isi_excel = & $tpl->parsedPage();
				$this->cetak_to_file($base,$filename,$isi_excel);
				
				$filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_per_".$this->get_var('per').".html";
				$isi = & $tpl->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			
			}
			else
			{
			//per jenis jo
				foreach ($jo as $key=>$val){
					if ($nospk != ''){
						foreach($detail as $kdspk => $v){
							if ($kdspk == $nospk){
								$no[$kdspk] =0;
								foreach($v as $kk => $vv){
									$subtotal[$kdspk] = 0;
									$saldo[$kdspk]	= 0;
									//subgroup
									foreach($data1 as $k => $val2 ){
										if ($val2['kdalat'] == $val and $kdspk == $val2['kdspk']){
											$dynamic_record = array(
												'tanggal'   => '<b>Saldo Awal ' . $val . '</b>',
												'no_bukti'  => '',
												'keterangan' => '',
												'debet' => '',
												'kredit' => '',
												'nasabah' => '',
												'saldo' => $this->format_money2($base,$val2['saldoawal'])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											$subtotal[$kdspk] = $val2['saldoawal'];
										}
									}
									foreach($vv as $kkk => $vvv)
									{
										if ($val == $vvv['kdalat'] ){
											$saldo[$kdspk] = $saldo[$kdspk] + $vvv['debet']-$vvv['kredit'];
											$dynamic_record = array(
												'tanggal'   => $vvv['tgl'],
												'no_bukti'  => $vvv['nobukti'],
												'keterangan' => $vvv['keterangan'],
												'nasabah' => $vvv['kdalat'],
												'kredit' => $this->format_money2($base, $vvv['kredit']),
												'debet' => $this->format_money2($base, $vvv['debet']),
												'saldo' => $this->format_money2($base, $saldo[$kdspk]+$subtotal[$kdspk])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');	
										}
									}
									
									//subtotal
									$dynamic_record = array(
										'tanggal'   => '<b>' . 'SUBTOTAL'. '</b>',
										'no_bukti'  => '',
										'keterangan' => '',
										'debet' => '',
										'kredit' => '',
										'nasabah' => '',
										'saldo' => '<b>' . $this->format_money2($base,$saldo[$kdspk]+$subtotal[$kdspk]) . '</b>',
									);
									$tpl1->assignDynamic('row', $dynamic_record);
									$tpl1->parseConcatDynamic('row');
									
								}
									
								//page
								$dynamic_record = array(
									// 'kdspk' => $kdspk,
									// 'nmspk' => $data[$kdspk]['nmspk'],
									'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
									'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
									'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
									'LBLKODE' => 'KODE SPK',
									'LBLNAMA' => 'NAMA SPK',
									'KDNASABAH' => $kdspk,
									'NAMANASABAH' => $data[$kdspk]['nmspk']
								);
								$tpl0->assignDynamic('page', $dynamic_record);
								$tpl0->parseConcatDynamic('page');
								$tpl2->emptyParsedPage();
							
								$no[$kdspk]++;
							}
						}
							
						//spk
						$dynamic_record = array(
							'PER'	=> ' PER JENIS JO',
							'kdspk' => $val,
							'nmspk' => $this->get_jenis_jo($base, $val),
							'title_kdspk' => 'KODE JO :',
							'title_nmspk' => 'JENIS JO :',
							'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
							'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
							'selisih'   => $this->format_money2($base, $data[$kdspk]['sum'])
						);
						$tpl->assignDynamic('spk', $dynamic_record);
						$tpl->parseConcatDynamic('spk');
						$tpl1->emptyParsedPage();
					}else{
						foreach($detail as $kdspk => $v){
								$no[$kdspk] =0;
								foreach($v as $kk => $vv){
									$subtotal[$kdspk] = 0;
									$saldo[$kdspk]	= 0;
									//subgroup
									foreach($data1 as $k => $val2 ){
										if ($val2['kdalat'] == $val and $kdspk == $val2['kdspk']){
											$dynamic_record = array(
												'tanggal'   => '<b>Saldo Awal ' . $val . '</b>',
												'no_bukti'  => '',
												'keterangan' => '',
												'debet' => '',
												'kredit' => '',
												'nasabah' => '',
												'saldo' => $this->format_money2($base,$val2['saldoawal'])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
											$subtotal[$kdspk] = $val2['saldoawal'];
										}
									}
									
									foreach($vv as $kkk => $vvv)
									{
										if ($val == $vvv['kdalat'] ){
											$saldo[$kdspk] = $saldo[$kdspk] + $vvv['debet']-$vvv['kredit'];
											$dynamic_record = array(
												'tanggal'   => $vvv['tgl'],
												'no_bukti'  => $vvv['nobukti'],
												'keterangan' => $vvv['keterangan'],
												'nasabah' => $vvv['kdalat'],
												'kredit' => $this->format_money2($base, $vvv['kredit']),
												'debet' => $this->format_money2($base, $vvv['debet']),
												'saldo' => $this->format_money2($base, $saldo[$kdspk]+$subtotal[$kdspk])
											);
											$tpl1->assignDynamic('row', $dynamic_record);
											$tpl1->parseConcatDynamic('row');
										}
									}
									
									//subtotal
									$dynamic_record = array(
										'tanggal'   => '<b>' . 'SUBTOTAL'. '</b>',
										'no_bukti'  => '',
										'keterangan' => '',
										'debet' => '',
										'kredit' => '',
										'nasabah' => '',
										'saldo' => '<b>' . $this->format_money2($base,$saldo[$kdspk]+$subtotal[$kdspk]) . '</b>',
									);
									$tpl1->assignDynamic('row', $dynamic_record);
									$tpl1->parseConcatDynamic('row');
									
								}
									
								//page
								$dynamic_record = array(
									// 'kdspk' => $kdspk,
									// 'nmspk' => $data[$kdspk]['nmspk'],
									'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
									'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
									'selisih'   => $this->format_money2($base, $data[$kdspk]['sum']),
									'LBLKODE' => 'KODE SPK',
									'LBLNAMA' => 'NAMA SPK',
									'KDNASABAH' => $kdspk,
									'NAMANASABAH' => $data[$kdspk]['nmspk']
								);
								$tpl0->assignDynamic('page', $dynamic_record);
								$tpl0->parseConcatDynamic('page');
								$tpl2->emptyParsedPage();
							
								$no[$kdspk]++;
							
						}
							
						//spk
						$dynamic_record = array(
							'PER'	=> ' PER JENIS JO',
							'kdspk' => $val,
							'nmspk' => $this->get_jenis_jo($base, $val),
							'title_kdspk' => 'KODE JO :',
							'title_nmspk' => 'JENIS JO :',
							'saldo_div1' => $this->format_money2($base, $data[$kdspk]['div']),
							'saldo_div2' => $this->format_money2($base, $data[$kdspk]['kso']),
							'selisih'   => $this->format_money2($base, $data[$kdspk]['sum'])
						);
						$tpl->assignDynamic('spk', $dynamic_record);
						$tpl->parseConcatDynamic('spk');
						$tpl1->emptyParsedPage();
						
					}
				}
				$kdreport = "rk_jo_16";
				
				$filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
				$isi_excel = & $tpl->parsedPage();
				$this->cetak_to_file($base,$filename,$isi_excel);
				
				$filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_per_".$this->get_var('per').".html";
				$isi = & $tpl->parsedPage();
				$this->cetak_to_file($base,$filename,$isi);
			}
			$this->tpl = $tpl;   
		// =====ikhtisar===== //
		}else{
			$tpl = $base->_get_tpl('laporan_rk_ikhtisar_16.html');
			// $tpl0 =& $tpl->defineDynamicBlock('spk');
			// $tpl1 =& $tpl0->defineDynamicBlock('page');
			$tpl1 =& $tpl->defineDynamicBlock('row');
			$tpl2 =& $tpl->defineDynamicBlock('head');
			$this->_fill_static_report($base,&$tpl);
			
			$kdperkiraan = "21911";
            $periode_end = date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode = date('Y-m-d', mktime(0,0,0,$month,1,$year));
			$periode_akhir 	= date('Y-m-d', mktime(0,0,0,$month+1,0,$year));
            $dp = new dateparse();
			
            
            $record_static = array(
                'TITLE' => 'IKHTISAR RK KSO',
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => 'Kantor Divisi',
                'DIV2' => 'Proyek KSO'
            );
            $tpl->assign($record_static);
            
            // $sqlg = "SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, j.kdspk, ds.nmspk
                    	// ,CASE WHEN substr(j.nobukti,1,2) = '01' THEN 'div' ELSE 'kso' END AS grp
                    // FROM {$jurnal} j
                    // LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
                    // WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                    // AND date_trunc('month', j.tanggal) <= '{$periode}'
                    // GROUP BY j.kdspk,nmspk,grp
                    // ORDER BY j.kdspk, grp";
            // $rsg = $base->dbQuery($sqlg);
            
            // $data = array();
            // while(!$rsg->EOF)
            // {
                // $data[$rsg->fields['kdspk']]['nmspk'] = $rsg->fields['nmspk'];
                // $data[$rsg->fields['kdspk']][$rsg->fields['grp']] = $rsg->fields['rupiah'];
                // $data[$rsg->fields['kdspk']]['sum'] += $rsg->fields['rupiah'];
                
                // $rsg->moveNext();
            // }
			
			$sqld="
					SELECT
					ax.kdalat, ax.kdspk, sum(ax.rupiah) as rupiah
					FROM
					(
					SELECT
					j.kdspk,
					j.kdalat,
					(CASE WHEN upper(j.dk)='D' THEN COALESCE(j.rupiah,0) ELSE COALESCE(j.rupiah,0)*-1 END) as rupiah
					FROM
					{$jurnal} j
					-- LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan = '{$kdperkiraan}'
					-- AND j2.nobukti NOT LIKE '01%' 
					-- AND (
					-- j.nobukti = j2.buktipelunasan OR j.buktipelunasan = j2.nobukti
					-- )
					-- AND j.rupiah = j2.rupiah
					-- AND j.kdspk = j2.kdspk
					-- AND j2.isdel = 'f'
					-- AND j2.isapp = 't'
					-- AND date_trunc('month', j2.tanggal) <= '{$periode_akhir}'
					-- )
					WHERE
					j.isdel = 'f' AND j.isapp = 't' AND j.kdperkiraan = '{$kdperkiraan}' 
					-- AND j.nobukti LIKE '01%'
					-- AND j2.nobukti IS NULL
					AND date_trunc('month', j.tanggal) <= '{$periode_akhir}'
					) ax
					GROUP BY 
					kdalat, kdspk
					ORDER BY kdspk, kdalat
			";
			
			$rsd = $base->dbQuery($sqld);
            while(!$rsd->EOF)
            {
                $detail[$rsd->fields['kdspk']][] = $rsd->fields;
                $rsd->moveNext();
            }
			
			$sqljo 	= 	"SELECT DISTINCT(kdalat) as kdalat FROM {$jurnal} j LEFT JOIN dspk ds ON (ds.kdspk=j.kdspk)
							WHERE j.kdperkiraan ='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
							AND date_trunc('month',j.tanggal) <= '{$periode}' ORDER BY kdalat
						";
			$rsjo	= $base->dbQuery($sqljo);
			while(!$rsjo->EOF)
            {
                $jo[] = $rsjo->fields['kdalat'];
                $rsjo->moveNext();
            }
			foreach( $detail as $nospk=> $data){
			$array[$nospk]['DJO7']=0;
				foreach ($data as $key => $val){
					if (in_array($val['kdalat'], array('DJO1', 'DJO2', 'DJO3','DJO4', 'DJO5','DJO6'))){
						foreach($jo as $kd=>$jnsjo){
							if ($jnsjo == $val['kdalat']){
								$array[$nospk][$jnsjo] = $val['rupiah'];
							}
						}
					}else{
							$array[$nospk]['DJO7'] += $val['rupiah'];
						}
				}
			}
			$totdjo1 = 0;
			$totdjo2 = 0;
			$totdjo3 = 0;
			$totdjo4 = 0;
			$totdjo5 = 0;
			$totdjo6 = 0;
			$totdjo7 = 0;
			$totalall= 0;
			foreach($array as $key1 => $val1){
				$totdjo1 += $val1['DJO1'];
				$totdjo2 += $val1['DJO2'];
				$totdjo3 += $val1['DJO3'];
				$totdjo4 += $val1['DJO4'];
				$totdjo5 += $val1['DJO5'];
				$totdjo6 += $val1['DJO6'];
				$totdjo7 += $val1['DJO7'];
				$total = $val1['DJO1']+$val1['DJO2']+$val1['DJO3']+$val1['DJO4']+$val1['DJO5']+$val1['DJO6']+$val1['DJO7'];
				$totalall += $total;
				$dynamic_record = array(
					'kdspk'   => $key1,
					'nmspk'  => $this->get_nama_spk($base, $key1, $kddivisi),
					'djo1' => $this->format_money2($base, $val1['DJO1']),
					'djo2' => $this->format_money2($base, $val1['DJO2']),
					'djo3' => $this->format_money2($base, $val1['DJO3']),
					'djo4' => $this->format_money2($base, $val1['DJO4']),
					'djo5' => $this->format_money2($base, $val1['DJO5']),
					'djo6' => $this->format_money2($base, $val1['DJO6']),
					'djo0' => $this->format_money2($base, $val1['DJO7']),
					'total' => $this->format_money2($base, $total),
				);
				$tpl->assignDynamic('row', $dynamic_record);
				$tpl->parseConcatDynamic('row');
				
				$dynamic_record = array(
					'TOTDJO1' => '<b>' . $this->format_money2($base, $totdjo1). '</b>',
					'TOTDJO2' => '<b>' . $this->format_money2($base, $totdjo2). '</b>',
					'TOTDJO3' => '<b>' . $this->format_money2($base, $totdjo3). '</b>',
					'TOTDJO4' => '<b>' . $this->format_money2($base, $totdjo4). '</b>',
					'TOTDJO5' => '<b>' . $this->format_money2($base, $totdjo5). '</b>',
					'TOTDJO6' => '<b>' . $this->format_money2($base, $totdjo6). '</b>',
					'TOTDJO7' => '<b>' . $this->format_money2($base, $totdjo7). '</b>',
					'TOTALALL' => '<b>' . $this->format_money2($base, $totalall). '</b>',
				);
				$tpl->assignDynamic('head', $dynamic_record);
				$tpl->parseConcatDynamic('head');
				$tpl2->emptyParsedPage();
				
				// $kdreport = "rk_jo_16";
				
				// $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_ikhtisar";
				// $isi_excel = & $tpl->parsedPage();
				// $this->cetak_to_file($base,$filename,$isi_excel);
				
				// $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_ikhtisar.html";
				// $isi = & $tpl->parsedPage();
				// $this->cetak_to_file($base,$filename,$isi);
			}
			$this->tpl = $tpl;
		}
        // }
        // else if($this->get_var('is_excel')=='t')
        // {
			// print_r("etduna");exit;
            // $kdreport = "rk_jo_16";
            
            // $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci_for_excel.html";
            // $fp = @fopen($filename,"r"); 
    		// if (!$fp) 
    			// die("The file does not exists!");
    			
    		
    		// $contents = fread ($fp, filesize ($filename));
    		
			// header('content-type: application/vnd.ms-excel');
			// header('Content-Disposition: attachment; filename='.$filename.'.xls');
    				
    		// fclose ($fp);
    
    		// $tpl = $base->_get_tpl('one_var.html');
    		// $tpl->assign('ONE' ,	$contents);
    		
    		// $this->tpl = $tpl;
        // }
        // else
        // {
			// print_r("etdunb");exit;
            // $kdreport = "rk_jo_16";
            
            // $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$year."_".$month."_rinci.html";
            // $fp = @fopen($filename,"r"); 
            // if (!$fp) 
            	// die("The file does not exists!");
            	
            
            // $contents = fread ($fp, filesize ($filename));
            // fclose ($fp);
            
            // $tpl = $base->_get_tpl('one_var.html');
            // $tpl->assign('ONE' , $contents);
            
            // $this->tpl = $tpl;
        // }
        
    }/*}}}*/
    
		
    function sub_rk_div_rul($base)/*{{{*/
    {
        $base->db= false;        
        $kddivisi = $this->S['curr_divisi'];
        $jurnal = "jurnal_".strtolower($kddivisi);
        $month = $this->get_var('rmonth');
        $year = $this->get_var('ryear');
        $kddiv_rk = $this->get_var('kddiv');
        $jurnal2 = "jurnal_".strtolower($kddiv_rk);

        $tpl = $base->_get_tpl('laporan_rk.html');
        $tpl1 =& $tpl->defineDynamicBlock('page');
        $tpl2 =& $tpl1->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        
        if($this->get_var('is_proses') == 't')
        {
            $periode_end = date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode = date('Y-m-d', mktime(0,0,0,$month,1,$year));
            
            $op1 = $kddivisi.$kddiv_rk;
            $op2 = $kddiv_rk.$kddivisi;
            $kdperkiraan = $base->dbGetOne("SELECT kdperkiraan FROM config_rk WHERE kddivisi_1||kddivisi_2 IN ('{$op1}','{$op2}')");
            
            $divname1 = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'");
            $divname2 = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddiv_rk}'");
            
            
            $dp = new dateparse();
            $tpl->assign(array(
                'TITLE' => 'Rekonsiliasi Hub R/K '. $kdperkiraan .' (Antar Departemen) <br />' . $divname1 ." - " . $divname2,
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => $divname1,
                'DIV2' => $divname2,
            ));
            
            if($jurnal=="jurnal_l"){
			$tambahan = " AND  j.tanggal >= '2012-01-01'";
			}
			elseif($kddiv_rk == "L"){
			$tambahan2 = " AND vj.tanggal >= '2012-01-01'";	
			}elseif($kddivisi == "L"){
			$tambahan2 = " AND vj.tanggal >= '2012-01-01'";	
			}else{
				$tambahan = "";
			}
            $sqlg = "SELECT * FROM (
                        SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, d.nmdivisi, j.kddivisi, 0 as ordby
                        FROM {$jurnal} j, ddivisi d
                        WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                            AND date_trunc('month', j.tanggal) <= '{$periode}' {$tambahan}
                            AND j.kddivisi=d.kddivisi
                        GROUP BY d.nmdivisi, j.kddivisi
                        
                        UNION ALL
                        
                        SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, d.nmdivisi, j.kddivisi, 0 as ordby
                        FROM {$jurnal2} j, ddivisi d
                        WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                            AND date_trunc('month', j.tanggal) <= '{$periode}' {$tambahan}
                            AND j.kddivisi=d.kddivisi
                        GROUP BY d.nmdivisi, j.kddivisi
                    ) a ORDER BY a.ordby";
            $rsg = $base->dbQuery($sqlg);
            
            $data = array();
            while(!$rsg->EOF)
            {
                $data[$rsg->fields['kddivisi']]['nmdivisi'] = $rsg->fields['nmdivisi'];
                $data[$rsg->fields['kddivisi']]['rupiah'] = $rsg->fields['rupiah'];
                $data['sum'] += $rsg->fields['rupiah'];
                
                $rsg->moveNext();
            }
            
            if($jurnal=="jurnal_l"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";
			$tambahan = " AND a.tanggal >= '2012-01-01'";
			}
			elseif($kddiv_rk == "L"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";	
			$tambahan = " AND a.tanggal >= '2012-01-01'";
			}
			elseif($kddivisi == "L"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";
			$tambahan = " AND a.tanggal >= '2012-01-01'";	
			}else{
				$tambahan2 = "";
				$tambahan = "";
			}
			
            $el_sqld_jurnal = "
            	SELECT * FROM (
            	
            		SELECT * FROM (
						SELECT 
		            		nobukti,
		            		to_char(tanggal, 'DD-Mon-YYYY') AS tgl,
		            		keterangan,
		            		dk,
		            		get_dk(dk)*rupiah AS rupiah,
		            		-- ABS(rupiah) AS rupiah,
		            		'{$kddiv_rk}'::text AS kddivisi,
		            		-- kddivisi,
		            		tanggal,
		            		buktipelunasan,
		            		kdperkiraan,
		            		createdate
						FROM {$jurnal}
						WHERE
						   tanggal < (DATE '{$year}-{$month}-01' + INTERVAL '1 month')
					       AND kdperkiraan = '{$kdperkiraan}'
					       AND isdel = 'f'
					       AND buktipelunasan = ''
					) tbl1
					
					UNION ALL
					
					SELECT * FROM (
						SELECT 
		            		nobukti,
		            		to_char(tanggal, 'DD-Mon-YYYY') AS tgl,
		            		keterangan,
		            		dk,
		            		get_dk(dk)*rupiah AS rupiah,
		            		-- ABS(rupiah) AS rupiah,
		            		'{$kddivisi}'::text AS kddivisi,
		            		-- kddivisi,
		            		tanggal,
		            		buktipelunasan,
		            		kdperkiraan,
		            		createdate
						FROM {$jurnal2}
						WHERE
						   tanggal < (DATE '{$year}-{$month}-01' + INTERVAL '1 month')
					       AND kdperkiraan = '{$kdperkiraan}'
					       AND isdel = 'f'
					       AND buktipelunasan <> ''
					) tbl2
					
				) tbl_union
				ORDER BY 
					kddivisi, tanggal, createdate
            ";
			$el_rs_jurnal = $base->dbQuery($el_sqld_jurnal);
			$detail = array();
			while ( ! $el_rs_jurnal->EOF) {
				$detail[ $el_rs_jurnal->fields['kddivisi'] ][] = $el_rs_jurnal->fields;
				$el_rs_jurnal->moveNext();
			}
			
			$unset_var = array();
			foreach ($detail[$kddiv_rk] as $key_1 => $val_1) { 			// jurnal
				foreach ($detail[$kddivisi] as $key_2 => $val_2) { 		// jurnal LAWAN
					if ( ($val_1['nobukti'] === $val_2['buktipelunasan']) && (abs($val_1['rupiah']) === abs($val_2['rupiah'])) ) {
						$unset_var[] = array($key_2, $key_1);
						break 1;
					}
				}
			}
			foreach ($unset_var as $item) {
				unset($detail[$kddivisi][$item[0]]);
				unset($detail[$kddiv_rk][$item[1]]);
			}
			
			// print '<pre>'. var_export($detail,TRUE) .'</pre>';
            
            foreach($detail as $k => $v)
            {
                $subtotal = 0;
                //subgroup
                $dynamic_record = array(
                    'tanggal'   => '<b>Belum dibuku di ' . $data[$k]['nmdivisi'] . '</b>',
                    'no_bukti'  => '',
                    'keterangan' => '',
                    'dk' => '',
                    'rupiah' => ''
                );
                $tpl1->assignDynamic('row', $dynamic_record);
                $tpl1->parseConcatDynamic('row');
    
                foreach($v as $kk => $vv)
                {
                    $dynamic_record = array(
                        'tanggal'   => $vv['tgl'],
                        'no_bukti'  => $vv['nobukti'],
                        'keterangan' => $vv['keterangan'],
                        'dk' => $vv['dk'],
                        'rupiah' => $this->format_money2($base, $vv['rupiah'])
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                    $subtotal += $vv['rupiah'];
                }
                
                //subtotal
                $dynamic_record = array(
                    'tanggal'   => '',
                    'no_bukti'  => '',
                    'keterangan' => '',
                    'dk' => '',
                    'rupiah' => '<b>' . $this->format_money2($base, $subtotal) . '</b>'
                );
                $tpl1->assignDynamic('row', $dynamic_record);
                $tpl1->parseConcatDynamic('row');
                
            }
            
            //group
            $dynamic_record = array(
                'kdspk' => '',
                'nmspk' => '',
                'title_kdspk' => '',
                'title_nmspk' => '',
                'saldo_div1' => $this->format_money2($base, $data[$kddivisi]['rupiah']),
                'saldo_div2' => $this->format_money2($base, $data[$kddiv_rk]['rupiah']),
                'selisih'   => $this->format_money2($base, $data['sum']),
            );
            $tpl->assignDynamic('page', $dynamic_record);
            $tpl->parseConcatDynamic('page');
            $tpl2->emptyParsedPage();
            
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci_for_excel.html";
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
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci.html";
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
	
	/** 
	* Backed up by Eldin
	*/
    function sub_rk_div($base)/*{{{*/
    {
       //$base->db->debug= true;        
        $kddivisi = $this->S['curr_divisi'];
        $jurnal = "jurnal_".strtolower($kddivisi);
        $month = $this->get_var('rmonth');
        $year = $this->get_var('ryear');
        $kddiv_rk = $this->get_var('kddiv');
        $jurnal2 = "jurnal_".strtolower($kddiv_rk);

        $tpl = $base->_get_tpl('laporan_rk.html');
        $tpl1 =& $tpl->defineDynamicBlock('page');
        $tpl2 =& $tpl1->defineDynamicBlock('row');
        $this->_fill_static_report($base,&$tpl);
        
        if($this->get_var('is_proses') == 't')
        {
            $periode_end = date('d/m/Y', mktime(0,0,0,$month+1,0,$year));
            $periode = date('Y-m-d', mktime(0,0,0,$month,1,$year));
            
            $op1 = $kddivisi.$kddiv_rk;
            $op2 = $kddiv_rk.$kddivisi;
            $kdperkiraan = $base->dbGetOne("SELECT kdperkiraan FROM config_rk WHERE kddivisi_1||kddivisi_2 IN ('{$op1}','{$op2}')");
            
            $divname1 = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'");
            $divname2 = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddiv_rk}'");
            
            
            $dp = new dateparse();
            $tpl->assign(array(
                'TITLE' => 'Rekonsiliasi Hub R/K (Antar Departemen) <br />' . $divname1 ." - " . $divname2,
                'BULAN' => $dp->monthnamelong[$month],
                'TAHUN' => $year,
                'DIVNAME' => $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi='{$kddivisi}'"),
                'periode' => $periode_end,
                'DIV1'  => $divname1,
                'DIV2' => $divname2,
            ));
            
            if($jurnal=="jurnal_l"){
			$tambahan = " AND  j.tanggal >= '2012-01-01'";
			}
			elseif($kddiv_rk == "L"){
			$tambahan2 = " AND vj.tanggal >= '2012-01-01'";	
			}elseif($kddivisi == "L"){
			$tambahan2 = " AND vj.tanggal >= '2012-01-01'";	
			}else{
				$tambahan = "";
			}
            $sqlg = "SELECT * FROM (
                        SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, d.nmdivisi, j.kddivisi, 0 as ordby
                        FROM {$jurnal} j, ddivisi d
                        WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                            AND date_trunc('month', j.tanggal) <= '{$periode}' {$tambahan}
                            AND j.kddivisi=d.kddivisi
                        GROUP BY d.nmdivisi, j.kddivisi
                        
                        UNION ALL
                        
                        SELECT SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*rupiah) as rupiah, d.nmdivisi, j.kddivisi, 0 as ordby
                        FROM {$jurnal2} j, ddivisi d
                        WHERE kdperkiraan='{$kdperkiraan}' AND j.isdel='f' AND j.isapp='t'
                            AND date_trunc('month', j.tanggal) <= '{$periode}' {$tambahan}
                            AND j.kddivisi=d.kddivisi
                        GROUP BY d.nmdivisi, j.kddivisi
                    ) a ORDER BY a.ordby";
            $rsg = $base->dbQuery($sqlg);
            
            $data = array();
            while(!$rsg->EOF)
            {
                $data[$rsg->fields['kddivisi']]['nmdivisi'] = $rsg->fields['nmdivisi'];
                $data[$rsg->fields['kddivisi']]['rupiah'] = $rsg->fields['rupiah'];
                $data['sum'] += $rsg->fields['rupiah'];
                
                $rsg->moveNext();
            }
            
            /*$sqld = "SELECT * FROM
                    (
                    SELECT j.nobukti, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, j.kddivisi, j.tanggal, 0 as ordby
                    FROM {$jurnal} j
                    LEFT JOIN {$jurnal2} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j2.isdel='f' AND j2.isapp='t' AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j2.nobukti IS NULL AND date_trunc('month', j.tanggal) <= '{$periode}' 
                    
                    UNION ALL
                    
                    SELECT j.nobukti, to_char(j.tanggal,'DD-Mon-YYYY') as tgl, j.keterangan, j.dk, (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah as rupiah, j.kddivisi, j.tanggal, 1 as ordby
                    FROM {$jurnal2} j
                    LEFT JOIN {$jurnal} j2 ON (j2.kdperkiraan='{$kdperkiraan}' AND (j.nobukti=j2.buktipelunasan OR j.buktipelunasan=j2.nobukti) 
                        AND j.rupiah=j2.rupiah AND j2.isdel='f' AND j2.isapp='t' AND date_trunc('month', j2.tanggal) <= '{$periode}')
                    WHERE j.isdel='f' AND j.isapp='t' AND j.kdperkiraan='{$kdperkiraan}' AND j2.nobukti IS NULL  AND date_trunc('month', j.tanggal) <= '{$periode}'
                    ) ax
                    ORDER BY ordby, tanggal";
            */
            if($jurnal=="jurnal_l"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";
			$tambahan = " AND a.tanggal >= '2012-01-01'";
			}
			elseif($kddiv_rk == "L"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";	
			$tambahan = " AND a.tanggal >= '2012-01-01'";
			}
			elseif($kddivisi == "L"){
			$tambahan2 = " AND b.tanggal >= '2012-01-01'";
			$tambahan = " AND a.tanggal >= '2012-01-01'";	
			}else{
				$tambahan2 = "";
				$tambahan = "";
			}//$base->db= true;
		//old sql removed by gunab <gunab@flovey.com>
            /*$sqld = "SELECT vj.nobukti, to_char(vj.tanggal,'DD-Mon-YYYY') as tgl, vj.keterangan, vj.dk, get_dk(vj.dk)*vj.rupiah as rupiah, vj.lawan_rk as kddivisi, vj.tanggal
                    FROM v_konsolidasi_rk vj
                    LEFT JOIN (SELECT * FROM v_konsolidasi_rk vjx
                    	WHERE vjx.kddivisi IN ('{$kddivisi}','{$kddiv_rk}') AND vjx.lawan_rk IN ('{$kddiv_rk}', '{$kddivisi}')
                    	AND COALESCE(vjx.buktipelunasan,'')<>''	AND date_trunc('month', vjx.tanggal) <= '{$periode}' 
                    ) vj2 ON (vj.nobukti=vj2.buktipelunasan AND vj.rupiah=vj2.rupiah)  {$tambahan2}
                    WHERE vj.kddivisi IN ('{$kddivisi}','{$kddiv_rk}') AND vj.lawan_rk IN ('{$kddiv_rk}', '{$kddivisi}') 
                    AND COALESCE(vj.buktipelunasan,'')='' AND date_trunc('month', vj.tanggal) <= '{$periode}' {$tambahan2}
                        AND vj2.nobukti IS NULL
                    ORDER BY vj.lawan_rk, vj.tanggal";*/
					//die($sqld);
		$sqld = "SELECT a.nobukti, to_char(a.tanggal,'DD-Mon-YYYY') as tgl, a.keterangan, a.dk
                        , get_dk(a.dk)*a.rupiah as rupiah, a.lawan_rk as kddivisi, a.tanggal
                    FROM v_konsolidasi_rk a 
                    LEFT JOIN (
                    	SELECT *
                        FROM v_konsolidasi_rk b
                        WHERE b.kdperkiraan='{$kdperkiraan}' AND date_trunc('month', b.tanggal) <= '{$periode}' {$tambahan2}
                    ) b ON (b.rupiah=a.rupiah AND b.dk<>a.dk AND (a.nobukti=b.buktipelunasan OR a.buktipelunasan=b.nobukti) 
                            AND a.nobukti<>b.nobukti )
                    WHERE a.kdperkiraan='{$kdperkiraan}' AND date_trunc('month', a.tanggal) <= '{$periode}' {$tambahan}
                    AND COALESCE(b.nobukti,'') = ''
			ORDER BY a.lawan_rk, a.tanggal";	
            $rsd = $base->dbQuery($sqld);
            while(!$rsd->EOF)
            {
                $detail[$rsd->fields['kddivisi']][] = $rsd->fields;
                $rsd->moveNext();
            }
            
            foreach($detail as $k => $v)
            {
                $subtotal = 0;
                //subgroup
                $dynamic_record = array(
                    'tanggal'   => '<b>Belum dibuku di ' . $data[$k]['nmdivisi'] . '</b>',
                    'no_bukti'  => '',
                    'keterangan' => '',
                    'dk' => '',
                    'rupiah' => ''
                );
                $tpl1->assignDynamic('row', $dynamic_record);
                $tpl1->parseConcatDynamic('row');
    
                foreach($v as $kk => $vv)
                {
                    $dynamic_record = array(
                        'tanggal'   => $vv['tgl'],
                        'no_bukti'  => $vv['nobukti'],
                        'keterangan' => $vv['keterangan'],
                        'dk' => $vv['dk'],
                        'rupiah' => $this->format_money2($base, $vv['rupiah'])
                    );
                    $tpl1->assignDynamic('row', $dynamic_record);
                    $tpl1->parseConcatDynamic('row');
                    
                    $subtotal += $vv['rupiah'];
                }
                
                //subtotal
                $dynamic_record = array(
                    'tanggal'   => '',
                    'no_bukti'  => '',
                    'keterangan' => '',
                    'dk' => '',
                    'rupiah' => '<b>' . $this->format_money2($base, $subtotal) . '</b>'
                );
                $tpl1->assignDynamic('row', $dynamic_record);
                $tpl1->parseConcatDynamic('row');
                
            }
            
            //group
            $dynamic_record = array(
                'kdspk' => '',
                'nmspk' => '',
                'title_kdspk' => '',
                'title_nmspk' => '',
                'saldo_div1' => $this->format_money2($base, $data[$kddivisi]['rupiah']),
                'saldo_div2' => $this->format_money2($base, $data[$kddiv_rk]['rupiah']),
                'selisih'   => $this->format_money2($base, $data['sum']),
            );
            $tpl->assignDynamic('page', $dynamic_record);
            $tpl->parseConcatDynamic('page');
            $tpl2->emptyParsedPage();
            
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;    
        }
        else if($this->get_var('is_excel')=='t')
        {
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci_for_excel.html";
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
            $kdreport = "rk_div";
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddivisi."_".$kddiv_rk."_".$year."_".$month."_rinci.html";
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
    
    function sub_cek_report($base)/*{{{*/
    {
        $kdreport = $this->get_var('kdreport');
        $grptype = $this->get_var('grptype');
        $rmonth = $this->get_var('month_');
        $ryear = $this->get_var('year_');
        $type = $this->get_var('tbtype');
        $kddiv = $this->S['curr_divisi'];
        $kddiv2 = ($this->get_var('kdreport') == 'rk_div') ? $this->get_var('kddiv').'_' : '';
        $report = ($this->get_var('report') == '') ? '' : $this->get_var('report').'_';
        $uker = ($this->get_var('uker') == '') ? '' : $this->get_var('uker').'_';
        $spk = ($this->get_var('spk') == '') ? '' : $this->get_var('spk').'_';
        
        
        switch($grptype)
        {
            case 'perspk': 
                $grptype_text = 'PER_SPK_';
                break;
            case 'pernsb':
                $grptype_text = 'PER_NSB_';
                break;
            case 'perwil':
                $grptype_text = 'PER_WILAYAH_';
                break;
            default:
                $grptype_text = '';
                break;
        }
        
        $filename = $base->kcfg['basedir']."files/".$kdreport."_".($grptype_text).$kddiv."_".$kddiv2.$spk.$report.$uker.$ryear."_".$rmonth."_".$type.".html";
		if ($_SERVER['REMOTE_ADDR'] == '10.10.5.13'){
			// echo $filename;
			// echo $grptype;
			// $base->db= true;
		}
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
    
    function sub_cron_consolidation_report($base)/*{{{*/
    {
        //buku besar
        //$this->sub_show_report_overlegder($base, true);
        
        //neraca lajur
        LoadClass("modules.accounting_report.neraca_lajur");
        $nl = new neraca_lajur($base,'controller');
        $nl->sub_report_neraca_lajur($base, true);
        
    }/*}}}*/
    
    function sub_save_labarugi($base)/*{{{*/
    {
        $now = $this->get_var('now', array());
        $bef = $this->get_var('bef', array());
        $tahun = $this->get_var('tahun');
        $month = $this->get_var('month');
        $kddivisi = $this->get_var('kddivisi');
        $kdspk = $this->get_var('kdspk');
        $uker = $this->get_var('uker');
        
        
        
        foreach($now as $k=>$v)
        {
            $sql = "SELECT * FROM labarugi_form 
                WHERE kddivisi='".$kddivisi."' AND kdspk='".$kdspk."' AND uker='".$uker."' 
                AND year=".$tahun." AND month=".$month." AND item_id=".$k."";
            $rs = $base->dbQuery($sql);
            
            $v = str_replace("(","-",$v);
            $v = str_replace(")","",$v);
            $v = str_replace(",","",$v);
            
            $rec =  array(
                'rupiah'    => $v,
                'item_id'   => $k,
                'year' => $tahun,
                'month' => $month,
                'kdspk' => $kdspk,
                'kddivisi'  => $kddivisi,
                'uker'  => $uker,
            );
            
            if($rs->EOF)
            {
                $sqli[] = $base->db->getInsertSql($rs, $rec);
            }
            else
            {
                $sqli[] = $base->db->getUpdateSql($rs, $rec);
            }
        }
        
        foreach($bef as $k=>$v)
        {
            $sql = "SELECT * FROM labarugi_form 
                WHERE kddivisi='".$kddivisi."' AND kdspk='".$kdspk."' AND uker='".$uker."' 
                AND year=".($tahun-1)." AND month=".$month." AND item_id=".$k."";
            $rs = $base->dbQuery($sql);
            
            $v = str_replace("(","-",$v);
            $v = str_replace(")","",$v);
            $v = str_replace(",","",$v);
            
            $rec =  array(
                'rupiah'    => $v,
                'item_id'   => $k,
                'year' => $tahun-1,
                'month' => $month,
                'kdspk' => $kdspk,
                'kddivisi'  => $kddivisi,
                'uker'  => $uker,
            );
            
            if($rs->EOF)
            {
                $sqli[] = $base->db->getInsertSql($rs, $rec);
            }
            else
            {
                $sqli[] = $base->db->getUpdateSql($rs, $rec);
            }
        }
        
        $ret = $base->array_trans_execute($sqli);
        if($ret == '')
            echo 'ok';
        else
            echo 'error';
        
        exit;
        
    }/*}}}*/
    
    function sub_rekon_pajak($base)/*{{{*/
    {//$base->db= true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $kddiv = $this->S['curr_divisi'];
        
        $tpl = $base->_get_tpl('report_rekon_pajak.html');
        $tpl->defineDynamicBlock('row');
        
        $kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk');
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $konsolidasi = $this->get_var('konsolidasi');
        $this->_fill_static_report($base,&$tpl);
        
        $table = "jurnal_".strtolower($this->S['curr_divisi']);    
        if($this->S['curr_divisi'] == '') $table = "jurnal";
		
        $ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        $is_proses = $this->get_var('is_proses');
        $kdreport = $this->get_var('kdreport');
        
		$year_start 	= $this->get_var('year_start',date('Y'));
		$month_start 	= $this->get_var('month_start',date('m'));
		$year_end 		= $this->get_var('year_end',date('Y'));
		$month_end		= $this->get_var('month_end',date('m'));
		
		$ryear 	= $year_end;
		$rmonth = ltrim($month_end,'0');
        
        if($is_proses == 't')
        {
            // $sql = "SELECT j.kdnasabah, j.nmnasabah, to_char(j.tanggal, 'FMDD-Mon-YYYY') as tgl_1, j.nobukti
                    	// , j.faktur_pajak as faktur_pajak_1, j.rupiah,
                        // p.npwp, p.faktur_pajak as faktur_pajak_2, to_char(p.tanggal, 'FMDD-Mon-YYYY') as tgl_2, p.ppn, p.dpp, j.kdspk, j.dk
                    // FROM (SELECT * FROM pembelian_pajak p WHERE date_trunc('month', p.tanggal) = '1-".$rmonth."-".$ryear."') p
                    // FULL JOIN (
                    	// SELECT j.*, n.npwp, n.nmnasabah
                        // FROM {$table} j LEFT JOIN dnasabah n ON (j.kdnasabah=n.kdnasabah)
                        // WHERE COALESCE(j.faktur_pajak,'')<>'' AND j.dk='D' 
                            // AND date_trunc('month', j.tanggal) = '1-".$rmonth."-".$ryear."'
                        // ) j ON (j.npwp=p.npwp AND j.faktur_pajak=p.faktur_pajak AND j.rupiah=p.ppn)
                    // ORDER BY j.kdspk, j.kdnasabah, COALESCE(j.tanggal,p.tanggal), COALESCE(j.faktur_pajak,p.faktur_pajak)";
            
            $sql = "SELECT j.kdnasabah, j.nmnasabah, to_char(j.tanggal, 'FMDD-Mon-YYYY') as tgl_1, j.nobukti
                    	, j.faktur_pajak as faktur_pajak_1, j.rupiah,
                        p.npwp, p.faktur_pajak as faktur_pajak_2, to_char(p.tanggal, 'FMDD-Mon-YYYY') as tgl_2, p.ppn, p.dpp, j.kdspk, j.dk
                    FROM (SELECT * FROM pembelian_pajak p WHERE p.tanggal::date >= '{$year_start}-{$month_start}-01'::date AND p.tanggal::date < ('{$year_end}-{$month_end}-01'::date + INTERVAL '1 month') ) p
                    FULL JOIN (
                    	SELECT j.*, n.npwp, n.nmnasabah
                        FROM {$table} j LEFT JOIN dnasabah n ON (j.kdnasabah=n.kdnasabah)
                        WHERE COALESCE(j.faktur_pajak,'')<>'' AND j.dk='D' AND j.kdperkiraan = '11242' --'1110221'
                            AND j.tanggal::date >= '{$year_start}-{$month_start}-01'::date AND j.tanggal::date < ('{$year_end}-{$month_end}-01'::date + INTERVAL '1 month') 
                    ) j ON (j.npwp=p.npwp AND j.faktur_pajak=p.faktur_pajak AND j.rupiah=p.ppn)
                    ORDER BY j.kdspk, j.kdnasabah, COALESCE(j.tanggal,p.tanggal), COALESCE(j.faktur_pajak,p.faktur_pajak)";
            
            $rs = $base->dbQuery($sql);
            
            $first = true;
            while(!$rs->EOF)
            {
                if($spk_tmp != $rs->fields['kdspk'] || $first)
                {
                    $spk_tmp = $rs->fields['kdspk'];
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => 'Kode SPK',
                        'nobukti'   => "= " . $rs->fields['kdspk'],
                        'faktur_pajak_1'    => '',
                        'rupiah'    => '',
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => '',
                        'dpp'       => '',
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D; background:#EDEDED;',
                    ));
                    $tpl->parseConcatDynamic('row');
                }
                
                if($kdnasabah_tmp != $rs->fields['kdnasabah'] || $first)
                {
                    $kdnasabah_tmp = $rs->fields['kdnasabah'];
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => 'Kode Nasabah',
                        'nobukti'   => "= " . $rs->fields['kdnasabah'],
                        'faktur_pajak_1'    => '',
                        'rupiah'    => '',
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => '',
                        'dpp'       => '',
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D;',
                    ));
                    $tpl->parseConcatDynamic('row');
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => 'Nama Nasabah',
                        'nobukti'   => "= " . $rs->fields['nmnasabah'],
                        'faktur_pajak_1'    => '',
                        'rupiah'    => '',
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => '',
                        'dpp'       => '',
                        'addstyle'  => 'font-weight: bold',
                    ));
                    $tpl->parseConcatDynamic('row');
                }
                
                $tpl->assignDynamic('row', array(
                    'tanggal_1' => $rs->fields['tgl_1'],
                    'nobukti'   => $rs->fields['nobukti'],
                    'faktur_pajak_1'    => $rs->fields['faktur_pajak_1'],
                    'rupiah'    => ($rs->fields['faktur_pajak_1']=='')? '' : $this->format_money2($base, $rs->fields['rupiah']),
                    'npwp'      => $rs->fields['npwp'],
                    'faktur_pajak_2'    => $rs->fields['faktur_pajak_2'],
                    'tanggal_2' => $rs->fields['tgl_2'],
                    'ppn'       => ($rs->fields['faktur_pajak_2']=='')? '' : $this->format_money2($base, $rs->fields['ppn']),
                    'dpp'       => ($rs->fields['faktur_pajak_2']=='')? '' : $this->format_money2($base, $rs->fields['dpp']),
                    'addstyle'  => 'border-bottom: none',
                ));
                $tpl->parseConcatDynamic('row');
                
                $rupiah_spk += $rs->fields['rupiah'];
                $ppn_spk += $rs->fields['ppn'];
                $dpp_spk += $rs->fields['dpp'];
                
                $rupiah_nsb += $rs->fields['rupiah'];
                $ppn_nsb += $rs->fields['ppn'];
                $dpp_nsb += $rs->fields['dpp'];
                
                $cross = ($rs->fields['faktur_pajak_1'] == $rs->fields['faktur_pajak_2']) ? true : false; 
                $rupiah_cross += $cross ? $rs->fields['rupiah'] : 0;
                $ppn_cross += $cross ? $rs->fields['ppn'] : 0;
                $dpp_cross += $cross ? $rs->fields['dpp'] : 0;
                
                $rupiah_notcross += $cross ? 0 : $rs->fields['rupiah'];
                $ppn_notcross += $cross ?  0 : $rs->fields['ppn'];
                $dpp_notcross += $cross ?  0 : $rs->fields['dpp'];
                
                $first = false;
                $rs->moveNext();
                
                if($kdnasabah_tmp != $rs->fields['kdnasabah'])
                {
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => '',
                        'nobukti'   => '',
                        'faktur_pajak_1'    => 'Total Per Nasabah',
                        'rupiah'    => $this->format_money2($base, $rupiah_nsb),
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => $this->format_money2($base, $ppn_nsb),
                        'dpp'       => $this->format_money2($base, $dpp_nsb),
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D;',
                    ));
                    $tpl->parseConcatDynamic('row');
                    
                    $rupiah_nsb = 0;
                    $ppn_nsb = 0;
                    $dpp_nsb = 0;
                }
                
                if($spk_tmp != $rs->fields['kdspk'])
                {
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => '',
                        'nobukti'   => '',
                        'faktur_pajak_1'    => 'Total Per SPK',
                        'rupiah'    => $this->format_money2($base, $rupiah_spk),
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => $this->format_money2($base, $ppn_spk),
                        'dpp'       => $this->format_money2($base, $dpp_spk),
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D;',
                    ));
                    $tpl->parseConcatDynamic('row');
                    
                    $rupiah_spk = 0;
                    $ppn_spk = 0;
                    $dpp_spk = 0;
                    $first = true;
                }
                
                if(!$rs->fields)
                {
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => '',
                        'nobukti'   => '',
                        'faktur_pajak_1'    => 'Total Cross',
                        'rupiah'    => $this->format_money2($base, $rupiah_cross),
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => $this->format_money2($base, $ppn_cross),
                        'dpp'       => $this->format_money2($base, $dpp_cross),
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D;',
                    ));
                    $tpl->parseConcatDynamic('row');
                    
                    $tpl->assignDynamic('row', array(
                        'tanggal_1' => '',
                        'nobukti'   => '',
                        'faktur_pajak_1'    => 'Total Tidak Cross',
                        'rupiah'    => $this->format_money2($base, $rupiah_notcross),
                        'npwp'      => '',
                        'faktur_pajak_2'    => '',
                        'tanggal_2' => '',
                        'ppn'       => $this->format_money2($base, $ppn_notcross),
                        'dpp'       => $this->format_money2($base, $dpp_notcross),
                        'addstyle'  => 'font-weight: bold; border-top: 1px solid #4D4D4D;',
                    ));
                    $tpl->parseConcatDynamic('row');
                }
            }
            $dp = new dateparse();
            
            $record_static = array(
        		'VTHN'  	=> $ryear,
        		'VBLN'  	=> $nm_bulan_,
        		'SDATE' 	=> $startdate,
        		'EDATE' 	=> $enddate,
        		'DIVNAME'	=> $divname,
                // 'PERIODE'   => strtoupper($dp->monthnamelong[$rmonth]) . ' ' . $ryear,
                'PERIODE'   => date('F Y',strtotime("{$year_start}-{$month_start}-01")) .' - '. date('F Y',strtotime("{$year_end}-{$month_end}-01")) ,
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
        		'SID'     => MYSID,
        	); 
            $tpl->Assign($record_static);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
        }
        else if($this->get_var('is_excel')=='t')
        {
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
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
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci.html";
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' ,	$contents);
            
            $this->tpl = $tpl;
        }
        
    }/*}}}*/
    
    function sub_selisih_effisiensi($base)/*{{{*/
    {
        //$base->db= true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $kddiv = $this->S['curr_divisi'];
        
        $tpl = $base->_get_tpl('report_selisih_effisiensi.html');
        $tpl->defineDynamicBlock('row');
        
        $kddiv = $this->S['curr_divisi'];
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        
        $kdspk = $this->get_var('kdspk', $this->S['curr_proyek'] );
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $konsolidasi = $this->get_var('konsolidasi');
        $this->_fill_static_report($base,&$tpl);
        
        $table = "jurnal_".strtolower($this->S['curr_divisi']);    
        if($this->S['curr_proyek'] != '') $table = "jurnal";
		
        $ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        $is_proses = $this->get_var('is_proses');
        $kdreport = $this->get_var('kdreport');
        
        if($is_proses == 't')
        {
            $sql = "SELECT 	SUM(CASE WHEN COALESCE(mr.estimasi,'')=j.kdperkiraan THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS estimasi,
                    		SUM(CASE WHEN COALESCE(mr.realisasi,'')=j.kdperkiraan THEN (CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah ELSE 0 END) AS realisasi,
                            CASE WHEN date_trunc('month',j.tanggal) < '1-$rmonth-$ryear' AND date_part('year', j.tanggal)='$ryear' THEN 'sd_bln_lalu' 
                            WHEN date_trunc('month',j.tanggal) = '1-$rmonth-$ryear' THEN 'bln_ini'
                            WHEN date_part('year', j.tanggal) < '$ryear' THEN 'thn_lalu' END AS status,
                            mr.description,mr.h_d,mr.priority,mr.sum_type, mr.parent, mr.id, mr.estimasi as kdperkir_estimasi, mr.realisasi as kdperkir_realisasi,
                            CASE WHEN mr.h_d = 'T' THEN 'show' 
                                WHEN mr.priority = 0 AND (COALESCE(mr.estimasi,'')='' AND COALESCE(mr.realisasi,'')='') THEN 'none'
                            	WHEN mr.priority = 1 AND COALESCE(mr.estimasi,'')='' AND COALESCE(mr.realisasi,'')='' THEN 'none'
                                WHEN mr.h_d = 'H' AND mr.priority = 1 AND (COALESCE(mr.estimasi,'')='' OR COALESCE(mr.realisasi,'')='') THEN 'none'
                                WHEN COALESCE(mr.estimasi,'')='' THEN 'realisasi'
                                WHEN COALESCE(mr.realisasi,'')='' THEN 'estimasi'
                                ELSE 'show' END AS is_show
                    FROM mapping_report mr
                    	LEFT JOIN {$table} j ON ( 
                        	j.kdperkiraan IN (COALESCE(mr.estimasi,''), COALESCE(mr.realisasi,''))
                        	AND DATE_TRUNC('month', j.tanggal) <= '1-".$rmonth."-".$ryear."'
                        )
                    WHERE mr.report_type='selef'
                    GROUP BY mr.description,mr.h_d,mr.priority,mr.sum_type, mr.parent, mr.id, mr.orderby,status,kdperkir_estimasi,kdperkir_realisasi,is_show
                    ORDER BY mr.orderby";
            $rs = $base->dbQuery($sql);
            
            while(!$rs->EOF)
            {
                if(!isset($records[$rs->fields['id']])) $records[$rs->fields['id']] = $rs->fields;
                $records[$rs->fields['id']]['estimasi_'.$rs->fields['status']] = $rs->fields['estimasi'];
                $records[$rs->fields['id']]['realisasi_'.$rs->fields['status']] = $rs->fields['realisasi'];
                
                $records[$rs->fields['id']]['estimasi_sd_bln_ini'] = $records[$rs->fields['id']]['estimasi_sd_bln_lalu'] + $records[$rs->fields['id']]['estimasi_bln_ini'];
                $records[$rs->fields['id']]['realisasi_sd_bln_ini'] = $records[$rs->fields['id']]['realisasi_sd_bln_lalu'] + $records[$rs->fields['id']]['realisasi_bln_ini'];
                
                $records[$rs->fields['id']]['estimasi_sd_thn_ini'] = $records[$rs->fields['id']]['estimasi_sd_bln_ini'] + $records[$rs->fields['id']]['estimasi_thn_lalu'];
                $records[$rs->fields['id']]['realisasi_sd_thn_ini'] = $records[$rs->fields['id']]['realisasi_sd_bln_ini'] + $records[$rs->fields['id']]['realisasi_thn_lalu'];
                
                $rs->moveNext();
            }
            
            foreach($records as $record)
            {
                switch($record['priority'])
                {
                    case 0: $style = "font-weight: bold;";
                            $style2 = "font-weight: bold;"; break;
                    case 1: $style = "font-weight: bold; padding-left: 20px;"; 
                            $style2 = "font-weight: bold;";break;
                    case 2: $style = "padding-left: 40px;"; 
                            $style2 = ""; break;
                    default: $style = ""; $style2 = "";
                }
                if ($record['h_d'] == 'T') $style2 .= " line-height: 28px;";
                
                
                $arrs = array('sd_bln_lalu', 'bln_ini', 'sd_bln_ini', 'sd_thn_ini');
                
                foreach($arrs as $arr)
                {
                    $records[$record['parent']]['realisasi_'.$arr] += $record['realisasi_'.$arr];
                    $records[$record['parent']]['estimasi_'.$arr] += $record['estimasi_'.$arr];
                }
                
                if($record['h_d'] == 'T' && $record['sum_type'] == '')
                {
                    foreach($arrs as $arr)
                    {
                        $record['realisasi_'.$arr] = $records[$record['parent']]['realisasi_'.$arr];
                        $record['estimasi_'.$arr] = $records[$record['parent']]['estimasi_'.$arr];
                        
                        $records[$record['id']]['realisasi_'.$arr] = $records[$record['parent']]['realisasi_'.$arr];
                        $records[$record['id']]['estimasi_'.$arr] = $records[$record['parent']]['estimasi_'.$arr];
                        
                        $root_parent = $records[$record['parent']]['parent'];
                        $records[$root_parent]['realisasi_'.$arr] += $record['realisasi_'.$arr];
                        $records[$root_parent]['estimasi_'.$arr] += $record['estimasi_'.$arr];
                    }
                }
                
                if($record['sum_type'] != '')
                {
                    $arr_sumtypes = explode(",", $record['sum_type']);
                    
                    foreach($arr_sumtypes as  $v)
                    {
                        foreach($arrs as $vv)
                        {
                            $record['realisasi_'.$vv] += $records[$v]['realisasi_'.$vv];
                            $record['estimasi_'.$vv] += $records[$v]['estimasi_'.$vv];
                            
                            $records[$record['id']]['realisasi_'.$vv] += $records[$v]['realisasi_'.$vv];
                            $records[$record['id']]['estimasi_'.$vv] += $records[$v]['estimasi_'.$vv];
                        }
                    }
                }
                
                $tpl->assignDynamic('row', array(
                    'description'   => $record['description'],
                    'style'         => $style,
                    'style2'         => $style2,
                    'realisasi_sd_bln_lalu' => (in_array($record['is_show'], array('realisasi', 'show')) ) ? $this->format_money2($base, $record['realisasi_sd_bln_lalu']) : '',
                    'estimasi_sd_bln_lalu' => (in_array($record['is_show'], array('estimasi', 'show'))) ? $this->format_money2($base, $record['estimasi_sd_bln_lalu']) : '',
                    
                    'realisasi_bln_ini' => (in_array($record['is_show'], array('realisasi', 'show'))) ? $this->format_money2($base, $record['realisasi_bln_ini']) : '',
                    'estimasi_bln_ini' => (in_array($record['is_show'], array('estimasi', 'show'))) ? $this->format_money2($base, $record['estimasi_bln_ini']) : '',
                    
                    'realisasi_sd_bln_ini' => (in_array($record['is_show'], array('realisasi', 'show'))) ? $this->format_money2($base, $record['realisasi_sd_bln_ini']) : '',
                    'estimasi_sd_bln_ini' => (in_array($record['is_show'], array('estimasi', 'show'))) ? $this->format_money2($base, $record['estimasi_sd_bln_ini']) : '',
                    'selisih_sd_bln_ini' => (in_array($record['is_show'], array('show'))) ? $this->format_money2($base, $record['estimasi_sd_bln_ini']-$record['realisasi_sd_bln_ini']) : '',
                    
                    'realisasi_sd_thn_ini' => (in_array($record['is_show'], array('realisasi', 'show'))) ? $this->format_money2($base, $record['realisasi_sd_thn_ini']) : '',
                    'estimasi_sd_thn_ini' => (in_array($record['is_show'], array('estimasi', 'show'))) ? $this->format_money2($base, $record['estimasi_sd_thn_ini']) : '',
                    'selisih_sd_thn_ini' => (in_array($record['is_show'], array('show'))) ? $this->format_money2($base, $record['estimasi_sd_thn_ini']-$record['realisasi_sd_thn_ini']) : '',
                ));
                $tpl->parseConcatDynamic('row');
            }
            
            $dp = new dateparse();
            
            $record_static = array(
        		'VTHN'  	=> $ryear,
        		'VBLN'  	=> $nm_bulan_,
        		'SDATE' 	=> $startdate,
        		'EDATE' 	=> $enddate,
        		'DIVNAME'	=> $divname,
                'PERIODE'   => strtoupper($dp->monthnamelong[$rmonth]) . ' ' . $ryear,
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
        		'SID'     => MYSID,
        	); 
            $tpl->Assign($record_static);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_rinci_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_rinci.html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
        }
        else if($this->get_var('is_excel')=='t')
        {
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci_for_excel.html";
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
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_rinci.html";
            
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' ,	$contents);
            
            $this->tpl = $tpl;
        }
        
    }/*}}}*/
    
    function sub_rari_persbdaya($base)/*{{{*/
    {//$base->db= true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $kddiv = $this->S['curr_divisi'];
        $tbtype = $this->get_var('tbtype', 'rinci');
        
        if($tbtype == 'group')
        {
            $tpl = $base->_get_tpl('rari_persbdaya.html');
            $tpl1 =& $tpl->defineDynamicBlock('row');
            $tpl2 =& $tpl1->defineDynamicBlock('row_d');    
        }
        else
        {
            $tpl = $base->_get_tpl('rari_persbdaya_rinci.html');
            $tpl1 =& $tpl->defineDynamicBlock('row');
            $tpl2 =& $tpl1->defineDynamicBlock('row_sub');
            $tpl3 =& $tpl2->defineDynamicBlock('row_d'); 
        }
        
        
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk', $this->S['curr_proyek']);
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $konsolidasi = $this->get_var('konsolidasi');
        $this->_fill_static_report($base,&$tpl);
        
        $table = "jurnal_".strtolower($this->S['curr_divisi']);    
        if($this->S['curr_proyek'] != '') $table = "jurnal";
		
        $ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        $is_proses = $this->get_var('is_proses');
        $kdreport = $this->get_var('kdreport');
        
        if($is_proses == 't')
        {
            if($tbtype == 'rinci')
            {
                $sql = "SELECT * FROM (
                        SELECT COALESCE(a.kdsbdaya, b.kdsbdaya) as kdsbdaya, sd.nmsbdaya 
                            , COALESCE(a.kdtahap, b.kdtahap) as kdtahap, dt.nmtahap, dt.sttahap
                        	, harga_rab_pelaksanaan, vol_rab_pelaksanaan
                            , harga_rab_rolling, vol_rab_rolling
                            , harga_realisasi, vol_realisasi
                            , 'bl' as rab_type
                        FROM 
                        	(
                        	SELECT a.kdsbdaya, a.kdtahap
                            	, SUM(a.vol_awal)*b.harga_awal as harga_rab_pelaksanaan
                                , SUM(a.vol_awal) as vol_rab_pelaksanaan
                            	, SUM(a.vol_rolling)*b.harga_rolling as harga_rab_rolling
                                , SUM(a.vol_rolling) as vol_rab_rolling
                            FROM rab_bl a, harga_sbdaya b
                            WHERE a.kdsbdaya=b.kdsbdaya
                            GROUP BY a.kdsbdaya, a.kdtahap, b.harga_awal, b.harga_rolling
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdsbdaya, j.kdtahap
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.volume) as vol_realisasi
                            FROM {$table} j 
                            WHERE COALESCE(j.kdsbdaya,'')<>'' AND COALESCE(j.kdtahap,'')<>''
                                AND DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '411%'
                            GROUP BY j.kdsbdaya, j.kdtahap
                            ) b ON ( b.kdsbdaya=a.kdsbdaya )
                         LEFT JOIN dsbdaya sd ON ( COALESCE(a.kdsbdaya, b.kdsbdaya)=sd.kdsbdaya )
                         LEFT JOIN dtahap dt ON ( COALESCE(a.kdtahap, b.kdtahap)=dt.kdtahap )
                        
                        UNION ALL
                        
                        SELECT dp.kdperkiraan as kdsbdaya, NULL as nmsbdaya
                            , dp.kdperkiraan as kdtahap, NULL as nmtahap, NULL as sttahap
                        	, harga_rab_pelaksanaan, NULL
                            , harga_rab_rolling, NULL
                            , harga_realisasi, NULL
                            , 'btl' as rab_type
                        FROM dperkir dp,
                        	(
                        	SELECT a.kdperkiraan
                            	, SUM(a.harga_awal) as harga_rab_pelaksanaan
                            	, SUM(a.harga_rolling) as harga_rab_rolling
                            FROM rab_btl a
                            GROUP BY a.kdperkiraan
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdperkiraan
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            FROM {$table} j 
                            WHERE DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '481%'
                            GROUP BY j.kdperkiraan
                            ) b ON ( b.kdperkiraan=a.kdperkiraan)
                        WHERE COALESCE(a.kdperkiraan, b.kdperkiraan)=dp.kdperkiraan
                        ) a ORDER BY rab_type, kdsbdaya";
                $rs = $base->dbQuery($sql);
            }
            else
            {
                $sql = "SELECT * FROM (
                        SELECT COALESCE(a.kdsbdaya, b.kdsbdaya) as kdsbdaya, sd.nmsbdaya , sd.stsbdaya
                        	, harga_rab_pelaksanaan, vol_rab_pelaksanaan
                            , harga_rab_rolling, vol_rab_rolling
                            , harga_realisasi, vol_realisasi
                            , 'bl' as rab_type
                        FROM 
                        	(
                        	SELECT a.kdsbdaya
                            	, SUM(a.vol_awal)*b.harga_awal as harga_rab_pelaksanaan
                                , SUM(a.vol_awal) as vol_rab_pelaksanaan
                            	, SUM(a.vol_rolling)*b.harga_rolling as harga_rab_rolling
                                , SUM(a.vol_rolling) as vol_rab_rolling
                            FROM rab_bl a, harga_sbdaya b
                            WHERE a.kdsbdaya=b.kdsbdaya
                            GROUP BY a.kdsbdaya, b.harga_awal, b.harga_rolling
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdsbdaya
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.volume) as vol_realisasi
                            FROM {$table} j 
                            WHERE COALESCE(j.kdsbdaya,'')<>'' AND COALESCE(j.kdtahap,'')<>''
                                AND DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '411%'
                            GROUP BY j.kdsbdaya
                            ) b ON ( b.kdsbdaya=a.kdsbdaya )
                         LEFT JOIN dsbdaya sd ON ( COALESCE(a.kdsbdaya, b.kdsbdaya)=sd.kdsbdaya )
                        
                        UNION ALL
                        
                        SELECT dp.kdperkiraan, NULL , NULL
                        	, harga_rab_pelaksanaan, NULL
                            , harga_rab_rolling, NULL
                            , harga_realisasi, NULL
                            , 'btl' as rab_type
                        FROM dperkir dp,
                        	(
                        	SELECT a.kdperkiraan
                            	, SUM(a.harga_awal) as harga_rab_pelaksanaan
                            	, SUM(a.harga_rolling) as harga_rab_rolling
                            FROM rab_btl a
                            GROUP BY a.kdperkiraan
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdperkiraan
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            FROM {$table} j 
                            WHERE DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '481%'
                            GROUP BY j.kdperkiraan
                            ) b ON ( b.kdperkiraan=a.kdperkiraan)
                        WHERE COALESCE(a.kdperkiraan, b.kdperkiraan)=dp.kdperkiraan
                        ) a ORDER BY rab_type, kdsbdaya";
                $rs = $base->dbQuery($sql);
            }
            
            
            $first=true;
            while(!$rs->EOF)
            {
                $rab_type = $rs->fields['rab_type'];
                $kdsbdaya = $rs->fields['kdsbdaya'];
                $nmsbdaya = $rs->fields['nmsbdaya'];
                $vol_selisih = $rs->fields['vol_rab_rolling']-$rs->fields['vol_realisasi'];
                $harga_selisih = $rs->fields['harga_rab_rolling']-$rs->fields['harga_realisasi'];
                
                $tprow_d = ($tbtype == 'rinci') ? $tpl2 : $tpl1;
                $tprow_d->assignDynamic('row_d', array(
                    'kdtahap'  => $rs->fields['kdtahap'],
                    'nmtahap'  => $rs->fields['nmtahap'],
                    'kdsbdaya'  => $rs->fields['kdsbdaya'],
                    'nmsbdaya'  => $rs->fields['nmsbdaya'],
                    'satuan'    => ($tbtype == 'rinci') ? $rs->fields['sttahap'] : $rs->fields['stsbdaya'],
                    'vol_rab_pelaksanaan'   => $this->format_money2($base, $rs->fields['vol_rab_pelaksanaan'], 4),
                    'harga_rab_pelaksanaan'   => $this->format_money2($base, $rs->fields['harga_rab_pelaksanaan']),
                    'vol_rab_rolling'   => $this->format_money2($base, $rs->fields['vol_rab_rolling'], 4),
                    'harga_rab_rolling'   => $this->format_money2($base, $rs->fields['harga_rab_rolling']),
                    'vol_realisasi'   => $this->format_money2($base, $rs->fields['vol_realisasi'], 4),
                    'harga_realisasi'   => $this->format_money2($base, $rs->fields['harga_realisasi']),
                    'vol_selisih'   => $this->format_money2($base, $vol_selisih, 4),
                    'harga_selisih'   => $this->format_money2($base, $harga_selisih),
                ));
                $tprow_d->parseConcatDynamic('row_d');
                
                $sub_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $sub_rab_rolling += $rs->fields['harga_rab_rolling'];
                $sub_realisasi += $rs->fields['harga_realisasi'];
                $sub_selisih += $harga_selisih;
                
                $total_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $total_rab_rolling += $rs->fields['harga_rab_rolling'];
                $total_realisasi += $rs->fields['harga_realisasi'];
                $total_selisih += $harga_selisih;
                
                $all_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $all_rab_rolling += $rs->fields['harga_rab_rolling'];
                $all_realisasi += $rs->fields['harga_realisasi'];
                $all_selisih += $harga_selisih;
                
                $rs->moveNext();
                
                if($tbtype == 'rinci')
                {
                    if($kdsbdaya != $rs->fields['kdsbdaya'])
                    { 
                        $tpl1->assignDynamic('row_sub', array(
                            'kdsbdaya'  => $kdsbdaya,
                            'nmsbdaya'  => $nmsbdaya,
                            'sub_rab_pelaksanaan' => $this->format_money2($base, $sub_rab_pelaksanaan),
                            'sub_rab_rolling' => $this->format_money2($base, $sub_rab_rolling),
                            'sub_realisasi'   => $this->format_money2($base, $sub_realisasi),
                            'sub_selisih' => $this->format_money2($base, $sub_selisih),
                        ));
                        $tpl1->parseConcatDynamic('row_sub');
                        $tpl3->emptyParsedPage();
                        
                        $sub_rab_pelaksanaan = 0;
                        $sub_rab_rolling = 0;
                        $sub_realisasi = 0;
                        $sub_selisih = 0;
                    }    
                }
                
                if($rab_type != $rs->fields['rab_type'])
                {
                    $tpl->assignDynamic('row', array(
                        'rab_title' => ($rab_type == 'bl') ? 'BIAYA LANGSUNG' : 'BIAYA TAK LANGSUNG',
                        'total_rab_pelaksanaan' => $this->format_money2($base, $total_rab_pelaksanaan),
                        'total_rab_rolling' => $this->format_money2($base, $total_rab_rolling),
                        'total_realisasi'   => $this->format_money2($base, $total_realisasi),
                        'total_selisih' => $this->format_money2($base, $total_selisih),
                    ));
                    $tpl->parseConcatDynamic('row');
                    
                    if($tbtype == 'rinci')
                    {
                        $tpl2->emptyParsedPage();
                        $tpl3->emptyParsedPage();
                    }
                    else
                        $tpl2->emptyParsedPage();
                    
                    $total_rab_pelaksanaan = 0;
                    $total_rab_rolling = 0;
                    $total_realisasi = 0;
                    $total_selisih = 0;
                }
            }
            
            $tpl->assign(array(
                'all_rab_pelaksanaan' => $this->format_money2($base, $all_rab_pelaksanaan),
                'all_rab_rolling' => $this->format_money2($base, $all_rab_rolling),
                'all_realisasi'   => $this->format_money2($base, $all_realisasi),
                'all_selisih' => $this->format_money2($base, $all_selisih),
            ));
                
            
            $dp = new dateparse();
            
            $record_static = array(
        		'VTHN'  	=> $ryear,
        		'VBLN'  	=> $nm_bulan_,
        		'SDATE' 	=> $startdate,
        		'EDATE' 	=> $enddate,
        		'DIVNAME'	=> $divname,
                'PERIODE'   => strtoupper($dp->monthnamelong[$rmonth]) . ' ' . $ryear,
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
        		'SID'     => MYSID,
        	); 
            $tpl->Assign($record_static);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_".$tbtype."_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_".$tbtype.".html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
        }
        else if($this->get_var('is_excel')=='t')
        {
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_".$tbtype."_for_excel.html";
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
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_".$tbtype.".html";
            
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' ,	$contents);
            
            $this->tpl = $tpl;
        }
    }/*}}}*/
    
    function sub_rari_pertahap($base)/*{{{*/
    {//$base->db= true;
        $month = $this->get_var('rmonth',date('m'));
        $year = $this->get_var('ryear',date('Y'));
        $kddiv = $this->S['curr_divisi'];
        $tbtype = $this->get_var('tbtype', 'rinci');
        
        if($tbtype == 'group')
        {
            $tpl = $base->_get_tpl('rari_pertahap.html');
            $tpl1 =& $tpl->defineDynamicBlock('row');
            $tpl2 =& $tpl1->defineDynamicBlock('row_d');    
        }
        else
        {
            $tpl = $base->_get_tpl('rari_pertahap_rinci.html');
            $tpl1 =& $tpl->defineDynamicBlock('row');
            $tpl2 =& $tpl1->defineDynamicBlock('row_sub');
            $tpl3 =& $tpl2->defineDynamicBlock('row_d'); 
        }
        
		$divname = $base->dbGetOne("SELECT nmdivisi FROM ddivisi WHERE kddivisi= '{$kddiv}' ");
        $kdspk = $this->get_var('kdspk',$this->S['curr_proyek']);
        $nmspk = $base->dbGetOne("SELECT nmspk FROM dspk WHERE kdspk='".$kdspk."' AND kddiv='".$kddiv."'");
        $konsolidasi = $this->get_var('konsolidasi');
        $this->_fill_static_report($base,&$tpl);
        
        $table = "jurnal_".strtolower($this->S['curr_divisi']);    
        if($this->S['curr_proyek'] != '') $table = "jurnal";
		
        $ryear = $this->get_var('ryear',date('Y'));
		$rmonth = $this->get_var('rmonth',date('m'));
        $is_proses = $this->get_var('is_proses');
        $kdreport = $this->get_var('kdreport');
        
        if($is_proses == 't')
        {
            if($tbtype == 'rinci')
            {
                $sql = "SELECT * FROM (
                        SELECT COALESCE(a.kdtahap, b.kdtahap) as kdtahap, dt.nmtahap
                            , COALESCE(a.kdsbdaya, b.kdsbdaya)as kdsbdaya, ds.nmsbdaya, ds.stsbdaya
                        	, harga_rab_pelaksanaan, vol_rab_pelaksanaan
                            , harga_rab_rolling, vol_rab_rolling
                            , harga_realisasi, vol_realisasi
                            , 'bl' as rab_type
                        FROM 
                        	(
                        	SELECT a.kdtahap, a.kdsbdaya
                            	, SUM(a.vol_awal)*b.harga_awal as harga_rab_pelaksanaan
                                , SUM(a.vol_awal) as vol_rab_pelaksanaan
                            	, SUM(a.vol_rolling)*b.harga_rolling as harga_rab_rolling
                                , SUM(a.vol_rolling) as vol_rab_rolling
                            FROM rab_bl a, harga_sbdaya b
                            WHERE a.kdsbdaya=b.kdsbdaya
                            GROUP BY a.kdtahap, a.kdsbdaya, b.harga_awal, b.harga_rolling
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdtahap, j.kdsbdaya
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.volume) as vol_realisasi
                            FROM {$table} j 
                            WHERE COALESCE(j.kdsbdaya,'')<>'' AND COALESCE(j.kdtahap,'')<>''
                                AND DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '411%'
                            GROUP BY j.kdtahap, j.kdsbdaya
                            ) b ON ( b.kdtahap=a.kdtahap )
                         LEFT JOIN dtahap dt ON ( COALESCE(a.kdtahap, b.kdtahap)=dt.kdtahap )
                         LEFT JOIN dsbdaya ds ON ( COALESCE(a.kdsbdaya, b.kdsbdaya)=ds.kdsbdaya )
                        
                        UNION ALL
                        
                        SELECT dp.kdperkiraan, dp.nmperkiraan 
                            , COALESCE(a.kdsbdaya, b.kdsbdaya) as kdsbdaya, ds.nmsbdaya, ds.stsbdaya
                        	, harga_rab_pelaksanaan, NULL
                            , harga_rab_rolling, NULL
                            , harga_realisasi, NULL
                            , 'btl' as rab_type
                        FROM dperkir dp,
                        	(
                        	SELECT a.kdperkiraan, a.kdsbdaya
                            	, SUM(a.harga_awal) as harga_rab_pelaksanaan
                            	, SUM(a.harga_rolling) as harga_rab_rolling
                            FROM rab_btl a
                            GROUP BY a.kdperkiraan, a.kdsbdaya
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdperkiraan, j.kdsbdaya
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            FROM {$table} j 
                            WHERE DATE_TRUNC('month', j.tanggal) = '1-".$rmonth."-".$ryear."'
                                AND j.kdperkiraan LIKE '481%'
                            GROUP BY j.kdperkiraan, j.kdsbdaya
                            ) b ON ( b.kdperkiraan=a.kdperkiraan)
                         LEFT JOIN dsbdaya ds ON ( COALESCE(a.kdsbdaya, b.kdsbdaya)=ds.kdsbdaya )
                        WHERE COALESCE(a.kdperkiraan, b.kdperkiraan)=dp.kdperkiraan
                        ) a ORDER BY rab_type,kdtahap
                        ";
            }
            else
            {
                $sql = "SELECT * FROM (SELECT COALESCE(a.kdtahap, b.kdtahap) as kdtahap, dt.nmtahap, dt.sttahap
                        	, harga_rab_pelaksanaan, vol_rab_pelaksanaan
                            , harga_rab_rolling, vol_rab_rolling
                            , harga_realisasi, vol_realisasi
                            , 'bl' as rab_type
                        FROM 
                        	(
                        	SELECT a.kdtahap
                            	, SUM(a.vol_awal)*b.harga_awal as harga_rab_pelaksanaan
                                , SUM(a.vol_awal) as vol_rab_pelaksanaan
                            	, SUM(a.vol_rolling)*b.harga_rolling as harga_rab_rolling
                                , SUM(a.vol_rolling) as vol_rab_rolling
                            FROM rab_bl a, harga_sbdaya b
                            WHERE a.kdsbdaya=b.kdsbdaya
                            GROUP BY a.kdtahap, b.harga_awal, b.harga_rolling
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdtahap
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.volume) as vol_realisasi
                            FROM {$table} j 
                            WHERE COALESCE(j.kdsbdaya,'')<>'' AND COALESCE(j.kdtahap,'')<>''
                                AND DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '411%'
                            GROUP BY j.kdtahap
                            ) b ON ( b.kdtahap=a.kdtahap )
                         LEFT JOIN dtahap dt ON ( COALESCE(a.kdtahap, b.kdtahap)=dt.kdtahap )
                         --ORDER BY COALESCE(a.kdtahap, b.kdtahap)
                        
                        UNION ALL
                        
                        SELECT dp.kdperkiraan, dp.nmperkiraan , NULL
                        	, harga_rab_pelaksanaan, NULL
                            , harga_rab_rolling, NULL
                            , harga_realisasi, NULL
                            , 'btl' as rab_type
                        FROM dperkir dp,
                        	(
                        	SELECT a.kdperkiraan
                            	, SUM(a.harga_awal) as harga_rab_pelaksanaan
                            	, SUM(a.harga_rolling) as harga_rab_rolling
                            FROM rab_btl a
                            GROUP BY a.kdperkiraan
                            ) a
                        RIGHT JOIN
                        	(
                            SELECT j.kdperkiraan
                            , SUM((CASE WHEN j.dk='D' THEN 1 ELSE -1 END)*j.rupiah) as harga_realisasi
                            FROM {$table} j 
                            WHERE DATE_TRUNC('month', j.tanggal) = '1-$rmonth-$ryear'
                                AND j.kdperkiraan LIKE '481%'
                            GROUP BY j.kdperkiraan
                            ) b ON ( b.kdperkiraan=a.kdperkiraan)
                        WHERE COALESCE(a.kdperkiraan, b.kdperkiraan)=dp.kdperkiraan
                        ) a ORDER BY rab_type,kdtahap
                        ";    
            }
            
            $rs = $base->dbQuery($sql);
            
            $first=true;
            while(!$rs->EOF)
            {
                $rab_type = $rs->fields['rab_type'];
                $kdtahap = $rs->fields['kdtahap'];
                $nmtahap = $rs->fields['nmtahap'];
                $vol_selisih = $rs->fields['vol_rab_rolling']-$rs->fields['vol_realisasi'];
                $harga_selisih = $rs->fields['harga_rab_rolling']-$rs->fields['harga_realisasi'];
                
                $tprow_d = ($tbtype == 'rinci') ? $tpl2 : $tpl1;
                $tprow_d->assignDynamic('row_d', array(
                    'kdtahap'  => $rs->fields['kdtahap'],
                    'nmtahap'  => $rs->fields['nmtahap'],
                    'kdsbdaya'  => $rs->fields['kdsbdaya'],
                    'nmsbdaya'  => $rs->fields['nmsbdaya'],
                    'satuan'    => ($tbtype == 'rinci') ? $rs->fields['stsbdaya']:$rs->fields['sttahap'],
                    'vol_rab_pelaksanaan'   => $this->format_money2($base, $rs->fields['vol_rab_pelaksanaan'], 4),
                    'harga_rab_pelaksanaan'   => $this->format_money2($base, $rs->fields['harga_rab_pelaksanaan']),
                    'vol_rab_rolling'   => $this->format_money2($base, $rs->fields['vol_rab_rolling'], 4),
                    'harga_rab_rolling'   => $this->format_money2($base, $rs->fields['harga_rab_rolling']),
                    'vol_realisasi'   => $this->format_money2($base, $rs->fields['vol_realisasi'], 4),
                    'harga_realisasi'   => $this->format_money2($base, $rs->fields['harga_realisasi']),
                    'vol_selisih'   => $this->format_money2($base, $vol_selisih, 4),
                    'harga_selisih'   => $this->format_money2($base, $harga_selisih),
                ));
                $tprow_d->parseConcatDynamic('row_d');
                
                $sub_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $sub_rab_rolling += $rs->fields['harga_rab_rolling'];
                $sub_realisasi += $rs->fields['harga_realisasi'];
                $sub_selisih += $harga_selisih;
                
                $total_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $total_rab_rolling += $rs->fields['harga_rab_rolling'];
                $total_realisasi += $rs->fields['harga_realisasi'];
                $total_selisih += $harga_selisih;
                
                $all_rab_pelaksanaan += $rs->fields['harga_rab_pelaksanaan'];
                $all_rab_rolling += $rs->fields['harga_rab_rolling'];
                $all_realisasi += $rs->fields['harga_realisasi'];
                $all_selisih += $harga_selisih;
                
                $rs->moveNext();
                
                if($tbtype == 'rinci')
                {
                    if($kdtahap != $rs->fields['kdtahap'])
                    { 
                        $tpl1->assignDynamic('row_sub', array(
                            'kdtahap'  => $kdtahap,
                            'nmtahap'  => $nmtahap,
                            'sub_rab_pelaksanaan' => $this->format_money2($base, $sub_rab_pelaksanaan),
                            'sub_rab_rolling' => $this->format_money2($base, $sub_rab_rolling),
                            'sub_realisasi'   => $this->format_money2($base, $sub_realisasi),
                            'sub_selisih' => $this->format_money2($base, $sub_selisih),
                        ));
                        $tpl1->parseConcatDynamic('row_sub');
                        $tpl3->emptyParsedPage();
                        
                        $sub_rab_pelaksanaan = 0;
                        $sub_rab_rolling = 0;
                        $sub_realisasi = 0;
                        $sub_selisih = 0;
                    }    
                }
                
                if($rab_type != $rs->fields['rab_type'])
                {
                    $tpl->assignDynamic('row', array(
                        'rab_title' => ($rab_type == 'bl') ? 'BIAYA LANGSUNG' : 'BIAYA TAK LANGSUNG',
                        'total_rab_pelaksanaan' => $this->format_money2($base, $total_rab_pelaksanaan),
                        'total_rab_rolling' => $this->format_money2($base, $total_rab_rolling),
                        'total_realisasi'   => $this->format_money2($base, $total_realisasi),
                        'total_selisih' => $this->format_money2($base, $total_selisih),
                    ));
                    $tpl->parseConcatDynamic('row');
                    if($tbtype == 'rinci')
                    {
                        $tpl2->emptyParsedPage();
                        $tpl3->emptyParsedPage();
                    }
                    else
                        $tpl2->emptyParsedPage();
                    
                    $total_rab_pelaksanaan = 0;
                    $total_rab_rolling = 0;
                    $total_realisasi = 0;
                    $total_selisih = 0;
                }
            }
            
            $tpl->assign(array(
                'all_rab_pelaksanaan' => $this->format_money2($base, $all_rab_pelaksanaan),
                'all_rab_rolling' => $this->format_money2($base, $all_rab_rolling),
                'all_realisasi'   => $this->format_money2($base, $all_realisasi),
                'all_selisih' => $this->format_money2($base, $all_selisih),
            ));
                
            
            $dp = new dateparse();
            
            $record_static = array(
        		'VTHN'  	=> $ryear,
        		'VBLN'  	=> $nm_bulan_,
        		'SDATE' 	=> $startdate,
        		'EDATE' 	=> $enddate,
        		'DIVNAME'	=> $divname,
                'PERIODE'   => strtoupper($dp->monthnamelong[$rmonth]) . ' ' . $ryear,
                'KDSPK' => $kdspk,
                'NMSPK' => $nmspk,
        		'SID'     => MYSID,
        	); 
            $tpl->Assign($record_static);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_".$tbtype."_for_excel.html";
            $isi_excel = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi_excel);
            
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$year."_".$month."_".$tbtype.".html";
            $isi = & $tpl->parsedPage();
    		$this->cetak_to_file($base,$filename,$isi);
            
            $this->tpl = $tpl;
        }
        else if($this->get_var('is_excel')=='t')
        {
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_".$tbtype."_for_excel.html";
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
            $filename = $base->kcfg['basedir']."files/".$kdreport."_".$kddiv."_".$ryear."_".$rmonth."_".$tbtype.".html";
            
            $fp = @fopen($filename,"r"); 
            if (!$fp) 
            	die("The file does not exists!");
            	
            $contents = fread ($fp, filesize ($filename));
            fclose ($fp);
            
            $tpl = $base->_get_tpl('one_var.html');
            $tpl->assign('ONE' ,	$contents);
            
            $this->tpl = $tpl;
        }
    }/*}}}*/
	
	//sub external report lama
	/**
	function sub_external_report($base)
	{
		$base_url = $base->kcfg['url'] .'ci/index.php/';
		$subtype = $this->get_var('subtype');
		$ukerr="";
    $addsqlwil = '';
		if ($subtype === 'antar_divisi_new')
		{
			$title = 'Laporan : Rekonsiliasi Hub R/K (Antar Departemen)';
			$form_action = $base_url .'report_rk/antar_divisi_new';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_konsolidasi.html');
		}
		elseif ($subtype === 'divisi_proyek') //sebetulnya dept - proyek
		{
			$title = 'Rekon Hub R/K Departemen-Proyek';
			$form_action = $base_url .'report_rk/divisi_proyek';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_spl.html');
		}
		elseif ($subtype === 'wilayah_proyek') //wilayah atau divisi - proyek COA 2110112
		{
      if ($this->S['curr_wil'] == '')
      {
        die('Untuk melihat report ini, Anda harus memilih divisi dulu !');
      }
			$title = 'Rekon Hub R/K Divisi-Proyek';
			$form_action = $base_url .'report_rk/divisi_proyek';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_wilayah_spl.html');
      $tpl->Assign('WILAYAH_ID', $this->S['curr_wil']);
      $addsqlwil = " AND kodewilayah='{$this->S['curr_wil']}'";
		}
		elseif ($subtype === 'rk_jo')
		{
			$title = 'Rekon Hub R/K JO (beta)';
			$form_action = $base_url .'report_rk/divisi_proyek_jo';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_jo.html');
		}
		elseif ($subtype === 'neraca_t')
		{
		//$base->db= true;
			$title = 'Posisi Keuangan';
			$form_action = $base_url .'neraca/neraca_t';
			$tpl = $base -> _get_tpl('accounting_report_form_neraca_t.html');
		//	$base->db->debug=true;
			//print_r($this->S);
      			$sql_wil = $this->S['userdata']->get_sql_wilayah($base, $this->S['curr_divisi']);
			
			 if ($this->S['curr_wil'] != '')
                		{
                        	 $sql_wil .= " AND kdspk='{$this->S['curr_wil']}'";
				 $sql = "SELECT nmspk,kdspk FROM dspk where iswilayah='t' AND kddiv='{$this->S['curr_divisi']}' $sql_wil ORDER BY kdspk";
				 $rs = $base->dbquery($sql);
	                         $wil = $rs->getMenu2('data[kdwilayah]','',false,false,0,' id="data[kdwilayah]"');
                		}
			 else
				{
				 $sql = "SELECT nmspk,kdspk FROM dspk where iswilayah='t' AND kddiv='{$this->S['curr_divisi']}' $sql_wil ORDER BY kdspk";
                                 $rs = $base->dbquery($sql);
				 $wil = $rs->getMenu2('data[kdwilayah]',$this->S['curr_wil'],$this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']),false,0,' id="data[kdwilayah]"');	
				}
			
//	$base->db->debug=true;
      $tpl->Assign('WILAYAH', $wil);
		}
		elseif ($subtype === 'biayausaha_rinci')
		{
			$title = "";
			$form_action = $base_url . 'biayausaha/rincian';
			$tpl = $base -> _get_tpl('biayausaha_report_form.html');
			$rsT = $base-> dbQuery("SELECT a.kduker,singkatan FROM anggar a LEFT JOIN z_groupname_bius b ON b.kddivisi = a.kddivisi and b.kduker = a.kduker
									 WHERE a.kddivisi='{$this->S['curr_divisi']}' and a.tahun = '2013' 
									GROUP BY a.kduker,a.kddivisi,singkatan ORDER BY a.kduker");
			while(!$rsT->EOF)
            {
                $ukerr .="<option value = '".$rsT->fields['kduker']."'>".$rsT->fields['kduker']." - ".$rsT->fields['singkatan']."</option>\r\n";
                $rsT->moveNext();
            }
		}
		elseif($subtype === 'labarugi')
		{
			$title = "Laba Rugi Divisi";
			$form_action = $base_url.'labarugi/divisi';
			$tpl = $base -> _get_tpl('labarugi_form.html');
      //print_r($this->S['userdata']->wilayah_permission);
      $sql_wil = $this->S['userdata']->get_sql_wilayah($base, $this->S['curr_divisi']);
      if ($this->S['curr_wil'] != '')
      {
        $sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
      }
      $sql = "SELECT nmspk,kdspk FROM dspk where iswilayah='t' AND kddiv='{$this->S['curr_divisi']}' AND kodewilayah=kdspk $sql_wil ORDER BY kdspk";
      $rs = $base->dbquery($sql);
      $wil = $rs->getMenu2('data[kdwilayah]',$this->S['curr_wil'],$this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']));
      $tpl->Assign('WILAYAH',$wil);
		}
		elseif($subtype === 'neraca_lajur')
		{
      if (!$this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']))
      {
        die("AKSES DITOLAK. Anda tidak memiliki akses ke data departemen.");
      }
			$title = "Neraca Lajur Konsolidasi";
			$form_action = $base_url.'neraca/konsolidasi';
			$tpl = $base -> _get_tpl('neraca_lajur_form.html');
		}
		elseif($subtype === 'rekon_pajak_konsolidasi')
		{
			$title = 'Rekon Pajak Konsolidasi';
			$form_action = $base_url .'rekon_pajak/rekon';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rekonpajak_konsolidasi.html');
		}
		
		$dp = new dateparse;
		$m = $this -> get_var('m', date('m'));
		$y = $this -> get_var('y', date('Y'));
		$show_tampil = $this -> get_var('show_tampil');
		
		$rekon_year_start 	= "<select name=\"year_start\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        $rekon_year_end 	= "<select name=\"year_end\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
		
		$bln 			= "<select name=\"data[periode][month]\" class=\"buttons\" id=\"month\">\r\n" . dateparse::get_combo_option_month_long($m) . "</select>&nbsp;\r\n";
		$tahun 			= "<select name=\"data[periode][year]\" class=\"buttons\" id=\"year\">\r\n" . dateparse::get_combo_option_year($y, date('Y') - 10, date('Y')) . "</select>\r\n";
		$tahunRkCons 	= "<select name=\"data[periode][year]\" class=\"buttons\" id=\"periode_year\">\r\n" . dateparse::get_combo_option_year($y, date('Y') - 10, date('Y')) . "</select>\r\n";
		loadClass('modules.accounting');
		
		 if ($this->S['curr_wil'] != '')
                {
                        $addsqlwil .= "  AND kddiv='{$this->S['curr_divisi']}' AND kodewilayah='{$this->S['curr_wil']}'";
                }
		else
		{
			$addsqlwil .= " AND kddiv='{$this->S['curr_divisi']}' ";
		}
		//$base->db->debug=true;	
		$kdspk = accounting::get_htmlselect_kdspk($base, 'data[kdspk]', $this -> get_var('kdspk'), true, '', '',$addsqlwil);
		$rsdiv = $base -> dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
		//echo $this->S['curr_divisi'];
		$combo_div = $rsdiv -> getMenu2('kddiv', $this -> get_var('kddiv'), false, false);
		
		$dDivisi 		= $base -> dbQuery("SELECT * FROM ddivisi WHERE is_visible = TRUE ORDER BY kddivisi");
		$formDivisi 	= '';
		while ( ! $dDivisi->EOF)
		{
			$formDivisi .= '<option value="'.strtolower($dDivisi->fields['kddivisi']).'">'.strtoupper($dDivisi->fields['kddivisi']).'. '.$dDivisi->fields['nmdivisi'].'</option>';
			$dDivisi->moveNext();
		}
			
		$tpl -> Assign(array(
			// 'TITLE' => 'Report Type : ' . $base -> dbGetOne("SELECT description FROM appsubmodule WHERE asmid='" . $this -> get_var('asmid') . "'"),
			'TITLE' => $title,
			'FORM_ACTION' => $form_action,
			'DIV' => strtolower($this->S['curr_divisi']),
			'ADMIN_FULLNAME' => $this->S['userdata']->real_name,
			'FORM_BLN' 			=> $bln,
			'FORM_TAHUN' 		=> $tahun,
			'FORM_TAHUN_RK' 	=> $tahunRkCons,
			'FORM_KDSPK' 		=> $kdspk,
			'UKER_DATA' 		=> $ukerr,
			'BASE_URL' 		=> $base->kcfg['url'],
			'BASE_URL_CI' 	=> $base_url,
			'TAHUN_START' 	=> $rekon_year_start,
			'TAHUN_END' 	=> $rekon_year_end,
			'FORM_DIVISI' 	=> $formDivisi
		));

		$this -> tpl = $tpl;
	}
	*/
	
	function sub_external_report($base)
	{
		//var_dump($base);die;
		if ($_SERVER['REMOTE_ADDR'] == '10.10.5.15'){
			$base->db= true;
		}
		
		$base_url = $base->kcfg['url'] .'ci/index.php/';
		$subtype = $this->get_var('subtype');
		$ukerr="";
		
		//UNTUK WILAYAH
		$wil = '';
		$sql_wil = $this->S['userdata']->get_sql_wilayah($base, $this->S['curr_divisi']);
		if ($this->S['curr_wil'] != '')
		{
		  $sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
		}
		//$sql = "SELECT nmspk,kdspk FROM dspk where iswilayah='t' AND kddiv='{$this->S['curr_divisi']}' AND kodewilayah=kdspk $sql_wil ORDER BY kdspk";
		//irul
		$sql = "SELECT nmspk,kdspk FROM dspk WHERE kddiv='{$this->S['curr_divisi']}'  ORDER BY kdspk";
		//print_r($sql);
		$rs = $base->dbquery($sql);
		$wil = $rs->getMenu2('kdwilayah',$this->S['curr_wil'],$this->S['userdata']->is_admin_wilayah($base,$this->S['curr_divisi']));
		// $tpl->Assign('WILAYAH',$wil);
		//==============
		
		
		// if ($subtype === 'antar_divisi_new')
		// {
			// $title = 'Laporan : Rekonsiliasi Hub R/K (Antar Departemen)';
			// $form_action = $base_url .'report_rk/antar_divisi_new';
			// $tpl = $base -> _get_tpl('accounting_report_form_external_rk_konsolidasi.html');
		// }
		if ($subtype === 'antar_divisi_new')
		{
			$title = 'Laporan : Rekonsiliasi Hub R/K (Antar Departemen)';
			$form_action = $base_url .'report_rk/divisi_proyek/11224';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_spl.html');
		}
		elseif ($subtype === 'divisi_proyek')
		{
			$title = 'Rekon Hub R/K Div-Spl (beta)';
			$form_action = $base_url .'report_rk/divisi_proyek';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_spl.html');
		}
		// elseif ($subtype === 'wilayah_proyek') //wilayah atau divisi - proyek COA 2110112
		// {
			// if ($this->S['curr_wil'] == ''){
				// die('Untuk melihat report ini, Anda harus memilih divisi dulu !');
			// }
				// $title = 'Rekon Hub R/K Divisi-Proyek';
				// $form_action = $base_url .'report_rk/divisi_proyek';
				// $tpl = $base -> _get_tpl('accounting_report_form_external_rk_wilayah_spl.html');
			// $tpl->Assign('WILAYAH_ID', $this->S['curr_wil']);
			// $addsqlwil = " AND kodewilayah='{$this->S['curr_wil']}'";
		// }
		elseif ($subtype === 'wilayah_proyek')
		{
			$title = 'Rekon Hub R/K Div-Proyek (beta)';
			$form_action = $base_url .'report_rk/divisi_proyek/21791';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_spl.html');
		}
		// elseif ($subtype === 'rk_jo')
		// {
			// $title = 'Rekon Hub R/K JO (beta)';
			// $form_action = $base_url .'report_rk/divisi_proyek_jo';
			// $tpl = $base -> _get_tpl('accounting_report_form_external_rk_jo.html');

		// }
		elseif ($subtype === 'rk_jo')
		{
			$title = 'Rekon Hub R/K JO (beta)';
			$form_action = $base_url .'report_rk/divisi_proyek/21911';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_spl.html');
		}
		elseif ($subtype === 'rk_jo_16')
		{
			$title = 'R/K JO';
			$form_action = $base_url .'report_rk/divisi_proyek_jo_16';
			echo $form_action;
			$tpl = $base -> _get_tpl('accounting_report_form_external_rk_jo_16.html');
		}
		elseif ($subtype === 'neraca_t')
		{
			$title = 'Posisi Keuangan';
			$form_action = $base_url .'neraca/neraca_t';
			$tpl = $base -> _get_tpl('accounting_report_form_neraca_t.html');
		}
		elseif ($subtype === 'biayausaha_rinci')
		{
			$title = "";
			$tahun_now = date('Y');
			$form_action = $base_url . 'biayausaha/rincian';
			$tpl = $base -> _get_tpl('biayausaha_report_form.html');
			$rsT = $base-> dbQuery("SELECT a.kduker,singkatan 
									FROM anggar a 
										LEFT JOIN z_groupname_bius b ON b.kddivisi = a.kddivisi and b.kduker = a.kduker
									WHERE a.kddivisi='{$this->S['curr_divisi']}' and a.tahun = '$tahun_now' 
									GROUP BY a.kduker,a.kddivisi,singkatan ORDER BY a.kduker");
			while(!$rsT->EOF)
            {
                $ukerr .="<option value = '".$rsT->fields['kduker']."'>".$rsT->fields['kduker']." - ".$rsT->fields['singkatan']."</option>\r\n";
                $rsT->moveNext();
            }
			if ($this->S['curr_divisi']=='A'){
				$FORM_BUSAHA = '
						<select class="textbox" name="data[periode][type2]" id="ikhtisar">      
							<option value="uker">per-Unit Kerja</option>    
							<option value="perkiraan">per-Perkiraan</option>        
						</select>';
			}else{
				$FORM_BUSAHA = '
						<select class="textbox" name="data[periode][type2]" id="ikhtisar">
							<option value="perkiraan">per-Perkiraan</option>        
						</select>';
			}
		}
		elseif ($subtype === 'export_txt')
		{
			$title = "";
			$tahun_now = date('Y');
			$form_action = $base_url . 'export_txt/export2paradox';
			$tpl = $base -> _get_tpl('export_txt.html');
			$rsT = $base-> dbQuery("SELECT a.kduker,singkatan 
									FROM anggar a 
										LEFT JOIN z_groupname_bius b ON b.kddivisi = a.kddivisi and b.kduker = a.kduker
									WHERE a.kddivisi='{$this->S['curr_divisi']}' and a.tahun = '$tahun_now' 
									GROUP BY a.kduker,a.kddivisi,singkatan ORDER BY a.kduker");
			$divisi = $base->dbQuery("SELECT * FROM ddivisi");
			while(!$divisi->EOF){
                $ddivisi .="<option value = '".$divisi->fields['kddivisi']."'>".$divisi->fields['kddivisi']." - ".$divisi->fields['nmdivisi']."</option>\r\n";
                $divisi->moveNext();				
			}
			while(!$rsT->EOF)
            {
                $ukerr .="<option value = '".$rsT->fields['kduker']."'>".$rsT->fields['kduker']." - ".$rsT->fields['singkatan']."</option>\r\n";
                $rsT->moveNext();
            }
			if ($this->S['curr_divisi']=='A'){
				$FORM_BUSAHA = '
						<select class="textbox" name="data[periode][type2]" id="ikhtisar">      
							<option value="uker">per-Unit Kerja</option>    
							<option value="perkiraan">per-Perkiraan</option>        
						</select>';
			}else{
				$FORM_BUSAHA = '
						<select class="textbox" name="data[periode][type2]" id="ikhtisar">
							<option value="perkiraan">per-Perkiraan</option>        
						</select>';
			}
		}
		elseif ($subtype === 'biayausaha_rinci_konsolidasi')
		{
			$title = "";
			$form_action = $base_url . 'biayausaha/rinciankonsolidasi';
			$tpl = $base -> _get_tpl('biayausaha_konsolidasi_report_form.html');
			$rsT = $base-> dbQuery("
								SELECT a.kduker,singkatan 
								FROM anggar a 
								LEFT JOIN z_groupname_bius b ON b.kddivisi = a.kddivisi and b.kduker = a.kduker
								WHERE a.kddivisi='{$this->S['curr_divisi']}' and a.tahun = '$tahun_now' 
								GROUP BY a.kduker,a.kddivisi,singkatan 
								ORDER BY a.kduker");
			while(!$rsT->EOF)
            {
                $ukerr .="<option value = '".$rsT->fields['kduker']."'>".$rsT->fields['kduker']." - ".$rsT->fields['singkatan']."</option>\r\n";
                $rsT->moveNext();
            }
		}
		elseif($subtype === 'labarugi')
		{
			$title = "Laba Rugi Departemen";
			$form_action = $base_url.'labarugi/divisi';
			$tpl = $base -> _get_tpl('labarugi_form.html');
			$base_url = $base->kcfg['url'] .'ci/index.php/';
			if ($_SERVER['REMOTE_ADDR'] == '10.10.5.108'){
			}
		}
		elseif($subtype === 'labarugi_proyek')
		{
			
			$title = "Laba Rugi Proyek";
			$form_action = $base_url.'labarugi/divisi_proyek';
			$tpl = $base -> _get_tpl('labarugi_proyek_form.html');
			$base_url = $base->kcfg['url'] .'ci/index.php/';
			if ($_SERVER['REMOTE_ADDR'] == '10.10.5.108'){
			}
		}
		elseif($subtype === 'labarugi_kompre')
		{
			//$base->db->debug= true;
			error_reporting(E_ALL);
			loadClass('modules.accounting');
			if ($this->S['curr_divisi'] == 'T') $sql_wil = $this->S['userdata']->get_sql_spk_wilayah($base);
			else $sql_wil = '';
			
			if ($this->S['curr_wil'] != '')
                {
					$sql_wil .= " AND kodewilayah='{$this->S['curr_wil']}'";
                }
			$title = "Laba Rugi Komprehensif beta v.1";
			$form_action = $base_url.'labarugi/kompre';
			$tpl = $base -> _get_tpl('labarugi_kompre_form.html');
			$base_url = $base->kcfg['url'] .'ci/index.php/';
			$kdspk = accounting::get_htmlselect_kdspk($base, 'data[kdspk]', $this -> get_var('kdspk'), true, '', '', "AND is_jo='t' AND kddiv='{$this->S['curr_divisi']}'");
			
			$tpl -> Assign(array(
				// 'TITLE' => 'Report Type : ' . $base -> dbGetOne("SELECT description FROM appsubmodule WHERE asmid='" . $this -> get_var('asmid') . "'"),
				'TITLE' 		=> 'Report Type : ' .$title,
				'FORM_ACTION' 	=> $form_action,
				'DIV' 			=> strtolower($this->S['curr_divisi']),
				'ADMIN_FULLNAME' => $this->S['userdata']->real_name,
				'FORM_BLN' 		=> $bln,
				'FORM_TAHUN'	=> $tahun,
				'FORM_TAHUN_RK' => $tahunRkCons,
				'FORM_KDSPK' 	=> $kdspk,
				'UKER_DATA' 	=> $ukerr,
				'BASE_URL' 		=> $base->kcfg['url'],
				'BASE_URL_CI' 	=> $base_url,
				'TAHUN_START' 	=> $rekon_year_start,
				'TAHUN_END' 	=> $rekon_year_end,
				'FORM_DIVISI' 	=> $formDivisi,
				'FORM_PER' 		=> $FORM_PER,
				'FORM_BIAYA_USAHA' => $FORM_BUSAHA,
				'WILAYAH'		=> $wil,
				'KDSPK'			=> $kdspk,
			));
			
		}
		elseif($subtype === 'neraca_lajur')
		{
			$title = "Neraca Lajur Konsolidasi";
			$form_action = $base_url.'neraca/konsolidasi';
			$tpl = $base -> _get_tpl('neraca_lajur_form.html');
		}
		elseif($subtype === 'rekon_pajak_konsolidasi')
		{
			$title = 'Rekon Pajak Konsolidasi';
			$form_action = $base_url .'rekon_pajak/rekon';
			$tpl = $base -> _get_tpl('accounting_report_form_external_rekonpajak_konsolidasi.html');
		}
		
		$dp = new dateparse;
		$m = $this -> get_var('m', date('m'));
		$y = $this -> get_var('y', date('Y'));
		$show_tampil = $this -> get_var('show_tampil');
		
		$rekon_year_start 	= "<select name=\"year_start\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
        $rekon_year_end 	= "<select name=\"year_end\" class=\"buttons\">\r\n".dateparse::get_combo_option_year($y,date('Y')-10,date('Y'))."</select>\r\n";
		
		$bln 			= "<select name=\"data[periode][month]\" class=\"buttons\" id=\"month\">\r\n" . dateparse::get_combo_option_month_long($m) . "</select>&nbsp;\r\n";
		$tahun 			= "<select name=\"data[periode][year]\" class=\"buttons\" id=\"year\">\r\n" . dateparse::get_combo_option_year($y, date('Y') - 10, date('Y')) . "</select>\r\n";
		$tahunRkCons 	= "<select name=\"data[periode][year]\" class=\"buttons\" id=\"periode_year\">\r\n" . dateparse::get_combo_option_year($y, date('Y') - 10, date('Y')) . "</select>\r\n";
		loadClass('modules.accounting');
		$kdspk = accounting::get_htmlselect_kdspk($base, 'data[kdspk]', $this -> get_var('kdspk'), true, '', '', "AND kddiv='{$this->S['curr_divisi']}'");

		$rsdiv = $base -> dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' AND is_visible='t' ORDER BY kddivisi");
		$combo_div = $rsdiv -> getMenu2('kddiv', $this -> get_var('kddiv'), false, false);
		
		$dDivisi 		= $base -> dbQuery("SELECT * FROM ddivisi WHERE is_visible = TRUE ORDER BY kddivisi");
		$formDivisi 	= '';
		while ( ! $dDivisi->EOF)
		{
			$formDivisi .= '<option value="'.strtolower($dDivisi->fields['kddivisi']).'">'.strtoupper($dDivisi->fields['kddivisi']).'. '.$dDivisi->fields['nmdivisi'].'</option>';
			$dDivisi->moveNext();
		}
		
		//irul
		if ($subtype == 'rk_jo_16' or $subtype == 'rk_jo'){
		//if ($subtype == 'rk_jo'){
			//$kdspk = accounting::get_htmlselect_kdspk($base, 'data[kdspk]', $this -> get_var('kdspk'), true, '', '', "AND is_jo='t' AND kddiv='{$this->S['curr_divisi']}'");
			loadClass('modules.accounting');
		$kdspk = accounting::get_htmlselect_kdspk($base, 'data[kdspk]', $this -> get_var('kdspk'), true, '', '', "AND kddiv='{$this->S['curr_divisi']}'");

		$rsdiv = $base -> dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
		$combo_div = $rsdiv -> getMenu2('kddiv', $this -> get_var('kddiv'), false, false);
			$FORM_PER = '
				<select name="data[per]" id="per">
	                <option value="spk">SPK</option>
	                <option value="jenisjo">Jenis Jo</option>
	            </select> ';
		}else{
			$FORM_PER ='';
		}
		
		$tpl -> Assign(array(
			// 'TITLE' => 'Report Type : ' . $base -> dbGetOne("SELECT description FROM appsubmodule WHERE asmid='" . $this -> get_var('asmid') . "'"),
			'TITLE' => $title,
			'FORM_ACTION' 	=> $form_action,
			'DIV' 			=> strtolower($this->S['curr_divisi']),
			'ADMIN_FULLNAME' => $this->S['userdata']->real_name,
			'FORM_BLN' 		=> $bln,
			'FORM_TAHUN'	=> $tahun,
			'FORM_TAHUN_RK' => $tahunRkCons,
			'FORM_KDSPK' 	=> $kdspk,
			'UKER_DATA' 	=> $ukerr,
			'BASE_URL' 		=> $base->kcfg['url'],
			'BASE_URL_CI' 	=> $base_url,
			'TAHUN_START' 	=> $rekon_year_start,
			'TAHUN_END' 	=> $rekon_year_end,
			'FORM_DIVISI' 	=> $formDivisi,
			'FORM_PER' 		=> $FORM_PER,
			'FORM_BIAYA_USAHA' => $FORM_BUSAHA,
			'WILAYAH'		=> $wil,
		));

		$this -> tpl = $tpl;
	}
	
	
	
	function sub_external_report_os($base)
	{
		// $base_url = 'http://'. $_SERVER['SERVER_NAME'] .'/ci/index.php/';
		$base_url = $base->kcfg['url'] .'ci/index.php/';
		$GET = (object)array(
			'kode_os' 		=> $this->get_var('kode_os'),
			'per' 			=> $this->get_var('per'),
			'report_label'  => $this->get_var('title'),
			'title'			=> $this->get_var('title')
		);
		
		$tpl = $base -> _get_tpl('accounting_report_form_external_os.html');
		
		$dp = new dateparse;
		$m = $this -> get_var('m', date('m'));
		$y = $this -> get_var('y', date('Y'));
		$show_tampil = $this -> get_var('show_tampil');

		$bln = "<select name=\"data[month]\" id=\"periode_month\" class=\"buttons\">\r\n" . dateparse::get_combo_option_month_long($m) . "</select>&nbsp;\r\n";
		$tahun = "<select name=\"data[year]\" id=\"periode_year\" class=\"buttons\">\r\n" . dateparse::get_combo_option_year($y, date('Y') - 10, date('Y')) . "</select>\r\n";
		loadClass('modules.accounting');
		$kdspk = accounting::get_htmlselect_kdspk($base,'data[kdspk]',$this->get_var('kdspk'),true,'','',"AND kddiv='{$this->S['curr_divisi']}'");
		// var_dump($kdspk);

		$rsdiv = $base -> dbQuery("SELECT kddivisi||' '||nmdivisi, kddivisi FROM ddivisi WHERE kddivisi<>'{$this->S['curr_divisi']}' ORDER BY kddivisi");
		$combo_div = $rsdiv -> getMenu2('kddiv', $this -> get_var('kddiv'), false, false);
		$title = $base->dbGetOne("SELECT keterangan FROM report_reff WHERE kdreport='{$GET->kode_os}' LIMIT 1");

		$FORM_PER = '';
		if (strtoupper($this->S['curr_divisi']) === 'A')
		{
			$FORM_PER = '
				<select name="data[per]" id="per">
	                <option value="nsb">Nasabah</option>
	                <option value="nsb-spk">Nasabah Per SPK</option>
	                <option value="spk">SPK</option>
	                <option value="spk-nsb">SPK Per Nasabah</option>
	                <option value="cons-nsb">Konsolidasi - Nasabah</option>
	                <option value="cons-spk">Konsolidasi - SPK</option>
	                <!-- <option value="cons-div">Konsolidasi - Departemen</option> -->
	            </select> ';
		}
		else
		{
			$FORM_PER = '
				<select name="data[per]" id="per">
	                <option value="nsb">Nasabah</option>
	                <option value="nsb-spk">Nasabah Per SPK</option>
	                <option value="spk">SPK</option>
	                <option value="spk-nsb">SPK Per Nasabah</option>
	            </select> ';
		}

		$tpl -> Assign(array(
			// 'TITLE' => 'Report Type : ' . $base -> dbGetOne("SELECT description FROM appsubmodule WHERE asmid='" . $this -> get_var('asmid') . "'"),
			'TITLE' 		=> ucwords( strtolower($title) ) .' per '. strtoupper($GET->per), 
			'FORM_ACTION' 	=> $base_url . 'report_os/rinci/', 
			'KDDIVISI' 		=> strtolower($this->S['curr_divisi']), 
			'ADMIN' 		=> $this->S['userdata']->real_name, 
			'FORM_BLN' 		=> $bln, 
			'FORM_TAHUN' 	=> $tahun, 
			'KODE_OS' 		=> $GET->kode_os, 
			'PER' 			=> $GET->per, 
			'FORM_KDSPK' 	=> $kdspk,
			'BASE_URL' 		=> $base_url,
			// 'FORM_CONS' 	=> ((strtoupper($this->S['curr_divisi'])==='A')?'<input id="cons" type="checkbox" name="cons" value="true" />':''),
			'FORM_PER' 		=> $FORM_PER
		));

		$this -> tpl = $tpl;
	}
	function get_jenis_jo($base, $kdalat){
		// $base->db= true;
		$sql 	= "SELECT nmalat FROM dalat WHERE kdalat='{$kdalat}'";
		$rs	= $base->dbQuery($sql);
		if(!$rs->EOF)
		{
			return $rs->fields['nmalat'];
		}else{
			return 'SALAH BUKU';
		}
	}
	
	function get_nama_spk($base, $kdspk, $kddivisi){
		// $base->db= true;
		$sql 	= "SELECT nmspk FROM dspk WHERE kdspk='{$kdspk}' and kddiv='{$kddivisi}' limit 1";
		$rs	= $base->dbQuery($sql);
		if(!$rs->EOF)
		{
			return $rs->fields['nmspk'];
		}else{
			return 'SALAH SPK';
		}
	}
}
?>
