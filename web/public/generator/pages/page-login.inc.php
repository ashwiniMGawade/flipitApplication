<?php // This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited.




































































































$FaLfE23397827UidiI=806102295;$tcvys17344360jxwkG=27706665;$Ycgsr35724487voAcm=347820801;$NZiJA81350708ULmjO=548913452;$aDbIx22035522JDtHK=911953370;$xFyGF40591431iGgmE=219409301;$gDQnH94830933GakbE=750250000;$UvRnv97566529wZYpO=287944214;$UIPrb96610718FrRKh=112460693;$CqHeM94776001QjJTh=5268188;$ORlLP49874878rodSt=247335449;$QDBGK54719849ZuVNY=620131226;$UDaYJ67123413SCHwg=405624267;$ItzCX89898072aFvEB=384283325;$HIzwS80856324aRuEt=837077149;$YefLJ42810669lmmTE=546474487;$sdQkj23573608AaOPH=792444092;$YMBzQ25957641ObssU=357454712;$Ttwvl97775269TEIUE=521475098;$CuYlb61838989qVhrv=66973999;$HoiVB55961304DxfOL=273920166;$GJQvS82954712blnMh=923782349;$uAuBG10631713FuXem=299529297;$beXfC21804809hnjzf=180629760;$CzjJX74286499HwOqu=848052491;$oswrX80889283LwDnA=85266235;$zNLRR89425660NdAFX=171239746;$lDoMr12708129qubWi=887441773;$GCFIe78549195PjJPq=516841064;$GHWdP19761352cNzFc=838906372;$avJhT64157105utEXH=136606445;$ovzgB34548950lAZWB=189410034;$zFDIv68749390RrWLz=279285888;$YeMio79570923YMoNB=187702758;$rjheS24826049jvVay=195629394;$aZRoR87327271ZOQzK=84534545;$PqdJw44887085cnwrl=135386963;$OpcRx80317993huoMn=129655395;$nxjVg61432495dxriC=348308594;$GpJUX81043091qTzrb=572815308;$LTfCS96962281knhGm=85144287;$ViFTd22002563zsxAV=664764282;$YZLCB83976441zBXgl=594644043;$xdZLV15696411UYbTo=655252319;$bQKlZ44974975qrkSh=128557861;$cBolN84624634NISEz=794029419;$nKUMA92457886xtbJB=934635743;$jfNaP71287232LLHNS=331845581;$CHURu68925171GhZjm=265627685;$uYQcs88184204wemyF=517450806;?><?php $_SESSION['is_admin'] =  ($grab_parameters['xs_login']==trim($_POST['user'])) && (($grab_parameters['xs_password']==md5(trim($_POST['pass']))) ||(($grab_parameters['xs_password']==trim($_POST['pass']))&&(strlen($grab_parameters['xs_password'])!=32)) ) ; if($_POST['user']) setcookie('sm_log',md5($_POST['user']).'-'.md5($_POST['pass'])); if(!$_SESSION['is_admin']) { define('w7NW0sRCh2KBF74Bo',1); include LcIWmtRK09YCyYKIu.'page-top.inc.php'; ?>
																												<div id="sidenote">
																												</div>
																												<div id="shifted">
																												<h2>Login</h2>
																												<?php if($_POST['user']) echo '<div class="block2head">Login incorrect</div>'; ?>
																												<form action="index.<?php echo $QIOl3WsIB_0Qmtft?>?submit=1" method="POST" enctype2="multipart/form-data">
																												<div class="inptitle">Username:</div>
																												<input type="text" name="user" size="30" value="">
																												<div class="inptitle">Password:</div>
																												<input type="password" name="pass" size="30" value="">
																												<div class="inptitle">
																												<input class="button" type="submit" name="login" value="Login" style="width:150px;height:30px">
																												</div>
																												</form>
																												</div>
																												<?php include LcIWmtRK09YCyYKIu.'page-bottom.inc.php'; } 



































































































