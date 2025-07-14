<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Het following language lines contain Het default error messages used by
    | Het validator class. Some of Hetse rules have multiple versions such
    | as Het size rules. Feel free to tweak each of Hetse messages here.
    |
    */

    'accepted' => 'Het:attribute veld moet geaccepteerd zijn.',
    'accepted_if' => 'Het :attribute veld moet geaccepteerd zijn wanneer :other is :value.',
    'active_url' => 'Het :attribute veld moet een geldige URL zijn.',
    'after' => 'Het :attribute moet een datum na :date zijn.',
    'after_or_equal' => 'Het :attribute veld moet een datum gelijk of na :date zijn.',
    'alpha' => 'Het :attribute veld mag alleen letters bevatten.',
    'alpha_dash' => 'Het :attribute veld mag alleen letters, nummers, en (platte) strepen bevatten.',
    'alpha_num' => 'Het :attribute veld mag alleen letters en nummers bevatten.',
    'any_of' => 'Het :attribute veld is onjuist.',
    'array' => 'Het :attribute veld moet een array zijn.',
    'ascii' => 'Het :attribute veld mag alleen enkele bytes alfanummerische karakters en getallen bevatten.',
    'before' => 'Het :attribute veld moet een datum zijn voor :date.',
    'before_or_equal' => 'Het :attribute veld moet een datum voor of gelijk aan :date zijn.',
    'between' => [
        'array' => 'Het :attribute veld moet tussen :min en :max items bevatten.',
        'file' => 'Het :attribute veld moet tussen de :min en :max kilobytes zijn.',
        'numeric' => 'Het :attribute moet tussen :min en :max zijn.',
        'string' => 'Het :attribute veld moet tussen :min en :max karakters zijn.',
    ],
    'boolean' => 'Het :attribute veld moet waar of onwaar zijn.',
    'can' => 'Het :attribute veld bevat een niet-toegestane waarde.',
    'confirmed' => 'De bevestiging van het :attribute veld komt niet overeen.',
    'contains' => 'Het :attribute veld mist een vereiste waarde.',
    'current_password' => 'Het wachtwoord is onjuist.',
    'date' => 'Het :attribute veld moet een geldige datum zijn.',
    'date_equals' => 'Het :attribute veld moet een datum gelijk aan :date zijn.',
    'date_format' => 'Het :attribute veld moet overeenkomen met het formaat :format.',
    'decimal' => 'Het :attribute veld moet :decimal decimalen hebben.',
    'declined' => 'Het :attribute veld moet geweigerd worden.',
    'declined_if' => 'Het :attribute veld moet geweigerd worden wanneer :oHetr :value is.',
    'different' => 'Het :attribute veld en :oHetr moeten verschillend zijn.',
    'digits' => 'Het :attribute veld moet :digits cijfers bevatten.',
    'digits_between' => 'Het :attribute veld moet tussen :min en :max cijfers bevatten.',
    'dimensions' => 'Het :attribute veld heeft ongeldige afbeeldingsafmetingen.',
    'distinct' => 'Het :attribute veld heeft een dubbele waarde.',
    'doesnt_end_with' => 'Het :attribute veld mag niet eindigen met een van de volgende: :values.',
    'doesnt_start_with' => 'Het :attribute veld mag niet beginnen met een van de volgende: :values.',
    'email' => 'Het :attribute veld moet een geldig e-mailadres zijn.',
    'ends_with' => 'Het :attribute veld moet eindigen met een van de volgende: :values.',
    'enum' => 'De geselecteerde :attribute is ongeldig.',
    'exists' => 'De geselecteerde :attribute is ongeldig.',
    'extensions' => 'Het :attribute veld moet een van de volgende extensies hebben: :values.',
    'file' => 'Het :attribute veld moet een bestand zijn.',
    'filled' => 'Het :attribute veld moet een waarde bevatten.',
    'gt' => [
        'array' => 'Het :attribute veld moet meer dan :value items bevatten.',
        'file' => 'Het :attribute veld moet groter zijn dan :value kilobytes.',
        'numeric' => 'Het :attribute veld moet groter zijn dan :value.',
        'string' => 'Het :attribute veld moet meer dan :value tekens bevatten.',
    ],
    'gte' => [
        'array' => 'Het :attribute veld moet :value items of meer bevatten.',
        'file' => 'Het :attribute veld moet groter of gelijk zijn aan :value kilobytes.',
        'numeric' => 'Het :attribute veld moet groter of gelijk zijn aan :value.',
        'string' => 'Het :attribute veld moet groter of gelijk zijn aan :value tekens.',
    ],
    'hex_color' => 'Het :attribute veld moet een geldige hexadecimale kleur zijn.',
    'image' => 'Het :attribute veld moet een afbeelding zijn.',
    'in' => 'De geselecteerde :attribute is ongeldig.',
    'in_array' => 'Het :attribute veld moet bestaan in :oHetr.',
    'integer' => 'Het :attribute veld moet een geheel getal zijn.',
    'ip' => 'Het :attribute veld moet een geldig IP-adres zijn.',
    'ipv4' => 'Het :attribute veld moet een geldig IPv4-adres zijn.',
    'ipv6' => 'Het :attribute veld moet een geldig IPv6-adres zijn.',
    'json' => 'Het :attribute veld moet een geldige JSON-string zijn.',
    'list' => 'Het :attribute veld moet een lijst zijn.',
    'lowercase' => 'Het :attribute veld moet kleine letters bevatten.',
    'lt' => [
        'array' => 'Het :attribute veld moet minder dan :value items bevatten.',
        'file' => 'Het :attribute veld moet kleiner zijn dan :value kilobytes.',
        'numeric' => 'Het :attribute veld moet kleiner zijn dan :value.',
        'string' => 'Het :attribute veld moet minder dan :value tekens bevatten.',
    ],
    'lte' => [
        'array' => 'Het :attribute veld mag niet meer dan :value items bevatten.',
        'file' => 'Het :attribute veld moet kleiner of gelijk zijn aan :value kilobytes.',
        'numeric' => 'Het :attribute veld moet kleiner of gelijk zijn aan :value.',
        'string' => 'Het :attribute veld moet kleiner of gelijk zijn aan :value tekens.',
    ],
    'mac_address' => 'Het :attribute veld moet een geldig MAC-adres zijn.',
    'max' => [
        'array' => 'Het :attribute veld mag niet meer dan :max items bevatten.',
        'file' => 'Het :attribute veld mag niet groter zijn dan :max kilobytes.',
        'numeric' => 'Het :attribute veld mag niet groter zijn dan :max.',
        'string' => 'Het :attribute veld mag niet meer dan :max tekens bevatten.',
    ],
    'max_digits' => 'Het :attribute veld mag niet meer dan :max cijfers bevatten.',
    'mimes' => 'Het :attribute veld moet een bestand zijn van het type: :values.',
    'mimetypes' => 'Het :attribute veld moet een bestand zijn van het type: :values.',
    'min' => [
        'array' => 'Het :attribute veld moet minstens :min items bevatten.',
        'file' => 'Het :attribute veld moet minstens :min kilobytes zijn.',
        'numeric' => 'Het :attribute veld moet minstens :min zijn.',
        'string' => 'Het :attribute veld moet minstens :min tekens bevatten.',
    ],
    'min_digits' => 'Het :attribute veld moet minstens :min cijfers bevatten.',
    'missing' => 'Het :attribute veld moet ontbreken.',
    'missing_if' => 'Het :attribute veld moet ontbreken wanneer :oHetr :value is.',
    'missing_unless' => 'Het :attribute veld moet ontbreken tenzij :oHetr :value is.',
    'missing_with' => 'Het :attribute veld moet ontbreken wanneer :values aanwezig is.',
    'missing_with_all' => 'Het :attribute veld moet ontbreken wanneer :values aanwezig zijn.',
    'multiple_of' => 'Het :attribute veld moet een veelvoud zijn van :value.',
    'not_in' => 'De geselecteerde :attribute is ongeldig.',
    'not_regex' => 'Het formaat van het :attribute veld is ongeldig.',
    'numeric' => 'Het :attribute veld moet een getal zijn.',
    'password' => [
        'letters' => 'Het :attribute veld moet minstens één letter bevatten.',
        'mixed' => 'Het :attribute veld moet minstens één hoofdletter en één kleine letter bevatten.',
        'numbers' => 'Het :attribute veld moet minstens één cijfer bevatten.',
        'symbols' => 'Het :attribute veld moet minstens één symbool bevatten.',
        'uncompromised' => 'De opgegeven :attribute is aangetroffen in een datalek. Kies een andere :attribute.',
    ],
    'present' => 'Het :attribute veld moet aanwezig zijn.',
    'present_if' => 'Het :attribute veld moet aanwezig zijn wanneer :oHetr :value is.',
    'present_unless' => 'Het :attribute veld moet aanwezig zijn tenzij :oHetr :value is.',
    'present_with' => 'Het :attribute veld moet aanwezig zijn wanneer :values aanwezig is.',
    'present_with_all' => 'Het :attribute veld moet aanwezig zijn wanneer :values aanwezig zijn.',
    'prohibited' => 'Het :attribute veld is verboden.',
    'prohibited_if' => 'Het :attribute veld is verboden wanneer :oHetr :value is.',
    'prohibited_if_accepted' => 'Het :attribute veld is verboden wanneer :oHetr geaccepteerd is.',
    'prohibited_if_declined' => 'Het :attribute veld is verboden wanneer :oHetr geweigerd is.',
    'prohibited_unless' => 'Het :attribute veld is verboden tenzij :oHetr in :values is.',
    'prohibits' => 'Het :attribute veld verbiedt dat :oHetr aanwezig is.',
    'regex' => 'Het formaat van het :attribute veld is ongeldig.',
    'required' => 'Het :attribute veld is verplicht.',
    'required_array_keys' => 'Het :attribute veld moet sleutels bevatten voor: :values.',
    'required_if' => 'Het :attribute veld is verplicht wanneer :oHetr :value is.',
    'required_if_accepted' => 'Het :attribute veld is verplicht wanneer :oHetr geaccepteerd is.',
    'required_if_declined' => 'Het :attribute veld is verplicht wanneer :oHetr geweigerd is.',
    'required_unless' => 'Het :attribute veld is verplicht tenzij :oHetr in :values is.',
    'required_with' => 'Het :attribute veld is verplicht wanneer :values aanwezig is.',
    'required_with_all' => 'Het :attribute veld is verplicht wanneer :values aanwezig zijn.',
    'required_without' => 'Het :attribute veld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het :attribute veld is verplicht wanneer geen van :values aanwezig zijn.',
    'same' => 'Het :attribute veld moet overeenkomen met :oHetr.',
    'size' => [
        'array' => 'Het :attribute veld moet :size items bevatten.',
        'file' => 'Het :attribute veld moet :size kilobytes zijn.',
        'numeric' => 'Het :attribute veld moet :size zijn.',
        'string' => 'Het :attribute veld moet :size tekens bevatten.',
    ],
    'starts_with' => 'Het :attribute veld moet beginnen met een van de volgende: :values.',
    'string' => 'Het :attribute veld moet een tekenreeks zijn.',
    'timezone' => 'Het :attribute veld moet een geldige tijdzone zijn.',
    'unique' => 'Het :attribute is al in gebruik.',
    'uploaded' => 'Het uploaden van :attribute is mislukt.',
    'uppercase' => 'Het :attribute veld moet hoofdletters bevatten.',
    'url' => 'Het :attribute veld moet een geldige URL zijn.',
    'ulid' => 'Het :attribute veld moet een geldige ULID zijn.',
    'uuid' => 'Het :attribute veld moet een geldige UUID zijn.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using Het
    | convention "attribute.rule" to name Het lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Het following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
