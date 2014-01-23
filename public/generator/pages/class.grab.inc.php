<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$izoqg47578735APDVg=675495728;$gQIaY77208863PEfjY=121484863;$gtiXw44085083QkElc=447452515;$gorCp86019898kbHSt=935367432;$Bhapj25825805hERLW=367698364;$bczMD91315308QhoGo=24414062;$vieuy15300903xXoIX=685983277;$emFSH25595092FkJfu=635374756;$NNmBC35010376iQEfH=653057251;$sITKj91359253dRJOl=20999511;$DwVVX17454223JIMVa=518670288;$EiNZE41107788sRvyI=429038330;$lEYdd75132447nRJHB=532572388;$VjjEX77340698tbeXL=111241211;$cHGsc50545044RQeeG=944513550;$HoUME42557983SQAhU=316358154;$OBLgv56192017fKiey=6243774;$hoxnJ49259643SBtnO=295139160;$ksYaI24573364Zbfwp=964513062;$PQHlH29945678deMHr=297334228;$slHDg68189087RNGlj=73071411;$fvMnS97116089EwDzy=572693359;$hHxBT29539184UQFSE=578668823;$slaQl93270874HoSTM=371966553;$kfGNS21123657ddUps=733055298;$rerTu40910034kLgIq=943903809;$Qwiih65442505ZIQmZ=785980835;$KjcCA52533569oXcGz=540255127;$skHLy94995728WJPqc=987195435;$EqUGI60641480ZNnjz=409770508;$NiZVI42283325RZvMp=587449097;$FUVcJ87733765ElLfj=802199951;$OqKey19805297kgNPb=835491822;$JdUrz66310425mSzpk=968293457;$OtRfJ50061645JlRsb=982073609;$YaVqS18871460XuKoR=158801025;$kIIND65552368zXnlb=277944458;$ONyns57916870rfMnW=621472656;$RXkgR88777466zYVWL=970854371;$uvcLi25946655kUbza=608058350;$MyEmj52236938MYpEv=313553345;$fNrBQ35460815Uasyd=368308105;$LWASy68430786muQEC=553791382;$zSsnP18959350QKXVU=151971923;$NccYx69859009tcuHz=942318482;$IfmWC88942261iMxtR=208799804;$eqbqz79021607rJdvt=729884644;$sYJsv87909546HcrSa=788541748;$pRjdh28418579IdfDg=166239868;$KMzpQ38361206Ujocg=142947754;?><?php class SiteCrawler { function uNFFhEr_Ldjz6gLuOb(&$a, $KCZQduSVvXZZGY28, $dtVinIseWb, $ndwxwUINtLIx81E, $FEajaUctQRUH5JF2gMO, $IKboqZ81Or = '') { global $grab_parameters; $kCPGNzrIjuQgkoxBRP = parse_url($FEajaUctQRUH5JF2gMO); if($kCPGNzrIjuQgkoxBRP['scheme'] && substr($a, 0, 2) == '//') 
																									 $a = $kCPGNzrIjuQgkoxBRP['scheme'].':'.$a; $a = str_replace(':80/', '/', $a); if($a[0]=='?')$a = preg_replace('#^([^\?]*?)([^/\?]*?)(\?.*)?$#','$2',$KCZQduSVvXZZGY28).$a; if($grab_parameters['xs_inc_ajax'] && ($a[0] == '#')){ $a = preg_replace('#\#.*$#', '', $KCZQduSVvXZZGY28).$a; } if(preg_match('#^https?(:|&\#58;)#is',$a)){ if(preg_match('#://[^/]*$#is',$a)) 
																									 $a .= '/'; } else if($a&&$a[0]=='/')$a = $dtVinIseWb.$a; else $a = $ndwxwUINtLIx81E.$a; $a=str_replace('/./','/',$a); if(substr($a,-2) == '..')$a.='/'; if(strstr($a,'../')){ preg_match('#(.*?:.*?//.*?)(/.*)$#',$a,$aa); 
																									 do{ $ap = $aa[2]; $aa[2] = preg_replace('#/?[^/]*/\.\.#','',$ap,1); }while($aa[2]!=$ap); $a = $aa[1].$aa[2]; } $a = preg_replace('#/\./#','/',$a); $a = str_replace('&#38;','&',$a); $a = str_replace('&#038;','&',$a); $a = str_replace('&amp;','&',$a); $a = preg_replace('#\#'.($grab_parameters['xs_inc_ajax']?'[^\!]':'').'.*$#','',$a); $a = preg_replace('#^([^\?]*[^/\:]/)/+#','\\1',$a); $a = preg_replace('#[\r\n]+#s','',$a); $ZxN8MjZW3uwPfqCTY = (strtolower(substr($a,0,strlen($FEajaUctQRUH5JF2gMO)) ) != strtolower($FEajaUctQRUH5JF2gMO)) ? 1 : 0; if($ZxN8MjZW3uwPfqCTY && $IKboqZ81Or) { $mWZ5i5d3fAL = preg_replace('#[\r\n]+#is', '|', trim($IKboqZ81Or)); if($mWZ5i5d3fAL && preg_match('#('.$mWZ5i5d3fAL.')#', $a)) $ZxN8MjZW3uwPfqCTY = 2; } D1tXUjHdde7g1("<br/>($a - $ZxN8MjZW3uwPfqCTY)<br>\n",2); return $ZxN8MjZW3uwPfqCTY; } function C1ZRyst7ZYH($IrW3Ht3AZogqo2OF,&$urls_completed) { global $grab_parameters,$OCFdVAYmxKRN; error_reporting(E_ALL&~E_NOTICE); @set_time_limit($grab_parameters['xs_exec_time']); if($IrW3Ht3AZogqo2OF['bgexec']) { ignore_user_abort(true); } register_shutdown_function('kEM8KMpb9E'); if(function_exists('ini_set')) { @ini_set("zlib.output_compression", 0); @ini_set("output_buffering", 0); } $zHTlHkx0y7jw = explode(" ",microtime()); $baLr5ayef = $zHTlHkx0y7jw[0]+$zHTlHkx0y7jw[1]; $starttime = $CmZLfMuLMSEZIiTevcX = time(); $H9ksmjIXr = $nettime = 0; $dREpFQ5xuzJSkbpu7AS = $IrW3Ht3AZogqo2OF['initurl']; $UL_rhtNF7ST = $IrW3Ht3AZogqo2OF['maxpg']>0 ? $IrW3Ht3AZogqo2OF['maxpg'] : 1E10; $RJhfKMpNS = $IrW3Ht3AZogqo2OF['maxdepth'] ? $IrW3Ht3AZogqo2OF['maxdepth'] : -1; $QmKaHsN_mnHEfYSpTEg = $IrW3Ht3AZogqo2OF['progress_callback']; $Olp20ADnjq2W_ThV6n = preg_replace("#\s*[\r\n]+\s*#",'|', (strstr($s=trim($grab_parameters['xs_excl_urls']),'*')?$s:preg_quote($s,'#'))); $s7rLwAieg_iDoSCpz1Z = preg_replace("#\s*[\r\n]+\s*#",'|', (strstr($s=trim($grab_parameters['xs_incl_urls']),'*')?$s:preg_quote($s,'#'))); $fqxdyrTNEz = $J951dqGyd9Mj = array(); $UIcNE7i_HnCy = preg_split('#[\r\n]+#', $grab_parameters['xs_ind_attr']); $KLhrN1FulWAW = '#200'.($grab_parameters['xs_allow_httpcode']?'|'.$grab_parameters['xs_allow_httpcode']:'').'#'; if($grab_parameters['xs_memsave']) { if(!file_exists(kxesmZvVXn)) mkdir(kxesmZvVXn, 0777); else if($IrW3Ht3AZogqo2OF['resume']=='') zSyg85rKFKJ8zd(kxesmZvVXn, '.txt'); } foreach($UIcNE7i_HnCy as $ia) if($ia) { $is = explode(',', $ia); if($is[0][0]=='$') $MWPxoCBCNe = substr($is[0], 1); else $MWPxoCBCNe = str_replace(array('\\^', '\\$'), array('^','$'), preg_quote($is[0],'#')); $J951dqGyd9Mj[] = $MWPxoCBCNe; $fqxdyrTNEz[str_replace(array('^','$'),array('',''),$is[0])] =  array('lm' => $is[1], 'f' => $is[2], 'p' => $is[3]); } if($J951dqGyd9Mj) $kgQxFsRGfcjLoo9_ = implode('|',$J951dqGyd9Mj); $f3rjBPjAjA5g2vr3_l = parse_url($dREpFQ5xuzJSkbpu7AS); if(!$f3rjBPjAjA5g2vr3_l['path']){$dREpFQ5xuzJSkbpu7AS.='/';$f3rjBPjAjA5g2vr3_l = parse_url($dREpFQ5xuzJSkbpu7AS);} $wrcGK5HRvX5Hc = $OCFdVAYmxKRN->fetch($dREpFQ5xuzJSkbpu7AS,0,true);// the first request is to skip session id 
																									 $UIPYmw0UFeGY = !preg_match($KLhrN1FulWAW,$wrcGK5HRvX5Hc['code']); if($UIPYmw0UFeGY) { $UIPYmw0UFeGY = ''; foreach($wrcGK5HRvX5Hc['headers'] as $k=>$v) $UIPYmw0UFeGY .= $k.': '.$v.'<br />'; return array( 'errmsg'=>'<b>There was an error while retrieving the URL specified:</b> '.$dREpFQ5xuzJSkbpu7AS.''. ($wrcGK5HRvX5Hc['errormsg']?'<br><b>Error message:</b> '.$wrcGK5HRvX5Hc['errormsg']:''). '<br><b>HTTP Code:</b><br>'.$wrcGK5HRvX5Hc['protoline']. '<br><b>HTTP headers:</b><br>'.$UIPYmw0UFeGY. '<br><b>HTTP output:</b><br>'.$wrcGK5HRvX5Hc['content'] , ); } $dREpFQ5xuzJSkbpu7AS = $wrcGK5HRvX5Hc['last_url']; $urls_completed = array(); $urls_ext = array(); $urls_404 = array(); $dtVinIseWb = $f3rjBPjAjA5g2vr3_l['scheme'].'://'.$f3rjBPjAjA5g2vr3_l['host'].((!$f3rjBPjAjA5g2vr3_l['port'] || ($f3rjBPjAjA5g2vr3_l['port']=='80'))?'':(':'.$f3rjBPjAjA5g2vr3_l['port'])); 
																									 $pn = $tsize = $retrno = 0; $FEajaUctQRUH5JF2gMO = Mq7SpvssgXyM($dtVinIseWb.'/', xnDpYg7WwA0($f3rjBPjAjA5g2vr3_l['path'])); $vQG590i8y = preg_replace('#^.+://[^/]+#', '', $FEajaUctQRUH5JF2gMO); 
																									 $RoI8NMX2YhCI5phmR = $OCFdVAYmxKRN->fetch($dREpFQ5xuzJSkbpu7AS,0,true,true); $QVcuW67y39PsmXB = str_replace($FEajaUctQRUH5JF2gMO,'',$dREpFQ5xuzJSkbpu7AS); $urls_list_full = array($QVcuW67y39PsmXB=>1); if(!$QVcuW67y39PsmXB)$QVcuW67y39PsmXB=''; $urls_list = array($QVcuW67y39PsmXB=>1); $urls_list2 = $urls_list_skipped = array(); $K4v1BKjLwk2MbkhbF = array(); $links_level = 0; $Anwo83cgBW0AC = $ref_links = $ref_links2 = array(); $DOgZUqwUCl = 0; $MbFeeAu1C = $UL_rhtNF7ST; if(!$grab_parameters['xs_progupdate'])$grab_parameters['xs_progupdate'] = 20; if(isset($grab_parameters['xs_robotstxt']) && $grab_parameters['xs_robotstxt']) { $urKftSsitV6UKM = $OCFdVAYmxKRN->fetch($dtVinIseWb.'/robots.txt'); if($dtVinIseWb.'/' != $FEajaUctQRUH5JF2gMO) { $TrV189ypHQ = "\n".$OCFdVAYmxKRN->fetch($FEajaUctQRUH5JF2gMO.'robots.txt'); $urKftSsitV6UKM['content']  .= "\n".$TrV189ypHQ['content']; } $ra=preg_split('#user-agent:\s*#im',$urKftSsitV6UKM['content']); $AbOnA9dFxAZFArqSEam=array(); for($i=1;$i<count($ra);$i++){ preg_match('#^(\S+)(.*)$#s',$ra[$i],$Y31Y74dyLIO); if($Y31Y74dyLIO[1]=='*'||strstr($Y31Y74dyLIO[1],'google')){ preg_match_all('#^disallow:[^\r\n\S](\S*)#im',$Y31Y74dyLIO[2],$rm); for($pi=0;$pi<count($rm[1]);$pi++) if($rm[1][$pi]) $AbOnA9dFxAZFArqSEam[] =  str_replace('\\$','$', str_replace('\\*','.*', preg_quote($rm[1][$pi],'#') )); } } for($i=0;$i<count($AbOnA9dFxAZFArqSEam);$i+=200) $rc3b9mqz2jbp[]=implode('|', array_slice($AbOnA9dFxAZFArqSEam, $i,200)); }else $rc3b9mqz2jbp = array(); if($grab_parameters['xs_inc_ajax']) $grab_parameters['xs_proto_skip'] = str_replace( '\#', '\#[^\!]', $grab_parameters['xs_proto_skip']); $k2mbaYG7Adhhp0nn = $grab_parameters['xs_exc_skip']!='\\.()'; $GO8TFrE_eI = $grab_parameters['xs_inc_skip']!='\\.()'; $grab_parameters['xs_inc_skip'] .= '$'; $grab_parameters['xs_exc_skip'] .= '$'; if($grab_parameters['xs_debug']) { $_GET['ddbg']=1; G99nA35xjYQh(); } $XWHbijqSXRb = 0; $url_ind = 0; $cnu = 1; $pf = PvFdgEcx0laOpBsp(REpEqrxI7DpN9.Jh02oPSmnHw,'w');fclose($pf); $x0KsMnatxIC9 = false; if($IrW3Ht3AZogqo2OF['resume']!=''){ $qNmmXd_uegk0tE9Fi = @PvEr4n2DQ(FRy4YMXr_PT(REpEqrxI7DpN9.OWNVYbuUt2KT49cveBR)); if($qNmmXd_uegk0tE9Fi) { $x0KsMnatxIC9 = true; echo 'Resuming the last session (last updated: '.date('Y-m-d H:i:s',$qNmmXd_uegk0tE9Fi['time']).')'."\n"; extract($qNmmXd_uegk0tE9Fi); $baLr5ayef-=$ctime; $XWHbijqSXRb = $ctime; unset($qNmmXd_uegk0tE9Fi); } } $NCzIEhBxs5f = 0; if(!$x0KsMnatxIC9){ if($grab_parameters['xs_moreurls']){ $mu = preg_split('#[\r\n]+#', $grab_parameters['xs_moreurls']); foreach($mu as $bXB74SJYMX2s6phkekL){ $bXB74SJYMX2s6phkekL = str_replace($FEajaUctQRUH5JF2gMO, '', $bXB74SJYMX2s6phkekL); if(!strstr($bXB74SJYMX2s6phkekL, '://')) 
																									 $urls_list[$bXB74SJYMX2s6phkekL]++; } } if($grab_parameters['xs_prev_sm_base']){ global $PRKe3jTsdEw9doYFe; $foaeXizKT_CpOLBpUxK = basename($grab_parameters['xs_smname']); $PRKe3jTsdEw9doYFe->HF6NqOcvPqnbh7 = $grab_parameters['xs_compress'] ? '.gz' : ''; $hCqPL8RwlB = $PRKe3jTsdEw9doYFe->YD8Ku5kCxh9EuzlBTRB($foaeXizKT_CpOLBpUxK, 0, $FEajaUctQRUH5JF2gMO); $urls_list = array_merge($urls_list, $hCqPL8RwlB); unset($hCqPL8RwlB); } $NCzIEhBxs5f = count($urls_list); $urls_list_full = $urls_list; $cnu = count($urls_list); } $Q1yPjl8DEG9ZLcPEA = explode('|', $grab_parameters['xs_force_inc']); sleep(1); @fTr9xtaaPTXU(REpEqrxI7DpN9.Jh02oPSmnHw); if($urls_list) do { list($KCZQduSVvXZZGY28, $Kg85Ttrs41sWuz) = each($urls_list); $HtFBZd5BGx4HvWf = ($Kg85Ttrs41sWuz>0 && $Kg85Ttrs41sWuz<1) ? $Kg85Ttrs41sWuz : 0; $url_ind++; D1tXUjHdde7g1("\n[ $url_ind - $KCZQduSVvXZZGY28, $Kg85Ttrs41sWuz] \n"); unset($urls_list[$KCZQduSVvXZZGY28]); $Su4TnWFte = VQAXwSjJPIxpPDQ5i5S($KCZQduSVvXZZGY28); $r1aeZ6w8pZekvR9G6 = false; $CMZfyKXzIg6kFN = ''; $wrcGK5HRvX5Hc = array(); $cn = ''; if(isset($K4v1BKjLwk2MbkhbF[$KCZQduSVvXZZGY28])) $KCZQduSVvXZZGY28=$K4v1BKjLwk2MbkhbF[$KCZQduSVvXZZGY28]; $f = $k2mbaYG7Adhhp0nn && preg_match('#'.$grab_parameters['xs_exc_skip'].'#i',$KCZQduSVvXZZGY28); if($Olp20ADnjq2W_ThV6n&&!$f)$f=$f||preg_match('#('.$Olp20ADnjq2W_ThV6n.')#',$KCZQduSVvXZZGY28); if($rc3b9mqz2jbp&&!$f) foreach($rc3b9mqz2jbp as $bm) { $f = $f||preg_match('#^('.$bm.')#',$vQG590i8y.$KCZQduSVvXZZGY28); } $f2 = false; if(!$f) { $f2 = $GO8TFrE_eI && preg_match('#'.$grab_parameters['xs_inc_skip'].'#i',$KCZQduSVvXZZGY28); if($s7rLwAieg_iDoSCpz1Z&&!$f2) $f2 = $f2||(preg_match('#('.$s7rLwAieg_iDoSCpz1Z.')#',$KCZQduSVvXZZGY28)); if($grab_parameters['xs_parse_only'] && !$f2 && $KCZQduSVvXZZGY28!='/') { $f2 = $f2 || !preg_match('#'.str_replace(' ', '|', preg_quote($grab_parameters['xs_parse_only'],'#')).'#',$KCZQduSVvXZZGY28); } } do{ $f3 = $Q1yPjl8DEG9ZLcPEA[2] && ( ($MbFeeAu1C*$Q1yPjl8DEG9ZLcPEA[2]+1000)< (count($urls_list)+count($urls_list2)+count($urls_completed) -$url_ind-$NCzIEhBxs5f)); if(!$f && !$f2) { $QxoEWBD1O1qswq3ii = ($Q1yPjl8DEG9ZLcPEA[1] &&  ( (($ctime>$Q1yPjl8DEG9ZLcPEA[0]) && ($pn>$UL_rhtNF7ST*$Q1yPjl8DEG9ZLcPEA[1])) || $f3));	 if($RJhfKMpNS<=0 || $links_level<$RJhfKMpNS) { $bn0GubjxW3nLmpyxamZ = Mq7SpvssgXyM($FEajaUctQRUH5JF2gMO,$KCZQduSVvXZZGY28); D1tXUjHdde7g1("<h4> { $bn0GubjxW3nLmpyxamZ } </h4>\n"); $S6zsGHiQqbyS93wtuJ=0; $PQ88kZzO1uRFvk=array_sum(explode(' ', microtime())); do { $wrcGK5HRvX5Hc = $OCFdVAYmxKRN->fetch($bn0GubjxW3nLmpyxamZ, 0, 1); if(($wrcGK5HRvX5Hc['code']==403)||!$wrcGK5HRvX5Hc['code']) { $S6zsGHiQqbyS93wtuJ++; sleep($grab_parameters['xs_delay_ms']?$grab_parameters['xs_delay_ms']:1); } else $S6zsGHiQqbyS93wtuJ=5; }while($S6zsGHiQqbyS93wtuJ<3); $XnUbn0PvStizD = array_sum(explode(' ', microtime()))-$PQ88kZzO1uRFvk; $nettime+=$XnUbn0PvStizD; D1tXUjHdde7g1("<hr>\n[[[ ".$wrcGK5HRvX5Hc['code']." ]]] - ".number_format($XnUbn0PvStizD,2)."s (".number_format($OCFdVAYmxKRN->QeahkPg4bVaAigh,2).' + '.number_format($OCFdVAYmxKRN->O2Mt183Pc1Aha,2).")\n".var_export($wrcGK5HRvX5Hc['headers'],1)); $yMBs2OPLVVuAK = is_array($wrcGK5HRvX5Hc['headers']) ? strtolower($wrcGK5HRvX5Hc['headers']['content-type']) : ''; $U_QkBfcAHSK3Jphne = strstr($yMBs2OPLVVuAK,'text/html') || strstr($yMBs2OPLVVuAK,'/xhtml') || !$yMBs2OPLVVuAK; if($yMBs2OPLVVuAK && !$U_QkBfcAHSK3Jphne && (!$grab_parameters['xs_parse_swf'] || !strstr($yMBs2OPLVVuAK, 'shockwave-flash')) ){ if(!$QxoEWBD1O1qswq3ii){ $CMZfyKXzIg6kFN = $yMBs2OPLVVuAK; continue; } } $VJrkoghoW = array(); if($wrcGK5HRvX5Hc['code']==404){ if($links_level>0) if(!$grab_parameters['xs_chlog_list_max'] || count($urls_404) < $grab_parameters['xs_chlog_list_max']) { $urls_404[]=array($KCZQduSVvXZZGY28,$ref_links2[$KCZQduSVvXZZGY28]); } } if($KLhrN1FulWAW && !preg_match($KLhrN1FulWAW,$wrcGK5HRvX5Hc['code'])){ $CMZfyKXzIg6kFN = $wrcGK5HRvX5Hc['code']; continue; } $cn = $wrcGK5HRvX5Hc['content']; $tsize+=strlen($cn); $retrno++; if($m51TO___YwFjZX = preg_replace('#<!--(\[if IE\]>|.*?-->)#is', '',$cn)) $cn = $m51TO___YwFjZX; if($grab_parameters['xs_canonical']) if(($bn0GubjxW3nLmpyxamZ == $wrcGK5HRvX5Hc['last_url']) && preg_match('#<link[^>]*rel="canonical"[^>]href="([^>]*?)"#', $cn, $vk7woDGzb9IB_Gal9)) $wrcGK5HRvX5Hc['last_url'] = $vk7woDGzb9IB_Gal9[1]; $IFy8IvAAwMb4K = preg_replace('#^.*?'.preg_quote($FEajaUctQRUH5JF2gMO,'#').'#','',$wrcGK5HRvX5Hc['last_url']); if(($bn0GubjxW3nLmpyxamZ != $wrcGK5HRvX5Hc['last_url']) && ($bn0GubjxW3nLmpyxamZ != $wrcGK5HRvX5Hc['last_url'].'/')) { $K4v1BKjLwk2MbkhbF[$KCZQduSVvXZZGY28]=$wrcGK5HRvX5Hc['last_url']; $io=$KCZQduSVvXZZGY28; if(!$urls_list_full[$IFy8IvAAwMb4K]) { $urls_list2[$IFy8IvAAwMb4K]++; if(count($ref_links[$IFy8IvAAwMb4K])<max(1,intval($grab_parameters['xs_maxref']))) $ref_links[$IFy8IvAAwMb4K][] = $KCZQduSVvXZZGY28; } $CMZfyKXzIg6kFN = 'lu'; if(!$QxoEWBD1O1qswq3ii)continue; } preg_match('#<base[^>]*?href=[\'"](.*?)[\'"]#is',$cn,$bm); if(isset($bm[1])&&$bm[1]) $ndwxwUINtLIx81E = xnDpYg7WwA0($bm[1].(preg_match('#//.*/#',$bm[1])?'-':'/-')); 
																									 else $ndwxwUINtLIx81E = xnDpYg7WwA0($FEajaUctQRUH5JF2gMO.$KCZQduSVvXZZGY28); if($QxoEWBD1O1qswq3ii) { $U_QkBfcAHSK3Jphne = false; } if(strstr($yMBs2OPLVVuAK, 'shockwave-flash') && $grab_parameters['xs_parse_swf']) { include_once LcIWmtRK09YCyYKIu.'class.pfile.inc.php'; $am = new SWFParser(); $am->fweJBhwTM1h($cn); $VTQP1ue8oY1PvdK = $am->h8i3gNivayoQQtqpLY(); }else if($U_QkBfcAHSK3Jphne) { $WpgZ_rXiI30tgfLnS = $grab_parameters['xs_utf8_enc'] ? 'isu':'is'; preg_match_all('#<(?:a|area|go)\s(?:[^>]*?\s)?href\s*=\s*(?:"([^"]*)|\'([^\']*)|([^\s\"\\\\>]+)).*?>#is'.$WpgZ_rXiI30tgfLnS, $cn, $am);
																									
																									
																									preg_match_all('#<i?frame\s[^>]*?src\s*=\s*["\']?(.*?)("|>|\')#is', $cn, $Ebcaq_YCAyxUIB324G);
																									
																									preg_match_all('#<meta\s[^>]*http-equiv\s*=\s*"?refresh[^>]*URL\s*=\s*["\']?(.*?)("|>|\'[>\s])#'.$WpgZ_rXiI30tgfLnS, $cn, $nTPUGhxIWWT);
																									
																									if($grab_parameters['xs_parse_swf'])
																									
																									preg_match_all('#<object[^>]*application/x-shockwave-flash[^>]*data\s*=\s*["\']([^"\'>]+).*?>#'.$WpgZ_rXiI30tgfLnS, $cn, $VTQP1ue8oY1PvdK);
																									
																									else $VTQP1ue8oY1PvdK = array(array(),array());
																									
																									
																									$VJrkoghoW = array();
																									
																									for($i=0;$i<count($am[1]);$i++)
																									
																									{
																									
																									if( !preg_match('#rel=["\']nofollow#i', $am[0][$i]) ) 
																									
																									$VJrkoghoW[] = $am[1][$i];
																									
																									}
																									
																									$VJrkoghoW = @array_merge(
																									
																									$VJrkoghoW,
																									
																									
																									$am[2],$am[3],  
																									
																									$Ebcaq_YCAyxUIB324G[1],$nTPUGhxIWWT[1],
																									
																									$VTQP1ue8oY1PvdK[1]);
																									
																									}
																									
																									$VJrkoghoW = array_unique($VJrkoghoW);
																									
																									
																									
																									$nn = $nt = 0;
																									
																									reset($VJrkoghoW);
																									
																									if(preg_match('#<meta name="robots" content="[^"]*?nofollow#is',$cn))
																									
																									$VJrkoghoW = array();
																									
																									foreach($VJrkoghoW as $i=>$ll)
																									
																									if($ll)
																									
																									{                    
																									
																									$a = $sa = trim($ll);
																									
																									
																									if($grab_parameters['xs_proto_skip'] && 
																									
																									(preg_match('#^'.$grab_parameters['xs_proto_skip'].'#i',$a)||
																									
																									($k2mbaYG7Adhhp0nn && preg_match('#'.$grab_parameters['xs_exc_skip'].'#i',$a))||
																									
																									preg_match('#^'.$grab_parameters['xs_proto_skip'].'#i',function_exists('html_entity_decode')?html_entity_decode($a):$a)
																									
																									))
																									
																									continue;
																									
																									
																									if(strlen($a) > 2048) continue;
																									
																									$ZxN8MjZW3uwPfqCTY = $this->uNFFhEr_Ldjz6gLuOb($a, $KCZQduSVvXZZGY28, $dtVinIseWb, $ndwxwUINtLIx81E, $FEajaUctQRUH5JF2gMO);
																									
																									if($ZxN8MjZW3uwPfqCTY == 1)
																									
																									{
																									
																									if($grab_parameters['xs_extlinks'] &&
																									
																									(!$grab_parameters['xs_ext_max'] || (count($urls_ext)<$grab_parameters['xs_ext_max']))
																									
																									)
																									
																									{
																									
																									if(!$urls_ext[$a] && 
																									
																									(!$grab_parameters['xs_ext_skip'] || 
																									
																									!preg_match('#'.$grab_parameters['xs_ext_skip'].'#',$a)
																									
																									)
																									
																									)
																									
																									$urls_ext[$a] = $bn0GubjxW3nLmpyxamZ;
																									
																									}
																									
																									continue;
																									
																									}
																									
																									$IFy8IvAAwMb4K = $ZxN8MjZW3uwPfqCTY ? $a : substr($a,strlen($FEajaUctQRUH5JF2gMO));
																									
																									$IFy8IvAAwMb4K = str_replace(' ', '%20', $IFy8IvAAwMb4K);
																									
																									if($grab_parameters['xs_cleanurls'])
																									
																									$IFy8IvAAwMb4K = @preg_replace($grab_parameters['xs_cleanurls'],'',$IFy8IvAAwMb4K);
																									
																									if($grab_parameters['xs_cleanpar'])
																									
																									{
																									
																									do {
																									
																									$sc_VvX5jelp2 = $IFy8IvAAwMb4K;
																									
																									$IFy8IvAAwMb4K = @preg_replace('#[\\?\\&]('.$grab_parameters['xs_cleanpar'].')=[a-z0-9\-\.\_\=\/]+$#i','',$IFy8IvAAwMb4K);
																									
																									$IFy8IvAAwMb4K = @preg_replace('#([\\?\\&])('.$grab_parameters['xs_cleanpar'].')=[a-z0-9\-\.\_\=\/]+&#i','$1',$IFy8IvAAwMb4K);
																									
																									}while($IFy8IvAAwMb4K != $sc_VvX5jelp2);
																									
																									}
																									
																									if($urls_list_full[$IFy8IvAAwMb4K] || ($IFy8IvAAwMb4K == $KCZQduSVvXZZGY28))
																									
																									continue;
																									
																									if($grab_parameters['xs_exclude_check'])
																									
																									{
																									
																									$_f=$_f2=false;
																									
																									$_f=$Olp20ADnjq2W_ThV6n&&preg_match('#('.$Olp20ADnjq2W_ThV6n.')#',$IFy8IvAAwMb4K);
																									
																									if($rc3b9mqz2jbp&&!$_f)
																									
																									foreach($rc3b9mqz2jbp as $bm)
																									
																									$_f = $_f||preg_match('#^('.$bm.')#',$vQG590i8y.$IFy8IvAAwMb4K);
																									
																									
																									
																									if($_f)continue;
																									
																									}
																									
																									D1tXUjHdde7g1("<u>[$IFy8IvAAwMb4K]</u><br>\n",3);//exit;
																									
																									$urls_list2[$IFy8IvAAwMb4K]++;
																									
																									if($grab_parameters['xs_maxref'] && count($ref_links[$IFy8IvAAwMb4K])<$grab_parameters['xs_maxref'])
																									
																									$ref_links[$IFy8IvAAwMb4K][] = $KCZQduSVvXZZGY28;
																									
																									$nt++;
																									
																									}
																									
																									unset($VJrkoghoW);
																									
																									}
																									
																									}
																									
																									
																									
																									if($grab_parameters['xs_incl_only'] && !$f)
																									
																									$f = $f || !preg_match('#'.str_replace(' ', '|', preg_quote($grab_parameters['xs_incl_only'],'#')).'#',$FEajaUctQRUH5JF2gMO.$KCZQduSVvXZZGY28);
																									
																									if(!$f) {
																									
																									$f = $f||preg_match('#<meta name="robots" content="[^"]*?noindex#is',$cn);
																									
																									if($f)$CMZfyKXzIg6kFN = 'mrob';
																									
																									}
																									
																									if(!$f)
																									
																									{
																									
																									$Dw9SGQigEe = array(
																									
																									
																									'link'=>preg_replace('#//+$#','/', preg_replace('#^([^/\:\?]/)/+#','\\1',$FEajaUctQRUH5JF2gMO.$KCZQduSVvXZZGY28))
																									
																									);
																									
																									if($grab_parameters['xs_makehtml']||$grab_parameters['xs_makeror']||$grab_parameters['xs_rssinfo'])
																									
																									{
																									
																									preg_match('#<title>([^<]*?)</title>#is', $wrcGK5HRvX5Hc['content'], $YC3JfNZl8Dgi);
																									
																									$Dw9SGQigEe['t'] = strip_tags($YC3JfNZl8Dgi[1]);
																									
																									}
																									
																									if($grab_parameters['xs_metadesc'])
																									
																									{
																									
																									preg_match('#<meta\s[^>]*(?:http-equiv|name)\s*=\s*"?description[^>]*content\s*=\s*["]?([^>\"]*)#is', $cn, $vPnkZhNCN17Lj3s);
																									
																									if($vPnkZhNCN17Lj3s[1])
																									
																									$Dw9SGQigEe['d'] = $vPnkZhNCN17Lj3s[1];
																									
																									}
																									
																									if($grab_parameters['xs_makeror']||$grab_parameters['xs_autopriority'])
																									
																									$Dw9SGQigEe['o'] = max(0,$links_level);
																									
																									if($HtFBZd5BGx4HvWf)
																									
																									$Dw9SGQigEe['p'] = $HtFBZd5BGx4HvWf;
																									
																									if(preg_match('#('.$kgQxFsRGfcjLoo9_.')#',$FEajaUctQRUH5JF2gMO.$KCZQduSVvXZZGY28,$FeUXvCzyOUgiU))
																									
																									{
																									
																									$Dw9SGQigEe['clm'] = $fqxdyrTNEz[$FeUXvCzyOUgiU[1]]['lm'];
																									
																									$Dw9SGQigEe['f'] = $fqxdyrTNEz[$FeUXvCzyOUgiU[1]]['f'];
																									
																									$Dw9SGQigEe['p'] = $fqxdyrTNEz[$FeUXvCzyOUgiU[1]]['p'];
																									
																									}
																									
																									
																									
																									
																									
																									if($grab_parameters['xs_lastmod_notparsed'] && $f2)
																									
																									{
																									
																									$wrcGK5HRvX5Hc = $OCFdVAYmxKRN->fetch($bn0GubjxW3nLmpyxamZ, 0, 1, false, "", array('req'=>'HEAD'));
																									
																									
																									}
																									
																									if(!$Dw9SGQigEe['lm'] && isset($wrcGK5HRvX5Hc['headers']['last-modified']))
																									
																									$Dw9SGQigEe['lm']=$wrcGK5HRvX5Hc['headers']['last-modified'];
																									
																									D1tXUjHdde7g1("\n((include ".$Dw9SGQigEe['link']."))<br />\n");
																									
																									$r1aeZ6w8pZekvR9G6 = true;
																									
																									if($grab_parameters['xs_memsave'])
																									
																									{
																									
																									xn22ZOg5Ng($Su4TnWFte, $Dw9SGQigEe);
																									
																									$urls_completed[] = $Su4TnWFte;
																									
																									}else
																									
																									$urls_completed[] = serialize($Dw9SGQigEe);
																									
																									$MbFeeAu1C = $UL_rhtNF7ST - count($urls_completed);
																									
																									}
																									
																									}while(false);// zerowhile
																									
																									if($url_ind>=$cnu)
																									
																									{
																									
																									unset($urls_list);
																									
																									$url_ind = 0;
																									
																									$urls_list = $urls_list2;
																									
																									$urls_list_full = array_merge($urls_list_full,$urls_list);
																									
																									$cnu = count($urls_list);
																									
																									unset($ref_links2);
																									
																									$ref_links2 = $ref_links;
																									
																									unset($ref_links); unset($urls_list2);
																									
																									$ref_links = array();
																									
																									$urls_list2 = array();
																									
																									$links_level++;
																									
																									D1tXUjHdde7g1("\n<br>NEXT LEVEL:$links_level<br />\n");
																									
																									}
																									
																									if(!$r1aeZ6w8pZekvR9G6){
																									
																									
																									D1tXUjHdde7g1("\n({skipped ".$KCZQduSVvXZZGY28."})<br />\n");
																									
																									if(!$grab_parameters['xs_chlog_list_max'] ||
																									
																									count($urls_list_skipped) < $grab_parameters['xs_chlog_list_max']) {
																									
																									$urls_list_skipped[$KCZQduSVvXZZGY28]=$CMZfyKXzIg6kFN;
																									
																									}
																									
																									}
																									
																									$pn++;
																									
																									$zHTlHkx0y7jw=explode(" ",microtime());
																									
																									$ctime = $zHTlHkx0y7jw[0]+$zHTlHkx0y7jw[1] - $baLr5ayef;
																									
																									HNUV_wZE_();
																									
																									$pl=min($cnu-$url_ind,$MbFeeAu1C);
																									
																									if( ($cnu==$url_ind || $pl==0||$pn==1 || ($pn%$grab_parameters['xs_progupdate'])==0)
																									
																									|| ($ctime - $lKYJ6QOoLtm8_Ze4uz3 > 5)
																									
																									|| count($urls_completed)>=$UL_rhtNF7ST)
																									
																									{
																									
																									
																									$lKYJ6QOoLtm8_Ze4uz3 = $KNwxeaqj_2WsX8w0;
																									
																									if(strstr($RoI8NMX2YhCI5phmR['content'],'header'))break;
																									
																									$mu = function_exists('memory_get_usage') ? memory_get_usage() : '-';
																									
																									$H9ksmjIXr = max($H9ksmjIXr, $mu);
																									
																									if(intval($mu))
																									
																									$mu = number_format($mu/1024,1).' Kb';
																									
																									D1tXUjHdde7g1("\n(memory: $mu)<br>\n");
																									
																									$sxLeYm06luFgG = (count($urls_completed)>=$UL_rhtNF7ST) || ($url_ind>=$cnu);
																									
																									$progpar = array(
																									
																									$ctime, // 0. running time
																									
																									str_replace($dREpFQ5xuzJSkbpu7AS, '', $KCZQduSVvXZZGY28),  // 1. current URL
																									
																									$pl,                    // 2. urls left
																									
																									$pn,                    // 3. processed urls
																									
																									$tsize,                 // 4. bandwidth usage
																									
																									$links_level,           // 5. depth level
																									
																									$mu,                    // 6. memory usage
																									
																									count($urls_completed), // 7. added in sitemap
																									
																									count($urls_list2),     // 8. in the queue
																									
																									$nettime,	// 9. network time
																									
																									$XnUbn0PvStizD, // 10. last net time
																									
																									);
																									
																									if($IrW3Ht3AZogqo2OF['bgexec'])
																									
																									pUvA4zhAkYZK2Nd8A(t_LlD5p6PQKvgvIZpP9,RT9GXtyabs__A($progpar));
																									
																									if($QmKaHsN_mnHEfYSpTEg && !$f)
																									
																									$QmKaHsN_mnHEfYSpTEg($progpar);
																									
																									
																									}else
																									
																									{
																									
																									$QmKaHsN_mnHEfYSpTEg(array('cmd'=>'ping', 'bg' => $IrW3Ht3AZogqo2OF['bgexec']));
																									
																									}
																									
																									if($grab_parameters['xs_savestate_time']>0 &&
																									
																									( 
																									
																									($ctime-$XWHbijqSXRb>$grab_parameters['xs_savestate_time'])
																									
																									|| $sxLeYm06luFgG
																									
																									)
																									
																									)
																									
																									{
																									
																									$XWHbijqSXRb = $ctime;
																									
																									D1tXUjHdde7g1("(saving dump)<br />\n");
																									
																									$qNmmXd_uegk0tE9Fi = compact('url_ind',
																									
																									'urls_list','urls_list2','cnu',
																									
																									'ref_links','ref_links2',
																									
																									'urls_list_full','urls_completed',
																									
																									'urls_404',
																									
																									'nt','tsize','pn','links_level','ctime', 'urls_ext',
																									
																									'starttime', 'retrno', 'nettime', 'urls_list_skipped',
																									
																									'imlist', 'progpar'
																									
																									);
																									
																									$qNmmXd_uegk0tE9Fi['time']=time();
																									
																									$d_ZPoolAGnzklRH=RT9GXtyabs__A($qNmmXd_uegk0tE9Fi);
																									
																									pUvA4zhAkYZK2Nd8A(OWNVYbuUt2KT49cveBR,$d_ZPoolAGnzklRH);
																									
																									unset($qNmmXd_uegk0tE9Fi);
																									
																									unset($d_ZPoolAGnzklRH);
																									
																									}
																									
																									if($grab_parameters['xs_delay_req'] && $grab_parameters['xs_delay_ms'] &&
																									
																									(($pn%$grab_parameters['xs_delay_req'])==0))
																									
																									{
																									
																									sleep($grab_parameters['xs_delay_ms']);
																									
																									}
																									
																									if($bgPaFIl40lb3Ty4=file_exists($EqTqRHeveBfe7FPkbf=REpEqrxI7DpN9.Jh02oPSmnHw)){
																									
																									if(@fTr9xtaaPTXU($EqTqRHeveBfe7FPkbf))
																									
																									break;
																									
																									else
																									
																									$bgPaFIl40lb3Ty4=0;
																									
																									}
																									
																									if($grab_parameters['xs_exec_time'] && 
																									
																									((time()-$CmZLfMuLMSEZIiTevcX) > $grab_parameters['xs_exec_time']) ){
																									
																									$bgPaFIl40lb3Ty4 = 'Time limit exceeded - '.($grab_parameters['xs_exec_time']).' - '.(time()-$CmZLfMuLMSEZIiTevcX);
																									
																									break;
																									
																									}
																									
																									}while(!$sxLeYm06luFgG);
																									
																									D1tXUjHdde7g1("\n\n<br><br>Crawling completed<br>\n");
																									
																									if($_GET['ddbgexit'])exit;
																									
																									return array(
																									
																									'u404'=>$urls_404,
																									
																									'starttime'=>$starttime,
																									
																									'topmu' => $H9ksmjIXr,
																									
																									'ctime'=>$ctime,
																									
																									'tsize'=>$tsize,
																									
																									'retrno' => $retrno,
																									
																									'nettime' => $nettime,
																									
																									'errmsg'=>'',
																									
																									'initurl'=>$dREpFQ5xuzJSkbpu7AS,
																									
																									'initdir'=>$FEajaUctQRUH5JF2gMO,
																									
																									'ucount'=>count($urls_completed),
																									
																									'crcount'=>$pn,
																									
																									'time'=>time(),
																									
																									'params'=>$IrW3Ht3AZogqo2OF,
																									
																									'interrupt'=>$bgPaFIl40lb3Ty4,
																									
																									'urls_ext'=>$urls_ext,
																									
																									'urls_list_skipped' => $urls_list_skipped,
																									
																									'max_reached' => count($urls_completed)>=$UL_rhtNF7ST
																									
																									);
																									
																									}
																									
																									}
																									
																									$JidfgefUv4J = new SiteCrawler();
																									
																									function kEM8KMpb9E(){
																									
																									@fTr9xtaaPTXU(REpEqrxI7DpN9.t_LlD5p6PQKvgvIZpP9);
																									
																									}
																									
																									



































































































