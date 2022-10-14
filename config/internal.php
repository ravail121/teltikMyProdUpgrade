<?php

return [
    /**
     * Ultra Mobile Api Url
     */
    '__TELTIK_ULTRA_MOBILE_URL'                     => env('API_ULTRA_MOBILE_URL', 'http://137.184.122.121'),

    /**
     * Endpoint to validate sim number for ultra carrier
     */
    '__ULTRA_MOBILE_NUMBER_VALIDATION_BASE_URL'     => env('ULTRA_MOBILE_NUMBER_VALIDATION_BASE_URL', 'https://ultramobile-teltik.azurewebsites.net/api'),

    /**
     * Token to validate for ultra mobile number
     */
    '__ULTRA_MOBILE_NUMBER_VALIDATION_TOKEN'        => env('ULTRA_MOBILE_NUMBER_VALIDATION_TOKEN', 'BxowiIVe9N6X1oTDptmz0meIB8jnLJzmrzvoXqb/PvQtSoLGaUHKFw=='),

    /**
     * Company Id
     */
    '__ULTRA_MOBILE_NUMBER_COMPANY_ID'              => env('ULTRA_MOBILE_NUMBER_COMPANY_ID', 1)
];
