<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$CVdyc39437866DyrEQ=558791260;$APugj83306275ZYpem=896465943;$iMpmy27858276OkEfo=959025391;$czxWp55906372RhDDc=527938355;$DZoOy35263061QmpUP=883173584;$qwxOe58740845ZlggS=807199829;$iAQSO84152222TohFd=580985840;$CXxNt24309692eIGKq=985000367;$vondq17025756onqrd=302212158;$zlIOR65112915sZTao=312089966;$Zrffv36383667mPPOV=296602539;$UjzqK23650512quBnE=37218627;$HFjei74725952FwESv=813906983;$BjSjD12422485gOugb=410136352;$sURSc64552612aFSAw=105875488;$RbdUt53928833vLsrt=681593140;$DGmXm28363647xlFBq=420258057;$JuwcG80669556PWZeL=102338989;$mQfnU78659058pmLxT=8804687;$KmkAo25144653dzBQR=920123902;$YtkSe57938843NBGce=120265380;$HSopn89854126RmSOV=387697876;$zHmle78703003MFfgU=5390136;$yxyOB27297973OiGGw=752810913;$tHxAS73451538QfmkR=912928955;$qxgEA39976196XEwuu=267213012;$MsowP64684448HLYQu=95631836;$LuBtb60388794mMSCb=179654174;$fHeNS74901734qjXzE=800248780;$bFzQM21035766LLbcg=739884400;$dgdBE36603393QpUrt=279529785;$QLGtP34417114FByZA=199653686;$juplr62289429mGwAM=781224854;$uevrD33032837MBrEc=806712036;$aECNU84459839khzMW=557083984;$EuRPU39382934EMTPk=812809449;$XDGQm35614624VXXLC=855857178;$wiblz75967407JfCEQ=467695923;$yoMrV28253784XVSjv=928294434;$FxdZf75286255FufaE=21121459;$hASCo84877320sUbZf=25145751;$itvBI59839478BKMzT=721836060;$TuWhb47985229WlqAF=394161133;$gmeOP52127075makAA=821589722;$ajaxw30077514KBHxo=287090576;$nZxgI74649048IhheU=570132446;$GvERp53654175irCFI=952684082;$NbJeF59905396OPalu=217214233;$oNaMu51215210rAHgm=642691651;$qmdla30396118FZHEU=12585083;?><?php if(!class_exists('XMLCreator')) { class XMLCreator { var $YO9KYM6L6wDupmsp  = array(); var $O8RE2k9RCjD3PxF_MJ = array('xml','','','','mobile'); var $IrW3Ht3AZogqo2OF = array(); var $n5r9fzJl7ca = array(),  $E09KaZe4IW = array(),  $Zwf4bxMTE8xK8E = array(); var $CjkPFJQ1MJyQxogX = 1000; function nue1JSywc(&$RZJrs4e9DWqT) { $ByzJ33WTH1 = false; if(is_array($RZJrs4e9DWqT)) foreach($RZJrs4e9DWqT as $k=>$v){ if(strlen($k)>200){ $ByzJ33WTH1 = true; $RZJrs4e9DWqT[$k] = substr($v, 0, 200); } } } function AUsclCf2r5Z($IrW3Ht3AZogqo2OF, $urls_completed, $jmTPqK4UHmexxwYuYDf) { global $PftdNZ9fEP0Z1yoaR, $LPIOS6cXQlpLvph5M1; $LPIOS6cXQlpLvph5M1 = array();    $this->PaHAdHXyU_QJd4 = new gYT2DH5A_("pages/"); $this->IrW3Ht3AZogqo2OF = $IrW3Ht3AZogqo2OF; if($this->IrW3Ht3AZogqo2OF['xs_chlog_list_max']) $this->CjkPFJQ1MJyQxogX = $this->IrW3Ht3AZogqo2OF['xs_chlog_list_max'];  $foaeXizKT_CpOLBpUxK = basename($this->IrW3Ht3AZogqo2OF['xs_smname']); $this->uurl_p = dirname($this->IrW3Ht3AZogqo2OF['xs_smurl']).'/'; $this->furl_p = dirname($this->IrW3Ht3AZogqo2OF['xs_smname']).'/'; $this->imgno = 0; $this->HF6NqOcvPqnbh7 = $this->IrW3Ht3AZogqo2OF['xs_compress'] ? '.gz' : ''; $this->n5r9fzJl7ca = $this->E09KaZe4IW = $this->urls_prevrss = array(); if($this->IrW3Ht3AZogqo2OF['xs_chlog']) $this->n5r9fzJl7ca = $this->YD8Ku5kCxh9EuzlBTRB($foaeXizKT_CpOLBpUxK); if($this->IrW3Ht3AZogqo2OF['xs_rssinfo']) $this->urls_prevrss = $this->YD8Ku5kCxh9EuzlBTRB($this->IrW3Ht3AZogqo2OF['xs_rssfilename'], $this->IrW3Ht3AZogqo2OF['xs_rssage'], false, 1); if($this->IrW3Ht3AZogqo2OF['xs_newsinfo']) $this->E09KaZe4IW = $this->YD8Ku5kCxh9EuzlBTRB($this->IrW3Ht3AZogqo2OF['xs_newsfilename'], $this->IrW3Ht3AZogqo2OF['xs_newsage']); $OG_mULAWKT_xD4fsdPN = $r6DB_IVvhDlkKfN53 = array(); $this->A18lnbzsiL = $this->IrW3Ht3AZogqo2OF['xs_compress'] ? array('fopen' => 'gzopen', 'fwrite' => 'gzwrite', 'fclose' => 'gzclose' ) : array('fopen' => 'PvFdgEcx0laOpBsp', 'fwrite' => 'fwrite', 'fclose' => 'fclose' ) ; $FAzOemScryy = strstr($this->IrW3Ht3AZogqo2OF['xs_initurl'],'://www.');
																										 $HxBreJLE_R = $PftdNZ9fEP0Z1yoaR.'/'; if(strstr($this->IrW3Ht3AZogqo2OF['xs_initurl'],'https:')) $HxBreJLE_R = str_replace('http:', 'https:', $HxBreJLE_R); $sqj4yHCj873BP = strstr($HxBreJLE_R,'://www.');
																										 $p1 = parse_url($this->IrW3Ht3AZogqo2OF['xs_initurl']); $p2 = parse_url($HxBreJLE_R); if(str_replace('www.', '', $p1['host'])==str_replace('www.', '', $p2['host']))  { if($FAzOemScryy && !$sqj4yHCj873BP)$HxBreJLE_R = str_replace('://', '://www.', $HxBreJLE_R);
																										 if(!$FAzOemScryy && $sqj4yHCj873BP)$HxBreJLE_R = str_replace('://www.', '://', $HxBreJLE_R);
																										 } $this->IrW3Ht3AZogqo2OF['gendom'] = $HxBreJLE_R; $this->A9WzwJYtTB0O3GkI69($urls_completed, $OG_mULAWKT_xD4fsdPN); $this->fslY6ssCNGE(); if($this->IrW3Ht3AZogqo2OF['xs_chlog']) { $KJwBTVXJeYjG  = array_keys($this->Zwf4bxMTE8xK8E); $AwcDkUITo4cg = array_slice(array_keys($this->n5r9fzJl7ca), 0, $this->CjkPFJQ1MJyQxogX); } if($this->imgno)$this->YO9KYM6L6wDupmsp[1]['xn'] = $this->imgno; if($this->videos_no)$this->YO9KYM6L6wDupmsp[2]['xn'] = $this->videos_no; if($this->news_no)$this->YO9KYM6L6wDupmsp[3]['xn'] = $this->news_no; $dr1lN9apl = array_merge($jmTPqK4UHmexxwYuYDf, array( 'files'   => array(), 'rinfo'   => $this->YO9KYM6L6wDupmsp, 'newurls' => $this->nue1JSywc($KJwBTVXJeYjG), 'losturls'=> $this->nue1JSywc($AwcDkUITo4cg), 'urls_ext'=> $jmTPqK4UHmexxwYuYDf['urls_ext'], 'images_no'  => $this->imgno, 'videos_no' => $this->videos_no, 'news_no'  => $this->newsno, 'rss_no'  => $this->rssno, 'rss_sm'  => $this->IrW3Ht3AZogqo2OF['xs_rssfilename'], 'fail_files' => $LPIOS6cXQlpLvph5M1, 'create_time' => time() )); $tUOd0ODXTNxNVUNnX9o = array('u404', 'urls_ext', 'urls_list_skipped', 'newurls', 'losturls'); foreach($tUOd0ODXTNxNVUNnX9o as $ca) $this->nue1JSywc($dr1lN9apl[$ca]); $BiONb5bgRD = date('Y-m-d H-i-s').'.log'; pUvA4zhAkYZK2Nd8A($BiONb5bgRD,serialize($dr1lN9apl)); $this->n5r9fzJl7ca = $this->Zwf4bxMTE8xK8E = $this->E09KaZe4IW = $this->urls_prevrss = array(); $OG_mULAWKT_xD4fsdPN = array(); return $dr1lN9apl; } function JAvBiMmjzH($pf) { global $CTzQtm2FnXsJI4f_hM; if(!$pf)return; $this->A18lnbzsiL['fwrite']($pf, $CTzQtm2FnXsJI4f_hM[3]); $this->A18lnbzsiL['fclose']($pf); } function Ufz4hdNjXhLx60oBsLt($pf, $M4MpKAUCS4w3) { global $CTzQtm2FnXsJI4f_hM; if(!$pf)return; $xs = $this->PaHAdHXyU_QJd4->SKg9CDNyrraUeOE5uUB($CTzQtm2FnXsJI4f_hM[1], array('TYPE'.$M4MpKAUCS4w3=>true)); $this->A18lnbzsiL['fwrite']($pf, $xs); } function F8eE_Rqcrb($r6DB_IVvhDlkKfN53) { $rCRb02h4n8Q = ""; $JlJYKIIrEYn6 = implode('', file(dlUE6X_RWe.'sitemap_index_tpl.xml')); preg_match('#^(.*)%SITEMAPS_LIST_FROM%(.*)%SITEMAPS_LIST_TO%(.*)$#is', $JlJYKIIrEYn6, $i64kV7HwR5y); $i64kV7HwR5y[1] = str_replace('%GEN_URL%', $this->IrW3Ht3AZogqo2OF['gendom'], $i64kV7HwR5y[1]); $ECiIdFEhSN3p79 = preg_replace('#[^\\/]+?\.xml$#', '', $this->IrW3Ht3AZogqo2OF['xs_smurl']); $i64kV7HwR5y[1] = str_replace('%SM_BASE%', $ECiIdFEhSN3p79, $i64kV7HwR5y[1]); for($i=0;$i<count($r6DB_IVvhDlkKfN53);$i++) $rCRb02h4n8Q.= $this->PaHAdHXyU_QJd4->SKg9CDNyrraUeOE5uUB($i64kV7HwR5y[2], array( 'URL'=>$r6DB_IVvhDlkKfN53[$i], 'LASTMOD'=>date('Y-m-d\TH:i:s+00:00') )); return $i64kV7HwR5y[1] . $rCRb02h4n8Q . $i64kV7HwR5y[3]; } function nNMzp2IgH7HiM($DJddRF2VRL6C, $lrwvB0xvcXXumi3V = false) { $t = $lrwvB0xvcXXumi3V ? htmlspecialchars($DJddRF2VRL6C) : str_replace("&", "&amp;", $DJddRF2VRL6C); if(function_exists('utf8_encode') && !$this->IrW3Ht3AZogqo2OF['xs_utf8']) { $t = utf8_encode($t); } return $t; } function T9mnmC3k9eENcJDb44Q($SXuHGf1Vd) { global $lrwvB0xvcXXumi3V; $l = str_replace("&amp;", "&", $SXuHGf1Vd); $l = str_replace("&", "&amp;", $l); $l = strtr($l, $lrwvB0xvcXXumi3V); if($this->IrW3Ht3AZogqo2OF['xs_utf8']) { }else if(function_exists('utf8_encode')) $l = utf8_encode($l); return $l; } function NYySB95cXj2bdre($IYxAjJo0uTeBRoqu) { $eA1uPQAzcVCQ8 = array( basename($this->IrW3Ht3AZogqo2OF['xs_smname']),  $this->IrW3Ht3AZogqo2OF['xs_imgfilename'], $this->IrW3Ht3AZogqo2OF['xs_videofilename'], $this->IrW3Ht3AZogqo2OF['xs_newsfilename'], $this->IrW3Ht3AZogqo2OF['xs_mobilefilename'], ); if($IYxAjJo0uTeBRoqu['rinfo']) $this->YO9KYM6L6wDupmsp = $IYxAjJo0uTeBRoqu['rinfo']; foreach($this->O8RE2k9RCjD3PxF_MJ as $M4MpKAUCS4w3=>$EA_T0CYlIF6tb7UtP) if($EA_T0CYlIF6tb7UtP) { $this->YO9KYM6L6wDupmsp[$M4MpKAUCS4w3]['sitemap_file'] = $eA1uPQAzcVCQ8[$M4MpKAUCS4w3]; $this->YO9KYM6L6wDupmsp[$M4MpKAUCS4w3]['filenum'] = intval($IYxAjJo0uTeBRoqu['istart']/$this->dFOsOrGrftDYFF)+1; if(!$IYxAjJo0uTeBRoqu['istart']) $this->W4Xzu7_XRKxGlHA($eA1uPQAzcVCQ8[$M4MpKAUCS4w3]); } } function HuD2wJN0EuqGM6r() { global $LPIOS6cXQlpLvph5M1; $qyrBCAYqEMp = 0; $l = false; foreach($this->O8RE2k9RCjD3PxF_MJ as $M4MpKAUCS4w3=>$EA_T0CYlIF6tb7UtP) { $ri = &$this->YO9KYM6L6wDupmsp[$M4MpKAUCS4w3]; $Qv8G6NWw9S0ZcPxVV = (($ri['xnp'] % $this->dFOsOrGrftDYFF) == 0) && ($ri['xnp'] || !$ri['pf']); $l|=$Qv8G6NWw9S0ZcPxVV; if($this->sm_filesplit && $ri['xchs'] && $ri['xnp']) $Qv8G6NWw9S0ZcPxVV |= ($ri['xchs']/$ri['xnp']*($ri['xnp']+1)>$this->sm_filesplit); if( $Qv8G6NWw9S0ZcPxVV ) { $qyrBCAYqEMp++; $ri['xchs'] = $ri['xnp'] = 0; $this->JAvBiMmjzH($ri['pf']); if($ri['filenum'] == 2) { if(!copy(REpEqrxI7DpN9 . $ri['sitemap_file'].$this->HF6NqOcvPqnbh7,  REpEqrxI7DpN9.($_xu = Hl4O6cWdjqldW6(1,$ri['sitemap_file']).$this->HF6NqOcvPqnbh7))) { $LPIOS6cXQlpLvph5M1[] = REpEqrxI7DpN9.$_xu; } $ri['urls'][0] = $this->uurl_p . $_xu; } $PSyERNx_fX3yH75 = (($ri['filenum']>1) ? Hl4O6cWdjqldW6($ri['filenum'],$ri['sitemap_file']) :$ri['sitemap_file']) . $this->HF6NqOcvPqnbh7; $ri['urls'][] = $this->uurl_p . $PSyERNx_fX3yH75; $ri['filenum']++; $ri['pf'] = $this->A18lnbzsiL['fopen'](REpEqrxI7DpN9.$PSyERNx_fX3yH75,'w'); if(!$ri['pf']) $LPIOS6cXQlpLvph5M1[] = REpEqrxI7DpN9.$PSyERNx_fX3yH75; $this->Ufz4hdNjXhLx60oBsLt($ri['pf'], $M4MpKAUCS4w3); } } return $l; } function XNUfCNwhY4T($LG9QHtb6yK6qR5InZ, $CTzQtm2FnXsJI4f_hM, $M4MpKAUCS4w3) { $LG9QHtb6yK6qR5InZ['TYPE'.$M4MpKAUCS4w3] = true; $ri = &$this->YO9KYM6L6wDupmsp[$M4MpKAUCS4w3]; if($ri['pf']) { $_xu = $this->PaHAdHXyU_QJd4->SKg9CDNyrraUeOE5uUB($CTzQtm2FnXsJI4f_hM, $LG9QHtb6yK6qR5InZ); $ri['xchs'] += strlen($_xu); $ri['xn']++; $ri['xnp']++; $this->A18lnbzsiL['fwrite']($ri['pf'], $_xu); } }  function mVTd3cRsYoUPyznbMc() { foreach($this->YO9KYM6L6wDupmsp as $M4MpKAUCS4w3=>$ri) { $this->JAvBiMmjzH($ri['pf']); } } function fslY6ssCNGE() { foreach($this->O8RE2k9RCjD3PxF_MJ as $M4MpKAUCS4w3=>$EA_T0CYlIF6tb7UtP) { $ri = &$this->YO9KYM6L6wDupmsp[$M4MpKAUCS4w3]; if(count($ri['urls'])>1) { $xf = $this->F8eE_Rqcrb($ri['urls']); array_unshift($ri['urls'],  $this->uurl_p.pUvA4zhAkYZK2Nd8A($ri['sitemap_file'], $xf, REpEqrxI7DpN9, $this->IrW3Ht3AZogqo2OF['xs_compress']) ); } $this->rO1mQuOaCrNFolA0Y($ri['sitemap_file']); } } function zdUTg22HNObKCXmp($jnlmADM5fBz3W9) { 
																										return $jnlmADM5fBz3W9;
																										}
																										function A9WzwJYtTB0O3GkI69($urls_completed, &$OG_mULAWKT_xD4fsdPN)
																										{
																										global $CTzQtm2FnXsJI4f_hM, $szdudf1EIIL, $yQTjPXCGcUnHW, $sm_proc_list, $IYxAjJo0uTeBRoqu, $pUrCgVuyLqY6_hjcC0, $LPIOS6cXQlpLvph5M1;
																										$C8YeeJvXHpz7ff = $this->IrW3Ht3AZogqo2OF['xs_chlog'];
																										$NEd4p7VjgbWpEWv8 = file_exists(dlUE6X_RWe.'sitemap_xml_tpl2.xml') ? 'sitemap_xml_tpl2.xml' : 'sitemap_xml_tpl.xml';
																										$JlJYKIIrEYn6 = implode('', file(dlUE6X_RWe.$NEd4p7VjgbWpEWv8));
																										preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $JlJYKIIrEYn6, $CTzQtm2FnXsJI4f_hM);
																										$CTzQtm2FnXsJI4f_hM[1] = str_replace('www.xml-sitemaps.com', 'www.xml-sitemaps.com ('. uCIu22Zx7O4C0vkg_.')', $CTzQtm2FnXsJI4f_hM[1]);
																										$CTzQtm2FnXsJI4f_hM[1] = str_replace('%GEN_URL%', $this->IrW3Ht3AZogqo2OF['gendom'], $CTzQtm2FnXsJI4f_hM[1]);
																										$ECiIdFEhSN3p79 = preg_replace('#[^\\/]+?\.xml$#', '', $this->IrW3Ht3AZogqo2OF['xs_smurl']);
																										$CTzQtm2FnXsJI4f_hM[1] = str_replace('%SM_BASE%', $ECiIdFEhSN3p79, $CTzQtm2FnXsJI4f_hM[1]);
																										if($this->IrW3Ht3AZogqo2OF['xs_disable_xsl'])
																										$CTzQtm2FnXsJI4f_hM[1] = preg_replace('#<\?xml-stylesheet.*\?>#', '', $CTzQtm2FnXsJI4f_hM[1]);
																										if($this->IrW3Ht3AZogqo2OF['xs_nobrand'])
																										$CTzQtm2FnXsJI4f_hM[1] = str_replace('sitemap.xsl','sitemap_nb.xsl',$CTzQtm2FnXsJI4f_hM[1]);
																										$uimXdkmdPAzeMTg1 = implode('', file(dlUE6X_RWe.'sitemap_ror_tpl.xml'));
																										preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $uimXdkmdPAzeMTg1, $szdudf1EIIL);
																										$x1Nsplkpc2Kdk5W = implode('', file(dlUE6X_RWe.'sitemap_rss_tpl.xml'));
																										preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $x1Nsplkpc2Kdk5W, $JtLwsGHOAgAKL7G);
																										$fRRNOswmJ = implode('', file(dlUE6X_RWe.'sitemap_base_tpl.xml'));
																										preg_match('#^(.*)%URLS_LIST_FROM%(.*)%URLS_LIST_TO%(.*)$#is', $fRRNOswmJ, $yQTjPXCGcUnHW);
																										$this->dFOsOrGrftDYFF = $this->IrW3Ht3AZogqo2OF['xs_sm_size']?$this->IrW3Ht3AZogqo2OF['xs_sm_size']:50000;
																										$this->sm_filesplit = $this->IrW3Ht3AZogqo2OF['xs_sm_filesize']?$this->IrW3Ht3AZogqo2OF['xs_sm_filesize']:10;
																										$this->sm_filesplit = max(intval($this->sm_filesplit*1024*1024),2000)-1000;
																										if(!$this->IrW3Ht3AZogqo2OF['xs_imginfo'])
																										unset($this->O8RE2k9RCjD3PxF_MJ[1]);
																										if(!$this->IrW3Ht3AZogqo2OF['xs_videoinfo'])
																										unset($this->O8RE2k9RCjD3PxF_MJ[2]);
																										if(!$this->IrW3Ht3AZogqo2OF['xs_newsinfo'])
																										unset($this->O8RE2k9RCjD3PxF_MJ[3]);
																										if(!$this->IrW3Ht3AZogqo2OF['xs_makemob'])
																										unset($this->O8RE2k9RCjD3PxF_MJ[4]);
																										if(!$this->IrW3Ht3AZogqo2OF['xs_rssinfo'])
																										unset($this->O8RE2k9RCjD3PxF_MJ[5]);
																										$ctime = date('Y-m-d H:i:s');
																										$K2dHWcwY5bEP = 0;
																										global $lrwvB0xvcXXumi3V;
																										$tt = array('<','>');
																										foreach ($tt as $TS2WAQ4zWkdnI_ )
																										$lrwvB0xvcXXumi3V[$TS2WAQ4zWkdnI_] = '&#'.ord($TS2WAQ4zWkdnI_).';';
																										for($i=0;$i<31;$i++)
																										$lrwvB0xvcXXumi3V[chr($i)] = '&#'.$i.';';
																										$lrwvB0xvcXXumi3V[chr(0)] = $lrwvB0xvcXXumi3V[chr(10)] = $lrwvB0xvcXXumi3V[chr(13)] = '';
																										$lrwvB0xvcXXumi3V[' '] = '%20';
																										$pf = 0;
																										
																										$e5V6M6TL5 = intval($IYxAjJo0uTeBRoqu['istart']);
																										$this->NYySB95cXj2bdre($IYxAjJo0uTeBRoqu);
																										if($this->IrW3Ht3AZogqo2OF['xs_maketxt'])
																										{
																										$HewsHj0TO = $this->A18lnbzsiL['fopen'](yyq7fDoK_cBPACC6n1.$this->HF6NqOcvPqnbh7, $e5V6M6TL5?'a':'w');
																										if(!$HewsHj0TO)$LPIOS6cXQlpLvph5M1[] = yyq7fDoK_cBPACC6n1.$this->HF6NqOcvPqnbh7;
																										}
																										if($this->IrW3Ht3AZogqo2OF['xs_makeror'])
																										{
																										$mUFi_j4mv4J = PvFdgEcx0laOpBsp(yCwTqe5GDcta, $e5V6M6TL5?'a':'w');
																										$rc = str_replace('%INIT_URL%', $this->IrW3Ht3AZogqo2OF['xs_initurl'], $szdudf1EIIL[1]);
																										if($mUFi_j4mv4J)
																										fwrite($mUFi_j4mv4J, $rc);
																										else
																										$LPIOS6cXQlpLvph5M1[] = yCwTqe5GDcta;
																										}
																										if($this->IrW3Ht3AZogqo2OF['xs_rssinfo'])
																										{
																										$iwe5WfZrZZ6OjkL1yFV = fSB9ZrUIK4aICK6XAM;
																										$VH_xpFhmDF6bO5F = PvFdgEcx0laOpBsp($iwe5WfZrZZ6OjkL1yFV, $e5V6M6TL5?'a':'w');
																										$rc = str_replace('%INIT_URL%', $this->IrW3Ht3AZogqo2OF['xs_initurl'], $JtLwsGHOAgAKL7G[1]);
																										$rc = str_replace('%FEED_TITLE%', $this->IrW3Ht3AZogqo2OF['xs_rsstitle'], $rc);
																										$rc = str_replace('%BUILD_DATE%', $ctime, $rc);
																										if($VH_xpFhmDF6bO5F)
																										fwrite($VH_xpFhmDF6bO5F, $rc);
																										else
																										$LPIOS6cXQlpLvph5M1[] = $iwe5WfZrZZ6OjkL1yFV;
																										}
																										if($sm_proc_list)
																										foreach($sm_proc_list as $k=>$u0pk35zQbvIj4)
																										$sm_proc_list[$k]->wBZSjAu6USLv869OfXK($this->IrW3Ht3AZogqo2OF, $this->A18lnbzsiL, $this->PaHAdHXyU_QJd4);
																										if($this->IrW3Ht3AZogqo2OF['xs_write_delay'])
																										list($BdQWNTcmByBXtJ, $tKVE8xlfmzSWy) = explode('|',$this->IrW3Ht3AZogqo2OF['xs_write_delay']);
																										for($i=$xn=$e5V6M6TL5;$i<count($urls_completed);$i++,$xn++)
																										{   
																										
																										
																										
																										if($i%100 == 0) {
																										HNUV_wZE_();
																										D1tXUjHdde7g1(" / $i / ".(time()-$_tm));
																										$_tm=time();
																										}
																										aHYbmExGS2Xg9WZI(array(
																										'cmd'=> 'info',
																										'id' => 'percprog',
																										'text'=> number_format($i*100/count($urls_completed),0).'%'
																										));
																										$qyrBCAYqEMp = $this->HuD2wJN0EuqGM6r();
																										if($qyrBCAYqEMp && ($i != $e5V6M6TL5))
																										{
																										pUvA4zhAkYZK2Nd8A($pUrCgVuyLqY6_hjcC0,RT9GXtyabs__A(array('istart'=>$i,'rinfo'=>$this->YO9KYM6L6wDupmsp)));
																										}
																										if($this->IrW3Ht3AZogqo2OF['xs_memsave'])
																										{
																										$cu = W5mf7Y9Huk0KaQU6($urls_completed[$i]);
																										}else
																										$cu = $urls_completed[$i];
																										if(!is_array($cu)) $cu = @unserialize($cu);
																										$l = $this->T9mnmC3k9eENcJDb44Q($cu['link']);
																										$cu['link'] = $l;
																										$t = $this->nNMzp2IgH7HiM($cu['t']);
																										$d = $this->nNMzp2IgH7HiM($cu['d'] ? $cu['d'] : $cu['t'], true);
																										$JfRC0egNhxmTca = '';
																										if($cu['clm'])
																										$JfRC0egNhxmTca = $cu['clm'];
																										else
																										switch($this->IrW3Ht3AZogqo2OF['xs_lastmod']){
																										case 1:$JfRC0egNhxmTca = $cu['lm']?$cu['lm']:$ctime;break;
																										case 2:$JfRC0egNhxmTca = $ctime;break;
																										case 3:$JfRC0egNhxmTca = $this->IrW3Ht3AZogqo2OF['xs_lastmodtime'];break;
																										}
																										$nU8Mj5ZUWIEjvw = $YWmYVMCd_Z1Jkw = false;
																										if($cu['p'])
																										$p = $cu['p'];
																										else
																										{
																										$p = $this->IrW3Ht3AZogqo2OF['xs_priority'];
																										if($this->IrW3Ht3AZogqo2OF['xs_autopriority'])
																										{
																										$p = $p*pow($this->IrW3Ht3AZogqo2OF['xs_descpriority']?$this->IrW3Ht3AZogqo2OF['xs_descpriority']:0.8,$cu['o']);
																										if($this->n5r9fzJl7ca)
																										{
																										$nU8Mj5ZUWIEjvw = true;
																										$YWmYVMCd_Z1Jkw = ($this->n5r9fzJl7ca&&!isset($this->n5r9fzJl7ca[$cu['link']]))||$this->E09KaZe4IW[$cu['link']];
																										if($YWmYVMCd_Z1Jkw)
																										$p=0.95;
																										}
																										$p = max(0.0001,min($p,1.0));
																										$p = @number_format($p, 4);
																										}
																										}
																										if($JfRC0egNhxmTca){
																										$JfRC0egNhxmTca = strtotime($JfRC0egNhxmTca);
																										$JfRC0egNhxmTca = gmdate('Y-m-d\TH:i:s+00:00',$JfRC0egNhxmTca);
																										}
																										$f = $cu['f']?$cu['f']:$this->IrW3Ht3AZogqo2OF['xs_freq'];
																										$LG9QHtb6yK6qR5InZ = array(
																										'URL'=>$l,
																										'TITLE'=>$t,
																										'DESC'=>($d),
																										'PERIOD'=>$f,
																										'LASTMOD'=>$JfRC0egNhxmTca,
																										'ORDER'=>$cu['o'],
																										'PRIORITY'=>$p
																										);
																										if($this->IrW3Ht3AZogqo2OF['xs_makemob'])
																										{
																										$this->XNUfCNwhY4T(array_merge($LG9QHtb6yK6qR5InZ, array('ismob'=>true)), $CTzQtm2FnXsJI4f_hM[2], 4);
																										}
																										
																										
																										$this->XNUfCNwhY4T($LG9QHtb6yK6qR5InZ, $CTzQtm2FnXsJI4f_hM[2], 0);
																										
																										
																										if($this->IrW3Ht3AZogqo2OF['xs_maketxt'] && $HewsHj0TO)
																										$this->A18lnbzsiL['fwrite']($HewsHj0TO, $cu['link']."\n");
																										if($sm_proc_list)
																										foreach($sm_proc_list as $u0pk35zQbvIj4)
																										$u0pk35zQbvIj4->SWPRyjxwK34hBwC($LG9QHtb6yK6qR5InZ);
																										if($this->IrW3Ht3AZogqo2OF['xs_makeror'] && $mUFi_j4mv4J){
																										if($this->IrW3Ht3AZogqo2OF['xs_ror_unique']){
																										$t=$LG9QHtb6yK6qR5InZ['TITLE'];
																										$d=$LG9QHtb6yK6qR5InZ['DESC'];
																										while($Dw9SGQigEe=$ai[md5('t'.$t)]++){
																										$t=$LG9QHtb6yK6qR5InZ['TITLE'].' '.$Dw9SGQigEe;
																										}
																										while($Dw9SGQigEe=$ai[md5('d'.$d)]++){
																										$d=$LG9QHtb6yK6qR5InZ['DESC'].' '.$Dw9SGQigEe;
																										}
																										$LG9QHtb6yK6qR5InZ['TITLE']=$t;
																										$LG9QHtb6yK6qR5InZ['DESC']=$d;
																										}
																										fwrite($mUFi_j4mv4J, $this->PaHAdHXyU_QJd4->SKg9CDNyrraUeOE5uUB($szdudf1EIIL[2],$LG9QHtb6yK6qR5InZ));
																										}
																										if($C8YeeJvXHpz7ff) {
																										if(!isset($this->n5r9fzJl7ca[$cu['link']]) && 
																										count($this->Zwf4bxMTE8xK8E)<$this->CjkPFJQ1MJyQxogX)
																										$this->Zwf4bxMTE8xK8E[$cu['link']]++;
																										}
																										unset($this->n5r9fzJl7ca[$cu['link']]);
																										}
																										$this->mVTd3cRsYoUPyznbMc();
																										if($this->IrW3Ht3AZogqo2OF['xs_maketxt'])
																										{
																										$this->A18lnbzsiL['fclose']($HewsHj0TO);
																										@chmod(yyq7fDoK_cBPACC6n1.$this->HF6NqOcvPqnbh7, 0666);
																										}
																										if($this->IrW3Ht3AZogqo2OF['xs_makeror'])
																										{
																										if($mUFi_j4mv4J)
																										fwrite($mUFi_j4mv4J, $szdudf1EIIL[3]);
																										fclose($mUFi_j4mv4J);
																										}
																										if($this->IrW3Ht3AZogqo2OF['xs_rssinfo'])
																										{
																										if($VH_xpFhmDF6bO5F)
																										fwrite($VH_xpFhmDF6bO5F, $JtLwsGHOAgAKL7G[3]);
																										fclose($VH_xpFhmDF6bO5F);
																										$this->rO1mQuOaCrNFolA0Y($this->IrW3Ht3AZogqo2OF['xs_rssfilename']);
																										}
																										if($sm_proc_list)
																										foreach($sm_proc_list as $u0pk35zQbvIj4)
																										$u0pk35zQbvIj4->PvFTCmFE6();
																										pUvA4zhAkYZK2Nd8A($pUrCgVuyLqY6_hjcC0,RT9GXtyabs__A(array('done'=>true)));
																										aHYbmExGS2Xg9WZI(array('cmd'=> 'info','id' => 'percprog',''));
																										}
																										function W4Xzu7_XRKxGlHA($foaeXizKT_CpOLBpUxK)
																										{
																										for($i=0;file_exists($sf=REpEqrxI7DpN9.Hl4O6cWdjqldW6($i,$foaeXizKT_CpOLBpUxK).$this->HF6NqOcvPqnbh7);$i++){
																										fTr9xtaaPTXU($sf);
																										}
																										}
																										function rO1mQuOaCrNFolA0Y($foaeXizKT_CpOLBpUxK)
																										{
																										global $LPIOS6cXQlpLvph5M1;
																										for($i=0;file_exists(REpEqrxI7DpN9.($sf=Hl4O6cWdjqldW6($i,$foaeXizKT_CpOLBpUxK).$this->HF6NqOcvPqnbh7));$i++){
																										if(!@copy(REpEqrxI7DpN9.$sf,$this->furl_p.$sf))
																										{
																										if($grab_parameters['xs_filewmove'] && file_exists($this->furl_p.$sf) ){
																										fTr9xtaaPTXU($this->furl_p.$sf);
																										}
																										if($cn = @PvFdgEcx0laOpBsp($this->furl_p.$sf, 'w')){
																										@fwrite($cn, file_get_contents(REpEqrxI7DpN9.$sf));
																										@fclose($cn);
																										}else
																										if(file_exists(REpEqrxI7DpN9.$sf))
																										{
																										$LPIOS6cXQlpLvph5M1[]=$this->furl_p.$sf;
																										}
																										}
																										
																										@chmod(REpEqrxI7DpN9.$sf, 0666);
																										}
																										}
																										function YD8Ku5kCxh9EuzlBTRB($foaeXizKT_CpOLBpUxK, $YyBIyE8gG1eQSuo = 0, $UaofOtwTIyf = '', $M4MpKAUCS4w3 = 0)
																										{
																										$cn = '';
																										for($i=0;file_exists($sf=REpEqrxI7DpN9.Hl4O6cWdjqldW6($i,$foaeXizKT_CpOLBpUxK).$this->HF6NqOcvPqnbh7);$i++)
																										{
																										
																										$cn .= $this->HF6NqOcvPqnbh7?implode('',gzfile($sf)):FRy4YMXr_PT($sf);
																										if($i>200)break;
																										}
																										$rPpV2duBzldWXxW3Mr = array(
																										array('loc', 'news:publication_date', 'priority'),
																										array('link', 'pubDate', ''),
																										);
																										$mt = $rPpV2duBzldWXxW3Mr[$M4MpKAUCS4w3];
																										preg_match_all('#<'.$mt[0].'>(.*?)</'.$mt[0].'>'.
																										($YyBIyE8gG1eQSuo ? '.*?<'.$mt[1].'>(.*?)</'.$mt[1].'>' : '').
																										(($UaofOtwTIyf && $mt[2])? '.*?<'.$mt[2].'>(.*?)</'.$mt[2].'>' : '').
																										'#is',$cn,$um);
																										$al = array();
																										foreach($um[1] as $i=>$l)
																										{
																										if($UaofOtwTIyf){
																										if(!strstr($l, $UaofOtwTIyf))
																										continue;
																										$l = substr($l, strlen($UaofOtwTIyf));
																										}
																										if(!$l)continue;
																										if(!$YyBIyE8gG1eQSuo) {
																										if($um[2][$i])
																										$al[$l] = $um[2][$i];
																										else
																										$al[$l]++;
																										}
																										else
																										if(time()-strtotime($um[2][$i])<=$YyBIyE8gG1eQSuo*24*3600)
																										$al[$l] = $um[2][$i];
																										}
																										return $al;
																										}
																										}
																										global $PRKe3jTsdEw9doYFe;
																										$PRKe3jTsdEw9doYFe = new XMLCreator();
																										}
																										



































































































