<?
//******************************************************************
//* GetTopNews           - Список новостей для ТОП
//* GetNewsRubrics       - Рубрики для новостей 
//* GetNewsForRubric     - Новости для рубрики RubricPTR
//* GetSubrubrics 		 - Cабрубрики сюжетов
//* GetBroadcasts  		 - Сюжеты для сабрубрики RubricID или SubRubricID
//* GetBroadcast 		 - Сюжет broadcastID
//* GetAllChannels       - Все каналы с рубриками одним запросом
//* GetUserPackets       - Какие пакеты у абонента куплены
//* GetRights        	 - Запрос на права пользователя на пакет
//* GetAccess        	 - Запрос на авторизацию пользователя
//* UpdateEPG        	 - Обновить программу передач
//* GetMainPromo       	 - Получить промо на главную
//* UpdateStatistic    	 - Статистика usrLogin packetID usrTerminal objectPTR
//* CreateLiteUser    	 - Создаем пользователя lite(c)  через СМС
//* CheckFirstAccount 	 - Проверяем на ервый вход
//* CreataActionSmartUser - создание пользователей и назначение им пакетов по промоакции от 18 мая 2015 года
//* MtsAllCorrection - коррекция МТС с акцией и без / коррекция СМАРТ на акции и без
//*
//*AlSoft (c) 2015
//******************************************************************

ini_set('memory_limit', '64M');

validateParams();

$FORMAT = $_REQUEST['format'];
$JSON_RESULT = array();


switch ($_REQUEST['request']) {
    case 'server':
        if (mysql_query("SET NAMES cp1251") !== false) {
            exit('1');
        } else {
            exit('0');
        }
        break;


    case 'MtsCorrection':

        $JSON_RESULT['CORRECTION'] = array();

        $sql = "Select  concat('call MTS_CORRECTION (68,',user_id,',\'2015-10-',day(expire_date),'\'',');')  as d from sf_guard_user  u , user_packet  p
            where username like '375001%' and  usrMAC<>'' and is_active=1 and user_id  = u.id
	        and (date(expire_date)< date ('2015-07-01') or date(expire_date)>= date('2015-09-01'))
	        and (expire_date)< date(now())
	        and  packet_id=68 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTION'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTION'][$JSON_INDEX]['MTS_CORRECTION'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTION'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTION'][$JSON_INDEX]['MTS_EXE_CORRECTION'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);

        }


        break;

    case 'MtsAllCorrection':
    {

        //коррекция МТС на акции
        $JSON_RESULT['CORRECTIONSTOCK'] = array();

        $sql = "Select   concat('call MTS_CORRECTION (68,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d  from sf_guard_user  u , user_packet  p
                 where username like '375001%' and  usrMAC<>'' and is_active=1 and user_id  = u.id
                  and (date(expire_date)> date ('2015-07-01') and date(expire_date)< date('2015-09-01'))
                  and (expire_date)< date(now())
                  and TO_DAYS(now())- TO_DAYS(expire_date)>92
                  and  packet_id=68 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX]['MTS_CORRECTION_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }


        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);
        }


        //коррекция МТС без акции
        $JSON_RESULT['CORRECTNOSTOCK'] = array();

        $sql = "Select  concat('call MTS_CORRECTION (68,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d from sf_guard_user  u , user_packet  p
                 where username like '375001%' and  usrMAC<>'' and is_active=1 and user_id  = u.id
                  and (date(expire_date)< date ('2015-07-01') or date(expire_date)>= date('2015-09-01'))
                  and (expire_date)< date(now())
                  and  packet_id=68 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX]['MTS_CORRECTION_NO_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_NO_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);


        }



        //коррекция СМАРТ без акции
        $JSON_RESULT['CORRECTSMARTNOSTOCK'] = array();

        $sql = "Select  concat('call MTS_CORRECTION (69,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d
                from sf_guard_user  u , user_packet  p where username like '375001%' and  ifnull(usrMAC,'')='' and is_active=1 and user_id  = u.id
                  and (date(expire_date)< date ('2015-07-01') or date(expire_date)>= date('2015-09-01'))
                  and (expire_date)< date(now())
                  and  packet_id=69 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX]['MTS_CORRECTION_SMART_NO_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_SMART_NO_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);
        }




        //коррецкция СМАРТ на акции
        $JSON_RESULT['CORRECTSMARTSTOCK'] = array();

        $sql = "Select   concat('call MTS_CORRECTION (69,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d from sf_guard_user  u , user_packet  p
                where username like '375001%' and  ifnull(usrMAC,'')='' and is_active=1 and user_id  = u.id
                  and (date(expire_date)> date ('2015-07-01') and date(expire_date)< date('2015-09-01'))
                  and (expire_date)< date(now())
                  and TO_DAYS(now())- TO_DAYS(expire_date)>92
                and  packet_id=69 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX]['MTS_CORRECTION_SMART_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_SMART_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);
        }


        break;



    }


    case 'MtsCorrectionStock':

        $JSON_RESULT['CORRECTIONSTOCK'] = array();

        $sql = "Select   concat('call MTS_CORRECTION (68,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d  from sf_guard_user  u , user_packet  p
                 where username like '375001%' and  usrMAC<>'' and is_active=1 and user_id  = u.id
                  and (date(expire_date)> date ('2015-07-01') and date(expire_date)< date('2015-09-01'))
                  and (expire_date)< date(now())
                  and TO_DAYS(now())- TO_DAYS(expire_date)>92
                  and  packet_id=68 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX]['MTS_CORRECTION_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }


            for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

                $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX] = array();
                $JSON_RESULT['CORRECTIONSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_STOCK'] = $mtsExeCorrect[0];

                $sql2 = $mtsExeCorrect[0];
                $res3 = @mysql_query($sql2);
            }

        break;

    case 'MtsCorrectionNoStock':

        $JSON_RESULT['CORRECTNOSTOCK'] = array();

        $sql = "Select  concat('call MTS_CORRECTION (68,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d from sf_guard_user  u , user_packet  p
                 where username like '375001%' and  usrMAC<>'' and is_active=1 and user_id  = u.id
                  and (date(expire_date)< date ('2015-07-01') or date(expire_date)>= date('2015-09-01'))
                  and (expire_date)< date(now())
                  and  packet_id=68 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX]['MTS_CORRECTION_NO_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTNOSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_NO_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);


        }
        break;

    case 'MtsCorrectionSmartNoStock':

        $JSON_RESULT['CORRECTSMARTNOSTOCK'] = array();

        $sql = "Select  concat('call MTS_CORRECTION (69,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d
                from sf_guard_user  u , user_packet  p where username like '375001%' and  ifnull(usrMAC,'')='' and is_active=1 and user_id  = u.id
                  and (date(expire_date)< date ('2015-07-01') or date(expire_date)>= date('2015-09-01'))
                  and (expire_date)< date(now())
                  and  packet_id=69 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX]['MTS_CORRECTION_SMART_NO_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

            for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {

                $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX] = array();
                $JSON_RESULT['CORRECTSMARTNOSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_SMART_NO_STOCK'] = $mtsExeCorrect[0];

                $sql2 = $mtsExeCorrect[0];
                $res3 = @mysql_query($sql2);
            }

        break;


    case 'MtsCorrectionSmartStock':

        $JSON_RESULT['CORRECTSMARTSTOCK'] = array();

        $sql = "Select   concat('call MTS_CORRECTION (69,',user_id,',\'2015-10-',day(expire_date),'\'',');') as d from sf_guard_user  u , user_packet  p
                where username like '375001%' and  ifnull(usrMAC,'')='' and is_active=1 and user_id  = u.id
                  and (date(expire_date)> date ('2015-07-01') and date(expire_date)< date('2015-09-01'))
                  and (expire_date)< date(now())
                  and TO_DAYS(now())- TO_DAYS(expire_date)>92
                and  packet_id=69 order by expire_date";
        $res = @mysql_query($sql);

        for ($JSON_INDEX = 0; $mtsCorrect = @mysql_fetch_array($res); $JSON_INDEX++) {

            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX]['MTS_CORRECTION_SMART_STOCK'] = $mtsCorrect['d'];

            $sql2 = $mtsCorrect['d'];
            $res2 = @mysql_query($sql2);

        }

        for ($JSON_INDEX = 0; $mtsExeCorrect = @mysql_fetch_array($res2); $JSON_INDEX++) {
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX] = array();
            $JSON_RESULT['CORRECTSMARTSTOCK'][$JSON_INDEX]['MTS_EXE_CORRECTION_SMART_STOCK'] = $mtsExeCorrect[0];

            $sql2 = $mtsExeCorrect[0];
            $res3 = @mysql_query($sql2);
        }


        break;


}


header('Access-Control-Allow-Origin: *');

if ($FORMAT == 'json') {
    echo json_encode($JSON_RESULT);
} else {
    print_r($JSON_RESULT);
}

function updateStatistic($login, $packet, $terminal, $object)
{
    $sql = "INSERT INTO sys_usercontent (uctUserPTR, uctServicePTR, uctObjectPTR, uctTerminal) VALUES ((SELECT id FROM sf_guard_user WHERE username='$login'),'$packet','$object',(SELECT id FROM spr_terminals  where sptName = '$terminal'))";
    $res = @mysql_query($sql);
    if ($res)
        return true;
    else
        return false;
}

function validateParams()
{
    foreach ($_REQUEST as $KEY => $PARAM) {
        $_REQUEST[$KEY] = mysql_real_escape_string($PARAM);
    }
}

function GetUser($usrLogin, $usrPass)
{

    $res = @mysql_query($sql);
    $user = @mysql_fetch_array($res);

    if ($user) {
        $usrPass = sha1($user['salt'] . $usrPass);
        if ($user['password'] == $usrPass)
            return $user;
        else
            return null;
    } else
        return null;
}

function GetUserByLogin($usrLogin)
{

    $res = @mysql_query($sql);
    $user = @mysql_fetch_array($res);

    if ($user) {
        return $user;
    } else
        return null;
}

