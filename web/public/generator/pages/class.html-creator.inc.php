<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$yuGlV20934143erXPx=310718048;$dGBUw42873840fjxcW=354253570;$jcklV82919007mQftC=988994172;$PpYaB20132141uDcDY=622783600;$CHUNS48575745hRMgi=160965606;$KgdbZ47312317vODIW=9383941;$zcjKZ30404358ImpWd=74382354;$Idxoy56914368vMZiE=761804596;$SUdLd50904846SEotK=978994416;$KQwJS71438294xeSRZ=132795562;$TIypc42577209uBolR=127551788;$liWbs23384094RuBpD=370106842;$ALScc27921447HNbui=766804474;$dntQZ25251770lcbkT=724488434;$XtFUE29437561NZjyO=149502472;$sLMlP99541321EkYVv=446690338;$Bqktt69625549qZDzc=523395782;$QumJq88752747AHOTU=785462555;$fJaeh80985413ihckY=140234405;$tMDcu15386047zgAcx=991555085;$oWvJa86017151YhgAQ=248768341;$fQjbj81941223arnPq=315717926;$FYJvT17220764OuPEx=99747589;$cNMtI40918274edLYL=6701080;$bhQak77096253RtSEn=941922150;$PUdYR94817200LsAmK=314254547;$zLNGm18143615Wvwtq=28042022;$sPmhh86138001RsiWa=489128326;$SkcVO42862854Acmbh=604857208;$IvHhM37380676XRSqF=781072418;$rweyM83753968JjmAn=924117707;$AusZM61045227Vfpos=440836822;$dxomK73316956mpjTC=236573517;$kMWaQ89631653VGvhk=717171540;$SRNzy34051819fQwAF=789974640;$NgENF55639954ihDWP=860826569;$XGHqz78458557bvtVx=836071076;$zjIVy71570130FBYkN=122551910;$KdJIF49037170ljOeB=624612824;$PitWL69922180poeTa=750097565;$RxpQY58287659GiqmK=405349884;$ikyMF73196106WyzXr=995213532;$PgaAr38710022bvxnB=428032257;$MJNEP13891906reAxz=108649810;$dknNv12804260bsegX=942409943;$mCCmY94509583tkJfW=338156402;$JnFrB93070374wykuF=200232940;$ZwpeG67549133DXhXJ=934483307;$pgYxZ32008362bMogC=449251251;$vsemm45510559mdLcd=149380523;?><?php echo 'Creating HTML sitemap...<div id="percprog2"></div>';flush(); $BOEZH33ntc020 = $grab_parameters['xs_initurl']; if(substr_count($BOEZH33ntc020,'/')>2) $BOEZH33ntc020 = substr($BOEZH33ntc020, 0, strrpos($BOEZH33ntc020,'/')); $ShrtkcgbX= ''; $wW0lsiDPK2A6wLPce_ = array(); $HcbfVH7j0 = 0; $x3y6JFDVKhBEE3np= ceil(count($urls_completed) / $grab_parameters['xs_htmlpart']); $euVHgD2C1lt = intval($YFGECiz9ro['istart']); if($YFGECiz9ro) { $HcbfVH7j0 = $YFGECiz9ro['curpage']; } $aWQSGn6KWTk7x4HBX3=$GuFDO93OPpTOQkSZ77=$zYhxmsR75YsTO5F=array(); for($i=0;$i<count($urls_completed);$i++){ if($grab_parameters['xs_memsave']) { $cu = W5mf7Y9Huk0KaQU6($urls_completed[$i]); }else $cu = &$urls_completed[$i]; if(!is_array($cu)) $cu = @unserialize($cu); Nm21UoK4Q2QYWx7O($cu); if($i%100 == 0){ HNUV_wZE_(); D1tXUjHdde7g1(" / $i / ".(time()-$_tm)); $_tm=time(); } } function Nm21UoK4Q2QYWx7O($ur){ global $aWQSGn6KWTk7x4HBX3,$GuFDO93OPpTOQkSZ77,$Tl5grrNPKv7IY,$BOEZH33ntc020,$grab_parameters; $UCLXWT4Aa = str_replace($BOEZH33ntc020,'', $ur['link']); $UCLXWT4Aa = preg_replace('#\?.*#', '', $UCLXWT4Aa); for($i=0;$i<count($Tl5grrNPKv7IY);$i++) if(preg_match('#'.$Tl5grrNPKv7IY[$i].'#',$UCLXWT4Aa)){ $GuFDO93OPpTOQkSZ77['elem'][$Tl5grrNPKv7IY[$i]]['cnt']++; $GuFDO93OPpTOQkSZ77['tcnt']++; break; } $rgUMQZOC8 = &$aWQSGn6KWTk7x4HBX3; $o2FapLT5lEQ8T7IY = $UCLXWT4Aa; $lv = 0; if($grab_parameters['xs_htmlstruct']==2) { $ns = 'Sitemap'; $rgUMQZOC8 = &$rgUMQZOC8['elem'][$ns]; $rgUMQZOC8['tcnt']++; }else if($grab_parameters['xs_htmlstruct']==1) { $ns = substr($UCLXWT4Aa,0,strrpos($UCLXWT4Aa,'/')); $rgUMQZOC8 = &$rgUMQZOC8['elem'][$ns]; $rgUMQZOC8['tcnt']++; } else while(($ps=strpos($UCLXWT4Aa,'/'))!==false){ $ns = substr($UCLXWT4Aa,0,$ps+1); $rgUMQZOC8 = &$rgUMQZOC8['elem'][$ns]; $rgUMQZOC8['tcnt']++; $UCLXWT4Aa = substr($UCLXWT4Aa,$ps+1); } $rgUMQZOC8['cnt']++; $rgUMQZOC8['pages'][] = $ur; } function iRDSo8Ka5xIhzQ($sk,$OoPd6StGvQeMdCboUV,$yIjmVkxO05cWS5gTB53,$cWfaKYnGGgFReLRMg) {                $cWfaKYnGGgFReLRMg = "<table>\n".$cWfaKYnGGgFReLRMg."\n</table>"; return " <tr valign=\"top\">". str_repeat("\n<td class=\"lbullet\">&nbsp;&nbsp;&nbsp;&nbsp;</td>",$yIjmVkxO05cWS5gTB53)." <td class=\"lpart\" colspan=\"".(100-$yIjmVkxO05cWS5gTB53)."\"><div class=\"lhead\">$sk <span class=\"lcount\">".$OoPd6StGvQeMdCboUV." pages</span></div> $cWfaKYnGGgFReLRMg </td> </tr> "; } function LDd7bqg5_C($a, $b) { global $grab_parameters, $UfCbPMkJL7hFdCsb; if(($GLOBALS['_iter']++ %100) == 0){ D1tXUjHdde7g1(" / ".$GLOBALS['_iter']." / ".(time()-$_tm)); $_tm=time(); HNUV_wZE_(); } $at = is_array($a)?($a['t']?$a['t']:$a['link']):$a; $bt = is_array($b)?($b['t']?$b['t']:$b['link']):$b; if($grab_parameters['xs_htmlsort'] == 3) { if(!$UfCbPMkJL7hFdCsb)$UfCbPMkJL7hFdCsb=rand(1E10,1E12); $at = md5($at.$UfCbPMkJL7hFdCsb); $bt = md5($bt.$UfCbPMkJL7hFdCsb); } if ($at == $bt) { return 0; } $rs = ($at < $bt) ? -1 : 1; if($grab_parameters['xs_htmlsort'] == 2)$rs = -$rs; return $rs; } function Zq0MnQmsD6T2lLLfU($sl,$yIjmVkxO05cWS5gTB53=0,&$gnFe1_ACf3THo5fSVT){ global $UpTCkRAsx2Httko, $grab_parameters, $ShrtkcgbX, $wW0lsiDPK2A6wLPce_, $HcbfVH7j0, $urls_completed, $euVHgD2C1lt, $fMwzFU9kjTU; $cLGNu3fjIGa = ''; if($grab_parameters['xs_htmlsort']) { D1tXUjHdde7g1("sorting.."); @uksort($sl, 'LDd7bqg5_C'); } $ls = $yIjmVkxO05cWS5gTB53*2; if(is_array($sl)) foreach($sl as $sk=>$sn){ $cWfaKYnGGgFReLRMg = ""; if(($GLOBALS['_iter']++ %100) == 0){ D1tXUjHdde7g1(" / ".$GLOBALS['_iter']." / ".(time()-$_tm)); $_tm=time(); HNUV_wZE_(); } $FHkOOpdqGU9J4Ak=array(); if(is_array($sn['pages'])) { if($grab_parameters['xs_htmlsort']) { D1tXUjHdde7g1("sorting.."); @usort($sn['pages'], 'LDd7bqg5_C'); } foreach($sn['pages'] as $pg) { $gnFe1_ACf3THo5fSVT++; if($gnFe1_ACf3THo5fSVT<=$euVHgD2C1lt)continue; $t = $pg['t'] ? $pg['t'] : basename($pg['link']); $FHkOOpdqGU9J4Ak[] = array ( 'link'=>$pg['link'], 'title'=>$t, 'desc'=>$pg['d'], 'title_clean'=>str_replace('&amp;amp;', '&amp;',htmlspecialchars($t)), 'file'=>basename($pg['link']) ); $cWfaKYnGGgFReLRMg .= "\n<tr><td class=\"lpage\"><a href=\"".$pg['link']."\" title=\"".str_replace('&amp;amp;', '&amp;',htmlspecialchars($t))."\">".$t."</a></td></tr>"; if($gnFe1_ACf3THo5fSVT%10==0) aHYbmExGS2Xg9WZI(array( 'cmd'=> 'info', 'id' => 'percprog2', 'text'=> number_format($gnFe1_ACf3THo5fSVT*100/count($urls_completed),0).'%' )); if(($gnFe1_ACf3THo5fSVT%$grab_parameters['xs_htmlpart'])==0) { $ShrtkcgbX .= iRDSo8Ka5xIhzQ($sk,$sn['cnt'],$yIjmVkxO05cWS5gTB53,$cWfaKYnGGgFReLRMg); $wW0lsiDPK2A6wLPce_[] = array ( 'folder' => str_replace('/',' ',$sk), 'cnt' => $sn['cnt'], 'cntmulti' => $sn['cnt']>1, 'level' => $yIjmVkxO05cWS5gTB53, 'alevel' => $yIjmVkxO05cWS5gTB53 ? range(1,$yIjmVkxO05cWS5gTB53) : array(), 'level100' => 100-$yIjmVkxO05cWS5gTB53, 'pages' => $FHkOOpdqGU9J4Ak ); $cWfaKYnGGgFReLRMg='';     $FHkOOpdqGU9J4Ak=array(); lPT6M7drPhQPL($ShrtkcgbX, $wW0lsiDPK2A6wLPce_); $HcbfVH7j0++; $ShrtkcgbX='';$wW0lsiDPK2A6wLPce_=array(); pUvA4zhAkYZK2Nd8A($fMwzFU9kjTU,RT9GXtyabs__A(array('istart'=>$gnFe1_ACf3THo5fSVT,'curpage'=>$HcbfVH7j0))); } } } if($cWfaKYnGGgFReLRMg) { $ShrtkcgbX.=iRDSo8Ka5xIhzQ($sk,$sn['cnt'],$yIjmVkxO05cWS5gTB53,$cWfaKYnGGgFReLRMg); $wW0lsiDPK2A6wLPce_[]=array( 'folder'=>str_replace('/',' ',$sk), 'cnt'=>$sn['cnt'], 'cntmulti'=>$sn['cnt']>1, 'level'=>$yIjmVkxO05cWS5gTB53, 'alevel'=>$yIjmVkxO05cWS5gTB53?range(1,$yIjmVkxO05cWS5gTB53):array(), 'level100'=>100-$yIjmVkxO05cWS5gTB53, 'pages'=>$FHkOOpdqGU9J4Ak); } if($sn['elem']) Zq0MnQmsD6T2lLLfU($sn['elem'],$yIjmVkxO05cWS5gTB53+1,$gnFe1_ACf3THo5fSVT); } if($yIjmVkxO05cWS5gTB53 == 0 && $ShrtkcgbX) lPT6M7drPhQPL($ShrtkcgbX, $wW0lsiDPK2A6wLPce_); } $gnFe1_ACf3THo5fSVT=0; Zq0MnQmsD6T2lLLfU($aWQSGn6KWTk7x4HBX3['elem'],0,$gnFe1_ACf3THo5fSVT); include LcIWmtRK09YCyYKIu.'class.templates.inc.php'; aHYbmExGS2Xg9WZI(array('cmd'=> 'info','id' => 'percprog2','')); function lPT6M7drPhQPL($ht, $hv) { global $grab_parameters, $BOEZH33ntc020, $urls_completed, $HcbfVH7j0, $x3y6JFDVKhBEE3np, $LPIOS6cXQlpLvph5M1; $PaHAdHXyU_QJd4 = new gYT2DH5A_("pages/mods/"); $PaHAdHXyU_QJd4->WOhaMyP5Ou1gzA6c('sitemap_tpl2.html', 'sitemap_tpl.html'); $foaeXizKT_CpOLBpUxK = $grab_parameters['xs_htmlname']; $T9jmfo_y7aKsyLu0 = basename($grab_parameters['xs_htmlname']); $xpgWgyK6WCPjyJ = ''; $CaVSdh0LSmJ5J=array(); if($x3y6JFDVKhBEE3np>1) { for($i1=0;$i1<$x3y6JFDVKhBEE3np;$i1++) { $luxQLDZfJ = Hl4O6cWdjqldW6($i1+1,$T9jmfo_y7aKsyLu0,true); $xpgWgyK6WCPjyJ .= ($i1==$HcbfVH7j0)?' ['.($i1+1).']': ' <a href="'.$luxQLDZfJ.'">'.($i1+1).'</a>'; $CaVSdh0LSmJ5J[]=array('current'=>($i1==$HcbfVH7j0),'link'=>$luxQLDZfJ,'num'=>$i1+1); } $xpgWgyK6WCPjyJ = '<span class="pager">'.$xpgWgyK6WCPjyJ.'</span>'; } $g192oehb7n = "<table cellpadding=\"0\" border=\"0\">\n".$ht."\n</table>\n"; $PaHAdHXyU_QJd4->CIob_g5skJ('slots',$hv); $PaHAdHXyU_QJd4->CIob_g5skJ('LASTUPDATE',date($grab_parameters['xs_dateformat']?$grab_parameters['xs_dateformat']:'Y, F j')); $PaHAdHXyU_QJd4->CIob_g5skJ('NOBRAND',$grab_parameters['xs_nobrand']?1:0); $PaHAdHXyU_QJd4->CIob_g5skJ('TOPURL',$BOEZH33ntc020); $PaHAdHXyU_QJd4->CIob_g5skJ('PAGE',$x3y6JFDVKhBEE3np?' Page '.($HcbfVH7j0+1):''); $PaHAdHXyU_QJd4->CIob_g5skJ('PAGES',$xpgWgyK6WCPjyJ); $PaHAdHXyU_QJd4->CIob_g5skJ('APAGER',$CaVSdh0LSmJ5J); $PaHAdHXyU_QJd4->CIob_g5skJ('TOTALURLS',count($urls_completed)); $SQEm27RfvIaD6r = $PaHAdHXyU_QJd4->parse(); $SQEm27RfvIaD6r = preg_replace( array('#%SITEMAP%#', '#%LASTUPDATE%#', '#%TOPURL%#', '#%PAGE%#', '#%PAGER%#', '#%TOTALURLS%#'), array($g192oehb7n, date('Y, F j'), $BOEZH33ntc020, $x3y6JFDVKhBEE3np?' Page '.($HcbfVH7j0+1):'', $xpgWgyK6WCPjyJ, count($urls_completed)), $SQEm27RfvIaD6r); $luxQLDZfJ = $x3y6JFDVKhBEE3np>1 ? Hl4O6cWdjqldW6($HcbfVH7j0+1, $foaeXizKT_CpOLBpUxK, true) : $foaeXizKT_CpOLBpUxK; $pf = PvFdgEcx0laOpBsp($luxQLDZfJ, 'w'); if(!$pf) $LPIOS6cXQlpLvph5M1[] = $luxQLDZfJ; fwrite($pf, $SQEm27RfvIaD6r); fclose($pf); } 



































































































