<?php
/**
 * @copyright   Copyright (C) 2010-2019 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */


class OQLClassTreeOptimizer
{
    /** @var OQLClassNode */
	private $oOQLClassNode;
	/** @var QueryBuilderContext */
	private $oBuild;

	/**
	 * OQLClassTreeOptimizer constructor.
	 *
	 * @param OQLClassNode $oOQLClassNode
	 * @param QueryBuilderContext $oBuild
	 */
	public function __construct($oOQLClassNode, $oBuild)
	{
		$this->oOQLClassNode = $oOQLClassNode;
		$this->oBuild = $oBuild;
	}

	/**
	 * Prune the unnecessary joins
	 */
	public function OptimizeClassTree()
	{
		$this->PruneJoins($this->oOQLClassNode);
	}

	/**
	 * @param OQLClassNode $oCurrentClassNode
	 *
	 * @return bool
	 */
	private function PruneJoins($oCurrentClassNode)
	{
		$aExpectedAttributes = $this->oBuild->m_oQBExpressions->GetExpectedFields($oCurrentClassNode->GetClassAlias());
		$bCanBeRemoved = empty($aExpectedAttributes);

		foreach ($oCurrentClassNode->GetJoins() as $sLeftKey => $aJoins)
		{
			foreach ($aJoins as $index => $oJoin)
			{
				if ($this->PruneJoins($oJoin->GetOOQLClassNode()))
				{
					if ($oJoin->IsOutbound())
					{
						// The join is not used, remove from tree
						$oCurrentClassNode->RemoveJoin($sLeftKey, $index);
					}
					else
					{
						// Inbound joins cannot be removed
						$bCanBeRemoved = false;
					}
				}
				else
				{
					// This join is used, so the current node cannot be removed
					$bCanBeRemoved = false;
				}
			}
		}
		return $bCanBeRemoved;
	}
}