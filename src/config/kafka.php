<?php

return [
    'group_id' => env('KAFKA_GROUP', ''),
    'sasl_username' => env('KAFKA_USER', ''),
    'sasl_password' => env('KAFKA_PASSWORD', ''),
    'metadata_broker_list' => env('KAFKA_BROKERS', ''),
    'auto_offset_reset' => 'earliest',
    'security_protocol' => 'sasl_plaintext',
    'sasl_mechanisms' => 'SCRAM-SHA-512',
    'auto_commit_interval_ms' => 100,
];