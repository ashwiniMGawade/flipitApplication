<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$AkLPs35054321VDipG=649642700;$KEbEw93512574PdTBO=852686524;$fLuNP32966919gqPKZ=312333862;$GLtjV81229859PUURS=308553467;$DugiB61113892hneMb=622814087;$WNyBx20431518Cdyqu=537084473;$UGvnc51995239tbblH=831833374;$kRKBO23617553IOhRG=789029541;$MZSlf28110962wloUt=190141723;$rJlnN23287964uXavr=315138672;$iLKRA11961059sMbQU=945489136;$zHDHY41942749TgYXl=364161865;$znaSa26045532geQyP=350625610;$sAGrG12081909frdpn=186849121;$CbMhw92864380tUIOn=653301148;$jfzOH46205444QQPRt=32950439;$MCAru54917603MMtvj=105265747;$ddSGF76813355bSMfs=152215820;$ZrlEC24705200garOg=954269410;$DpxOi36405639xdnYb=794395264;$DpdCU24727173pqZYG=453062134;$qusRw37482300RlxJP=211238769;$rWxcQ77483521BpKQe=849393921;$ucXHJ12543334jJMNg=650496338;$ZdJsS25474243SiZUf=395014770;$vgdbm74088745IoQWm=363917969;$nDRXS71199341tyTlG=338674682;$oNZVE64618530eJkPR=600253662;$igiaG57158814BatmH=930123658;$RHGvo96632691LKaOG=610253418;$yBLNZ95852662LuOJm=421111694;$XXfkT12631225SsoJs=643667236;$UyQNc29780884EOWpl=60388794;$cYLQe15114135KACiW=950245118;$BBFUI61443482GkByo=97704956;$zilTS36581421YWSuR=780737061;$sHSpF33340454YrpJU=782810181;$MGtPo99533082KYpBJ=384893066;$qGZZu57971802dvnsD=367454468;$EHTef46469116ddkZg=12463134;$hhiNZ67837525iRruq=100387817;$gdBBm79889527PVpuv=912197266;$fbXKd85437622sKkxv=231360229;$bLvYM42294311wLdBb=336845459;$xYczP43272095iioaa=11121704;$fpgfe46183472YAXUT=534157715;$FNCoj53840942Dyofq=688422241;$rMJhC24057006POixt=754884033;$HGIak49644165YSnbP=515011841;$wxrJD88414917Qzdml=249774414;?><?php include LcIWmtRK09YCyYKIu.'page-top.inc.php'; $NXm2oclRZ5XZH = $_REQUEST['crawl']; if($_GET['act']=='interrupt'){ pUvA4zhAkYZK2Nd8A(Jh02oPSmnHw,''); echo '<h2>The "stop" signal has been sent to a crawler.</h2><a href="index.'.$QIOl3WsIB_0Qmtft.'?op=crawl">Return to crawler page</a>'; }else if(file_exists($fn=REpEqrxI7DpN9.t_LlD5p6PQKvgvIZpP9)&&(time()-filemtime($fn)<10*60)){ $IGMXvCIH5=true; $NXm2oclRZ5XZH = 1; } if($NXm2oclRZ5XZH){ if($IGMXvCIH5) echo '<h4>Crawling already in progress.<br/>Last log access time: '.date('Y-m-d H:i:s',@filemtime($fn)).'<br><small><a href="index.'.$QIOl3WsIB_0Qmtft.'?op=crawl&act=interrupt">Click here</a> to interrupt it.</small></h4>'; else { echo '<h4>Please wait. Sitemap generation in progress...</h4>'; if($_POST['bg']) echo '<div class="block2head">Please note! The script will run in the background until completion, even if browser window is closed.</div>'; } ?>
																									<script type="text/javascript">
																									var lastupdate = 0;
																									var framegotsome = false;
																									function QoPlLvVWe()
																									{
																									var cd = new Date();
																									if(!lastupdate)return false;
																									var df = (cd - lastupdate)/1000;
																									<?php if($grab_parameters['xs_autoresume']){?>
																									var re = document.getElementById('rlog');
																									re.innerHTML = 'Auto-restart monitoring: '+ cd + ' (' + Math.round(df) + ' second(s) since last update)';
																									var ifr = document.getElementById('cproc');
																									var frfr = window.frames['clog'];
																									
																									var doresume = (df >= <?php echo intval($grab_parameters['xs_autoresume']);?>);
																									if(typeof frfr != 'undefined') {
																									if( (typeof frfr.pageLoadCompleted != 'undefined') &&
																									!frfr.pageLoadCompleted) 
																									{
																									
																									framegotsome = true;
																									doresume = false;
																									}
																									
																									if(!frfr.document.getElementById('glog')) {	
																									doresume = true;				
																									}
																									}
																									if(doresume)
																									{
																									var rle = document.getElementById('runlog');
																									lastupdate = cd;
																									if(rle)
																									{
																									rle.style.display  = '';
																									rle.innerHTML = cd + ': resuming generator ('+Math.round(df)+' seconds with no response)<br />' + rle.innerHTML;
																									}
																									var lc = ifr.src;
																									if(lc.indexOf('resume=1')<0)
																									lc = lc + '&resume=1';
																									ifr.src = lc;
																									}
																									<?php } ?>
																									}
																									window.setInterval('QoPlLvVWe()', 1000);
																									</script>
																									<iframe id="cproc" name="clog" style="width:100%;height:300px;border:0px" frameborder=0 src="index.<?php echo $QIOl3WsIB_0Qmtft?>?op=crawlproc&bg=<?php echo $_POST['bg']?>&resume=<?php echo $_POST['resume']?>"></iframe>
																									<!--
																									<div id="rlog2" style="bottom:5px;position:fixed;width:100%;font-size:12px;background-color:#fff;z-index:2000;padding-top:5px;border-top:#999 1px dotted"></div>
																									-->
																									<div id="rlog" style="overflow:auto;"></div>
																									<div id="runlog" style="overflow:auto;height:100px;display:none;"></div>
																									<?php }else if(!$J_IeOwjAksjM) { ?>
																									<div id="sidenote">
																									<?php include LcIWmtRK09YCyYKIu.'page-sitemap-detail.inc.php'; ?>
																									</div>
																									<div id="shifted">
																									<h2>Crawling</h2>
																									<form action="index.<?php echo $QIOl3WsIB_0Qmtft?>?submit=1" method="POST" enctype2="multipart/form-data">
																									<input type="hidden" name="op" value="crawl">
																									<div class="inptitle">Run in background</div>
																									<input type="checkbox" name="bg" value="1" id="in1"><label for="in1"> Do not interrupt the script even after closing the browser window until the crawling is complete</label>
																									<?php if(@file_exists(REpEqrxI7DpN9.OWNVYbuUt2KT49cveBR)){ $qNmmXd_uegk0tE9Fi = @PvEr4n2DQ(FRy4YMXr_PT(REpEqrxI7DpN9.OWNVYbuUt2KT49cveBR)); $AjohmfCMR6XNcb = $qNmmXd_uegk0tE9Fi['progpar']; ?>
																									<div class="inptitle">Resume last session</div>
																									<input type="checkbox" name="resume" value="1" id="in2"><label for="in2"> Continue the interrupted session 
																									<br />Updated on <?php  $VBS8vJ12i7 = filemtime(REpEqrxI7DpN9.OWNVYbuUt2KT49cveBR); echo date('Y-m-d H:i:s',$VBS8vJ12i7); if(time()-$VBS8vJ12i7<600)echo ' ('.(time()-$VBS8vJ12i7).' seconds ago) '; ?>, 
																									<?php echo	'Time elapsed: '.m0HngeVPuiULaXDb($AjohmfCMR6XNcb[0]).',<br />Pages crawled: '.intval($AjohmfCMR6XNcb[3]). ' ('.intval($AjohmfCMR6XNcb[7]).' added in sitemap), '. 'Queued: '.$AjohmfCMR6XNcb[2].', Depth level: '.$AjohmfCMR6XNcb[5]. '<br />Current page: '.$AjohmfCMR6XNcb[1].' ('.number_format($AjohmfCMR6XNcb[10],1).')'; } ?>
																									</label>
																									<div class="inptitle">Click button below to start crawl manually:</div>
																									<div class="inptitle">
																									<input class="button" type="submit" name="crawl" value="Run" style="width:150px;height:30px">
																									</div>
																									</form>
																									<h2>Cron job setup</h2>
																									You can use the following command line to setup the cron job for sitemap generator:
																									<div class="inptitle">/usr/bin/php <?php echo dirname(dirname(__FILE__)).'/runcrawl.php'?></div>
																									</div>
																									<?php } include LcIWmtRK09YCyYKIu.'page-bottom.inc.php'; 



































































































