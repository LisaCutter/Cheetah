<?php

/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

ch_import('ChWsbPageView');
ch_import('ChWsbViews');

require_once('ChFilesCmts.php');
require_once('ChFilesSearch.php');

class ChFilesPageView extends ChWsbPageView
{
    var $iProfileId;
    var $aFileInfo;

    var $oModule;
    var $oTemplate;
    var $oConfig;
    var $oDb;
    var $oSearch;

    function __construct (&$oShared, &$aFileInfo)
    {
        parent::__construct('ch_files_view');
        $this->aFileInfo = $aFileInfo;
        $this->iProfileId = &$oShared->_iProfileId;

        $this->oModule = $oShared;
        $this->oTemplate = $oShared->_oTemplate;
        $this->oConfig = $oShared->_oConfig;
        $this->oDb = $oShared->_oDb;
        $this->oSearch = new ChFilesSearch();
        $this->oTemplate->addCss('view.css');
        new ChWsbViews('ch_files', $this->aFileInfo['medID']);
    }

    function getBlockCode_ActionList ()
    {
        $sCode = null;
        $sMainPrefix = $this->oConfig->getMainPrefix();

        ch_import('ChWsbSubscription');
        $oSubscription = ChWsbSubscription::getInstance();
        $aButton = $oSubscription->getButton($this->iProfileId, $sMainPrefix, '', (int)$this->aFileInfo['medID']);
        $sCode .= $oSubscription->getData();

        $aReplacement = array(
            'featured' => (int)$this->aFileInfo['Featured'],
            'featuredCpt' => '',
            'approvedCpt' => '',
            'approvedAct' => '',
            'moduleUrl' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri(),
            'fileUri' => $this->aFileInfo['medUri'],
            'extension' => !empty($this->aFileInfo['medExt']) ? '.' . $this->aFileInfo['medExt'] : '',
            'iViewer' => $this->iProfileId,
            'favorited' => $this->aFileInfo['favorited'] == false ? '' : 'favorited',
            'ID' => (int)$this->aFileInfo['medID'],
            'Owner' => (int)$this->aFileInfo['medProfId'],
            'OwnerName' => $this->aFileInfo['NickName'],
            'AlbumUri' => $this->aFileInfo['albumUri'],
            'sbs_' . $sMainPrefix . '_title' => $aButton['title'],
            'sbs_' . $sMainPrefix . '_script' => $aButton['script'],
            'shareCpt' => $this->oModule->isAllowedShare($this->aFileInfo) ? _t('_Share') : '',
            'downloadCpt' => $this->oModule->isAllowedDownload($this->aFileInfo) ? _t('_Download') : '',
        );
        if (isAdmin($this->iProfileId)) {
            $sMsg = $aReplacement['featured'] > 0 ? 'un' : '';
            $aReplacement['featuredCpt'] = _t('_' . $sMainPrefix . '_action_' . $sMsg . 'feature');
        }
        if ($this->oModule->isAllowedApprove($this->aFileInfo))
        {
            $sMsg = '';
            $iAppr = 1;
            if ($this->aFileInfo['Approved'] == 'approved')
            {
                $sMsg = 'de';
                $iAppr = 0;
            }
            $aReplacement['approvedCpt'] = _t('_' . $sMainPrefix . '_admin_' . $sMsg . 'activate');
            $aReplacement['approvedAct'] = $iAppr;
        }

        $aReplacement['repostCpt'] = $aReplacement['repostScript'] = '';
    	if(ChWsbRequest::serviceExists('wall', 'get_repost_js_click')) {
        	$sCode .= ChWsbService::call('wall', 'get_repost_js_script');

			$aReplacement['repostCpt'] = _t('_Repost');
			$aReplacement['repostScript'] = ChWsbService::call('wall', 'get_repost_js_click', array($this->iProfileId, $sMainPrefix, 'add', (int)$this->aFileInfo['medID']));
        }

        $sActionsList = $GLOBALS['oFunctions']->genObjectsActions($aReplacement, $sMainPrefix);
        if(is_null($sActionsList))
            return '';

        return $sCode . $sActionsList;
    }

    function getBlockCode_FileInfo ()
    {
        return $this->oTemplate->getFileInfo($this->aFileInfo);
    }

    function getBlockCode_LastAlbums ()
    {
        $sPref        = CH_WSB_URL_ROOT . $this->oConfig->getBaseUri();
        $sSimpleUrl   = $sPref . 'albums/browse/owner/' . $this->aFileInfo['NickName'];
        $sPaginateUrl = $sPref . 'view/' . $this->aFileInfo['medUri'];
        return $this->oSearch->getAlbumsBlock(array('owner' => $this->aFileInfo['medProfId']), array(), array('paginate_url' => $sPaginateUrl, 'simple_paginate_url' => $sSimpleUrl));
    }

    function getBlockCode_RelatedFiles ()
    {
        $this->oSearch->clearFilters(array('activeStatus', 'albumType', 'allow_view', 'album_status'), array('albumsObjects', 'albums'));
        $bLike = getParam('useLikeOperator');
        if ($bLike != 'on') {
            $aRel = array($this->aFileInfo['medTitle'], $this->aFileInfo['medDesc'], $this->aFileInfo['medTags'], $this->aFileInfo['Categories']);
            $sKeywords = getRelatedWords($aRel);
            if (!empty($sKeywords)) {
                $this->oSearch->aCurrent['restriction']['keyword'] = array(
                    'value' => $sKeywords,
                    'field' => '',
                    'operator' => 'against'
                );
            }
        } else {
            $sKeywords = $this->aFileInfo['medTitle'].' '.$this->aFileInfo['medTags'];
            $aWords = explode(' ', $sKeywords);
            foreach (array_unique($aWords) as $iKey => $sValue) {
                if (strlen($sValue) > 2) {
                    $this->oSearch->aCurrent['restriction']['keyword'.$iKey] = array(
                        'value' => trim(addslashes($sValue)),
                        'field' => '',
                        'operator' => 'against'
                    );
                }
            }
        }
        $this->oSearch->aCurrent['join']['icon'] = array(
            'type' => 'left',
            'table' => 'ch_files_types',
            'mainField' => 'Type',
            'onField' => 'Type',
            'joinFields' => array('Icon')
        );
        $this->oSearch->aCurrent['restriction']['id'] = array(
            'value' => $this->aFileInfo['medID'],
            'field' => $this->oSearch->aCurrent['ident'],
            'operator' => '<>',
            'paramName' => 'fileID'
        );
        $this->oSearch->aCurrent['sorting'] = 'score';
        $iLimit = (int)$this->oConfig->getGlParam('number_related');
        $iLimit = $iLimit == 0 ? 2 : $iLimit;

        $this->oSearch->aCurrent['paginate']['perPage'] = $iLimit;
        $this->oSearch->aCurrent['view'] = 'short';
        $sCode = $this->oSearch->displayResultBlock();

        $aBottomMenu = array();
        if(strlen($sCode) > 0)
            $aBottomMenu = $this->oSearch->getBottomMenu('category', 0, $this->aFileInfo['Categories']);

        return array($sCode, array(), $aBottomMenu, '');
    }

    function getBlockCode_ViewComments ()
    {
        $this->oTemplate->addCss('cmts.css');

        $oCmtsView = new ChFilesCmts('ch_files', $this->aFileInfo['medID']);
        if (!$oCmtsView->isEnabled())
        	return '';

		return $oCmtsView->getCommentsFirst ();
    }

    function getBlockCode_ViewFile ()
    {
        $oVotingView = new ChTemplVotingView('ch_files', $this->aFileInfo['medID']);
        if ($this->aFileInfo['prevItem'] > 0)
            $aPrev = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['prevItem']), true, array('medUri', 'medTitle'));
        if ($this->aFileInfo['nextItem'] > 0)
            $aNext = $this->oDb->getFileInfo(array('fileId'=>$this->aFileInfo['nextItem']), true, array('medUri', 'medTitle'));
        //icon
        $sIcon = $this->oDb->getTypeIcon($this->aFileInfo['Type']);
        if (!$sIcon)
            $sIcon = 'default.png';
        $aUnit = array(
            'pic' => $this->oTemplate->getIconUrl($sIcon),
            'fileTitle' => $this->aFileInfo['medTitle'],
            'fileSize' => (int)$this->aFileInfo['medSize'] > 0 ? _t_format_size((int)$this->aFileInfo['medSize']) : 0,
            'fileExt' => $this->aFileInfo['medExt'],
            'fileDescription' => nl2br($this->aFileInfo['medDesc']),
            'rate' => $oVotingView->isEnabled() ? $oVotingView->getBigVoting(1, $this->aFileInfo['Rate']): '',
            'favInfo' => $this->oDb->getFavoritesCount($this->aFileInfo['medID']),
            'viewInfo' => $this->aFileInfo['medViews'],
            'albumUri' => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/album/' . $this->aFileInfo['albumUri'] . '/owner/' . $this->aFileInfo['NickName'],
            'albumCaption' => $this->aFileInfo['albumCaption'],
            'ch_if:prev' => array(
                'condition' => $this->aFileInfo['prevItem'] > 0,
                'content' => array(
                    'linkPrev'  => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aPrev['medUri'],
                    'titlePrev' => $aPrev['medTitle'],
                    'percent' => $this->aFileInfo['nextItem'] > 0 ? 50 : 100,
                )
            ),
            'ch_if:next' => array(
                'condition' => $this->aFileInfo['nextItem'] > 0,
                'content' => array(
                    'linkNext'  => CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aNext['medUri'],
                    'titleNext' => $aNext['medTitle'],
                    'percent' => $this->aFileInfo['prevItem'] > 0 ? 50 : 100,
                )
            ),
        );

        $sCode = $this->oTemplate->parseHtmlByName('view_unit.html', $aUnit);
        return array($sCode, array(), array(), false);
    }

    function getBlockCode_MainFileInfo ()
    {
        return $this->oTemplate->getFileInfoMain($this->aFileInfo);
    }

    function getBlockCode_SocialSharing ()
    {
    	if(!$this->oModule->isAllowedShare($this->aFileInfo))
    		return '';

        $sUrl = CH_WSB_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $this->aFileInfo['medUri'];
        $sTitle = $this->aFileInfo['medTitle'];
        ch_import('ChTemplSocialSharing');
        $sCode = ChTemplSocialSharing::getInstance()->getCode($sUrl, $sTitle);
        return array($sCode, array(), array(), false);
    }
}
