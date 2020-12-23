<?php
namespace App\GamePlatforms\Tools;

class IMESPORTSTool extends IMBaseTool
{

    public function getPayoutDate($record)
    {
        if(isset($record['SettlementDateTime']) && !empty($record['SettlementDateTime'])) {
            return $record['SettlementDateTime'];
        }
        return $record['LastUpdatedDate'];
    }
}
