<?php

use Schoolaid\Fel\Entities\General\GeneralData;

it('sets the issue_date_time correctly', function () {
    $generalData = new GeneralData("2021-09-01T00:00:00", "123456", "USD", "01", false);

    expect($generalData->issue_date_time)->toBe("2021-09-01T00:00:00");
});
