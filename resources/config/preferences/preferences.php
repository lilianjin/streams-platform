<?php

return [
    'locale' => [
        'type'   => 'anomaly.field_type.select',
        'config' => [
            'options' => function () {

                $options = [];

                foreach (config('streams::locales.supported') as $iso => $locale) {
                    $options[$iso] = trans('streams::locale.' . $iso . '.name');
                }

                return $options;
            }
        ],
    ],
];
