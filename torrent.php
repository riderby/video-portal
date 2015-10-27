<?php

 ini_set('display_errors', 1);
 ini_set('log_errors', 1);
 error_reporting(E_ALL);

 
session_start();
foreach (getallheaders() as $name => $value) {
 	 if ($name =="User-Agent") {
		 	$headers = split (";", $value);
			foreach ($headers as $value) {
				if (strstr($value,'Mozilla')) {
					$Curent_brouser='Mozilla';
				}
				if (strstr($value,'Opera')) {
					$Curent_brouser='Opera';			
				}
				if (strstr($value,'Chrome')) {
					$Curent_brouser='Chrome';			
				}					
				if (strstr($value,'MSIE 7.0')) {
					echo"
						<script>
						top.location.href='http://ikino.itv.by/error.php';
						</script>
					";		
				}		
				if (strstr($value,'MSIE 6.0')) {
					echo"
						<script>
						top.location.href='http://ikino.itv.by/error.php';
						</script>
					";		
				}		
				if (strstr($value,'MSIE 8.0')) {
					$Curent_brouser='MSIE8';	
				}				
			}
	}
}

if (strpos($_SERVER['HTTP_REFERER'],'show.php') == 0)
{
$_SESSION['prev_addr']=$_SERVER['HTTP_REFERER'];
}
if (!($prev_addr=$_SESSION['prev_addr']))$prev_addr='http://ikino.itv.by/';


if($_GET['act']=='logout' )
{
unset($_SESSION['is_autorised']);
unset($_SESSION['autorised_login']);
unset($_SESSION['autorised_pass']);
unset($_SESSION['autorised_mac']);
unset($_SESSION['autorised_first_name']);
unset($_SESSION['autorised_last_name']);

header("Cache-Control: no-store, no-cache,  must-revalidate"); 
setcookie('itv_auth', 'false', time()-120*60, '/', '.itv.by');
setcookie('itv_login', '', time()-36000, '/', '.itv.by');
setcookie('itv_password', '', time()-36000, '/', '.itv.by');
setcookie('itv_mac', '', time()-36000, '/', '.itv.by');
setcookie('itv_first_name', '', time()-36000, '/', '.itv.by');
setcookie('itv_last_name','', time()-36000, '/', '.itv.by');
header("Location: http://ikino.itv.by/"); 

}
 
require_once("options.php");
require_once("login.php");


/*авторизация после отравки запроса, или из куки*/
if ($_POST['go-login']==' ')
{
  $usrLogin=$_POST['signin']['username'];
  $usrPass=$_POST['signin']['password'];
  $login_result=try_login($usrLogin,$usrPass);
  if($login_result->STATUS === 0)
  {
    $_SESSION['is_autorised']=true;
    $_SESSION['autorised_login']=$usrLogin;
    $_SESSION['autorised_pass']=$usrPass;
    $_SESSION['autorised_first_name']=$login_result->FIRST_NAME;
    $_SESSION['autorised_last_name']=$login_result->LAST_NAME;
	$_SESSION['autorised_mac']=$login_result->MAC;
	
	//сохранение данных в куке для авторизации 
	setcookie('itv_auth', 'true', time()+36000, '/', '.itv.by');
	setcookie('itv_login', $_SESSION['autorised_login'], time()+36000, '/', '.itv.by');
	setcookie('itv_password', $_SESSION['autorised_pass'], time()+36000, '/', '.itv.by');
	setcookie('itv_mac', $_SESSION['autorised_mac'], time()+36000, '/', '.itv.by');
	setcookie('itv_first_name',$_SESSION['autorised_first_name'], time()+36000, '/', '.itv.by');
	setcookie('itv_last_name', $_SESSION['autorised_last_name'], time()+36000, '/', '.itv.by');	
  }else{
    $message = $login_result->MSG;
  }
}

if(!isset($_REQUEST['go-login']))
{
  if(!$_COOKIE['itv_auth'])
  {
unset($_SESSION['is_autorised']);
unset($_SESSION['autorised_login']);
unset($_SESSION['autorised_pass']);
unset($_SESSION['autorised_mac']);
unset($_SESSION['autorised_first_name']);
unset($_SESSION['autorised_last_name']);

header("Cache-Control: no-store, no-cache,  must-revalidate"); 
setcookie('itv_auth', 'false', time()-120*60, '/', '.itv.by');
setcookie('itv_login', '', time()-36000, '/', '.itv.by');
setcookie('itv_password', '', time()-36000, '/', '.itv.by');
setcookie('itv_mac', '', time()-36000, '/', '.itv.by');
setcookie('itv_first_name', '', time()-36000, '/', '.itv.by');
setcookie('itv_last_name','', time()-36000, '/', '.itv.by');
  }
}

if(($_COOKIE['itv_auth'])&&(!$_SESSION['is_autorised']))
{
	$_SESSION['is_autorised']=true;
	$_SESSION['autorised_login']=$_COOKIE['itv_login'];
	$_SESSION['autorised_pass']=$_COOKIE['itv_password'];
	$_SESSION['autorised_mac']=$_COOKIE['itv_mac'];
	$_SESSION['autorised_first_name']=$_COOKIE['itv_first_name'];
	$_SESSION['autorised_last_name']=$_COOKIE['itv_last_name'];	
}
if(isset($message))
{
  $message="
  <div class='error-message'>
	  <div class='container'>
	    <div class='error-message-top'></div>
		<div class='error-message-center'>
		  <div>
			<li>$message</li>
	      </div>
		</div>
		<div class='error-message-footer'></div>
	 </div>
  </div>
  <div class='error-field'></div>";
}

 if($_GET['act'] == 'toSTB'){
	@mysql_connect($hostname,$username,$password); 
	@mysql_select_db($dbName);
	$sql="select url from torrents where id=".mysql_real_escape_string($_GET['id']);
	$tmpres = @mysql_query($sql);
	$row = @mysql_fetch_array($tmpres);
	if($_SESSION['is_autorised']){
		$user = GetMediaRights($_SESSION['autorised_login'],$_SESSION['autorised_pass']);
		if(($user->STATUS == 0)){
			if(isset($user->MAC) && (strlen($user->MAC) == 17)){				
				$answere = json_decode(file_get_contents("http://86.57.251.22/stalker_portal/torrent.php?request=SetTorrentDownload&usrLogin=".$_SESSION['autorised_login']."&usrPass=".$_SESSION['autorised_pass']."&torentUrl=".urlencode($row['url'])."&format=json"));
				if($answere->RESULT != null && $answere->RESULT->STATUS == 0){
					$torrent_msg = "Файл добавлен. В течение нескольких минут он появится во вкладке «Загрузки» в вашем iкабинете.";
				}else{
					$torrent_msg = $answere->RESULT->MSG;
				}
			}else{
				$torrent_msg = "На Вашей учетной записи не чиcлится приставка STB! Запись невозможна!  Для скачивания торрент файлов на STB Необходимо приобретси пакети ITV Media и получить приставку в офисе компании.";
			}
		}else{
			$torrent_msg = $user->MSG;
		}
	}else{
		$torrent_msg = "Необходимо авторизоваться.";
	}
}

function GetMediaRights($usrLogin, $usrPass){
	$result = json_decode(file_get_contents("http://api.itv.by/xml.php?request=GetRights&usrLogin=$usrLogin&usrPass=$usrPass&format=json&packetID=29"));
	return $result->USER;
}

if(isset($message))
{
  $message ="
  <div class='error-message'>
	  <div class='container'>
	    <div class='error-message-top'></div>
		<div class='error-message-center'>
		  <div>
			<li>$message</li>
	      </div>
		</div>
		<div class='error-message-footer'></div>
	 </div>
  </div>
  <div class='error-field'></div>";

}


echo 
"<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>  
<html>  
<head> 
<link rel=\"shortcut icon\" href=\"/favicon.png\" /> 
<title>iKINO - смотрите фильмы онлайн бесплатно на ikino.itv.by. Лучшие фильмы, сериалы и мультфильмы в хорошем качестве.</title> 
<meta name=\"keywords\" content=\"ikino, айкино, онлайн фильмы, онлайн кино, сериалы, мультфильмы, фильмы 2011, мелодрамы, триллеры, боевики, ужасы, комедии, фантастика, смотреть, бесплатно, новинки, премьеры, в хорошем качестве, без регистрации\">
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" />   ";
echo'	
<link rel="alternate" href="http://ikino.itv.by/rss.php" type="application/rss+xml" title="Фильмы iKINO.ITV.BY">
<link rel="alternate" href="http://new.itv.by/rss" type="application/rss+xml" title="Видео ITV.BY">
	<link rel="alternate" href="http://new.itv.by/rss?rubric=news" type="application/rss+xml" title="Видеоновости ITV.BY">
	<link rel="alternate" href="http://new.itv.by/rss?rubric=music" type="application/rss+xml" title="Музыка ITV.BY">
	<link rel="alternate" href="http://new.itv.by/rss?rubric=sport" type="application/rss+xml" title="Спорт ITV.BY">
	<link rel="alternate" href="http://new.itv.by/rss?rubric=trailers" type="application/rss+xml" title="Трейлеры ITV.BY">
	<link rel="alternate" href="http://new.itv.by/rss?rubric=projects" type="application/rss+xml" title="Проекты ITV.BY">
	
';
	echo" 
<link rel='stylesheet' type='text/css' href='index.css'>
<link rel='shortcut icon' href='images/icon.ico' />
<script type='text/javascript' src='$site/view/lib/glossy.js'></script>
<script type='text/javascript' src='/js1/./jquery/jquery-1.5.1.min.js'></script>

<script type=\"text/javascript\">
<!--//--><![CDATA[//><!--
var pp_gemius_identifier = new String('.R.lDW8yrbTj1kOaYaQwObQG.F6K0RgRJarFd3pm2Bf.t7');
var pp_gemius_hitcollector = 'gaby.hit.gemius.pl';
//--><!]]>
</script>
<script type=\"text/javascript\" src=\"/js/xgemius.js\"></script>
</head>
<body style='background:url(images/body_bg.jpg) 0 0 no-repeat #000000;padding:0;margin:0;background-position:top;overflow-X: hidden'>
";
require_once("login_menu.php");

if(isset($torrent_msg)){
	require_once("login_menu_torrent.php");
	echo"<script> document.getElementById('popup-torrent-layer').style.display = 'block';</script>	";
}	

require_once("serial_menu.php");
if($login_result->STATUS > 0 ) {
	echo"<script> document.getElementById('popup-login-layer').style.display = 'block';</script>	";
}
echo "
<center>

<table cellspacing='0' cellpadding='0' align='center' width='988'  style='margin:0px 0px 0px 0px;border:0;top:0;height:62px;'>
<tr><td align='right' border ='0' valign='top'  margin='0px 0px 0px 0px' background='images/menu.png' style='background-repeat:no-repeat; margin:0 auto;height:58;width:100%;'>
";
if (!$_SESSION['is_autorised'])
echo "<INPUT TYPE='image' name='login_button' align='right' style='position:relative;top:12px; right:40'  border=0 src='images/enter-button.png' onMouseOver=\"this.src='images/enter-button-hover.png';\" onMouseOut=\"this.src='images/enter-button.png';\"/ onClick=\" document.getElementById('popup-login-layer').style.display = 'block'\">";
else{
$first_name = $_SESSION['autorised_first_name'];
$last_name = $_SESSION['autorised_last_name'];
if($Curent_brouser=='MSIE8')
{
		
	echo"
	  <div style='font-family: tahoma;font-size: 12px; font-weight: bold; margin: 13px 37px 0 0'>
		<img src='../images/logout-button-left.png' style='margin:0'/>
		<span style='background: url(../images/logout-button-center-repeat.png); line-height: 9px; height: 27px; padding: 7px 5px 0 4px; margin-left:-4px'>
			<a href='http://new.itv.by/cabinet_profile'>$first_name $last_name</a>
		</span>
		<img style='margin-left: 3px; cursor:pointer' src='../images/logout-right-button.png' onClick=top.location.href='?act=logout' />
	  </div>
	  ";
		

}
else
{

	echo"
	  <div class='itv-top-cabinet-button logout'>
		<a class='out' onClick=top.location.href='?act=logout'></a>
		<a href='http://new.itv.by/cabinet_profile' class='profile'>
		  <div class='name'>
			$first_name $last_name
		  </div>
		</a>
	  </div>";
}
		
}


echo"
	<a style='position:absolute;left:45%;top:0px;' href='$site'><img left='70px' border=0 src='images/logo.png' alt='iKino'> </a> 

</tr></td></table>
<script>
    jQuery('#login-close').click(function()
    {
      jQuery('#popup-login-layer').hide();
    })
    jQuery('#reg-popup').click(function()
    {
      jQuery('#popup-login-layer').hide();
      jQuery('#popup-reg-layer').show();
      return false;
    })
  </script>

<table border=0 align='center'  width='992' style='border:0;cellpadding:-4;height:158;'>
<tr><td align='right' border=0 valign='top' height='158'  background='images/fon_scroll.png' style='background-repeat:no-repeat;padding:0 2 0 0;height:158;width:990;'>";
	require_once("view/scroll.php");
	echo "</td></tr></table>";
	require_once("view/findview.php");


	
echo "<div style='position:absolute;top:301;left:50%;margin-left:-494px'><table align='center' border='0' width='990' height='495' cellpadding=0 cellspacing=0><tr>
   <td  align='right' width='231' height='495' background='images/fon_top.png' style='background-repeat:no-repeat;padding:0;padding-right:10;height:495;'>";
echo "</td>
    <td rowspan=2 align='left' valign='top' background='images/back_film.png' style='background-repeat:no-repeat;padding:0;padding-left:20;padding-top:20;'>";
    echo "
    <script type='text/javascript' src='view/lib/jquery.blockUI.js'></script>
    <script type='text/javascript'>
function get_result(str){  
$.get( 'view/gettorrent.php', { id: $_GET[id],page: str, par: \"$par\", text: \"$text\" }, onAjaxSuccess);
} 

function onAjaxSuccess(data) 
{ 
$(\"#result\") 
.html(data) 
.animate({height: \"show\"}, 900);  
}
</script>
    ";
    echo "<div id='result' style='border:1;position:relative;left:0;top:0;width:600;height:470'>
<script type='text/javascript'>
$(document).ready(function (){ get_result(1);});
</script>
</div>";
echo "</td>
</tr>
<tr>
    <td align='left' valign='top' width='230' height='0' style='padding:0;padding-right:10'>";
require_once("view/topindex.php");
echo "</td>
</tr>
</table></div>";


echo "
         <div class=\"itv-copyright-container\">
         <span><a target=_top
href=\"http://www.akavita.by/\">
<script language=javascript><!--
d=document;w=window;n=navigator;d.cookie=\"cc=1\";
r=''+escape(d.referrer);js=10;c=(d.cookie)?1:0;j=0;
x=Math.random();u=''+escape(w.location.href);lt=0;
h=history.length;t=new Date;f=(self!=top)?1:0;cd=0;
tz=t.getTimezoneOffset();cpu=n.cpuClass;ww=wh=ss=0;
//--></script><script language=\"javascript1.1\"><!--
js=11;j=(n.javaEnabled()?1:0);
//--></script><script language=\"javascript1.2\"><!--
js=12;lt=1;s=screen;ss=s.width;
cd=(s.colorDepth?s.colorDepth:s.pixelDepth);
//--></script><script language=\"javascript1.3\"><!--
js=13;wh=w.innerHeight;ww=w.innerWidth;
wh=(wh?wh:d.documentElement.offsetHeight);
ww=(ww?ww:d.documentElement.offsetWidth);
//--></script><script language=javascript><!--
q='lik?id=22901&d='+u+'&r='+r+'&h='+h+'&f='+f;
q+='&c='+c+'&tz='+tz+'&cpu='+cpu+'&js='+js+'&wh='+wh;
q+='&ww='+ww+'&ss='+ss+'&cd='+cd+'&j='+j+'&x='+x;
d.write('<img src=\"http://adlik.akavita.com/bin/'+
q+'\" alt=\"Akavita: каталог, рейтинг, счетчик для сайтов Беларуси\" '+
'border=0 width=1 height=1>');
if(lt){d.write('<'+'!-- ');}//--></script><noscript>
<img src=\"http://adlik.akavita.com/bin/lik?id=22901\" border=0 height=1 width=1 alt=\"Akavita: каталог, рейтинг, счетчик для сайтов Беларуси\">

</noscript><script language=\"JavaScript\"><!--
if(lt){d.write('--'+'>');}//--></script></a>© 2007-2015 <a style=\"text-decoration:none;font-size: 13px;\" href=\"http://itv.by\" >ITV.BY </a></span>
		 		  <center>
";
echo'		  <link href="for_menu.css" type="text/css" rel="stylesheet"/>
   <div class="menu-container">
    <div id="menu">
      <div class="up-down-button" id="down-button"></div>
      <div class="up-down-button" id="up-button"></div>
      <script>
        jQuery("#down-button").click(function(){
          jQuery("#menu").animate({
            top: "94px"
            }, "slow",
            function(){
              jQuery("#down-button").hide();
              jQuery("#up-button").show();
			   jQuery("#result").css("z-index",100);
            }
           );
        });

        jQuery("#up-button").click(function(){
		jQuery("#result").css("z-index",0);
          jQuery("#menu").animate({
            top: "0px"
            }, "slow",
            function(){
              jQuery("#down-button").show();
              jQuery("#up-button").hide();
			 
            }
           );
        });

      </script>
      <div class="first" id="itv"><a href="http://new.itv.by" class="img_link"><img src="../images_main/itv.png" border="0"  alt=""></a></div>
      <div id="ikino"><a href="http://ikino.itv.by/" class="img_link"><img src="../images_main/ikino.png" border="0"  alt=""></a></div>
      <div id="imusic"><a href="http://imusic.itv.by" class="img_link"><img src="../images_main/imusic.png" border="0"  alt=""></a></div>
      <div class="last" id="icab"><a href="http://new.itv.by/cabinet" class="img_link"><img src="../images_main/icab.png" border="0"  alt=""></a></div>
    </div>
    <div class="bottom-shaddow"></div>
  </div>
  <div style="padding-top:20px;">
			 <a style="text-decoration:none;font-size: 13px;" href="http://new.itv.by/text_info_about" >О проекте</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 <a style="text-decoration:none;font-size: 13px;" href="http://new.itv.by/text_info_promo" >Smart-акция</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 <a style="text-decoration:none;font-size: 13px;" href="http://new.itv.by/text_info_stb" >ТВ-приставка</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		     <a style="text-decoration:none;font-size: 13px;" href="http://new.itv.by/text_info_api" >Приложения</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			 		 
			 <a style="text-decoration:none;font-size: 13px;" href="http://new.itv.by/text_info_faq" >Техподдержка</a>
<script>
function hide_menu(){
jQuery("#down-button").click();
}
setTimeout("hide_menu()", 5000);
</script>
 </div>';


echo"	 
		 </center>	
         <div class=\"itv-like\">	
			<a href='http://www.odnoklassniki.ru/group/52459875336436'><img border=0 src='images/odnoklasniki.png' alt='Одноклассники'onMouseOver=\"this.src='images/odnoklasniki2.png';\" onMouseOut=\"this.src='images/odnoklasniki.png';\"></a>		 
	<a href='http://vk.com/itv_by'><img border=0 src='images/vkontakte.png' alt='ВКонтакте'onMouseOver=\"this.src='images/vkontakte2.png';\" onMouseOut=\"this.src='images/vkontakte.png';\"></a>
	<a href='http://twitter.com/#!/ITV_BY'><img border=0 src='images/twitter.png' alt='Twitter' onMouseOver=\"this.src='images/twitter2.png';\" onMouseOut=\"this.src='images/twitter.png';\"></a>
	<a href='http://www.facebook.com/photo.php?fbid=208662509158620&set=a.181095688581969.42958.100000446160696&type=1&theater#!/home.php?sk=group_205638992786268&ap=1' >
		<img border=0 src='images/facebook.png' alt='Facebook'onMouseOver=\"this.src='images/facebook2.png';\" onMouseOut=\"this.src='images/facebook.png';\"></a>
  </div>
        </div>
";

echo "</center>";


?>
<!--mernik counter start-->
<span id='Mernik_Image'></span>
<script type='text/javascript'>
var SID=1105;
(function(){var t='text/javascript', h, s, u, d=document, l='http://s3.countby.com/code.js';
try{h=d.getElementsByTagName('head')[0];s=d.createElement('script');
s.src=l;s.type=t;s.async=true;h.appendChild(s);}catch(e){
u='%3Cscript%20src="'+l+'"%20type="'+t+'"%3E%3C/script%3E'; d.write(unescape(u));}})();
</script><noscript>
<img src='http://c2.countby.com/cnt?id=1105' border='0' height='1' width='1' alt=''/>
</noscript>
<!--mernik counter end-->
<? echo "
</body> 
</html>";

?>
