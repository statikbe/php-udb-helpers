<?php

namespace statikbe\udb;

enum Environments
{
    case TEST;

    case PROD;

    public function getJWTUrl(): string
    {
        return match ($this) {
            self::TEST => 'https://jwt-test.uitdatabank.be',
            self::PROD => 'https://jwt.uitdatabank.be',
        };
    }

    public function getEndpoint(): string
    {
        return match ($this) {
            self::TEST => 'https://io-test.uitdatabank.be',
            self::PROD => 'https://io.uitdatabank.be',
        };
    }
}