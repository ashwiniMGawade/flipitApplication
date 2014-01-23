<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$NjDvV41374206HRvcz=96150116;$uUwBe31936950WUSnj=579674896;$RqqgD64511414oLKNR=795889130;$SfEEg85660096NvUFi=900886567;$MXBvV86945496LMTxh=551260956;$WKNrS14930114PwuUL=902106049;$miCiQ41176452IKfVJ=611015595;$cuEGM22247009CLYlo=833083344;$swoIF39704284jutSj=225903045;$GHWCN40110779COgHG=943568451;$HHVke15028991Jnlaw=644673309;$SCiKe91021424BpcRG=484311371;$wYntQ79650574zMCHd=119076385;$ZFPnb17478942hQhAv=704062103;$zSwWc76069031cVngx=896862275;$yOsxb21983337tXrXM=853570649;$rPZHe26784362tEyim=230780975;$FGcJg37034607LRtVP=183587005;$szjwI44296570PyrIA=368582489;$kxuGp85132752dDRlF=941861176;$blzUv61105652cSUqH=561016815;$TJJJv98777771kAHNW=381143158;$IxWGL99711609Eqqmc=58833953;$otQZg10469665IBCnp=749182953;$plNKP92614441dAmsy=110783905;$kOObB22708435AagNN=297730560;$fZCKY62314148qUfFr=966616669;$NJmLL67994080rdyKp=275535980;$hOIeH31310730STLuR=878082245;$PRpXF78826599RtIBq=932349213;$fSxCa22104187eNVRD=94930633;$Yzejn77705994TXuCH=519920258;$OYvRP57194519Vwhxy=864911835;$MvfeM87132264zwSXI=286999115;$UhNfB69081726LwCDO=440775848;$tNlQT39605408pLqyV=483335785;$vqfdb80265808UlouJ=71272674;$ihqCW47625427gpXFZ=359680267;$diICx23246765VFjET=6152313;$vENme43692322XKKjL=165782562;$WXhfe10524597Xtxsn=495164764;$ordmc50306091gRGPP=151392669;$smxJP64599304HfXKU=789060028;$TtdpA89966736PBfar=566260590;$NkdmQ27970886GPMMf=138588104;$MSlFI95174256aoQWt=661136322;$LdhFF13139343lQCzJ=791498993;$YThYq88428650RcLbw=685769867;$HenXF42604675hqCik=999542695;$PTLSk92229920ENtay=889911225;?><?php include LcIWmtRK09YCyYKIu.'page-top.inc.php'; $JxsQWbvuZdjq9Bnq = cVhR96lmkjBRF(); if($grab_parameters['xs_chlogorder'] == 'desc') rsort($JxsQWbvuZdjq9Bnq); $dr1lN9apl=$_GET['log']; if($dr1lN9apl){ ?>
																														<div id="sidenote">
																														<div class="block1head">
																														Crawler logs
																														</div>
																														<div class="block1">
																														<?php for($i=0;$i<count($JxsQWbvuZdjq9Bnq);$i++){ $jmTPqK4UHmexxwYuYDf = @unserialize(FRy4YMXr_PT(REpEqrxI7DpN9.$JxsQWbvuZdjq9Bnq[$i])); if($i+1==$dr1lN9apl)echo '<u>'; ?>
																														<a href="index.<?php echo $QIOl3WsIB_0Qmtft?>?op=chlog&log=<?php echo $i+1?>" title="View details"><?php echo date('Y-m-d H:i',$jmTPqK4UHmexxwYuYDf['time'])?></a>
																														( +<?php echo count($jmTPqK4UHmexxwYuYDf['newurls'])?> -<?php echo count($jmTPqK4UHmexxwYuYDf['losturls'])?>)
																														</u>
																														<br>
																														<?php	} ?>
																														</div>
																														</div>
																														<?php } ?>
																														<div<?php if($dr1lN9apl) echo ' id="shifted"';?> >
																														<h2>ChangeLog</h2>
																														<?php if($dr1lN9apl){ $jmTPqK4UHmexxwYuYDf = @unserialize(FRy4YMXr_PT(REpEqrxI7DpN9.$JxsQWbvuZdjq9Bnq[$dr1lN9apl-1])); ?><h4><?php echo date('j F Y, H:i',$jmTPqK4UHmexxwYuYDf['time'])?></h4>
																														<div class="inptitle">New URLs (<?php echo count($jmTPqK4UHmexxwYuYDf['newurls'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php echo @htmlspecialchars(implode("\n",$jmTPqK4UHmexxwYuYDf['newurls']))?></textarea>
																														<div class="inptitle">Removed URLs (<?php echo count($jmTPqK4UHmexxwYuYDf['losturls'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php echo @htmlspecialchars(implode("\n",$jmTPqK4UHmexxwYuYDf['losturls']))?></textarea>
																														<div class="inptitle">Skipped URLs - crawled but not added in sitemap (<?php echo count($jmTPqK4UHmexxwYuYDf['urls_list_skipped'])?>)</div>
																														<textarea style="width:100%;height:300px"><?php foreach($jmTPqK4UHmexxwYuYDf['urls_list_skipped'] as $k=>$v)echo @htmlspecialchars($k.' - '.$v)."\n";?></textarea>
																														<?php	 }else{ ?>
																														<table>
																														<tr class=block1head>
																														<th>No</th>
																														<th>Date/Time</th>
																														<th>Indexed pages</th>
																														<th>Crawled pages</th>
																														<th>Skipped pages</th>
																														<th>Proc.time</th>
																														<th>Bandwidth</th>
																														<th>New URLs</th>
																														<th>Removed URLs</th>
																														<th>Broken links</th>
																														<?php if($grab_parameters['xs_imginfo'])echo '<th>Images</th>';?>
																														<?php if($grab_parameters['xs_videoinfo'])echo '<th>Videos</th>';?>
																														<?php if($grab_parameters['xs_newsinfo'])echo '<th>News</th>';?>
																														<?php if($grab_parameters['xs_rssinfo'])echo '<th>RSS</th>';?>
																														</tr>
																														<?php  $J8NZ8Rx5Xj8oKk=array(); for($i=0;$i<count($JxsQWbvuZdjq9Bnq);$i++){ $jmTPqK4UHmexxwYuYDf = @unserialize(FRy4YMXr_PT(REpEqrxI7DpN9.$JxsQWbvuZdjq9Bnq[$i])); if(!$jmTPqK4UHmexxwYuYDf)continue; foreach($jmTPqK4UHmexxwYuYDf as $k=>$v)if(!is_array($v))$J8NZ8Rx5Xj8oKk[$k]+=$v;else $J8NZ8Rx5Xj8oKk[$k]+=count($v); ?>
																														<tr class=block1>
																														<td><?php echo $i+1?></td>
																														<td><a href="index.php?op=chlog&log=<?php echo $i+1?>" title="View details"><?php echo date('Y-m-d H:i',$jmTPqK4UHmexxwYuYDf['time'])?></a></td>
																														<td><?php echo number_format($jmTPqK4UHmexxwYuYDf['ucount'])?></td>
																														<td><?php echo number_format($jmTPqK4UHmexxwYuYDf['crcount'])?></td>
																														<td><?php echo count($jmTPqK4UHmexxwYuYDf['urls_list_skipped'])?></td>
																														<td><?php echo number_format($jmTPqK4UHmexxwYuYDf['ctime'],2)?>s</td>
																														<td><?php echo number_format($jmTPqK4UHmexxwYuYDf['tsize']/1024/1024,2)?></td>
																														<td><?php echo count($jmTPqK4UHmexxwYuYDf['newurls'])?></td>
																														<td><?php echo count($jmTPqK4UHmexxwYuYDf['losturls'])?></td>
																														<td><?php echo count($jmTPqK4UHmexxwYuYDf['u404'])?></td>
																														<?php if($grab_parameters['xs_imginfo'])echo '<td>'.$jmTPqK4UHmexxwYuYDf['images_no'].'</td>';?>
																														<?php if($grab_parameters['xs_videoinfo'])echo '<td>'.$jmTPqK4UHmexxwYuYDf['videos_no'].'</td>';?>
																														<?php if($grab_parameters['xs_newsinfo'])echo '<td>'.$jmTPqK4UHmexxwYuYDf['news_no'].'</td>';?>
																														<?php if($grab_parameters['xs_rssinfo'])echo '<td>'.$jmTPqK4UHmexxwYuYDf['rss_no'].'</td>';?>
																														</tr>
																														<?php }?>
																														<tr class=block1>
																														<th colspan=2>Total</th>
																														<th><?php echo number_format($J8NZ8Rx5Xj8oKk['ucount'])?></th>
																														<th><?php echo number_format($J8NZ8Rx5Xj8oKk['crcount'])?></th>
																														<th><?php echo number_format($J8NZ8Rx5Xj8oKk['ctime'],2)?>s</th>
																														<th><?php echo number_format($J8NZ8Rx5Xj8oKk['tsize']/1024/1024,2)?> Mb</th>
																														<th><?php echo ($J8NZ8Rx5Xj8oKk['newurls'])?></th>
																														<th><?php echo ($J8NZ8Rx5Xj8oKk['losturls'])?></th>
																														<th>-</th>
																														</tr>
																														</table>
																														<?php } ?>
																														</div>
																														<?php include LcIWmtRK09YCyYKIu.'page-bottom.inc.php'; 



































































































