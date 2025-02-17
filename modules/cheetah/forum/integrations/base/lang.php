<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

/**
 * file to get language string, overrride it if you use external language file
 *******************************************************************************/

function getLangString ($s, $sLang = '')
{
    global $gConf;

    if (!$sLang)
        $sLang = $gConf['lang'];

    require_once ($gConf['dir']['langs'] . $sLang .  '.php');
    return isset($GLOBALS['L'][$s]) ? $GLOBALS['L'][$s] : '_' . $s;
}

?>
