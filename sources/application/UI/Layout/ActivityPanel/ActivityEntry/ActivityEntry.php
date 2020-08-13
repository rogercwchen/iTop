<?php
/**
 * Copyright (C) 2013-2020 Combodo SARL
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 */

namespace Combodo\iTop\Application\UI\Layout\ActivityPanel\ActivityEntry;


use AttributeDateTime;
use Combodo\iTop\Application\UI\UIBlock;
use DateTime;
use User;
use UserRights;

/**
 * Class ActivityEntry
 *
 * @author Guillaume Lajarige <guillaume.lajarige@combodo.com>
 * @package Combodo\iTop\Application\UI\Layout\ActivityPanel\ActivityEntry
 * @internal
 * @since 2.8.0
 */
class ActivityEntry extends UIBlock
{
	// Overloaded constants
	const BLOCK_CODE = 'ibo-activity-entry';
	const HTML_TEMPLATE_REL_PATH = 'layouts/activity-panel/activity-entry/layout';

	// Specific constants
	const DEFAULT_ORIGIN = 'unknown';

	/** @var string $sContent Raw content of the entry itself (should not have been processed / escaped) */
	protected $sContent;
	/** @var \DateTime $oDateTime Date / time the entry occurred */
	protected $oDateTime;
	/** @var string $sAuthorLogin Login of the author (user, cron, extension, ...) who made the activity of the entry */
	protected $sAuthorLogin;
	/** @var string $sAuthorFriendlyname */
	protected $sAuthorFriendlyname;
	/** @var string $sAuthorInitials */
	protected $sAuthorInitials;
	/** @var string $sAuthorPictureAbsUrl */
	protected $sAuthorPictureAbsUrl;
	/** @var bool $bIsFromCurrentUser Flag to know if the user who made the activity was the current user */
	protected $bIsFromCurrentUser;
	/** @var string $sOrigin Origin of the entry (case log, cron, lifecycle, user edit, ...) */
	protected $sOrigin;

	/**
	 * ActivityEntry constructor.
	 *
	 * @param string $sContent
	 * @param \DateTime $oDateTime
	 * @param \User $sAuthorLogin
	 * @param string $sId
	 *
	 * @throws \OQLException
	 */
	public function __construct($sContent, DateTime $oDateTime, $sAuthorLogin, $sId = null)
	{
		parent::__construct($sId);

		$this->SetContent($sContent);
		$this->SetDateTime($oDateTime);
		$this->SetAuthor($sAuthorLogin);
		$this->SetOrigin(static::DEFAULT_ORIGIN);
	}

	/**
	 * Set the content without any filtering / escaping
	 *
	 * @param string $sContent
	 *
	 * @return $this
	 */
	public function SetContent($sContent)
	{
		$this->sContent = $sContent;
		return $this;
	}

	/**
	 * Return the raw content without any filtering / escaping
	 *
	 * @return string
	 */
	public function GetContent()
	{
		return $this->sContent;
	}

	/**
	 * @param \DateTime $oDateTime
	 *
	 * @return $this
	 */
	public function SetDateTime(DateTime $oDateTime)
	{
		$this->oDateTime = $oDateTime;
		return $this;
	}

	/**
	 * Return the date time without formatting, as per the mysql format
	 * @return string
	 */
	public function GetRawDateTime()
	{
		return $this->oDateTime->format(AttributeDateTime::GetInternalFormat());
	}

	/**
	 * Return the date time formatted as per the iTop config.
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function GetFormattedDateTime()
	{
		$oDateTimeFormat = AttributeDateTime::GetFormat();
		return $oDateTimeFormat->Format($this->oDateTime);
	}

	/**
	 * Set the author and its information based on the $sAuthorLogin
	 *
	 * @param string $sAuthorLogin
	 *
	 * @return $this
	 * @throws \OQLException
	 * @throws \Exception
	 */
	public function SetAuthor($sAuthorLogin)
	{
		$this->sAuthorLogin = $sAuthorLogin;
		// TODO: Check that this does not return '' when author is the CRON or an extension.
		$this->sAuthorFriendlyname = UserRights::GetUserFriendlyName($this->sAuthorLogin);
		$this->sAuthorInitials = UserRights::GetUserInitials($this->sAuthorLogin);
		$this->sAuthorPictureAbsUrl = UserRights::GetContactPictureAbsUrl($this->sAuthorLogin, false);
		$this->bIsFromCurrentUser = UserRights::GetUserId($this->sAuthorLogin) === UserRights::GetUserId();

		return $this;
	}

	/**
	 * @return string
	 */
	public function GetAuthorLogin()
	{
		return $this->sAuthorLogin;
	}

	/**
	 * @return string
	 */
	public function GetAuthorFriendlyname()
	{
		return $this->sAuthorFriendlyname;
	}

	/**
	 * @return string
	 */
	public function GetAuthorInitials()
	{
		return $this->sAuthorInitials;
	}

	/**
	 * @return string
	 */
	public function GetAuthorPictureAbsUrl()
	{
		return $this->sAuthorPictureAbsUrl;
	}

	/**
	 * Return true if the current user is the author of the activity entry
	 *
	 * @return bool
	 */
	public function IsFromCurrentUser()
	{
		return $this->bIsFromCurrentUser;
	}

	/**
	 * Set the origin of the activity entry
	 *
	 * @param string $sOrigin
	 *
	 * @return $this
	 */
	protected function SetOrigin($sOrigin)
	{
		$this->sOrigin = $sOrigin;
		return $this;
	}

	/**
	 * Return the origin of the activity entry
	 *
	 * @return string
	 */
	public function GetOrigin()
	{
		return $this->sOrigin;
	}
}