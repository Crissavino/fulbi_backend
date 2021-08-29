<?php
return [
    'match' => [
        'join' => ':userName se unió al partido del :day a las :hour',
        'left' => ':userName abandonó el partido del :day a las :hour',
        'created' => 'Hay un nuevo partido creado en tu zona!',
        'edited' => ':userName editó el partido del :day a las :hour, compruebalo!',
        'invited' => ':userName te invitó al partido del :day a las :hour',
        'reject' => ':userName rechazó tu invitación al partido del :day a las :hour',
        'chat' => [
            'newMessage' => ':userName envió un mensaje al partido del :day a las :hour',
            'join' => ':userName se unió al partido',
            'left' => ':userName abandonó el partid',
            'created' => ':userName creó un partido el :dateCreated',
            'edited' => ':userName editó el partido, compruebalo!',
            'expelled' => ':userName fue expulsado del partido',
        ]
    ],

];
