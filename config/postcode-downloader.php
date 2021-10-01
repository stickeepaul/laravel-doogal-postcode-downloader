<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Directory
    |--------------------------------------------------------------------------
    |
    | Where the new CSV should be saved to.
    |
    */

    'directory' => storage_path('app/postcodes'),

    /*
    |--------------------------------------------------------------------------
    | Fields
    |--------------------------------------------------------------------------
    |
    | The fields you want to include in your own CSV.
    |
    */

    'fields' => [
        'Postcode'                       => true,
        'In Use?'                        => false,
        'Latitude'                       => false,
        'Longitude'                      => false,
        'Easting'                        => false,
        'Northing'                       => false,
        'Grid Ref'                       => false,
        'County'                         => false,
        'District'                       => false,
        'Ward'                           => false,
        'District Code'                  => false,
        'Ward Code'                      => false,
        'Country'                        => false,
        'County Code'                    => false,
        'Constituency'                   => false,
        'Introduced'                     => false,
        'Terminated'                     => false,
        'Parish'                         => false,
        'National Park'                  => false,
        'Population'                     => false,
        'Households'                     => false,
        'Built up area'                  => false,
        'Built up sub-division'          => false,
        'Lower layer super output area'  => false,
        'Rural/urban'                    => false,
        'Region'                         => false,
        'Altitude'                       => false,
        'London zone'                    => false,
        'LSOA Code'                      => false,
        'Local authority'                => false,
        'MSOA Code'                      => false,
        'Middle layer super output area' => false,
        'Parish Code'                    => false,
        'Census output area'             => false,
        'Constituency Code'              => false,
        'Index of Multiple Deprivation'  => false,
        'Quality'                        => false,
        'User Type'                      => false,
        'Last updated'                   => false,
        'Nearest station'                => false,
        'Distance to station'            => false,
        'Postcode area'                  => false,
        'Postcode district'              => false,
        'Police force'                   => false,
        'Water company'                  => false,
        'Plus Code'                      => false,
        'Average Income'                 => false,
        'Sewage Company'                 => false,
        'Travel To Work Area'            => false,
        'ITL level 2'                    => false,
        'ITL level 3'                    => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Only include postcodes still in use?
    |--------------------------------------------------------------------------
    |
    | By default this package ignores Postcodes not in use.
    |
    */

    'only_include_in_use' => true,

    /*
    |--------------------------------------------------------------------------
    | Gzip
    |--------------------------------------------------------------------------
    |
    | By default this package will Gzip the CSV.
    |
    */

    'gzip' => true,

];